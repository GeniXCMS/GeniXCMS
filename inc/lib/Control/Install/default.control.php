<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150126
 *
 * @version 2.0.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

date_default_timezone_set('UTC');
//define('SITE_ID', Typo::getToken(20));
new Http();

!defined('DB_DRIVER') ? define('DB_DRIVER', 'mysqli') : '';

// echo '<h2>Install Page</h2>'; // Removed redundant header

if (isset($_GET['step'])) {
    $step = $_GET['step'];
} else {
    $step = '';
}
switch ($step) {
    case '1':
        if (empty($_POST['dbuser'])) {
            $data['alertDanger'][] = 'Database User Empty!';
        }
        if (empty($_POST['dbname'])) {
            $data['alertDanger'][] = 'Database Name Empty!';
        }

        if (isset($data['alertDanger'])) {
            Theme::install('step_error');
        } else {
            $dbhost = (isset($_POST['dbhost']) ? Typo::cleanX($_POST['dbhost']) : '');
            $dbuser = (isset($_POST['dbuser']) ? Typo::strip(Typo::cleanX($_POST['dbuser'])) : '');
            $dbpass = (isset($_POST['dbpass']) ? Typo::strip(Typo::cleanX($_POST['dbpass'])) : '');
            $dbname = (isset($_POST['dbname']) ? Typo::cleanX($_POST['dbname']) : '');
            $dbdriver = (isset($_POST['dbdriver']) ? Typo::cleanX($_POST['dbdriver']) : 'mysql');
            
            if (Db::connect($dbhost, $dbuser, $dbpass, $dbname, $dbdriver)) {
                $vars = array(
                        'dbhost' => $dbhost,
                        'dbname' => $dbname,
                        'dbuser' => $dbuser,
                        'dbpass' => $dbpass,
                        'dbdriver' => $dbdriver,
                    );
                Session::set_session($vars);
                Theme::install('step1');
            } else {
                $data['alertDanger'][] = "Database connection failed! Please check your configuration.";
                Theme::install('step_error');
            }
        }
        break;

    case '2':
        $vars = array(
                    'sitename' => (isset($_POST['sitename']) ? Typo::cleanX($_POST['sitename']) : ''),
                    'siteslogan' => (isset($_POST['siteslogan']) ? Typo::cleanX($_POST['siteslogan']) : ''),
                    'sitedomain' => (isset($_POST['sitedomain']) ? Typo::cleanX($_POST['sitedomain']) : ''),
                    'siteurl' => (isset($_POST['siteurl']) ? Typo::cleanX($_POST['siteurl']) : ''),
                );
        Session::set_session($vars);
        Theme::install('step2');
        break;

    case '3':
        $vars = array(
                    'adminname' => (isset($_POST['adminname']) ? Typo::cleanX(Typo::strip($_POST['adminname'])) : ''),
                    'adminuser' => (isset($_POST['adminuser']) ? Typo::cleanX(Typo::strip($_POST['adminuser'])) : ''),
                    'adminpass' => (isset($_POST['adminpass']) ? Typo::strip(Typo::strip($_POST['adminpass'])) : ''),
                );
        Session::set_session($vars);
        Theme::install('step3');
        break;

    case '4':
        try {
            $file = GX_PATH.'/inc/config/config.php';
            $result = Install::makeConfig($file);
            // makeConfig() now returns an array with 'config' and 'security_key'.
            // Define SECURITY_KEY in the current request scope so User::randpass()
            // can use it without re-including the newly written config file.
            if (!defined('SECURITY_KEY')) {
                define('SECURITY_KEY', $result['security_key']);
            }
            if (System::existConf()) {
                Install::createTable();
                Install::insertData();
                $vars = array(
                        'user' => array(
                            'userid' => Session::val('adminuser'),
                            'pass' => User::randpass(Session::val('adminpass')),
                            'email' => Session::val('adminuser') . '@' . Session::val('sitedomain'),
                            'group' => '0',
                            'join_date' => date('Y-m-d H:i:s'),
                            'status' => '1'
                            ),
                        'detail' => array(
                            'userid' => Session::val('adminuser'),
                            'fname' => Session::val('adminname')
                            )
                        );
                User::create($vars);

                Theme::install('step4'); // Success
            } else {
                $data['alertDanger'][] = "Config File Not Found. Please CHMOD 777 the config directory.";
                Theme::install('step_error');
            }
        } catch (exception $e) {
             $data['alertDanger'][] = $e->getMessage();
             Theme::install('step_error');
        }

        break;

    default:
        if (System::existConf()) {
            echo '<div class="alert alert-info border-0 rounded-4">Configuration detected. System is established.</div>';
        } else {
            Theme::install('step0');
        }
        break;
}
