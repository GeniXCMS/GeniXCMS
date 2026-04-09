<?php

function loadLib($class_name)
{
    $array_paths = array(
        GX_LIB,
        GX_LIB . '/Control/',
        GX_LIB . '/Control/Api/',
    );

    foreach ($array_paths as $path) {
        $file = sprintf('%s/%s.class.php', $path, $class_name);
        if (is_file($file)) {
            include_once $file;
            return;
        }
    }
}
spl_autoload_register('loadLib');
