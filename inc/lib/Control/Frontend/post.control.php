<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

$post = Typo::int($_GET['post']);
$data['posts'] = Db::result(
                    sprintf("SELECT * FROM `posts` 
                            WHERE `id` = '%d' 
                            AND `type` = 'post'
                            AND `status` = '1' 
                            LIMIT 1",
                            $post
                            )
                );
$num_rows = Db::$num_rows;
if(Options::get('multilang_enable') === 'on') {
    if (isset($_GET['lang'])) {
        foreach ($data['posts'] as $p) {
            if (Posts::existParam('multilang', $p->id)) {
                # code...
                $multilang = json_decode(Posts::getParam('multilang', $p->id),true);
                foreach ($multilang as $key => $value) {
                    // print_r($value);
                    $keys = array_keys($value);
                    // print_r($keys);
                    if ($keys[0] == $_GET['lang']) {
                        $lang = $multilang[$key][$_GET['lang']];
                    }
                }
                $posts = get_object_vars($p);
                $posts = array_replace($posts, $lang);
                
            }else{
                $posts = $p;
            }
            $posts_arr = array();
            $posts_arr = json_decode(json_encode($posts), FALSE);
            // $posts[] = $posts;
            $post_arr[] = $posts_arr;
            $data['posts'] = $post_arr;
        }
    }else{
        $data['posts'] = $data['posts'];
    }

}else{
    $data['posts'] = $data['posts'];
}

// print_r($data['posts']);
if($num_rows > 0) {
    Theme::theme('header',$data);
    Theme::theme('single', $data);
    Theme::footer();
    Stats::addViews($post);
    exit;
}else{
    Control::error('404');
    exit;
}


/* End of file post.control.php */
/* Location: ./inc/lib/Control/Frontend/post.control.php */