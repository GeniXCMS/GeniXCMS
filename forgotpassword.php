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

if (isset($_POST['forgotpass'])) {
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

    // Check token first
    if (!isset($alertDanger)) {
        /*check if username is exist or not */
        $username = Typo::cleanX(Typo::strip($_POST['username']));
        $sql = sprintf("SELECT `userid`,`email`,`status`,`activation` FROM `user` WHERE `userid` = '%s'", $username);
        $usr = Db::result($sql);
        $c = Db::$num_rows;
        //echo $c;
        //print_r($usr);
        if ($c == '1') {
            //$alertSuccess = "";
            // check if user is active
            if ($usr[0]->status == '1') {
                /* get user password */
                $newpass = User::generatePass();
                $id = User::id($username);
                $pass = User::randpass($newpass);
                $vars = array(
                            'id' => $id,
                            'user' => array(
                                        'pass' => $pass,
                                    ),
                        );
                User::update($vars);
                $date = Date::format(date('Y-m-d H:i:s'));
                $msg = "
		Hello {$usr[0]->userid},

		You are requesting Password Reset at ".Site::$name." on {$date}. 
		Below are your new Password :

		{$newpass}

		Now you can login with your new Password at ".Site::$url.'


		Best Regards,


		'.Site::$name.'
		'.Site::$email.'
				';
                $vars = array(
                            'to' => $usr[0]->email,
                            'to_name' => $usr[0]->userid,
                            'message' => $msg,
                            'subject' => 'Password Reset at '.Site::$name,
                            'msgtype' => 'text',
                        );
                //echo "<pre>".$msg."</pre>";
                if (Mail::send($vars)) {
                    $alertSuccess = PASSWORD_SENT_NOTIF;
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

        Token::remove($_POST['token']);
    } else {
        $alertDanger[] = TOKEN_NOT_EXIST;
    }
}
Theme::theme('header');
if (isset($alertDanger)) {
    echo '
		<div class="alert alert-danger">
			';
    foreach ($alertDanger as $alert) {
        echo $alert;
    }
    echo'
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
<div class="container">
    <div style="max-width: 300px; margin-left: auto; margin-right: auto; margin-top: 30px; margin-bottom: 60px ">
        <form action="" class="form-signin" role="form" method="post">
            <h2 class="form-signin-heading"><?=FORGOT_PASS; ?></h2>
            <div class="form-group">
                <label><?=FILLIN_USERNAME; ?></label>
                <input type="text" name="username" class="form-control" placeholder="<?=USERNAME; ?>" required autofocus>
            </div>
            <?=Xaptcha::html(); ?>
            <input type="hidden" name="token" value="<?=TOKEN; ?>">
            <button class="btn btn-lg btn-success btn-block" name="forgotpass" type="submit"><?=REQUEST_PASS; ?></button>
        </form>
    </div>
</div>
<?php
} else {
    echo"<div class=\"alert alert-info\">You're already Logged In. <br /><a href=\"logout.php\">Logout</a></div>";
}

Theme::theme('footer');
System::Zipped();
?>