<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * @since 1.4.0
 * @version 2.4.0
 */

if (User::access(0)) {
    $data['sitetitle'] = _("API Service Settings");
    if (isset($_POST['change'])) {
        $token = Typo::cleanX($_POST['token'] ?? '');
        if (!Token::validate($token)) {
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }
        
        if (isset($alertDanger)) {
            $data['alertDanger'] = $alertDanger;
        } else {
            $vars = array();
            
            // Checkbox/Switch handling
            $switches = array('go_service_fallback');
            foreach ($switches as $s) {
                $vars[$s] = isset($_POST[$s]) ? 'on' : 'off';
            }

            // Standard inputs
            $inputs = array('api_backend', 'go_service_url', 'go_service_secret', 'go_service_whitelist', 'api_key', 'api_rate_limit');
            foreach ($inputs as $i) {
                if (isset($_POST[$i])) {
                    $vars[$i] = Typo::cleanX($_POST[$i]);
                }
            }

            Options::update($vars);
            new Options();
            $data['alertSuccess'][] = _("API Settings Updated Successfully.");
        }
        
        if ($token) {
            Token::remove($token);
        }
    }

    Theme::admin('header', $data);
    System::inc('settings-api', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
