<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
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

if (User::access(1)) {
    $data['sitetitle'] = CATEGORIES;
    switch (isset($_POST['addcat'])) {
        case true:
            # code...
            // cleanup first
            $slug = Typo::slugify(Typo::cleanX($_POST['cat']));
            $cat = Typo::cleanX($_POST['cat']);

            if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                // VALIDATE ALL
                $alertDanger[] = TOKEN_NOT_EXIST;
            }
            if (!isset($_POST['cat']) || $_POST['cat'] == '') {
                $alertDanger[] = CATEGORY_CANNOT_EMPTY;
            }
            if (isset($alertDanger)) {
                $data['alertDanger'] = $alertDanger;
            } else {
                $cat = Db::insert(
                    sprintf(
                        "INSERT INTO `cat` VALUES (null, '%s', '%s', '%d', '', 'post' )",
                        $cat,
                        $slug,
                        $_POST['parent']
                    )
                );
                //print_r($cat);
                $data['alertSuccess'][] = MSG_CATEGORY_ADDED.' '.$_POST['cat'];
            }
            if (isset($_POST['token'])) {
                Token::remove($_POST['token']);
            }
            break;

        default:
            # code...
            break;
    }

    switch (isset($_POST['updatecat'])) {
        case true:
            # code...
            // cleanup first
            $cat = Typo::cleanX($_POST['cat']);
            if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                // VALIDATE ALL
                $alertDanger[] = TOKEN_NOT_EXIST;
            }
            if (isset($alertDanger)) {
                $data['alertDanger'] = $alertDanger;
            } else {
                $vars = array(
                    'table' => 'cat',
                    'id' => Typo::int($_POST['id']),
                    'key' => array(
                                'name' => $cat,
                            ),
                );
                $cat = Db::update($vars);
                $data['alertSuccess'][] = MSG_CATEGORY_UPDATED.' '.$_POST['cat'];
            }
            if (isset($_POST['token'])) {
                Token::remove($_POST['token']);
            }
            break;

        default:
            # code...
            break;
    }

    if (isset($_GET['act']) == 'del') {
        if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
            // VALIDATE ALL
            $alertDanger[] = TOKEN_NOT_EXIST;
        }
        if (isset($alertDanger)) {
            $data['alertDanger'] = $alertDanger;
        } else {
            Categories::delete($_GET['id']);
            $data['alertSuccess'][] = MSG_CATEGORY_REMOVED;
        }
        if (isset($_GET['token'])) {
            Token::remove($_GET['token']);
        }
    }
    $data['cat'] = Db::result("SELECT * FROM `cat` WHERE `type` = 'post' ORDER BY `id` DESC");
    $data['num'] = Db::$num_rows;
    Theme::admin('header', $data);
    System::inc('categories', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file categories.control.php */
/* Location: ./inc/lib/Control/Backend/categories.control.php */
