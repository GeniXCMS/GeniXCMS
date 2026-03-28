<?php

class Mods
{
    public function __construct()
    {

        Hooks::attach('mod_control', array('Mods', 'page'));
        $menulist = array(
            'page' => 'Mod Page'
        );
        Mod::addMenuList($menulist);
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
