<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.2 build date 20150309
 * @version 2.0.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 * @author GenixCMS <genixcms@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Token Class - Re-designed for high reliability (One-Token-Per-Session model).
 * This ensures stable CSRF protection across deep AJAX operations like elFinder.
 */
class Token
{
    public function __construct()
    {
        self::create();
    }

    /**
     * Initializes or retrieves the persistent session token.
     */
    public static function create()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        $pairing = self::pairing();
        $reqToken = $_GET['token'] ?? null;
        if (!$reqToken && preg_match('#/ajax/[^/]+/([a-zA-Z0-9]{10,})#', $_SERVER['REQUEST_URI'] ?? '', $m)) {
            $reqToken = $m[1];
        }

        // If a valid 20-char token is being sent, ADOPT IT as primary to fix race conditions
        if ($reqToken && strlen($reqToken) >= 20 && !isset($_SESSION['gx_token'])) {
            $_SESSION['gx_token'] = $reqToken;
            $_SESSION['gx_token_pairing'] = $pairing;
            $_SESSION['gx_token_time'] = time();
        }

        // Generate a persistent session token if it doesn't exist
        if (!isset($_SESSION['gx_token']) || empty($_SESSION['gx_token'])) {
            $_SESSION['gx_token'] = Typo::createToken(20);
            $_SESSION['gx_token_pairing'] = $pairing;
            $_SESSION['gx_token_time'] = time();
        }

        $token = $_SESSION['gx_token'];
        if (!defined('TOKEN'))
            define('TOKEN', $token);
        return $token;
    }

    /**
     * Validates a provided token against the session storage.
     */
    public static function validate($token, $is_ajax = false)
    {
        if (empty($token) || !isset($_SESSION)) {
            return false;
        }

        // Ensure primary token exists
        self::create();

        // 1. Check Primary Persistent Token
        if ($token === ($_SESSION['gx_token'] ?? '')) {
            return true;
        }

        // 2. Self-Healing Fallback: If it's a valid legacy token, PROMOTE it to primary!
        if (isset($_SESSION['gx_tokens']) && is_array($_SESSION['gx_tokens'])) {
            $found = false;
            if (isset($_SESSION['gx_tokens'][$token])) {
                $found = $token;
            } else {
                foreach ($_SESSION['gx_tokens'] as $t => $data) {
                    if (strpos($t, $token) === 0) {
                        $found = $t;
                        break;
                    }
                }
            }

            if ($found) {
                // Promote this token to primary so future requests are stable
                $_SESSION['gx_token'] = $token;
                $_SESSION['gx_token_pairing'] = $_SESSION['gx_tokens'][$found]['pairing'] ?? self::pairing();
                $_SESSION['gx_token_time'] = $_SESSION['gx_tokens'][$found]['time'] ?? time();
                // Clean up the legacy list to prevent bloat
                unset($_SESSION['gx_tokens'][$found]);
                return true;
            }
        }

        error_log("SECURITY NOTICE: Token mismatch or missing for $token. Session ID: " . session_id());
        return false;
    }

    public static function isValid($token)
    {
        return self::validate($token);
    }

    public static function isExist($token, $is_ajax = false)
    {
        return self::validate($token, $is_ajax);
    }

    public static function remove($token)
    {
        // One-token-per-session is persistent, we only remove if explicitly asked to clear session
        if ($token === ($_SESSION['gx_token'] ?? '')) {
            unset($_SESSION['gx_token']);
            unset($_SESSION['gx_token_pairing']);
            unset($_SESSION['gx_token_time']);
        }
        return true;
    }

    /**
     * Generates a security fingerprint for the current user context.
     */
    public static function pairing()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 50) : '';
        $site_id = !defined('SITE_ID') ? 'Installation' : SITE_ID;
        return md5($ip . $ua . $site_id);
    }

    public static function urlMatch($token, $is_ajax = false)
    {
        return self::validate($token, $is_ajax);
    }
}
