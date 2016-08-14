<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
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

if (SMART_URL == true) {
    if (isset($data['page'])) {
        $page = $data['page'];
    } elseif (isset($_GET['page'])) {
        $page = Typo::int($_GET['page']);
    }

    if (isset($data['lang']) && !isset($_GET['lang'])) {
        Language::setActive($data['lang']);
    }
} elseif (isset($_GET['page'])) {
    $page = Typo::int($_GET['page']);
}

switch ($page) {
    case 'sitemap':
        # code...
        Sitemap::create();
        exit;
        break;

    default:
        # code...

        $data['posts'] = Db::result(
            sprintf(
                "SELECT * FROM `posts` 
                                    WHERE (`id` = '%d' OR `slug` = '%s')
                                    AND `type` = 'page'
                                    AND `status` = '1'
                                    LIMIT 1",
                $page,
                $page
            )
        );

        $num_rows = Db::$num_rows;
        $data['posts'] = Posts::prepare($data['posts']);

        if ($num_rows > 0) {
            Theme::theme('header', $data);
            Theme::theme('page', $data);
            Theme::footer();
            Stats::addViews($page);
            exit;
        } else {
            Control::error('404');
            exit;
        }
        break;
}

/* End of file page.control.php */
/* Location: ./inc/lib/Control/Frontend/page.control.php */
