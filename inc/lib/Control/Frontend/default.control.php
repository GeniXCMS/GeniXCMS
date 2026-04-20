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

class DefaultControl extends BaseControl
{
    public function run($param)
    {
        $data = Router::scrap($param);
        $data['p_type'] = 'index';
        $data['max'] = Options::v('post_perpage');

        // Check for Custom Frontpage setting
        $frontpage_id = (int) Options::v('frontpage_id');
        if ($frontpage_id > 0) {
            $post = Query::table('posts')
                ->where('id', $frontpage_id)
                ->where('status', '1')
                ->first();

            if ($post) {
                $data['posts'] = Posts::prepare([$post]);
                $post = $data['posts'][0];
                $data['p_type'] = $post->type;
                $data['title'] = $post->title;
                $data['content'] = Posts::content($post->content);
                $data['author'] = $post->author;
                $data['date_published'] = Date::format($post->date);
                $data['last_modified'] = Date::format($post->modified);
                $data['url_author'] = Url::author($post->author);

                // Load post parameters (meta)
                $params = Posts::getParams($post->id);
                foreach ($params as $k => $v) {
                    $data[$k] = $v;
                }

                $layout = $data['layout'] ?? 'index';
                
                $this->render($layout, $data);
                return;
            }
        }

        if (SMART_URL) {
            $paging = isset($data['paging']) ? $data['paging'] : 1;
        } else {
            $paging = isset($_GET['paging']) ? Typo::int($_GET['paging']) : 1;
        }

        $offset = ($paging > 1) ? ($paging - 1) * $data['max'] : 0;
        $pagingtitle = ($paging > 1) ? " - Page {$paging}" : '';

        $data['sitetitle'] = Site::$slogan . $pagingtitle;
        $posts = Query::table('posts')
            ->where('type', 'post')
            ->where('status', '1')
            ->orderBy('date', 'DESC')
            ->limit($data['max'], $offset)
            ->get();
        $data['num'] = count($posts);
        $data['posts'] = Posts::prepare($posts);

        $url = (SMART_URL) ? Site::$url : Site::$url . '/index.php?';
        $paging_arr = [
            'paging' => $paging,
            'table' => 'posts',
            'where' => "`type` = 'post'",
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

        $data['curr_paging'] = $paging;
        $this->render('index', $data);
    }
}

$control = new DefaultControl();
$control->run($param);
