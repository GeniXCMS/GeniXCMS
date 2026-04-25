<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$data[] = "";

if (isset($_POST['register'])) {
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
    if (User::validate($_POST['userid'])) {
        $alertDanger[] = _("User Exist! Choose Another Username");
    }
    if (!User::isSame($_POST['pass1'], $_POST['pass1'])) {
        $alertDanger[] = _("Password Did Not Match, Retype Your Password Again.");
    }
    if (!User::isEmail($_POST['email'])) {
        $alertDanger[] = _("Email Already Used. Please Use Another E-Mail:");
    }
    if (!Typo::validateEmail($_POST['email'])) {
        $alertDanger[] = _('Your email is not Valid !!');
    }

    if (!isset($alertDanger)) {
        $activation = Typo::getToken(60);
        $userid = Typo::cleanX(Typo::strip($_POST['userid']));
        $email = Typo::strip($_POST['email']);
        $vars = array(
            'user' => array(
                'userid' => $userid,
                'pass' => User::randpass($_POST['pass1']),
                'email' => $email,
                'group' => '6',
                'status' => '0',
                'join_date' => date('Y-m-d H:i:s'),
                'activation' => $activation,
            ),
            'user_detail' => array(
                'userid' => Typo::cleanX(Typo::strip($_POST['userid'])),
            ),

        );
        if (User::create($vars) === true) {
            $data['alertSuccess'][] = _("Thank You for Registering with Us. Please Activate Your Account to login");
        } else {
            $alertDanger[] = _("We can not create your account");
        }

        $vars = array(
            'to' => $email,
            'to_name' => $userid,
            'subject' => _('Account Activation Needed at ') . Site::$name,
            'message' => _('
                            Hi ') . $userid . _(',

                            Thank You for Registering with Us. Please activate your account by clicking this link :
                            ') . Site::$url . '/register/activation/' . $activation . '

                            Sincerely,
                            {$sitename}
                            ',
            'mailtype' => 'text',
        );

        $mailsend = Mail::send($vars);
        if ($mailsend != '') {
            $alertDanger[] = $mailsend;
        } else {
            $data['alertSuccess'][] = _("Thank You for Registering with Us. Please Activate Your Account to login");
        }
        echo Hooks::run('user_reg_action');
    } else {
        $data['alertDanger'] = $alertDanger;
    }

    if (isset($_POST['token'])) {
        Token::remove($_POST['token']);
    }
}
if (isset($_GET['activation'])) {
    $activation = Typo::strip(Typo::cleanX($_GET['activation']));
    $usr = Query::table('user')->where('activation', $activation)->first();
    if ($usr) {
        $act = Query::table('user')
            ->where('id', Typo::int($usr->id))
            ->update(['status' => '1', 'activation' => null]);
        if ($act) {
            $data['alertSuccess'][] = _("Your Account activated successfully. You can now Login with your Username and Password.");
            $vars = array(
                'to' => $usr->email,
                'to_name' => $usr->userid,
                'subject' => _('Welcome to ') . Site::$name,
                'message' => _('
                            Hi ') . $usr->userid . _(',

                            Thank You for Registering with Us. Your Account is Activated.
                            You can now login : ') . Site::$url . '/login/ with your username and password

                            Sincerely,
                            {$sitename}
                            ',
                'mailtype' => 'text',
            );

            $mailsend = Mail::send($vars);
            if ($mailsend != '') {
                $alertDanger[] = $mailsend;
            } else {
                $data['alertSuccess'][] = _("Thank You for Registering with Us. Please Activate Your Account to login");
            }
            echo Hooks::run('user_activation_action');
        } else {
            $data['alertDanger'][] = _("Activation Failed.");
        }
    } else {
        $data['alertDanger'][] = _("Activation Failed. No such code, or maybe already activated.");
    }
}


Theme::admin('headermini', $data);
$loggedin = Session::val('loggedin');
if (isset($loggedin)) {
    echo '<div class="alert alert-info">' . _("You are Already Registered and Logged In!") . ' </div>';
} else {
    Theme::auth('register');
    $js = "
<script>
    $(document).ready(function() {
        $('.registerform').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'bi bi-check2',
                invalid: 'bi bi-x-square',
                validating: 'bi bi-refresh'
            },
            fields: {
                userid: {
                    message: '" . _('The username is not valid') . "',
                    validators: {
                        notEmpty: {
                            message: '" . _('The username is required and cannot be empty') . "'
                        },
                        stringLength: {
                            min: 6,
                            max: 30,
                            message: '" . _('The username must be more than 6 and less than 30 characters long') . "'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_]+$/,
                            message: '" . _('The username can only consist of alphabetical, number and underscore') . "'
                        },
                        different: {
                            field: 'password',
                            message: '" . _('The username and password cannot be the same as each other') . "'
                        }
                    }
                },
                pass1: {
                    message: '" . _('The password is not valid') . "',
                    validators: {
                        notEmpty: {
                            message: '" . _('The password is not valid') . "''The password is required and cannot be empty'
                        },
                        different: {
                            field: 'userid',
                            message: '" . _('The password is not valid') . "''The password cannot be the same as username'
                        },
                        identical: {
                            field: 'pass2',
                            message: '" . _('The password is not valid') . "''The password and its confirm are not the same'
                        }
                    }
                },
                pass2: {
                    message: '" . _('The password is not valid') . "',
                    validators: {
                        notEmpty: {
                            message: '" . _('The password is required and cannot be empty') . "'
                        },
                        different: {
                            field: 'userid',
                            message: '" . _('The password cannot be the same as username') . "'
                        },
                        identical: {
                            field: 'pass1',
                            message: '" . _('The password and its confirm are not the same') . "'
                        }

                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: '" . _('The email is required and cannot be empty') . "'
                        },
                        emailAddress: {
                            message: '" . _('The input is not a valid email address') . "'
                        }
                    }
                }
            }
        });
    });
</script>
";
    Theme::validator($js);
}

Theme::admin('footermini', $data);
