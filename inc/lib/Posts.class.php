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

class Posts
{
    static $last_id = '';


    public function __construct() {
    }

    //
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
                        'table' => 'posts',
                        'key' => $vars
                    );
            $post = Db::insert($ins);
            self::$last_id = Db::$last_id;
            Pinger::run(Options::get('pinger'));
        }
        return $post;
    }

    public static function update($vars) {
        if(is_array($vars)) {
            //$slug = Typo::slugify($vars['title']);
            //$vars = array_merge($vars, array('slug' => $slug));
            //print_r($vars);
            $ins = array(
                        'table' => 'posts',
                        'id' => $_GET['id'],
                        'key' => $vars
                    );
            $post = Db::update($ins);
            Pinger::run(Options::get('pinger'));
        }
        return $post;
    }

    public static function publish($id) {
        $ins = array(
                    'table' => 'posts',
                    'id' => $id,
                    'key' => array(
                                'status' => '1'
                            )
                );
        $post = Db::update($ins);
        return $post;
    }

    public static function unpublish($id) {
        $ins = array(
                    'table' => 'posts',
                    'id' => $id,
                    'key' => array(
                                'status' => '0'
                            )
                );
        $post = Db::update($ins);
        return $post;
    }

    public static function delete($id) {
        $id = Typo::int($id);
        try
        {
            $vars1 = array(
                        'table' => 'posts',
                        'where' => array(
                                    'id' => $id
                                    )
                        );
            $d = Db::delete($vars1);

            $vars2 = array(
                        'table' => 'posts_param',
                        'where' => array(
                                    'post_id' => $id
                                    )
                        );
            $d = Db::delete($vars2);
            return true;
        }
        catch (Exception $e)
        {
            return $e->getMessage();
        }

    }

    public static function content($vars) {
        $c = Typo::Xclean($vars);
        return $c;
    }

    public static function recent($vars, $type = 'post') {
        $sql = "SELECT * FROM `posts` WHERE `type` = '{$type}' AND `status` = '1' ORDER BY `date` DESC LIMIT {$vars}";
        $posts = Db::result($sql);
        if(isset($posts['error'])){
            $posts['error'] = "No Posts found.";
        }else{
            $posts = $posts;
        }
        return $posts;
    }

    public static function title($id){
        $sql = sprintf("SELECT `title` FROM `posts` WHERE `id` = '%d'", $id);
        try
        {
            $r = Db::result($sql);
            if(isset($r['error'])){
                $title['error'] = $r['error'];
                //echo $title['error'];
            }else{
                $title = $r[0]->title;
            }
            
        }
        catch (Exception $e)
        {
            $title = $e->getMessage();
        }
        
        return $title;
    }
    /* Page Dropdown
    *
    *    $vars = array(
    *                'name'    => 'input_name',
    *                'type'    =>    'type',
    *                'parent'    =>    'parent',
    *                'order_by'    =>    '',
    *                'sort'    =>    'ASC',
    *                'selected'    =>    ''
    *            )
    */

    public static function dropdown($vars){
        if(is_array($vars)){
            //print_r($vars);
            $name = $vars['name'];
            $where = "WHERE `status` = '1' AND ";
            if(isset($vars['type'])) {
                $where .= " `type` = '{$vars['type']}' AND ";
            }else{
                $where .= " ";
            }
            $where .= " `status` = '1' ";
            $order_by = "ORDER BY ";
            if(isset($vars['order_by'])) {
                $order_by .= " {$vars['order_by']} ";
            }else{
                $order_by .= " `name` ";
            }
            if (isset($vars['sort'])) {
                $sort = " {$vars['sort']}";
            }else{
                $sort = 'ASC';
            }
        }
        $cat = Db::result("SELECT * FROM `posts` {$where} {$order_by} {$sort}");
        $num = Db::$num_rows;
        $drop = "<select name=\"{$name}\" class=\"form-control\"><option></option>";
        if($num > 0){
            foreach ($cat as $c) {
                # code...
                // if($c->parent == ''){
                    if(isset($vars['selected']) && $c->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                    $drop .= "<option value=\"{$c->id}\" $sel style=\"padding-left: 10px;\">{$c->title}</option>";
                    // foreach ($cat as $c2) {
                    //     # code...
                    //     if($c2->parent == $c->id){
                    //         if(isset($vars['selected']) && $c2->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                    //         $drop .= "<option value=\"{$c2->id}\" $sel style=\"padding-left: 10px;\">&nbsp;&nbsp;&nbsp;{$c2->name}</option>";
                    //     }
                    // }
                // }
                
            }
        }
        $drop .= "</select>";

        return $drop;
    }
    
}

/* End of file Posts.class.php */
/* Location: ./inc/lib/Posts.class.php */