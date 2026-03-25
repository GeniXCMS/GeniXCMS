<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
 *
 * @version 2.0.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(1)) {
    $data['sitetitle'] = _("Tags");
    switch (isset($_POST['addcat'])) {
        case true:
            // cleanup first
            $slug = Typo::slugify(Typo::cleanX($_POST['cat']));
            $cat = Typo::cleanX($_POST['cat']);
            $token = Typo::cleanX($_POST['token']);
            if (!isset($_POST['token']) && !Token::validate($token)) {
                // VALIDATE ALL
                $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
            }
            if (!isset($_POST['cat']) || $_POST['cat'] == '') {
                $alertDanger[] = _("Tag Name Cannot be Empty");
            }
            if (isset($alertDanger)) {
                $data['alertDanger'] = $alertDanger;
            } else {
                $cat = Db::insert(
                    sprintf(
                        "INSERT INTO `cat` VALUES (null, '%s', '%s', '%d', '', 'tag' )",
                        $cat,
                        $slug,
                        0
                    )
                );
                //print_r($cat);
                $data['alertSuccess'][] = _("Tag Added").' '.$_POST['cat'];
            }
            if (isset($_POST['token'])) {
                Token::remove($token);
            }
            break;

        default:
            break;
    }

    switch (isset($_POST['updatecat'])) {
        case true:
            // cleanup first
            $cat = Typo::cleanX($_POST['cat']);
            $token = Typo::cleanX($_POST['token']);
            if (!isset($_POST['token']) && !Token::validate($token)) {
                // VALIDATE ALL
                $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
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
                $data['alertSuccess'][] = _("Tag Updated").' '.$_POST['cat'];
            }
            if (isset($_POST['token'])) {
                Token::remove($token);
            }
            break;

        default:
            break;
    }

    if (isset($_GET['act']) && $_GET['act'] == 'del' && !isset($_POST['addcat'])) {
        $token = Typo::cleanX($_GET['token']);
        if (!isset($_GET['token']) || !Token::validate($token)) {
            // VALIDATE ALL
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }
        if (isset($alertDanger)) {
            $data['alertDanger'] = $alertDanger;
        } else {
            echo "Detel";
            Categories::delete(Typo::int($_GET['id']));
            $data['alertSuccess'][] = _("Tag Removed");
        }
        if (isset($_GET['token'])) {
            Token::remove($token);
        }
    }
    System::alert($data);
    $data['cat'] = Db::result("SELECT * FROM `cat` WHERE `type` = 'tag' ORDER BY `id` DESC");
    $data['num'] = Db::$num_rows;
    Theme::admin('header', $data);
    System::inc('tags', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}

/* End of file categories.control.php */
/* Location: ./inc/lib/Control/Backend/categories.control.php */
