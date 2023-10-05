<?php

date_default_timezone_set('UTC');

define('GX_PATH', realpath(__DIR__.'/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require 'autoload.php';

$str = Typo::jsonFormat('"><script src=x onerror=confirm(2);>');
echo $str;
