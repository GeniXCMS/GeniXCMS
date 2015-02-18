<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1-pre build date 20141006
* @version 0.0.1-pre
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


/**
* Control Class
*
* This class will proccess the control c 
* the categories.
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1-pre
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