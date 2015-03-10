<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
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
                            AND `cat` = '%d'
                            AND `status` = '1'
                            ORDER BY `date` 
                            DESC LIMIT %d, %d",
                            $_GET['cat'], $offset, $data['max']
                            )
                        );
$data['num'] = Db::$num_rows;
if($data['num'] > 0) {
    Theme::theme('header',$data);
    Theme::theme('cat', $data);
    Theme::footer();
    exit;
}else{
    Control::error('404');
    exit;
}

/* End of file cat.control.php */
/* Location: ./inc/lib/Control/Frontend/cat.control.php */