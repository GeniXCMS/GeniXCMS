<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140930
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* Url Class
*
* This class will create all the URL format automatically for Posts, Categories,
* pages, sitemap, rss.
* 
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class Url
{
    public function __construct() {
    }

    /**
    * Post URL Function.
    * This will create the posts url automatically based on the SMART_URL 
    * will formatted as friendly url if SMART_URL is set to true.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function post($vars) {
        switch (SMART_URL) {
            case true:
                # code...
                $url = Site::$url."/".self::slug($vars)."/{$vars}";
                break;
            
            default:
                # code...
                $url = Site::$url."/index.php?post={$vars}";
                break;

        }

        return $url;
    }

    /**
    * Page URL Function.
    * This will create the pages url automatically based on the SMART_URL 
    * will formatted as friendly url if SMART_URL is set to true.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function page($vars) {
        switch (SMART_URL) {
            case true:
                # code...
                $url = Site::$url."/".self::slug($vars).GX_URL_PREFIX;
                break;
            
            default:
                # code...
                $url = Site::$url."/index.php?page={$vars}";
                break;

        }

        return $url;
    }
    

    /**
    * Categories URL Function.
    * This will create the categories url automatically based on the SMART_URL 
    * will formatted as friendly url if SMART_URL is set to true.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function cat($vars) {
        switch (SMART_URL) {
            case true:
                # code...
                $url = Site::$url."/".$vars."/".Typo::slugify(Categories::name($vars));
                break;
            
            default:
                # code...
                $url = Site::$url."/index.php?cat={$vars}";
                break;

        }

        return $url;
    }
    
    /**
    * Custom URL Function.
    * This will create the custom url. It will result as is.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function custom($vars) {
        $url = $vars;
        return $url;
    }

    /**
    * Sitemap URL Function.
    * This will create the sitemap url automatically based on the SMART_URL 
    * will formatted as friendly url if SMART_URL is set to true.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function sitemap() {
        switch (SMART_URL) {
            case true:
                # code...
                $url = Site::$url."/sitemap".GX_URL_PREFIX;
                break;
            
            default:
                # code...
                $url = Site::$url."/index.php?page=sitemap";
                break;

        }

        return $url;
    }

    /**
    * RSS URL Function.
    * This will create the rss url automatically based on the SMART_URL 
    * will formatted as friendly url if SMART_URL is set to true.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function rss() {
        switch (SMART_URL) {
            case true:
                # code...
                $url = Site::$url."/rss".GX_URL_PREFIX;
                break;
            
            default:
                # code...
                $url = Site::$url."/index.php?rss";
                break;

        }

        return $url;
    }

    /**
    * URL Slug Function.
    * This will load the url slug from the database according to the posts id.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function slug($vars) {
        $s = Db::result("SELECT `slug` FROM `posts` WHERE `id` = '{$vars}' LIMIT 1");
        $s = $s[0]->slug;
        return $s;
    }
    
}

/* End of file Url.class.php */
/* Location: ./inc/lib/Url.class.php */