<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Pinger.class.php
* version : 0.0.1 pre
* build : 20150125
*/


class Pinger
{

    static $myBlogName          = "";
    static $myBlogUrl           = "";
    static $myBlogUpdateUrl     = "";
    static $myBlogRSSFeedUrl    = "";


    public function __construct () {
        
        $myBlogName          = Options::get('sitename');
        $myBlogUrl           = Options::get('siteurl');
        $myBlogUpdateUrl     = Options::get('siteurl');
        $myBlogRSSFeedUrl    = Url::sitemap();
    }

    public static function rpc ($url) {
        require_once( GX_LIB.'/Vendor/IXR_Library.php' );
        $url = 'http://'.$url;
        $client = new IXR_Client( $url );
        $client->timeout = 3;
        $client->useragent .= ' -- PingTool/1.0.0';
        $client->debug = false;
        if( $client->query( 'weblogUpdates.extendedPing', self::$myBlogName, self::$myBlogUrl, self::$myBlogUpdateUrl, self::$myBlogRSSFeedUrl ) )
        {
            return $client->getResponse();
        }
        echo 'Failed extended XML-RPC ping for "' . $url . '": ' . $client->getErrorCode() . '->' . $client->getErrorMessage() . '<br />';
        if( $client->query( 'weblogUpdates.ping', self::$myBlogName, self::$myBlogUrl ) )
        {
            return $client->getResponse();
        }
        echo 'Failed basic XML-RPC ping for "' . $url . '": ' . $client->getErrorCode() . '->' . $client->getErrorMessage() . '<br />';
        return false;
    }
}