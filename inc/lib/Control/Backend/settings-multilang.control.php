<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150718
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

if (User::access(0)) {
    $data['sitetitle'] = _('Multilanguage');

    if (isset($_POST['addcountry'])) {
        $token = Typo::cleanX($_POST['token']);
        if (!isset($_POST['token']) && !Token::validate($token)) {
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }
        if (!isset($_POST['multilang_country_name']) || $_POST['multilang_country_name'] == '') {
            $alertDanger[] = _('Please insert Country Name');
        }
        if (!isset($_POST['multilang_country_code']) || $_POST['multilang_country_code'] == '') {
            $alertDanger[] = _('Please insert Country Code');
        }
        if (!isset($alertDanger)) {
            // print_r($_POST);
            $lang = array(
                $_POST['multilang_country_code'] => array(
                        'country' => Typo::jsonFormat($_POST['multilang_country_name']),
                        'system_lang' => Typo::jsonFormat($_POST['multilang_system_lang']),
                        'flag' => Typo::jsonFormat($_POST['multilang_country_flag']),
                    ),
            );
            $langs = json_decode(Options::v('multilang_country'), true);
            $langs = array_merge((array) $langs, $lang);
            $langs = json_encode($langs);
            Options::update('multilang_country', $langs);
            $data['alertSuccess'][] = _("Country Added");
            new Options();
            Token::remove($token);
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }

    if (isset($_GET['del']) && $_GET['del'] != '') {
        $token = Typo::cleanX($_GET['token']);
        if (!isset($_GET['token']) || !Token::validate($token)) {
            $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
        }
        if (!isset($alertDanger)) {
            $langs = json_decode(Options::v('multilang_country'), true);
            if (array_key_exists($_GET['del'], $langs)) {
                unset($langs[$_GET['del']]);
                $langs = json_encode($langs);
            // print_r($langs);
                Options::update('multilang_country', $langs);
                new Options();
                $data['alertSuccess'][] = _('Language Removed');
                Token::remove($_GET['token']);
            } else {
                $data['alertDanger'][] = _('Error!');
            }
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }

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
            $input = array('multilang_enable');
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
            foreach ($_POST as $key => $val) {
                $vars[$key] = Typo::cleanX($val);
            }

            try {
                if( count($vars) > 0 )
                    Options::update($vars);
                    $data['alertSuccess'][] = _("Settings Updated");
            } catch (\Throwable $th) {
                throw $th;
            }
            
            new Options();
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }
    $data['default_lang'] = Options::v('multilang_default');
    $data['list_lang'] = json_decode(Options::v('multilang_country'), true);
    System::alert($data);
    Theme::admin('header', $data);
    System::inc('settings-multilang', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file default.control.php */
/* Location: ./inc/lib/Control/Backend/default.control.php */
