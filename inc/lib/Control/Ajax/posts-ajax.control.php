<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Core AJAX Controller for Posts.
 */
class PostsAjaxControl
{
    public function __construct()
    {
    }

    public function list_posts($param = null)
    {
        $ajax = new PostsAjax();
        return $ajax->list_posts($param);
    }
}

$postsAjax = new PostsAjaxControl();
if (isset($_GET['action'])) {
    $action = Typo::cleanX($_GET['action']);
    if (method_exists($postsAjax, $action)) {
        $postsAjax->$action();
    }
}
