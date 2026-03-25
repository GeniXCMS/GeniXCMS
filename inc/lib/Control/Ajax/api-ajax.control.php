<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * CMS API Service for Dynamic Builder
 * Provides access to posts, categories, authors, etc.
 */
$data = Router::scrap($param);
$gettoken = (SMART_URL) ? ($data['token'] ?? '') : (isset($_GET['token']) ? Typo::cleanX($_GET['token']) : '');
$token = (true === Token::validate($gettoken, true)) ? $gettoken: '';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? Typo::cleanX($_GET['action']) : 'recent_posts';

// Allow public actions without token
$public_actions = ['recent_posts', 'categories'];

if ($token != '' || in_array($action, $public_actions)) {
    
    switch ($action) {
        case 'recent_posts':
            $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 5;
            $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
            $cat = isset($_GET['cat']) ? Typo::int($_GET['cat']) : null;
            
            $args = ['num' => $num, 'type' => $type];
            if ($cat) $args['cat'] = $cat;
            
            $posts = Posts::recent($args);
            $result = [];
            
            if (!isset($posts['error'])) {
                foreach ($posts as $p) {
                    $img = Posts::getPostImage($p->id);
                    if ($img == "") $img = Posts::getImage(Typo::Xclean($p->content), 1);
                    
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
                        'excerpt' => substr(strip_tags(Typo::Xclean($p->content)), 0, 150) . '...'
                    ];
                }
            }
            echo json_encode(['status' => 'success', 'data' => $result]);
            break;

        case 'categories':
            $type = isset($_GET['type']) ? Typo::cleanX($_GET['type']) : 'post';
            $cats = Db::result("SELECT * FROM `cat` WHERE `type` = '{$type}' ORDER BY `name` ASC");
            echo json_encode(['status' => 'success', 'data' => $cats]);
            break;

        case 'tags':
            $term = isset($_GET['term']) ? Typo::cleanX($_GET['term']) : '';
            $tags = Db::result("SELECT * FROM `cat` WHERE `type` = 'tag' AND `name` LIKE '{$term}%' ORDER BY `name` ASC");
            echo json_encode(['status' => 'success', 'data' => $tags]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Token not exist or invalid']);
}
exit;
