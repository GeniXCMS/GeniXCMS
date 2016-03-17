<?php
/**
* GeniXCMS - Content Management System
*
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140928
* @version 0.0.8
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2016 Puguh Wijayanto
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
	new System();
} catch (Exception $e) {
    echo $e->getMessage();
}

System::gZip();

if(isset($_POST['login']))
{
	if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
        // VALIDATE ALL
        $alertDanger[] = TOKEN_NOT_EXIST;
    }
    if (Xaptcha::isEnable()) {

	    if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '' ) {
	    	$alertDanger[] = "Please insert the Captcha";
	    }
	    if (!Xaptcha::verify($_POST['g-recaptcha-response'])) {
	    	$alertDanger[] = "Your Captcha is not correct.";
	    }
	}

    if (!isset($alertDanger)) {
    	# code...

		/*check if username is exist or not */
		$username = Typo::cleanX(Typo::strip($_POST['username']));
		$sql = sprintf("SELECT `userid`,`status`,`activation` FROM `user` WHERE `userid` = '%s'", $username);
		$usr = Db::result($sql);
		$c = Db::$num_rows;
		//echo $c;
		//print_r($usr);
		if($c == "1"){
			//$alertSuccess = "";
			// check if user is active
			if($usr[0]->status == '1') {
				/* get user password */
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
				if($p == $pass){
					$vars = array(
								'username'	=> $username,
								'loggedin'	=> true,
								'group'		=> $g
							);
					Session::set_session($vars);
					/*
					$_SESSION['username'] = $_POST['username'];
					$_SESSION['login'] = "true";
					$_SESSION['group'] = $group;
					*/
					//print_r($_SESSION);
					$alertSuccess = MSG_USER_LOGGED_IN;
					echo Hooks::run('user_login_action');
				}elseif($p != $pass){
					$alertDanger[] = PASS_NOT_MATCH;
				}
			}else{
				if($usr[0]->activation != ''){
					$alertDanger[] = ACOUNT_NOT_ACTIVE;
				}else{
					$alertDanger[] = ACOUNT_NOT_ACTIVE_BLOCK;
				}
			}
		}elseif($c == "0"){
			$alertDanger[] = NO_USER;
		}
	}
}
Theme::theme('header');
echo "<div class=\"container\">";

	if(isset($alertDanger)) {
		echo "
		<div class=\"alert alert-danger\">
			<ul>
			";
			foreach($alertDanger as $alert)
			{
				echo "<li>".$alert."</li>";
			}
		echo"</ul>
		</div>";
	}
	if(isset($alertSuccess)) {
		echo "
		<div class=\"alert alert-success\">
			{$alertSuccess}
		</div>";
	}
	if(!User::is_loggedin()){

?>

	<div style="max-width: 302px; margin-left: auto; margin-right: auto; margin-top: 20px; margin-bottom: 60px ">
		<form class="form-signin" role="form" method="post">
			<h2 class="form-signin-heading"><?=LOGIN_TITLE;?></h2>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-user"></i></span>
					<input type="text" name="username" class="form-control" placeholder="<?=USERNAME;?>" required autofocus>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-key"></i></span>
					<input type="password" name="password" class="form-control" placeholder="<?=PASSWORD;?>" required>
				</div>
			</div>
			<?=Xaptcha::html();?>
			<label class="checkbox">
				<a href="forgotpassword.php"><?=FORGOT_PASS;?></a>
			</label>
			<input type="hidden" name="token" value="<?=TOKEN;?>">
			<button class="btn btn-lg btn-success btn-block" name="login" type="submit">
				<i class="fa fa-sign-in"></i> <?=SIGN_IN;?>
			</button>
		</form>
	</div>

<?php
}else {
	echo"<div class=\"alert alert-info\">".MSG_USER_ALREADY_LOGGED."<br /><a href=\"logout.php\">".LOGOUT."</a></div>";
}
echo "</div>";
Theme::theme('footer');
System::Zipped();
?>
