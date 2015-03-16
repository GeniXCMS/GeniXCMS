<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141001
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class Options
{

    public function __construct() {
    }

    // $vars = array(
    //             'name' => '',
    //             'value' => ''
    //         );
    public static function insert($vars) {
        if(is_array($vars)) {
            $ins = array(
                        'table' => 'options',
                        'name' => $vars['name'],
                        'value' => $vars['value']
                    );
            $opt = Db::insert($ins);
        }else{
            Control::error('unknown','Format not Found, please in array');
        }
        return $opt;
    }

    public static function update($key, $val = '') {
        if(is_array($key)){
            foreach ($key as $k => $v) {
                $post = Db::query("UPDATE `options` SET `value`='{$v}' WHERE `name` = '{$k}' LIMIT 1");
            }
        }else{
            $post = Db::query("UPDATE `options` SET `value`='{$val}' WHERE `name` = '{$key}' LIMIT 1");
        }
        
        return $post;
    }

    public static function get($vars) {
        $op = Db::result("SELECT `value` FROM `options` WHERE `name` = '{$vars}' LIMIT 1");
        if(Db::$num_rows > 0){
            return $op[0]->value;
        }else{
            return false;
        }
    }
    
}

/* End of file Options.class.php */
/* Location: ./inc/lib/Options.class.php */