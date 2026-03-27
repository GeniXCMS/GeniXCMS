<?php
define('GX_PATH', __DIR__);
define('GX_LIB', GX_PATH.'/inc/lib/');
require GX_LIB.'System.class.php';
require GX_LIB.'Vendor.class.php';
$vendorPath = Vendor::path('studio-42/elfinder');
echo "Loading plugins...\n";
include_once $vendorPath.'php/plugins/AutoResize/plugin.php';
include_once $vendorPath.'php/plugins/Normalizer/plugin.php';
include_once $vendorPath.'php/plugins/Sanitizer/plugin.php';
echo "Done loading.\n";
