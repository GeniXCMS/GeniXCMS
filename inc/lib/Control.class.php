<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
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

/**
 * Control Class.
 *
 * This class will proccess the controller
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.1
 */
class Control
{
    public function __construct()
    {
    }

    /**
     * Control Handler Function. This is the loader of the controller. This
     * function is not necessary. Controller can be loaded directly below. Will
     * be removed on the next update.
     *
     * @param string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function handler($vars)
    {
        self::$vars();
    }

    /**
     * Control Frontend Inclusion Function. This will include the controller at
     * the Frontend directory.
     *
     * @param string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function incFront($vars, $param = '')
    {
        // print_r($param);
        // echo $vars;
        $file = GX_PATH.'/inc/lib/Control/Frontend/'.$vars.'.control.php';
        if (file_exists($file)) {
            include $file;
        } else {
            // echo "error";
            self::error('404');
        }
    }

    /**
     * Control Backend Inclusion Function. This will include the controller at
     * the Backend directory.
     *
     * @param string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function incBack($vars)
    {
        $file = GX_PATH.'/inc/lib/Control/Backend/'.$vars.'.control.php';
        if (file_exists($file)) {
            include $file;
        } else {
            self::error('404');
        }
    }

    /**
     * Control Frontend Handler Function. This will handle the controller which
     * file will be included at the Frontend controller.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     * Add New SMART URL handler for better and simple router.
     * @since 0.0.7
     */
    public static function frontend()
    {
        $arr = array('ajax', 'post', 'page', 'cat', 'mod', 'sitemap', 'rss',
                'account', 'search', 'author', 'tag', 'thumb', 'default'                
            );
        if (SMART_URL) {
            if (isset($_REQUEST) && $_REQUEST != '' && count($_REQUEST) > 0) {
                (SMART_URL && isset($_GET)) ? self::route($arr) : self::get($arr);
            } else {
                self::route($arr);
            }

//            self::route($arr);
        } elseif (!SMART_URL && isset($_GET) && $_GET != '' && count($_GET) > 0 ) {
            self::get($arr);
        } else {

            self::incFront('default');
        }
    }

    public static function get($arr)
    {
        $get = 0;
        foreach ($_GET as $k => $v) {
            if (in_array($k, $arr)
                || $k == 'paging'
                || $k == 'error'
                || $k == 'ajax'
                || $k == 'lang') {
                $get = $get + 1;
            } else {
                $get = $get;
            }
        }
//        echo $get;
        if ($get > 0) {
            foreach ($_GET as $k => $v) {
                if (in_array($k, $arr)) {
                    if ($k == 'ajax') {
                        self::ajax($v);
                    } else {
                        self::incFront($k);
                    }
                } elseif ($k == 'lang') {
                    self::incFront('default');
                } elseif ($k == 'error') {
                    self::error($v);
                } elseif (!in_array($k, $arr) && $k != 'paging') {
                    //self::error('404');
                } else {
                    self::incFront('default');
                }
            }
        } else {
            self::error('404');
        }
    }

    public static function route($arr)
    {
        $var = Router::run();
        if (isset($var['error']) || $var[0] == 'error') {
            self::error('404');
        } else {
            foreach ((array) $var[0] as $k => $v) {
                if ($k == '0' && $v != 'error' && $v != 'ajax') {
                    /** Frontpage */
                    self::incFront($v, $var);
                } elseif (!SMART_URL && isset($_REQUEST) && $_REQUEST != '' && count($_REQUEST) > 0) {
                    self::get($arr);
                } elseif ($v == 'error' || $k == 'error') {
                    $error = ($k == 'error') ? $v : '404';
                    self::error($error, $var);
                } elseif ($k == 'ajax') {
                    self::ajax($v, $var);
                } else {
                    if (in_array($k, $arr)) {
                        self::incFront($k, $var);
                    } else {
                        self::error('404');
                    }
                }
            }
        }
    }

    /**
     * Control Backend Handler Function. This will handle the controller which
     * file will be included at the Backend controller.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function backend($vars = '')
    {
        if (!empty($_GET['page'])) {
            self::incBack($_GET['page']);
        } else {
            self::incBack('default');
        }
    }

    /**
     * Control Error Handler Function. This will handle the error page. Default
     * is 404 not found. This handler include file which is called by specific
     * name at the Error directory.
     *
     * @param string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function error($vars = '', $val = '')
    {
        if (isset($vars) && $vars != '') {
            $file = GX_PATH.'/inc/lib/Control/Error/'.$vars.'.control.php';
            if (file_exists($file)) {
                include $file;
            }
        } else {
            include GX_PATH.'/inc/lib/Control/Error/unknown.control.php';
        }
    }

    /**
     * Control Install Handler Function. This will handle the Install page.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function install()
    {
        include GX_PATH.'/inc/lib/Control/Install/default.control.php';
    }

    public static function ajax($vars = '', $param = '')
    {
        if (isset($vars) && $vars != '') {
            $file = GX_PATH.'/inc/lib/Control/Ajax/'.$vars.'-ajax.control.php';
            if (file_exists($file)) {
                include $file;
            } else {
                self::error('404');
            }
        }
    }
}

/* End of file Control.class.php */
/* Location: ./inc/lib/Control.class.php */
