<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140928
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
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
        $mod = self::modList();
        //print_r($mod);
        if(is_array($mod)){
            $list = '';
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
        }
        return $list;
    }

    public static function inc($vars, $data, $dir){
        include($dir."/".$vars.".php");
    }

}

/* End of file Mod.class.php */
/* Location: ./inc/lib/Mod.class.php */