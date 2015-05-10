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
        if (self::exist($var)) {
            include(GX_THEME.THEME.'/'.$var.'.php');
        }else{
            Control::error('unknown','Theme file is missing.');
        }
        
    }

    public static function exist ($vars) {
        if(file_exists(GX_THEME.THEME.'/'.$vars.'.php')) {
            return true;
        }else{
            return false;
        }
    }
    
    public static function admin($var, $data='') {
        if (isset($data)) {
            # code...
            $GLOBALS['data'] = $data;
        }
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

    public static function install ($var) {
        include(GX_PATH.'/gxadmin/themes/install/'.$var.'.php');
    }

    public static function options($var) {
        if (self::optionsExist($var)) {
            include(GX_THEME.$var.'/options.php');
        }
        
    }

    public static function optionsExist($var) {
        if (file_exists(GX_THEME.$var.'/options.php')) {
            return true;
        }else{
            return false;
        }
        
    }

    public static function thmList(){
        //$mod = '';
        $handle = dir(GX_THEME);
        while (false !== ($entry = $handle->read())) {
            if ($entry != "." && $entry != ".." ) {
                    $dir = GX_THEME.$entry;
                    if(is_dir($dir) == true){
                        $thm[] = basename($dir);
                    } 
            }
        }
        
        $handle->close();
        return $thm;
    }

    public static function activate($thm) {
        if (Options::update('themes', $thm)) {
            return true;
        }else{
            return false;
        }
    }


    public static function data($vars){
        $file = GX_THEME.'/'.$vars.'/themeinfo.php';
        $handle = fopen($file, 'r');
        $data = fread($handle, filesize($file));
        fclose($handle);
        preg_match('/\* Name: (.*)\n\*/U', $data, $matches);
        $d['name'] = $matches[1];
        preg_match('/\* Desc: (.*)\n\*/U', $data, $matches);
        $d['desc'] = $matches[1];
        preg_match('/\* Version: (.*)\n\*/U', $data, $matches);
        $d['version'] = $matches[1];
        preg_match('/\* Build: (.*)\n\*/U', $data, $matches);
        $d['build'] = $matches[1];
        preg_match('/\* Developer: (.*)\n\*/U', $data, $matches);
        $d['developer'] = $matches[1];
        preg_match('/\* URI: (.*)\n\*/U', $data, $matches);
        $d['url'] = $matches[1];
        preg_match('/\* License: (.*)\n\*/U', $data, $matches);
        $d['license'] = $matches[1];
        preg_match('/\* Icon: (.*)\n\*/U', $data, $matches);
        $d['icon'] = $matches[1];
        return $d;
    }

    public static function isActive($thm){
        if(Options::get('themes') === $thm){
            return true;
        }else{
            return false;
        }
    }

    public static function loader(){
        $theme = Options::get('themes');
        define('THEME', $theme);
    }

    public static function thmMenu(){
        $thm = Options::get('themes');
        //$mod = self::modList();
        //print_r($mod);
        $list = '';
        # code...
        $data = self::data($thm);
        if(isset($_GET['page']) 
            && $_GET['page'] == 'themes' 
            && isset($_GET['view'])
            && $_GET['view'] == 'options'){
            $class = 'class="active"';
        }else{
            $class = "";
        }
        if (self::optionsExist($thm)) {
            $list .= "
            <li $class>
                <a href=\"index.php?page=themes&view=options\" >".$data['icon']." ".$data['name']."</a>
            </li>";
        }else{
            $list = '';
        }

        return $list;
    }
}

/* End of file Theme.class.php */
/* Location: ./inc/lib/Theme.class.php */