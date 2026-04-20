<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Core AJAX Controller for Comments.
 */
class CommentsAjaxControl
{
    public function __construct()
    {
    }

    public function list_comments($param = null)
    {
        $ajax = new CommentsAjax();
        return $ajax->list_comments($param);
    }
}

$commentsAjax = new CommentsAjaxControl();
if (isset($_GET['action'])) {
    $action = Typo::cleanX($_GET['action']);
    if (method_exists($commentsAjax, $action)) {
        $commentsAjax->$action();
    }
}
