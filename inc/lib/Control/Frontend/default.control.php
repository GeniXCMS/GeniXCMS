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
                            ORDER BY `date` 
                            DESC LIMIT %d, %d",
                            $offset, $data['max']
                            )
                        );
$data['num'] = Db::$num_rows;
Theme::theme('header');
Theme::theme('index', $data);
Theme::footer();

/* End of file default.control.php */
/* Location: ./inc/lib/Control/Frontend/default.control.php */