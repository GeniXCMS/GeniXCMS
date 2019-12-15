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

            Hooks::attach('header_load_meta', array('Gneex', 'loadCSS'));
            Hooks::attach('admin_footer_action', array('Gneex', 'loadAdminAsset'));

            Hooks::attach('post_param_form', array('Gneex', 'postParam'));
            Hooks::attach('page_param_form', array('Gneex', 'postParam'));
        }
    }

    public static function checkDB()
    {
        if (Options::validate('gneex_options')) {
            return true;
        } else {
            return false;
        }
    }

    public static function parseDB()
    {
        $opt = Options::get('gneex_options');
        $opt = json_decode($opt, true);
        $o = array();
        if (is_array($opt)) {
            foreach ($opt as $k => $v) {
                $o[$k] = Typo::jsonDeFormat($v);
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

    public static function optionPost($type, $post='')
    {
        $sql = "SELECT * FROM `posts` WHERE `type` = '{$type}' ORDER BY `title` ASC";
        $q = Db::result($sql);
        $opt = '<option></option>';
        foreach ($q as $k => $v) {
            $sel = ($post != '' && $post == $v->id) ? 'selected': '';
            $opt .= "<option value='{$v->id}' {$sel}>{$v->title}</option>";
        }

        return $opt;
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
                return self::isAdsense(Typo::Xclean($opt[$var]));
            } else {
                return Typo::Xclean($opt[$var]);
            }
        }
    }

    public static function introIg($url)
    {
        
        if (strpos($url, 'youtube') || strpos($url, 'youtu.be')) {
            if (strpos($url, 'youtube')) {
                parse_str( parse_url( $url, PHP_URL_QUERY ), $dom );
            } else {
                $dom = explode('/', $url);
            }

            $hash = (strpos($url, 'youtu.be')) ? $dom[3] : $dom['v'];
            $html = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$hash.'?rel=0&amp;controls=0&amp;showinfo=0" class="center-block" frameborder="0" allowfullscreen></iframe>';
        } elseif(strpos($url, 'vimeo')) {
            $dom = explode('/', $url);
            $html = '<iframe src="https://player.vimeo.com/video/'.$dom[3].'?byline=0&portrait=0" width="640" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        } elseif(strpos($url, 'dailymotion') || strpos($url, 'dai.ly')) {
            $dom = explode('/', $url);
            $hash = (strpos($url, 'dai.ly')) ? $dom[3]: $dom[4];
            $html = '<iframe frameborder="0" width="480" height="270" src="//www.dailymotion.com/embed/video/'.$hash.'" allowfullscreen></iframe>';
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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.6.2/flexslider.min.css" rel="stylesheet">
        <link href="'.Site::$url.'inc/themes/gneex/css/style.css" rel="stylesheet">
        <style>';
        $css .= '
        body {
            background-color: '.$opt['body_background_color'].';
        }
        .container {
            max-width: '.$opt['container_width'].'px;
        }
        a {
            color: '.$opt['link_color'].';
        }
        a:hover {
            color: '.$opt['link_color_hover'].';
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
        if ($opt['panel_1_color'] != "") {
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
        }
        if ($opt['panel_2_color'] != "") {
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
        }
        if ($opt['panel_3_color'] != "") {
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
        }
        if ($opt['panel_5_color'] != "") {
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
        }
        
        $css .= '
        .panel.panel-red .panel-heading, .panel.panel-red .panel-body {
            background-color: '.$opt['sidebar_background_color_header'].';
            color: '.$opt['sidebar_font_color_header'].';
        }
        .panel.panel-red {
            border: '.$opt['sidebar_border_width'].'px solid '.$opt['sidebar_border_color'].';    
        }
        .panel.panel-red .panel-body a {
            color: '.$opt['sidebar_font_color_body'].';
        }
        .panel.panel-black .panel-heading, .panel.panel-black .panel-body {
            background-color: '.$opt['sidebar_background_color_header'].';
            color: '.$opt['sidebar_font_color_header'].';
        }
        .panel.panel-black {
            border: '.$opt['sidebar_border_width'].'px solid '.$opt['sidebar_border_color'].';    
        }
        .panel.panel-black .panel-body a {
            color: '.$opt['sidebar_font_color_body'].';
        }
        .panel.panel-green .panel-heading, .panel.panel-green .panel-body {
            background-color: '.$opt['sidebar_background_color_header'].';
            color: '.$opt['sidebar_font_color_header'].';
        }
        .panel.panel-green {
            border: '.$opt['sidebar_border_width'].'px solid '.$opt['sidebar_border_color'].';    
        }
        .panel.panel-green .panel-body a {
            color: '.$opt['sidebar_font_color_body'].';
        }
        .panel.panel-blue .panel-heading, .panel.panel-blue .panel-body {
            background-color: '.$opt['sidebar_background_color_header'].';
            color: '.$opt['sidebar_font_color_header'].';
        }
        .panel.panel-blue {
            border: '.$opt['sidebar_border_width'].'px solid '.$opt['sidebar_border_color'].';    
        }
        .panel.panel-blue .panel-body a {
            color: '.$opt['sidebar_font_color_body'].';
        }
        ';
        $css .= '
        .blog-lists {
            border: '.$opt['content_border_width'].'px solid '.$opt['content_border_color'].';
            background-color: '.$opt['content_background_color_body'].';
            color: '.$opt['content_font_color_body'].';
        }
        article h2.title {
            font-size: '.$opt['content_title_size'].'px;
            color: '.$opt['content_title_color'].';
        }
        article h3.title {
            font-size: '.$opt['content_title_cat_size'].'px;
            color: '.$opt['content_title_color'].';
        }
        article h3.title a, article h2.title a {
            color: '.$opt['content_title_color'].';
        }
        article h3.title a:hover, article h2.title a:hover {
            color: '.$opt['content_title_color_hover'].';
        }
        h2.category-title {
            font-size: '.$opt['list_title_size'].'px;
            color: '.$opt['list_title_color'].';
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
        <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.2.0/css/bootstrap-slider.min.css">-->
        <style>
            #containerWidthSlider .slider-selection {
                background: #BABABA;
            }
        </style>

        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/js/bootstrap-colorpicker.min.js"></script>
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.2.0/bootstrap-slider.min.js"></script>-->
        <script>
            $(function() {
                $(\'#body_background_color\').colorpicker();
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
                $(\'#sidebar_background_color_header\').colorpicker();
                $(\'#sidebar_font_color_header\').colorpicker();
                $(\'#sidebar_border_color\').colorpicker();
                $(\'#sidebar_background_color_body\').colorpicker();
                $(\'#sidebar_font_color_body\').colorpicker();
                $(\'#content_border_color\').colorpicker();
                $(\'#content_background_color_body\').colorpicker();
                $(\'#content_font_color_body\').colorpicker();
                $(\'#content_title_color\').colorpicker();
                $(\'#content_title_color_hover\').colorpicker();
                $(\'#link_color\').colorpicker();
                $(\'#link_color_hover\').colorpicker();
                $(\'#list_title_color\').colorpicker();
            });
            $(\'#myTabs a\').click(function (e) {
              e.preventDefault();
              $(this).tab(\'show\');
            });
            
            $.viewMap = {
                \'blog\' : $([]),
                \'magazine\' : $(\'#magazine\'),
                \'fullwidth\' : $(\'#fullwidth\'),
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
        System::adminAsset(Site::minifyJS($js));
    }

    public static function postParam($data)
    {
//        print_r($data);
        $bar = isset($data[0]['post'][0]->id) ? Posts::getParam('sidebar', $data[0]['post'][0]->id): 'yes';
//        echo $bar;
        $yes = ($bar == 'yes') ? 'selected': '';
        $no = ($bar == 'no') ? 'selected': '';
        $form = '<div class="row">
                <div class="col-sm-4">
                    <label>Show/Hide Sidebar</label>
                    <select name="param[sidebar]" class="form-control">
                        <option value="yes" '.$yes.'>Show Sidebar</option>
                        <option value="no" '.$no.'>Hide Sidebar</option>
                    </select>
                </div>
                </div>';
        echo $form;
    }
}

new Gneex();
