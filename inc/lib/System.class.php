<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140925
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class System
{
    /**
     * GeniXCMS Version Variable.
     *
     * @return float
     */
    public static $version = '2.1.0';

    /**
     * GeniXCMS Version Release.
     *
     * @return string
     */
    public static $v_release = '';

    public static $admin_asset = '';

    public static $toolbar = '';
    public static $toolbar_mode = '';

    /**
     * System Constructor.
     * Initializing the system, check the config file.
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        if (array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") {
            $_SERVER["HTTPS"] = "on";
        }

        /* Load config file */
        self::config('config');

        /* Initiate core services in Container */
        Container::set('db', new Db());
        Container::set('hooks', new Hooks());
        Container::set('http', new Http());
        Container::set('options', new Options());
        Container::set('cache', new Cache());
        Container::set('session', new Session());
        Container::set('token', new Token());
        Container::set('date', new Date());
        Container::set('site', new Site());
        Container::set('user', new User());

        /* Initiate System Language */
        self::lang(Options::v('system_lang'));
        Container::set('language', new Language());

        /* Initiate Admin Menu Registry — must run before Mod and Theme
           so their function.php can call AdminMenu::add() / addChild() */
        new AdminMenu();

        /* Initiate Router */
        Container::set('router', new Router());

        /* Initiate Vendor */
        new Vendor();

        /* Initiate Sitemap */
        new Sitemap();

        /* Initiate Modules (function.php may call AdminMenu::add/addChild) */
        new Mod();

        /* Initiate Params */
        new Params();

        /* Initiate Archives */
        new Archives();

        /* Run Hooks : init */
        Hooks::run('init');

        /* Initiate Asset Manager */
        Asset::init();

        /* Initiate Editor */
        if (Options::v('use_editor') == 'on') {
            Editor::init();
        }

        /* Run Cron : exec scheduled tasks */
        Cron::run();

        /* Load themes configuration (function.php may call AdminMenu::addChild()) */
        new Theme();

        /* Set Security Headers (Moved here so Modules and Themes can hook into it) */
        self::securityHeaders();


        /* Attach Hooks : admin_page_notif_action */
        Hooks::attach('admin_page_notif_action', array('System', 'alert'));

        Hooks::attach('admin_footer_action', array('System', 'loadAdminAsset'));

        self::$toolbar = self::toolbar(self::$toolbar_mode);
    }

    /**
     * Includes a system library file from the GX_LIB directory.
     *
     * @param string $var Class name to load (without extension).
     */
    public static function lib($var)
    {
        $file = GX_LIB . $var . '.class.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * Initializes the system localization using Gettext.
     * Handles differences between Linux (LC_MESSAGES) and Windows (putenv).
     *
     * @param string $vars Locale identifier (e.g. 'en_US', 'id_ID').
     */
    public static function lang($vars)
    {
        $dir = GX_PATH . '/inc/lang/locale';
        if (defined('LC_MESSAGES')) {
            setlocale(LC_MESSAGES, $vars); // Linux
            bindtextdomain("genixcms", $dir);
        } else {
            putenv("LC_ALL={$vars}"); // windows
            bindtextdomain("genixcms", $dir);
        }


        textdomain("genixcms");
    }

    /**
     * Legacy language file loader (PHP-based translations).
     *
     * @param string $vars Language identifier.
     */
    public static function lang2($vars)
    {
        $file = GX_PATH . '/inc/lang/' . $vars . '.lang.php';
        if (file_exists($file)) {
            include $file;
        } else {
            include GX_PATH . '/inc/lang/id_ID.lang.php';
        }
    }

    /**
     * Set System Security Headers.
     *
     * This method initializes various security headers to protect GeniXCMS from common web exploits.
     * It includes a central Content Security Policy (CSP) that can be extended by modules.
     *
     * @since 2.0.0
     * @hook system_security_headers_args (Filter) - Modifier of CSP mapping array.
     */
    public static function securityHeaders()
    {
        // Skip for CLI tasks
        if (PHP_SAPI === 'cli')
            return;

        /**
         * Standard Security Directives
         * - nosniff: Stops browser from inferring MIME types
         * - SAMEORIGIN: Denies framing by external sites (Anti-Clickjacking)
         * - 1; mode=block: High security XSS filtering for legacy browsers
         */
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");

        /**
         * Content Security Policy (CSP)
         * Defines trusted sources for scripts, styles, and other resources.
         * Default includes common CDNs used by GeniXCMS Admin UI.
         */
        $csp_rules = [
            "default-src" => ["'self'"],
            "script-src" => ["'self'", "'unsafe-inline'", "'unsafe-eval'", "https://cdn.jsdelivr.net", "https://code.jquery.com", "https://cdnjs.cloudflare.com", "https://cdn.tailwindcss.com", "https://static.cloudflareinsights.com", "https://unpkg.com"],
            "style-src" => ["'self'", "'unsafe-inline'", "https://cdn.jsdelivr.net", "https://fonts.googleapis.com", "https://code.jquery.com", "https://cdnjs.cloudflare.com", "https://unpkg.com"],
            "img-src" => ["'self'", "data:", "https:", "*"],
            "font-src" => ["'self'", "data:", "https://cdn.jsdelivr.net", "https://fonts.gstatic.com", "https://cdnjs.cloudflare.com", "https://unpkg.com"],
            "connect-src" => ["'self'", "https://cdn.jsdelivr.net", "https://cdnjs.cloudflare.com", "https://cloudflareinsights.com", "https://unpkg.com"],
            "frame-src" => ["'self'", "https://www.youtube.com", "https://www.youtube-nocookie.com", "https://player.vimeo.com", "https://www.dailymotion.com"],
            "object-src" => ["'none'"]
        ];

        // Allow developers to inject their own trusted origins or adjust directives
        $csp_rules_filtered = Hooks::filter('system_security_headers_args', $csp_rules);
        if (is_array($csp_rules_filtered)) {
            $csp_rules = $csp_rules_filtered;
        }

        // Compile and send CSP header
        $csp_string = "";
        foreach ($csp_rules as $directive => $sources) {
            if (is_array($sources)) {
                // Ensure we only have strings in the array to prevent warnings
                $clean_sources = array_filter($sources, function ($v) {
                    return is_string($v);
                });
                $csp_string .= "{$directive} " . implode(' ', $clean_sources) . "; ";
            } else {
                $csp_string .= "{$directive} {$sources}; ";
            }
        }

        header("Content-Security-Policy: " . trim($csp_string));
    }

    /**
     * Includes a configuration file from the inc/config directory.
     *
     * @param string $var Config file name (without extension).
     */
    public static function config($var)
    {
        $file = GX_PATH . '/inc/config/' . $var . '.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * Checks if the primary system configuration file exists.
     *
     * @return bool True if config.php is present.
     */
    public static function existConf()
    {
        if (file_exists(GX_PATH . '/inc/config/config.php')) {
            return true;
        } else {
            return false;
        }
    }

    // At the beginning of each page call these functions
    /**
     * Initializes output buffering with optional minification.
     *
     * @param bool $minify Whether to enable HTML minification (default: false).
     */
    public static function gZip($minify = false)
    {
        if ($minify) {
            ob_start('Site::minifIed');
        } else {
            ob_start();
        }
        #ob_start(ob_gzhandler);
        // ob_start('Site::minifyHTML');
        // ob_start('Site::minifIed');

        ob_implicit_flush(0);
    }

    /**
     * Flushes the output buffer with GZip compression if supported by the client.
     * Automatically handles Vary headers and Content-Encoding.
     */
    public static function Zipped()
    {
        // global $_SERVER['HTTP_ACCEPT_ENCODING'];
        if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $encoding = false;
        } else {
            if (headers_sent()) {
                $encoding = false;
            } elseif (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
                $encoding = 'x-gzip';
            } elseif (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
                $encoding = 'gzip';
            } else {
                $encoding = false;
            }
        }


        if ($encoding) {
            $contents = ob_get_contents();
            ob_end_clean();
            header('Content-Encoding: ' . $encoding);
            header('Vary: Accept-Encoding');
            echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $size = strlen($contents);
            $contents = gzcompress($contents, 9);
            $contents = substr($contents, 0, $size);
            echo $contents;
            exit();
        } else {
            ob_end_flush();
            exit();
        }
    }

    /**
     * Admin Dashboard Controller (Placeholder).
     */
    public static function admin()
    {
    }

    /**
     * Includes an administrative PHP fragment.
     *
     * @param string $vars Fragment name (without extension).
     * @param mixed  $data Optional data buffer to pass to the fragment.
     */
    public static function inc($vars, $data = '')
    {
        $admin_dir = defined('ADMIN_DIR') ? ADMIN_DIR : 'gxadmin';
        $file = GX_PATH . '/' . $admin_dir . '/inc/' . $vars . '.php';

        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * Returns the full system version and release status.
     *
     * @return string Version string.
     */
    public static function v()
    {
        return self::$version . ' ' . self::$v_release;
    }



    /**
     * Processes and renders administrative system alerts.
     * Transforms alert arrays into Toast notifications. In administrative contexts,
     * alerts are queued for execution in the footer.
     *
     * @param array $data Alert mapping array (Success, Danger, Info, Warning).
     * @return string      JavaScript block containing toast triggers.
     */
    public static function alert($data)
    {
        static $seen_msgs = [];
        $data = (isset($data[0]) && is_array($data[0])) ? $data[0] : $data;
        $scripts = [];

        $types = [
            'alertSuccess' => 'success',
            'alertDanger' => 'error',
            'alertInfo' => 'info',
            'alertWarning' => 'warning',
            'alertDefault' => 'info'
        ];

        foreach ($types as $key => $type) {
            if (isset($data[$key]) && is_array($data[$key])) {
                foreach ($data[$key] as $alert) {
                    $msg = addslashes(Typo::cleanX($alert));
                    if (!empty($msg) && !in_array($msg, $seen_msgs)) {
                        $seen_msgs[] = $msg;
                        $scripts[] = "window.showGxToast('{$msg}', '{$type}');";
                    }
                }
            }
        }

        if (!empty($scripts)) {
            $html = '<script>' . implode('', $scripts) . '</script>';

            // To ensure compatibility across both direct-echo systems 
            // and the admin footer queueing system:
            static $footer_hook_attached = false;
            if (!$footer_hook_attached && defined('GX_ADMIN') && GX_ADMIN) {
                $footer_hook_attached = true;
                static $accumulated_scripts = '';
                $accumulated_scripts .= $html;

                Hooks::attach('admin_footer_action', function () use (&$accumulated_scripts) {
                    echo $accumulated_scripts;
                    $accumulated_scripts = '';
                });
                return ''; // Admin handles via footer
            }

            return $html; // Frontend or mid-page call: return directly
        }

        return '';
    }

    // public static function print($data) {
    //     global $data;
    //     print_r($data);
    //     echo $data[$data['var']];
    // }

    /**
     * Echoes the accumulated administrative asset string.
     */
    public static function loadAdminAsset()
    {
        echo self::$admin_asset;
    }

    /**
     * Appends an asset (JS/CSS) to the administrative asset queue.
     *
     * @param string $asset HTML markup for the asset.
     */
    public static function adminAsset($asset)
    {
        $admin_asset = self::$admin_asset;
        $admin_asset .= $asset;
        self::$admin_asset = $admin_asset;
    }

    /**
     * Returns a JSON-formatted toolbar configuration for the Summernote editor.
     *
     * @param string $mode Toolbar complexity ('mini', 'light', 'full').
     * @return string      JS array fragment for Summernote toolbar.
     */
    public static function toolbar($mode = 'mini')
    {
        if ($mode == 'mini') {
            $toolbar = "
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['para', ['ul', 'ol']],
                    ['genixcms', ['gxcode']]";
        } elseif ($mode == 'light') {
            $toolbar = "['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore', 'gxcode', 'elfinder']],
                    ['view', ['fullscreen']]";
        } elseif ($mode == 'full') {
            $toolbar = "['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear', 'highlight']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore', 'gxcode', 'elfinder']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']]";
        } else {
            $toolbar = self::toolbar('mini');
        }
        self::$toolbar = $toolbar;

        return $toolbar;
    }

    /**
     * Sets the global toolbar mode preference.
     *
     * @param string $mode Toolbar mode identifier.
     * @return string      The set mode.
     */
    public static function toolbarMode($mode)
    {
        self::$toolbar_mode = $mode;

        return self::$toolbar_mode;
    }
}

/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */
