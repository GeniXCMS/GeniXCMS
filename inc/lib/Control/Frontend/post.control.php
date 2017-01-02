<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

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

$data['posts'] = Db::result(
    sprintf(
        "SELECT * FROM `posts`
            WHERE (`id` = '%d'
            OR `slug` = '%s')
            AND `type` = '%s'
            AND `status` = '%d'
            LIMIT 1",
        $post,
        $post,
        $data['p_type'],
        '1'
    )
);
$num_rows = Db::$num_rows;

$data['posts'] = Posts::prepare($data['posts']);
// print_r($data['posts']);
if ($num_rows > 0) {
    $theme = ($data['p_type'] == 'post') ? 'single' : $data['p_type'];
    Theme::theme('header', $data);
    Theme::theme($theme, $data);
    Theme::footer();
    Stats::addViews($post_id);

} else {
    Control::error('404');
    exit;
}

/* End of file post.control.php */
/* Location: ./inc/lib/Control/Frontend/post.control.php */
