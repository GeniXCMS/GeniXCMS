<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Theme
{
    public static $url;
    public static $active;

    public function __construct()
    {
        global $GLOBALS;
        self::$url = Url::theme();
        self::$active = Options::v('themes');
        self::loader();
    }

    public static function theme($var, $data = '')
    {
        if (isset($data)) {
            $GLOBALS['data'] = $data;
        }
        if (self::exist($var)) {
            include GX_THEME.THEME.'/'.$var.'.php';
        } else {
            Control::error('unknown', 'Theme file is missing.');
        }
    }

    public static function inc($var, $data = '')
    {
        self::theme($var, $data);
    }

    public static function exist($vars)
    {
        if (file_exists(GX_THEME.THEME.'/'.$vars.'.php')) {
            return true;
        } else {
            return false;
        }
    }

    public static function admin($var, $data = '')
    {
        if (isset($data)) {
            $GLOBALS['data'] = $data;
        }
        $admin_dir = defined('ADMIN_DIR') ? ADMIN_DIR : 'gxadmin';
        include GX_PATH.'/'.$admin_dir.'/themes/'.$var.'.php';
    }

    public static function header($vars = '')
    {
        header('Cache-Control: must-revalidate,max-age=300,s-maxage=900');
        $offset = 60 * 60 * 24 * 3;
        $ExpStr = 'Expires: '.gmdate('D, d M Y H:i:s', time() + $offset).' GMT';
        header($ExpStr);
        header('Content-Type: text/html; charset=utf-8');

        if (isset($vars)) {
            $GLOBALS['data'] = $vars;
            self::theme('header', $vars);
        } else {
            self::theme('header');
        }
    }
    public static function footer($vars = '')
    {
        global $GLOBALS;
        if (isset($vars)) {
            $GLOBALS['data'] = $vars;
            self::theme('footer', $vars);
        } else {
            self::theme('footer');
        }
    }

    public static function editor($mode = 'light', $height = '300')
    {
        $editor = Options::v('use_editor');
        if ($editor == 'on') {
            $GLOBALS['editor'] = true;
        } else {
            $GLOBALS['editor'] = false;
        }
        System::toolbarMode($mode);
        System::toolbar($mode);
        $GLOBALS['editor_height'] = $height;
        //return $editor;
    }

    public static function validator($vars = '')
    {
        $GLOBALS['validator'] = true;
        $GLOBALS['validator_js'] = $vars;
        //return $editor;
    }

    public static function install($var)
    {
        include GX_PATH.'/gxadmin/themes/install/'.$var.'.php';
    }

    public static function options($var)
    {
        if (self::optionsExist($var)) {
            include GX_THEME.$var.'/options.php';
        }
    }

    public static function optionsExist($var)
    {
        if (file_exists(GX_THEME.$var.'/options.php')) {
            return true;
        } else {
            return false;
        }
    }

    public static function incFunc($var)
    {
        if (self::functionExist($var)) {
            include GX_THEME.$var.'/function.php';
        }
    }

    public static function functionExist($var)
    {
        if (file_exists(GX_THEME.$var.'/function.php')) {
            return true;
        } else {
            return false;
        }
    }

    public static function thmList()
    {
        //$mod = '';
        $handle = dir(GX_THEME);
        while (false !== ($entry = $handle->read())) {
            if ($entry != '.' && $entry != '..') {
                $dir = GX_THEME.$entry;
                if (is_dir($dir) == true && file_exists($dir.'/themeinfo.php')) {
                    $thm[] = basename($dir);
                }
            }
        }

        $handle->close();

        return $thm;
    }

    public static function activate($thm)
    {
        if (Options::update('themes', Typo::cleanX($thm))) {
            new Options();

            return true;
        } else {
            return false;
        }
    }

    public static function data($vars)
    {
        $file = GX_THEME.'/'.$vars.'/themeinfo.php';
        $handle = fopen($file, 'r');
        $data = fread($handle, filesize($file));
        fclose($handle);
        preg_match('/\* Name: (.*)\s\*/Us', $data, $matches);
        $d['name'] = $matches[1];
        preg_match('/\* Desc: (.*)\s\*/Us', $data, $matches);
        $d['desc'] = $matches[1];
        preg_match('/\* Version: (.*)\s\*/Us', $data, $matches);
        $d['version'] = $matches[1];
        preg_match('/\* Build: (.*)\s\*/Us', $data, $matches);
        $d['build'] = $matches[1];
        preg_match('/\* Developer: (.*)\s\*/Us', $data, $matches);
        $d['developer'] = $matches[1];
        preg_match('/\* URI: (.*)\s\*/Us', $data, $matches);
        $d['url'] = $matches[1];
        preg_match('/\* License: (.*)\s\*/Us', $data, $matches);
        $d['license'] = $matches[1];
        preg_match('/\* Icon: (.*)\s\*/Us', $data, $matches);
        $d['icon'] = $matches[1];

        return $d;
    }

    public static function isActive($thm)
    {
        if (Options::v('themes') === $thm) {
            return true;
        } else {
            return false;
        }
    }

    public static function loader()
    {
        $theme = Options::v('themes');
        define('THEME', $theme);
        self::incFunc($theme);
    }

    public static function thmMenu()
    {
        $thm = Options::v('themes');
        //$mod = self::modList();
        //print_r($mod);
        $list = '';

        if (User::access(0)) {
            $data = self::data($thm);
            if (isset($_GET['page'])
                && $_GET['page'] == 'themes'
                && isset($_GET['view'])
                && $_GET['view'] == 'options') {
                $class = 'class="active"';
            } else {
                $class = '';
            }
            if (self::optionsExist($thm)) {
                $active = (isset($_GET['page'])
                    && $_GET['page'] == 'themes'
                    && isset($_GET['view'])
                    && $_GET['view']  == 'options') ? 'class="active"' : '';
                $list .= "
                <li $class $active>
                    <a href=\"index.php?page=themes&view=options\" $active>".$data['icon'].' <span>'.$data['name'].'</span></a>
                </li>';
            } else {
                $list = '';
            }
        }

        return $list;
    }

    public static function name($mod)
    {
        $data = self::data($mod);

        return $data['name'];
    }
}

/* End of file Theme.class.php */
/* Location: ./inc/lib/Theme.class.php */
