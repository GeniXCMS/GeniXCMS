<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 1.1.2 build date 20170912
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 *
 */
class Cache
{
    private static $enabled;
    private static $type;
    private static $path;
    private static $timeout;
    private static $cache_key;
    private static $cache_file;
    private static $redis_host;
    private static $redis_port;
    private static $redis_pass;
    private static $redis_db;

    /**
     * Cache Constructor.
     * Initializes cache settings from the database and generates a unique cache key based on the current URL.
     */
    public function __construct()
    {
        self::$enabled = Options::v('cache_enabled');
        self::$type = Options::v('cache_type') ?: 'file';
        self::$path = Options::v('cache_path');
        self::$timeout = (int) Options::v('cache_timeout') ?: 3600;

        self::$redis_host = Options::v('redis_host') ?: '127.0.0.1';
        self::$redis_port = Options::v('redis_port') ?: 6379;
        self::$redis_pass = Options::v('redis_pass');
        self::$redis_db = Options::v('redis_db') ?: 0;

        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'];
        self::$cache_key = 'gx_cache_' . md5($url);
        self::$cache_file = GX_PATH . self::$path . md5($url) . '.cache';
    }

    /**
     * Starts the output buffering and checks for existing valid cache content.
     * If a valid cache exists (File or Redis), it is served immediately, and the script terminates.
     */
    public static function start()
    {
        if (self::$enabled == 'on') {
            ob_start();
            ob_implicit_flush(0);

            if (self::$type == 'redis' && extension_loaded('redis')) {
                try {
                    $redis = new Redis();
                    if ($redis->connect(self::$redis_host, self::$redis_port, 1)) {
                        if (self::$redis_pass) {
                            $redis->auth(self::$redis_pass);
                        }
                        $redis->select(self::$redis_db);
                        $c = $redis->get(self::$cache_key);
                        if ($c) {
                            echo $c;
                            exit;
                        }
                    }
                } catch (Exception $e) {
                    // Fallback to no-cache if redis fails
                }
            } else {
                $cachefile = self::$cache_file;
                $cachetime = self::$timeout;

                if (file_exists($cachefile) && time() - $cachetime <= filemtime($cachefile)) {
                    $c = @file_get_contents($cachefile);
                    echo $c;
                    exit;
                } else {
                    @unlink($cachefile);
                }
            }
        }
    }

    /**
     * Ends the output buffering, captures the content, and stores it in the cache (File or Redis).
     * The content is then flushed to the browser.
     */
    public static function end()
    {
        if (self::$enabled == 'on') {
            $content = ob_get_contents();
            if ($content) {
                if (self::$type == 'redis' && extension_loaded('redis')) {
                    try {
                        $redis = new Redis();
                        if ($redis->connect(self::$redis_host, self::$redis_port, 1)) {
                            if (self::$redis_pass) {
                                $redis->auth(self::$redis_pass);
                            }
                            $redis->select(self::$redis_db);
                            $redis->setex(self::$cache_key, self::$timeout, $content);
                        }
                    } catch (Exception $e) {
                    }
                } else {
                    $cachefile = self::$cache_file;
                    @file_put_contents($cachefile, $content);
                }
            }
            ob_end_flush();
        }
    }
}
