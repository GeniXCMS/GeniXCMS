<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$data = [];

if (isset($_POST['forgotpass'])) {
    $token = Typo::cleanX($_POST['token']);
    if (!isset($_POST['token']) && !Token::validate($token)) {
        // VALIDATE ALL
        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
    }

    // check last request 
    if (!User::lastRequestPassword()) {
        $alertDanger[] = 'You had reached request password Limit!';
    }
    if (Xaptcha::isEnable()) {
        if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
            $alertDanger[] = _('Please insert the Captcha');
        }
        if (!Xaptcha::verify($_POST['g-recaptcha-response'])) {
            $alertDanger[] = _('Your Captcha is not correct.');
        }
    }

    // Check token first
    if (!isset($alertDanger)) {
        /*check if username is exist or not */
        $username = Typo::cleanX(Typo::strip($_POST['username']));
        $usr = Query::table('user')
            ->select('userid, email, status, activation')
            ->where('userid', $username)
            ->first();

        if ($usr) {
            // check if user is active
            if ($usr->status == '1') {
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
		Hello {$usr->userid},

		You are requesting Password Reset at " . Site::$name . " on {$date}. 
		Below are your new Password :

		{$newpass}

		Now you can login with your new Password at " . Site::$url . '


		Best Regards,


		' . Site::$name . '
		' . Site::$email . '
				';
                $vars = array(
                    'to' => $usr->email,
                    'to_name' => $usr->userid,
                    'message' => $msg,
                    'subject' => 'Password Reset at ' . Site::$name,
                    'msgtype' => 'text',
                );
                if (Mail::send($vars)) {
                    $data['alertSuccess'][] = _("New Password had been sent to Your E-Mail. Please check Your e-mail for the new password.");
                }
            } else {
                if ($usr->activation != '') {
                    $data['alertDanger'][] = _("Your Account is not active. Please activate it first. Check your email for the activation link.");
                } else {
                    $data['alertDanger'][] = _("Your Account is not active. Please contact Support for this problems.");
                }
            }
        } else {
            $data['alertDanger'][] = _("Username is incorrect, No such user available. Please check your username.");
        }

        Token::remove($token);
    } else {
        $data['alertDanger'] = $alertDanger;
    }
}

Theme::admin('headermini', $data);
Theme::auth('forgotpass', $data);
Theme::admin('footermini', $data);
