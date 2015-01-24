<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : System.class.php
* version : 0.0.1 pre
* build : 20140925
*/

class System
{

    var $data = "";

    public function __construct () {
        self::config('config');
        self::lang(GX_LANG);
    }

    public static function lib($var) {
        include(GX_LIB.$var.'.class.php');
    }

    

    public static function lang($vars) {
        include(GX_PATH.'/inc/lang/'.$vars.'.lang.php');
    }

    public static function config($var) {
        include(GX_PATH.'/inc/config/'.$var.'.php');
    }

    // At the beginning of each page call these functions
    public static function gZip () {
        #ob_start(ob_gzhandler);
        ob_start();
        ob_implicit_flush(0);
    }

    // Call this function to output everything as gzipped content.
    public static function Zipped () {
        global $HTTP_ACCEPT_ENCODING;
        if( headers_sent() ){
            $encoding = false;
        }elseif( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ){
            $encoding = 'x-gzip';
        }elseif( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ){
            $encoding = 'gzip';
        }else{
            $encoding = false;
        }

        if( $encoding ){
            $contents = ob_get_contents();
            ob_end_clean();
            header('Content-Encoding: '.$encoding);
            print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
            $size = strlen($contents);
            $contents = gzcompress($contents, 9);
            $contents = substr($contents, 0, $size);
            print($contents);
            exit();
        }else{
            ob_end_flush();
            exit();
        }
    }

    public static function admin () {
        

    }

    public static function inc ($vars, $data = "") {
        include(GX_PATH.'/gxadmin/inc/'.$vars.'.php');
    }




}

/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */