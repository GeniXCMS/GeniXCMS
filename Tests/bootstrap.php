<?php

define('GX_PATH', realpath(__DIR__ . '/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');

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