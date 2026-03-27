<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20160313
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
    $data['sitetitle'] = 'Comments Settings';

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
            $input = array('comments_enable', 'spamwords', 'comments_perpage');

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
            $data['alertSuccess'][] = _("Settings Updated");
            new Options();
        } else {
            $data['alertDanger'] = $alertDanger;
        }

        System::alert($data);
    }

    $data['comments_enable'] = Options::v('comments_enable');
    $data['comments_perpage'] = Options::v('comments_perpage');
    $data['spamwords'] = Options::v('spamwords');

    Theme::admin('header', $data);
    System::inc('settings-comments', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file default.control.php */
/* Location: ./inc/lib/Control/Backend/default.control.php */
