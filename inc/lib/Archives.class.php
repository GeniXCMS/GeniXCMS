<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.0.0
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Archives
{

    /**
     * Archives Constructor.
     */
    public function __construct()
    {
        // No longer triggers generate here — generate is lazy-called from list()
    }

    /**
     * Renders the archives list HTML.
     * Generates and caches archive data on demand if missing or stale.
     *
     * @param int    $max  Maximum number of archive entries to display.
     * @param string $type The content type (e.g., 'post').
     * @return string The rendered HTML list.
     */
    public static function getList($max, $type = 'post')
    {
        // Ensure the cache is populated (lazy, idempotent)
        self::generate($type);

        // Direct DB read to bypass stale in-memory Options cache
        $raw = Options::get('archives_list');
        $dates = ($raw !== null && $raw !== false && $raw !== '') ? json_decode(Typo::Xclean($raw), true) : null;

        if (!is_array($dates) || !isset($dates[$type]) || empty($dates[$type])) {
            return "<ul class='list-unstyled mb-0'><li class='text-muted small ps-2'>" . _('No archives yet.') . "</li></ul>";
        }

        $html = "<ul class='list-unstyled mb-0 archives-list-group'>";
        $count = 0;
        foreach ($dates[$type] as $year => $months) {
            if (!is_array($months))
                continue;
            foreach ($months as $monthKey => $monthVal) {
                if ($count >= $max)
                    break 2;

                /**
                 * Support both structures:
                 * New: [month_num => count]
                 * Old: [0 => month_num]
                 */
                $monthNum = ($monthKey > 0) ? $monthKey : $monthVal;
                $monthCnt = ($monthKey > 0) ? $monthVal : ''; // Old didn't have count

                $monthName = Date::monthName($monthNum);
                $url = Url::archive($monthNum, $year);
                $badge = ($monthCnt !== '') ? "<span class='badge rounded-pill text-bg-light border opacity-75'>{$monthCnt}</span>" : '';

                $html .= "
                <li class='mb-2'>
                    <a href='{$url}' class='d-flex justify-content-between align-items-center py-1 px-2 link-body-emphasis text-decoration-none rounded hover-bg-light transition-base'>
                        <span>{$monthName} {$year}</span>
                        {$badge}
                    </a>
                </li>";
                $count++;
            }
        }
        $html .= "</ul>";

        return $html;
    }

    /**
     * Validates if any content exists for a given month and year.
     *
     * @param int|string $month
     * @param int|string $year
     * @return bool True if content exists.
     */
    public static function validate($month, $year)
    {
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $date = $year . "-" . $month;
        $q = Db::result("SELECT `id` FROM `posts` WHERE `date` LIKE '%{$date}%' ");
        return Db::$num_rows > 0;
    }

    /**
     * Checks requirements and triggers data generation if cache is stale.
     * Refreshes when: data is missing, data is empty, or cache is older than 24 hours.
     *
     * @param string $type The content type.
     */
    public static function generate($type = 'post')
    {
        $list_value = Options::get('archives_list');
        $list_empty = ($list_value === false || $list_value === null || $list_value === '');

        // Check deeper: is the data actually populated (not just empty year arrays)?
        if (!$list_empty) {
            $decoded = json_decode($list_value, true);
            $has_months = false;
            if (is_array($decoded) && isset($decoded[$type])) {
                foreach ($decoded[$type] as $year => $months) {
                    if (is_array($months) && count($months) > 0) {
                        $has_months = true;
                        break;
                    }
                }
            }
            if (!$has_months) {
                $list_empty = true; // Force refresh
            }
        }

        $last_update = (int) Options::get('archives_last_update');
        $margin = time() - $last_update;

        if ($list_empty || $margin > 3600 * 24) {
            self::doUpdate($type);
        }
    }

    /**
     * Triggers the fetch and update process for archives.
     *
     * @param string $type The content type.
     */
    public static function doUpdate($type)
    {
        $data = self::fetchData($type);
        self::optUpdate($data);
    }

    /**
     * Queries the database to calculate archive counts by month/year.
     * Supports both MySQL and SQLite date functions.
     *
     * @param string $type The content type to query.
     * @return string JSON encoded string of archive data.
     */
    public static function fetchData($type)
    {
        if (defined('DB_DRIVER') && DB_DRIVER === 'sqlite') {
            $month = "strftime('%m', `date`)";
            $year = "strftime('%Y', `date`)";
        } else {
            $month = "Month(`date`)";
            $year = "Year(`date`)";
        }

        $archives = Db::result("SELECT $month as `month`, $year as `year`, count(*) as `cnt`
        FROM `posts` WHERE `type` = '{$type}' AND `status` = '1'
        GROUP BY $year, $month
        ORDER BY $year DESC, $month DESC");

        $archive_dates = [];
        if (is_array($archives)) {
            foreach ($archives as $v) {
                // Store counts too: year -> month -> count
                $archive_dates[$type][(int) $v->year][(int) $v->month] = (int) $v->cnt;
            }
        }

        return json_encode($archive_dates);
    }

    /**
     * Persists the generated archive data into the options table.
     * Refreshes the in-memory options cache for immediate consistency.
     *
     * @param string $data JSON encoded archive data.
     */
    public static function optUpdate($data)
    {
        if (!is_string($data)) {
            $data = json_encode([]);
        }

        $time = (string) time();

        // Always replace with fresh data — never merge (stale data causes empty archives)
        if (TRUE === Options::isExist('archives_list')) {
            Options::update('archives_list', $data);
            Options::update('archives_last_update', $time);
        } else {
            Options::insert([
                'archives_list' => $data,
                'archives_last_update' => $time,
            ]);
        }

        // Refresh in-memory Options cache so the same request reads the new value
        Options::$_data = Options::load();
    }
}
