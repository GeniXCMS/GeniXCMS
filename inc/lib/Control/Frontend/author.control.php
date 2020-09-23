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
$data['p_type'] = 'author';

//$cat = Db::escape(Typo::Xclean($_GET['cat']));
$author = (SMART_URL) ? $data['author'] : Typo::cleanX(Typo::strip($_GET['author']));
$data['author'] = $author;
$data['max'] = Options::v('post_perpage');
//echo User::validate($author);
if (User::validate($author)) {
    Cache::start();
    if (SMART_URL) {
        if (isset($data['paging'])) {
            $paging = $data['paging'];
        }
        $type = isset($data['type']) ? $data['type']: '';
    } else {
        if (isset($_GET['paging'])) {
            $paging = Typo::int($_GET['paging']);
        }
        $type = isset($_GET['type']) ? Typo::cleanX(Typo::strip($_GET['type'])): '';
    }

    if ($type != '') {
        $where = " AND `type` = '{$type}' ";
    } else {
        $where = '';
    }
    //$paging = (SMART_URL) ? $data['paging'] : Typo::int(is_int($_GET['paging']));
    if (isset($paging) && $paging != '') {
        if ($paging > 0) {
            $offset = ($paging - 1) * $data['max'];
        } else {
            $offset = 0;
        }
//        echo $offset;
        $pagingtitle = " - Page {$paging}";
    } else {
        $offset = 0;
        $paging = 1;
        $pagingtitle = '';
    }
//    echo $paging;
    $data['sitetitle'] = 'Post by : '.$author.$pagingtitle;
    $data['posts'] = Db::result(
        sprintf(
            "SELECT * FROM `posts`
                        WHERE `author` = '%s' %s
                        AND `status` = '1'
                        AND `type` = 'post'
                        ORDER BY `date`
                        DESC LIMIT %d, %d",
            $author,
            $where,
            $offset,
            $data['max']
        )
    );
    $data['num'] = Db::$num_rows;
//    echo $data['num'];
    $data['posts'] = Posts::prepare($data['posts']);
    // print_r($data['posts']);
    $url = Url::author($author, $type);
    $paging = array(
                    'paging' => $paging,
                    'table' => 'posts',
                    'where' => '`author` = \''.$author.'\' AND `status` = \'1\' '.$where,
                    'max' => $data['max'],
                    'url' => $url,
                    'type' => Options::v('pagination'),
                );
    $data['paging'] = Paging::create($paging, SMART_URL);
    Theme::theme('header', $data);
    Theme::theme('author', $data);
    Theme::footer($data);
    Cache::end();
    exit;
} else {
    Control::error('404');
    exit;
}
System::Zipped();
/* End of file cat.control.php */
/* Location: ./inc/lib/Control/Frontend/cat.control.php */
