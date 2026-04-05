<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Vite Asset Bundler Helper for GeniXCMS.
 * 
 * Enables seamless integration with Vite dev server and production builds.
 * Supports hot module replacement (HMR) and manifest-based production assets.
 * 
 * @since 1.1.0
 */
class Vite
{
    private static $dev_server = 'http://localhost:5173';
    private static $entry_points = [
        'main' => 'assets/src/main.js',
        'style' => 'assets/src/style.scss'
    ];

    /**
     * Check if dev server is active.
     * Can be toggled via Options or environment.
     */
    public static function isDev()
    {
        return Options::v('vite_dev_mode') === 'on';
    }

    /**
     * Output Vite client and entry points.
     */
    public static function client($entry = 'assets/src/main.js')
    {
        if (self::isDev()) {
            echo "<!-- Vite Dev Server -->\n";
            echo "<script type=\"module\" src=\"" . self::$dev_server . "/@vite/client\"></script>\n";
            echo "<script type=\"module\" src=\"" . self::$dev_server . "/{$entry}\"></script>\n";
        } else {
            // Production: Load from manifest.json
            self::loadFromManifest($entry);
        }
    }

    /**
     * Load production assets from manifest.json
     */
    private static function loadFromManifest($entry)
    {
        $manifestPath = GX_PATH . '/assets/dist/manifest.json';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (isset($manifest[$entry])) {
                $file = $manifest[$entry]['file'];
                echo "<script type=\"module\" src=\"" . Site::$url . "/assets/dist/{$file}\"></script>\n";
                
                // Load CSS chunks
                if (isset($manifest[$entry]['css'])) {
                    foreach ($manifest[$entry]['css'] as $css) {
                        echo "<link rel=\"stylesheet\" href=\"" . Site::$url . "/assets/dist/{$css}\">\n";
                    }
                }
            }
        }
    }

    /**
     * Set the Vite dev server URL.
     */
    public static function setDevServer($url)
    {
        self::$dev_server = rtrim($url, '/');
    }
}
