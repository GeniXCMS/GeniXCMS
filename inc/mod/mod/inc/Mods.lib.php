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
        if (isset($data[0]) && $data[0] == 'page') {
            Mod::inc('frontpage', $data, realpath(__DIR__.'/../layout/'));
        }
    }
}
