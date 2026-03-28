<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 2.0.0-alpha
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class System
{
    /**
     * GeniXCMS Version Variable.
     *
     * @return float
     */
    public static $version = '2.0.0-alpha';

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
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        if(array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") {
            $_SERVER["HTTPS"] = "on";
        }

        /* Load config file */
        self::config('config');

        /* Set Security Headers */
        self::securityHeaders();

        /* Initiate core services in Container */
        Container::set('db', new Db());
        Container::set('hooks', new Hooks());
        Container::set('http', new Http());
        Container::set('options', new Options());
        Container::set('cache', new Cache());
        Container::set('token', new Token());
        Container::set('date', new Date());
        Container::set('site', new Site());
        Container::set('session', new Session());
        Container::set('user', new User());

        /* Initiate System Language */
        self::lang(Options::v('system_lang'));
        Container::set('language', new Language());

        /* Initiate Router */
        Container::set('router', new Router());

        /* Initiate Vendor */
        new Vendor();

        /* Initiate Sitemap */
        new Sitemap();

        /* Initiate Modules */
        new Mod();

        /* Initiate Params */
        new Params();

        /* Initiate Archives */
        new Archives();

        /* Run Hooks : init */
        Hooks::run('init');

        /* Load themes configuration */
        new Theme();


        /* Attach Hooks : admin_page_notif_action */
        Hooks::attach('admin_page_notif_action', array('System', 'alert'));

        Hooks::attach('admin_footer_action', array('System', 'loadAdminAsset'));

        self::$toolbar = self::toolbar(self::$toolbar_mode);
    }

    /**
     * System Library Loader.
     * This will include library which is called.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function lib($var)
    {
        $file = GX_LIB.$var.'.class.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function lang($vars)
    {
        $dir = GX_PATH.'/inc/lang/locale';
        if (defined('LC_MESSAGES')) {
            setlocale(LC_MESSAGES, $vars); // Linux
            bindtextdomain("genixcms", $dir);
        } else {
            putenv("LC_ALL={$vars}"); // windows
            bindtextdomain("genixcms", $dir);
        }
        
        
        textdomain("genixcms");
    }

    public static function lang2($vars)
    {
        $file = GX_PATH.'/inc/lang/'.$vars.'.lang.php';
        if (file_exists($file)) {
            include $file;
        } else {
            include GX_PATH.'/inc/lang/id_ID.lang.php';
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
            "script-src" => ["'self'", "'unsafe-inline'", "'unsafe-eval'", "https://cdn.jsdelivr.net", "https://code.jquery.com", "https://cdnjs.cloudflare.com", "https://cdn.tailwindcss.com"],
            "style-src" => ["'self'", "'unsafe-inline'", "https://cdn.jsdelivr.net", "https://fonts.googleapis.com", "https://code.jquery.com", "https://cdnjs.cloudflare.com"],
            "img-src" => ["'self'", "data:", "https:", "*"],
            "font-src" => ["'self'", "data:", "https://cdn.jsdelivr.net", "https://fonts.gstatic.com", "https://cdnjs.cloudflare.com"],
            "connect-src" => ["'self'", "https://cdn.jsdelivr.net", "https://cdnjs.cloudflare.com"],
            "frame-src" => ["'self'", "https://www.youtube.com", "https://www.youtube-nocookie.com", "https://player.vimeo.com", "https://www.dailymotion.com"],
            "object-src" => ["'none'"]
        ];

        // Allow developers to inject their own trusted origins or adjust directives
        $csp_rules_filtered = Hooks::filter('system_security_headers_args', $csp_rules);
        if (is_array($csp_rules_filtered)) {
            $csp_rules = $csp_rules_filtered;
        }

        // Complie and send CSP header
        $csp_string = "";
        foreach ($csp_rules as $directive => $sources) {
            $csp_string .= "{$directive} " . implode(' ', $sources) . "; ";
        }

        header("Content-Security-Policy: " . trim($csp_string));
    }

    public static function config($var)
    {
        $file = GX_PATH.'/inc/config/'.$var.'.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    public static function existConf()
    {
        if (file_exists(GX_PATH.'/inc/config/config.php')) {
            return true;
        } else {
            return false;
        }
    }

    // At the beginning of each page call these functions
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

    // Call this function to output everything as gzipped content.
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
            header('Content-Encoding: '.$encoding);
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

    public static function admin()
    {
    }

    public static function inc($vars, $data = '')
    {
        $admin_dir = defined('ADMIN_DIR') ? ADMIN_DIR : 'gxadmin';
        $file = GX_PATH.'/'.$admin_dir.'/inc/'.$vars.'.php';

        if (file_exists($file)) {
            include $file;
        }
    }

    public static function v()
    {
        return self::$version.' '.self::$v_release;
    }

    public static function versionCheck()
    {
        $v = trim(self::latestVersion());

    // print_r($v);
        $v = str_replace('.', '', $v);
        $selfv = str_replace('.', '', self::$version);
        if ($v > $selfv) {
            Hooks::attach('admin_page_notif_action', array('System', 'versionReport'));
        }
    }

    public static function latestVersion()
    {
        $check = json_decode(Options::v('system_check'), true);
        $now = strtotime(date('Y-m-d H:i:s'));

        if (isset($check['last_check'])) {
            $limit = $now - $check['last_check'];

            if ($limit < 86400) {
                $v = $check['version'];
            } else {
                $v = self::getLatestVersion($now);
            }
        } else {
            $v = self::getLatestVersion($now);
        }

        return $v;
    }

    public static function getLatestVersion($now)
    {
        $v = Http::fetch('https://raw.githubusercontent.com/GeniXCMS/GeniXCMS/master/VERSION');

        $arr = array(
        'version' => trim($v),
        'last_check' => $now,
        );
        $arr = json_encode($arr);

        Options::update('system_check', $arr);

        return $v;
    }

    public static function versionReport()
    {
        $v = self::latestVersion();

        $html = "
        <div class=\"alert alert-warning\">
            <span class=\"fa fa-warning\"></span> "._("Warning: Your CMS version is different with our latest version (<strong>$v</strong>). Please upgrade your system.")."
        </div>
        ";

        return $html;
    }

    public static function alert($data)
    {
       global $html;
        $html = '';
//     print_r($data);
        $data = (isset($data[0]) && is_array($data[0])) ? $data[0]: $data;
        if (isset($data['alertSuccess'])) {
            $html .= '
            <script>
            ';
            foreach ($data['alertSuccess'] as $alert) {
                $html .="toastr.success('".Typo::cleanX($alert)."');";
            }
        $html .= "</script>";
        }
        if (isset($data['alertDanger'])) {
            $html .= '<script>';
            foreach ($data['alertDanger'] as $alert) {
                $html .= "toastr.error('".Typo::cleanX($alert)."');";
            }
            $html .= '</script>';
        }
        if (isset($data['alertInfo'])) {
            $html .= '<script>';
            foreach ($data['alertInfo'] as $alert) {
                $html .= "toastr.info('".Typo::cleanX($alert)."');";
            }
            $html .= '</script>';
        }
        if (isset($data['alertWarning'])) {
            $html .= '<script>';
            foreach ($data['alertWarning'] as $alert) {
                $html .= "toastr.warning('".Typo::cleanX($alert)."');";
            }
            $html .= '</script>';
        }
        if (isset($data['alertDefault'])) {
            $html .= '<script>';
            foreach ($data['alertDefault'] as $alert) {
                $html .= "toastr.info('".Typo::cleanX($alert)."');";
            }
            $html .= '</script>';
        }
        // $data['var'] = 'alert';
        // $data['alert'] = $html;
        

        Hooks::attach('footer_load_lib', function(){
            global $html;
            echo $html;
        });
        Hooks::attach('admin_footer_action', function(){
            global $html;
            echo $html;
        });
    }

    // public static function print($data) {
    //     global $data;
    //     print_r($data);
    //     echo $data[$data['var']];
    // }

    public static function loadAdminAsset()
    {
        echo self::$admin_asset;
    }

    public static function adminAsset($asset)
    {
        $admin_asset = self::$admin_asset;
        $admin_asset .= $asset;
        self::$admin_asset = $admin_asset;
    }

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
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore', 'gxcode']],
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
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore', 'gxcode']],
                    ['genixcms', ['elfinder']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']]";
        } else {
            $toolbar = self::toolbar('mini');
        }
        self::$toolbar = $toolbar;

        return $toolbar;
    }

    public static function toolbarMode($mode)
    {
        self::$toolbar_mode = $mode;

        return self::$toolbar_mode;
    }
}

/* End of file system.class.php */
/* Location: ./inc/lib/system.class.php */
