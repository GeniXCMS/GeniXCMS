<?php
define('GX_LIB', true);
// Mock some constants needed by Image
define('GX_PATH', __DIR__);
define('GX_ASSET', __DIR__.'/assets/');

include 'e:/laragon/www/genixcms2/inc/lib/Files.class.php';
include 'e:/laragon/www/genixcms2/inc/lib/Image.class.php';

$refl = new ReflectionClass('Files');
echo "Files class file: " . $refl->getFileName() . "\n";

$reflMethod = new ReflectionMethod('Files', 'isClean');
echo "isClean method file: " . $reflMethod->getFileName() . "\n";
echo "isClean params: " . $reflMethod->getNumberOfParameters() . "\n";
foreach ($reflMethod->getParameters() as $p) {
    echo "Param: " . $p->getName() . " Type: " . ($p->getType() ?: 'none') . "\n";
}
