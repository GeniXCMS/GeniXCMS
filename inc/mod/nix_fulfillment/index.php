<?php
/**
 * Name: Nixomers Fulfillment
 * Desc: Manage order shipping, packing, and fulfillment logistics perfectly synced with Nixomers.
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-box-seam
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// Register Autoloader for Nix Fulfillment Classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/lib/' . $class . '.class.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

Hooks::attach('init', function () {
    if (class_exists('AdminMenu')) {
        AdminMenu::addChild('nixomers', [
            'label' => 'Fulfillment Control',
            'url' => 'index.php?page=mods&mod=nix_fulfillment',
            'access' => 1
        ]);
    }
});
