<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150202
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
class Pinger
{
    private static $myBlogName;
    private static $myBlogUrl;
    private static $myBlogUpdateUrl;
    private static $myBlogRSSFeedUrl;

    public function __construct()
    {
        self::$myBlogName = Options::v('sitename');
        self::$myBlogUrl = Options::v('siteurl');
        self::$myBlogUpdateUrl = Options::v('siteurl');
        self::$myBlogRSSFeedUrl = Url::rss();
    }

    public static function rpc($url)
    {
        new self();
        //require_once( GX_LIB.'/Vendor/IXR_Library.php' );
        $url = 'http://'.$url;
        $client = new IXR\Client\Client($url, false, 80, 3);
        // $client->getTimeoutIO = 3;
        // $client->useragent .= ' -- PingTool/1.0.0';
        // $client->debug = false;
        if ($client->query('weblogUpdates.extendedPing', self::$myBlogName, self::$myBlogUrl, self::$myBlogUpdateUrl, self::$myBlogRSSFeedUrl)) {
            return $client->getResponse();
        }
        //echo 'Failed extended XML-RPC ping for "' . $url . '": ' . $client->getErrorCode() . '->' . $client->getErrorMessage() . '<br />';
        if ($client->query('weblogUpdates.ping', self::$myBlogName, self::$myBlogUrl)) {
            return $client->getResponse();
        }
        //echo 'Failed basic XML-RPC ping for "' . $url . '": ' . $client->getErrorCode() . '->' . $client->getErrorMessage() . '<br />';
        return false;
    }

    public static function run($vars)
    {
        if (is_array($vars)) {
            foreach ($vars as $v) {
                self::rpc($v);
            }
        } else {
            $pinger = str_replace("\r\n", "\n", $vars);
            $pinger = explode("\n", $pinger);
            $pinger = str_replace("\r\n", '', $pinger);
            $pinger = str_replace('{{domain}}', Options::v('sitedomain'), $pinger);
            foreach ($pinger as $p) {
                self::rpc($p);
                //echo "'$p'<br>";
            }
        }
    }

    public static function isOn()
    {
        $on = Options::v('pinger_enable');
        if ($on == 'on') {
            return true;
        } else {
            return false;
        }
    }
}
