<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150126
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

date_default_timezone_set('UTC');
//define('SITE_ID', Typo::getToken(20));
!defined('DB_DRIVER') ? define('DB_DRIVER', 'mysqli') : '';

echo '<h2>Install Page</h2>';
if (isset($_GET['step'])) {
    $step = $_GET['step'];
} else {
    $step = '';
}
switch ($step) {
    case '1':
        if ($_POST['dbuser'] == '') {
            $data['alertDanger'][] = 'Database User Empty!';
        }
        if ($_POST['dbname'] == '') {
            $data['alertDanger'][] = 'Database Name Empty!';
        }
        if (isset($data['alertDanger'])) {
            Control::error('db', $data);

            echo 'Please Press <a href="?" 
                class="btn btn-danger">Back Button</a>.';
        } else {
            $dbhost = (isset($_POST['dbhost']) ? Typo::cleanX($_POST['dbhost']) : '');
            $dbuser = (isset($_POST['dbuser']) ? Typo::strip(Typo::cleanX($_POST['dbuser'])) : '');
            $dbpass = (isset($_POST['dbpass']) ? Typo::strip(Typo::cleanX($_POST['dbpass'])) : '');
            $dbname = (isset($_POST['dbname']) ? Typo::cleanX($_POST['dbname']) : '');
            if (Db::connect($dbhost, $dbuser, $dbpass, $dbname)) {
                $vars = array(
                        'dbhost' => $dbhost,
                        'dbname' => $dbname,
                        'dbuser' => $dbuser,
                        'dbpass' => $dbpass,
                    );
                Session::set_session($vars);
                Theme::install('step1');
            } else {
                $data['alertDanger'][] = Db::$mysqli->connect_error;
                Control::error('db', $data);
                echo 'Please Press <a href="?" 
                class="btn btn-danger">Back Button</a>.';
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
            $config = Install::makeConfig($file);
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
                            'status' => '1',
                            ),
                        'detail' => array(
                            'userid' => Session::val('adminuser'),
                            'fname' => Session::val('adminname'),
                            ),
                        );
                User::create($vars);

                echo 'Installation Success. Go to <a href="gxadmin">Admin Page</a>.';
            } else {
                echo '<h2>Error !! Config File Not Found.</h2>
                Please make sure you had permission to write on the config directory. 
                Do ftp to the server and CHMOD 777 the config directory. After 
                config file is created, you can chmod it back to 755.
                <br>
                <br>
                After You had set the permission, please refresh this page. 
                <br>
                <br>
                or <a href="?step=4" class="btn btn-primary">Click Here</a>';
            }
        } catch (exception $e) {
            echo $e->getMessage();
        }

        break;

    default:
        if (System::existConf()) {
            echo 'Config File Already Exist';
        } else {
            Theme::install('step0');
        }
        break;
}
