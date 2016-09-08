<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
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

if (User::access(1) || (isset($_GET['id']) && User::id(Session::val('username')) == $_GET['id'])) {
    $data['sitetitle'] = USERS;
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }

    // search query
    $where = ' 1 ';
    $qpage = '';
    if (isset($_GET['q']) && $_GET['q'] != '') {
        $q = Typo::cleanX($_GET['q']);
        $where .= "AND (`userid` LIKE '%%{$q}%%' OR `email` LIKE '%%{$q}%%') ";
        $qpage .= "&q={$_GET['q']}";
    }
    if (isset($_GET['from']) && $_GET['from'] != '') {
        $from = Typo::cleanX($_GET['from']);
        $where .= "AND `join_date` >= '{$from}' ";
        $qpage .= "&from={$from}";
    }
    if (isset($_GET['to']) && $_GET['to'] != '') {
        $to = Typo::cleanX($_GET['to']);
        $where .= "AND `join_date` <= '{$to}' ";
        $qpage .= "&to={$to}";
    }
    if (isset($_GET['group']) && $_GET['group'] != '') {
        $group = Typo::int($_GET['group']);
        $where .= "AND `group` = '{$group}' ";
        $qpage .= "&group={$group}";
    }
    if (isset($_GET['status']) && $_GET['status'] != '') {
        $status = Typo::int($_GET['status']);
        $where .= "AND `status` = '{$status}' ";
        $qpage .= "&status={$status}";
    }

    $max = '10';
    if (isset($_GET['paging'])) {
        $paging = Typo::int($_GET['paging']);
        $offset = ($paging - 1) * $max;
    } else {
        $paging = 1;
        $offset = 0;
    }

    switch ($act) {
        case 'edit':
            $data[] = '';
            switch (isset($_POST['edituser'])) {
                case true:
                    //check token first
                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }

                    // VALIDATE ALL
                    if (!User::isExist($_POST['userid'])) {
                        $alertDanger[] = MSG_USER_EXIST;
                    }

                    if (!User::isEmail($_POST['email'])) {
                        $alertDanger[] = MSG_USER_EMAIL_EXIST;
                    }

                    if (!isset($alertDanger)) {
                        $group = (User::access(1)) ? Typo::int($_POST['group']) : Session::val('group');
                        $userid = (User::access(0)) ? Typo::cleanX($_POST['userid']) : User::id($_GET['id']);
                        $vars = array(
                                        'id' => sprintf('%d', $_GET['id']),
                                        'user' => array(
                                                        'userid' => $userid,
                                                        'email' => Typo::cleanX($_POST['email']),
                                                        'group' => $group,
                                                    ),

                                    );
                        if (!empty($_POST['pass']) || $_POST['pass'] != '') {
                            $pass = array(
                                        'pass' => User::randpass($_POST['pass']),
                                    );
                            $vars['user'] = array_merge($vars['user'], $pass);
                            //print_r($vars);
                        }
                        User::update($vars);
                        $alertSuccess[] = 'User : '.User::userid($_GET['id']).' Updated';

                        if (isset($alertSuccess)) {
                            $data['alertSuccess'] = $alertSuccess;
                        }
                        Hooks::run('user_submit_edit_action', $_GET);
                    } else {
                        $data['alertDanger'] = $alertDanger;
                    }

                    if (isset($_POST['token'])) {
                        Token::remove($_POST['token']);
                    }
                    break;
                default:
                    break;
            }
            Theme::admin('header', $data);
            System::inc('user_form', $data);
            Theme::admin('footer');
            break;
        case 'del':
            if (User::access(1)) {
                if (isset($_GET['id'])) {
                    $user = User::userid($_GET['id']);
                    if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                        // VALIDATE ALL
                        $data['alertDanger'][] = TOKEN_NOT_EXIST;
                    } else {
                        User::delete($_GET['id']);
                        Hooks::run('user_delete_action', $_GET);
                        $data['alertSuccess'][] = USER.' '.$user.' '.MSG_USER_REMOVED;
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($_GET['token']);
                    }
                } else {
                    $data['alertDanger'][] = MSG_USER_NO_ID_SELECTED;
                }
                $data['usr'] = Db::result("SELECT * FROM `user` WHERE {$where} ORDER BY `userid` ASC LIMIT {$offset}, {$max}");
                $data['num'] = Db::$num_rows;
                $page = array(
                            'paging' => $paging,
                            'table' => 'user',
                            'where' => $where,
                            'max' => $max,
                            'url' => 'index.php?page=users'.$qpage,
                            'type' => 'pager',
                        );
                $data['paging'] = Paging::create($page);
                Theme::admin('header', $data);
                System::inc('user', $data);
                Theme::admin('footer');
            }
            break;
        case 'active':
            if (User::access(1)) {
                if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                    // VALIDATE ALL
                    $data['alertDanger'][] = TOKEN_NOT_EXIST;
                } else {
                    if (User::activate($_GET['id'])) {
                        $data['alertSuccess'][] = USER.' '.User::userid($_GET['id']).' '.MSG_USER_ACTIVATED;
                    } else {
                        $data['alertDanger'][] = USER.' '.User::userid($_GET['id']).' '.MSG_USER_ACTIVATION_FAIL;
                    }
                }
                if (isset($_GET['token'])) {
                    Token::remove($_GET['token']);
                }
                $data['usr'] = Db::result("SELECT * FROM `user` WHERE {$where} ORDER BY `userid` ASC LIMIT {$offset}, {$max}");
                $data['num'] = Db::$num_rows;
                $page = array(
                            'paging' => $paging,
                            'table' => 'user',
                            'where' => $where,
                            'max' => $max,
                            'url' => 'index.php?page=users'.$qpage,
                            'type' => 'pager',
                        );
                $data['paging'] = Paging::create($page);
                Theme::admin('header', $data);
                System::inc('user', $data);
                Theme::admin('footer');
            }
            break;

        case 'inactive':
            if (User::access(1)) {
                if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                    // VALIDATE ALL
                    $data['alertDanger'][] = TOKEN_NOT_EXIST;
                } else {
                    if (User::deactivate($_GET['id'])) {
                        $data['alertSuccess'][] = USER.' '.User::userid($_GET['id']).' '.MSG_USER_DEACTIVATED;
                    } else {
                        $data['alertDanger'][] = USER.' '.User::userid($_GET['id']).' '.MSG_USER_DEACTIVATION_FAIL;
                    }
                }
                if (isset($_GET['token'])) {
                    Token::remove($_GET['token']);
                }
                $data['usr'] = Db::result("SELECT * FROM `user` WHERE {$where} ORDER BY `userid` ASC LIMIT {$offset}, {$max}");
                $data['num'] = Db::$num_rows;
                $page = array(
                            'paging' => $paging,
                            'table' => 'user',
                            'where' => $where,
                            'max' => $max,
                            'url' => 'index.php?page=users'.$qpage,
                            'type' => 'pager',
                        );
                $data['paging'] = Paging::create($page);
                Theme::admin('header', $data);
                System::inc('user', $data);
                Theme::admin('footer');
            }
            break;

        default:
            $data[] = '';
            if (User::access(1)) {
                switch (isset($_POST['adduser'])) {
                    case true:
                        // CHECK TOKEN FIRST
                        //echo Token::isExist($_POST['token']);
                        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                            // VALIDATE ALL
                            $alertDanger[] = TOKEN_NOT_EXIST;
                        }

                        $userid = Typo::cleanX($_POST['userid']);
                        $email = Typo::cleanX($_POST['email']);
                        $group = Typo::int($_POST['group']);

                        if (!isset($userid) || $userid == '') {
                            // VALIDATE ALL
                            $alertDanger[] = USERID_CANNOT_EMPTY;
                        }
                        if (!isset($_POST['pass1']) || $_POST['pass1'] == '') {
                            // VALIDATE ALL
                            $alertDanger[] = PASS1_CANNOT_EMPTY;
                        }
                        if (!isset($_POST['pass2']) || $_POST['pass2'] == '') {
                            // VALIDATE ALL
                            $alertDanger[] = PASS2_CANNOT_EMPTY;
                        }

                        if (!User::isExist($_POST['userid'])) {
                            $alertDanger[] = MSG_USER_EXIST;
                        }
                        if (!User::isSame($_POST['pass1'], $_POST['pass2'])) {
                            $alertDanger[] = MSG_USER_PWD_MISMATCH;
                        }
                        if (!User::isEmail($_POST['email'])) {
                            $alertDanger[] = MSG_USER_EMAIL_EXIST;
                        }

                        if (!isset($alertDanger)) {
                            $vars = array(
                                            'user' => array(
                                                            'userid' => $userid,
                                                            'pass' => User::randpass($_POST['pass1']),
                                                            'email' => $email,
                                                            'group' => $group,
                                                            'status' => '1',
                                                            'join_date' => date('Y-m-d H:i:s'),
                                                        ),

                                        );
                            User::create($vars);
                            Hooks::run('user_submit_add_action', $_POST);
                            Token::remove($_POST['token']);
                            $data['alertSuccess'][] = USER." {$_POST['userid']}, ".MSG_USER_ADDED;
                        } else {
                            $data['alertDanger'] = $alertDanger;
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($_POST['token']);
                        }
                        break;

                    default:
                        break;
                }

                if (isset($_POST['action'])) {
                    $action = Typo::cleanX($_POST['action']);
                } else {
                    $action = '';
                }
                if (isset($_POST['user_id'])) {
                    $user_id = Typo::cleanX($_POST['user_id']);
                } else {
                    $user_id = '';
                }
                switch ($action) {
                    case 'activate':
                        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                            // VALIDATE ALL
                            $alertDanger[] = TOKEN_NOT_EXIST;
                        }
                        if (isset($alertDanger)) {
                            $data['alertDanger'] = $alertDanger;
                        } else {
                            foreach ($user_id as $id) {
                                User::activate($id);
                            }
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($_POST['token']);
                        }
                        break;
                    case 'deactivate':
                        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                            // VALIDATE ALL
                            $alertDanger[] = TOKEN_NOT_EXIST;
                        }
                        if (isset($alertDanger)) {
                            $data['alertDanger'] = $alertDanger;
                        } else {
                            foreach ($user_id as $id) {
                                User::deactivate($id);
                            }
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($_POST['token']);
                        }
                        break;
                    case 'delete':
                        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                            // VALIDATE ALL
                            $alertDanger[] = TOKEN_NOT_EXIST;
                        }
                        if (isset($alertDanger)) {
                            $data['alertDanger'] = $alertDanger;
                        } else {
                            foreach ($user_id as $id) {
                                User::delete($id);
                            }
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($_POST['token']);
                        }
                        break;

                    default:
                        break;
                }

                $data['usr'] = Db::result("SELECT * FROM `user` WHERE {$where} ORDER BY `userid` ASC LIMIT {$offset}, {$max}");
                $data['num'] = Db::$num_rows;
                $page = array(
                            'paging' => $paging,
                            'table' => 'user',
                            'where' => $where,
                            'max' => $max,
                            'url' => 'index.php?page=users'.$qpage,
                            'type' => 'pager',
                        );
                $data['paging'] = Paging::create($page);

                Theme::admin('header', $data);
                System::inc('user', $data);
                Theme::admin('footer');
            }
            break;
    }
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}

/* End of file users.control.php */
/* Location: ./inc/lib/Control/Backend/users.control.php */
