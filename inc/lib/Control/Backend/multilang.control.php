<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150718
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
    $data['sitetitle'] = 'Multilanguage';

    if (isset($_POST['addcountry'])) {
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            $alertDanger[] = TOKEN_NOT_EXIST;
        }
        if (!isset($_POST['multilang_country_name']) || $_POST['multilang_country_name'] == '') {
            $alertDanger[] = 'Please insert Country Name';
        }
        if (!isset($_POST['multilang_country_code']) || $_POST['multilang_country_code'] == '') {
            $alertDanger[] = 'Please insert Country Code';
        }
        if (!isset($alertDanger)) {
            // print_r($_POST);
            $lang = array(
                $_POST['multilang_country_code'] => array(
                        'country' => $_POST['multilang_country_name'],
                        'system_lang' => $_POST['multilang_system_lang'],
                        'flag' => $_POST['multilang_country_flag'],
                    ),
            );
            $langs = json_decode(Options::v('multilang_country'), true);
            $langs = array_merge((array) $langs, $lang);
            $langs = json_encode($langs);
            Options::update('multilang_country', $langs);
            new Options();
            Token::remove($_POST['token']);
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }

    if (isset($_GET['del']) && $_GET['del'] != '') {
        if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
            $alertDanger[] = TOKEN_NOT_EXIST;
        }
        if (!isset($alertDanger)) {
            $langs = json_decode(Options::v('multilang_country'), true);
            if (array_key_exists($_GET['del'], $langs)) {
                unset($langs[$_GET['del']]);
                $langs = json_encode($langs);
            // print_r($langs);
                Options::update('multilang_country', $langs);
                new Options();
                $data['alertSuccess'][] = 'Work';
                Token::remove($_GET['token']);
            } else {
                $data['alertDanger'][] = 'Error!';
            }
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }

    if (isset($_POST['change'])) {
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            $alertDanger[] = TOKEN_NOT_EXIST;
        }

        if (!isset($alertDanger)) {
            $vars = array();
            $flip = array_flip($_POST);
        // print_r($_POST);
            $sql = "SELECT * FROM `options` WHERE `value` = 'on'";
            $q = Db::result($sql);
            $input = array('multilang_default', 'multilang_enable');
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
            unset($_POST['token']);
            unset($_POST['change']);
        // print_r($vars);
            foreach ($_POST as $key => $val) {
                # code...
                $vars[$key] = Typo::cleanX($val);
            }
        // print_r($vars);

            Options::update($vars);
            new Options();
        } else {
            $data['alertDanger'] = $alertDanger;
        }
    }
    $data['default_lang'] = Options::v('multilang_default');
    $data['list_lang'] = json_decode(Options::v('multilang_country'), true);

    Theme::admin('header', $data);
    System::inc('multilang', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file default.control.php */
/* Location: ./inc/lib/Control/Backend/default.control.php */
