<?php
/*
 *
 * 
 *
 **/

class MdoTheme
{
    public static $opt;
    public static $optionKey = 'default_theme_options_v2';

    public function __construct()
    {
        $mdo_opt = Options::get(self::$optionKey);
        self::$opt = json_decode($mdo_opt, true) ?: [];

        $isAdmin = defined('GX_ADMIN') || strpos($_SERVER['SCRIPT_FILENAME'], 'gxadmin') !== false;

        if (!$isAdmin) {
            if (!empty(self::$opt['mdo_adsense'])) {
                Hooks::attach('footer_load_lib', array(__CLASS__, 'loadAdsenseJs'));
            }
            
            Hooks::attach('header_load_meta', array(__CLASS__, 'loadThemeCSS'));
        }
    }

    public static function opt($var)
    {
        $opt = self::$opt;
        if (isset($opt[$var])) {
            return ($var == 'mdo_adsense' || $var == 'mdo_analytics' || $var == 'custom_css') ? $opt[$var] : htmlspecialchars_decode($opt[$var]);
        }
        return '';
    }

    public static function loadAdsenseJs()
    {
        echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
    }

    public static function loadThemeCSS()
    {
        if (class_exists('OptionsBuilder')) {
            echo OptionsBuilder::generateFrontendCSS(self::$opt, [
                'themeUrl' => Url::theme(),
                'minify' => true
            ]);
        }
        
        // Custom Inline CSS for Default Theme Specifics
        $primary = self::opt('link_color') ?: '#0085A1';
        $nav_bg = self::opt('background_color_navbar');
        $footer_bg = self::opt('background_color_footer');
        $body_bg = self::opt('body_background_color');

        echo "<style>
            :root { --primary-color: {$primary}; }
            " . ($nav_bg ? ".blog-masthead { background-color: {$nav_bg} !important; }" : "") . "
            " . ($footer_bg ? ".blog-footer { background-color: {$footer_bg} !important; }" : "") . "
            " . ($body_bg ? "body { background-color: {$body_bg} !important; }" : "") . "
            .blog-title a:hover { color: {$primary} !important; }
            .btn-primary { background-color: {$primary} !important; border-color: {$primary} !important; }
        </style>";
    }

    public static function loadAnalytics()
    {
        echo self::opt('mdo_analytics');
    }
}
