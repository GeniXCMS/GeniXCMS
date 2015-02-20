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
        $file = GX_PATH.'/inc/lib/Control/Frontend/'.$vars.'.control.php';
        if ( file_exists($file) ) {
            # code...
            include($file);
        }else{
            self::error('404');
        }
        
    }

    public static function incBack($vars) {
        $file = GX_PATH.'/inc/lib/Control/Backend/'.$vars.'.control.php';
        if ( file_exists($file) ) {
            # code...
            include($file);
        }else{
            self::error('404');
        }
    }

    public static function frontend() {
        
        if($_GET){
            //print_r($_GET);
            $arr = array ('post','page', 'cat', 'mod', 'sitemap', 'rss');

            foreach ($_GET as $k => $v) {
                # code...
                //echo $k;
                if(in_array($k, $arr)){
                    self::incFront($k);
                }elseif($k == "error"){
                    self::error($v);
                }elseif(!in_array($k, $arr) && $k != 'paging'){
                    self::error('404');
                }else{
                    self::incFront('default');
                }
            }
            
        }else{
            self::incFront('default');
        }
        
        // $arr = array ('post','page', 'cat', 'mod', 'sitemap', 'rss');
        // if(isset($_GET['post'])) {
        //     self::incFront('post');
        // }elseif(isset($_GET['page'])){
        //     self::incFront('page');
        // }elseif(isset($_GET['cat'])){
        //     self::incFront('cat');
        // }elseif(isset($_GET['mod'])){
        //     self::incFront('mod');
        // }elseif(isset($_GET['sitemap'])){
        //     self::incFront('sitemap');
        // }elseif(isset($_GET['rss'])){
        //     self::incFront('rss');
        // }else{
        //     self::incFront('default');
            
        // }
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


    public static function error ($vars="") {
        if( isset($vars) && $vars != "" ) {
            include(GX_PATH.'/inc/lib/Control/Error/'.$vars.'.control.php');
        }else{
            include(GX_PATH.'/inc/lib/Control/Error/404.control.php');
        }
    }

    public static function install () {
        include(GX_PATH.'/inc/lib/Control/Install/default.control.php');
    }

}


/* End of file Control.class.php */
/* Location: ./inc/lib/Control.class.php */