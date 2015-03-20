<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

switch ($_GET['page']) {
    case 'sitemap':
        # code...
        Sitemap::create();
        exit;
        break;
    
    default:
        # code...
        $page = Typo::cleanX(
                    $_GET['page']
                );
        $data['posts'] = Db::result(
                            sprintf("SELECT * FROM `posts` 
                                    WHERE (`id` = '%d' OR `slug` = '%s')
                                    AND `type` = 'page'
                                    AND `status` = '1'
                                    LIMIT 1", 
                                    $page, 
                                    $page
                                    )
                            );
        if(Db::$num_rows > 0) {
            Theme::theme('header',$data);
            Theme::theme('page', $data);
            Theme::footer();
            exit;
        }else{
            Control::error('404');
            exit;
        }
        break;
}

/* End of file page.control.php */
/* Location: ./inc/lib/Control/Frontend/page.control.php */