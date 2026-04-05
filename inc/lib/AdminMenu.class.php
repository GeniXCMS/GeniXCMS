<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Programmable Admin Menu Registry.
 *
 * Centralizes ALL admin navigation items (core + module-registered).
 * Modules and themes can call AdminMenu::add() to inject menu items
 * without hardcoding HTML in any template file.
 *
 * @since 2.0.0
 * @author GeniXCMS
 * @license MIT
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
            'id'       => 'posts',
            'label'    => _('Posts'),
            'icon'     => 'bi bi-file-earmark-richtext',
            'url'      => 'index.php?page=posts',
            'access'   => 4,
            'position' => 'main',
            'order'    => 20,
            'children' => [
                ['label' => _('All Posts'),   'url' => 'index.php?page=posts',      'access' => 4],
                ['label' => _('Categories'),  'url' => 'index.php?page=categories', 'access' => 1],
                ['label' => _('Tags'),        'url' => 'index.php?page=tags',       'access' => 1],
            ],
        ]);

        // Pages (Editor+)
        self::add([
            'id'       => 'pages',
            'label'    => _('Pages'),
            'icon'     => 'bi bi-journal-text',
            'url'      => 'index.php?page=pages',
            'access'   => 1,
            'position' => 'main',
            'order'    => 30,
        ]);

        // Comments (Editor+)
        self::add([
            'id'       => 'comments',
            'label'    => _('Comments'),
            'icon'     => 'bi bi-chat-left-dots',
            'url'      => 'index.php?page=comments',
            'access'   => 1,
            'position' => 'main',
            'order'    => 40,
        ]);

        // Media (all logged-in)
        self::add([
            'id'       => 'media',
            'label'    => _('Media'),
            'icon'     => 'bi bi-images',
            'url'      => 'index.php?page=media',
            'access'   => 6,
            'position' => 'main',
            'order'    => 50,
        ]);

        // ── MANAGEMENT ───────────────────────────────────────────────────
        
        // User Management (Supervisor+)
        self::add([
            'id'       => 'users',
            'label'    => _('Users'),
            'icon'     => 'bi bi-people',
            'url'      => 'index.php?page=users',
            'access'   => 1,
            'position' => 'management',
            'order'    => 10,
            'children' => [
                ['label' => _('All Users'),   'url' => 'index.php?page=users',       'access' => 1],
                ['label' => _('ACL Manager'), 'url' => 'index.php?page=permissions', 'access' => 1],
            ],
        ]);

        // Appearance Group (Supervisor+)
        self::add([
            'id'       => 'themes',
            'label'    => _('Appearance'),
            'icon'     => 'bi bi-palette',
            'url'      => 'index.php?page=themes',
            'access'   => 1,
            'position' => 'management',
            'order'    => 30,
            'children' => [
                ['label' => _('Themes'),  'url' => 'index.php?page=themes',  'access' => 0],
                ['label' => _('Menus'),   'url' => 'index.php?page=menus',   'access' => 1],
                ['label' => _('Widgets'), 'url' => 'index.php?page=widgets', 'access' => 1],
            ],
        ]);

        // Modules (Admin only)
        self::add([
            'id'       => 'modules',
            'label'    => _('Modules'),
            'icon'     => 'bi bi-plugin',
            'url'      => 'index.php?page=modules',
            'access'   => 0,
            'position' => 'management',
            'order'    => 60,
        ]);

        // System Group (Admin only)
        self::add([
            'id'       => 'system',
            'label'    => _('System'),
            'icon'     => 'bi bi-shield-check',
            'url'      => 'index.php?page=health',
            'access'   => 0,
            'position' => 'management',
            'order'    => 70,
            'children' => [
                ['label' => _('System Health'), 'url' => 'index.php?page=health', 'access' => 0],
                ['label' => _('Updates'),       'url' => 'index.php?page=updates', 'access' => 0],
            ],
        ]);

        // ── SETTINGS ─────────────────────────────────────────────────────

        self::add([
            'id'       => 'settings',
            'label'    => _('Settings'),
            'icon'     => 'bi bi-gear',
            'url'      => 'index.php?page=settings',
            'access'   => 1,
            'position' => 'settings',
            'order'    => 10,
            'children' => [
                ['label' => _('Global Settings'),       'url' => 'index.php?page=settings',            'access' => 1],
                ['label' => _('Media Settings'),         'url' => 'index.php?page=settings-media',      'access' => 0],
                ['label' => _('Multilanguage Settings'), 'url' => 'index.php?page=settings-multilang',  'access' => 0],
                ['label' => _('Permalink Settings'),     'url' => 'index.php?page=settings-permalink',  'access' => 0],
                ['label' => _('Comments Settings'),      'url' => 'index.php?page=settings-comments',   'access' => 1],
                ['label' => _('Cache Settings'),         'url' => 'index.php?page=settings-cache',      'access' => 1],
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
            'id'       => 'menu_' . uniqid(),
            'label'    => _('Menu Item'),
            'icon'     => 'bi bi-circle',
            'url'      => '#',
            'access'   => 6,
            'position' => 'external',
            'order'    => 50,
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
        if (empty($items)) return '';

        $currentPage = $_GET['page'] ?? '';
        $currentMod  = $_GET['mod']  ?? '';
        $currentView = $_GET['view'] ?? '';
        $currentAct  = $_GET['act']  ?? '';
        $currentType = $_GET['type'] ?? '';

        $html = '';
        foreach ($items as $item) {
            // Access check — skip children-level access check here (handled in render)
            if (!User::access((string) $item['access'])) continue;

            $isActive = self::isActive($item, $currentPage, $currentMod);

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
                        $cActive   = self::isUrlActive($child['url'], $currentPage, $currentMod, $currentView, $currentAct, $currentType) ? 'text-white' : '';
                        $cIcon     = isset($child['icon']) ? "<i class=\"{$child['icon']} me-1\"></i> " : '';
                        $html     .= "<li><a href=\"{$child['url']}\" class=\"{$cActive}\">{$cIcon}{$child['label']}</a></li>";
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
        if (empty($items)) return '';

        $currentPage = $_GET['page'] ?? '';
        $currentMod  = $_GET['mod']  ?? '';
        $currentView = $_GET['view'] ?? '';
        $currentAct  = $_GET['act']  ?? '';
        $currentType = $_GET['type'] ?? '';

        $html = '';
        foreach ($items as $item) {
            if (!User::access((string) $item['access'])) continue;

            $isActive    = self::isActive($item, $currentPage, $currentMod);
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
                        $cActive = self::isUrlActive($child['url'], $currentPage, $currentMod, $currentView, $currentAct, $currentType) ? 'bg-primary bg-opacity-10 text-primary fw-bold' : '';
                        $cIcon  = isset($child['icon']) ? "<i class=\"{$child['icon']} me-2 text-muted\"></i>" : '';
                        $html  .= "<li><a class=\"dropdown-item py-2 {$cActive}\" href=\"{$child['url']}\">{$cIcon}{$child['label']}</a></li>";
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

    /**
     * Determine if a menu item is currently active.
     */
    private static function isActive(array $item, string $page, string $mod): bool
    {
        $currentView = $_GET['view'] ?? '';
        $currentAct  = $_GET['act']  ?? '';
        $currentType = $_GET['type'] ?? '';

        // Exact ID match for simple pages
        if ($page === $item['id'] && empty($currentView) && empty($currentAct) && empty($currentType)) return true;
        if ($mod  === $item['id'] && empty($currentView) && empty($currentAct) && empty($currentType)) return true;

        // Specific URL matching (handles parameters like page=themes&view=options)
        if (self::isUrlActive($item['url'], $page, $mod, $currentView, $currentAct, $currentType)) return true;

        // Check if any child is active
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if (self::isUrlActive($child['url'], $page, $mod, $currentView, $currentAct, $currentType)) return true;
            }
        }
        return false;
    }

    /**
     * Precise URL check for active states.
     * Prevents 'themes' from matching 'themes&view=options' by checking 
     * both the base parameter and optional 'view' parameter.
     */
    private static function isUrlActive(string $url, string $page, string $mod, string $view = '', string $act = '', string $type = ''): bool
    {
        $query = parse_url($url, PHP_URL_QUERY) ?? '';
        parse_str($query, $params);

        $itemPage = $params['page'] ?? '';
        $itemMod  = $params['mod']  ?? '';
        $itemView = $params['view'] ?? '';
        $itemAct  = $params['act']  ?? '';
        $itemType = $params['type'] ?? '';

        if (empty($itemPage) && empty($itemMod) && empty($page) && empty($mod)) return true;

        $pageMatch = ($page === $itemPage);
        $modMatch  = ($mod === $itemMod);

        if (!$pageMatch && !$modMatch) return false;
        if ($itemPage && !$pageMatch) return false;
        if ($itemMod && !$modMatch) return false;

        $viewMatch = ($itemView !== '') ? ($view === $itemView) : ($view === '' || $view === 'all');
        $actMatch  = ($itemAct !== '')  ? ($act === $itemAct)   : ($act === '' || $act === 'index');
        $typeMatch = ($itemType !== '') ? ($type === $itemType) : ($type === '' || $type === 'post');

        return $viewMatch && $actMatch && $typeMatch;
    }
}

/* End of file AdminMenu.class.php */
/* Location: ./inc/lib/AdminMenu.class.php */
