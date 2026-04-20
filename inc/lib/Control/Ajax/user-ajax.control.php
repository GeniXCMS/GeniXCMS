<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Core AJAX Controller for Users.
 */
class UserAjaxControl
{
    public function __construct()
    {
    }

    public function list_users($param = null)
    {
        $ajax = new UserAjax();
        return $ajax->list_users($param);
    }
}

$userAjax = new UserAjaxControl();
if (isset($_GET['action'])) {
    $action = Typo::cleanX($_GET['action']);
    if (method_exists($userAjax, $action)) {
        $userAjax->$action();
    }
}
