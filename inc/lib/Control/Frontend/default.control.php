<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : default.control.php
* version : 0.0.1 pre
* build : 20141006
*/


$post="";
$data['max'] = Options::get('post_perpage');
if(isset($_GET['paging'])){
    $offset = ($_GET['paging']-1)*$data['max'];
}else{
    $offset = 0;
}
$data['posts'] = Db::result(
                        sprintf("SELECT * FROM `posts` 
                            WHERE `type` = 'post' 
                            ORDER BY `date` 
                            DESC LIMIT %d, %d",
                            $offset, $data['max']
                            )
                        );
$num = Db::$num_rows;
Theme::theme('header');
Theme::theme('index', $data);
Theme::footer();

/* End of file default.control.php */
/* Location: ./inc/lib/Control/Frontend/default.control.php */