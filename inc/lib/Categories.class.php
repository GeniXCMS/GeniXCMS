<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Categories.class.php
* version : 0.0.1 pre
* build : 20140930
*/

class Categories
{
    public function __construct() {
    }

    /* Categories Dropdown
    *
    *    $vars = array(
    *                'name'    =>    'catname',
    *                'parent'    =>    'parent',
    *                'order_by'    =>    '',
    *                'sort'    =>    'ASC',
    *            )
    */
    public static function dropdown($vars) {
        if(is_array($vars)){
            //print_r($vars);
            $name = $vars['name'];
            $where = "WHERE ";
            if(isset($vars['parent'])) {
                $where .= " `parent` = '{$vars['parent']}' ";
            }else{
                $where .= "1 ";
            }
            $order_by = "ORDER BY ";
            if(isset($vars['order_by'])) {
                $order_by .= " {$vars['order_by']} ";
            }else{
                $order_by .= " `name` ";
            }
            if (isset($vars['sort'])) {
                $sort = " {$vars['sort']}";
            }
        }
        $cat = Db::result("SELECT * FROM `cat` {$where} {$order_by} {$sort}");
        $drop = "<select name=\"{$name}\" class=\"form-control\"><option></option>";
        foreach ($cat as $c) {
            # code...
            if($c->parent == ''){
                if(isset($vars['selected']) && $c->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                $drop .= "<option value=\"{$c->id}\" $sel style=\"padding-left: 10px;\">{$c->name}</option>";
                foreach ($cat as $c2) {
                    # code...
                    if($c2->parent == $c->id){
                        if(isset($vars['selected']) && $c2->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                        $drop .= "<option value=\"{$c2->id}\" $sel style=\"padding-left: 10px;\">&nbsp;&nbsp;&nbsp;{$c2->name}</option>";
                    }
                }
            }
            
        }
        $drop .= "</select>";

        return $drop;
    }

    public static function name($id) {
        if(isset($id)){
            $cat = Db::result("SELECT `name` FROM `cat` WHERE `id` = '{$id}' LIMIT 1");
            //print_r($cat);
            if(isset($cat['error'])){
                return '';
            }else{
                return $cat[0]->name;
            }
            
        }else{
            echo "No ID Selected";
        }
        
        //print_r($cat);
    }

    public static function getParent($id=''){
        $sql = sprintf("SELECT `parent` FROM `cat` WHERE `id` = '%d'", $id);
        $menu = Db::result($sql);
        return $menu;
    }

    public static function delete($id){
        $parent = self::getParent($id);

        $sql = array(
                    'table' => 'cat',
                    'where' => array(
                                    'id' => $id
                                )
                );
        $menu = Db::delete($sql);
        // check all posts with this category
        $post = Db::result("SELECT `id` FROM `posts` WHERE `cat` = '{$id}'");
        $npost = Db::$num_rows;
        
        //print_r($parent);
        if($npost > 0){
            $sql = "UPDATE `posts` SET `cat` = '{$parent[0]->parent}' WHERE `cat` = '{$id}'";
            Db::query($sql);
        }
        
    }
    
}

/* End of file Categories.class.php */
/* Location: ./inc/lib/Categories.class.php */