<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class System
{
    /** 
    * GeniXCMS Version Variable 
    * @return double
    */
    static $version          = "0.0.3";

    /** 
    * GeniXCMS Version Release 
    * @return string
    */
    static $v_release        = "";

    /**
    * System Constructor.
    * Initializing the system, check the config file, if exist run the config
    * loader. 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public function __construct () {
        self::config('config');
        new Db();
        self::lang(Options::get('system_lang'));
    }

    /**
    * System Library Loader.
    * This will include library which is called.
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
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
