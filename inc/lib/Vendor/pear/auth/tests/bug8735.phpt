--TEST--
Regression test for bug #8735
--SKIPIF--
<?php
$file = 'File/Passwd.php';
if (!$fp = @fopen($file, 'r', true)) {
    die("skip $file package is not installed.");
}
fclose($fp);
?>
--FILE--
<?php
set_include_path(dirname(dirname(__FILE__)) . ':' . get_include_path());
$datasrc = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bug8735.passwd';

require_once 'PEAR.php';
require_once 'Auth.php';

$a = new Auth('File', 
		array('file' => $datasrc, 
			'type' => 'AuthBasic'),
		'displayLogin');
if (PEAR::isError($a)) {
	print $a->getMessage();
	exit;
}

$error = $a->removeUser('username');
if (PEAR::isError($error)) {
	print $error->getMessage();
	exit;
}

readfile($datasrc);
print "-- cut --\n";

$error = $a->addUser('username', 'password');
if (PEAR::isError($error) || $error === false) {
	print "Error happened when adding.\n";
	print $error->getMessage();
	exit;
}

readfile($datasrc);

$a->removeUser('username');
?>
--EXPECT--
test:fcfKBtvEwG4g.
-- cut --
test:fcfKBtvEwG4g.
username:{SHA}W6ph5Mm5Pz8GgiULbPgzG37mj9g=
