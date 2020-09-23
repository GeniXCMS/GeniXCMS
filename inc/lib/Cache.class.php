<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.2 build date 20170912
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
* 
*/
class Cache
{
    private static $enabled;
    private static $path;
    private static $timeout;
    private static $cachefile;

    function __construct()
    {
        self::$enabled = Options::v('cache_enabled');
        self::$path = Options::v('cache_path');
        self::$timeout = Options::v('cache_timeout');
        $url    = 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'];
        self::$cachefile = GX_PATH.self::$path.md5($url).'.cache';
    }

    public static function start() {
        if (self::$enabled == 'on') {
            # code...
        
            $cachefile = self::$cachefile;
            $cachetime = self::$timeout; 

            if(file_exists($cachefile) && time()-$cachetime <= filemtime($cachefile)){
              $c = @file_get_contents($cachefile);
              echo $c;
              exit;
            }else{
              @unlink($cachefile);
            }
        } 
    }

    public static function end() {
        if (self::$enabled == 'on') {
            $cachefile = self::$cachefile;
            $c = ob_get_contents();
            file_put_contents($cachefile, $c);
        }
    }
}