<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140928
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

define('GX_PATH', realpath(__DIR__ . '/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require("../autoload.php");


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
    $thm->admin('header');
} catch (Exception $e) {
    echo $e->getMessage();
}


if(isset($_POST['login']))
{
	/*check if username is exist or not */
	$username = Typo::cleanX(Typo::strip($_POST['username']));
	$sql = sprintf("SELECT `userid`,`status`,`activation` FROM `user` WHERE `userid` = '%s'", $username);
	$usr = $db->result($sql);
	$c = Db::$num_rows;
	//echo $c;
	//print_r($usr);
	if($c == "1"){
		//$alertgreen = "";
		// check if user is active 
		if($usr[0]->status == '1') {
			/* get user password */
			$pass = User::randpass($_POST['password']);
			$sql = "SELECT `pass`,`group` FROM `user` WHERE `userid` = '{$username}'";

			$l = $db->result($sql);
			$c = Db::$num_rows;

			foreach ($l as $v) {
				# code...
				//print_r($v);
				$p = $v->pass;
				$g = $v->group;
			}
			//echo $p;
			if($p == $pass)
			{
				$vars = array(
							'username'	=> $username,
							'loggedin'	=> true,
							'group'		=> $g
						);
				$sess->set_session($vars);
				/*
				$_SESSION['username'] = $_POST['username'];
				$_SESSION['login'] = "true";
				$_SESSION['group'] = $group;
				*/
				//print_r($_SESSION);
				$alertgreen = "You are logged in now.";
			}elseif($p != $pass){
				$alertred[] = PASS_NOT_MATCH;
			}
		}else{
			if($usr[0]->activation != ''){
				$alertred[] = ACOUNT_NOT_ACTIVE;
			}else{
				$alertred[] = ACOUNT_NOT_ACTIVE_BLOCK;
			}
		}
	}elseif($c == "0"){
		$alertred[] = NO_USER;
	}
}

	if(isset($alertred)) {
		echo "
		<div class=\"alert alert-danger\">
			";
			foreach($alertred as $alert)
			{
				echo $alert;
			}
		echo"
		</div>";
	}
	if(isset($alertgreen)) {
		echo "
		<div class=\"alert alert-success\">
			{$alertgreen}
		</div>";
	}
	if(!User::is_loggedin()){

?>
<div class="row">
	<div style="max-width: 300px; margin-left: auto; margin-right: auto">
		<form class="form-signin" role="form" method="post">
			<h2 class="form-signin-heading"><?=LOGIN_TITLE;?></h2>
			<div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user fa-lg" style="color:#3b5998"></i></span>
			<input type="text" name="username" class="form-control" placeholder="<?=USERNAME;?>" required autofocus>
			</div>
			<br>
			<div class="input-group">
            <span class="input-group-addon"><i class="fa fa-lock fa-lg" style="color:#3b5998"></i></span>
			<input type="password" name="password" class="form-control" placeholder="<?=PASSWORD;?>" required>
			</div>
			<label class="checkbox">
				<a href="forgotpassword.php"><?=FORGOT_PASS;?></a>
			</label>
			<button class="btn btn-lg btn-success btn-block" name="login" type="submit"><span class="glyphicon glyphicon-log-in"></span>&nbsp;&nbsp;<?=SIGN_IN;?></button>
		</form>
	</div>
</div>

<?php
}else {
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
$thm->admin('footer');
System::Zipped();
?>

