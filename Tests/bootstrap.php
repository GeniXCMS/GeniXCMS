<?php

define('GX_PATH', realpath(__DIR__ . '/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('DB_DRIVER', 'mysqli');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_test');
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