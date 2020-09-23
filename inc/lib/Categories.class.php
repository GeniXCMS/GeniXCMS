<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140930
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Categories Class.
 *
 * This class will process the categories function. Including Create, Edit,
 * Delete the categories.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.1
 */
class Categories
{
    public function __construct()
    {
    }

    /**
     * Categories Dropdown Function.
     *
     * This will list the categories into the HTML Dropdown Below are how to use
     * it :
     * <code>
     *     $vars = array(
     *         'name' => 'catname',
     *         'parent' => 'parent',
     *         'order_by' => '',
     *         'sort' => 'ASC',
     *         'type' => ''
     *      );
     *      Categories::dropdown($vars);
     * </code>
     *
     * @param array $vars the delivered data must be in array with above format
     *
     * @uses Db::result();
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function dropdown($vars)
    {
        if (is_array($vars)) {
            //print_r($vars);
            $name = Typo::cleanX($vars['name']);
            $where = 'WHERE 1 ';
            if (isset($vars['parent'])) {
                $where .= " AND `parent` = '".Typo::int($vars['parent'])."' ";
            } else {
                $where .= '';
            }
            if (isset($vars['type'])) {
                $type = Typo::cleanX($vars['type']);
                if ($type == 'tag') {
                    $where .= " AND `type` = '".$type."' ";
                } else {
                    $where .= " AND `type` = '".$type."' AND `type` != 'tag' ";
                }

            } else {
                $where .= " AND `type` != 'tag' ";
            }
            $where .= ' ';
            $order_by = 'ORDER BY ';
            if (isset($vars['order_by'])) {
                $order_by .= ' '.Typo::cleanX($vars['order_by']).' ';
            } else {
                $order_by .= ' `name` ';
            }
            if (isset($vars['sort'])) {
                $sort = " ".Typo::cleanX($vars['sort'])." ";
            } else {
                $sort = ' ASC';
            }

            // $cat = Db::result("SELECT * FROM `cat` {$where} {$order_by} {$sort}");
            $cat = Db::result('SELECT * FROM `cat` '.$where.' '.$order_by.' '.$sort);
            // print_r($cat);
            $drop = "<select name=\"{$name}\" class=\"form-control\"><option></option>";
            if (Db::$num_rows > 0) {
                foreach ($cat as $c) {
                    if ($c->parent == null || $c->parent == '0') {
                        if (isset($vars['selected']) && $c->id == $vars['selected']) {
                            $sel = 'SELECTED';
                        } else {
                            $sel = '';
                        }
                        $drop .= "<option value=\"{$c->id}\" $sel style=\"padding-left: 10px;\">{$c->name}</option>";
                        foreach ($cat as $c2) {
                            if ($c2->parent == $c->id) {
                                if (isset($vars['selected']) && $c2->id == $vars['selected']) {
                                    $sel = 'SELECTED';
                                } else {
                                    $sel = '';
                                }
                                $drop .= "<option value=\"{$c2->id}\" $sel style=\"padding-left: 10px;\">
                                    &nbsp;&nbsp;&nbsp;{$c2->name}</option>";
                            }
                        }
                    }
                }
            }
            $drop .= '</select>';
        } else {
            $drop = 'Category config not in Array';
        }


        return $drop;
    }

    public static function lists($vars)
    {
        if (is_array($vars)) {
            //print_r($vars);

            $where = 'WHERE 1';
            if (isset($vars['parent'])) {
                $where .= " AND `parent` = '".Typo::int($vars['parent'])."' ";
            } else {
                $where .= '';
            }
            if (isset($vars['type'])) {
                $type = Typo::cleanX($vars['type']);
                if ($type == 'tag') {
                    $where .= " AND `type` = '".$type."' ";
                } else {
                    $where .= " AND `type` = '".$type."' AND `type` != 'tag' ";
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
        $cat = Db::result("SELECT * FROM `cat` {$where} {$order_by} {$sort}");
        //print_r($cat);
        $html = '<div class="panel-group" id="accordion" role="tablist">
            ';
        if (Db::$num_rows > 0) {
            foreach ($cat as $c) {
                if ($c->parent == null || $c->parent == '0') {
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
                    <a href=\"{$href}\" data-toggle=\"{$data_toggle}\"  aria-expanded=\"false\"
                    aria-controls=\"collapse-{$c->id}\" class=\"collapsed\" data-parent=\"#accordion\"><strong>{$c->name}</strong></a>
                    </div>
                    <div class=\"panel-collapse collapse {$in}\" role=\"tabpanel\" id=\"collapse-{$c->id}\" aria-labelledby=\"collapseListGroupHeading{$c->id}\">
                    <ul class=\"nav nav-pills nav-stacked \" >";
                    foreach ($cat as $c2) {
                        if ($c2->parent == $c->id) {
                            //if (isset($vars['selected']) && $c2->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                            $html .= '<li><a href="'.Url::cat($c2->id)."\">{$c2->name}</a></li>";
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
     * Category Name function.
     *
     * This will get the specified ID category name
     *
     * @param int $id
     *
     * @uses Db::result();
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function name($id)
    {
        $id = sprintf('%d', $id);
        if (isset($id)) {
            $cat = Db::result("SELECT `name` FROM `cat`
                                WHERE `id` = '{$id}' LIMIT 1");
            //print_r($cat);
            if (isset($cat['error'])) {
                return '';
            } else {
                return $cat[0]->name;
            }
        } else {
            echo 'No ID Selected';
        }

        //print_r($cat);
    }

    /**
     * Category Get Parent function.
     *
     * This will get the specified ID category parent data
     *
     * @param int $id
     *
     * @uses Db::result();
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function getParent($id = '')
    {
        $id = sprintf('%d', $id);
        $sql = sprintf("SELECT `parent` FROM `cat`
                        WHERE `id` = '%d'", $id);
        $cat = Db::result($sql);

        return $cat;
    }

    /**
     * Category Delete function.
     *
     * This will delete the specified ID category data
     *
     * @param int   $id
     * @param array $sql
     *
     * @uses self::getParent();
     * @uses Db::delete();
     * @uses Db::result();
     * @uses Db::$num_rows;
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function delete($id)
    {
        $id = sprintf('%d', $id);
        $parent = self::getParent($id);

        $sql = array(
                    'table' => 'cat',
                    'where' => array(
                                    'id' => $id,
                                ),
                );
        $cat = Db::delete($sql);
        if ($cat) {
            return true;
        } else {
            return false;
        }
        // check all posts with this category and move to parent categories
        $post = Db::result("SELECT `id` FROM `posts`
                            WHERE `cat` = '{$id}'");
        $npost = Db::$num_rows;

        //print_r($parent);
        if ($npost > 0) {
            $sql = "UPDATE `posts`
                    SET `cat` = '{$parent[0]->parent}'
                    WHERE `cat` = '{$id}'";
            Db::query($sql);
        }
    }

    public static function type($id)
    {
        $id = sprintf('%d', $id);
        if (isset($id)) {
            $cat = Db::result("SELECT `type` FROM `cat`
                                WHERE `id` = '{$id}' LIMIT 1");
            //print_r($cat);
            if (isset($cat['error'])) {
                return '';
            } else {
                return $cat[0]->type;
            }
        } else {
            echo 'No ID Selected';
        }
    }

    public static function id($name)
    {
        $name = sprintf('%s', $name);
        if (isset($name)) {
            $cat = Db::result("SELECT `id` FROM `cat`
                                WHERE `name` = '{$name}'
                                OR `slug` = '{$name}' LIMIT 1");
            // print_r($cat);
            if (isset($cat['error'])) {
                return '';
            } else {
                return $cat[0]->id;
            }
        } else {
            echo 'No Name Selected';
        }
    }

    public static function exist($cat)
    {
        $cat = Typo::int($cat);
        $sql = "SELECT `id` FROM `cat` WHERE `id` = '{$cat}'";
        $q = Db::result($sql);
        // echo Db::$num_rows;
        if (Db::$num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function slug($id)
    {
        $id = sprintf('%d', $id);
        if (isset($id)) {
            $cat = Db::result("SELECT `slug` FROM `cat`
                                WHERE `id` = '{$id}' LIMIT 1");
            //print_r($cat);
            if (isset($cat['error'])) {
                return '';
            } else {
                return $cat[0]->slug;
            }
        } else {
            echo 'No ID Selected';
        }

        //print_r($cat);
    }
}

/* End of file Categories.class.php */
/* Location: ./inc/lib/Categories.class.php */
