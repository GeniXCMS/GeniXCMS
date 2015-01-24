<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Options.class.php
* version : 0.0.1 pre
* build : 20141001
*/

class Options
{

    public function __construct() {
    }

    // $vars = array(
    //             'title' => '',
    //             'cat' => '',
    //             'content' => '',
    //             'date' => '',
    //             'author' => '',
    //             'type' => '',
    //             'status' => ''
    //         );
    public static function insert($vars) {
        if(is_array($vars)) {
            $slug = Typo::slugify($vars['title']);
            $vars = array_merge($vars, array('slug' => $slug));
            //print_r($vars);
            $ins = array(
                        'table' => 'options',
                        'key' => $vars
                    );
            $post = Db::insert($ins);
        }
        return $post;
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
        return $op[0]->value;
    }
    
}

/* End of file Options.class.php */
/* Location: ./inc/lib/Options.class.php */