<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$data = [];

$login_url = Url::login();
if (!isset($_GET['backto'])) {
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $login_url) === false) {
        $url = $_SERVER['HTTP_REFERER'];
        header('Location: ' . Url::login("backto=" . urlencode($url)));
        exit;
    } else {
        // Just set it in data, don't necessarily redirect if we're already on login
        $_GET['backto'] = Site::$url;
    }
} else {
    // Prevent redirect loop if backto is the login page
    if (strpos($_GET['backto'], $login_url) !== false) {
        $_GET['backto'] = Site::$url;
    }
}

if (isset($_POST['login'])) {
    $token = Typo::cleanX($_POST['token']);
    if (!isset($_POST['token']) && !Token::validate($token)) {
        // VALIDATE ALL
        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
    }
    if (Xaptcha::isEnable()) {
        if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
            $alertDanger[] = _('Please insert the Captcha');
        }
        if (!Xaptcha::verify($_POST['g-recaptcha-response'])) {
            $alertDanger[] = _('Your Captcha is not correct.');
        }
    }

    if (!isset($alertDanger)) {
        /*check if username is exist or not */
        $username = Typo::cleanX(Typo::strip(trim($_POST['username'])));
        $usr = Query::table('user')
            ->select('userid, status, activation')
            ->where('userid', $username)
            ->first();

        if ($usr) {
            // check if user is active
            if ($usr->status == '1') {
                /* get user password */
                $pass = User::randpass($_POST['password']);
                $l = Query::table('user')
                    ->select(['pass', 'group'])
                    ->where('userid', $username)
                    ->first();

                $p = $l ? $l->pass : null;
                $g = $l ? $l->group : null;

                if (isset($p)) {
                    if ($p == $pass) {
                        $remember = isset($_POST['rememberme']) ? true : false;
                        $vars = array(
                            'username' => $username,
                            'loggedin' => true,
                            'group' => $g,
                            'rememberme' => $remember
                        );
                        Session::set_session($vars);
                        $data['alertSuccess'][] = _("You Are Logged In Now");
                        echo Hooks::run('user_login_action');
                    } elseif ($p != $pass) {
                        $data['alertDanger'][] = _("Password Didn't Match With Our Records. Please check Your password and try again.");
                    }
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
    } else {
        $data['alertDanger'] = $alertDanger;
        //        print_r($data['alertDanger']);
    }
    //    print_r($data);
}






if (!User::isLoggedin()) {
    Theme::admin('headermini', $data);
    Theme::auth('login', $data);
    Theme::admin('footermini', $data);
} else {
    $backto = isset($_GET['backto']) ? Typo::cleanX($_GET['backto']) : Site::$url;
    header("Location: " . $backto);
    exit;
}
