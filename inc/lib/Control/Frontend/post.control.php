<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : post.control.php
* version : 0.0.1 pre
* build : 20141006
*/
$post = $_GET[$vars];
$data['posts'] = Db::result(
                    sprintf("SELECT * FROM `posts` 
                            WHERE `id` = '%d' AND `type` = 'post' LIMIT 1",
                            $post
                            )
                );

if(Db::$num_rows > 0) {
    Theme::theme('header');
    Theme::theme('single', $data);
    Theme::footer();
}else{
    Control::error('404');
}


/* End of file post.control.php */
/* Location: ./inc/lib/Control/Frontend/post.control.php */