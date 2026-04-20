<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class AuthorControl extends BaseControl
{
    public function run($param)
    {
        $data = Router::scrap($param);
        $data['p_type'] = 'author';

        $author = (SMART_URL) ? $data['author'] : Typo::cleanX(Typo::strip($_GET['author']));
        $data['author'] = $author;
        $data['max'] = Options::v('post_perpage');

        if (User::validate($author)) {
            if (SMART_URL) {
                $paging = isset($data['paging']) ? $data['paging'] : 1;
                $type = isset($data['type']) ? $data['type'] : '';
            } else {
                $paging = isset($_GET['paging']) ? Typo::int($_GET['paging']) : 1;
                $type = isset($_GET['type']) ? Typo::cleanX(Typo::strip($_GET['type'])) : '';
            }

            $where = ($type != '') ? " AND `type` = '{$type}' " : '';
            $offset = ($paging > 1) ? ($paging - 1) * $data['max'] : 0;
            $pagingtitle = ($paging > 1) ? " - Page {$paging}" : '';

            $data['sitetitle'] = 'Post by : ' . $author . $pagingtitle;
            $data['title'] = $author;
            $q = Query::table('posts')
                ->where('author', $author)
                ->where('status', '1')
                ->where('type', 'post');
            if ($type != '') {
                $q->where('type', $type);
            }
            $posts = $q->orderBy('date', 'DESC')->limit($data['max'], $offset)->get();
            $data['num'] = count($posts);
            $data['posts'] = Posts::prepare($posts);

            $url = Url::author($author, $type);
            $paging_arr = [
                'paging' => $paging,
                'table' => 'posts',
                'where' => "`author` = '{$author}' AND `status` = '1' {$where}",
                'max' => $data['max'],
                'url' => $url,
                'type' => Options::v('pagination'),
            ];
            $data['paging'] = Paging::create($paging_arr, SMART_URL);

            $data['recent_posts'] = Posts::lists([
                'num' => 5,
                'image' => true,
                'image_size' => 100,
                'title' => true,
                'date' => true,
                'type' => "post",
                'class' => [
                    'row' => 'd-flex align-items-center mb-3 border-bottom pb-3',
                    'img' => 'rounded flex-shrink-0',
                    'list' => 'flex-grow-1 ms-3',
                    'h4' => 'fs-5 mb-0 text-dark',
                    'date' => 'text-body-secondary mt-0'
                ]
            ]);

            $this->render('author', $data);
            exit;
        } else {
            Control::error('404');
            exit;
        }
    }
}

$control = new AuthorControl();
$control->run($param);
