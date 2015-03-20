<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* Control Class
*
* This class will proccess the controller
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class Control
{

    public function __construct () {

    }

    /**
    * Control Handler Function.
    * This is the loader of the controller. This function is not necessary. 
    * Controller can be loaded directly below. Will be removed on the next
    * update.
    *
    * @param string $vars
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1 
    */
    public static function handler($vars) {
            self::$vars();
    }

    /**
    * Control Frontend Inclusion Function.
    * This will include the controller at the Frontend directory.
    *
    * @param string $vars
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1 
    */
    public static function incFront($vars) {
        $file = GX_PATH.'/inc/lib/Control/Frontend/'.$vars.'.control.php';
        if ( file_exists($file) ) {
            # code...
            include($file);
        }else{
            self::error('404');
        }
        
    }

    /**
    * Control Backend Inclusion Function.
    * This will include the controller at the Backend directory.
    *
    * @param string $vars
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1 
    */
    public static function incBack($vars) {
        $file = GX_PATH.'/inc/lib/Control/Backend/'.$vars.'.control.php';
        if ( file_exists($file) ) {
            # code...
            include($file);
        }else{
            self::error('404');
        }
    }

    /**
    * Control Frontend Handler Function.
    * This will handle the controller which file will be included at the Frontend
    * controller.
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1 
    */
    public static function frontend() {
        
        if($_GET){
            //print_r($_GET);
            $arr = array ('post','page', 'cat', 'mod', 'sitemap', 'rss','pay','paidorder','cancelorder');
            $get =0;
            foreach ($_GET as $k => $v) {
                    # code...
                    //echo $k;
                if (in_array($k,$arr ) 
                    || $k == 'paging' 
                    || $k == 'error' ) {
                    $get = $get+1;
                }else{
                    $get = $get;
                }


            }
            //echo $get;
            if ($get>0) {
                foreach ($_GET as $k => $v) {
                    if(in_array($k, $arr)){
                        self::incFront($k);
                    }elseif($k == "error"){
                        self::error($v);
                    }elseif(!in_array($k, $arr) && $k != 'paging'){
                        //self::error('404');
                    }else{
                        self::incFront('default');
                    }
                }
            }else{
                self::error('404');
            }
            
            
        }else{
            self::incFront('default');
        }
        
    }

    /**
    * Control Backend Handler Function.
    * This will handle the controller which file will be included at the Backend
    * controller.
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1 
    */
    public static function backend($vars="") {
        //if(isset($_GET['post'])) {
        //echo "frontend";
        if(!empty($_GET['page'])) {
            self::incBack($_GET['page']);
        }else{
            self::incBack('default');
        }
    }

    /**
    * Control Error Handler Function.
    * This will handle the error page. Default is 404 not found. This handler 
    * include file which is called by specific name at the Error directory.
    *
    * @param string $vars
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1 
    */
    public static function error ($vars="", $val='') {
        if( isset($vars) && $vars != "" ) {
            include(GX_PATH.'/inc/lib/Control/Error/'.$vars.'.control.php');
        }else{
            include(GX_PATH.'/inc/lib/Control/Error/unknown.control.php');
        }
    }

    /**
    * Control Install Handler Function.
    * This will handle the Install page.
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1 
    */
    public static function install () {
        include(GX_PATH.'/inc/lib/Control/Install/default.control.php');
    }

}


/* End of file Control.class.php */
/* Location: ./inc/lib/Control.class.php */