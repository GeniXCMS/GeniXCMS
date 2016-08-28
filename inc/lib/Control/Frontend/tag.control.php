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
$data['p_type'] = 'tag';
//$cat = Db::escape(Typo::Xclean($_GET['cat']));
$tag = (SMART_URL) ?
Tags::id(
    Typo::cleanX(
        Db::escape($data['tag'])
    )
) :
Tags::id(
    Typo::cleanX(
        Db::escape(
            Typo::strip($_GET['tag'])
        )
    )
);
$type = Categories::type($tag);
$name = Tags::name($tag);
$slug = Tags::slug($tag);
if (Tags::exist($name)) {
    # code...

    $data['max'] = Options::v('post_perpage');

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
    $data['sitetitle'] = 'Post in : '.$name.$pagingtitle;
    $data['posts'] = Db::result(
        sprintf(
            "SELECT B.`post_id`, A.`id`, A.`date`, A.`title`, A.`content`,
                    A.`author`, A.`cat` FROM `posts` AS A
                    JOIN `posts_param` AS B
                    ON A.`id` = B.`post_id`
                    WHERE B.`param` = 'tags' 
                    AND B.`value` LIKE '%%%s%%'
                    AND A.`status` = '1'
                    ORDER BY A.`date`
                    DESC LIMIT %d, %d",
            $name,
            $offset,
            $data['max']
        )
    );
    $data['num'] = Db::$num_rows;
    $data['posts'] = Posts::prepare($data['posts']);

    $url = Url::tag($tag);
    $paging = array(
                'paging' => $paging,
                'table' => 'posts',
                'where' => '`type` = \''.$type.'\' ',
                'max' => $data['max'],
                'url' => $url,
                'type' => Options::v('pagination'),
            );
    $data['paging'] = Paging::create($paging, SMART_URL);
    Theme::theme('header', $data);
    Theme::theme('tag', $data);
    Theme::footer();
    exit;
} else {
    Control::error('404');
}

/* End of file cat.control.php */
/* Location: ./inc/lib/Control/Frontend/cat.control.php */
