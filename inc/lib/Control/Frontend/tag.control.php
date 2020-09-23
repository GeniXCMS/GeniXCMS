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
$data['name'] = $name;

if (Tags::exist($name)) {
    # code...
    Cache::start();
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
    $data['sitetitle'] = 'Post in : Tags - '.$name.$pagingtitle;
    $data['posts'] = Db::result(
        sprintf("SELECT * FROM `posts` AS A
                    LEFT JOIN `posts_param` AS B
                    ON A.`id` = B.`post_id`
                    WHERE B.`param` = 'tags' 
                    AND B.`value` LIKE '%%%s%%'
                    AND A.`status` = '1'
                    ORDER BY A.`date` 
                    DESC LIMIT %d, %d",
            $name, $offset, $data['max']
        )
    );
    // echo $type;
    // print_r($data['posts']);
    $data['num'] = Db::$num_rows;
    $data['posts'] = Posts::prepare($data['posts']);

    $url = Url::tag($name);
    $paging = array(
                'paging' => $paging,
                'table' => 'posts',
                'where' => '`type` = \''.$type.'\' AND `status` = \'1\' ',
                'max' => $data['max'],
                'url' => $url,
                'type' => Options::v('pagination'),
            );
    $data['paging'] = Paging::create($paging, SMART_URL);
    Theme::theme('header', $data);
    Theme::theme('tag', $data);
    Theme::footer($data);

    Cache::end();
    exit;
} else {
    Control::error('404');
}

System::Zipped();
/* End of file cat.control.php */
/* Location: ./inc/lib/Control/Frontend/cat.control.php */
