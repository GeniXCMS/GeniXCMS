<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class PageControl extends BaseControl
{
    public function run($param)
    {
        $data = Router::scrap($param);
        $data['p_type'] = 'page';

        $page = $this->getPageId($data);

        $pageInt = Typo::int($page);
        $pageSlug = Typo::cleanX($page);
        $post_row = Query::table('posts')
            ->whereRaw("`id` = ? OR `slug` = ?", [$pageInt, $pageSlug])
            ->where('type', 'page')
            ->where('status', '1')
            ->first();

        $posts = $post_row ? [$post_row] : [];
        $data['posts'] = Posts::prepare($posts);

        if (!empty($posts)) {
            $post = $data['posts'][0];

            $data['title'] = $post->title;
            $data['author'] = $post->author;
            $data['date_published'] = Date::format($post->date);
            $data['last_modified'] = Date::format($post->modified);
            $data['url_author'] = Url::author($post->author);

            $post_image = Posts::getPostImage($post->id);
            if ($post_image != "") {
                $data['imgurl'] = Url::thumb($post_image, 'large', 850);
            }

            $data['content'] = "";
            $data['content'] .= Hooks::run('post_content_before_action', $data);
            $data['content'] .= Posts::content($post->content);
            $data['content'] .= Hooks::run('post_content_after_action', $data);

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

            $data['layout'] = Posts::getParam('layout', $post->id) ?: 'default';

            $this->render('page', $data);

            Stats::addViews($page);
            exit;
        } else {
            Control::error('404');
            exit;
        }
    }

    private function getPageId($data)
    {
        $page = 0;
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
        return $page;
    }
}

$control = new PageControl();
$control->run($param);

/* End of file page.control.php */
/* Location: ./inc/lib/Control/Frontend/page.control.php */
