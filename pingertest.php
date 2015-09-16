<?php

define('GX_PATH', realpath(__DIR__ . '/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require("autoload.php");


try {
    $sys = new System();
    $sess = new Session();
    $thm = new Theme();
    $db = new Db();
    $u = new User();
    Vendor::autoload();
    Session::start();
    System::gZip();
    //$thm->header();
} catch (Exception $e) {
    echo $e->getMessage();
}

echo Pinger::run(Options::get('pinger'));

//$thm->footer();