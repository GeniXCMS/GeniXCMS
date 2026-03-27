<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 */

if (User::access(1)) {
    $data['sitetitle'] = _("Media Settings");
    if (isset($_POST['change'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!isset($_POST['token']) || !Token::validate($token)) {
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }

        if (isset($alertDanger)) {
            $data['alertDanger'] = $alertDanger;
        } else {
            $vars = array();
            foreach ($_POST as $key => $val) {
                $vars[$key] = Typo::cleanX($val);
            }
            unset($vars['change']);
            unset($vars['token']);

            // handle checkbox
            $checkbox = ['media_autoresize_image', 'media_autogenerate_webp', 'media_watermark_enable'];
            foreach ($checkbox as $c) {
                if (!isset($vars[$c])) {
                    $vars[$c] = 'off';
                } else {
                    $vars[$c] = 'on';
                }
            }

            if (count($vars) > 0) {
                Options::update($vars);
                $data['alertSuccess'][] = _("Media Settings Updated Successfully.");
            }
        }
    }

    Theme::admin('header', $data);
    System::inc('settings-media', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
