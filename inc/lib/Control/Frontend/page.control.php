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
$data = Router::scrap($param);
$page = (SMART_URL) ? $data['page'] : Typo::cleanX(Typo::strip($_GET['page']));
switch ($page) {
    case 'sitemap':
        # code...
        Sitemap::create();
        exit;
        break;
    
    default:
        # code...
        
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

        $num_rows = Db::$num_rows;
        if(Options::get('multilang_enable') === 'on') {
            if (isset($_GET['lang'])) {
                foreach ($data['posts'] as $p) {
                    if (Posts::existParam('multilang', $p->id) 
                        && Options::get('multilang_default') !== $_GET['lang']) {
                        # code...
                        $lang = Language::getLangParam($_GET['lang'], $p->id);
                        $posts = get_object_vars($p);
                        $posts = array_merge($posts,$lang);
                        
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

        if($num_rows > 0) {
            Theme::theme('header',$data);
            Theme::theme('page', $data);
            Theme::footer();
            Stats::addViews($page);
            exit;
        }else{
            Control::error('404');
            exit;
        }
        break;
}

/* End of file page.control.php */
/* Location: ./inc/lib/Control/Frontend/page.control.php */