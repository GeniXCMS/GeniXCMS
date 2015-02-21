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

switch ($_GET['page']) {
    case 'sitemap':
        # code...
        Sitemap::create();
        break;
    
    default:
        # code...
        $page = $_GET['page'];
        $data['posts'] = Db::result(
                            sprintf("SELECT * FROM `posts` 
                                    WHERE `id` = '%d' 
                                    AND `type` = 'page'
                                    OR `slug` = '%s' 
                                    LIMIT 1", 
                                    $page, 
                                    $page
                                    )
                            );
        if(Db::$num_rows > 0) {
            Theme::theme('header');
            Theme::theme('page', $data);
            Theme::footer();
        }else{
            Control::error('404');
        }
        break;
}

/* End of file page.control.php */
/* Location: ./inc/lib/Control/Frontend/page.control.php */