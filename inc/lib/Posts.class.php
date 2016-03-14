<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
*
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140930
* @version 0.0.8
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2016 Puguh Wijayanto
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
            Hooks::run('post_sqladd_action', $vars, self::$last_id);
            $pinger = Options::v('pinger');
            if ($pinger != "") {
                Pinger::run($pinger);
            }

        }
        return $post;
    }

    public static function update($vars) {
        if(is_array($vars)) {
            //$slug = Typo::slugify($vars['title']);
            //$vars = array_merge($vars, array('slug' => $slug));
            //print_r($vars);
            $id = Typo::int($_GET['id']);
            $ins = array(
                        'table' => 'posts',
                        'id' => $id,
                        'key' => $vars
                    );
            $post = Db::update($ins);
            Hooks::run('post_sqladd_action', $vars, $id);
            $pinger = Options::v('pinger');
            if ($pinger != "") {
                Pinger::run($pinger);
            }
        }
        return $post;
    }

    public static function publish($id) {
        $id = Typo::int($id);
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
        $id = Typo::int($id);
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
            Hooks::run('post_sqldel_action', $id);
            return true;
        }
        catch (Exception $e)
        {
            return $e->getMessage();
        }

    }

    public static function content($vars) {
        $post = Typo::Xclean($vars);

        preg_match_all("[[\-\-readmore\-\-]]", $post, $more);

        if (is_array($more[0])) {
            $post = str_replace('[[--readmore--]]', '', $post);
            // return $post;
        }else{
            $post = $post;
        }
        $post = Hooks::filter('post_content_filter', $post);
        return $post;
    }

    public static function format ($post, $id) {
        // split post for readmore...
        $post = Typo::Xclean($post);
        $more = explode('[[--readmore--]]', $post);
        //print_r($more);
        if (count($more) > 1) {
            $post = explode('[[--readmore--]]', $post);
            $post = $post[0]." <a href=\"".Url::post($id)."\">".READ_MORE."</a>";
        }else{
            $post = $post;
        }

        $post = Hooks::filter('post_content_filter', $post);
        return $post;
    }

    // $vars = array(
    //     'num' => '',
    //     'cat' => '',
    //     'type' => 'post'
    // );


    public static function recent($vars) {

        $catW = isset($vars['cat'])? " AND `cat` = '".$vars['cat']:"";
        $type = isset($vars['type'])? $vars['type']: "post";
        $num = isset($vars['num'])? $vars['num']: "10";
        $sql = "SELECT * FROM `posts`
                WHERE `type` = '{$type}' $catW AND `status` = '1'
                ORDER BY `date` DESC LIMIT {$num}";
        $posts = Db::result($sql);
        if(isset($posts['error'])){
            $posts['error'] = "No Posts found.";
        }else{
            $posts = self::prepare($posts);
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

    public static function addParam($param, $value, $post_id) {
        $sql = array(
                'table' => 'posts_param',
                'key' => array(
                        'post_id' => $post_id,
                        'param' => $param,
                        'value' => $value
                    )
            );
        $q = Db::insert($sql);
        if ($q) {
            return true;
        }else{
            return false;
        }
    }

    public static function editParam($param, $value, $post_id) {
        $sql = "UPDATE `posts_param` SET `value` = '{$value}' WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' ";
        $q = Db::query($sql);
        if ($q) {
            return true;
        }else{
            return false;
        }
    }

    public static function getParam($param, $post_id) {
        $sql = "SELECT * FROM `posts_param` WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' LIMIT 1";
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            return $q[0]->value;
        }else{
            return '';
        }
    }

    public static function delParam($param, $post_id) {
        $sql = "DELETE FROM `posts_param` WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' LIMIT 1";
        $q = Db::query($sql);
        if ($q) {
            return true;
        }else{
            return false;
        }
    }

    public static function existParam($param, $post_id) {
        $sql = "SELECT * FROM `posts_param` WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' LIMIT 1";
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            return true;
        }else{
            return false;
        }
    }

    public static function prepare($post) {
        if(Options::v('multilang_enable') === 'on') {
            $langs = Language::isActive();
            if (!empty($langs)) {
                foreach ($post as $p) {
                    if (Posts::existParam('multilang', $p->id)
                        && Options::v('multilang_default') !== $langs) {
                        # code...
                        $lang = Language::getLangParam($langs, $p->id);
                        $posts = get_object_vars($p);
                        $posts = array_merge($posts,$lang);
                    }else{
                        $posts = $p;
                    }
                    $posts_arr = array();
                    $posts_arr = json_decode(json_encode($posts), FALSE);
                    // $posts[] = $posts;
                    $post_arr[] = $posts_arr;
                    $post = $post_arr;
                }
            }else{
                $post = $post;
            }

        }else{
            $post = $post;
        }

        return $post;
    }

    // $vars = array(
    //     'num' => '',
    //     'cat' => '',
    //     'type' => 'post',
    //     'excerpt' => 'true',
    //     'title' => 'true',
    //     'author' => 'true',
    //     'date' => 'true',
    //     'class' => array(
    //                    'ul' => '',
    //                    'li' => '',
    //                    'p' => '',
    //                    'h4' => '',
    //                )
    // );

    public static function lists($vars){

        $class = isset($vars['class'])? $vars['class']: "";

        $ulClass = isset($class['ul'])? $class['ul']: "";
        $liClass = isset($class['li'])? $class['li']: "";
        $pClass = isset($class['p'])? $class['p']: "";
        $h4Class = isset($class['h4'])? $class['h4']: "";
        $excerptMax = isset($vars['excerpt_max'])? $vars['excerpt_max']: "200";

        $pcat = self::recent($vars['num']);
        if(isset($pcat['error'])){
            $pcat['error'] = "No Post(s) found.";
        }else{
            $pcat= self::prepare($pcat);
            echo "<ul class=\"".$ulClass."\">";
            foreach($pcat as $p){
                $content = ($vars['excerpt'])? substr(
                    strip_tags(
                        Typo::Xclean($p->content)
                    ), 0, $excerptMax): "";

                echo "<li class=\"".$liClass."\">
                    <h4 class=\"".$h4Class."\"><a href=\"".Url::post($p->id)."\">{$p->title}</a></h4>
                    <p class=\"".$pClass."\">".$content."</p>
                </li>";
            }
            echo "</ul>";
        }
    }

}

/* End of file Posts.class.php */
/* Location: ./inc/lib/Posts.class.php */
