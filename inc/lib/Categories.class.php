<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140930
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Categories Class.
 *
 * This class will process the categories function. Including Create, Edit,
 * Delete the categories.
 *
 * @since 0.0.1
 */
class Categories
{
    /**
     * Categories Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Renders an HTML dropdown select for categories.
     * Supports nested categories (one level deep) and various filtering options.
     *
     * @param array $vars {
     *     Configuration array.
     *     @type string $name     Select element name.
     *     @type int    $parent   Parent category ID filter.
     *     @type string $type     Category type filter (default: not 'tag').
     *     @type string $order_by Database column for sorting.
     *     @type string $sort     Sort direction (ASC/DESC).
     *     @type string $class    CSS class for the select element.
     *     @type string $id       CSS ID for the select element.
     *     @type int    $selected Initially selected category ID.
     * }
     * @return string The rendered HTML select element.
     * @since 0.0.1
     */
    public static function dropdown($vars)
    {
        if (is_array($vars)) {
            $q = Query::table('cat');
            if (isset($vars['parent'])) {
                $q->where('parent', $vars['parent']);
            }
            if (isset($vars['type'])) {
                $q->where('type', $vars['type']);
            } else {
                $q->where('type', '!=', 'tag');
            }
            $cat = $q->orderBy($vars['order_by'] ?? 'name', $vars['sort'] ?? 'ASC')->get();
            $name = Typo::cleanX($vars['name'] ?? 'cat');
            $class = isset($vars['class']) ? $vars['class'] : 'form-control';
            $id = isset($vars['id']) ? "id=\"{$vars['id']}\"" : "";
            $attr = isset($vars['attr']) ? $vars['attr'] : "";
            $drop = "<select name=\"{$name}\" {$id} class=\"{$class}\" {$attr}><option value=\"0\">None</option>";
            if (!empty($cat) && is_array($cat)) {
                foreach ($cat as $c) {
                    if (is_object($c) && ($c->parent == null || $c->parent == '0')) {
                        $sel = (isset($vars['selected']) && $c->id == $vars['selected']) ? 'selected' : '';
                        $drop .= "<option value=\"{$c->id}\" $sel style=\"padding-left: 10px;\">{$c->name}</option>";
                        foreach ($cat as $c2) {
                            if (is_object($c2) && $c2->parent == $c->id) {
                                $sel2 = (isset($vars['selected']) && $c2->id == $vars['selected']) ? 'selected' : '';
                                $drop .= "<option value=\"{$c2->id}\" $sel2 style=\"padding-left: 10px;\">
                                    &nbsp;&nbsp;&nbsp;{$c2->name}</option>";
                            }
                        }
                    }
                }
            }
            $drop .= '</select>';
        } else {
            $drop = _('Category config not in Array');
        }


        return $drop;
    }

    /**
     * Renders a Bootstrap accordion list of categories.
     *
     * @param array $vars Configuration array (parent, type, order_by, sort).
     * @return string The rendered HTML accordion.
     */
    public static function lists($vars)
    {
        if (is_array($vars)) {
            //print_r($vars);

            $where = 'WHERE 1';
            if (isset($vars['parent'])) {
                $where .= " AND `parent` = '" . Typo::int($vars['parent']) . "' ";
            } else {
                $where .= '';
            }
            if (isset($vars['type'])) {
                $type = Typo::cleanX($vars['type']);
                if ($type == 'tag') {
                    $where .= " AND `type` = '" . $type . "' ";
                } else {
                    $where .= " AND `type` = '" . $type . "' AND `type` != 'tag' ";
                }

            } else {
                $where .= " AND `type` != 'tag' ";
            }

            $order_by = ' ORDER BY ';
            if (isset($vars['order_by'])) {
                $order_by .= " {$vars['order_by']} ";
            } else {
                $order_by .= ' `name` ';
            }
            if (isset($vars['sort'])) {
                $sort = " {$vars['sort']}";
            } else {
                $sort = ' ASC';
            }
        }
        $q = Query::table('cat');
        if (isset($vars['parent'])) {
            $q->where('parent', $vars['parent']);
        }
        if (isset($vars['type'])) {
            $q->where('type', $vars['type']);
        } else {
            $q->where('type', '!=', 'tag');
        }
        $cat = $q->orderBy($vars['order_by'] ?? 'name', $vars['sort'] ?? 'ASC')->get();
        //print_r($cat);
        $html = '<div class="panel-group" id="accordion" role="tablist">
            ';
        if (!empty($cat) && is_array($cat)) {
            foreach ($cat as $c) {
                if (is_object($c) && ($c->parent == null || $c->parent == '0')) {
                    //if (isset($vars['selected']) && $c->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                    if (isset($_GET['cat'])) {
                        $catparent = self::getParent($_GET['cat']);
                        $in = ($catparent[0]->parent === $c->id) ? 'in' : '';
                        $collapseHeading = ($catparent[0]->parent === $c->id) ? "collapseListGroupHeading{$c->id}" : '';
                        $href = ($catparent[0]->parent === $c->id) ? "#collapse-{$c->id}" : Url::cat($c->id);
                        $data_toggle = ($catparent[0]->parent === $c->id) ? 'collapse' : '';
                    } else {
                        $catparent = '';
                        $in = '';
                        $collapseHeading = '';
                        $href = ($catparent == $c->id) ? "#collapse-{$c->id}" : Url::cat($c->id);
                        $data_toggle = '';
                    }
                    // print_r($catparent);

                    $html .= "<div class=\"panel panel-default\">
                    <div id=\"{$collapseHeading}\" class=\"panel-heading\" role=\"tab\" >
                    <a href=\"{$href}\" data-bs-toggle=\"{$data_toggle}\"  aria-expanded=\"false\"
                    aria-controls=\"collapse-{$c->id}\" class=\"collapsed\" data-parent=\"#accordion\"><strong>{$c->name}</strong></a>
                    </div>
                    <div class=\"panel-collapse collapse {$in}\" role=\"tabpanel\" id=\"collapse-{$c->id}\" aria-labelledby=\"collapseListGroupHeading{$c->id}\">
                    <ul class=\"nav nav-pills nav-stacked \" >";
                    foreach ($cat as $c2) {
                        if (is_object($c2) && $c2->parent == $c->id) {
                            //if (isset($vars['selected']) && $c2->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                            $html .= '<li><a href="' . Url::cat($c2->id) . "\">{$c2->name}</a></li>";
                        }
                    }
                    $html .= '</ul></div></div>';
                }
            }
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Retrieves the name of a category by its ID.
     *
     * @param int|string $id Category ID.
     * @return string Category name or error message.
     * @since 0.0.1
     */
    public static function name($id)
    {
        if (is_array($id)) {
            error_log("DEBUG: Categories::name received an ARRAY: " . json_encode($id));
            return _('Multiple Categories');
        }
        if (isset($id)) {
            $cat = Query::table('cat')->where('id', $id)->first();
            //print_r($cat);
            if (!$cat) {
                return '';
            } else {
                return $cat->name;
            }
        } else {
            return _('No ID Selected');
        }

        //print_r($cat);
    }

    /**
     * Retrieves the parent ID data for a specified category.
     *
     * @param int|string $id Category ID.
     * @return array Array containing the parent ID object.
     * @since 0.0.1
     */
    public static function getParent($id = '')
    {
        $id = sprintf('%d', $id);
        $sql = 'SELECT ' . Db::quoteIdentifier('parent') . ' FROM ' . Db::quoteIdentifier('cat') . ' WHERE ' . Db::quoteIdentifier('id') . ' = ?';
        $cat = Db::result($sql, [$id]);

        return $cat;
    }

    /**
     * Deletes a category and re-assigns its posts to the parent category (or root).
     *
     * @param int|string $id Category ID to delete.
     * @return bool True if deleted successfully.
     * @since 0.0.1
     */
    public static function delete($id)
    {
        $id = sprintf('%d', $id);
        $parent = self::getParent($id);

        $cat = Query::table('cat')->where('id', $id)->delete();
        if ($cat) {
            // check all posts with this category and move to parent categories
            $postCount = Query::table('posts')->where('cat', $id)->count();

            if ($postCount > 0) {
                Query::table('posts')->where('cat', $id)->update(['cat' => $parent[0]->parent ?? 0]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieves the type of a category by its ID.
     *
     * @param int|string $id Category ID.
     * @return string Category type.
     */
    public static function type($id)
    {
        $id = sprintf('%d', $id);
        if (isset($id)) {
            $cat = Db::result('SELECT ' . Db::quoteIdentifier('type') . ' FROM ' . Db::quoteIdentifier('cat') . ' WHERE ' . Db::quoteIdentifier('id') . ' = ? LIMIT 1', [$id]);
            //print_r($cat);
            if (isset($cat['error'])) {
                return '';
            } else {
                return $cat[0]->type;
            }
        } else {
            return _('No ID Selected');
        }
    }

    /**
     * Retrieves the ID of a category by its name or slug.
     *
     * @param string $name Category name or slug.
     * @return int|string Category ID.
     */
    public static function id($name)
    {
        $name = sprintf('%s', $name);
        if (isset($name)) {
            $cat = Db::result('SELECT ' . Db::quoteIdentifier('id') . ' FROM ' . Db::quoteIdentifier('cat') . ' WHERE ' . Db::quoteIdentifier('name') . ' = ? OR ' . Db::quoteIdentifier('slug') . ' = ? LIMIT 1', [$name, $name]);
            // print_r($cat);
            if (isset($cat['error'])) {
                return '';
            } else {
                return $cat[0]->id;
            }
        } else {
            return _('No Name Selected');
        }
    }

    /**
     * Checks if a category exists by its ID.
     *
     * @param int|string $cat Category ID.
     * @return bool True if exists.
     */
    public static function exist($cat)
    {
        $id = Typo::int($cat);
        $q = Query::table('cat')->where('id', $id)->first();

        return ($q) ? true : false;
    }

    /**
     * Retrieves the slug of a category by its ID.
     *
     * @param int|string $id Category ID.
     * @return string Category slug.
     */
    public static function slug($id)
    {
        if (isset($id)) {
            $cat = Query::table('cat')->where('id', $id)->first();
            if (!$cat) {
                return '';
            } else {
                return $cat->slug;
            }
        } else {
            return _('No ID Selected');
        }
    }
}

/* End of file Categories.class.php */
/* Location: ./inc/lib/Categories.class.php */
