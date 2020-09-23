<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140928
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

/* 
    Set the date Timezone first 
 */
date_default_timezone_set('UTC');

define('GX_PATH', realpath(__DIR__.'/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require '../autoload.php';

try {
    new System();
} catch (Exception $e) {
    echo $e->getMessage();
}

if (!isset($_GET['backto']) && isset($_SERVER['HTTP_REFERER'])) {
    $url = str_replace(Site::$url.'login.php?backto=', '', $_SERVER['HTTP_REFERER']);
    header('Location: '.Site::$url.'login.php?backto='.$url);   
} elseif (!isset($_GET['backto']) && !isset($_SERVER['HTTP_REFERER'])) {
    header('Location: '.Site::$url.'login.php?backto='.Site::$url);
}

System::gZip();
$data = [];
if (isset($_POST['login'])) {
    $token = Typo::cleanX($_POST['token']);
    if (!isset($_POST['token']) || !Token::validate($token)) {
        // VALIDATE ALL
        $alertDanger[] = TOKEN_NOT_EXIST;
    }
    if (Xaptcha::isEnable()) {
        if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
            $alertDanger[] = 'Please insert the Captcha';
        }
        if (!Xaptcha::verify($_POST['g-recaptcha-response'])) {
            $alertDanger[] = 'Your Captcha is not correct.';
        }
    }

    if (!isset($alertDanger)) {
        // check if username is exist or not
        $username = Typo::cleanX(Typo::strip($_POST['username']));
        $sql = sprintf("SELECT `userid`,`status`,`activation` FROM `user` WHERE `userid` = '%s'", $username);
        $usr = Db::result($sql);
        $c = Db::$num_rows;
        //echo $c;
        //print_r($usr);
        if ($c == '1') {
            //$alertSuccess = "";
            // check if user is active
            if ($usr[0]->status == '1') {
                // get user password
                $pass = User::randpass($_POST['password']);
                $sql = "SELECT `pass`,`group` FROM `user` WHERE `userid` = '{$username}'";

                $l = Db::result($sql);
                $c = Db::$num_rows;

                foreach ($l as $v) {
                    //print_r($v);
                    $p = $v->pass;
                    $g = $v->group;
                }
                //echo $p;
                if ($p == $pass) {
                    $vars = array(
                                'username' => $username,
                                'loggedin' => true,
                                'group' => $g,
                            );
                    Session::set_session($vars);
                    // session_regenerate_id(); //not working

                    // $_SESSION['username'] = $_POST['username'];
                    // $_SESSION['login'] = "true";
                    // $_SESSION['group'] = $group;

                    // print_r($_COOKIE);
                    $data['alertSuccess'][] = 'You are logged in now.';
                } elseif ($p != $pass) {
                    $data['alertDanger'][] = PASS_NOT_MATCH;
                }
            } else {
                if ($usr[0]->activation != '') {
                    $data['alertDanger'][] = ACOUNT_NOT_ACTIVE;
                } else {
                    $data['alertDanger'][] = ACOUNT_NOT_ACTIVE_BLOCK;
                }
            }
        } elseif ($c == '0') {
            $data['alertDanger'][] = NO_USER;
        }
    } else {
        $data['alertDanger'] = $alertDanger;
    }
}
Theme::admin('headermini', $data);
echo "<div class='container'>";
echo System::alert($data);
echo "</div>";

if (!User::isLoggedin()) {
    ?>
    <div class="login-box">
        <div class="login-logo">
            <a href="index.php">
                <?=Site::logo('', '45px');?>
            </a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg"><?=LOGIN_TITLE; ?></p>

            <form action="" method="post">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="username" placeholder="<?=USERNAME;?>" required autofocus>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="<?=PASSWORD; ?>">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <?=Xaptcha::html(); ?>
                <div class="row">
<!--                    <div class="col-xs-8">-->
<!--                        <div class="checkbox icheck">-->
<!--                            <label>-->
<!--                                <input type="checkbox"> Remember Me-->
<!--                            </label>-->
<!--                        </div>-->
<!--                    </div>-->
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" name="login"  class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
                <input type="hidden" name="token" value="<?=TOKEN; ?>">
            </form>

<!--            <div class="social-auth-links text-center">-->
<!--                <p>- OR -</p>-->
<!--                <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using-->
<!--                    Facebook</a>-->
<!--                <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using-->
<!--                    Google+</a>-->
<!--            </div>-->
            <!-- /.social-auth-links -->

            <a href="forgotpassword.php"><?=FORGOT_PASS; ?></a><br>
            <a href="<?=Site::$url;?>register.php" class="text-center">Register a new membership</a>

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

<?php
} else {
    echo"<div class=\"alert alert-info\">You're already Logged In. <br /><a href=\"logout.php\">Logout</a></div>";
    header('location: index.php');
    exit;
}
?>
<style>
    #page-wrapper {
        margin-left: 0px!important;
    }
</style>

<?php

Theme::admin('footermini');
System::Zipped();
?>

