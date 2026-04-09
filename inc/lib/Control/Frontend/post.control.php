<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class PostControl extends BaseControl
{
    public function run($param)
    {
        $lang = Options::v('system_lang');
        $data = Router::scrap($param);
        $data['website_lang'] = substr($lang, 0, 2);

        if (SMART_URL == true) {
            if (isset($data['post'])) {
                $post_slug = Typo::cleanX($data['post']);
                $post_id = Posts::idSlug($post_slug);
            }
            if (isset($data['lang']) && !isset($_GET['lang'])) {
                Language::setActive($data['lang']);
                $data['website_lang'] = $data['lang'];
            }
        } elseif (isset($_GET['post'])) {
            $post_id = Typo::int($_GET['post']);
            if (isset($_GET['lang']) && $_GET['lang'] != "") {
                $data['website_lang'] = $_GET['lang'];
            }
        }

        if (!isset($post_id)) {
            Control::error('404');
            exit;
        }

        $data['p_type'] = Posts::type($post_id);
        $posts = Posts::fetch([
            'id' => $post_id,
            'type' => $data['p_type'],
            'status' => '1'
        ]);

        $data['posts'] = Posts::prepare($posts);

        if (!isset($data['posts']['error'])) {
            $post = $data['posts'][0];
            $data['title'] = $post->title;
            $data['author'] = $post->author;
            $data['category_name'] = Categories::name($post->cat);
            $data['date_published'] = Date::format($post->date);
            $data['last_modified'] = Date::format($post->modified);
            $data['url_author'] = Url::author($post->author);
            $data['post_tags'] = Posts::tags($post->id);
            $data['related_post'] = _('Related Post');
            $data['url_category'] = Url::cat($post->cat);

            $data['content'] = "";
            $post_image = Posts::getPostImage($post->id);
            if ($post_image != "") {
                $imgurl = Url::thumb($post_image, 'large', 850);
                $data['content'] .= "<p><img src='{$imgurl}' width='850' class='img-fluid post_image' alt='{$post->title}' loading='lazy'></p>";
            }

            $data['content'] .= Hooks::run('post_content_before_action', $data);
            $data['content'] .= Posts::content($post->content);
            $data['content'] .= Hooks::run('post_content_after_action', $data);
            $data['related'] = Posts::related($post->id, 8, $post->cat);

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

            $view = Theme::exist($data['p_type']) ? $data['p_type'] : 'single';
            $this->render($view, $data);

            Stats::addViews($post_id);
            exit;
        } else {
            Control::error('404');
            exit;
        }
    }
}

$control = new PostControl();
$control->run($param);
