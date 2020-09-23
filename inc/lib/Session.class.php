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
class Session
{
    public function __construct()
    {
    }

    public static function start()
    {
        $url = Site::$url;
        $site_id = !defined('SITE_ID') ? 'Installation' : SITE_ID;
        session_name('GeniXCMS-'.$site_id);
        session_start();
        $uri = parse_url($url);
        // print_r($_SESSION);

        // unset($_SESSION);
        if (!isset($_SESSION['gxsess']) || $_SESSION['gxsess'] == '') {
            $_SESSION['gxsess'] = array(
                                    'key' => self::sesKey(),
                                    'time' => date('Y-m-d H:i:s'),
                                    'val' => array(),
                                );
        }
        session_regenerate_id();
        $path = isset($uri['path']) ? $uri['path'] : '';
        setcookie(session_name(), session_id(), time() + 3600, $path, '', '', true);
        $GLOBALS['start_time'] = microtime(true);
    }

    private static function sesKey()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $dt = date('Y-m-d H');

        $key = md5($ip.$browser.$dt);

        return $key;
    }

    /*
    *    Session Handler
    *
    *    $gxsess = array (
    *                    'key' => 'sesskey_val',
    *                    'time' => 'sesstime_val',
    *                    'val' => array (
    *                                   'sessval1_key' => 'sessval1_val',
    *                                   'sessval2_key' => 'sessval2_val',
    *                                 )
    *                )
    */
    public static function get_session($vars)
    {
    }

    public static function val($vars)
    {
        $val = $_SESSION['gxsess']['val'];
        if (is_array($val)) {
            # code...
            foreach ($val as $k => $v) {
                switch ($k) {
                    case $vars:
                        return $v;
                        break;

                    default:
                        //echo "no value";
                        break;
                }
            }
        } else {
            return null;
        }
    }

    public static function set_session($vars, $val = '')
    {
        if (is_array($vars)) {
            if (is_array($_SESSION['gxsess']['val'])) {
                $arr = array_merge($_SESSION['gxsess']['val'], $vars);
                $_SESSION['gxsess']['val'] = $arr;
            } else {
                $_SESSION['gxsess']['val'][$vars] = $val;
            }
        } else {
            if (array_key_exists($vars, $_SESSION['gxsess']['val']) && is_array($_SESSION['gxsess']['val'][$vars])) {
                $arr = array_merge($_SESSION['gxsess']['val'][$vars], $val);
                $_SESSION['gxsess']['val'][$vars] = $arr;
            } else {
                $_SESSION['gxsess']['val'][$vars] = $val;
            }

        }

        // $uri = parse_url(Site::$url);
        // setcookie(session_name(),session_id(), time()+3600,$uri['path']);
    }

    public static function set($vars, $val ='')
    {
        self::set_session($vars, $val);
    }

    public static function destroy()
    {
        session_destroy();
        unset($_SESSION['gxsess']);
    }

    public static function remove($var)
    {
        unset($_SESSION['gxsess']['val'][$var]);
    }
}

/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */
