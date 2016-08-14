<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140928
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

System::gZip();

if (isset($_POST['login'])) {
    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                    # code...
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
                    $alertSuccess = 'You are logged in now.';
                } elseif ($p != $pass) {
                    $alertDanger[] = PASS_NOT_MATCH;
                }
            } else {
                if ($usr[0]->activation != '') {
                    $alertDanger[] = ACOUNT_NOT_ACTIVE;
                } else {
                    $alertDanger[] = ACOUNT_NOT_ACTIVE_BLOCK;
                }
            }
        } elseif ($c == '0') {
            $alertDanger[] = NO_USER;
        }
    }
}
Theme::admin('header');
if (isset($alertDanger)) {
    echo '
		<div class="alert alert-danger">
			<ul>
			';
    foreach ($alertDanger as $alert) {
        echo '<li>'.$alert.'</li>';
    }
    echo'</ul>
		</div>';
}
if (isset($alertSuccess)) {
    echo "
		<div class=\"alert alert-success\">
			{$alertSuccess}
		</div>";
}

if (!User::is_loggedin()) {
    ?>
<div class="row">
    <div style="max-width: 300px; margin-left: auto; margin-right: auto; margin-top: 50px;">
        <form class="form-signin" role="form" method="post">
            <h3 class="form-signin-heading"><?=LOGIN_TITLE; ?></h3>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="<?=USERNAME; ?>" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="<?=PASSWORD; ?>" required>
                </div>
            </div>
            <?=Xaptcha::html(); ?>
            <label class="checkbox">
                <a href="forgotpassword.php"><?=FORGOT_PASS; ?></a>
            </label>
            <input type="hidden" name="token" value="<?=TOKEN; ?>">
            <button class="btn  btn-success center-block" name="login" type="submit"><span class="glyphicon glyphicon-log-in"></span>&nbsp;&nbsp;<?=SIGN_IN; ?></button>
        </form>
    </div>
</div>

<?php
} else {
    echo"<div class=\"alert alert-info\">You're already Logged In. <br /><a href=\"logout.php\">Logout</a></div>";
    header('location: index.php');
}
?>
<style>
    #page-wrapper {
        margin-left: 0px!important;
    }
</style>

<?php
Theme::admin('footer');
System::Zipped();
?>

