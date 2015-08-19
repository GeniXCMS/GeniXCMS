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
if (SMART_URL == true) {
    if ( isset($data['post']) ) {
        
        $post = $data['post'];
        
    }elseif (isset($_GET['post'])){
        
        $post = Typo::int($_GET['post']);
        
    }
}elseif (isset($_GET['post'])){
    
    $post = Typo::int($_GET['post']);
    
}

$data['posts'] = Db::result(
                    sprintf("SELECT * FROM `posts` 
                            WHERE `id` = '%d' 
                            AND `type` = 'post'
                            AND `status` = '1' 
                            LIMIT 1",
                            $post
                            )
                );

if(Db::$num_rows > 0) {
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