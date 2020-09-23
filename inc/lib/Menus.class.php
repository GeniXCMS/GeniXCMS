<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141007
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
 * Menus Class.
 *
 * This class is for managing the menu at the dasboard.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.1
 */
class Menus
{
    /**
     * Menus Constructor.
     *
     * Nothing to construct
     *
     * @since 0.0.1
     */
    public function __construct()
    {
    }

    /**
     * isHadParent Function.
     *
     * This function is to get the list of parent in certain conditions. There
     * are two paramaters to be inserted.
     *
     * @param int    $parent
     * @param string $menuid
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function isHadParent($parent = '', $menuid = '')
    {
        $parent = Typo::cleanX($parent);
        $menuid = Typo::cleanX($menuid);
        if (isset($menuid)) {
            $where = " AND `menuid` = '{$menuid}'";
        } else {
            $where = '';
        }
        if (isset($parent) && $parent != '') {
            $parent = " `parent` = '{$parent}'";
        } else {
            $parent = '1';
        }
        $sql = sprintf('SELECT * FROM `menus` WHERE %s %s', $parent, $where);
        $menu = Db::result($sql);

        return $menu;
    }

    public static function getParent($id)
    {
        $q = self::getId($id);

        return $q[0]->parent;
    }

    /**
     * Menu for User Frontend.
     *
     * used for frontend interface. 4 level deep submenu.
     *
     * @since 0.0.1pre
     */
    public static function getMenu($menuid, $class = '', $bsnav = false)
    {
        $menus = self::getMenuRaw($menuid);
        $n = Db::$num_rows;
        if ($n > 0) {
            $menu = "<ul class=\"menu-{$menuid} {$class}\">";
            foreach ($menus as $m) {
                
                if ($m->parent == '0') {
                    $parent = self::isHadParent($m->id, $menuid);
                    $n = Db::$num_rows;
                    if ($n > 0 && $bsnav) {
                        $class = 'class="dropdown"';
                        $aclass = 'dropdown-toggle" data-toggle="dropdown';
                    } else {
                        $class = '';
                        $aclass = '';
                    }
                    $type = $m->type;
                    $menu .= "<li $class>";
                    $menu .= "<a href='".Url::$type($m->value)."' class=\"{$m->class} {$aclass}\">".$m->name.'</a>';
                    $parent = $m->id;
//                    echo $parent;

                    if ($n > 0) {
                        $class = 'dropdown-menu';
                        $menu .= "<ul class=\" {$class}\" role=\"dropdown\">";
                        foreach ($menus as $m2) {
                            if ($m2->parent == $m->id) {
                                $parent = self::isHadParent($m2->id, $menuid);
                                $n = Db::$num_rows;
                                if ($n > 0 && $bsnav) {
                                    $class = 'class="dropdown-submenu"';
                                    $aclass = 'dropdown-toggle" data-toggle="dropdown';
                                } else {
                                    $class = '';
                                    $aclass = '';
                                }
                                $type = $m2->type;
//                                print_r($m2);
                                $menu .= "<li $class>";
                                $menu .= "<a href='".Url::$type($m2->value)."' class=\"{$m2->class} {$aclass}\">".$m2->name.'</a>';

                                if ($n > 0) {
                                    $class = 'dropdown-menu';
                                    $menu .= "<ul class=\" {$class}\">";
                                    foreach ($menus as $m3) {
                                        if ($m3->parent == $m2->id) {
                                            $parent = self::isHadParent($m3->id, $menuid);
                                            $n = Db::$num_rows;
                                            if ($n > 0 && $bsnav) {
                                                $class = 'class="dropdown-submenu"';
                                                $aclass = 'dropdown-toggle" data-toggle="dropdown';
                                            } else {
                                                $class = '';
                                                $aclass = '';
                                            }
                                            $type = $m3->type;
                                            $menu .= "<li $class>";
                                                    //$menu .= "<li>";
                                                    $menu .= "<a href='".Url::$type($m3->value)."' class=\"{$m3->class} {$aclass}\">".$m3->name.'</a>';

                                            if ($n > 0) {
                                                $class = 'dropdown-menu';
                                                $menu .= "<ul class=\" {$class}\">";
                                                foreach ($menus as $m4) {
                                                    if ($m4->parent == $m3->id) {
                                                        $parent = self::isHadParent($m4->id, $menuid);
                                                        $n = Db::$num_rows;
                                                        if ($n > 0 && $bsnav) {
                                                            $class = 'class="dropdown-submenu"';
                                                            $aclass = 'dropdown-toggle" data-toggle="dropdown';
                                                        } else {
                                                            $class = '';
                                                            $aclass = '';
                                                        }
                                                        $type = $m4->type;
                                                        $menu .= "<li $class>";
                                                        $menu .= "<a href='".Url::$type($m4->value)."' class=\"{$m4->class} {$aclass}\">".$m4->name.'</a>';
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
     * Menu for Admin Backeend.
     *
     * so this won't make general menu messed up.
     *
     * @since 0.0.1-pre
     */
    public static function getMenuAdmin($menuid, $class = '')
    {
        $menus = self::getMenuRaw($menuid);
        $n = Db::$num_rows;
        if ($n > 0) {
            $menu = "<form action=\"\" method=\"post\"><ul class=\"menu-{$menuid} {$class} \">";
            foreach ($menus as $m) {
                
                if ($m->parent == '0') {
                    $menu .= '<li clas="form-inline"><div class="row">';
                    $menu .= '
                            <h4 class="col-md-10">'.$m->name." 
                                <a href=\"index.php?page=menus&act=edit&id={$menuid}&itemid={$m->id}&token=".TOKEN."\" class=\"label label-primary pull-right\" >
                                    <span class=\"glyphicon glyphicon-edit\"></span>
                                </a>
                                <a href=\"index.php?page=menus&act=del&id={$menuid}&itemid={$m->id}&token=".TOKEN."\" class=\"label label-danger pull-right\" >
                                    <span class=\"glyphicon glyphicon-remove\"></span>
                                </a>
                            </h4>
                            <div class=\"pull-right col-md-2\">
                                    <input type=\"text\" value=\"$m->order\" name=\"order[$m->id][order]\" class=\"form-control text-center\">
                                    
                            </div>
                        </div>
                        ";

                    $parent = $m->id;
                    //echo $parent;
                    $parent = self::isHadParent($m->id, $menuid);
                    $n = Db::$num_rows;
                    if ($n > 0) {
                        $menu .= "<ul class=\"submenu {$class}\">";
                        foreach ($menus as $m2) {
                            if ($m2->parent == $m->id) {
                                $menu .= '<li><div class="row">';
                                $menu .= '<h5 class="col-md-10">'.$m2->name."
                                                <a href=\"index.php?page=menus&act=edit&id={$menuid}&itemid={$m2->id}&token=".TOKEN."\" class=\"label label-primary pull-right\" >
                                                    <span class=\"glyphicon glyphicon-edit\"></span>
                                                </a>
                                                <a href=\"index.php?page=menus&act=del&id={$menuid}&itemid={$m2->id}&token=".TOKEN.'" class="label label-danger pull-right" >
                                                    <span class="glyphicon glyphicon-remove"></span>
                                                </a>

                                            </h5>';
                                $menu .= "
                                            <div class=\"pull-right col-md-2\">
                                                    <input type=\"text\" value=\"$m2->order\" name=\"order[$m2->id][order]\" class=\"form-control text-center\">

                                            </div>
                                        </div>
                                        
                                                ";
                                $parent = self::isHadParent($m2->id, $menuid);
                                $n = Db::$num_rows;
                                if ($n > 0) {
                                    $menu .= "<ul class=\"submenu {$class}\">";
                                    foreach ($menus as $m3) {
                                        if ($m3->parent == $m2->id) {
                                            $menu .= '<li><div class="row">';
                                            $menu .= '<h6 class="col-md-10">'.$m3->name."
                                                                <a href=\"index.php?page=menus&act=edit&id={$menuid}&itemid={$m3->id}&token=".TOKEN."\" class=\"label label-primary pull-right\" >
                                                                    <span class=\"glyphicon glyphicon-edit\"></span>
                                                                </a>
                                                                <a href=\"index.php?page=menus&act=del&id={$menuid}&itemid={$m3->id}&token=".TOKEN.'" class="label label-danger pull-right" >
                                                                    <span class="glyphicon glyphicon-remove"></span>
                                                                </a>
                                                            </h6>';
                                            $menu .= "
                                                            <div class=\"pull-right col-md-2\">
                                                                    <input type=\"text\" value=\"$m3->order\" name=\"order[$m3->id][order]\" class=\"form-control text-center\">
                                                            </div>
                                                        </div>
                                                                ";
                                            $parent = self::isHadParent($m3->id, $menuid);
                                            $n = Db::$num_rows;
                                            if ($n > 0) {
                                                $menu .= "<ul class=\"submenu {$class}\">";
                                                foreach ($menus as $m4) {
                                                    if ($m4->parent == $m3->id) {
                                                        $menu .= '<li><div class="row">';
                                                        $menu .= '<h6 class="col-md-10">'.$m4->name."
                                                                                <a href=\"index.php?page=menus&act=edit&id={$menuid}&itemid={$m4->id}&token=".TOKEN."\" class=\"label label-primary pull-right\" >
                                                                                    <span class=\"glyphicon glyphicon-edit\"></span>
                                                                                </a>
                                                                                <a href=\"index.php?page=menus&act=del&id={$menuid}&itemid={$m4->id}&token=".TOKEN.'" class="label label-primary pull-right" >
                                                                                    <span class="glyphicon glyphicon-remove"></span>
                                                                                </a>
                                                                            </h6>';
                                                        $menu .= "
                                                                        <div class=\"pull-right col-md-2\">
                                                                                <input type=\"text\" value=\"$m4->order\" name=\"order[$m4->id][order]\" class=\"form-control text-center\">
                                                                        </div>
                                                                    </div>
                                                                            ";
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
            $menu .= '</ul>
                    <div class="row">
                        <div class="col-md-2 pull-right">
                            <input type="hidden" name="token" value="'.TOKEN.'">
                            <button name="changeorder" type="submit" class="btn btn-warning pull-right">
                                Change Order
                            </button>
                        </div>
                    </div>
            </form>';
        } else {
            $menu = '';
        }

        return $menu;
    }

    public static function getMenuRaw($menuid)
    {
        $menuid = Typo::cleanX($menuid);
        $sql = sprintf("SELECT * FROM `menus` WHERE `menuid` = '%s' ORDER BY `order` ASC", $menuid);
        $menus = Db::result($sql);
        $n = Db::$num_rows;

        return $menus;
    }

    public static function getId($id = '')
    {
        if (isset($id)) {
            Typo::int($id);
            $sql = sprintf("SELECT * FROM `menus` WHERE `id` = '%d'", $id);
            $menus = Db::result($sql);
            $n = Db::$num_rows;
        } else {
            $menus = '';
        }

        return $menus;
    }

    public static function updateMenuOrder($vars)
    {
        foreach ($vars as $k => $v) {
            
            $v['order'] = Typo::int($v['order']);
            $sql = array(
                        'table' => 'menus',
                        'id' => Typo::int($k),
                        'key' => $v,
                    );
            Db::update($sql);
        }
    }

    /*
    *    $vars = array(
    *                    'parent' => $_POST['parent'],
    *                    'menuid' => $_POST['id'],
    *                    'name' => $_POST['name'],
    *                    'class' => $_POST['class'],
    *                    'type' => $_POST['type'],
    *                    'value' => $_POST['value']
    *                );
    */
    public static function insert($vars)
    {
        if (is_array($vars)) {
            $sql = array(
                        'table' => 'menus',
                        'key' => $vars,
                    );
            $menu = Db::insert($sql);
        }
    }

    public static function update($vars)
    {
        if (is_array($vars)) {
            $sql = array(
                        'table' => 'menus',
                        'id' => $vars['id'],
                        'key' => $vars['key']
                    );
            $menu = Db::update($sql);
        }
    }

    public static function delete($id)
    {
        $id = Typo::int($id);
        $sql = array(
                    'table' => 'menus',
                    'where' => array(
                                    'id' => $id
                                ),
                );
        $menu = Db::delete($sql);
    }
}

/* End of file Menus.class.php */
/* Location: ./inc/lib/Menus.class.php */
