<?php

class Mods
{
    public function __construct()
    {
        Hooks::attach('mod_control', array('Mods', 'page'));

        Mod::addMenuList(['page' => _('My Sample Mod Page')]);

        Hooks::attach('init', function () {
            AdminMenu::add([
                'id' => 'mod_page',
                'label' => _('My Sample Mod Page'),
                'icon' => 'bi bi-box',
                'url' => 'index.php?page=mods&mod=page',
                'access' => 1,
                'position' => 'external',
                'order' => 30,
            ]);
        });
    }
    public static function show()
    {
        echo 'Mod Show';
    }

    public static function page($data)
    {
        // global $data;
        // if (SMART_URL) {
        //     $data = $data[0];
        // } else {
        //     $data = $_GET;
        // }
        // print_r($data);
        $params = $data[0]['mod_params'] ?? [];
        $mod_name = $data[0]['mod'] ?? '';
        if ((isset($params[0]) && $params[0] == 'page') || $mod_name == 'page') {
            return Mod::inc('frontpage', $data[0], realpath(__DIR__ . '/../layout/'));
        }
    }
}
