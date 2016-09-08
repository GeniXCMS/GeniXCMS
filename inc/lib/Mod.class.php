<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140928
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Mod
{
    public static $listMenu = array();

    public function __construct()
    {
        self::loader();
    }

    public static function mod($var)
    {
        self::load($var);
    }

    public static function options($var)
    {
        $file = GX_MOD.$var.'/options.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function modList()
    {
        //$mod = '';
        $handle = dir(GX_MOD);
        while (false !== ($entry = $handle->read())) {
            if ($entry != '.' && $entry != '..') {
                $dir = GX_MOD.$entry;
                if (is_dir($dir) == true) {
                    (file_exists($dir.'/index.php')) ? $mod[] = basename($dir) : '';
                }
            }
        }

        $handle->close();

        return $mod;
    }

    public static function data($vars)
    {
        $file = GX_MOD.'/'.$vars.'/index.php';
        if (file_exists($file)) {
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
        } else {
            $d = '';
        }

        return $d;
    }

    public static function modMenu()
    {
        $json = Options::v('modules');
        $mod = json_decode($json, true);
        //$mod = self::modList();
        //print_r($mod);
        if (is_array($mod)) {
            $list = '';
            asort($mod);
            foreach ($mod as $m) {
                if (self::exist($m)) {
                    $data = self::data($m);
                    if (isset($_GET['mod']) && $_GET['mod'] == $m) {
                        $class = 'class="active"';
                    } else {
                        $class = '';
                    }
                    $list .= "<li><a href=\"index.php?page=mods&mod={$m}\" $class >".$data['icon'].' '.$data['name'].'</a></li>';
                }
            }
        } else {
            $list = '';
        }

        return $list;
    }

    public static function inc($vars, $data, $dir)
    {
        $file = $dir.'/'.$vars.'.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function activate($mod)
    {
        $json = Options::v('modules');
        $mods = json_decode($json, true);
        //print_r($mods);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }
        if (!in_array($mod, $mods)) {
            $mods = array_merge($mods, array($mod));
        }

        $mods = json_encode($mods);

        $mods = Options::update('modules', $mods);
        if ($mods) {
            new Options();

            return true;
        } else {
            return false;
        }
    }

    public static function deactivate($mod)
    {
        $mods = Options::v('modules');
        $mods = json_decode($mods, true);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }
        //print_r($mods);
        $arr = '';
        for ($i = 0; $i < count($mods); ++$i) {
            if ($mods[$i] == $mod) {
                //unset($mods[$i]);
            } else {
                $arr[] = $mods[$i];
            }
        }
        //print_r($arr);
        //asort($mods);
        $mods = json_encode($arr);
        $mods = Options::update('modules', $mods);
        if ($mods) {
            new Options();

            return true;
        } else {
            return false;
        }
    }

    public static function isActive($mod)
    {
        $json = Options::v('modules');
        $mods = json_decode($json, true);
        //print_r($mods);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }

        if (in_array($mod, $mods)) {
            return true;
        } else {
            return false;
        }
    }

    public static function loader()
    {
        $data = '';
        if (User::access(0)) {
            if (isset($_GET['page']) && $_GET['page'] == 'modules') {
                if (isset($_GET['act'])) {
                    if ($_GET['act'] == ACTIVATE) {
                        if (!Token::isExist($_GET['token'])) {
                            $alertDanger[] = TOKEN_NOT_EXIST;
                        }

                        if (!isset($alertDanger)) {
                            self::activate($_GET['modules']);
                            $GLOBALS['alertSuccess'] = MODULES_ACTIVATED;
                        } else {
                            $GLOBALS['alertDanger'] = $alertDanger;
                        }
                    } elseif ($_GET['act'] == DEACTIVATE) {
                        if (!Token::isExist($_GET['token'])) {
                            $alertDanger[] = TOKEN_NOT_EXIST;
                        }

                        if (!isset($alertDanger)) {
                            self::deactivate($_GET['modules']);
                            $GLOBALS['alertSuccess'] = MODULES_DEACTIVATED;
                        } else {
                            $GLOBALS['alertDanger'] = $alertDanger;
                        }
                    } elseif ($_GET['act'] == 'remove') {
                        if (!Token::isExist($_GET['token'])) {
                            $alertDanger[] = TOKEN_NOT_EXIST;
                        }
                        if (self::isActive($_GET['modules'])) {
                            $alertDanger[] = 'Module is Active. Please deactivate first.';
                        }
                        if (!isset($alertDanger)) {
                            self::deactivate($_GET['modules']);
                            if (false != Files::delTree(GX_MOD.$_GET['modules'])) {
                                $GLOBALS['alertSuccess'] = MODULES_DELETED;
                            } else {
                                $GLOBALS['alertDanger'][] = "Can't delete module files";
                            }
                        } else {
                            $GLOBALS['alertDanger'] = $alertDanger;
                        }
                    }
                }
            }
        }
        $json = Options::v('modules');
        $mods = json_decode($json, true);
        if (!is_array($mods) || $mods == '') {
            $mods = array();
        }
        foreach ($mods as $m) {
            if (self::exist($m)) {
                self::load($m);
            }
        }

        return $data;
    }

    public static function load($mod)
    {
        $file = GX_MOD.'/'.$mod.'/index.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function url($mod)
    {
        $url = Site::$url.'/inc/mod/'.$mod;

        return $url;
    }

    public static function exist($mod)
    {
        $file = GX_MOD.'/'.$mod.'/options.php';
        if (file_exists($file)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * menulist
     *
     *
     */

    public static function menuList($var = '')
    {
        $list = self::$listMenu;

        foreach ($list as $k => $v) {
            if (!empty($var) && $k == $var) {
                $selected = 'selected';
            }
            echo "<option value=\"{$k}\" {$selected}>{$v}</option>";
        }
    }

    public static function addMenuList($method, $title)
    {
        $new = array($method => $title);
        self::$listMenu = array_merge(self::$listMenu, $new);

        return true;
    }
}

/* End of file Mod.class.php */
/* Location: ./inc/lib/Mod.class.php */
