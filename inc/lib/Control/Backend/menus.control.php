<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
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

if (User::access(0)) {
    $data['sitetitle'] = MENUS;
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }
    switch ($act) {
        case 'add':
            if (isset($_POST['submit'])) {
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (!isset($_POST['id']) || $_POST['id'] == '') {
                        $alertDanger[] = MENUID_CANNOT_EMPTY;
                    }
                    if (!isset($_POST['name']) || $_POST['name'] == '') {
                        $alertDanger[] = MENUNAME_CANNOT_EMPTY;
                    }

                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $menus = Options::v('menus');
                        $menus = json_decode($menus, true);
                       //echo "<pre>"; print_r($menus); echo "</pre>";
                        // $menu = array(
                        //                 $_POST['id']  =>  array(
                        //                             'name' => $menus[$_POST['id']]['name'],
                        //                             'class' => $menus[$_POST['id']]['class'],
                        //                             'menu' => array(
                        //                                     'parent' => $_POST['parent'],
                        //                                     'menuid' => $_POST['id'],
                        //                                     'type' => $_POST['type'],
                        //                                     'value' => $_POST[$_POST['type']]
                        //                                 )
                        //                         )
                        //                 );

                        // if (is_array($menus)) {
                        //     $menu = array_merge($menus, $menu);
                        // }
                        // echo "<pre>"; print_r($menu); echo "</pre>";
                        //$menu = $menus;
                        $parent = Typo::int(Typo::filterXss($_POST['parent']));
                        $menuid = Typo::cleanX(Typo::filterXss($_POST['id']));
                        $name = Typo::cleanX(Typo::filterXss($_POST['name']));
                        $type = Typo::cleanX(Typo::filterXss($_POST['type']));
                        $class = Typo::cleanX(Typo::filterXss($_POST['class']));
                        $menu[$menuid]['menu'] = $menus[$menuid]['menu'];
                        $menu[$menuid]['menu'][] = array(
                                                    'parent' => $parent,
                                                    'menuid' => $menuid,
                                                    'name' => $name,
                                                    'type' => $type,
                                                    'value' => Typo::cleanX($_POST[$type]),
                                                    'sub' => '',
                                                );
                        $menu = array(
                                $menuid => array(
                                            'name' => $menus[$menuid]['name'],
                                            'class' => $menus[$menuid]['class'],
                                            'menu' => $menu[$menuid]['menu'],
                                        ),
                                );
                        if (is_array($menus)) {
                            $menu = array_merge($menus, $menu);
                        }
                        //echo "<pre>"; print_r($menu); echo "</pre>";
                        $menu = json_encode($menu);
                        //echo "<pre>"; print_r($menu); echo "</pre>";
                        //Options::update('menus', $menu);

                        $vars = array(
                            'parent' => $parent,
                            'menuid' => $menuid,
                            'name' => $name,
                            'class' => $class,
                            'type' => $type,
                            'value' => $_POST[$type],
                        );
                        Menus::insert($vars);
                        $data['alertSuccess'][] = 'Menu Added';
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($_POST['token']);
                    }
                    break;

                default:
                    break;
            }
                //$data['abc'] = "abc";
            if (isset($_GET['id'])) {
                $menuid = Typo::cleanX(Typo::filterXss($_POST['id']));
            } else {
                $menuid = '';
            }
                $data['parent'] = Menus::isHadParent('', $menuid);
                //echo "<pre>"; print_r($data); echo "</pre>";
                Theme::admin('header', $data);
                System::inc('menus_form', $data);
                Theme::admin('footer');
            break;

        case 'edit':
            if (isset($_POST['edititem'])) {
                $submit = true;
            } else {
                $submit = false;
            }
            $itemid = Typo::int($_GET['itemid']);
            switch ($submit) {
                case true:

                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $parent = Typo::int(Typo::filterXss($_POST['parent']));
                        $menuid = Typo::cleanX(Typo::filterXss($_POST['id']));
                        $name = Typo::cleanX(Typo::filterXss($_POST['name']));
                        $type = Typo::cleanX(Typo::filterXss($_POST['type']));
                        $class = Typo::cleanX(Typo::filterXss($_POST['class']));
                        $vars = array(
                            'parent' => $parent,
                            'menuid' => $menuid,
                            'name' => $name,
                            'class' => $class,
                            'type' => $type,
                            'value' => $_POST[$type],
                        );
                        $vars = array(
                            'id' => $itemid,
                            'key' => $vars,
                        );
                        Menus::update($vars);
                        $data['alertSuccess'][] = 'Menu Updated';
                        Token::remove($token);
                    }

                    break;

                default:
                    break;
            }

            if (isset($_GET['id'])) {
                $menuid = Typo::cleanX(Typo::filterXss($_GET['id']));
            } else {
                $menuid = '';
            }
                $data['menus'] = Menus::getId($itemid);
                $data['parent'] = Menus::isHadParent('', $menuid);
                Theme::admin('header', $data);
                System::inc('menus_form_edit', $data);
                Theme::admin('footer');
            break;
        case 'del':
            if (isset($_POST['additem'])) {
                $token = Typo::cleanX($_POST['token']);
                if (!isset($_POST['token']) || !Token::validate($token)) {
                    // VALIDATE ALL
                    $alertDanger[] = TOKEN_NOT_EXIST;
                }
                if (!isset($_POST['name']) || $_POST['name'] == '') {
                    $alertDanger[] = MENU_NAME_CANNOT_EMPTY;
                }
                if (!isset($_POST['type']) || $_POST['type'] == '') {
                    $alertDanger[] = MENU_TYPE_CANNOT_EMPTY;
                }
                if (isset($alertDanger)) {
                    $data['alertDanger'] = $alertDanger;
                } else {
                    $vars = array(
                            'parent' => Typo::int($_POST['parent']),
                            'menuid' => Typo::strip($_POST['id']),
                            'name' => Typo::cleanX($_POST['name']),
                            'class' => Typo::cleanX($_POST['class']),
                            'type' => Typo::strip($_POST['type']),
                            'value' => Typo::cleanX($_POST[$_POST['type']]),
                        );
                    Menus::insert($vars);
                    $data['alertSuccess'][] = 'Menu Item Added';
                    Token::remove($token);
                }
            } else {
                if (isset($_GET['itemid']) && !isset($_POST['additem'])) {
                    $token = Typo::cleanX($_GET['token']);
                    $itemid = Typo::int($_GET['itemid']);
                    if (!isset($_GET['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        Menus::delete($itemid);
                        $data['alertSuccess'][] = 'Menu Deleted';
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($token);
                    }
                } else {
                    $data['alertDanger'][] = 'No ID Selected.';
                }
            }
                $data['menus'] = Options::get('menus');
                Theme::admin('header', $data);
                System::inc('menus', $data);
                Theme::admin('footer');
            break;

        case 'remove':
            if (isset($_GET['menuid'])) {
                $token = Typo::cleanX($_GET['token']);
                if (!isset($_GET['token']) || !Token::validate($token)) {
                    // VALIDATE ALL
                    $alertDanger[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertDanger)) {
                    $data['alertDanger'] = $alertDanger;
                } else {
                    $menus = json_decode(Options::v('menus'), true);
                    unset($menus[$_GET['menuid']]);
                    $menuid = Typo::cleanX($_GET['menuid']);
                    $sql = sprintf("DELETE FROM `menus` WHERE `menuid` = '%s' ", $menuid);
                    Db::query($sql);
                    $menu = json_encode($menus);
                    Options::update('menus', $menu);
                    new Options();
                    $data['alertSuccess'][] = 'Menu Deleted';
                }
                if (isset($_GET['token'])) {
                    Token::remove($token);
                }
            } else {
                $data['alertDanger'][] = 'No ID Selected.';
            }
                $data['menus'] = Options::get('menus');
                Theme::admin('header', $data);
                System::inc('menus', $data);
                Theme::admin('footer');
            break;

        default:
            if (isset($_POST['submit'])) {
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (!isset($_POST['id']) || $_POST['id'] == '') {
                        $alertDanger[] = MENUID_CANNOT_EMPTY;
                    }
                    if (!isset($_POST['name']) || $_POST['name'] == '') {
                        $alertDanger[] = MENUNAME_CANNOT_EMPTY;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {

                        $menuid = Typo::jsonFormat(Typo::strip($_POST['id']));
                        $name = Typo::jsonFormat(Typo::strip($_POST['name']));
                        $class = Typo::jsonFormat(Typo::strip($_POST['class']));
                        $menu = array(
                                $menuid => array(
                                            'name' => $name,
                                            'class' => $class,
                                            'menu' => array(),
                                        ),
                                );
                        $menus = json_decode(Options::v('menus'), true);
                        if (is_array($menus)) {
                            $menu = array_merge($menus, $menu);
                        }

                        $menu = json_encode($menu);
                        // echo $menu;
                        Options::update('menus', $menu);
                        new Options();
                        $data['alertSuccess'][] = 'Menu Added';
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($token);
                    }
                    break;

                default:
                    break;
            }

                // ADD MENU ITEM START
            if (isset($_POST['additem'])) {
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (!isset($_POST['name']) || $_POST['name'] == '') {
                        $alertDanger[] = MENU_NAME_CANNOT_EMPTY;
                    }
                    if (!isset($_POST['type']) || $_POST['type'] == '') {
                        $alertDanger[] = MENU_TYPE_CANNOT_EMPTY;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $vars = array(
                            'parent' => Typo::int($_POST['parent']),
                            'menuid' => Typo::strip($_POST['id']),
                            'name' => Typo::cleanX($_POST['name']),
                            'class' => Typo::cleanX($_POST['class']),
                            'type' => Typo::strip($_POST['type']),
                            'value' => Typo::cleanX($_POST[$_POST['type']]),
                        );
                        Menus::insert($vars);
                        $data['alertSuccess'][] = 'Menu Item Added';
                        Token::remove($token);
                    }

                    break;

                default:
                    break;
            }

                // ADD MENU ITEM END

                // CHANGE ORDER START
            if (isset($_POST['changeorder'])) {
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    // echo "<pre>";
                    // print_r($_POST['order']);
                    // echo "</pre>";
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        Menus::updateMenuOrder(
                            $_POST['order']
                        );
                        $data['alertSuccess'][] = 'Menu Order Changed';
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($token);
                    }
                    break;

                default:
                    break;
            }

                // CHANGE ORDER END

                $data['menus'] = Options::get('menus', false);
                Theme::admin('header', $data);
                System::inc('menus', $data);
                Theme::admin('footer');
            break;
    }
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}

/* End of file menus.control.php */
/* Location: ./inc/lib/Control/Backend/menus.control.php */
