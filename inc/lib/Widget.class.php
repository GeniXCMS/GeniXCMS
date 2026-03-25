<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

class Widget {
    public static function show($location = 'sidebar') {
        $sql = "SELECT * FROM `widgets` WHERE `status` = '1' AND `location` = '$location' ORDER BY `sorting` ASC";
        $q = Db::result($sql);
        $html = '';
        if (Db::$num_rows > 0) {
            foreach ($q as $w) {
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
        } elseif ($w->type == 'module') {
            // Memanggil konten dari Module melalui Hook
            return Hooks::run($w->content);
        } else {
            return $w->content;
        }
    }
}
