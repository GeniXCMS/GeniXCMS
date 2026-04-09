<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.2 build date 20170912
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */


if (User::access(1)) {
    $data['sitetitle'] = "Cache Settings";

    if (isset($_POST['change'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!isset($_POST['token']) && !Token::validate($token)) {
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }

        if (!isset($alertDanger)) {
            $vars = array();
            $flip = array_flip($_POST);
            // print_r($_POST);
            $q = Query::table('options')->where('value', 'on')->get();
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
            $data['alertSuccess'][] = _('Settings Updated');
            new Options();
        } else {
            $data['alertDanger'] = $alertDanger;
        }

    }

    $data['cache_enabled'] = Options::v('cache_enabled');
    $data['cache_type'] = Options::v('cache_type') ?: 'file';
    $data['cache_path'] = Options::v('cache_path');
    $data['cache_timeout'] = Options::v('cache_timeout');
    $data['redis_host'] = Options::v('redis_host') ?: '127.0.0.1';
    $data['redis_port'] = Options::v('redis_port') ?: '6379';
    $data['redis_pass'] = Options::v('redis_pass');
    $data['redis_db'] = Options::v('redis_db') ?: '0';

    Theme::admin('header', $data);
    System::inc('settings-cache', $data);
    Theme::admin('footer');

} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
