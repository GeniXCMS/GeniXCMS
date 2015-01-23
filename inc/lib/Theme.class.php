<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
* filename : Theme.class.php
* version : 0.0.1 pre
* build : 20140925
*/

class Theme
{
    public function __construct() {
        global $GLOBALS;
    }

    public static function theme($var, $data='') {
        if (isset($data)) {
            # code...
            $GLOBALS['data'] = $data;
        }
        include(GX_THEME.THEME.'/'.$var.'.php');
    }

    public static function admin($var) {
        include(GX_PATH.'/gxadmin/themes/'.$var.'.php');
    }

    public static function header($vars=""){
        header("Cache-Control: must-revalidate,max-age=300,s-maxage=900");
        $offset = 60 * 60 * 24 * 3;
        $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        header($ExpStr);
        header("Content-Type: text/html; charset=utf-8");

        if (isset($vars)) {
            # code...
            $GLOBALS['data'] = $vars;
            self::theme('header', $vars);
        }else{
            self::theme('header');
        }
        
    }
    public static function footer($vars=""){
        global $GLOBALS;
        if (isset($vars)) {
            # code...
            $GLOBALS['data'] = $vars;
            self::theme('footer', $vars);
        }else{
            self::theme('footer');
        }

        $end_time = microtime(TRUE);
        $time_taken = $end_time - $GLOBALS['start_time'];
        $time_taken = round($time_taken,5);
        echo '<center>Page generated in '.$time_taken.' seconds.</center>';
        
    }

    public static function editor(){
        $editor = Options::get('use_editor');
        if($editor == 'on'){
            $GLOBALS['editor'] = true;
        }else{
            $GLOBALS['editor'] = false;
        }
        
        //return $editor;
    }

    public static function validator($vars =""){
        $GLOBALS['validator'] = true;
        $GLOBALS['validator_js'] = $vars;
        //return $editor;
    }
}

/* End of file Theme.class.php */
/* Location: ./inc/lib/Theme.class.php */