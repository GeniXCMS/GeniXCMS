<?php
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
*    filename : register.php
*    version : 0.0.1 pre
*    build : 20141003
*/

define('GX_PATH', realpath(__DIR__ . '/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

function __autoload($f) {
    require GX_LIB. $f . '.class.php';
}


try {
    $sys = new System();
    $sess = new Session();
    $thm = new Theme();
    $db = new Db();
    $u = new User();
    Session::start();
    System::gZip();
    $thm->header();
} catch (Exception $e) {
    echo $e->getMessage();
}

if(isset($_POST['register']))
{

	if(!User::is_exist($_POST['userid'])){
        $alertred[] = "User Exist!! Choose another userid.";
    }
    if(!User::is_same($_POST['pass1'], $_POST['pass1'])){
        $alertred[] = "Password Didn't Match!! Retype Your Password again.";
    }
    if(!User::is_email($_POST['email'])){
        $alertred[] = "Email already used. Please use another email.";
    }

    if(!isset($alertred)){

        $vars = array(
                        'user' => array(
                                        'userid' => Typo::cleanX($_POST['userid']),
                                        'pass' => User::randpass(Typo::cleanX($_POST['pass1'])),
                                        'email' => $_POST['email'],
                                        'group' => '4'
                                    ),
                        
                    );   
        User::create($vars);
        $alertgreen[] = "Thank You for Registering with Us. You can now <a href=\"login.php\">Login</a> with your username and password";
  //       $to      = "{$_POST['email']}";
		// $name    = $_POST['username'];
		// $subject = 'Welcome to '.siteName();
		// $message = 'Hi '.$_POST['username'].', Thank You for Registering with Us. You can now login : '.$website.'login.php with your username and password';
		// $headers = 'From: '.$emailAdmin . "\r\n" .
		// 'Reply-To: '.$emailAdmin . "\r\n" .
		// 'X-Mailer: PHP/' . phpversion();
		//mail($to, $subject, $message, $headers);
		//sendEmail($to, $name, $subject, $msg);
    }else{
        $data['alertred'] = $alertred;
    }

	
	
}
?>
<div class="col-md-8">
<?php
	if(isset($alertred)) {
		echo "
		<div class=\"alert alert-danger\">
		<button type=\"button\" class=\"close\" data-dismiss=\"alert\">
            <span aria-hidden=\"true\">&times;</span>
            <span class=\"sr-only\">Close</span>
        </button>
        <ul>
			";
			foreach($alertred as $alert)
			{
				echo "<li>".$alert."</li>";
			}
		echo"
		</ul>
		</div>";
	}
	if(isset($alertgreen)) {
		echo "
		<div class=\"alert alert-success\">
		<button type=\"button\" class=\"close\" data-dismiss=\"alert\">
            <span aria-hidden=\"true\">&times;</span>
            <span class=\"sr-only\">Close</span>
        </button>
			{$alertgreen}
		</div>";
	}
	$loggedin = Session::val('loggedin');
	if(isset($loggedin)){
		echo "You are already registered and Logged In. ";
	}else{
?>
<h1>Register</h1>
<form action="" method="post" name="register" class="registerform">
	<div class="form-group">
		<label for="exampleInputEmail1">Username</label>
		<input type="text" class="form-control" id="exampleInputEmail1" placeholder="Username" name="userid" required="required" value="">
	</div>
	<div class="form-group">
		<label for="exampleInputPassword1">Password</label>
		<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="pass1" required="required">
	</div>
	<div class="form-group">
		<label for="exampleInputPassword1">Retype Password</label>
		<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="pass2" required="required">
	</div>
	<div class="form-group">
		<label for="exampleInputEmail1">Email address</label>
		<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" name="email" required="required" value="">
	</div>
	
		<button type="submit" name="register" class="btn btn-success">Submit</button>
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
                                        min: 6,
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