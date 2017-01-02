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

$post = '';
$data = Router::scrap($param);
$data['p_type'] = 'cat';

//$cat = Db::escape(Typo::Xclean($_GET['cat']));
$cat = (SMART_URL) ? $data['cat'] : Typo::cleanX(Typo::strip($_GET['cat']));
$type = Categories::type($cat);
$data['max'] = Options::v('post_perpage');

if (Categories::exist($cat)) {
    if (SMART_URL) {
        if (isset($data['paging'])) {
            $paging = $data['paging'];
        }
    } else {
        if (isset($_GET['paging'])) {
            $paging = Typo::int($_GET['paging']);
        }
    }

    //$paging = (SMART_URL) ? $data['paging'] : Typo::int(is_int($_GET['paging']));
    if (isset($paging)) {
        if ($paging > 0) {
            $offset = ($paging - 1) * $data['max'];
        } else {
            $offset = 0;
        }
        $pagingtitle = " - Page {$paging}";
    } else {
        $offset = 0;
        $paging = 1;
        $pagingtitle = '';
    }
    $data['sitetitle'] = 'Post in : '.Categories::name($cat).$pagingtitle;
    $data['posts'] = Db::result(
        sprintf(
            "SELECT * FROM `posts`
                        WHERE `type` = '%s'
                        AND `cat` = '%d'
                        AND `status` = '1'
                        ORDER BY `date`
                        DESC LIMIT %d, %d",
            $type,
            $cat,
            $offset,
            $data['max']
        )
    );
    $data['num'] = Db::$num_rows;

    $data['posts'] = Posts::prepare($data['posts']);
    // print_r($data['posts']);
    $url = Url::cat($cat);
    $paging = array(
                    'paging' => $paging,
                    'table' => 'posts',
                    'where' => '`type` = \''.$type.'\' AND `cat` = \''.$cat.'\'',
                    'max' => $data['max'],
                    'url' => $url,
                    'type' => Options::v('pagination'),
                );
    $data['paging'] = Paging::create($paging, SMART_URL);
    Theme::theme('header', $data);
    Theme::theme('cat', $data);
    Theme::footer();
    exit;
} else {
    Control::error('404');
}
/* End of file cat.control.php */
/* Location: ./inc/lib/Control/Frontend/cat.control.php */
