<?php

require_once "Auth.php";
require_once 'Log.php';
require_once 'Log/observer.php';

// Callback function to display login form
function loginFunction($username = null, $status = null, &$auth = null)
{
	/*
	 * Change the HTML output so that it fits to your
	 * application.
	 */
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">";
	echo "Username: <input type=\"text\" name=\"username\"><br/>";
	echo "Password: <input type=\"password\" name=\"password\"><br/>";
	echo "<input type=\"submit\">";
	echo "</form>";
}

class Auth_Log_Observer extends Log_observer {

	var $messages = array();

	function notify($event) {

		$this->messages[] = $event;

	}

}

$options = array(
	'enableLogging' => true,
	array(
		'type' => 'Array',
		'options' => array(
			'cryptType' => 'md5',
			'users' => array(
				'guest' => md5('password'),
			),
		),
	),
	array(
		'type' => 'Array',
		'options' => array(
			'cryptType' => 'md5',
			'users' => array(
				'admin' => md5('password'),
			),
		),
	),
);
$a = new Auth("Multiple", $options, "loginFunction");

$infoObserver = new Auth_Log_Observer(AUTH_LOG_INFO);

$a->attachLogObserver($infoObserver);

$debugObserver = new Auth_Log_Observer(AUTH_LOG_DEBUG);

$a->attachLogObserver($debugObserver);

$a->start();

if ($a->checkAuth()) {
	/*
	 * The output of your site goes here.
	 */
	print "Authentication Successful.<br/>";
}

print '<h3>Logging Output:</h3>'
	.'<b>AUTH_LOG_INFO level messages:</b><br/>';

foreach ($infoObserver->messages as $event) {
	print $event['priority'].': '.$event['message'].'<br/>';
}

print '<br/>'
	.'<b>AUTH_LOG_DEBUG level messages:</b><br/>';

foreach ($debugObserver->messages as $event) {
	print $event['priority'].': '.$event['message'].'<br/>';
}

print '<br/>';

?>
