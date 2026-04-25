<?php
/**
 * Name: Media Manager
 * Desc: High-performance independent media management and image editing tool.
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-images
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// Register Autoloader for Media Manager Classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/lib/' . $class . '.class.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Register Assets and Inject Global Variables for Selector
Asset::register("gxmedia-vars", "raw", '
<script>
    var GX_TOKEN = "' . TOKEN . '";
    var GX_AJAX_URL = "' . Url::ajax('media-manager') . '";
    var GX_MEDIA_DIR = "' . (Options::v('media_local_path') ?: 'assets/media') . '";
    var GX_MEDIA_SELECTOR = "' . (Options::v('active_media_selector') ?: 'media-manager') . '";
</script>
', "header", [], 20, "all");
Asset::enqueue("gxmedia-vars");

// Load Assets early for proper queueing - Use context "all" to ensure frontend availability
Asset::load("gxmedia-selector-js", "js", Site::$url . "inc/mod/media-manager/assets/js/media-selector.js?t=" . time(), "footer", [], 20, "all");

// Initialize Admin Menu and Routes
Hooks::attach('init', function () {
    AdminMenu::add([
        'id' => 'media-manager',
        'label' => 'Media Manager',
        'icon' => 'bi bi-images',
        'url' => Site::$url . ADMIN_DIR . '/index.php?page=mods&mod=media-manager&sel=manager',
        'access' => 0 // Admin only
    ]);
});

// Expose media-manager as a Go-compatible AJAX resource for GET-only listing.
Hooks::attach('go_supported_resources', function ($args) {
    $resources = $args[0];
    if (!in_array('media-manager', $resources)) {
        $resources[] = 'media-manager';
    }
    return [$resources];
});


