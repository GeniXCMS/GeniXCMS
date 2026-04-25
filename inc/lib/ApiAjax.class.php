<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class ApiAjax
{
    /**
     * GeniXCMS - CMS API Service for Dynamic Builder
     * Provides access to posts, categories, authors, etc.
     */
    public function index($param = null)
    {
        $action = isset($_GET['action']) ? Typo::cleanX($_GET['action']) : 'recent_posts';
        if (method_exists($this, $action)) {
            return $this->$action($param);
        }
        return Ajax::error(404, 'Unknown action');
    }

    public function recent_posts()
    {
        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 5;
        $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
        $cat = isset($_GET['cat']) ? Typo::int($_GET['cat']) : null;
        $args = ['num' => $num, 'type' => $type];
        if ($cat)
            $args['cat'] = $cat;
        $posts = Posts::recent($args);
        return $this->_process_posts($posts);
    }

    public function random_posts()
    {
        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 5;
        $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
        $posts = Query::table('posts')
            ->where('status', '1')
            ->where('type', $type)
            ->orderBy('RAND()')
            ->limit($num)
            ->get();
        return $this->_process_posts($posts);
    }

    public function custom_posts()
    {
        $ids = isset($_GET['ids']) ? Typo::cleanX($_GET['ids']) : '';
        $posts = [];
        if ($ids != '') {
            $ids_arr = array_map('intval', explode(',', $ids));
            $posts = Query::table('posts')
                ->whereIn('id', $ids_arr)
                ->get();
        }
        return $this->_process_posts($posts);
    }

    public function categories()
    {
        $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
        $cats = Query::table('cat')->where('type', $type)->orderBy('name', 'ASC')->get();
        return Ajax::response(['status' => 'success', 'data' => $cats]);
    }

    public function tags($param = null)
    {
        if (!$this->_auth($param)) {
            return Ajax::error(401, 'Token not exist or invalid');
        }

        $term = isset($_GET['term']) ? Typo::cleanX($_GET['term']) : '';
        $tags = Query::table('cat')
            ->where('type', 'tag')
            ->where('name', 'LIKE', $term . '%')
            ->orderBy('name', 'ASC')
            ->get();
        return Ajax::response(['status' => 'success', 'data' => $tags]);
    }

    public function authors($param = null)
    {
        if (!$this->_auth($param)) {
            return Ajax::error(401, 'Token not exist or invalid');
        }

        $authors = Query::table('posts')
            ->select('DISTINCT `author`')
            ->where('status', '1')
            ->orderBy('author', 'ASC')
            ->get();
        return Ajax::response(['status' => 'success', 'data' => $authors]);
    }

    /**
     * Private helper for post processing
     */
    private function _process_posts($posts)
    {
        $result = [];
        if (is_array($posts) && !isset($posts['error'])) {
            foreach ($posts as $p) {
                $img = Posts::getPostImage($p->id);
                if ($img == "")
                    $img = Posts::getImage(Typo::Xclean($p->content), 1);

                $result[] = [
                    'id' => $p->id,
                    'title' => $p->title,
                    'url' => Url::post($p->id),
                    'date' => Date::local($p->date),
                    'author' => $p->author,
                    'author_url' => Url::author($p->author),
                    'category' => Categories::name($p->cat),
                    'category_url' => Url::cat($p->cat),
                    'image' => ($img != "") ? Url::thumb($img, 'square', 300) : Url::thumb('assets/images/noimage.png', 'square', 300),
                    'excerpt' => substr(Shortcode::strip(strip_tags(Typo::Xclean($p->content))), 0, 150) . '...'
                ];
            }
        }
        return Ajax::response(['status' => 'success', 'data' => $result]);
    }

    /**
     * Internal auth check for specific actions
     */
    private function _auth($param = null)
    {
        $data = Router::scrap($param);
        $gettoken = (SMART_URL) ? ($data['token'] ?? '') : (isset($_GET['token']) ? Typo::cleanX($_GET['token']) : '');
        return (true === Token::validate($gettoken, true));
    }
}
