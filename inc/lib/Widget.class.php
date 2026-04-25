<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.0.0
 * @version 2.4.0
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
            $num = self::getParam('recent_posts_num', $w->id);
            $num = ($num > 0) ? $num : 5;
            return Posts::lists([
                'num' => $num,
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
            $num = self::getParam('recent_comments_num', $w->id);
            $num = ($num > 0) ? $num : 5;
            return Comments::recent(['num' => $num]);
        } elseif ($w->type == 'tag_cloud') {
            return '<div class="tag-cloud-wrapper">' . Tags::cloud() . '</div>';
        } elseif ($w->type == 'archive_list') {
            return '<div class="archive-widget-list">' . Archives::getList(10) . '</div>';
        } elseif ($w->type == 'navigation') {
            $menu = self::getParam('navigation_name', $w->id);
            return '<div class="navigation-widget-list">' . Menus::getMenu($menu) . '</div>';
        } elseif ($w->type == 'module') {
            // Memanggil konten dari Module melalui Hook
            return Hooks::run($w->content);
        } else {
            // Fall-through for custom modular widgets from external modules
            $html = Hooks::run('widget_render_' . $w->type, $w);
            if ($html != '') {
                return $html;
            }
            return $w->content;
        }
    }

    /** @var array Custom labels and settings for widget types */
    private static $_typeLabels = [];

    public static function setTypeLabel($type, $labels = [])
    {
        self::$_typeLabels[$type] = $labels;
    }

    public static function types()
    {
        $defaults = [
            'html' => _('Custom HTML / Raw'),
            'module' => _('Module Hook / Callback'),
            'recent_posts' => _('Stream: Recent Posts'),
            'recent_comments' => _('Stream: Recent Comments'),
            'tag_cloud' => _('Visualization: Tag Cloud'),
            'archive_list' => _('Archive: Monthly List'),
            'navigation' => _('Navigation: Menu'),
        ];
        return Hooks::filter('widget_types_list', $defaults);
    }

    public static function getTypeLabel($type, $key = null)
    {
        $defaults = [
            'html' => ['title' => 'Custom HTML', 'icon' => 'bi bi-code-slash'],
            'module' => ['title' => 'Module Integration', 'icon' => 'bi bi-plugin'],
            'recent_posts' => ['title' => 'Recent Posts', 'icon' => 'bi bi-file-earmark-post'],
            'recent_comments' => ['title' => 'Recent Comments', 'icon' => 'bi bi-chat-dots'],
            'tag_cloud' => ['title' => 'Tag Cloud', 'icon' => 'bi bi-cloud'],
            'archive_list' => ['title' => 'Archive List', 'icon' => 'bi bi-calendar3'],
            'navigation' => ['title' => 'Navigation Menu', 'icon' => 'bi bi-list-nested'],
        ];

        // Ensure custom types from types() are present
        $allTypes = self::types();
        foreach ($allTypes as $id => $label) {
            if (!isset($defaults[$id])) {
                $defaults[$id] = ['title' => $label, 'icon' => 'bi bi-box'];
            }
        }

        $merged = array_merge($defaults, self::$_typeLabels);
        if ($key === null) {
            return $merged[$type] ?? ['title' => $type, 'icon' => 'bi bi-box'];
        }
        return $merged[$type][$key] ?? '';
    }

    public static function addParam($param, $value, $widget_id)
    {
        return Query::table('widgets_param')->insert([
            'widget_id' => Typo::int($widget_id),
            'param' => Typo::cleanX($param),
            'value' => Typo::cleanX($value)
        ]);
    }

    public static function editParam($param, $value, $widget_id)
    {
        return Query::table('widgets_param')
            ->where('widget_id', Typo::int($widget_id))
            ->where('param', Typo::cleanX($param))
            ->update(['value' => Typo::cleanX($value)]);
    }

    public static function getParam($param, $widget_id)
    {
        $q = Query::table('widgets_param')
            ->where('widget_id', Typo::int($widget_id))
            ->where('param', Typo::cleanX($param))
            ->first();

        return $q ? Typo::Xclean($q->value) : '';
    }

    public static function delParam($param, $widget_id)
    {
        return Query::table('widgets_param')
            ->where('widget_id', Typo::int($widget_id))
            ->where('param', Typo::cleanX($param))
            ->delete();
    }

    public static function existParam($param, $widget_id)
    {
        $q = Query::table('widgets_param')
            ->select('id')
            ->where('widget_id', Typo::int($widget_id))
            ->where('param', Typo::cleanX($param))
            ->first();

        return $q ? true : false;
    }

    public static function setup()
    {

        // Also fetch from menus table to catch any non-registered active menus
        $menus_db = Query::table('menus')->select('menuid')->groupBy('menuid')->get();
        if (!empty($menus_db)) {
            foreach ($menus_db as $m) {
                if (!empty($m->menuid) && !isset($menu_options[$m->menuid])) {
                    $menu_options[$m->menuid] = $m->menuid;
                }
            }
        }

        if (count($menu_options) <= 1) {
            $menu_options['none'] = _('No Menus Found');
        }

        Params::register([
            'widget' => [
                [
                    'widget_type' => 'recent_posts',
                    'grouptitle' => _('Recent Posts Configuration'),
                    'icon' => 'bi bi-file-earmark-post',
                    'fields' => [
                        [
                            'name' => 'recent_posts_num',
                            'title' => _('Number of Posts to Show'),
                            'type' => 'number',
                            'default' => '5',
                        ]
                    ]
                ],
                [
                    'widget_type' => 'recent_comments',
                    'grouptitle' => _('Recent Comments Configuration'),
                    'icon' => 'bi bi-chat-dots',
                    'fields' => [
                        [
                            'name' => 'recent_comments_num',
                            'title' => _('Number of Comments to Show'),
                            'type' => 'number',
                            'default' => '5',
                        ]
                    ]
                ],
                [
                    'widget_type' => 'tag_cloud',
                    'grouptitle' => _('Tag Cloud Settings'),
                    'icon' => 'bi bi-cloud',
                    'fields' => []
                ],
                [
                    'widget_type' => 'archive_list',
                    'grouptitle' => _('Archive List Settings'),
                    'icon' => 'bi bi-calendar3',
                    'fields' => []
                ],
                [
                    'widget_type' => 'navigation',
                    'grouptitle' => _('Navigation Settings'),
                    'icon' => 'bi bi-list-nested',
                    'fields' => [
                        [
                            'name' => 'navigation_name',
                            'title' => _('Select Menu'),
                            'type' => 'select',
                            'value' => $menu_options
                        ]
                    ]
                ]
            ]
        ]);
    }
}

Widget::setup();
