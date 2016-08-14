<?php
/*
 *
 * 
 *
 **/

class MdoTheme
{
    public static $opt;

    public function __construct () {
        self::$opt = json_decode(Options::v('mdo_theme_options'), true);
        if (self::$opt['mdo_adsense'] != '') {
            Hooks::attach('footer_load_lib', array('MdoTheme', 'loadAdsenseJs'));
        }
    }

    public static function opt($var) {
        $opt = self::$opt;
        if (key_exists($var,$opt)) {
            if ($var == 'mdo_adsense') {
                return self::isAdsense($opt[$var]);
            } else {
                return urldecode(self::$opt[$var]);
            }
        }
    }

    public static function isAdsense($adc) {
        if ($adc != '') {
            
            return str_replace('<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>', '', urldecode($adc));
        }
    }

    public static function loadAdsenseJs() {
        echo '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
    }

    public static function loadAnalytics() {
        echo self::opt('mdo_analytics');
    }
}
