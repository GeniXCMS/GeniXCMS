<?php

define('GX_PATH', realpath(__DIR__ . '/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require("autoload.php");

try {
    new System();
} catch (Exception $e) {
    echo $e->getMessage();
}

$vars = array(
        'to'      => 'o@rake.re',
        'to_name' => 'OraKere',
        'subject' => 'Welcome to Mailer Test',
        'message' => '
                    Hi OraKere, 

                    Thank You for Registering with Us. You can now login : http://www.metalgenix.com/login.php with your username and password

                    Sincerely,
                    MetalGenix
                   '
    );

echo Mail::send($vars);
