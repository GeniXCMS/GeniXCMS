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

date_default_timezone_set('UTC');

define('GX_PATH', realpath(__DIR__.'/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require 'autoload.php';

try {
    new System();
} catch (Exception $e) {
    echo $e->getMessage();
}

if (!isset($_GET['backto']) && isset($_SERVER['HTTP_REFERER'])) {
    $url = str_replace(Site::$url.'login.php?backto=', '', $_SERVER['HTTP_REFERER']);
    header('Location: '.Site::$url.'login.php?backto='.Typo::cleanX($url));   
} elseif (!isset($_GET['backto']) && !isset($_SERVER['HTTP_REFERER'])) {
    header('Location: '.Site::$url.'login.php?backto='.Site::$url);
}

System::gZip(true);

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
        /*check if username is exist or not */
        $username = Typo::cleanX(Typo::strip(trim($_POST['username'])));
        $sql = sprintf("SELECT `userid`,`status`,`activation` FROM `user` WHERE `userid` = '%s'", $username);
        $usr = Db::result($sql);
        $c = Db::$num_rows;
        //echo $c;
        //print_r($usr);
        if ($c == '1') {
            //$alertSuccess = "";
            // check if user is active
            if ($usr[0]->status == '1') {
                /* get user password */
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
                if (isset($p)) {
                    if ($p == $pass) {
                        // session_regenerate_id();
                        $vars = array(
                                    'username' => $username,
                                    'loggedin' => true,
                                    'group' => $g,
                                );
                        Session::set_session($vars);
                        /*
                        $_SESSION['username'] = $_POST['username'];
                        $_SESSION['login'] = "true";
                        $_SESSION['group'] = $group;
                        */
                        //print_r($_SESSION);
                        $data['alertSuccess'][] = MSG_USER_LOGGED_IN;
                        echo Hooks::run('user_login_action');
                    } elseif ($p != $pass) {
                        $data['alertDanger'][] = PASS_NOT_MATCH;
                    }
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
//        print_r($data['alertDanger']);
    }
//    print_r($data);
}

Theme::theme('header', $data);
echo '<div class="container">';

echo System::alert($data);
if (!User::isLoggedin()) {
    ?>

    <div style="max-width: 302px; margin-left: auto; margin-right: auto; margin-top: 20px; margin-bottom: 60px ">
    <form class="form-signin" role="form" method="post">
    <h2 class="form-signin-heading"><?=LOGIN_TITLE; ?></h2>
    <div class="form-group">
        <?php
            echo Hooks::run('login_form_header');
        ?>
    </div>
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
        <button class="btn btn-lg btn-success btn-block" name="login" type="submit">
            <i class="fa fa-sign-in"></i> <?=SIGN_IN; ?>
        </button>
    </form>
        <div class="">
            <?php
                echo Hooks::run('login_form_footer');
            ?>
        </div>
    </div>

<?php
} else {
    $backto = isset($_GET['backto']) ? Typo::cleanX($_GET['backto']): Site::$url; 
    header("Location: ".$backto);
}
echo '</div>';

Theme::theme('footer', $data);
System::Zipped();
?>
