<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Asset Management Class
 *
 * Handles registration and enqueuing of JS/CSS assets
 * @since 2.0.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */



class Asset
{
    private static $registry = [];
    private static $queue = [
        'header' => [],
        'footer' => []
    ];

    /**
     * Returns the full asset registry (all registered assets).
     *
     * @return array
     */
    public static function getRegistry(): array
    {
        return self::$registry;
    }

    /**
     * Returns the current enqueue queue for header and footer.
     *
     * @return array
     */
    public static function getQueue(): array
    {
        return self::$queue;
    }

    /**
     * Registers a new asset in the system registry for later enqueuing.
     *
     * @param string $id       Unique identifier for the asset.
     * @param string $type     Asset type: 'js', 'css', or 'raw'.
     * @param string $src      The URL or raw HTML content of the asset.
     * @param string $pos      Target position: 'header' or 'footer' (default 'footer').
     * @param array  $deps     List of asset IDs that must be loaded before this one.
     * @param int    $priority Loading priority (0-100, common: 1-9 Frameworks, 10-19 Core, 20+ Plugins).
     * @param string $context  Availability context: 'admin', 'frontend', or 'all'.
     */
    public static function register($id, $type, $src, $pos = "footer", $deps = [], $priority = 20, $context = "admin")
    {
        self::$registry[$id] = [
            "type" => $type, // "js", "css", or "raw"
            "src" => $src,
            "pos" => $pos,
            "deps" => $deps,
            "priority" => $priority,
            "context" => $context
        ];
    }

    /**
     * Adds one or more registered assets to the current page's loading queue.
     * Automatically resolves and enqueues dependencies.
     *
     * @param string|array $id The ID of the asset or an array of asset IDs.
     */
    public static function enqueue($id)
    {
        if (is_array($id)) {
            foreach ($id as $i) {
                self::enqueue($i);
            }
            return;
        }

        if (!isset(self::$registry[$id]))
            return;

        $asset = self::$registry[$id];
        $pos = $asset["pos"];

        // Context check
        $currentContext = (defined('GX_ADMIN') && GX_ADMIN) ? 'admin' : 'frontend';
        $allowedContext = $asset['context'] ?? 'admin';

        if ($allowedContext !== 'all' && $allowedContext !== $currentContext) {
            return;
        }

        // Prevent duplicates
        if (!isset(self::$queue[$pos][$id])) {
            // First, load dependencies
            if (!empty($asset["deps"]) && is_array($asset["deps"])) {
                foreach ($asset["deps"] as $depId) {
                    if (isset(self::$registry[$depId])) {
                        // Ensure dependency has at least the same priority as this asset
                        if (self::$registry[$depId]["priority"] > $asset["priority"]) {
                            self::$registry[$depId]["priority"] = $asset["priority"] - 1;
                        }
                    }
                    self::enqueue($depId);
                }
            }
            self::$queue[$pos][$id] = self::$registry[$id]["priority"];
        }
    }

    /**
     * Removes an asset from the current page's queue.
     * Also clears any rendered cache for this asset to allow re-rendering.
     *
     * @param string|array $id The ID of the asset or an array of asset IDs.
     */
    public static function dequeue($id)
    {
        if (is_array($id)) {
            foreach ($id as $i) {
                self::dequeue($i);
            }
            return;
        }

        // Remove from both header and footer queues
        if (isset(self::$queue['header'][$id])) {
            unset(self::$queue['header'][$id]);
        }
        if (isset(self::$queue['footer'][$id])) {
            unset(self::$queue['footer'][$id]);
        }

        // Also remove from rendered cache to allow re-enqueueing if needed
        if (isset(self::$rendered['header'][$id])) {
            unset(self::$rendered['header'][$id]);
        }
        if (isset(self::$rendered['footer'][$id])) {
            unset(self::$rendered['footer'][$id]);
        }
    }

    private static $rendered = [
        "header" => [],
        "footer" => []
    ];

    /**
     * Renders all queued assets for a specific position (header/footer).
     * Outputs the HTML tags directly to the browser.
     *
     * @param string $pos The position to render ('header' or 'footer').
     */
    public static function render($pos)
    {
        echo self::get($pos);
    }

    /**
     * Retrieves all queued assets for a specific position as a string.
     *
     * @param string $pos The position to retrieve ('header' or 'footer').
     * @return string The combined HTML tags for all queued assets.
     */
    public static function get($pos)
    {
        if (!isset(self::$queue[$pos]) || empty(self::$queue[$pos]))
            return '';

        ob_start();
        // Sort the queue by priority (value of the array)
        asort(self::$queue[$pos]);

        foreach (self::$queue[$pos] as $id => $priority) {
            // Prevent duplicate rendering across multiple calls
            if (isset(self::$rendered[$pos][$id]))
                continue;

            $asset = self::$registry[$id];
            $type = $asset["type"];
            $src = $asset["src"];

            if ($type === "js") {
                echo '<script src="' . $src . '"></script>' . PHP_EOL;
            } elseif ($type === "css") {
                echo '<link rel="stylesheet" href="' . $src . '">' . PHP_EOL;
            } elseif ($type === "raw") {
                echo $src . PHP_EOL;
            }

            // Mark as rendered
            self::$rendered[$pos][$id] = true;
        }
        return ob_get_clean();
    }

    /**
     * Shorthand method to register and immediately enqueue an asset.
     * If only the ID is provided, it enqueues an already registered asset.
     *
     * @param string $id       Unique identifier.
     * @param string $type     Asset type (optional).
     * @param string $src      The asset source (optional).
     * @param string $pos      Target position ('header' or 'footer').
     * @param array  $deps     Dependencies list.
     * @param int    $priority Priority level.
     * @param string $context  Context ('admin', 'frontend', or 'all').
     */
    public static function load($id, $type = null, $src = null, $pos = 'footer', $deps = [], $priority = 20, $context = 'admin')
    {
        if ($type && $src) {
            self::register($id, $type, $src, $pos, $deps, $priority, $context);
        }
        self::enqueue($id);
    }

    /**
     * Initializes the asset manager, registers core libraries, and attaches hooks.
     * Automatically enqueues critical global assets like jQuery and Bootstrap.
     */
    public static function init()
    {
        self::registerCore();
        // Asset manager hooks are redundant as Site class handles rendering
        // after hook execution. This allows correct sorting by priority.

        // Auto-enqueue Core Assets globally (Context handled by registration)
        self::enqueue(['jquery', 'jquery-ui', 'bootstrap-js', 'bootstrap-icons', 'fontawesome']);
        self::enqueue(['gx-toast-css', 'gx-toast-js']);

        // Only enqueue Bootstrap CSS on admin, not frontend
        // Auto-enqueue elFinder for all Admin pages
        if (defined('GX_ADMIN') && GX_ADMIN) {
            self::enqueue('bootstrap-css');
            self::enqueue('elfinder-helper');
        }
    }

    /**
     * Registers all core system libraries into the registry.
     * Includes frameworks (jQuery, Bootstrap), icons (FontAwesome, BI), and internal tools (elFinder).
     */
    public static function registerCore()
    {
        $vendorUrl = Vendor::url();
        $siteUrl = rtrim(Site::$url, "/");
        $offline = defined('OFFLINE_MODE') && OFFLINE_MODE;

        // Priorities: 1-9 Frameworks, 10-19 Core Libs, 20+ Mod Assets

        // ── FONTS ───────────────────────────────────────────────────
        // Inter font: local when offline, Google Fonts when online
        if ($offline) {
            self::register('inter-font', 'css', $siteUrl . '/assets/css/vendor/inter-local.css', 'header', [], 0, 'all');
        } else {
            self::register('inter-font', 'css', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap', 'header', [], 0, 'all');
        }
        self::enqueue('inter-font');
        $jqueryJs    = $offline ? $siteUrl . '/assets/js/vendor/jquery-3.7.1.min.js'       : 'https://code.jquery.com/jquery-3.7.1.min.js';
        $jqueryUiJs  = $offline ? $siteUrl . '/assets/js/vendor/jquery-ui-1.13.2.min.js'   : 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js';
        $jqueryUiCss = $offline ? $siteUrl . '/assets/css/vendor/jquery-ui-1.13.2.min.css' : 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css';

        self::register('jquery',      'js',  $jqueryJs,    'header', [],         1, 'all');
        self::register('jquery-ui',   'js',  $jqueryUiJs,  'header', ['jquery'], 2, 'all');
        self::register('jquery-ui-css','css', $jqueryUiCss, 'header', [],         2, 'all');

        // ── BOOTSTRAP (Priority 3) ──────────────────────────────────
        $bootstrapCss = $offline ? $siteUrl . '/assets/css/vendor/bootstrap-5.3.3.min.css'        : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
        $bootstrapJs  = $offline ? $siteUrl . '/assets/js/vendor/bootstrap-5.3.3.bundle.min.js'   : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
        $bootstrapIco = $offline ? $siteUrl . '/assets/css/vendor/bootstrap-icons-1.11.3.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css';

        self::register('bootstrap-css',   'css', $bootstrapCss, 'header', [],         3, 'all');
        self::register('bootstrap-js',    'js',  $bootstrapJs,  'footer', ['jquery'], 3, 'all');
        self::register('bootstrap-icons', 'css', $bootstrapIco, 'header', [],         4, 'all');

        // ── FONT AWESOME (Priority 5) ───────────────────────────────
        $fontawesome = $offline ? $siteUrl . '/assets/fonts/fontawesome/css/all.min.css' : 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css';

        self::register('fontawesome', 'css', $fontawesome, 'header', [], 5, 'all');


        // elFinder Core Assets (Priority 10+)
        self::register('elfinder-css', 'css', $vendorUrl . '/studio-42/elfinder/css/elfinder.min.css', 'header', [], 10);
        self::register('elfinder-theme', 'css', $vendorUrl . '/studio-42/elfinder/css/theme.css', 'header', ['elfinder-css'], 11);

        // elFinder Custom Styling (From Files::elfinderLib)
        self::register('elfinder-css-custom', 'raw', '
        <style>
            :root { --elfinder-primary: #3b82f6; --elfinder-radius: 10px; }
            .elfinder { border: 1px solid #e2e8f0 !important; border-radius: var(--elfinder-radius) !important; font-family: "Outfit", sans-serif !important; box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important; background: #fff !important; }
            .elfinder-navbar { background: #f8fafc !important; border-right: 1px solid #e2e8f0 !important; }
            .elfinder-toolbar { background: #fff !important; border-bottom: 1px solid #e2e8f0 !important; padding: 10px !important; }
            .elfinder-button { background: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; border-radius: 6px !important; margin-right: 5px !important; }
            .elfinder-button:hover { background: #e2e8f0 !important; }
            .elfinder-button-active { background: var(--elfinder-primary) !important; color: #fff !important; }
            .elfinder-cwd-view-icons .elfinder-cwd-file .elfinder-cwd-filename { border-radius: 4px !important; font-size: 13px !important; margin-top: 5px !important; }
            .ui-state-hover, .ui-widget-content .ui-state-hover { border-color: #cbd5e1 !important; background: #f1f5f9 !important; }
            .ui-state-active, .ui-widget-content .ui-state-active { background: var(--elfinder-primary) !important; border-color: var(--elfinder-primary) !important; color: #fff !important; }
            .elfinder-drag-helper { border-radius: var(--elfinder-radius) !important; }
            .elfinder-statusbar { background: #fff !important; border-top: 1px solid #e2e8f0 !important; padding: 5px 15px !important; font-size: 12px !important; color: #64748b !important; }
            .elfinder-navbar .ui-state-active { background: var(--elfinder-primary) !important; font-weight: 600 !important; }
            .elfinder-navbar .ui-state-hover { background: #f1f5f9 !important; color: #1e293b !important; border-width: 0 !important; }
            .elfinder-tree .elfinder-navbar-arrow { font-family: "bootstrap-icons" !important; }
            .elfinder-tree .elfinder-navbar-arrow:before { content: "\f282"; }
            .elfinder-statusbar .elfinder-stat-selected { color: var(--elfinder-primary) !important; font-weight: 700 !important; }
            .elfinder-button-search input { border-radius: 20px !important; padding: 4px 12px !important; background: #f8fafc !important; border: 1px solid #e2e8f0 !important; }
            .dialogelfinder { border-radius: var(--elfinder-radius) !important; overflow: hidden !important; border: 0 !important; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important; }
            .ui-dialog-titlebar { background: #1e293b !important; color: #fff !important; border: 0 !important; border-radius: 0 !important; padding: 15px 20px !important; font-weight: 600 !important; }
            .ui-dialog-titlebar-close { filter: invert(1) !important; top: 18px !important; right: 20px !important; }
            .elfinder-contextmenu { border-radius: 8px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; padding: 5px 0 !important; background: #fff !important; }
            .elfinder-contextmenu .elfinder-contextmenu-item.ui-state-hover { background: var(--elfinder-primary) !important; color: #fff !important; border: 0 !important; }
            .elfinder-contextmenu-separator { background-color: #f1f5f9 !important; height: 1px !important; margin: 5px 0 !important; }
        </style>', "header", ["elfinder-theme"], 12);

        self::register('elfinder-js', 'js', $vendorUrl . '/studio-42/elfinder/js/elfinder.min.js', 'footer', ['jquery', 'jquery-ui'], 10);
        self::register('elfinder-proxy', 'js', $vendorUrl . '/studio-42/elfinder/js/proxy/elFinderSupportVer1.js', 'footer', ['elfinder-js'], 11);

        // ── ADMIN UI TOOLS (Priority 15-17) ────────────────────────
        $tagsinputCss    = $offline ? $siteUrl . '/assets/css/vendor/jquery-tagsinput-1.3.6.min.css' : 'https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.css';
        $tagsinputJs     = $offline ? $siteUrl . '/assets/js/vendor/jquery-tagsinput-1.3.6.min.js'   : 'https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js';
        $jsvectormapCss  = $offline ? $siteUrl . '/assets/css/vendor/jsvectormap-1.5.3.min.css'      : 'https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css';
        $jsvectormapJs   = $offline ? $siteUrl . '/assets/js/vendor/jsvectormap-1.5.3.min.js'        : 'https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js';
        $jsvectormapWorld= $offline ? $siteUrl . '/assets/js/vendor/jsvectormap-world-1.5.3.js'      : 'https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js';
        $chartjsUrl      = $offline ? $siteUrl . '/assets/js/vendor/chartjs-4.4.1.min.js'            : 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js';

        self::register('tagsinput-css',    'css', $tagsinputCss,     'header', [],                15);
        self::register('tagsinput-js',     'js',  $tagsinputJs,      'header', ['jquery'],        15);
        self::register('jsvectormap-css',  'css', $jsvectormapCss,   'header', [],                16);
        self::register('jsvectormap-js',   'js',  $jsvectormapJs,    'header', ['jquery'],        16);
        self::register('jsvectormap-world','js',  $jsvectormapWorld, 'header', ['jsvectormap-js'],17);
        self::register('chartjs',          'js',  $chartjsUrl,       'header', [],                18);

        // GeniXCMS Base Framework (Basic Styles) - Priority 10
        self::register('genixcms-css', 'css', $siteUrl . '/assets/css/genixcms.css', 'header', [], 10, 'all');

        // gneex-admin custom assets
        self::register('gneex-admin-css', 'css', $siteUrl . '/assets/css/gneex-admin.css', 'header', [], 50);
        self::register('genixcms-js', 'js', $siteUrl . '/assets/js/genixcms.js', 'header', ['jquery'], 50);

        // GX Toast System (Modern Replacement)
        self::register('gx-toast-css', 'raw', '
        <style>
            #gx-toast-container { position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; }
            .gx-toast { background: #0f172a; color: #fff; padding: 14px 22px; border-radius: 14px; font-weight: 600; font-size: 14px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.25); display: flex; align-items: center; gap: 12px; min-width: 280px; max-width: 420px; border: 1px solid rgba(255,255,255,0.08); animation: gxToastIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            .gx-toast.out { animation: gxToastOut 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
            @keyframes gxToastIn  { from { transform: translateX(110%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
            @keyframes gxToastOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(110%); opacity: 0; } }
            .gx-toast .gx-toast-icon { font-size: 18px; flex-shrink: 0; }
            .gx-toast.gx-toast-success .gx-toast-icon { color: #22c55e; }
            .gx-toast.gx-toast-error   .gx-toast-icon { color: #ef4444; }
            .gx-toast.gx-toast-warning .gx-toast-icon { color: #f59e0b; }
            .gx-toast.gx-toast-info    .gx-toast-icon { color: #38bdf8; }
        </style>', 'header', [], 7, 'all');
        self::register('gx-toast-js', 'raw', '
        <script>
        window.showGxToast = function(msg, type) {
            type = type || "success";
            var icons = { success: "bi-check-circle", error: "bi-x-circle", warning: "bi-exclamation-triangle", info: "bi-info-circle" };
            var icon = icons[type] || icons.success;
            var container = document.getElementById("gx-toast-container");
            if (!container) { container = document.createElement("div"); container.id = "gx-toast-container"; document.body.appendChild(container); }
            var toast = document.createElement("div"); toast.className = "gx-toast gx-toast-" + type;
            toast.innerHTML = "<i class=\'bi " + icon + " gx-toast-icon\'></i><span>" + msg + "</span>";
            container.appendChild(toast);
            setTimeout(function() { toast.classList.add("out"); setTimeout(function() { toast.remove(); }, 450); }, 4500);
        };
        </script>', 'footer', [], 7, 'all');

        // Register internal common helpers (Raw HTML/JS) - Priority 15
        $url = Url::ajax('elfinder');
        $elfinderUrl = json_encode($url);
        $editorType = Options::v('editor_type') ?: 'summernote';

        self::register('elfinder-helper', 'raw', '
        <script>
            $(document).ready(function() {
                // Compatibility shim for jQuery UI 1.12+ (deprecated buttonset)
                if ($.fn.buttonset === undefined) {
                    $.fn.buttonset = function() {
                        return this.each(function() {
                            var el = $(this);
                            if (el.is("div")) { el.controlgroup(); } else { el.checkboxradio(); }
                        });
                    };
                }

                if ($("#elfinder").length > 0) {
                    $("#elfinder").elfinder({
                        url : ' . $elfinderUrl . ',
                        baseUrl : "' . $vendorUrl . '/studio-42/elfinder/",
                        height : "100%",
                        lang : "en"
                    });
                }
            });

            window.elfinderDialog = function(context) {
                var gxEditorType = "' . $editorType . '";
                var fm = $("<div/>").dialogelfinder({
                    url : ' . $elfinderUrl . ',
                    lang : "en", width : 840, height: 450,
                    destroyOnClose : true,
                    getFileCallback : function(file, fm) {
                        if (context && typeof context.invoke === "function") {
                            context.invoke("editor.insertImage", file.url);
                        } else if (gxEditorType === "editorjs" || gxEditorType === "gxeditor") {
                            if (window.GxEditor && window.GxEditor._editors && window.GxEditor._editors.length > 0) {
                                // For GxEditor Module
                                var active = window.GxEditor._editors[0];
                                if (active && typeof active.insertImage === "function") {
                                     active.insertImage(file.url);
                                } else {
                                     document.execCommand("insertImage", false, file.url);
                                }
                            } else if (window.__gxEditors) {
                                // For Legacy EditorJS based GxEditor
                                var idx = Object.keys(window.__gxEditors)[0];
                                if (window.__gxEditors[idx]) {
                                    window.__gxEditors[idx].blocks.insert("image", { file: { url: file.url } });
                                }
                            } else {
                                document.execCommand("insertImage", false, file.url);
                            }
                        } else {
                            try {
                                $(".editor").summernote("editor.insertImage", file.url);
                            } catch (e) {
                                document.execCommand("insertImage", false, file.url);
                            }
                        }
                    },
                    commandsOptions : { getfile : { oncomplete : "close", folders : false } }
                }).dialogelfinder("instance");
            };

            window.elfinderDialog2 = function() {
                var fm = $("<div/>").dialogelfinder({
                    url: ' . $elfinderUrl . ',
                    lang: "en", width : 840, height: 450,
                    destroyOnClose: true,
                    getFileCallback: function (file, fm) {
                        $("#post_image").val(file.url);
                        $("#post_image_preview").attr("src", file.url).removeClass("d-none");
                        $("#post_image_placeholder").addClass("d-none");
                    },
                    commandsOptions: { getfile: { oncomplete: "close", folders: false } }
                }).dialogelfinder("instance");
            };
        </script>', 'header', ['elfinder-proxy', 'elfinder-css-custom', 'jquery-ui-css'], 15);

        // Auto-enqueue based on options
        if (Options::v('use_jquery') == 'on') {
            self::enqueue('jquery');
            self::enqueue('jquery-ui');
        }
        if (Options::v('use_bootstrap') == 'on') {
            // Only enqueue Bootstrap CSS on admin, not frontend (to prevent conflicts with TailwindCSS)
            if (defined('GX_ADMIN') && GX_ADMIN) {
                self::enqueue('bootstrap-css');
            }
            self::enqueue('bootstrap-js');
            self::enqueue('bootstrap-icons');
            // self::enqueue('toastr-init'); // Skip legacy toastr if gx-toast is used
        }
        if (Options::v('use_fontawesome') == 'on') {
            self::enqueue('fontawesome');
        }

        // Auto-enqueue admin critical assets
        if (defined('GX_ADMIN') && GX_ADMIN) {
            self::enqueue('tagsinput-css');
            self::enqueue('tagsinput-js');
            self::enqueue('jsvectormap-world');
            self::enqueue('gneex-admin-css');
            self::enqueue('genixcms-js');
        }
    }
}
