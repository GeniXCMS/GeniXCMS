<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 2.0.0-alpha
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Session implements SessionHandlerInterface
{
    public function __construct()
    {
        if(SESSION_DB == true) {
            // Set handler to overide SESSION
            session_set_save_handler($this, true);
            // Start the session
            // session_start();
            $this->gc((int)(SESSION_EXPIRES*3600));
        }
        $this::start(SESSION_EXPIRES);
    }

    public static function start($duration = 1)
    {
        $url = Site::$url;
        $url = ( $url == "" ) ? $_SERVER['REQUEST_URI']: $url;
        $site_id = !defined('SITE_ID') ? 'Installation' : SITE_ID;
        $expires = (int)(isset($duration) ? $duration * 3600: 1 * 3600);
        $uri = parse_url($url);
        $path = isset($uri['path']) ? $uri['path'] : '/';
        $domain = Site::$domain == "" ? $_SERVER["HTTP_HOST"] : Site::$domain;
        
        session_name('GeniXCMS-'.$site_id);
        session_set_cookie_params([
            'lifetime' => (int)$expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => false,
            'httponly' => true,
        ]);
        session_start([
            'cookie_lifetime' => 31536000,
        ]);
        
        // print_r($_SESSION);
        
        // unset($_SESSION);
        if (!isset($_SESSION['gxsess']) || $_SESSION['gxsess'] == '') {
            $_SESSION['gxsess'] = array(
                                    'key' => self::sesKey(),
                                    'time' => date('Y-m-d H:i:s'),
                                    'val' => array(),
                                );
        }
        if (isset($_SESSION['gxsess']) || $_SESSION['gxsess'] != '') {
            $expires = isset($_SESSION['gxsess']['val']['rememberme']) && $_SESSION['gxsess']['val']['rememberme'] == true ? 3600 * 24 * 365 : $expires;
        }
        session_regenerate_id();
        
        setcookie(name: session_name(), value: session_id(), expires_or_options: (int)(time() + $expires), path: $path, domain: $domain, secure: false, httponly: true);
        $GLOBALS['start_time'] = microtime(true);
    }

    private static function sesKey()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']: '';
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
                if( $k == $vars ) {
                    return $v;
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

    public static function logout()
    {
        session_destroy();
        unset($_SESSION['gxsess']);
    }

    public static function remove($var)
    {
        unset($_SESSION['gxsess']['val'][$var]);
    }
    
    /**
     * Open
     */
    public function open($path, $name): bool {
        // If successful
        if (Db::connect()) {
            // Return True
            return true;
        }
        // Return False
        return false;
    }

    /**
     * Close
     */
    public function close(): bool {
        // Close the database connection
        // If successful
        if (Db::close()) {
            // Return True
            return true;
        }
        // Return False
        return false;
    }

    /**
     * Read
     */
    #[\ReturnTypeWillChange]
    public function read($id) {
        // Set query
        $q = Db::result("SELECT `data` FROM `sessions` WHERE `id` = ?", [$id]);
        // Attempt execution
        // If successful
        if ($q) {
            // Return the data
            return isset($q[0]->data) ? $q[0]->data: "";
        }else{
            // Return an empty string
            return "";
        }
    }

    /**
     * Write
     */
    public function write($id, $data): bool {
        // Create time stamp
        $access = time();

        // Set query
        $q = Db::query("REPLACE INTO `sessions` VALUES (?, ?, ?)", [$id, $access, $data]);


        // Attempt Execution
        // If successful
        if ($q) {
            // Return True
            return true;
        }

        // Return False
        return false;
    }

    /**
     * Destroy
     */
    public function destroy($id): bool {
        // Set query
        $q = Db::query("DELETE FROM `sessions` WHERE `id` = ?", [$id]);


        // Attempt execution
        // If successful
        if ($q) {
            // Return True
            return true;
        }

        // Return False
        return false;
    }

    /**
     * Garbage Collection
     */
    #[\ReturnTypeWillChange]
    public function gc($max) {
        // Calculate what is to be deemed old
        $old = time() - $max;
        $q = Db::query("DELETE FROM `sessions` WHERE `access` < ?", [$old]);
        if ($q) {
            return (int) Db::$num_rows;
        }
        
        // Return False
        return false;
    }
}

/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */
