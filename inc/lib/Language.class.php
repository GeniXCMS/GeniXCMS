<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

class Language
{
    public function __construct () {

    }

    public static function getList () {
        $handle = dir(GX_PATH.'/inc/lang/');
        while (false !== ($entry = $handle->read())) {
            if ($entry != "." && $entry != ".." ) {
                $file = GX_PATH.'/inc/lang/'.$entry;
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if(is_file($file) == true && $ext == 'php'){
                    $lang[] = $entry;
                } 
            }
        }
        
        $handle->close();
        return $lang;
    }

    public static function optDropdown ($var) {
        $langs =  self::getList();
        $opt = '';
        foreach ($langs as $lang) {

            $file = explode('.', $lang);
            if ($var == $file[0]) {
                $sel = 'SELECTED';
            }else{
                $sel = '';
            }
            $opt .= "<option {$sel}>{$file[0]}</option>";
        }
        return $opt;
    }

    public static function getDefaultLang() {
        $def = Options::get('multilang_default');
        $lang = json_decode(Options::get('multilang_country'), true);
        $deflang = $lang[$def];
        return $deflang;
    }

    public static function getLangParam($lang, $post_id) {
        if (Posts::existParam('multilang',$post_id)) {
            $multilang = json_decode(Posts::getParam('multilang', $post_id), true);
            // print_r(Posts::getParam('multilang', $post_id));
            foreach ($multilang as $key => $value) {
                // print_r($value);
                $keys = array_keys($value);
                // print_r($keys);
                if ($keys[0] == $lang) {
                    $lang = $multilang[$key][$lang];
                    return $lang;
                }
            }
        }
    }
}
/* End of file Language.class.php */
/* Location: ./inc/lib/Language.class.php */