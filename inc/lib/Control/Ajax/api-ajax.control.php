<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - CMS API Service for Dynamic Builder
 * Provides access to posts, categories, authors, etc.
 * 
 * @since 2.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
$data = Router::scrap($param);
$gettoken = (SMART_URL) ? ($data['token'] ?? '') : (isset($_GET['token']) ? Typo::cleanX($_GET['token']) : '');
$token = (true === Token::validate($gettoken, true)) ? $gettoken : '';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? Typo::cleanX($_GET['action']) : 'recent_posts';

// Allow public actions without token
$public_actions = ['recent_posts', 'random_posts', 'custom_posts', 'categories'];

if ($token != '' || in_array($action, $public_actions)) {

    $result = [];
    $posts = [];

    switch ($action) {
        case 'recent_posts':
            $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 5;
            $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
            $cat = isset($_GET['cat']) ? Typo::int($_GET['cat']) : null;
            $args = ['num' => $num, 'type' => $type];
            if ($cat)
                $args['cat'] = $cat;
            $posts = Posts::recent($args);
            break;

        case 'random_posts':
            $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 5;
            $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
            $posts = Query::table('posts')
                ->where('status', '1')
                ->where('type', $type)
                ->orderBy('RAND()')
                ->limit($num)
                ->get();
            break;

        case 'custom_posts':
            $ids = isset($_GET['ids']) ? Typo::cleanX($_GET['ids']) : '';
            if ($ids != '') {
                $ids_arr = array_map('intval', explode(',', $ids));
                $posts = Query::table('posts')
                    ->whereIn('id', $ids_arr)
                    ->get();
            }
            break;

        case 'categories':
            $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
            $cats = Query::table('cat')->where('type', $type)->orderBy('name', 'ASC')->get();
            echo json_encode(['status' => 'success', 'data' => $cats]);
            exit;
            break;

        case 'tags':
            $term = isset($_GET['term']) ? Typo::cleanX($_GET['term']) : '';
            $tags = Query::table('cat')
                ->where('type', 'tag')
                ->where('name', 'LIKE', $term . '%')
                ->orderBy('name', 'ASC')
                ->get();
            echo json_encode(['status' => 'success', 'data' => $tags]);
            exit;
            break;

        case 'authors':
            $authors = Query::table('posts')
                ->select('DISTINCT `author`')
                ->where('status', '1')
                ->orderBy('author', 'ASC')
                ->get();
            echo json_encode(['status' => 'success', 'data' => $authors]);
            exit;
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
            exit;
            break;
    }

    // Common Post Processing Logic
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
    echo json_encode(['status' => 'success', 'data' => $result]);
    exit;

} else {
    echo json_encode(['status' => 'error', 'message' => 'Token not exist or invalid']);
}
exit;
