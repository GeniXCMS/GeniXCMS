<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

class Widget {
    
    private static $locations = [
        'sidebar' => 'Sidebar',
        'footer_1' => 'Footer 1 (Left)',
        'footer_2' => 'Footer 2 (Center)',
        'footer_3' => 'Footer 3 (Right)'
    ];

    /**
     * Register a new widget location (e.g., for themes)
     */
    public static function addLocation($id, $name) {
        self::$locations[$id] = $name;
    }

    /**
     * Get all registered widget locations
     */
    public static function getLocations() {
        return Hooks::filter('widget_locations', self::$locations);
    }

    public static function show($location = 'sidebar') {
        $q = Query::table('widgets')
            ->where('status', '1')
            ->where('location', $location)
            ->orderBy('sorting', 'ASC')
            ->get();
        $html = '';
        if (!empty($q)) {
            foreach ($q as $w) {
                if (!is_object($w)) continue;
                $html .= '<div class="widget-box mb-4" id="widget-'.$w->id.'">';
                if ($w->title != '') {
                    $html .= '<div class="widget-header"><h3 class="widget-title h5 fw-bold m-0">'.$w->title.'</h3></div>';
                }
                $html .= '<div class="widget-body">'.self::content($w).'</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    public static function content($w) {
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
            return '<div class="tag-cloud-wrapper">'.Tags::cloud().'</div>';
        } elseif ($w->type == 'archive_list') {
            return '<div class="archive-widget-list">'.Archives::getList(10).'</div>';
        } elseif ($w->type == 'module') {
            // Memanggil konten dari Module melalui Hook
            return Hooks::run($w->content);
        } else {
            return $w->content;
        }
    }
}
