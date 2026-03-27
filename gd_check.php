<?php
header('Content-Type: text/plain');

$file = 'assets/media/images/COVER_YT_RUMBUK.webp';
if (!file_exists($file)) {
    echo "File not found: " . $file . "\n";
    exit;
}

$info = getimagesize($file);
print_r($info);

if ($info[2] === IMAGETYPE_WEBP) {
    echo "Identified as WEBP.\n";
    $img = imagecreatefromwebp($file);
    if ($img) {
        echo "imagecreatefromwebp SUCCESS.\n";
        imagedestroy($img);
    } else {
        echo "imagecreatefromwebp FAIL.\n";
    }
} else {
    echo "NOT identified as WEBP.\n";
}

echo "GD support for WebP: " . (function_exists('imagecreatefromwebp') ? "YES" : "NO") . "\n";
echo "GD support for PNG: " . (function_exists('imagecreatefrompng') ? "YES" : "NO") . "\n";
