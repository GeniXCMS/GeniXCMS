<?php
/*
 *
 * 
 *
 **/

class MdoTheme
{
    public static $opt;

    public function __construct()
    {
        $mdo_opt = Options::v('mdo_theme_options');
        $mdo_opt = ($mdo_opt == "") ? json_encode(['error' => 'Data Not Found']): $mdo_opt;
        self::$opt = json_decode($mdo_opt, true);

        $mdo_adsense = (key_exists("mdo_adsense", self::$opt)) ? self::$opt['mdo_adsense']: "";
        if ($mdo_adsense != '') {
            Hooks::attach('footer_load_lib', array('MdoTheme', 'loadAdsenseJs'));
        }
    }

    public static function opt($var)
    {
        $opt = self::$opt;
        if (key_exists($var, $opt)) {
            if ($var == 'mdo_adsense') {
                return self::isAdsense($opt[$var]);
            } else {
                return urldecode(self::$opt[$var]);
            }
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

    public static function loadAnalytics()
    {
        echo self::opt('mdo_analytics');
    }
}
