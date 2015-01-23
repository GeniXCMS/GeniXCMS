<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
* filename : Mod.class.php
* version : 0.0.1 pre
* build : 20140928
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
        if ($handle = opendir(GX_MOD)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." ) {
                    $mod[] = "$entry";
                }
            }
            closedir($handle);
        }
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
        return $d;
    }

    public static function ModMenu(){
        $mod = self::modList();
        echo "<ul class=\"nav nav-sidebar\">";
        foreach ($mod as $m) {
            # code...
            $data = self::data($m);
            if(isset($_GET['mod']) && $_GET['mod'] == $m){
                $class = 'class="active"';
            }else{
                $class = "";
            }
            echo "<li $class><a href=\"index.php?page=mods&mod={$m}\" >".$data['name']."</a></li>";
        }
        echo "</ul>";
    }

    public static function inc($vars, $data, $dir){
        include($dir."/".$vars.".php");
    }

}

/* End of file Mod.class.php */
/* Location: ./inc/lib/Mod.class.php */