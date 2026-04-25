<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
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
    $data['sitetitle'] = _("Settings");
    if (isset($_POST['change'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!isset($_POST['token']) && !Token::validate($token)) {
            // VALIDATE ALL
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }
        if (isset($alertDanger)) {
            $data['alertDanger'] = $alertDanger;
        } else {
            $vars = array();
            // if (isset($_FILES['logo']) && $_FILES['logo'] != '') {
            //     $path = '/assets/images/';
            //     $allowed = array('png', 'jpg', 'gif');
            //     $upload = Upload::go('logo', $path, $allowed);
            //     if (isset($upload['error']) != '') {
            //         echo $upload['error'];
            //     } else {
            //         if (Image::isPng($upload['filepath'])) {
            //             Image::compressPng($upload['filepath']);
            //         } elseif (Image::isJpg($upload['filepath'])) {
            //             Image::compressJpg($upload['filepath']);
            //         }
            //         $vars['logo'] = $upload['path'];
            //     }
            // } else {
            //     unset($_POST['logo']);
            // }

            //print_r($_POST);
            $flip = array_flip($_POST);
            $q = Query::table('options')->where('value', 'on')->get();
            $input = array(
                'is_logourl',
                'use_jquery',
                'use_bootstrap',
                'use_fontawesome',
                'use_editor',
                'use_bsvalidator',
                'ppsandbox',
                'google_captcha_enable',
                'pinger_enable',
                'go_service_fallback',
            );
            foreach ($q as $ob) {
                if (in_array($ob->name, $input)) {
                    if (isset($flip[$ob->name])) {
                        $vars[$ob->name] = 'on';
                        //echo $ob->name;
                    } else {
                        $vars[$ob->name] = 'off';
                        //echo $ob->name;
                    }
                }
            }
            //print_r($ob);
            foreach ($_POST as $key => $val) {
                $vars[$key] = Typo::cleanX($val);
            }
            unset($vars['change']);
            //print_r($vars);
            Options::update($vars);
            new Options();
            $data['alertSuccess'][] = _("Settings Updated Successfully.");
        }
        if (isset($_POST['token'])) {
            Token::remove($token);
        }
    }

    Theme::admin('header', $data);
    System::inc('settings', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file settings.control.php */
/* Location: ./inc/lib/Control/Backend/settings.control.php */
