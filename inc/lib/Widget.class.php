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

class Widget
{

    private static $locations = [
        'sidebar' => 'Sidebar',
        'footer_1' => 'Footer 1 (Left)',
        'footer_2' => 'Footer 2 (Center)',
        'footer_3' => 'Footer 3 (Right)'
    ];

    /**
     * Registers a new widget location (e.g., for themes).
     *
     * @param string $id   Unique identifier for the location.
     * @param string $name Human-readable name for the location.
     */
    public static function addLocation($id, $name)
    {
        self::$locations[$id] = $name;
    }

    /**
     * Retrieves all registered widget locations.
     *
     * @return array List of locations.
     */
    public static function getLocations()
    {
        return Hooks::filter('widget_locations', self::$locations);
    }

    /**
     * Renders widgets for a specific location.
     * Fetches enabled widgets from the database and wraps them in standard widget boxes.
     *
     * @param string $location Location identifier (default: 'sidebar').
     * @return string           HTML markup for all widgets in the location.
     */
    public static function show($location = 'sidebar')
    {
        $q = Query::table('widgets')
            ->where('status', '1')
            ->where('location', $location)
            ->orderBy('sorting', 'ASC')
            ->get();
        $html = '';
        if (!empty($q)) {
            foreach ($q as $w) {
                if (!is_object($w))
                    continue;
                $html .= '<div class="widget-box mb-4" id="widget-' . $w->id . '">';
                if ($w->title != '') {
                    $html .= '<div class="widget-header"><h3 class="widget-title h5 fw-bold m-0">' . $w->title . '</h3></div>';
                }
                $html .= '<div class="widget-body">' . self::content($w) . '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /**
     * Generates the content for a specific widget object.
     * Supports built-in types (Recent Posts, Comments, Tags, Archives) and Module-driven content.
     *
     * @param object $w Widget database object.
     * @return string    Generated HTML content for the widget.
     */
    public static function content($w)
    {
        if ($w->type == 'recent_posts') {
            return Posts::lists([
                'num' => 5,
                'title' => true,
                'image' => true,
                'image_size' => 60,
                'class' => [
                    'row' => 'mb-3',
                    'img' => 'rounded shadow-sm',
                    'h4' => 'fs-6 fw-bold m-0'
                ]
            ]);
        } elseif ($w->type == 'recent_comments') {
            return Comments::recent();
        } elseif ($w->type == 'tag_cloud') {
            return '<div class="tag-cloud-wrapper">' . Tags::cloud() . '</div>';
        } elseif ($w->type == 'archive_list') {
            return '<div class="archive-widget-list">' . Archives::getList(10) . '</div>';
        } elseif ($w->type == 'module') {
            // Memanggil konten dari Module melalui Hook
            return Hooks::run($w->content);
        } else {
            return $w->content;
        }
    }
}
