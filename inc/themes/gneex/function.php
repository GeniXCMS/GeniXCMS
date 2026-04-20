<?php

class Gneex
{
    public static $opt;

    public function __construct()
    {
        if (self::checkDB()) {
            self::$opt = self::parseDB();
            if (isset(self::$opt['adsense']) && self::$opt['adsense'] != '') {
                Hooks::attach('footer_load_lib', array('Gneex', 'loadAdsenseJs'));
            }
            if (isset(self::$opt['analytics']) && self::$opt['analytics'] != '') {
                Hooks::attach('footer_load_lib', array('Gneex', 'loadAnalytics'));
            }

            Hooks::attach('header_load_lib', array('Gneex', 'loadCSS'));
            Hooks::attach('admin_footer_action', array('Gneex', 'loadAdminAsset'));

            Hooks::attach('post_param_form_bottom', array('Gneex', 'postParam'));
            Hooks::attach('page_param_form_bottom', array('Gneex', 'postParam'));

        }

        // Programmatically enqueue theme assets
        self::registerAssets();

        // Register "GneeX Options" sub-item under the Themes admin menu.
        Hooks::attach('init', function () {
            AdminMenu::addChild('themes', [
                'label' => _('GneeX Options'),
                'url' => 'index.php?page=themes&view=options',
                'icon' => 'bi bi-sliders',
                'access' => 0,
            ]);
        });
    }

    /**
     * Programmatically register and enqueue theme-specific assets via Asset class.
     */
    public static function registerAssets()
    {
        $themeUrl = rtrim(Url::theme(), '/') . '/';
        
        // Register CSS
        Asset::register('gneex-style', 'css', $themeUrl . 'css/style.css', 'header', ['bootstrap-css'], 20, 'frontend');
        Asset::register('aos-css', 'css', 'https://unpkg.com/aos@2.3.1/dist/aos.css', 'header', [], 10, 'frontend');

        // Register JS
        Asset::register('aos-js', 'js', 'https://unpkg.com/aos@2.3.1/dist/aos.js', 'footer', [], 10, 'frontend');
        Asset::register('flexslider', 'js', 'https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.6.2/jquery.flexslider.min.js', 'footer', ['jquery'], 11, 'frontend');

        // Enqueue them all for the frontend
        if (!defined('GX_ADMIN') || !GX_ADMIN) {
            Asset::enqueue(['aos-css', 'gneex-style', 'aos-js', 'flexslider']);
        }
    }

    /**
     * Generate Google Fonts <link> tags for panel-selected fonts.
     * Called from Latte template as {Gneex::panelFontsLink()|noescape}
     */
    public static function panelFontsLink()
    {
        $gfMap = [
            "'Inter', sans-serif" => 'Inter:wght@400;600;700',
            "'Roboto', sans-serif" => 'Roboto:wght@400;500;700',
            "'Merriweather', serif" => 'Merriweather:wght@400;700',
            "'Playfair Display', serif" => 'Playfair+Display:wght@400;700',
            "'JetBrains Mono', monospace" => 'JetBrains+Mono:wght@400;700',
        ];
        $fontsToLoad = [];
        for ($pi = 1; $pi <= 5; $pi++) {
            $ff = self::$opt["panel_{$pi}_font_family"] ?? 'inherit';
            if ($ff !== 'inherit' && isset($gfMap[$ff])) {
                $fontsToLoad[] = $gfMap[$ff];
            }
        }
        if (!$fontsToLoad)
            return '';
        $fontsToLoad = array_unique($fontsToLoad);
        $q = implode('&family=', $fontsToLoad);
        return '<link rel="preconnect" href="https://fonts.googleapis.com">'
            . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=' . $q . '&display=swap">';
    }

    public static function checkDB()
    {
        if (Options::validate('gneex_options')) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDefaults()
    {
        return [
            'intro_title' => 'Welcome to ' . Site::$name,
            'intro_text' => 'We create amazing digital experiences that help brands grow and thrive in the modern world.',
            'intro_image' => '',
            'featured_posts' => '',
            'adsense' => '',
            'analytics' => '',
            'front_layout' => 'blog',
            'panel_1' => '',
            'panel_1_color' => '',
            'panel_1_font_color' => '',
            'panel_1_bg' => '',
            'panel_1_text_color' => '',
            'panel_1_font_family' => 'inherit',
            'panel_1_font_size' => '1',
            'panel_2' => '',
            'panel_2_color' => '',
            'panel_2_font_color' => '',
            'panel_2_bg' => '',
            'panel_2_text_color' => '',
            'panel_2_font_family' => 'inherit',
            'panel_2_font_size' => '1',
            'panel_3' => '',
            'panel_3_color' => '',
            'panel_3_font_color' => '',
            'panel_3_bg' => '',
            'panel_3_text_color' => '',
            'panel_3_font_family' => 'inherit',
            'panel_3_font_size' => '1',
            'panel_4' => '',
            'panel_4_color' => '',
            'panel_4_font_color' => '',
            'panel_4_bg' => '',
            'panel_4_text_color' => '',
            'panel_4_font_family' => 'inherit',
            'panel_4_font_size' => '1',
            'panel_5' => '',
            'panel_5_color' => '',
            'panel_5_font_color' => '',
            'panel_5_bg' => '',
            'panel_5_text_color' => '',
            'panel_5_font_family' => 'inherit',
            'panel_5_font_size' => '1',
            'background_header' => Url::theme() . 'images/header-bg.jpg',
            'background_color_header' => '#0f172a',
            'background_featured' => '',
            'background_color_featured' => '#050505',
            'background_color_footer' => '#0f172a',
            'font_color_footer' => '#ffffff',
            'font_color_header' => '#ffffff',
            'container_width' => '1280',
            'category_layout' => 'blog',
            'body_background_color' => '#f8fafc',
            'link_color' => '#3b82f6',
            'link_color_hover' => '#2563eb',
            'background_footer' => '',
            'link_color_footer' => '#3b82f6',
            'sidebar_background_color_header' => '',
            'sidebar_font_color_header' => '',
            'sidebar_border_width' => '0',
            'sidebar_border_color' => '',
            'sidebar_background_color_body' => '',
            'sidebar_font_color_body' => '',
            'content_border_width' => '0',
            'content_border_color' => '',
            'content_background_color_body' => '',
            'fullwidth_page' => '',
            'content_font_color_body' => '#334155',
            'content_title_size' => '32',
            'content_title_cat_size' => '24',
            'content_title_color' => '#0f172a',
            'content_title_color_hover' => '#3b82f6',
            'list_title_color' => '#0f172a',
            'list_title_size' => '28',
            'social_fb' => '#',
            'social_tw' => '#',
            'social_ig' => '#',
            'social_yt' => '#',
            'show_breadcrumb' => 'yes',
            'logo_width' => '',
            'logo_height' => '50px',
            'custom_css' => '',
            // Typography defaults
            'typo_body_font' => '"Inter", sans-serif',
            'typo_body_size' => '16',
            'typo_body_weight' => '400',
            'typo_body_color' => '#334155',
            'typo_h1_font' => 'inherit',
            'typo_h1_size' => '36',
            'typo_h1_weight' => '800',
            'typo_h1_color' => '#0f172a',
            'typo_h2_font' => 'inherit',
            'typo_h2_size' => '28',
            'typo_h2_weight' => '700',
            'typo_h2_color' => '#0f172a',
            'typo_h3_font' => 'inherit',
            'typo_h3_size' => '22',
            'typo_h3_weight' => '700',
            'typo_h3_color' => '#1e293b',
            'typo_h4_font' => 'inherit',
            'typo_h4_size' => '18',
            'typo_h4_weight' => '600',
            'typo_h4_color' => '#1e293b',
            'typo_nav_font' => 'inherit',
            'typo_nav_size' => '14',
            'typo_nav_weight' => '600',
            'typo_nav_color' => '#ffffff',
            'typo_post_title_font' => 'inherit',
            'typo_post_title_size' => '20',
            'typo_post_title_weight' => '700',
            'typo_post_title_color' => '#0f172a',
            'typo_meta_font' => 'inherit',
            'typo_meta_size' => '13',
            'typo_meta_weight' => '400',
            'typo_meta_color' => '#64748b',
            // Blog Post Typography
            'typo_single_title_font' => 'inherit',
            'typo_single_title_size' => '36',
            'typo_single_title_weight' => '800',
            'typo_single_title_color' => '#0f172a',
            'typo_content_font' => 'inherit',
            'typo_content_size' => '17',
            'typo_content_weight' => '400',
            'typo_content_color' => '#334155',
            'typo_post_meta_font' => 'inherit',
            'typo_post_meta_size' => '14',
            'typo_post_meta_weight' => '400',
            'typo_post_meta_color' => '#64748b',
            // Blockquote
            'typo_blockquote_font' => 'inherit',
            'typo_blockquote_size' => '18',
            'typo_blockquote_weight' => '400',
            'typo_blockquote_color' => '#475569',
            // Breadcrumb
            'typo_breadcrumb_font' => 'inherit',
            'typo_breadcrumb_size' => '13',
            'typo_breadcrumb_weight' => '600',
            'typo_breadcrumb_color' => '#ffffff',
            // Comments
            'typo_comment_title_font' => 'inherit',
            'typo_comment_title_size' => '22',
            'typo_comment_title_weight' => '700',
            'typo_comment_title_color' => '#0f172a',
            'typo_comment_body_font' => 'inherit',
            'typo_comment_body_size' => '15',
            'typo_comment_body_weight' => '400',
            'typo_comment_body_color' => '#334155'
        ];
    }

    public static function parseDB()
    {
        $opt = Options::get('gneex_options');
        $opt = json_decode((string)$opt, true);
        $defaults = self::getDefaults();
        $o = $defaults;
        if (is_array($opt)) {
            foreach ($opt as $k => $v) {
                $deformatted = is_string($v) ? Typo::jsonDeFormat($v) : $v;
                // If stored value is empty string but default is non-empty, keep default
                if ($deformatted === '' && isset($defaults[$k]) && $defaults[$k] !== '') {
                    // keep $o[$k] = default
                } else {
                    $o[$k] = $deformatted;
                }
            }
        }
        return $o;
    }

    public static function getImage($post, $id = null)
    {
        if ($id) {
            $image = Posts::getPostImage($id);
            if ($image != '') {
                return $image;
            }
        }
        if (!$post)
            return '';
        preg_match_all('/<img .*?src=[\'"]([^\'"]+)[\'"].*?>/Ui', $post, $im);
        return isset($im[1][0]) ? $im[1][0] : '';
    }

    public static function getPost($id)
    {
        if (empty($id))
            return '';
        $id = Typo::int($id);
        $sql = "SELECT `content` FROM `posts` WHERE `id` = '{$id}' LIMIT 1";
        $q = Db::result($sql);
        if (isset($q[0]->content)) {
            return Posts::content($q[0]->content);
        }
        return '';
    }

    public static function optionPost($type, $post = '')
    {
        $sql = "SELECT * FROM `posts` WHERE `type` = '{$type}' ORDER BY `title` ASC";
        $q = Db::result($sql);
        $opt = '<option></option>';
        foreach ($q as $k => $v) {
            $sel = ($post != '' && $post == $v->id) ? 'selected' : '';
            $opt .= "<option value='{$v->id}' {$sel}>{$v->title}</option>";
        }
        return $opt;
    }

    public static function featuredExist()
    {
        $feat = isset(self::$opt['featured_posts']) ? self::$opt['featured_posts'] : '';
        return ($feat != '' && $feat !== null);
    }

    public static function isAdsense($adc)
    {
        if ($adc != '') {
            return str_replace('<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>', '', urldecode($adc));
        }
    }

    public static function loadAdsenseJs()
    {
        return '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
    }

    public static function opt($var)
    {
        $opt = self::$opt;
        if (isset($opt[$var])) {
            if ($var == 'adsense') {
                return self::isAdsense(Typo::Xclean($opt[$var]));
            } else {
                return is_string($opt[$var]) ? Typo::Xclean($opt[$var]) : $opt[$var];
            }
        } else {
            $defaults = self::getDefaults();
            return isset($defaults[$var]) ? $defaults[$var] : '';
        }
    }

    public static function introIg($url)
    {
        if (empty($url))
            return '';
        if (strpos($url, 'youtube') || strpos($url, 'youtu.be')) {
            if (strpos($url, 'youtube')) {
                parse_str(parse_url($url, PHP_URL_QUERY), $dom);
            } else {
                $dom = explode('/', $url);
            }
            $hash = (strpos($url, 'youtu.be')) ? $dom[3] : (isset($dom['v']) ? $dom['v'] : '');
            $html = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $hash . '?rel=0&amp;controls=0&amp;showinfo=0" class="center-block" frameborder="0" allowfullscreen></iframe>';
        } elseif (strpos($url, 'vimeo')) {
            $dom = explode('/', $url);
            $html = '<iframe src="https://player.vimeo.com/video/' . $dom[3] . '?byline=0&portrait=0" width="640" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        } elseif (strpos($url, 'dailymotion') || strpos($url, 'dai.ly')) {
            $dom = explode('/', $url);
            $hash = (strpos($url, 'dai.ly')) ? $dom[3] : (isset($dom[4]) ? $dom[4] : '');
            $html = '<iframe frameborder="0" width="480" height="270" src="//www.dailymotion.com/embed/video/' . $hash . '" allowfullscreen></iframe>';
        } else {
            $html = '<img src="' . $url . '" class="img-fluid rounded-4 shadow-sm">';
        }
        return $html;
    }

    public static function loadAnalytics()
    {
        return self::opt('analytics');
    }

    public static function loadCSS()
    {
        // Global safeguard: never inject the theme's premium frontend styles into the administrative portal
        if (strpos($_SERVER['PHP_SELF'], 'gxadmin') !== false) {
            return '';
        }

        return OptionsBuilder::generateFrontendCSS(self::$opt, [
            'themeUrl' => Url::theme(),
            'minify' => true,
        ]);
    }

    public static function loadAdminAsset()
    {
        $js = '
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            /* GneeX admin base reset */
            #gneexForm, .gx-wrap { box-sizing: border-box; }
            #gneexForm *, .gx-wrap * { box-sizing: inherit; }
        </style>';
        System::adminAsset($js);
    }

    public static function postParam($data)
    {
        $id = 0;
        if (isset($data['post']) && is_array($data['post']) && isset($data['post'][0])) {
            $id = is_object($data['post'][0]) ? $data['post'][0]->id : (isset($data['post'][0]['id']) ? $data['post'][0]['id'] : 0);
        }
        $bar = ($id > 0) ? Posts::getParam('sidebar', $id) : 'yes';
        $yes = ($bar == 'yes') ? 'selected' : '';
        $no = ($bar == 'no') ? 'selected' : '';
        echo '
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold m-0"><i class="bi bi-layout-sidebar me-2 text-primary"></i>' . _("Theme Layout Options") . '</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-0">
                <div class="mb-0">
                    <label class="form-label fw-bold text-dark small text-uppercase opacity-75" style="font-size: 0.7rem; letter-spacing: 0.5px;">' . _("Sidebar Visibility") . '</label>
                    <select name="param[sidebar]" class="form-select border-0 bg-light rounded-3 px-3 py-2 fw-medium shadow-none">
                        <option value="yes" ' . $yes . '>' . _("Show Sidebar") . '</option>
                        <option value="no" ' . $no . '>' . _("Hide Sidebar") . '</option>
                    </select>
                </div>
            </div>
        </div>';
    }
}
new Gneex();
