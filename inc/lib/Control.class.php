<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20141006
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Control Class.
 *
 * This class will proccess the controller
 *
 * @since 0.0.1
 */
class Control
{
    /**
     * Control Constructor.
     */
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
     * @since 0.0.1
     */
    public static function incFront($vars, $param = '')
    {
        // print_r($param);
        // echo $vars;
        $file = GX_PATH . '/inc/lib/Control/Frontend/' . $vars . '.control.php';
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
     * @since 0.0.1
     */
    public static function incBack($vars)
    {
        $file = GX_PATH . '/inc/lib/Control/Backend/' . $vars . '.control.php';
        if (file_exists($file)) {
            include $file;
        } else {
            self::error('404', 'File Not Found');
        }
    }

    /**
     * Entry point for frontend requests.
     * Determines whether to use traditional GET routing or SMART_URL routing.
     *
     * @since 0.0.1
     * @since 0.0.7 Added SMART_URL support.
     */
    public static function frontend()
    {
        $arr = array(
            'api',
            'ajax',
            'post',
            'page',
            'cat',
            'mod',
            'sitemap',
            'rss',
            'account',
            'search',
            'author',
            'tag',
            'thumb',
            'default',
            'login',
            'register',
            'forgotpass',
            'logout',
            'archive'
        );
        if (defined('SMART_URL') && SMART_URL) {
            if (isset($_REQUEST) && $_REQUEST != '' && count($_REQUEST) > 0) {
                (SMART_URL && isset($_GET)) ? self::route($arr) : self::get($arr);
            } else {
                self::route($arr);
            }
        } elseif (!SMART_URL && isset($_GET) && $_GET != '' && count($_GET) > 0) {
            self::get($arr);
        } else {
            self::incFront('default');
        }
    }

    /**
     * Handles routing via traditional $_GET parameters.
     *
     * @param array $arr List of valid controller keys.
     */
    public static function get($arr)
    {
        $get = 0;
        foreach ($_GET as $k => $v) {
            if (
                in_array($k, $arr)
                || $k == 'api'
                || $k == 'paging'
                || $k == 'error'
                || $k == 'ajax'
                || $k == 'lang'
            ) {
                $get = (int) $get + 1;
            } else {
                $get = (int) $get;
            }
        }

        if ($get > 0) {
            foreach ($_GET as $k => $v) {
                if (in_array($k, $arr)) {
                    if ($k == 'ajax') {
                        self::ajax($v);
                    } elseif ($k == 'api') {
                        $res = $_GET['resource'] ?? '';
                        $id = $_GET['identifier'] ?? '';
                        $act = $_GET['action'] ?? '';

                        // Fallback: Parse `api=module/identifier/action` if resource wasn't explicit
                        if (empty($res) && !empty($_GET['api']) && $_GET['api'] !== '1' && $_GET['api'] !== 'true') {
                            $apiParts = explode('/', $_GET['api']);
                            $res = $apiParts[0] ?? '';
                            $id = $apiParts[1] ?? '';
                            $act = $apiParts[2] ?? '';
                        }

                        self::api($res, $id, $act);
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

    /**
     * Handles routing via SMART_URL (pretty URLs) using the Router class.
     *
     * @param array $arr List of valid controller keys.
     */
    public static function route($arr)
    {
        $var = Router::run();
        // fallbacks to get array
        if ((isset($var['error']) || (isset($var[0]) && $var[0] == 'error')) && count($_GET) > 0) {
            self::get($arr);
            return;
        }

        if (isset($var['error']) || (isset($var[0]) && $var[0] == 'error')) {
            self::error('404');
        } else {
            foreach ((array) $var[0] as $k => $v) {
                if ($k == '0' && $v != 'error' && $v != 'ajax' && $v != 'api') {
                    /** Frontpage */
                    self::incFront($v, $var);
                } elseif (!SMART_URL && isset($_REQUEST) && $_REQUEST != '' && count($_REQUEST) > 0) {
                    self::get($arr);
                } elseif ($v == 'error' || $k == 'error') {
                    $error = ($k == 'error') ? $v : '404';
                    self::error($error, $var);
                } elseif ($k == 'ajax' || $v == 'ajax') {
                    self::ajax($v, $var);
                } elseif ($k == 'api' || $v == 'api') {

                    $res = '';
                    $id = '';
                    $act = '';
                    foreach ($var as $vk => $vv) {
                        if (isset($vv['resource']))
                            $res = $vv['resource'];
                        if (isset($vv['identifier']))
                            $id = $vv['identifier'];
                        if (isset($vv['action']))
                            $act = $vv['action'];
                    }

                    // Fallback in case the smart router fell back to $_GET
                    if (empty($res) && !empty($_GET['api']) && $_GET['api'] !== '1' && $_GET['api'] !== 'true') {
                        $apiParts = explode('/', $_GET['api']);
                        $res = $apiParts[0] ?? '';
                        $id = $apiParts[1] ?? '';
                        $act = $apiParts[2] ?? '';
                    }

                    self::api($res, $id, $act);
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
     * Dispatches the request to the API controller.
     *
     * @param string $resource   API resource name.
     * @param string $identifier Resource identifier.
     * @param string $action     Specific action to perform.
     */
    public static function api($resource = '', $identifier = '', $action = '')
    {
        Api::dispatch($resource, $identifier, $action);
    }

    /**
     * Control Backend Handler Function. This will handle the controller which
     * file will be included at the Backend controller.
     *
     * @since 0.0.1
     */
    public static function backend()
    {
        if (!empty($_GET['page'])) {
            self::incBack(urlencode($_GET['page']));
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
     * @since 0.0.1
     */
    public static function error($vars = '', $val = '')
    {
        if (isset($vars) && $vars != '') {
            $file = GX_PATH . '/inc/lib/Control/Error/' . $vars . '.control.php';
            if (file_exists($file)) {
                include $file;
            }
        } else {
            include GX_PATH . '/inc/lib/Control/Error/unknown.control.php';
        }
    }

    /**
     * Control Install Handler Function. This will handle the Install page.
     *
     * @since 0.0.1
     */
    public static function install()
    {
        include GX_PATH . '/inc/lib/Control/Install/default.control.php';
    }

    /**
     * Handles and dispatches Ajax requests to specific Ajax controller files.
     *
     * @param string $vars  The Ajax action/resource name.
     * @param mixed  $param Additional data to pass.
     */
    public static function ajax($vars = '', $param = '')
    {
        if (isset($vars) && $vars != '') {
            $page = "ajax";
            $file = GX_PATH . '/inc/lib/Control/Ajax/' . $vars . '-ajax.control.php';
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
