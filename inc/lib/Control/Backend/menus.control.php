<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141007
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
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
            # code...
            if (isset($_POST['submit'])) {
                # code...
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    # code...

                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                        $menus = json_decode(Options::v('menus'), true);
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
                        $menu[$_POST['id']]['menu'] = $menus[$_POST['id']]['menu'];
                        $menu[$_POST['id']]['menu'][] = array(
                                                    'parent' => $_POST['parent'],
                                                    'menuid' => Typo::strip($_POST['id']),
                                                    'name' => Typo::cleanX($_POST['name']),
                                                    'type' => $_POST['type'],
                                                    'value' => $_POST[$_POST['type']],
                                                    'sub' => '',
                                                );
                        $menu = array(
                                $_POST['id'] => array(
                                            'name' => $menus[$_POST['id']]['name'],
                                            'class' => $menus[$_POST['id']]['class'],
                                            'menu' => $menu[$_POST['id']]['menu'],
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
                            'parent' => $_POST['parent'],
                            'menuid' => $_POST['id'],
                            'name' => Typo::cleanX($_POST['name']),
                            'class' => Typo::cleanX($_POST['class']),
                            'type' => $_POST['type'],
                            'value' => $_POST[$_POST['type']],
                        );
                        Menus::insert($vars);
                        $data['alertSuccess'][] = 'Menu Added';
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($_POST['token']);
                    }
                    break;

                default:
                    # code...

                    break;
            }
                //$data['abc'] = "abc";
            if (isset($_GET['id'])) {
                $menuid = $_GET['id'];
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
            # code...
            if (isset($_POST['edititem'])) {
                # code...
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $vars = array(
                            'parent' => Typo::int($_POST['parent']),
                            'menuid' => Typo::strip($_POST['id']),
                            'name' => Typo::cleanX($_POST['name']),
                            'class' => Typo::cleanX($_POST['class']),
                            'type' => $_POST['type'],
                            'value' => $_POST[$_POST['type']],
                        );
                        $vars = array(
                            'id' => $_GET['itemid'],
                            'key' => $vars,
                        );
                        Menus::update($vars);
                        $data['alertSuccess'][] = 'Menu Updated';
                        Token::remove($_POST['token']);
                    }

                    break;

                default:
                    # code...

                    break;
            }

            if (isset($_GET['id'])) {
                $menuid = $_GET['id'];
            } else {
                $menuid = '';
            }
                $data['menus'] = Menus::getId($_GET['itemid']);
                $data['parent'] = Menus::isHadParent('', $menuid);
                Theme::admin('header', $data);
                System::inc('menus_form_edit', $data);
                Theme::admin('footer');
            break;
        case 'del':
            if (isset($_POST['additem'])) {
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                    Token::remove($_POST['token']);
                }
            } else {
                if (isset($_GET['itemid']) && !isset($_POST['additem'])) {
                    if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        Menus::delete($_GET['itemid']);
                        $data['alertSuccess'][] = 'Menu Deleted';
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($_GET['token']);
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
                if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                    // VALIDATE ALL
                    $alertDanger[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertDanger)) {
                    $data['alertDanger'] = $alertDanger;
                } else {
                    $menus = json_decode(Options::v('menus'), true);
                    unset($menus[$_GET['menuid']]);

                    $sql = sprintf("DELETE FROM `menus` WHERE `menuid` = '%s' ", $_GET['menuid']);
                    Db::query($sql);
                    $menu = json_encode($menus);
                    Options::update('menus', $menu);
                    new Options();
                    $data['alertSuccess'][] = 'Menu Deleted';
                }
                if (isset($_GET['token'])) {
                    Token::remove($_GET['token']);
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
            # code...
            if (isset($_POST['submit'])) {
                # code...
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    # code...

                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                        $menu = array(
                                $_POST['id'] => array(
                                            'name' => Typo::cleanX($_POST['name']),
                                            'class' => Typo::cleanX($_POST['class']),
                                            'menu' => array(),
                                        ),
                                );
                        $menus = json_decode(Options::v('menus'), true);
                        if (is_array($menus)) {
                            $menu = array_merge($menus, $menu);
                        }

                        $menu = json_encode($menu);
                        Options::update('menus', $menu);
                        new Options();
                        $data['alertSuccess'][] = 'Menu Added';
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($_POST['token']);
                    }
                    break;

                default:
                    # code...

                    break;
            }

                // ADD MENU ITEM START
            if (isset($_POST['additem'])) {
                # code...
                $submit = true;
            } else {
                $submit = false;
            }
            switch ($submit) {
                case true:
                    echo $_POST['token'];
                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                        Token::remove($_POST['token']);
                    }

                    break;

                default:
                    # code...

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
                    # code...
                    // echo "<pre>";
                    // print_r($_POST['order']);
                    // echo "</pre>";
                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                        Token::remove($_POST['token']);
                    }
                    break;

                default:
                    # code...
                    break;
            }

                // CHANGE ORDER END

                $data['menus'] = Options::get('menus');
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
