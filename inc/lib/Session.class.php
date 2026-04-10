<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140925
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Session implements SessionHandlerInterface
{
    /**
     * Session Constructor.
     * Initializes database-backed sessions if SESSION_DB is enabled,
     * otherwise defaults to native PHP sessions.
     */
    public function __construct()
    {
        if (SESSION_DB == true) {
            // Set handler to overide SESSION
            session_set_save_handler($this, true);
            // Start the session
            // session_start();
            $this->gc((int) (SESSION_EXPIRES * 3600));
        }
        $this::start(SESSION_EXPIRES);
    }

    /**
     * Starts the session with specific duration and security parameters.
     * Configures HttpOnly, SameSite, and Secure flags for the session cookie.
     * Initialises the 'gxsess' session container if not present.
     *
     * @param int $duration Session duration in hours (default: 1).
     */
    public static function start($duration = 1)
    {
        $url = Site::$url;
        $site_id = !defined('SITE_ID') ? 'Installation' : SITE_ID;
        $expires = (int) (isset($duration) ? $duration * 3600 : 1 * 3600);
        $path = '/';
        $domain = (empty(Site::$domain) || Site::$domain == 'localhost' || Site::$domain == '127.0.0.1') ? null : Site::$domain;

        $cookie_params = [
            'lifetime' => (int) $expires,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ];

        if ($domain !== null) {
            $cookie_params['domain'] = $domain;
        }

        ini_set('session.gc_maxlifetime', $expires);
        session_set_cookie_params($cookie_params);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
        $GLOBALS['start_time'] = microtime(true);
    }

    /**
     * Generates a unique session key based on IP, user agent, and current hour.
     *
     * @return string MD5 hashed session key.
     */
    private static function sesKey()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $dt = date('Y-m-d H');

        $key = md5($ip . $browser . $dt);

        return $key;
    }

    /**
     * Generic session retriever (Placeholder).
     *
     * @param mixed $vars Search criteria.
     * @return void
     */
    public static function get_session($vars)
    {
    }

    /**
     * Retrieves a value from the 'gxsess' data container.
     *
     * @param string $vars Key name to retrieve.
     * @return mixed       The stored value or null if not found.
     */
    public static function val($vars)
    {
        $val = $_SESSION['gxsess']['val'];
        if (is_array($val)) {
            # code...
            foreach ($val as $k => $v) {
                if ($k == $vars) {
                    return $v;
                }
            }
        } else {
            return null;
        }
    }

    /**
     * Sets a value in the 'gxsess' data container.
     * Supports merging of arrays if $vars is an associative array.
     *
     * @param string|array $vars  Key name or associative array of data.
     * @param mixed        $val   Value to store (if $vars is a string).
     */
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

    /**
     * Alias for set_session().
     *
     * @param string|array $vars Key name or data.
     * @param mixed        $val  Value.
     */
    public static function set($vars, $val = '')
    {
        self::set_session($vars, $val);
    }

    /**
     * Destroys the current session and clears 'gxsess' data.
     */
    public static function logout()
    {
        session_destroy();
        unset($_SESSION['gxsess']);
    }

    /**
     * Removes a specific key from the 'gxsess' container.
     *
     * @param string $var Key name to remove.
     */
    public static function remove($var)
    {
        unset($_SESSION['gxsess']['val'][$var]);
    }

    /**
     * session_set_save_handler open() callback.
     * Establishes database connectivity for session persistence.
     *
     * @param string $path The path where to store/retrieve the session.
     * @param string $name The session name.
     * @return bool        True on success.
     */
    public function open($path, $name): bool
    {
        // If successful
        if (Db::connect()) {
            // Return True
            return true;
        }
        // Return False
        return false;
    }

    /**
     * session_set_save_handler close() callback.
     * Closes the database connection.
     *
     * @return bool True on success.
     */
    public function close(): bool
    {
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
     * session_set_save_handler read() callback.
     * Retrieves session data from the database.
     *
     * @param string $id The session ID.
     * @return string    Serialized session data or empty string.
     */
    #[\ReturnTypeWillChange]
    public function read($id)
    {
        // Set query
        $q = Db::result("SELECT `data` FROM `sessions` WHERE `id` = ?", [$id]);
        // Attempt execution
        // If successful
        if ($q) {
            // Return the data
            return isset($q[0]->data) ? $q[0]->data : "";
        } else {
            // Return an empty string
            return "";
        }
    }

    /**
     * session_set_save_handler write() callback.
     * Persists session data to the database using REPLACE INTO (upsert).
     *
     * @param string $id   The session ID.
     * @param string $data Serialized session data.
     * @return bool        True on success.
     */
    public function write($id, $data): bool
    {
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
     * session_set_save_handler destroy() callback.
     * Removes session record from the database.
     *
     * @param string $id The session ID.
     * @return bool       True on success.
     */
    public function destroy($id): bool
    {
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
     * session_set_save_handler gc() callback.
     * Cleans up expired sessions from the database.
     *
     * @param int $max Maximum session lifetime (seconds).
     * @return int|bool Number of deleted sessions or false on failure.
     */
    #[\ReturnTypeWillChange]
    public function gc($max)
    {
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
