<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class CatControl extends BaseControl
{
    public function run($param)
    {
        $data = Router::scrap($param);
        $data['p_type'] = 'cat';

        $cat = (SMART_URL) ? $data['cat'] : Typo::cleanX(Typo::strip($_GET['cat']));
        $data['cat'] = $cat;
        $type = Categories::type($cat);
        $data['max'] = Options::v('post_perpage');

        if (Categories::exist($cat)) {
            if (SMART_URL) {
                $paging = isset($data['paging']) ? $data['paging'] : 1;
            } else {
                $paging = isset($_GET['paging']) ? Typo::int($_GET['paging']) : 1;
            }

            $offset = ($paging > 1) ? ($paging - 1) * $data['max'] : 0;
            $pagingtitle = ($paging > 1) ? " - Page {$paging}" : '';

            $data['sitetitle'] = 'Post in : ' . Categories::name($cat) . $pagingtitle;
            $data['title'] = Categories::name($cat);
            $posts = Query::table('posts')
                ->where('type', $type)
                ->where('cat', Typo::int($cat))
                ->where('status', '1')
                ->orderBy('date', 'DESC')
                ->limit($data['max'], $offset)
                ->get();
            $data['num'] = count($posts);
            $data['posts'] = Posts::prepare($posts);

            $url = Url::cat($cat);
            $paging_arr = [
                'paging' => $paging,
                'table' => 'posts',
                'where' => "`type` = '{$type}' AND `cat` = '{$cat}' AND `status` = '1'",
                'max' => $data['max'],
                'url' => $url,
                'type' => Options::v('pagination'),
                'post_type' => $type,
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

            $this->render('cat', $data);
            exit;
        } else {
            Control::error('404');
        }
    }
}

$control = new CatControl();
$control->run($param);
