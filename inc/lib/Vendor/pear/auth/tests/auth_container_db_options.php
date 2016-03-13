<?php

// Using constants because excecuting tests via "php tests/AllTests.php"
// drops the $_ENV vars after the first tests are executed.
if (defined('PEAR_AUTH_TEST_OPTIONS')) {
    return;
}

if (!empty($_ENV['MYSQL_TEST_USER']) && extension_loaded('mysqli')) {
    $dsn = array(
        'phptype' => 'mysqli',
        'username' => $_ENV['MYSQL_TEST_USER'],
        'password' => $_ENV['MYSQL_TEST_PASSWD'],
        'database' => $_ENV['MYSQL_TEST_DB'],

        'hostspec' => empty($_ENV['MYSQL_TEST_HOST'])
                ? null : $_ENV['MYSQL_TEST_HOST'],

        'port' => empty($_ENV['MYSQL_TEST_PORT'])
                ? null : $_ENV['MYSQL_TEST_PORT'],

        'socket' => empty($_ENV['MYSQL_TEST_SOCKET'])
                ? null : $_ENV['MYSQL_TEST_SOCKET'],

        // Hack for MDB2's silly connect method logic.
        'protocol' => empty($_ENV['MYSQL_TEST_SOCKET'])
                ? null : 'unix',
    );
} else {
    $dsn = array('username' => '', 'password' => '');
}

$options = array(
    'dsn'=>$dsn,
    'table'=>'temp',
    'usernamecol'=>'username',
    'passwordcol'=>'password',
    'cryptType'=>'md5',
    'db_fields'=>'*'
);

$extra_options['username'] = $dsn['username'];
$extra_options['passwd'] = $dsn['password'];

define('PEAR_AUTH_TEST_OPTIONS', serialize($options));
define('PEAR_AUTH_TEST_EXTRA_OPTIONS', serialize($extra_options));
