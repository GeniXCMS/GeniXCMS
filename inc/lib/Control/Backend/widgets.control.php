<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 2.0.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(1)) {
    $data['sitetitle'] = "Widgets Management";
    $token = TOKEN;

    if (isset($_POST['add_widget'])) {
        if (!Token::validate($_POST['token'])) {
            $data['alertDanger'][] = _("Invalid token.");
        } else {
            Query::table('widgets')->insert([
                'name' => Typo::cleanX($_POST['name']),
                'title' => Typo::cleanX($_POST['title']),
                'type' => Typo::cleanX($_POST['type']),
                'location' => Typo::cleanX($_POST['location']),
                'content' => $_POST['content'],
                'status' => 1,
                'sorting' => (int) $_POST['sorting'],
            ]);
            $widget_id = Db::lastId();
            if (isset($_POST['param']) && is_array($_POST['param'])) {
                foreach ($_POST['param'] as $k => $v) {
                    Widget::addParam($k, $v, $widget_id);
                }
            }
            $data['alertSuccess'][] = _("Widget created successfully.");
        }
    }

    if (isset($_POST['edit_widget'])) {
        if (!Token::validate($_POST['token'])) {
            $data['alertDanger'][] = _("Invalid token.");
        } else {
            Query::table('widgets')->where('id', (int) $_POST['id'])->update([
                'name' => Typo::cleanX($_POST['name']),
                'title' => Typo::cleanX($_POST['title']),
                'type' => Typo::cleanX($_POST['type']),
                'location' => Typo::cleanX($_POST['location']),
                'content' => $_POST['content'],
                'sorting' => (int) $_POST['sorting'],
            ]);
            if (isset($_POST['param']) && is_array($_POST['param'])) {
                foreach ($_POST['param'] as $k => $v) {
                    if (Widget::existParam($k, $_POST['id'])) {
                        Widget::editParam($k, $v, $_POST['id']);
                    } else {
                        Widget::addParam($k, $v, $_POST['id']);
                    }
                }
            }
            $data['alertSuccess'][] = _("Widget updated.");
        }
    }

    if (isset($_GET['act'])) {
        if ($_GET['act'] == 'del') {
            Query::table('widgets')->where('id', Typo::int($_GET['id']))->delete();
            $data['alertSuccess'][] = _("Widget deleted.");
        } elseif ($_GET['act'] == 'activate') {
            Query::table('widgets')->where('id', Typo::int($_GET['id']))->update(['status' => 1]);
        } elseif ($_GET['act'] == 'deactivate') {
            Query::table('widgets')->where('id', Typo::int($_GET['id']))->update(['status' => 0]);
        } elseif ($_GET['act'] == 'edit') {
            $data['widget'] = Query::table('widgets')->where('id', Typo::int($_GET['id']))->first();
        }
    }

    $data['widgets'] = Query::table('widgets')->orderBy('location', 'ASC')->get();
    $data['num'] = count($data['widgets']);

    Theme::admin('header', $data);
    System::inc('widgets', $data);
    Theme::admin('footer');
} else {
    Control::error('noaccess');
}
