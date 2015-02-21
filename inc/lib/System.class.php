<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class System
{

    static $version          = "0.0.1";
    static $v_release        = "";

    public function __construct () {
        if (self::existConf()) {
            # code...
            self::config('config');
            self::lang(GX_LANG);
        }else{
            GxMain::install();
        }
        
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

    public static function existConf () {
        if(file_exists(GX_PATH.'/inc/config/config.php')){
            return true;
        }else{
            return false;
        }
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


    public static function v () {
        return self::$version." ".self::$v_release;
    }


}

/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */