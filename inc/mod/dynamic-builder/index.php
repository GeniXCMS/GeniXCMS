<?php
/**
 * Name: Page Dynamic Builder
 * Desc: Module builder drag & drop mirip Elementor untuk membuat halaman yang menakjubkan.
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id
 * License: MIT License
 * Icon: bi bi-layers-half
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class DynamicBuilder
{
    public static function init()
    {
        Hooks::attach('admin_footer_action', array('DynamicBuilder', 'injectStatic'));
        Hooks::attach('page_param_form_bottom', array('DynamicBuilder', 'injectToggle'));
        Hooks::attach('footer_load_lib', array('DynamicBuilder', 'injectFrontendJS'));

        // Automatic Font Queuing on Frontend
        Hooks::attach('header_load_lib', array('DynamicBuilder', 'enqueueFonts'));
        Hooks::attach('admin_header_action', array('DynamicBuilder', 'handleAssets'));

        // Register Assets
        $modUrl = self::getModUrl();
        Asset::register('material-symbols', 'css', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200', 'header', [], 5);
        Asset::register('grapesjs-css', 'css', 'https://cdn.jsdelivr.net/npm/grapesjs@0.21.10/dist/css/grapes.min.css', 'header', [], 20);
        Asset::register('grapesjs-js', 'js', 'https://unpkg.com/grapesjs@0.21.10/dist/grapes.min.js', 'footer', ['jquery'], 20);
        Asset::register('builder-css', 'css', $modUrl . 'assets/css/builder.css', 'header', ['grapesjs-css'], 21);
        Asset::register('builder-js', 'js', $modUrl . 'assets/js/builder.js?v=' . time(), 'footer', ['grapesjs-js'], 21);

        // Register Admin Menu
        AdminMenu::add([
            'id' => 'dynamic-builder',
            'label' => 'Dynamic Builder',
            'icon' => 'bi bi-magic',
            'url' => 'index.php?page=mods&mod=dynamic-builder',
            'access' => 1,
            'position' => 'external',
            'order' => 31,
        ]);

        // Handle Ajax Save
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'dynamic_builder_save') {
            self::savePage();
        }
    }

    public static function handleAssets()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'pages' && (isset($_GET['act']) && ($_GET['act'] == 'add' || $_GET['act'] == 'edit'))) {
            Asset::enqueue(['material-symbols', 'grapesjs-css', 'builder-css', 'grapesjs-js', 'builder-js']);
        }
    }

    public static function enqueueFonts()
    {
        global $data;

        $content = '';
        if (defined('GX_ADMIN') && GX_ADMIN) {
            // On admin, we scan when editing/viewing
        } else {
            // Frontend
            if (isset($data['posts'][0]->content)) {
                $content = $data['posts'][0]->content;
            }
        }

        if ($content) {
            $fonts = [
                'Inter' => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
                'Roboto' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
                'Open Sans' => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap',
                'Montserrat' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap',
                'Poppins' => 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
                'Playfair Display' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap',
                'Lato' => 'https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap',
                'Plus Jakarta Sans' => 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap'
            ];

            foreach ($fonts as $name => $url) {
                // Check if font name exists in content (GrapesJS adds font-family: 'Font Name')
                if (stripos($content, '"' . $name . '"') !== false || stripos($content, "'" . $name . "'") !== false || stripos($content, $name . ',') !== false) {
                    $id = 'font-' . strtolower(str_replace(' ', '-', $name));
                    Asset::load($id, 'css', $url, 'header', [], 20, 'all');
                }
            }
        }
    }

    public static function savePage()
    {
        if (!User::access(0)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        // This method is a placeholder for direct AJAX saving if implemented in the future.
        // Currently, the builder exports content back to the main editor.
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'AJAX Save endpoint reached']);
        exit;
    }

    public static function getModUrl()
    {
        return rtrim(Site::$url, '/') . '/inc/mod/dynamic-builder/';
    }

    public static function getBlocks()
    {
        return Hooks::filter('dynamic_builder_blocks', []);
    }

    public static function injectFrontendJS()
    {
        $modUrl = self::getModUrl();
        $apiBase = Url::ajax('api');
        $config = [
            'siteUrl' => rtrim(Site::$url, '/') . '/',
            'apiEndpoint' => Url::ajax('api', ['action' => 'recent_posts', 'num' => 3]),
            'ajaxToken' => Token::create(),
            'elfinderUrl' => rtrim(Site::$url, '/') . '/inc/lib/vendor/studio-42/elfinder/elfinder-gx.php',
        ];

        echo '<script>window.dynamicBuilderConfig = ' . json_encode($config, JSON_UNESCAPED_SLASHES) . ';</script>';
        echo '<script src="' . $modUrl . 'assets/js/front.js"></script>';
    }

    public static function injectToggle()
    {
        echo '
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 mt-4 bg-primary text-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold m-0"><i class="bi bi-magic me-2"></i> Design with Dynamic Builder</h5>
                    <p class="small m-0 text-white-50">Create stunning layouts with visual drag & drop experience.</p>
                </div>
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-primary" id="launch-builder">
                    Launch Visual Editor
                </button>
            </div>
        </div>
        ';
    }

    public static function injectStatic()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'pages' && (isset($_GET['act']) && ($_GET['act'] == 'add' || $_GET['act'] == 'edit'))) {
            include __DIR__ . '/layout.php';
            echo Hooks::run('dynamic_builder_layout');
        }
    }
}

DynamicBuilder::init();
