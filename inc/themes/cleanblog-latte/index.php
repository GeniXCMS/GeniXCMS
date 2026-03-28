<?php
/**
 * Clean Blog Latte Entry Point
 */

require_once GX_LIB . 'Vendor/autoload.php';

use Latte\Engine;

// Initialize Latte
$latte = new Engine();
$latte->setTempDirectory(GX_CACHE);

// Theme metadata
$theme_url = Url::theme();

// Global parameters for templates
$params = [
    'site' => [
        'url'      => Site::$url,
        'name'     => Site::$name,
        'slogan'   => Options::v('siteslogan'),
        'description' => Options::v('sitedesc'),
    ],
    'data'     => $data, // Contains posts, num, paging, titles
    'theme'    => [
        'url'  => $theme_url,
    ],
    'menus'    => [
        'main' => Menus::getMenu('main'),
    ],
    'user'     => User::is_logged_in(),
    'token'    => TOKEN
];

// Determine view
$view = 'index';
if (isset($data['type'])) {
    if ($data['type'] == 'single') {
        $view = 'single';
    } elseif ($data['type'] == 'page') {
        $view = 'page';
    } elseif ($data['type'] == 'cat') {
        $view = 'cat';
    } elseif ($data['type'] == 'tag') {
        $view = 'tag';
    }
}
$params['view'] = $view;

// Set correct header metadata
$params['meta'] = [
    'title' => $data['sitetitle'] ?? Site::$name,
    'desc'  => $data['p_desc'] ?? Options::v('sitedesc'),
];

// Render the main layout
$latte->render(__DIR__ . '/layout.latte', $params);
