<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.2 build date 20170912
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */


if (User::access(1)) {
    $data['sitetitle'] = "Cache Settings";

    if (isset($_POST['change'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!isset($_POST['token']) || !Token::validate($token)) {
            $alertDanger[] = TOKEN_NOT_EXIST;
        }

        if (!isset($alertDanger)) {
            $vars = array();
            $flip = array_flip($_POST);
        // print_r($_POST);
            $sql = "SELECT * FROM `options` WHERE `value` = 'on'";
            $q = Db::result($sql);
            $input = array('cache_enabled', 'cache_path', 'cache_timeout');

            foreach ($q as $ob) {
                if (in_array($ob->name, $input)) {
                    if (isset($flip[$ob->name])) {
                        $vars[$ob->name] = 'on';
                    } else {
                        $vars[$ob->name] = 'off';
                    }
                }
            }

            unset($_POST['token']);
            unset($_POST['change']);
        // print_r($vars);
            foreach ($_POST as $key => $val) {
                $vars[$key] = Typo::cleanX($val);
            }
        // print_r($vars);

            Options::update($vars);
            new Options();
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }

    $data['cache_enabled'] = Options::v('cache_enabled');
    $data['cache_path'] = Options::v('cache_path');
    $data['cache_timeout'] = Options::v('cache_timeout');

    Theme::admin('header', $data);
    System::inc('cache', $data);
    Theme::admin('footer');

} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}