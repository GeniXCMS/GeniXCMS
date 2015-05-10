<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140930
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* Categories Class
*
* This class will process the categories function. Including Create, Edit, Delete
* the categories.
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class Categories
{
    public function __construct() {
    }

    /**
    * Categories Dropdown Function
    *
    * This will list the categories into the HTML Dropdown
    * Below are how to use it :
    * <code>
    *    $vars = array(
    *                'name'    =>    'catname',
    *                'parent'    =>    'parent',
    *                'order_by'    =>    '',
    *                'sort'    =>    'ASC',
    *            )
    *    Categories::dropdown($vars);
    * </code>
    *
    * @param array $vars the delivered data must be in array with above format
    * @uses Db::result();
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
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
            }else{
                $sort = " ASC";
            }
        }
        $cat = Db::result("SELECT * FROM `cat` {$where} {$order_by} {$sort}");
        //print_r($cat);
        $drop = "<select name=\"{$name}\" class=\"form-control\"><option></option>";
        if(Db::$num_rows > 0 ){
            foreach ($cat as $c) {
                # code...
                if($c->parent == null || $c->parent == '0' ){
                    if(isset($vars['selected']) && $c->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                    $drop .= "<option value=\"{$c->id}\" $sel style=\"padding-left: 10px;\">{$c->name}</option>";
                    foreach ($cat as $c2) {
                        # code...
                        if($c2->parent == $c->id){
                            if(isset($vars['selected']) && $c2->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                            $drop .= "<option value=\"{$c2->id}\" $sel style=\"padding-left: 10px;\">
                                    &nbsp;&nbsp;&nbsp;{$c2->name}</option>";
                        }
                    }
                }
                
            }
        }
        $drop .= "</select>";

        return $drop;
    }


    public static function lists($vars) {
        if(is_array($vars)){
            //print_r($vars);
            
            $where = "WHERE ";
            if(isset($vars['parent'])) {
                $where .= " `parent` = '{$vars['parent']}' AND ";
            }
            if(isset($vars['type'])) {
                $where .= " `type` = '{$vars['type']}' AND ";
            }
            $where .= "1 ";
            $order_by = "ORDER BY ";
            if(isset($vars['order_by'])) {
                $order_by .= " {$vars['order_by']} ";
            }else{
                $order_by .= " `name` ";
            }
            if (isset($vars['sort'])) {
                $sort = " {$vars['sort']}";
            }else{
                $sort = " ASC";
            }
        }
        $cat = Db::result("SELECT * FROM `cat` {$where} {$order_by} {$sort}");
        //print_r($cat);
        $drop = "<div class=\"panel-group\" id=\"accordion\" role=\"tablist\">
            ";
        if(Db::$num_rows > 0 ){
            foreach ($cat as $c) {
                # code...
                if($c->parent == null || $c->parent == '0' ){
                    //if(isset($vars['selected']) && $c->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                    if(isset($_GET['cat'])){
                        $catparent = self::getParent($_GET['cat']);
                        $in = ($catparent[0]->parent === $c->id)? 'in': '';
                    }else{
                        $catparent = '';
                        $in =  '';
                    }
                    // print_r($catparent);
                    
                    $drop .= "<div class=\"panel panel-default\">
                    <div id=\"collapseListGroupHeading{$c->id}\" class=\"panel-heading\" role=\"tab\" >
                    <a href=\"#collapse-{$c->id}\" data-toggle=\"collapse\"  aria-expanded=\"false\" 
                    aria-controls=\"collapse-{$c->id}\" class=\"collapsed\" data-parent=\"#accordion\"><strong>{$c->name}</strong></a>
                    </div>
                    <div class=\"panel-collapse collapse {$in}\" role=\"tabpanel\" id=\"collapse-{$c->id}\" aria-labelledby=\"collapseListGroupHeading{$c->id}\">
                    <ul class=\"nav nav-pills nav-stacked \" >";
                    foreach ($cat as $c2) {
                        # code...
                        if($c2->parent == $c->id){
                            //if(isset($vars['selected']) && $c2->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                            $drop .= "<li><a href=\"".Url::cat($c2->id)."\">{$c2->name}</a></li>";
                        }
                    }
                    $drop .= "</ul></div></div>";
                }
                
            }
        }
        $drop .= "</div>";

        return $drop;
    }

    /**
    * Category Name function
    *
    * This will get the specified ID category name
    * @param int $id
    * @uses Db::result();
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function name($id) {
        $id = sprintf('%d', $id);
        if(isset($id)){
            $cat = Db::result("SELECT `name` FROM `cat` 
                                WHERE `id` = '{$id}' LIMIT 1");
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

    /**
    * Category Get Parent function
    *
    * This will get the specified ID category parent data
    * @param int $id
    * @uses Db::result();
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function getParent($id=''){
        $sql = sprintf("SELECT `parent` FROM `cat` 
                        WHERE `id` = '%d'", $id);
        $cat = Db::result($sql);
        return $cat;
    }

    /**
    * Category Delete function
    *
    * This will delete the specified ID category data
    * @param int $id
    * @param array $sql
    * @uses self::getParent();
    * @uses Db::delete();
    * @uses Db::result();
    * @uses Db::$num_rows;
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function delete($id){
        $id = sprintf('%d', $id);
        $parent = self::getParent($id);

        $sql = array(
                    'table' => 'cat',
                    'where' => array(
                                    'id' => $id
                                )
                );
        $cat = Db::delete($sql);
        if($cat){
            return true;
        }else{
            return false;
        }
        // check all posts with this category and move to parent categories
        $post = Db::result("SELECT `id` FROM `posts` 
                            WHERE `cat` = '{$id}'");
        $npost = Db::$num_rows;
        
        //print_r($parent);
        if($npost > 0){
            $sql = "UPDATE `posts` 
                    SET `cat` = '{$parent[0]->parent}' 
                    WHERE `cat` = '{$id}'";
            Db::query($sql);
        }
        
    }
    
}

/* End of file Categories.class.php */
/* Location: ./inc/lib/Categories.class.php */