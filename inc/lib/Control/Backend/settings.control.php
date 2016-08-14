<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(0)) {
    # code...

    $data['sitetitle'] = SETTINGS;
    switch (isset($_POST['change'])) {
        case '1':
            # code...
            if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                // VALIDATE ALL
                $alertDanger[] = TOKEN_NOT_EXIST;
            }
            if (isset($alertDanger)) {
                $data['alertDanger'] = $alertDanger;
            } else {
                $vars = array();
                if (isset($_FILES['logo']) && $_FILES['logo'] != '') {
                    $path = '/assets/images/';
                    $allowed = array('png', 'jpg', 'gif');
                    $upload = Upload::go('logo', $path, $allowed);
                    if (isset($upload['error']) != '') {
                        echo $upload['error'];
                    } else {
                        if (Image::isPng($upload['filepath'])) {
                            Image::compressPng($upload['filepath']);
                        } elseif (Image::isJpg($upload['filepath'])) {
                            Image::compressJpg($upload['filepath']);
                        }
                        $vars['logo'] = $upload['path'];
                    }
                } else {
                    unset($_POST['logo']);
                }

                //print_r($_POST);
                $flip = array_flip($_POST);
                $sql = "SELECT * FROM `options` WHERE `value` = 'on'";
                $q = Db::result($sql);
                $input = array('is_logourl', 'use_jquery', 'use_bootstrap', 'use_fontawesome',
                    'use_editor', 'use_bsvalidator', 'ppsandbox', 'google_captcha_enable', 'pinger_enable', );
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
                    # code...
                    $vars[$key] = Typo::cleanX($val);
                }
                unset($vars['change']);
                //print_r($vars);
                Options::update($vars);
                new Options();
                $data['alertSuccess'][] = MSG_SETTINGS_SAVED;
            }
            if (isset($_POST['token'])) {
                Token::remove($_POST['token']);
            }
            break;

        default:
            # code...
            //print_r($data);
            break;
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
