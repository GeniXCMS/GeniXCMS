<?php

define('GX_PATH', realpath(__DIR__ . '/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');

define('DB_DRIVER', 'mysqli');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_test');

define('SMART_URL', true); //set 'true' if you want use SMART URL (SEO Friendly URL)
define('GX_URL_PREFIX', '.html');

define('SITE_ID', 'GeniXCMSTestEngine201701');
define('ADMIN_DIR', 'gxadmin');
define('USE_MEMCACHED', false);

function loadlib($f) {
    $file =  GX_LIB. $f . '.class.php';
    if (file_exists($file)) {
        require $file;
    }
}
spl_autoload_register('loadlib');

function loader($class)
{
    $file = $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
}
spl_autoload_register('loader');
