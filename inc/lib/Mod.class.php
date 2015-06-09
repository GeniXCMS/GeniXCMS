<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140928
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class Mod
{
    public function __construct() {
        
    }
    
    public static function mod($var) {
        include(GX_MOD.$var.'/index.php');
    }

    public static function options($var) {
        include(GX_MOD.$var.'/options.php');
    }

    public static function modList(){
        //$mod = '';
        $handle = dir(GX_MOD);
        while (false !== ($entry = $handle->read())) {
            if ($entry != "." && $entry != ".." ) {
                    $dir = GX_MOD.$entry;
                    if(is_dir($dir) == true){
                        $mod[] = basename($dir);
                    } 
            }
        }
        
        $handle->close();
        return $mod;
    }

    public static function data($vars){
        $file = GX_MOD.'/'.$vars.'/index.php';
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

    public static function ModMenu(){
        $json = Options::get('modules');
        $mod = json_decode($json, true);
        //$mod = self::modList();
        //print_r($mod);
        if(is_array($mod)){
            $list = '';
            asort($mod);
            foreach ($mod as $m) {
                # code...
                $data = self::data($m);
                if(isset($_GET['mod']) && $_GET['mod'] == $m){
                    $class = 'class="active"';
                }else{
                    $class = "";
                }
                $list .= "<li $class><a href=\"index.php?page=mods&mod={$m}\" >".$data['icon']." ".$data['name']."</a></li>";
            }
        }else{
            $list = "";
        }
        return $list;
    }

    public static function inc($vars, $data, $dir){
        include($dir."/".$vars.".php");
    }

    public static function activate($mod){
        $json = Options::get('modules');
        $mods = json_decode($json, true);
        //print_r($mods);
        if (!is_array($mods) || $mods == "") {
            $mods = array();
        }
        if (!in_array($mod, $mods)) {
            # code...
            $mods = array_merge($mods, array($mod));
        }
        

        $mods = json_encode($mods);

        $mods = Options::update('modules', $mods);
        if($mods){
            return true;
        }else{
            return false;
        }
    }

    public static function deactivate($mod){
        $mods = Options::get('modules');
        $mods = json_decode($mods, true);
        if (!is_array($mods) || $mods == "") {
            $mods = array();
        }
        //print_r($mods);
        $arr = "";
        for ($i=0;$i<count($mods);$i++) {
            # code...
            if ($mods[$i] == $mod) {
                //unset($mods[$i]);
            }else{
                $arr[] = $mods[$i];
            }
            
        }
        //print_r($arr);
        //asort($mods);
        $mods = json_encode($arr);
        $mods = Options::update('modules', $mods);
        if($mods){
            return true;
        }else{
            return false;
        }
    }

    public static function isActive($mod){
        $json = Options::get('modules');
        $mods = json_decode($json, true);
        //print_r($mods);
        if (!is_array($mods) || $mods == "") {
            $mods = array();
        }

        if(in_array($mod, $mods)){
            return true;
        }else{
            return false;
        }
    }

    public static function loader() {
        $data = "";
        if (isset($_GET['page']) && $_GET['page'] == "modules") {
            if (isset($_GET['act'])) {

                if ($_GET['act'] == ACTIVATE) {

                    if(!Token::isExist($_GET['token'])){
                        $alertred[] = TOKEN_NOT_EXIST;
                    }

                    if(!isset($alertred)){
                        self::activate($_GET['modules']);
                        $GLOBALS['alertgreen'] = MODULES_ACTIVATED;
                    }else{
                        $GLOBALS['alertred'] = $alertred;
                    }
                }elseif($_GET['act'] == DEACTIVATE){
                    if(!Token::isExist($_GET['token'])){
                        $alertred[] = TOKEN_NOT_EXIST;
                    }

                    if(!isset($alertred)){
                        self::deactivate($_GET['modules']);
                        $GLOBALS['alertgreen'] = MODULES_DEACTIVATED;
                    }else{
                        $GLOBALS['alertred'] = $alertred;
                    }
                }elseif ($_GET['act'] == 'remove') {
                    if(!Token::isExist($_GET['token'])){
                        $alertred[] = TOKEN_NOT_EXIST;
                    }
                    if (Mod::isActive($_GET['modules'])) {
                        $alertred[] = "Module is Active. Please deactivate first.";
                    }
                    if(!isset($alertred)){
                        self::deactivate($_GET['modules']);
                        Files::delTree(GX_MOD."/".$_GET['modules']);
                        $GLOBALS['alertgreen'] = MODULES_DELETED;
                    }else{
                        $GLOBALS['alertred'] = $alertred;
                    }
                }
                
            }
        }

        $json = Options::get('modules');
        $mods = json_decode($json, true);
        if (!is_array($mods) || $mods == "") {
            $mods = array();
        }
        foreach ($mods as $m) {
            self::load($m);
        }

        return $data;

    }

    public static function load($mod) {
        $file = GX_MOD."/".$mod."/index.php";
        if(file_exists($file)){
            include ($file);
        }
    }

    public static function url($mod) {
        $url = Site::$url."/inc/mod/".$mod;
        return $url;
    }
}

/* End of file Mod.class.php */
/* Location: ./inc/lib/Mod.class.php */
