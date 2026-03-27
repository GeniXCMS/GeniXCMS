<?php
define('GX_LIB', true);
include 'e:/laragon/www/genixcms2/inc/lib/Files.class.php';
$refl = new ReflectionMethod('Files', 'isClean');
echo "Params:\n";
foreach ($refl->getParameters() as $param) {
    echo $param->getName() . ': ' . ($param->getType() ?: 'no type') . "\n";
}
echo 'Return type: ' . ($refl->getReturnType() ?: 'no return type') . "\n";
