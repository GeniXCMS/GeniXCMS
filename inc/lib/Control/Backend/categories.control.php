<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20141006
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(1)) {
    $catType = Typo::cleanX($_GET['type'] ?? 'post');

    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        echo Hooks::run('category_param_form');
        exit;
    }

    $data['sitetitle'] = _('Categories') . ' - ' . ucfirst(str_replace('_', ' ', $catType));
    switch (isset($_POST['addcat'])) {
        case true:
            // cleanup first
            $slug = Typo::slugify($_POST['cat']);
            $cat = Typo::cleanX($_POST['cat']);
            $token = Typo::cleanX($_POST['token']);
            if (!isset($_POST['token']) && !Token::validate($token)) {
                // VALIDATE ALL
                $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
            }
            if (!isset($_POST['cat']) || $_POST['cat'] == '') {
                $alertDanger[] = _("Category cannot be empty.");
            }
            if (isset($alertDanger)) {
                $data['alertDanger'] = $alertDanger;
            } else {
                $image = Typo::cleanX($_POST['image'] ?? '');
                $desc = Typo::cleanX($_POST['desc'] ?? '');
                $parent = Typo::int($_POST['parent'] ?? 0);

                Query::table('cat')->insert([
                    'name' => $cat,
                    'slug' => $slug,
                    'parent' => $parent,
                    'image' => $image,
                    'desc' => $desc,
                    'type' => $catType,
                ]);
                $cat_id = Db::$last_id;
                if (isset($_POST['param'])) {
                    foreach ($_POST['param'] as $k => $v) {
                        Categories::addParam($k, $v, $cat_id);
                    }
                }
                $data['alertSuccess'][] = _("Category Added") . ' ' . $_POST['cat'];
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
            $slug = Typo::slugify($_POST['cat']);
            $token = Typo::cleanX($_POST['token']);
            if (!isset($_POST['token']) && !Token::validate($token)) {
                // VALIDATE ALL
                $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
            }
            if (isset($alertDanger)) {
                $data['alertDanger'] = $alertDanger;
            } else {
                Query::table('cat')->where('id', Typo::int($_POST['id']))->update([
                    'name' => $cat,
                    'slug' => $slug,
                    'parent' => Typo::int($_POST['parent'] ?? 0),
                    'image' => Typo::cleanX($_POST['image'] ?? ''),
                    'desc' => Typo::cleanX($_POST['desc'] ?? ''),
                ]);
                $cat_id = Typo::int($_POST['id']);
                if (isset($_POST['param'])) {
                    foreach ($_POST['param'] as $k => $v) {
                        if (!Categories::existParam($k, $cat_id)) {
                            Categories::addParam($k, $v, $cat_id);
                        } else {
                            Categories::editParam($k, $v, $cat_id);
                        }
                    }
                }
                $data['alertSuccess'][] = _("Category Updated") . ' ' . $_POST['cat'];
            }
            if (isset($_POST['token'])) {
                Token::remove($token);
            }
            break;

        default:
            break;
    }

    if (isset($_GET['act']) == 'del' && !isset($_POST['addcat'])) {
        $token = Typo::cleanX($_GET['token']);
        if (!isset($_GET['token']) || !Token::validate($token)) {
            // VALIDATE ALL
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }
        if (isset($alertDanger)) {
            $data['alertDanger'] = $alertDanger;
        } else {
            Categories::delete(Typo::int($_GET['id']));
            $data['alertSuccess'][] = _("Category Removed");
        }
        if (isset($_GET['token'])) {
            Token::remove($token);
        }
    }

    $data['cat'] = Query::table('cat')->where('type', $catType)->orderBy('id', 'DESC')->get();
    $data['num'] = count($data['cat'] ?? []);
    $data['type'] = $catType;
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
