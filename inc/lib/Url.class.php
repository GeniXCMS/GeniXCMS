<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
* filename : Url.class.php
* version : 0.0.1 pre
* build : 20140930
*/

class Url
{
    public function __construct() {
    }

    public static function post($vars) {
        switch (SMART_URL) {
            case true:
                # code...
                $url = GX_URL."/".self::slug($vars)."/{$vars}";
                break;
            
            default:
                # code...
                $url = GX_URL."/index.php?post={$vars}";
                break;

        }

        return $url;
    }

     public static function page($vars) {
        switch (SMART_URL) {
            case true:
                # code...
                $url = GX_URL."/".self::slug($vars).GX_URL_PREFIX;
                break;
            
            default:
                # code...
                $url = GX_URL."/index.php?page={$vars}";
                break;

        }

        return $url;
    }
    


    public static function cat($vars) {
        switch (SMART_URL) {
            case true:
                # code...
                $url = GX_URL."/".$vars."/".Typo::slugify(Categories::name($vars));
                break;
            
            default:
                # code...
                $url = GX_URL."/index.php?cat={$vars}";
                break;

        }

        return $url;
    }
    

    public static function custom($vars) {
        // switch (SMART_URL) {
        //     case true:
        //         # code...
        //         // $url = GX_URL."/".self::slug($vars).GX_URL_PREFIX;
        //         $url = $vars;
        //         break;
            
        //     default:
        //         # code...
        //         $url = GX_URL."/index.php?page={$vars}";

        //         break;

        // }
        $url = $vars;
        return $url;
    }

    public static function slug($vars) {
        $s = Db::result("SELECT `slug` FROM `posts` WHERE `id` = '{$vars}' LIMIT 1");
        $s = $s[0]->slug;
        return $s;
    }
    
}

/* End of file Url.class.php */
/* Location: ./inc/lib/Url.class.php */