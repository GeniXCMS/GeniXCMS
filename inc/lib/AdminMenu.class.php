<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Programmable Admin Menu Registry.
 *
 * Centralizes ALL admin navigation items (core + module-registered).
 * Modules and themes can call AdminMenu::add() to inject menu items
 * without hardcoding HTML in any template file.
 * @since 2.0.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class AdminMenu
{
    /** @var array Registered menu items */
    private static $_items = [];

    /**
     * Bootstrap the core menu items.
     * Called automatically via new AdminMenu() in System::__construct().
     */
    public function __construct()
    {
        self::bootCoreMenu();
    }

    /**
     * Register all default GeniXCMS core menu items.
     * These replace the hardcoded HTML previously in header.php.
     */
    private static function bootCoreMenu(): void
    {
        // ── MAIN NAVIGATION ──────────────────────────────────────────────

        // Posts (with children – requires Author access)
        self::add([
            'id' => 'posts',
            'label' => _('Posts'),
            'icon' => 'bi bi-file-earmark-richtext',
            'url' => 'index.php?page=posts',
            'access' => 4,
            'position' => 'main',
            'order' => 20,
            'children' => [
                ['label' => _('All Posts'),   'icon' => 'bi bi-file-earmark-richtext', 'url' => 'index.php?page=posts', 'access' => 4, 'aliases' => [
                    'index.php?page=posts&act=add&type=post',
                    'index.php?page=posts&act=edit&type=post'
                ]],
                ['label' => _('Categories'),  'icon' => 'bi bi-tag',                   'url' => 'index.php?page=categories&type=post', 'access' => 1],
                ['label' => _('Tags'),         'icon' => 'bi bi-hash',                  'url' => 'index.php?page=tags', 'access' => 1],
            ],
        ]);

        // Pages (Editor+)
        self::add([
            'id' => 'pages',
            'label' => _('Pages'),
            'icon' => 'bi bi-journal-text',
            'url' => 'index.php?page=pages',
            'access' => 1,
            'position' => 'main',
            'order' => 30,
            'aliases' => [
                'index.php?page=pages&act=add&type=page', 
                'index.php?page=pages&act=edit&type=page',
                'index.php?page=posts&act=add&type=page', // Ensure we catch standard pages logic if routed via posts logic
                'index.php?page=posts&act=edit&type=page'
            ]
        ]);

        // Comments (Editor+)
        self::add([
            'id' => 'comments',
            'label' => _('Comments'),
            'icon' => 'bi bi-chat-left-dots',
            'url' => 'index.php?page=comments',
            'access' => 1,
            'position' => 'main',
            'order' => 40,
            'aliases' => ['index.php?page=comments&act=edit']
        ]);

        // Media (all logged-in)
        self::add([
            'id' => 'media',
            'label' => _('Media'),
            'icon' => 'bi bi-images',
            'url' => 'index.php?page=media',
            'access' => 6,
            'position' => 'main',
            'order' => 50,
        ]);

        // ── MANAGEMENT ───────────────────────────────────────────────────

        // User Management (Supervisor+)
        self::add([
            'id' => 'users',
            'label' => _('Users'),
            'icon' => 'bi bi-people',
            'url' => 'index.php?page=users',
            'access' => 1,
            'position' => 'management',
            'order' => 10,
            'children' => [
                ['label' => _('All Users'),  'icon' => 'bi bi-people',       'url' => 'index.php?page=users', 'access' => 1, 'aliases' => ['index.php?page=users&act=edit', 'index.php?page=users&act=add']],
                ['label' => _('ACL Manager'),'icon' => 'bi bi-shield-lock',  'url' => 'index.php?page=permissions', 'access' => 1],
            ],
        ]);

        // Appearance Group (Supervisor+)
        self::add([
            'id' => 'themes',
            'label' => _('Appearance'),
            'icon' => 'bi bi-palette',
            'url' => 'index.php?page=themes',
            'access' => 1,
            'position' => 'management',
            'order' => 30,
            'children' => [
                ['label' => _('Themes'),  'icon' => 'bi bi-palette2',       'url' => 'index.php?page=themes', 'access' => 0],
                ['label' => _('Menus'),   'icon' => 'bi bi-list-nested',    'url' => 'index.php?page=menus', 'access' => 1, 'aliases' => ['index.php?page=menus&act=edit']],
                ['label' => _('Widgets'), 'icon' => 'bi bi-layout-sidebar', 'url' => 'index.php?page=widgets', 'access' => 1],
            ],
        ]);

        // Modules (Admin only)
        self::add([
            'id' => 'modules',
            'label' => _('Modules'),
            'icon' => 'bi bi-plugin',
            'url' => 'index.php?page=modules',
            'access' => 0,
            'position' => 'management',
            'order' => 60,
        ]);

        // System Group (Admin only)
        self::add([
            'id' => 'system',
            'label' => _('System'),
            'icon' => 'bi bi-shield-check',
            'url' => 'index.php?page=health',
            'access' => 0,
            'position' => 'management',
            'order' => 70,
            'children' => [
                ['label' => _('System Health'), 'icon' => 'bi bi-heart-pulse',  'url' => 'index.php?page=health',  'access' => 0],
                ['label' => _('Updates'),        'icon' => 'bi bi-arrow-repeat', 'url' => 'index.php?page=updates', 'access' => 0],
            ],
        ]);

        // Developer Tools (Admin only, DEVELOPER_MODE must be true)
        if (defined('DEVELOPER_MODE') && DEVELOPER_MODE) {
            self::add([
                'id' => 'devtools',
                'label' => _('Dev Tools'),
                'icon' => 'bi bi-terminal',
                'url' => 'index.php?page=devtools-assets',
                'access' => 0,
                'position' => 'management',
                'order' => 80,
                'children' => [
                    ['label' => _('Asset Inspector'), 'icon' => 'bi bi-box-seam',   'url' => 'index.php?page=devtools-assets', 'access' => 0],
                    ['label' => _('Hook Inspector'),  'icon' => 'bi bi-diagram-3',  'url' => 'index.php?page=devtools-hooks',  'access' => 0],
                ],
            ]);
        }

        // ── SETTINGS ─────────────────────────────────────────────────────

        self::add([
            'id' => 'settings',
            'label' => _('Settings'),
            'icon' => 'bi bi-gear',
            'url' => 'index.php?page=settings',
            'access' => 1,
            'position' => 'settings',
            'order' => 10,
            'children' => [
                ['label' => _('Global Settings'),        'icon' => 'bi bi-sliders',          'url' => 'index.php?page=settings',            'access' => 1],
                ['label' => _('Media Settings'),         'icon' => 'bi bi-image',             'url' => 'index.php?page=settings-media',       'access' => 0],
                ['label' => _('Multilanguage Settings'), 'icon' => 'bi bi-translate',         'url' => 'index.php?page=settings-multilang',   'access' => 0],
                ['label' => _('Permalink Settings'),     'icon' => 'bi bi-link-45deg',        'url' => 'index.php?page=settings-permalink',   'access' => 0],
                ['label' => _('Comments Settings'),      'icon' => 'bi bi-chat-square-text',  'url' => 'index.php?page=settings-comments',    'access' => 1],
                ['label' => _('Cache Settings'),         'icon' => 'bi bi-lightning-charge',  'url' => 'index.php?page=settings-cache',       'access' => 1],
                ['label' => _('API Service'),            'icon' => 'bi bi-cloud-arrow-up',    'url' => 'index.php?page=settings-api',         'access' => 0],
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Register a new admin menu item.
     *
     * @param array $item {
     *   @type string   $id        Unique identifier.
     *   @type string   $label     Display label. Use _() for i18n.
     *   @type string   $icon      Bootstrap Icons class (e.g. 'bi bi-box').
     *   @type string   $url       Target URL.
     *   @type int      $access    Minimum user level (0=Admin … 6=Member). Default: 6.
     *   @type string   $position  'main'|'management'|'settings'|'external'. Default: 'external'.
     *   @type int      $order     Sort weight. Lower = higher position. Default: 50.
     *   @type array    $children  Sub-items: [['label','url','icon','access'], ...].
     * }
     */
    public static function add(array $item): void
    {
        $item = array_merge([
            'id' => 'menu_' . uniqid(),
            'label' => _('Menu Item'),
            'icon' => 'bi bi-circle',
            'url' => '#',
            'access' => 6,
            'position' => 'external',
            'order' => 50,
            'children' => [],
        ], $item);

        self::$_items[$item['id']] = $item;
    }

    /**
     * Add multiple sub-menu items to an existing parent menu.
     *
     * @param string $parentId The ID of the parent menu (e.g., 'posts').
     * @param array  $children Array of child arrays [['label', 'url', ...], ...].
     */
    public static function addChildren(string $parentId, array $children): void
    {
        if (isset(self::$_items[$parentId])) {
            if (!isset(self::$_items[$parentId]['children'])) {
                self::$_items[$parentId]['children'] = [];
            }
            foreach ($children as $child) {
                self::$_items[$parentId]['children'][] = $child;
            }
        }
    }

    /**
     * Add a single sub-menu item to an existing parent menu.
     *
     * @param string $parentId The ID of the parent menu (e.g., 'posts').
     * @param array  $child    Child definition ['label' => '...', 'url' => '...'].
     */
    public static function addChild(string $parentId, array $child): void
    {
        self::addChildren($parentId, array($child));
    }

    /**
     * Remove a registered menu item by its ID.
     */
    public static function remove(string $id): void
    {
        unset(self::$_items[$id]);
    }

    /**
     * Retrieve all items, optionally filtered by position.
     *
     * @param string|null $position  'main'|'management'|'settings'|'external'|null (all).
     * @return array Sorted array of menu items.
     */
    public static function getItems(?string $position = null): array
    {
        $items = self::$_items;

        if ($position !== null) {
            $items = array_filter($items, fn($i) => $i['position'] === $position);
        }

        usort($items, fn($a, $b) => $a['order'] <=> $b['order']);

        return $items;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RENDER: SIDEBAR
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Render <li> items for the admin sidebar.
     *
     * @param string|null $position  Position group to render.
     * @param string      $style     'sidebar' (default).
     * @return string HTML
     */
    public static function renderSidebar(?string $position = null): string
    {
        $items = self::getItems($position);
        if (empty($items))
            return '';

        $currentPage = $_GET['page'] ?? '';
        $currentMod = $_GET['mod'] ?? '';
        $currentView = $_GET['view'] ?? '';
        $currentAct = $_GET['act'] ?? '';
        $currentType = $_GET['type'] ?? '';
        $currentSel = $_GET['sel'] ?? '';

        $html = '';
        foreach ($items as $item) {
            // Access check — skip children-level access check here (handled in render)
            if (!User::access((string) $item['access']))
                continue;

            $isActive = self::isActive($item, $currentPage, $currentMod, $currentSel);

            if (!empty($item['children'])) {
                // Filter children the user can access
                $visibleChildren = array_filter(
                    $item['children'],
                    fn($c) => User::access((string) ($c['access'] ?? 6))
                );
                if (!empty($visibleChildren)) {
                    $activeClass = $isActive ? 'open active' : '';
                    $html .= "<li class=\"nav-item {$activeClass}\">";
                    $html .= "<a href=\"#\" class=\"has-tree\"><i class=\"{$item['icon']}\"></i> <span>{$item['label']}</span> <i class=\"bi bi-chevron-down ms-auto small\"></i></a>";
                    $html .= '<ul class="nav-tree">';
                    foreach ($visibleChildren as $child) {
                        $cActive = self::isUrlActive($child['url'], $currentPage, $currentMod, $currentView, $currentAct, $currentType, $currentSel);
                        if (!$cActive && !empty($child['aliases'])) {
                            foreach ((array)$child['aliases'] as $alias) {
                                if (self::isUrlActive($alias, $currentPage, $currentMod, $currentView, $currentAct, $currentType, $currentSel)) {
                                    $cActive = true;
                                    break;
                                }
                            }
                        }
                        $cActiveCls = $cActive ? 'text-white fw-bold active' : '';
                        $cIcon = isset($child['icon']) ? "<i class=\"{$child['icon']} me-1\"></i> " : '';
                        $html .= "<li><a href=\"{$child['url']}\" class=\"{$cActiveCls}\">{$cIcon}{$child['label']}</a></li>";
                    }
                    $html .= '</ul></li>';
                } else {
                    $activeClass = $isActive ? 'active' : '';
                    $html .= "<li class=\"{$activeClass}\">";
                    $html .= "<a href=\"{$item['url']}\"><i class=\"{$item['icon']}\"></i> <span>{$item['label']}</span></a>";
                    $html .= '</li>';
                }
            } else {
                $activeClass = $isActive ? 'active' : '';
                $html .= "<li class=\"{$activeClass}\">";
                $html .= "<a href=\"{$item['url']}\"><i class=\"{$item['icon']}\"></i> <span>{$item['label']}</span></a>";
                $html .= '</li>';
            }
        }

        return $html;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RENDER: TOP NAVBAR
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Render Bootstrap nav items for the horizontal top navbar.
     *
     * @param string|null $position  Position group to render.
     * @param bool        $nested    Set to true if rendering inside another dropdown menu.
     * @return string HTML
     */
    public static function renderTopNav(?string $position = null, bool $nested = false): string
    {
        $items = self::getItems($position);
        if (empty($items))
            return '';

        $currentPage = $_GET['page'] ?? '';
        $currentMod = $_GET['mod'] ?? '';
        $currentView = $_GET['view'] ?? '';
        $currentAct = $_GET['act'] ?? '';
        $currentType = $_GET['type'] ?? '';
        $currentSel = $_GET['sel'] ?? '';

        $html = '';
        foreach ($items as $item) {
            if (!User::access((string) $item['access']))
                continue;

            $isActive = self::isActive($item, $currentPage, $currentMod, $currentSel);
            $activeClass = $isActive
                ? 'bg-primary bg-opacity-10 text-primary fw-bold'
                : 'text-secondary hover-bg-light';

            if (!empty($item['children'])) {
                $visibleChildren = array_filter(
                    $item['children'],
                    fn($c) => User::access((string) ($c['access'] ?? 6))
                );
                if (!empty($visibleChildren)) {
                    if ($nested) {
                        $html .= "<li class=\"dropdown dropend\">";
                        $html .= "<a class=\"dropdown-item dropdown-toggle px-3 py-2 {$activeClass}\" href=\"#\" data-bs-toggle=\"dropdown\">";
                    } else {
                        $html .= "<li class=\"nav-item dropdown\">";
                        $html .= "<a class=\"nav-link dropdown-toggle px-3 py-2 rounded {$activeClass}\" href=\"#\" data-bs-toggle=\"dropdown\">";
                    }
                    $html .= "<i class=\"{$item['icon']} me-1\"></i> {$item['label']}</a>";
                    $html .= "<ul class=\"dropdown-menu border-0 shadow-sm mt-0 rounded-3\">";
                    foreach ($visibleChildren as $child) {
                        $cActive = self::isUrlActive($child['url'], $currentPage, $currentMod, $currentView, $currentAct, $currentType, $currentSel);
                        if (!$cActive && !empty($child['aliases'])) {
                            foreach ((array)$child['aliases'] as $alias) {
                                if (self::isUrlActive($alias, $currentPage, $currentMod, $currentView, $currentAct, $currentType, $currentSel)) {
                                    $cActive = true;
                                    break;
                                }
                            }
                        }
                        $cActiveCls = $cActive ? 'bg-primary bg-opacity-10 text-primary fw-bold active' : '';
                        $cIcon = isset($child['icon']) ? "<i class=\"{$child['icon']} me-2 text-muted\"></i>" : '';
                        $html .= "<li><a class=\"dropdown-item py-2 {$cActiveCls}\" href=\"{$child['url']}\">{$cIcon}{$child['label']}</a></li>";
                    }
                    $html .= "</ul></li>";
                } else {
                    if ($nested) {
                        $html .= "<li>";
                        $html .= "<a class=\"dropdown-item px-3 py-2 {$activeClass}\" href=\"{$item['url']}\">";
                    } else {
                        $html .= "<li class=\"nav-item\">";
                        $html .= "<a class=\"nav-link px-3 py-2 rounded {$activeClass}\" href=\"{$item['url']}\">";
                    }
                    $html .= "<i class=\"{$item['icon']} me-1\"></i> {$item['label']}</a>";
                    $html .= "</li>";
                }
            } else {
                if ($nested) {
                    $html .= "<li>";
                    $html .= "<a class=\"dropdown-item px-3 py-2 {$activeClass}\" href=\"{$item['url']}\">";
                } else {
                    $html .= "<li class=\"nav-item\">";
                    $html .= "<a class=\"nav-link px-3 py-2 rounded {$activeClass}\" href=\"{$item['url']}\">";
                }
                $html .= "<i class=\"{$item['icon']} me-1\"></i> {$item['label']}</a>";
                $html .= "</li>";
            }
        }

        return $html;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private static function isActive(array $item, string $page, string $mod, string $sel = ''): bool
    {
        $currentView = $_GET['view'] ?? '';
        $currentAct = $_GET['act'] ?? '';
        $currentType = $_GET['type'] ?? '';
        $currentSel = $sel ?: ($_GET['sel'] ?? '');

        // Exact ID match for simple pages
        if ($page === $item['id'] && empty($currentView) && empty($currentAct) && empty($currentType) && empty($currentSel))
            return true;
        if ($mod === $item['id'] && empty($currentView) && empty($currentAct) && empty($currentType) && empty($currentSel))
            return true;

        // Specific URL matching
        if (self::isUrlActive($item['url'], $page, $mod, $currentView, $currentAct, $currentType, $currentSel))
            return true;
        
        if (!empty($item['aliases'])) {
            foreach ((array)$item['aliases'] as $alias) {
                if (self::isUrlActive($alias, $page, $mod, $currentView, $currentAct, $currentType, $currentSel))
                    return true;
            }
        }

        // Check if any child is active
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if (self::isUrlActive($child['url'], $page, $mod, $currentView, $currentAct, $currentType, $currentSel))
                    return true;
                if (!empty($child['aliases'])) {
                    foreach ((array)$child['aliases'] as $alias) {
                        if (self::isUrlActive($alias, $page, $mod, $currentView, $currentAct, $currentType, $currentSel))
                            return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Precise URL check for active states.
     * Prevents 'themes' from matching 'themes&view=options' by checking 
     * both the base parameter and optional 'view' parameter.
     */
    private static function isUrlActive(string $url, string $page, string $mod, string $view = '', string $act = '', string $type = '', string $sel = ''): bool
    {
        $query = parse_url($url, PHP_URL_QUERY) ?? '';
        parse_str($query, $params);

        $itemPage = $params['page'] ?? '';
        $itemMod = $params['mod'] ?? '';
        $itemView = $params['view'] ?? '';
        $itemAct = $params['act'] ?? '';
        $itemType = $params['type'] ?? '';
        $itemSel = $params['sel'] ?? '';

        if (empty($itemPage) && empty($itemMod) && empty($page) && empty($mod))
            return true;

        $pageMatch = ($page === $itemPage);
        $modMatch = ($mod === $itemMod);

        if (!$pageMatch && !$modMatch)
            return false;
        if ($itemPage && !$pageMatch)
            return false;
        if ($itemMod && !$modMatch)
            return false;

        $viewMatch = ($itemView !== '') ? ($view === $itemView) : ($view === '' || $view === 'all');
        $actMatch = ($itemAct !== '') ? ($act === $itemAct) : ($act === '' || $act === 'index');
        $typeMatch = ($itemType !== '') ? ($type === $itemType) : ($type === '' || $type === 'post');
        $selMatch = ($itemSel !== '') ? ($sel === $itemSel) : true;

        return $viewMatch && $actMatch && $typeMatch && $selMatch;
    }
}

/* End of file AdminMenu.class.php */
/* Location: ./inc/lib/AdminMenu.class.php */
