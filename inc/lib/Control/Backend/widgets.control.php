<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

if (User::access(1)) {
    $data['sitetitle'] = "Widgets Management";
    $token = TOKEN;

    if (isset($_POST['add_widget'])) {
        if (!Token::validate($_POST['token'])) {
            $data['alertDanger'][] = _("Invalid token.");
        } else {
            $vars = [
                'table' => 'widgets',
                'key' => [
                    'name' => Typo::cleanX($_POST['name']),
                    'title' => Typo::cleanX($_POST['title']),
                    'type' => Typo::cleanX($_POST['type']),
                    'location' => Typo::cleanX($_POST['location']),
                    'content' => $_POST['content'], 
                    'status' => 1,
                    'sorting' => (int)$_POST['sorting']
                ]
            ];
            Db::insert($vars);
            $data['alertSuccess'][] = _("Widget created successfully.");
        }
        System::alert($data);
    }

    if (isset($_POST['edit_widget'])) {
        if (!Token::validate($_POST['token'])) {
            $data['alertDanger'][] = _("Invalid token.");
        } else {
            $vars = [
                'table' => 'widgets',
                'id' => (int)$_POST['id'],
                'key' => [
                    'name' => Typo::cleanX($_POST['name']),
                    'title' => Typo::cleanX($_POST['title']),
                    'type' => Typo::cleanX($_POST['type']),
                    'location' => Typo::cleanX($_POST['location']),
                    'content' => $_POST['content'],
                    'sorting' => (int)$_POST['sorting']
                ]
            ];
            Db::update($vars);
            $data['alertSuccess'][] = _("Widget updated.");
        }
        System::alert($data);
    }

    if (isset($_GET['act'])) {
        if ($_GET['act'] == 'del') {
            Db::delete(['table' => 'widgets', 'where' => ['id' => $_GET['id']]]);
            $data['alertSuccess'][] = _("Widget deleted.");
        } elseif ($_GET['act'] == 'activate') {
            Db::update(['table' => 'widgets', 'key' => ['status' => 1], 'id' => $_GET['id']]);
        } elseif ($_GET['act'] == 'deactivate') {
            Db::update(['table' => 'widgets', 'key' => ['status' => 0], 'id' => $_GET['id']]);
        } elseif ($_GET['act'] == 'edit') {
            $data['widget'] = Db::result("SELECT * FROM `widgets` WHERE `id` = '".(int)$_GET['id']."' LIMIT 1");
        }
        System::alert($data);
    }

    $data['widgets'] = Db::result("SELECT * FROM `widgets` ORDER BY `location`, `sorting` ASC");
    $data['num'] = Db::$num_rows;

    Theme::admin('header', $data);
    System::inc('widgets', $data);
    Theme::admin('footer');
} else {
    Control::error('noaccess');
}
