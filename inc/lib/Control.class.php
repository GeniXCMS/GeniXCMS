<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Control.class.php
* version : 0.0.1 pre
* build : 20141006
*/

class Control
{

    public function __construct () {

    }

    public static function handler($vars) {
            self::$vars();
    }

    public static function incFront($vars) {
        include(GX_PATH.'/inc/lib/Control/Frontend/'.$vars.'.control.php');
    }

    public static function incBack($vars) {
        include(GX_PATH.'/inc/lib/Control/Backend/'.$vars.'.control.php');
    }

    public static function frontend() {
        if(isset($_GET['post'])) {
        //echo "frontend";
            self::incFront('post');
        }elseif(isset($_GET['page'])){
            self::incFront('page');
        }elseif(isset($_GET['cat'])){
            self::incFront('cat');
        }elseif(isset($_GET['mod'])){
            self::incFront('mod');
        }elseif(isset($_GET['sitemap'])){
            self::incFront('sitemap');
        }elseif(isset($_GET['rss'])){
            self::incFront('rss');
        }else{
            self::incFront('default');
        }
    }

    public static function backend($vars="") {
        //if(isset($_GET['post'])) {
        //echo "frontend";
        if(!empty($_GET['page'])) {
            self::incBack($_GET['page']);
        }else{
            self::incBack('default');
        }
    }

    public static function install () {
        include(GX_PATH.'/inc/lib/Control/Install/default.control.php');
    }

}


/* End of file Control.class.php */
/* Location: ./inc/lib/Control.class.php */