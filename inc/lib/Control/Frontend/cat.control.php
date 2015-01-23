<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
* filename : cat.control.php
* version : 0.0.1 pre
* build : 20141006
*/


$post="";
$max = 10;
if(isset($_GET['paging'])){
    $offset = ($_GET['paging']-1)*$max;
}else{
    $offset = 0;
}
$data['posts'] = Db::result(
                        sprintf("SELECT * FROM `posts` 
                            WHERE `type` = 'post' 
                            AND `cat` = '%d'
                            ORDER BY `date` 
                            DESC LIMIT %d, %d",
                            $_GET['cat'], $offset, $max
                            )
                        );
$data['num'] = Db::$num_rows;
Theme::theme('header');
Theme::theme('cat', $data);
Theme::footer();

/* End of file cat.control.php */
/* Location: ./inc/lib/Control/Frontend/cat.control.php */