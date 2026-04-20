<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150312
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(1) || (isset($_GET['id']) && User::id(Session::val('username')) == $_GET['id'])) {
    $data['sitetitle'] = _('Users');
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }

    // search query - build parameterized conditions
    $whereRaws = [];
    $whereBindings = [];
    $qpage = '';
    if (isset($_GET['q']) && $_GET['q'] != '') {
        $q = Typo::cleanX($_GET['q']);
        $whereRaws[] = "(A.`userid` LIKE ? OR A.`email` LIKE ?)";
        $whereBindings[] = "%{$q}%";
        $whereBindings[] = "%{$q}%";
        $qpage .= "&q={$_GET['q']}";
    }
    if (isset($_GET['from']) && $_GET['from'] != '') {
        $from = Typo::cleanX($_GET['from']);
        $whereRaws[] = "A.`join_date` >= ?";
        $whereBindings[] = $from;
        $qpage .= "&from={$from}";
    }
    if (isset($_GET['to']) && $_GET['to'] != '') {
        $to = Typo::cleanX($_GET['to']);
        $whereRaws[] = "A.`join_date` <= ?";
        $whereBindings[] = $to;
        $qpage .= "&to={$to}";
    }
    if (isset($_GET['group']) && $_GET['group'] != '') {
        $group = Typo::int($_GET['group']);
        $whereRaws[] = "A.`group` = ?";
        $whereBindings[] = $group;
        $qpage .= "&group={$group}";
    }
    if (isset($_GET['status']) && $_GET['status'] != '') {
        $status = Typo::int($_GET['status']);
        $whereRaws[] = "A.`status` = ?";
        $whereBindings[] = $status;
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
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    }

                    // VALIDATE ALL check if inputed userid is not same
                    $userid = Typo::cleanX($_POST['userid']);
                    $olduserid = Typo::cleanX($_POST['olduserid']);
                    $id = Typo::int($_GET['id']);
                    if (!User::isSame($olduserid, $userid) && User::validate($userid)) {
                        $alertDanger[] = _("User Exist! Choose Another Username");
                    }

                    if (!User::isEmail($_POST['email'], $id)) {
                        $alertDanger[] = _("Email Already Used. Please Use Another E-Mail:");
                    }

                    if (!isset($alertDanger)) {


                        $group = (User::access(1)) ? Typo::int($_POST['group']) : Session::val('group');
                        $userid = (User::access(0)) ? Typo::cleanX($_POST['userid']) : User::id($id);

                        $vars = array(
                            'id' => $id,
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
                        $alertSuccess[] = 'User : ' . User::userid($id) . ' Updated';

                        if (isset($alertSuccess)) {
                            $data['alertSuccess'] = $alertSuccess;
                        }
                        Hooks::run('user_submit_edit_action', $_GET);
                    } else {
                        $data['alertDanger'] = $alertDanger;
                    }

                    if (isset($_POST['token'])) {
                        Token::remove($token);
                    }
                    break;
                default:
                    break;
            }
            Theme::admin('header', $data);
            System::inc('user_form', $data);
            Theme::admin('footer', $data);
            return;
        case 'del':
            if (User::access(1)) {
                if (isset($_GET['id'])) {
                    $id = Typo::int($_GET['id']);
                    $user = User::userid($id);
                    $token = Typo::cleanX($_GET['token']);
                    if (!isset($_GET['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $data['alertDanger'][] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    } else {
                        User::delete($id);
                        Hooks::run('user_delete_action', $_GET);
                        $data['alertSuccess'][] = _("User") . ' ' . $user . ' ' . _("Removed Successfully");
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($token);
                    }
                } else {
                    $data['alertDanger'][] = _("No ID Selected");
                }
            }
            break;
        case 'active':
            if (User::access(1)) {
                $id = Typo::int($_GET['id']);
                $token = Typo::cleanX($_GET['token']);
                if (!isset($_GET['token']) || !Token::validate($_GET['token'])) {
                    // VALIDATE ALL
                    $data['alertDanger'][] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                } else {
                    if (User::activate($id)) {
                        $data['alertSuccess'][] = _("User") . ' ' . User::userid($id) . ' ' . _("Activated Successfully.");
                    } else {
                        $data['alertDanger'][] = _("User") . ' ' . User::userid($id) . ' ' . _("Activation fail.");
                    }
                }
                if (isset($_GET['token'])) {
                    Token::remove($token);
                }
            }
            break;

        case 'inactive':
            if (User::access(1)) {
                $token = Typo::cleanX($_GET['token']);
                $id = Typo::int($_GET['id']);
                if (!isset($_GET['token']) || !Token::validate($token)) {
                    // VALIDATE ALL
                    $data['alertDanger'][] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                } else {
                    if (User::deactivate($id)) {
                        $data['alertSuccess'][] = _("User") . ' ' . User::userid($id) . ' ' . _("Deactivated Successfully.");
                    } else {
                        $data['alertDanger'][] = _("User") . ' ' . User::userid($id) . ' ' . _("Deactivation fail.");
                    }
                }
                if (isset($_GET['token'])) {
                    Token::remove($token);
                }
            }
            break;

        default:
            $data[] = '';
            if (User::access(1)) {
                switch (isset($_POST['adduser'])) {
                    case true:
                        // CHECK TOKEN FIRST
                        //echo Token::validate($_POST['token']);
                        $userid = Typo::cleanX($_POST['userid']);
                        $email = Typo::cleanX($_POST['email']);
                        $group = Typo::int($_POST['group']);
                        $pass1 = Typo::strip($_POST['pass1']);
                        $pass2 = Typo::strip($_POST['pass2']);

                        $token = Typo::cleanX($_POST['token']);
                        if (!isset($_POST['token']) || !Token::validate($token)) {
                            // VALIDATE ALL
                            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                        }

                        if (!isset($userid) || $userid == '') {
                            // VALIDATE ALL
                            $alertDanger[] = _("Username cannot be empty.");
                        }
                        if (!isset($_POST['pass1']) || $_POST['pass1'] == '') {
                            // VALIDATE ALL
                            $alertDanger[] = _("Password 1 cannot be empty.");
                        }
                        if (!isset($_POST['pass2']) || $_POST['pass2'] == '') {
                            // VALIDATE ALL
                            $alertDanger[] = _("Password 2 cannot be empty.");
                        }

                        if (User::validate($userid)) {
                            $alertDanger[] = _("User Exist! Choose Another Username");
                        }
                        if (!User::isSame($_POST['pass1'], $_POST['pass2'])) {
                            $alertDanger[] = _("Password Did Not Match, Retype Your Password Again.");
                        }
                        if (!User::isEmail($_POST['email'])) {
                            $alertDanger[] = _("Email Already Used. Please Use Another E-Mail:");
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
                            Token::remove($token);
                            $data['alertSuccess'][] = _("User") . " {$userid}, " . _("Added Successfully");
                        } else {
                            $data['alertDanger'] = $alertDanger;
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($token);
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
                    $user_id = $_POST['user_id'];
                } else {
                    $user_id = '';
                }
                switch ($action) {
                    case 'activate':
                        $token = Typo::cleanX($_POST['token']);
                        if (!isset($_POST['token']) || !Token::validate($token)) {
                            // VALIDATE ALL
                            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                        }
                        if (isset($alertDanger)) {
                            $data['alertDanger'] = $alertDanger;
                        } else {
                            foreach ($user_id as $id) {
                                $userid = User::userid($id);
                                User::activate($id);
                                $data['alertSuccess'][] = _("User {$userid} Activated");
                            }
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($token);
                        }
                        break;
                    case 'deactivate':
                        $token = Typo::cleanX($_POST['token']);
                        if (!isset($_POST['token']) || !Token::validate($token)) {
                            // VALIDATE ALL
                            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                        }
                        if (isset($alertDanger)) {
                            $data['alertDanger'] = $alertDanger;
                        } else {
                            foreach ($user_id as $id) {
                                $userid = User::userid($id);
                                User::deactivate($id);
                                $data['alertSuccess'][] = _("User {$userid} Deactivated");
                            }
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($token);
                        }
                        break;
                    case 'delete':
                        $token = Typo::cleanX($_POST['token']);
                        if (!isset($_POST['token']) || !Token::validate($token)) {
                            // VALIDATE ALL
                            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                        }
                        if (isset($alertDanger)) {
                            $data['alertDanger'] = $alertDanger;
                        } else {
                            foreach ($user_id as $id) {
                                $userid = User::userid($id);
                                User::delete($id);
                                $data['alertSuccess'][] = _("User {$userid} Deleted");
                            }
                        }
                        if (isset($_POST['token'])) {
                            Token::remove($token);
                        }
                        break;

                    default:
                        break;
                }



            }
            break;
    }
    $userSql = "SELECT A.`userid`, A.`group`, A.`email`, A.`join_date`, A.`status`, A.`id` as `id`, B.`country`
        FROM `user` AS A LEFT JOIN `user_detail` AS B ON A.`userid` = B.`userid`";
    if (!empty($whereRaws)) {
        $userSql .= " WHERE " . implode(' AND ', $whereRaws);
    }

    // Calculate total for paging
    $countSql = "SELECT COUNT(A.`id`) as total FROM `user` AS A LEFT JOIN `user_detail` AS B ON A.`userid` = B.`userid`";
    if (!empty($whereRaws)) {
        $countSql .= " WHERE " . implode(' AND ', $whereRaws);
    }
    $countRes = Db::result($countSql, $whereBindings);
    $totalCount = isset($countRes[0]) ? (int) $countRes[0]->total : 0;

    $userSql .= " ORDER BY A.`userid` ASC LIMIT " . (int) $offset . ", " . (int) $max;
    $data['usr'] = Db::result($userSql, $whereBindings);
    $data['num'] = count($data['usr']);
    $page = array(
        'paging' => $paging,
        'table' => [
            'user' => ['A', 'LEFT JOIN', 'userid'],
            'user_detail' => ['B', 'LEFT JOIN', 'userid']
        ],
        'select' => 'A.`id` ',
        'total' => $totalCount,
        'max' => $max,
        'url' => 'index.php?page=users' . $qpage,
        'type' => 'pager',
    );
    $data['paging'] = Paging::create($page);
    Theme::admin('header', $data);
    System::inc('user', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}

/* End of file users.control.php */
/* Location: ./inc/lib/Control/Backend/users.control.php */
