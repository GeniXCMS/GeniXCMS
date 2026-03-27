<?php
define('GX_LIB', true);
define('GX_PATH', __DIR__);
define('GX_ASSET', __DIR__.'/assets/');

include 'e:/laragon/www/genixcms2/inc/lib/Files.class.php';
include 'e:/laragon/www/genixcms2/inc/lib/Image.class.php';

try {
    $img = 'test.jpg';
    $type = 'square';
    $size = '100';
    $align = '';
    Image::thumbFly($img, $type, $size, $align);
} catch (TypeError $e) {
    echo "CAUGHT TYPERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "CAUGHT ERROR: " . $e->getMessage() . "\n";
}
