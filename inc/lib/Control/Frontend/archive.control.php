<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 2.0.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class ArchiveControl extends BaseControl
{
    public function run($param)
    {
        $data = Router::scrap($param);
        $data['p_type'] = 'archive';

        $month = (SMART_URL) ? $data['month'] : Typo::cleanX(Typo::strip($_GET['month']));
        $year = (SMART_URL) ? $data['year'] : Typo::cleanX(Typo::strip($_GET['year']));
        $type = Typo::cleanX(Typo::strip($_GET['type'] ?? 'post'));
        $data['month'] = $month;
        $data['year'] = $year;
        $data['type'] = $type;
        $data['max'] = Options::v('post_perpage');

        if (Archives::validate($month, $year)) {
            if (SMART_URL) {
                $paging = isset($data['paging']) ? $data['paging'] : 1;
            } else {
                $paging = isset($_GET['paging']) ? Typo::int($_GET['paging']) : 1;
            }

            $offset = ($paging > 1) ? ($paging - 1) * $data['max'] : 0;
            $pagingtitle = ($paging > 1) ? " - Page {$paging}" : '';

            $data['dateName'] = Date::monthName($month) . " " . $year;
            $date = $year . "-" . $month;

            $data['sitetitle'] = 'Post On : ' . $data['dateName'] . $pagingtitle;
            $posts = Query::table('posts')
                ->whereRaw("`date` LIKE ?", ["%{$date}%"])
                ->where('status', '1')
                ->where('type', $type)
                ->orderBy('date', 'DESC')
                ->limit($data['max'], $offset)
                ->get();

            $data['num'] = count($posts);
            $data['posts'] = Posts::prepare($posts);
            $url = Url::archive($month, $year);
            $paging_arr = [
                'paging' => $paging,
                'table' => 'posts',
                'where' => "`date` LIKE '%{$date}%' AND `status` = '1' AND `type` = '{$type}'",
                'max' => $data['max'],
                'url' => $url,
                'type' => Options::v('pagination'),
                'post_type' => $type,
            ];
            $data['paging'] = Paging::create($paging_arr, SMART_URL);

            $data['recent_posts'] = Posts::lists([
                'num' => 5,
                'image' => true,
                'image_size' => 100,
                'title' => true,
                'date' => true,
                'type' => "post",
                'class' => [
                    'row' => 'd-flex align-items-center mb-3 border-bottom pb-3',
                    'img' => 'rounded flex-shrink-0',
                    'list' => 'flex-grow-1 ms-3',
                    'h4' => 'fs-5 mb-0 text-dark',
                    'date' => 'text-body-secondary mt-0'
                ]
            ]);

            $this->render('archive', $data);
            exit;
        } else {
            Control::error('404');
            exit;
        }
    }
}

$control = new ArchiveControl();
$control->run($param);
