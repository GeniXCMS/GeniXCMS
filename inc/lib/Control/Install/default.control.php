<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150126
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


echo "<h2>Install Page</h2>";
if (isset($_GET['step'])) {
    $step = $_GET['step'];
}else{
    $step = '';
}
switch ($step){
    case '1':
        if(Db::connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'])){
            $vars = array(
                    'dbhost' => (isset($_POST)? $_POST['dbhost'] : "") ,
                    'dbname' => (isset($_POST)? $_POST['dbname'] : "") ,
                    'dbuser' => (isset($_POST)? $_POST['dbuser'] : "") ,
                    'dbpass' => (isset($_POST)? $_POST['dbpass'] : "") 
                );
            Session::set_session($vars);
            Theme::install('step1');
        }else{
            echo "<h3>Database Error</h3>Please Press <a href=\"?\" 
            class=\"btn btn-danger\">Back Button</a>.";
        }
        

        
    break;

    case '2':

        $vars = array(
                    'sitename' => (isset($_POST)? $_POST['sitename'] : "") ,
                    'siteslogan' => (isset($_POST)? $_POST['siteslogan'] : ""),
                    'sitedomain' => (isset($_POST)? $_POST['sitedomain'] : ""),
                    'siteurl' => (isset($_POST)? $_POST['siteurl'] : "")
                );
        Session::set_session($vars);
        Theme::install('step2');
    break;

    case '3':
        $vars = array(
                    'adminname' => (isset($_POST)? $_POST['adminname'] : ""),
                    'adminuser' => (isset($_POST)? $_POST['adminuser'] : ""),
                    'adminpass' => (isset($_POST)? $_POST['adminpass'] : "")
                );
        Session::set_session($vars);
        Theme::install('step3');
    break;

    case '4':
        try {
            $file = GX_PATH."/inc/config/config.php";
            $config = Install::makeConfig($file);
            if (System::existConf()) {

                Install::createTable();
                Install::insertData();
                $vars = array(
                        'user' => array(
                            'userid' => Session::val('adminuser'),
                            'pass' => User::randpass(Session::val('adminpass')),
                            'email' => 'admin@'.Session::val('sitedomain'),
                            'group' => '0',
                            'join_date' => date("Y-m-d H:i:s"),
                            'status' => '1'
                            ),
                        'detail' => array(
                            'userid' => Session::val('adminuser'),
                            'fname' => Session::val('adminname')
                            )
                        );
                User::create($vars);

                echo "Installation Success. Go to <a href=\"gxadmin\">Admin Page</a>.";
            }else{
                echo "<h2>Error !! Config File Not Found.</h2>
                Please make sure you had permission to write on the config directory. 
                Do ftp to the server and CHMOD 777 the config directory. After 
                config file is created, you can chmod it back to 755.
                <br>
                <br>
                After You had set the permission, please refresh this page. 
                <br>
                <br>
                or <a href=\"?step=4\" class=\"btn btn-primary\">Click Here</a>";
            }
        }catch (exception $e) {
            echo $e->getMessage();
        }
        

    break;

    default:
        if (System::existConf()) {
            # code...
            echo "Config File Already Exist";
        }else{
            Theme::install('step0');
        }
    break;
}
