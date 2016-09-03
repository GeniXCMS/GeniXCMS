<?php

class Gneex
{
    public static $opt;

    public function __construct()
    {
        if (self::checkDB()) {
            // global $gneex;
            self::$opt = self::parseDB();
        // print_r($gneex);
            if (isset(self::$opt['adsense']) && self::$opt['adsense'] != '') {
                Hooks::attach('footer_load_lib', array('Gneex', 'loadAdsenseJs'));
            }
            if (isset(self::$opt['analytics']) && self::$opt['analytics'] != '') {
                Hooks::attach('footer_load_lib', array('Gneex', 'loadAnalytics'));
            }

            Hooks::attach('footer_load_lib', array('Gneex', 'loadCSS'));
            Hooks::attach('admin_footer_action', array('Gneex', 'loadAdminAsset'));
        }
    }

    public static function checkDB()
    {
        $sql = "SELECT `id` FROM `options` WHERE `name` = 'gneex_options' ";
        $q = Db::query($sql);
        if ($q->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function parseDB()
    {
        $opt = Options::get('gneex_options');
        $opt = json_decode($opt, true);
        $o = [];
        if (is_array($opt)) {
            foreach ($opt as $k => $v) {
                $o[$k] = urldecode($v);
            }
        }

        return $o;
    }

    public static function getImage($post)
    {
        preg_match_all('/<img .*?src=[\'"]([^\'"]+)[\'"].*?>/Ui', $post, $im);
        if (count($im) >= 1) {
            for ($i = 1; $i <= count($im); $i += 2) {
                if (isset($im[$i][0])) {
                    return $im[$i][0];
                    break;
                }
            }
        }
    }

    public static function getPost($id)
    {
        $sql = "SELECT `content` FROM `posts` WHERE `id` = '{$id}'";
        $q = Db::result($sql);

        return $q[0]->content;
    }

    public static function featuredExist()
    {
        $feat = self::$opt['featured_posts'];
        if ($feat != '') {
            return true;
        } else {
            return false;
        }
    }

    public static function isAdsense($adc)
    {
        if ($adc != '') {
            return str_replace('<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>', '', urldecode($adc));
        }
    }

    public static function loadAdsenseJs()
    {
        echo '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
    }

    public static function opt($var)
    {
        $opt = self::$opt;
        if (key_exists($var, $opt)) {
            if ($var == 'adsense') {
                return self::isAdsense(Typo::jsonDeFormat($opt[$var]));
            } else {
                return Typo::jsonDeFormat($opt[$var]);
            }
        }
    }

    public static function introIg($url)
    {
        $dom = explode('/', $url);
        if (strpos($dom[2], 'youtube') || strpos($dom[2], 'youtu.be')) {
            $hash = (strpos($dom[2], 'youtu.be')) ? $dom[3] : str_replace('watch?v=', '', $dom[3]);
            $html = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$hash.'?rel=0&amp;controls=0&amp;showinfo=0" class="center-block" frameborder="0" allowfullscreen></iframe>';
        } else {
            $html = '<img src="'.$url.'" class="img-responsive center-block">';
        }

        return $html;
    }

    public static function loadAnalytics()
    {
        echo self::opt('analytics');
    }

    public static function loadCSS()
    {
        $opt = self::$opt;
        $css = '
        <link href="'.Site::$url.'inc/themes/gneex/css/style.css" rel="stylesheet">
        <style>';
        $css .= '
        .container {
            max-width: '.$opt['container_width'].'px;
        }
        ';
        $css .= '
        .bg-slide {
            background-color: '.$opt['background_color_header'].';
            background-image: url('.$opt['background_header'].');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }';
        $css .= '
        #header, #front-text, #front-text h2 span {
            color: '.$opt['font_color_header'].';
        }';
        $css .= '
        footer {
            background-color: '.$opt['background_color_footer'].';
            background-image: url('.$opt['background_footer'].');
            color: '.$opt['font_color_footer'].';
        }';
        $css .= '
        footer a {
            color: '.$opt['link_color_footer'].';
        }';
        $css .= '
        #featured {
            background-color: '.$opt['background_color_featured'].';
            background-image: url(\''.$opt['background_featured'].'\');
        }';
        $css .= '
        .panel.panel-one .panel-heading, .panel.panel-one .panel-body {
            background-color: '.$opt['panel_1_color'].';
            color: '.$opt['panel_1_font_color'].';
        }
        .panel.panel-one {
            border: 1px solid '.$opt['panel_1_color'].';    
        }
        .panel.panel-one .panel-body a {
            color: '.$opt['panel_1_font_color'].';
        }
        ';
        $css .= '
        .panel.panel-two .panel-heading, .panel.panel-two .panel-body {
            background-color: '.$opt['panel_2_color'].';
            color: '.$opt['panel_2_font_color'].';
        }
        .panel.panel-two {
            border: 1px solid '.$opt['panel_2_color'].';    
        }
        .panel.panel-two .panel-body a {
            color: '.$opt['panel_2_font_color'].';
        }
        ';
        $css .= '
        .panel.panel-three .panel-heading, .panel.panel-three .panel-body {
            background-color: '.$opt['panel_3_color'].';
            color: '.$opt['panel_3_font_color'].';
        }
        .panel.panel-three {
            border: 1px solid '.$opt['panel_3_color'].';    
        }
        .panel.panel-three .panel-body a {
            color: '.$opt['panel_3_font_color'].';
        }
        ';
        $css .= '
        .panel.panel-five .panel-heading, .panel.panel-five .panel-body {
            background-color: '.$opt['panel_5_color'].';
            color: '.$opt['panel_5_font_color'].';
        }
        .panel.panel-five {
            border: 1px solid '.$opt['panel_5_color'].';    
        }
        .panel.panel-five .panel-body a {
            color: '.$opt['panel_5_font_color'].';
        }
        ';
        $css .= '
        </style>
        ';

        echo Site::minifyCSS($css);
    }

    public static function loadAdminAsset()
    {
        $opt = self::$opt;
        $js = '

        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/css/bootstrap-colorpicker.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.2.0/css/bootstrap-slider.min.css">
        <style>
            #containerWidthSlider .slider-selection {
                background: #BABABA;
            }
        </style>

        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/js/bootstrap-colorpicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.2.0/bootstrap-slider.min.js"></script>
        <script>
            $(function() {
                $(\'#background_color_header\').colorpicker();
                $(\'#background_color_footer\').colorpicker();
                $(\'#background_color_featured\').colorpicker();
                $(\'#panel_1_color\').colorpicker();
                $(\'#panel_1_font_color\').colorpicker();
                $(\'#panel_2_color\').colorpicker();
                $(\'#panel_2_font_color\').colorpicker();
                $(\'#panel_3_color\').colorpicker();
                $(\'#panel_3_font_color\').colorpicker();
                $(\'#panel_5_color\').colorpicker();
                $(\'#panel_5_font_color\').colorpicker();
                $(\'#font_color_header\').colorpicker();
                $(\'#font_color_footer\').colorpicker();
                $(\'#link_color_footer\').colorpicker();
            });
            $(\'#myTabs a\').click(function (e) {
              e.preventDefault();
              $(this).tab(\'show\');
            });
            
            $.viewMap = {
                \'blog\' : $([]),
                \'magazine\' : $(\'#magazine\'),
            };
            $.each($.viewMap, function () {
                this.hide();
            });
            var e = document.getElementById("frontpageSelector");
            var vMap = e.options[e.selectedIndex].value;
            console.log(vMap);
            $.viewMap[vMap].show();

            $(\'#frontpageSelector\').change(function () {
            // hide all
                $.each($.viewMap, function () {
                    this.hide();
                });
            // show current
                $.viewMap[$(this).val()].show();
            });
            $(\'#containerWidth\').slider({
                formatter: function(value) {
                    return \'Current value: \' + value;
                }
            });
        </script>
        ';

        echo Site::minifyJS($js);
    }
}

new Gneex();
