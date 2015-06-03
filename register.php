<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141003
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

define('GX_PATH', realpath(__DIR__ . '/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require("autoload.php");


try {
    $sys = new System();
    $sess = new Session();
    $thm = new Theme();
    $db = new Db();
    $u = new User();
    new Site();
    Session::start();
    System::gZip();
    Token::create();
    Mod::loader();
    Theme::loader();
    $thm->header($data);
} catch (Exception $e) {
    echo $e->getMessage();
}

if(isset($_POST['register']))
{
    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
        // VALIDATE ALL
        $alertred[] = TOKEN_NOT_EXIST;
    }
	if(!User::is_exist($_POST['userid'])){
        $alertred[] = MSG_USER_EXIST;
    }
    if(!User::is_same($_POST['pass1'], $_POST['pass1'])){
        $alertred[] = MSG_USER_PWD_MISMATCH;
    }
    if(!User::is_email($_POST['email'])){
        $alertred[] = MSG_USER_EMAIL_EXIST;
    }

    if(!isset($alertred)){
        $activation = Typo::getToken(60);
        $vars = array(
                        'user' => array(
                                        'userid' => Typo::cleanX(Typo::strip($_POST['userid'])),
                                        'pass' => User::randpass($_POST['pass1']),
                                        'email' => $_POST['email'],
                                        'group' => '4',
                                        'status' => '0',
                                        'join_date' => date("Y-m-d H:i:s"),
                                        'activation' => $activation
                                    ),
                        
                    );   
        if(User::create($vars) === true){
            $data['alertgreen'][] = REG_ACTIVATE_ACCOUNT;
        }else{
            $alertred[] = REG_CANT_CREATE_ACCOUNT;
        }
        
        
        $vars = array(
                'to'      => $_POST['email'],
                'to_name' => $_POST['userid'],
                'subject' => 'Account Activation Needed at '.Site::$name,
                'message' => '
                            Hi '.$_POST['userid'].', 

                            Thank You for Registering with Us. Please activate your account by clicking this link :
                            '.Site::$url.'/register.php?activation='.$activation.'

                            Sincerely,
                            {$sitename}
                            ',
                'mailtype' => 'text'
            );
        
		$mailsend = Mail::send($vars);
        if($mailsend != ""){
            $alertred[] = $mailsend;
        }else{
            $data['alertgreen'][] = REG_ACTIVATE_ACCOUNT;
        }
		
    }else{
        $data['alertred'] = $alertred;
    }

	if(isset($_POST['token'])){ Token::remove($_POST['token']); }
	
}
if (isset($_GET['activation'])) {
    # code...
    $usr = Db::result(sprintf("SELECT * FROM `user` WHERE `activation` = '%s' LIMIT 1", $_GET['activation'] ));
    if(Db::$num_rows > 0){
        $act = Db::query(sprintf("UPDATE `user` SET `status` = '1',`activation` = NULL WHERE `id` = '%d' ", $usr[0]->id));
        if($act){
            $data['alertgreen'][] = REG_ACCOUNT_ACTIVATED;
            $vars = array(
                'to'      => $usr[0]->email,
                'to_name' => $usr[0]->userid,
                'subject' => 'Welcome to '.Site::$name,
                'message' => '
                            Hi '.$usr[0]->userid.', 

                            Thank You for Registering with Us. Your Account is Activated. 
                            You can now login : '.Site::$url.'/login.php with your username and password

                            Sincerely,
                            {$sitename}
                            ',
                'mailtype' => 'text'
            );
        
            $mailsend = Mail::send($vars);
            if($mailsend != ""){
                $alertred[] = $mailsend;
            }else{
                $data['alertgreen'][] = REG_ACTIVATE_ACCOUNT;
            }
        }else{
            $data['alertred'][] = REG_ACTIVATION_FAILED;
        }
        
    }else{
        $data['alertred'][] = REG_ACTIVATION_FAILED_CODE;
    }
}
$loggedin = Session::val('loggedin');
if(isset($loggedin)){
    echo "<div class=\"alert alert-info\">".REG_ALREADY_REGISTERED_ACC." </div>";
}else{
?>
<div class="col-md-8">
<?php
	if (isset($data['alertgreen'])) {
        # code...
        echo "<div class=\"alert alert-success\" >
        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
            <span aria-hidden=\"true\">&times;</span>
            <span class=\"sr-only\">".CLOSE."</span>
        </button>
        ";
        foreach ($data['alertgreen'] as $alert) {
            # code...
            echo "$alert\n";
        }
        echo "</div>";
    }elseif (isset($data['alertred'])) {
        # code...
        //print_r($data['alertred']);
        echo "<div class=\"alert alert-danger\" >
        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
            <span aria-hidden=\"true\">&times;</span>
            <span class=\"sr-only\">".CLOSE."</span>
        </button>
        <ul>";
        foreach ($data['alertred'] as $alert) {
            # code...
            echo "<li>$alert</li>\n";
        }
        echo "</ul></div>";
    }
	
?>
<h1><?=REG_FORM;?></h1>
<form action="" method="post" name="register" class="registerform">
	<div class="form-group">
		<label for="username"><?=USERNAME;?></label>
		<input type="text" class="form-control" id="username" placeholder="Username" name="userid" required="required" value="">
	</div>
	<div class="form-group">
		<label for="password1"><?=PASSWORD;?></label>
		<input type="password" class="form-control" id="password1" placeholder="Password" name="pass1" required="required">
	</div>
	<div class="form-group">
		<label for="password2"><?=RETYPE_PASSWORD;?></label>
		<input type="password" class="form-control" id="password2" placeholder="Password" name="pass2" required="required">
	</div>
	<div class="form-group">
		<label for="email"><?=EMAIL;?></label>
		<input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required="required" value="">
	</div>
	
		<button type="submit" name="register" class="btn btn-success"><?=SUBMIT;?></button>
        <input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
<div class="clearfix">
&nbsp;
</div>
</div>
<div class="center-block col-sm-4">
    <div class="alert alert-success">
        <?=REG_ALREADY_HAVE_ACC;?>
    </div>
    <form class="form-signin" role="form" method="post" action="login.php">
        <h2 class="form-signin-heading"><?=LOGIN_TITLE;?></h2>
        <label for="username"><?=USERNAME;?></label>
        <input type="text" id="username" name="username" class="form-control" placeholder="<?=USERNAME;?>" required autofocus>
        <label for="password"><?=PASSWORD;?></label>
        <input type="password" id="password" name="password" class="form-control" placeholder="<?=PASSWORD;?>" required>
        <label class="checkbox">
            <a href="forgotpassword.php"><?=FORGOT_PASS;?></a>
        </label>
        <button class="btn btn-success" name="login" type="submit"><?=SIGN_IN;?></button>
    </form>
</div>
<?php
$js = "
                <script>
                    $(document).ready(function() {
                        $('.registerform').bootstrapValidator({
                            message: 'This value is not valid',
                            feedbackIcons: {
                                valid: 'glyphicon glyphicon-ok',
                                invalid: 'glyphicon glyphicon-remove',
                                validating: 'glyphicon glyphicon-refresh'
                            },
                            fields: {
                                userid: {
                                    message: 'The username is not valid',
                                    validators: {
                                        notEmpty: {
                                            message: 'The username is required and cannot be empty'
                                        },
                                        stringLength: {
                                            min: 4,
                                            max: 30,
                                            message: 'The username must be more than 6 and less than 30 characters long'
                                        },
                                        regexp: {
                                            regexp: /^[a-zA-Z0-9_]+$/,
                                            message: 'The username can only consist of alphabetical, number and underscore'
                                        },
                                        different: {
                                            field: 'password',
                                            message: 'The username and password cannot be the same as each other'
                                        }
                                    }
                                },
                                pass1: {
                                    message: 'The password is not valid',
                                    validators: {
                                        notEmpty: {
                                            message: 'The password is required and cannot be empty'
                                        },
                                        different: {
                                            field: 'userid',
                                            message: 'The password cannot be the same as username'
                                        },
                                        identical: {
                                            field: 'pass2',
                                            message: 'The password and its confirm are not the same'
                                        }
                                    }
                                },
                                pass2: {
                                    message: 'The password is not valid',
                                    validators: {
                                        notEmpty: {
                                            message: 'The password is required and cannot be empty'
                                        },
                                        different: {
                                            field: 'userid',
                                            message: 'The password cannot be the same as username'
                                        },
                                        identical: {
                                            field: 'pass1',
                                            message: 'The password and its confirm are not the same'
                                        }

                                    }
                                },
                                email: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The email is required and cannot be empty'
                                        },
                                        emailAddress: {
                                            message: 'The input is not a valid email address'
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
";
$thm->validator($js);
	}
$thm->footer($vars);
System::Zipped();

?>