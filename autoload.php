<?php

function loadLib($class_name) 
{
    $array_paths = array(
        GX_LIB
    );

    foreach($array_paths as $path)
    {
        $file = sprintf('%s/%s.class.php', GX_LIB, $class_name);
        if(is_file($file)) 
        {
            include_once $file;
        } 

    }
}
spl_autoload_register('loadLib');