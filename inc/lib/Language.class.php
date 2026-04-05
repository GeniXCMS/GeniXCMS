<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 2.0.1
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Language
{
    public function __construct()
    {
        self::setActive();
    }

    public static function getList()
    {
        $handle = glob(GX_PATH . '/inc/lang/locale/*', GLOB_ONLYDIR);
        // print_r($handle);
        $lang = [];
        foreach ($handle as $k => $v) {
            $lang[] = basename($v);
        }
        // while (false !== ($entry = $handle->read())) {
        //     if ($entry != '.' && $entry != '..') {
        //         $file = GX_PATH.'/inc/lang/locale/'.$entry;
        //         $ext = pathinfo($file, PATHINFO_EXTENSION);
        //         if (is_file($file) == true && $ext == 'php') {
        //             $lang[] = $entry;
        //         }
        //     }
        // }

        // $handle->close();

        return $lang;
    }

    public static function getList2()
    {
        $handle = dir(GX_PATH . '/inc/lang/');
        while (false !== ($entry = $handle->read())) {
            if ($entry != '.' && $entry != '..') {
                $file = GX_PATH . '/inc/lang/' . $entry;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (is_file($file) == true && $ext == 'php') {
                    $lang[] = $entry;
                }
            }
        }

        $handle->close();

        return $lang;
    }

    public static function optDropdown($var = '')
    {
        $langs = self::getList();
        $opt = '';
        foreach ($langs as $lang) {
            $file = explode('.', $lang);
            if ($var == $file[0]) {
                $sel = 'SELECTED';
            } else {
                $sel = '';
            }
            $opt .= "<option {$sel}>{$file[0]}</option>";
        }

        return $opt;
    }

    public static function getDefaultLang()
    {
        $def = Options::v('multilang_default');
        $lang = json_decode(Options::v('multilang_country') ?? '', true);
        $deflang = $lang[$def];

        return $deflang;
    }

    public static function getLangParam($lang, $post_id)
    {
        $post_id = Typo::int($post_id);
        if (Posts::existParam('multilang', $post_id)) {
            $langparam = Typo::Xclean(Posts::getParam('multilang', $post_id));
            $multilang = json_decode($langparam, true);

            foreach ($multilang as $key => $value) {
                // print_r($value);
                $keys = array_keys($value);
                // print_r($keys);
                if ($keys[0] == $lang) {
                    $lang = $multilang[$key][$lang];

                    return $lang;
                }
            }
        }
    }

    public static function setActive($lang = '')
    {
        $lg = json_decode(Options::v('multilang_country') ?? '', true);

        if (isset($_GET['lang']) && $_GET['lang'] != '' && $lang == '') {
            $getLang = Typo::cleanX($_GET['lang']);
            if (key_exists($getLang, $lg)) {
                Session::set(array('lang' => $getLang));
            } else {
                Session::remove('lang');
            }
        } elseif ($lang != '') {
            if (key_exists($lang, $lg)) {
                Session::set(array('lang' => $lang));
            } else {
                Session::remove('lang');
            }
        }
    }

    public static function isActive()
    {
        switch (SMART_URL) {
            case true:
                if (Options::v('multilang_enable') === 'on') {
                    $langs = Session::val('lang');
                    if ($langs != '') {
                        $lang = Session::val('lang');
                    } else {
                        $lang = '';
                    }
                } else {
                    $lang = '';
                }

                break;

            default:
                if (Options::v('multilang_enable') === 'on') {
                    $langs = Session::val('lang');
                    if ($langs != '') {
                        $lang = Session::val('lang');
                    } else {
                        $lang = isset($_GET['lang']) ? Typo::cleanX($_GET['lang']) : '';
                    }
                } else {
                    $lang = '';
                }
                break;
        }

        return $lang;
    }

    public static function flagList()
    {
        $lang = json_decode(Options::v('multilang_country') ?? '', true);
        $multilang_enable = Options::v('multilang_enable');
        // print_r($lang);
        $html = '';
        if (!empty($lang) && $multilang_enable == 'on') {
            $html = '<ul class="nav nav-pills flaglist">';
            foreach ($lang as $key => $value) {
                $flag = strtolower($value['flag']);
                $html .= '
                <li class=""><a href="' . Url::flag($key) . "\" class=\"flag-icon flag-icon-{$flag}\"></a></li>
                ";
            }
            $html .= '</ul>';
            Hooks::attach('footer_load_lib', array('Language', 'flagLib'));
        }

        return $html;
    }

    public static function flagLib()
    {
        echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet">';
    }
}
/* End of file Language.class.php */
/* Location: ./inc/lib/Language.class.php */
