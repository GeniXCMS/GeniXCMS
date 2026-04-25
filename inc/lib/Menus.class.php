<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20141007
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Menus Class.
 *
 * This class is for managing the menu at the dasboard.
 *
 * @since 0.0.1
 */
class Menus
{
    /**
     * Menus Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Checks if a menu item has children or finds items by parent/menu ID.
     *
     * @param int|string $parent The parent item ID (optional).
     * @param string     $menuid The menu identifier (optional).
     * @return array              Array of menu items matching criteria.
     */
    public static function isHadParent($parent = '', $menuid = '')
    {
        $parent = Typo::cleanX($parent);
        $menuid = Typo::cleanX($menuid);
        $q = Query::table('menus');
        if (isset($menuid) && $menuid != '') {
            $q->where('menuid', $menuid);
        }
        if (isset($parent) && $parent !== '') {
            $q->where('parent', (string) $parent);
        }

        return $q->get();
    }

    /**
     * Retrieves the parent ID for a specific menu item.
     *
     * @param int $id The menu item ID.
     * @return int|string The parent item ID.
     */
    public static function getParent($id)
    {
        $q = self::getId($id);

        return $q[0]->parent;
    }

    /**
     * Renders an HTML menu for the frontend.
     * Supports nested submenus up to 4 levels and optional Bootstrap 5 styling.
     *
     * @param string $menuid    The menu group identifier.
     * @param string $class     Custom CSS class for the root <ul>.
     * @param bool   $bsnav     Whether to apply Bootstrap 5 navigation classes (default: false).
     * @param string $itemClass Additional CSS class for anchor (<a>) elements.
     * @return string            The generated HTML menu.
     */
    public static function getMenu($menuid, $class = '', $bsnav = false, $itemClass = '')
    {
        $menus = self::getMenuRaw($menuid);
        $n = count($menus);
        if ($n > 0) {
            $menu = "<ul class=\"menu-{$menuid} {$class}\">";
            foreach ($menus as $m) {

                if ($m->parent == '0') {
                    $parent = self::isHadParent($m->id, $menuid);
                    $n = count($parent);
                    if ($n > 0 && $bsnav) {
                        $class = 'nav-item dropdown';
                        $aclass = 'nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"';
                    } else {
                        $class = $bsnav ? 'nav-item' : '';
                        $aclass = $bsnav ? 'nav-link' : '';
                    }
                    $type = $m->type;
                    $menu .= "<li class='$class'>";
                    $menu .= "<a href='" . Url::$type($m->value) . "' class=\"{$m->class} {$aclass} {$itemClass}\">" . $m->name . '</a>';
                    $parent = $m->id;
                    //                    echo $parent;

                    if ($n > 0) {
                        $class = $bsnav ? 'dropdown-menu' : '';
                        $menu .= "<ul class=\" {$class}\" role=\"dropdown\">";
                        foreach ($menus as $m2) {
                            if ($m2->parent == $m->id) {
                                $parent = self::isHadParent($m2->id, $menuid);
                                $n = count($parent);
                                if ($n > 0 && $bsnav) {
                                    $class = 'dropdown-submenu dropdown-item dropdown-toggle';
                                    $aclass = '" data-bs-toggle="dropdown" aria-expanded="false"';
                                } else {
                                    $class = '';
                                    $aclass = $bsnav ? 'dropdown-item' : '';
                                }
                                $type = $m2->type;
                                //                                print_r($m2);
                                $menu .= "<li $class>";
                                $menu .= "<a href='" . Url::$type($m2->value) . "' class=\"{$m2->class} {$aclass} {$itemClass}\">" . $m2->name . '</a>';

                                if ($n > 0) {
                                    $class = 'dropdown-menu';
                                    $menu .= "<ul class=\" {$class}\">";
                                    foreach ($menus as $m3) {
                                        if ($m3->parent == $m2->id) {
                                            $parent = self::isHadParent($m3->id, $menuid);
                                            $n = count($parent);
                                            if ($n > 0 && $bsnav) {
                                                $class = '';
                                                $aclass = 'dropdown-item';
                                            } else {
                                                $class = '';
                                                $aclass = $bsnav ? 'dropdown-item' : '';
                                            }
                                            $type = $m3->type;
                                            $menu .= "<li $class>";
                                            //$menu .= "<li>";
                                            $menu .= "<a href='" . Url::$type($m3->value) . "' class=\"{$m3->class} {$aclass} {$itemClass}\">" . $m3->name . '</a>';

                                            if ($n > 0) {
                                                $class = $bsnav ? 'dropdown-menu' : '';
                                                $menu .= "<ul class=\" {$class}\">";
                                                foreach ($menus as $m4) {
                                                    if ($m4->parent == $m3->id) {
                                                        $parent = self::isHadParent($m4->id, $menuid);
                                                        $n = count($parent);
                                                        if ($n > 0 && $bsnav) {
                                                            $class = 'class="dropdown-submenu"';
                                                            $aclass = 'dropdown-item';
                                                        } else {
                                                            $class = '';
                                                            $aclass = $bsnav ? 'dropdown-item' : '';
                                                        }
                                                        $type = $m4->type;
                                                        $menu .= "<li $class>";
                                                        $menu .= "<a href='" . Url::$type($m4->value) . "' class=\"{$m4->class} {$aclass} {$itemClass}\">" . $m4->name . '</a>';
                                                        $menu .= '</li>';
                                                    }
                                                }
                                                $menu .= '</ul>';
                                            }
                                            $menu .= '</li>';
                                        }
                                    }
                                    $menu .= '</ul>';
                                }
                                $menu .= '</li>';
                            }
                        }
                        $menu .= '</ul>';
                    }

                    $menu .= '</li>';
                }
            }
            $menu .= '</ul>';
        } else {
            $menu = '';
        }

        return $menu;
    }

    /**
     * Renders the administrative interface for managing menu navigation.
     * Includes order management forms and recursive item building.
     *
     * @param string $menuid The menu group identifier.
     * @param string $class  Optional CSS class for the wrapper.
     * @return string        The generated HTML admin interface.
     */
    public static function getMenuAdmin($menuid, $class = '')
    {
        $menus = self::getMenuRaw($menuid);
        if (count($menus) == 0)
            return '<div class="text-center py-5 text-muted"><i class="bi bi-folder2-open display-4 mb-3 d-block"></i>' . _("This menu is currently empty. Start by adding your first link below.") . '</div>';

        $html = '<form action="" method="post" class="menu-admin-form">';
        $html .= '<div class="list-group list-group-flush border-0 rounded-4 overflow-hidden mb-4 shadow-sm">';
        $html .= self::renderAdminMenuItems($menus, '0', $menuid);
        $html .= '</div>';
        $html .= '<div class="d-flex justify-content-end p-3 bg-light border-top rounded-bottom-4">
                    <input type="hidden" name="token" value="' . TOKEN . '">
                    <button name="changeorder" type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-sort-numeric-down me-2"></i> ' . _('Save Navigation Order') . '
                    </button>
                </div>';
        $html .= '</form>';

        return $html;
    }

    /**
     * Recursively renders menu items for the admin interface list.
     *
     * @param array      $menus    The full array of menu data.
     * @param int|string $parentId The current parent ID being rendered.
     * @param string     $menuid   The menu group ID.
     * @param int        $level    Current recursion depth level.
     * @return string              Generated HTML list items.
     */
    private static function renderAdminMenuItems($menus, $parentId, $menuid, $level = 0)
    {
        $html = '';
        $indent = $level * 30;

        foreach ($menus as $m) {
            if ($m->parent == $parentId) {
                $html .= "
                <div class='list-group-item border-0 py-3 px-4 position-relative hover-bg-light transition-all' style='padding-left: " . ($level == 0 ? 24 : 24 + $indent) . "px !important;'>
                    <div class='d-flex align-items-center'>
                        <div class='me-3 text-muted opacity-50'><i class='bi bi-grip-vertical fs-5'></i></div>
                        <div class='flex-grow-1'>
                            <div class='d-flex align-items-center mb-1'>
                                <span class='fw-bold text-dark h6 mb-0 me-3'>{$m->name}</span>
                                <span class='badge bg-light text-muted border extra-small px-2 py-1 rounded-pill'>{$m->type}</span>
                            </div>
                            <div class='extra-small text-muted opacity-75 text-truncate' style='max-width: 300px;'>{$m->value}</div>
                        </div>
                        <div class='d-flex align-items-center ms-3'>
                            <div class='me-3' style='width: 60px;'>
                                <input type='number' value='{$m->order}' name='order[{$m->id}][order]' class='form-control form-control-sm text-center rounded-pill border-light bg-light'>
                            </div>
                            <div class='btn-group shadow-sm rounded-pill overflow-hidden'>
                                <a href='index.php?page=menus&act=edit&id={$menuid}&itemid={$m->id}&token=" . TOKEN . "' class='btn btn-white btn-sm px-3 border-end' title='Edit'><i class='bi bi-pencil-square text-primary'></i></a>
                                <a href='index.php?page=menus&act=del&id={$menuid}&itemid={$m->id}&token=" . TOKEN . "' class='btn btn-white btn-sm px-3' title='Delete' onclick=\"return confirm('" . _("Delete this menu item?") . "');\"><i class='bi bi-trash text-danger'></i></a>
                            </div>
                        </div>
                    </div>";

                // Recursive call for children
                $html .= self::renderAdminMenuItems($menus, $m->id, $menuid, $level + 1);

                $html .= "</div>";
            }
        }

        return $html;
    }

    /**
     * Retrieves all raw menu data for a specific menu group.
     *
     * @param string $menuid The menu group identifier.
     * @return array        Array of menu objects.
     */
    public static function getMenuRaw($menuid)
    {
        $menuid = Typo::cleanX($menuid);
        $menus = Query::table('menus')->where('menuid', $menuid)->orderBy('order', 'ASC')->get();

        return $menus;
    }

    /**
     * Retrieves a specific menu item by its database ID.
     *
     * @param int|string $id The menu item ID.
     * @return array         Array containing the menu object or empty.
     */
    public static function getId($id = '')
    {
        if (isset($id)) {
            $id = Typo::int($id);
            $menus = Query::table('menus')->where('id', $id)->first();
            $menus = ($menus) ? [$menus] : [];
        } else {
            $menus = [];
        }

        return $menus;
    }

    /**
     * Updates the display order for multiple menu items.
     *
     * @param array $vars Multi-dimensional array mapping item IDs to their order values.
     */
    public static function updateMenuOrder($vars)
    {
        foreach ($vars as $k => $v) {
            $order = Typo::int($v['order']);
            Query::table('menus')->where('id', Typo::int($k))->update(['order' => $order]);
        }
    }

    /**
     * Inserts a new menu item into the database.
     *
     * @param array $vars Key-value pairs matching database columns.
     */
    public static function insert($vars)
    {
        if (is_array($vars)) {
            if (!isset($vars['sub'])) {
                $vars['sub'] = '0';
            }
            Query::table('menus')->insert($vars);
        }
    }

    /**
     * Updates an existing menu item's data.
     *
     * @param array $vars Dictionary containing 'id' and 'key' (data array).
     */
    public static function update($vars)
    {
        if (is_array($vars)) {
            Query::table('menus')->where('id', $vars['id'])->update($vars['key']);
        }
    }

    /**
     * Deletes a menu item from the database.
     *
     * @param int|string $id The menu item ID.
     */
    public static function delete($id)
    {
        $id = Typo::int($id);
        Query::table('menus')->where('id', $id)->delete();
    }
}

/* End of file Menus.class.php */
/* Location: ./inc/lib/Menus.class.php */
