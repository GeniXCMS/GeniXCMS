<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
System::gZip(true);
$data = Router::scrap($param);
//print_r($_GET);
if (SMART_URL == true) {
    if (isset($data['post'])) {
        $post = Typo::cleanX($data['post']);
        $post_id = Posts::idSlug($post);
    }
    if (isset($data['lang']) && !isset($_GET['lang'])) {
        Language::setActive($data['lang']);
    }
} elseif (isset($_GET['post'])) {
    $post = Typo::int($_GET['post']);
    $post_id = $post;
}

$data['p_type'] = Posts::type($post_id);

$vars = array(
    'id'        => $post_id,
    'type'      => $data['p_type'],
    'status'    => '1'
);
$posts = Posts::fetch($vars);

$data['posts'] = Posts::prepare($posts);

if (!isset($posts['error'])) {
    Cache::start();
    $theme = Theme::exist($data['p_type']) ? $data['p_type']: 'single';
    Theme::theme('header', $data);
    Theme::theme($theme, $data);
    Theme::footer($data);
    Cache::end();
    Stats::addViews($post_id);

} else {
    Control::error('404');
    exit;
}

System::Zipped();
/* End of file post.control.php */
/* Location: ./inc/lib/Control/Frontend/post.control.php */
