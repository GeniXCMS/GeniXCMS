<?php

if ($fp = @fopen('PHPUnit/Autoload.php', 'r', true)) {
    require_once 'PHPUnit/Autoload.php';
} elseif ($fp = @fopen('PHPUnit/Framework.php', 'r', true)) {
    require_once 'PHPUnit/Framework.php';
    require_once 'PHPUnit/TextUI/TestRunner.php';
} else {
    die('skip could not find PHPUnit');
}
fclose($fp);

if ('@php_dir@' == '@'.'php_dir'.'@') {
    // This package hasn't been installed.
    // Adjust path to ensure includes find files in working directory.
    set_include_path(dirname(dirname(__FILE__))
        . PATH_SEPARATOR . dirname(__FILE__)
        . PATH_SEPARATOR . get_include_path());
}

require_once 'Auth.php';

class TestAuthContainer extends PHPUnit_Framework_TestCase
{
    protected static $add_supported = false;
    protected $add_supported_tmp = false;

    // Abstract
    function &getContainer() {}
    function &getExtraOptions() {}

    function setUp()
    {
        $this->container =& $this->getContainer();

        if (!empty($this->container->db)) {
            $res = $this->container->db->query(file_get_contents(
                dirname(__FILE__) . '/mysql_test_db.sql'));
            if (PEAR::isError($res)) {
                $this->fail('setUp() could not create table: ' . $res->getUserInfo());
            }
        }

        $this->user = 'joe';
        $this->pass = 'doe';
        $this->opt = 'VeryCoolUser';
        // Nedded since lazy loading of container was introduced
        $this->container->_auth_obj = new Auth($this);

        $opt = $this->getExtraOptions();
        // Add the default user to be used for some testing
        $this->container->addUser($opt['username'], $opt['passwd']);
    }

    function tearDown()
    {
        $opt = $this->getExtraOptions();
        // Remove default user
        $this->container->removeUser($opt['username']);
        // Workaround for DBLite sillieness.
        self::$add_supported = $this->add_supported_tmp;
    }

    public static function tearDownAfterClass()
    {
        @unlink('users');
    }

    function testAddUser()
    {
        $cb = count($this->container->listUsers());
        $res = $this->container->addUser($this->user, $this->pass, $this->opt);
        if (AUTH_METHOD_NOT_SUPPORTED === $res) {
            $this->markTestSkipped("This operation is not supported by ".get_class($this->container));
        }

        // Workaround for DBLite sillieness.
        $this->add_supported_tmp = true;

        if (PEAR::isError($res)) {
            $this->fail($res->getUserInfo());
        }

        $ca = count($this->container->listUsers());
        $users = $this->container->listUsers();
        $last_username = $users[$ca-1]['username'];
        $this->assertTrue( ($cb === $ca-1) , sprintf('Count of users before (%s) and after (%s) does not differ by one', $cb, $ca));
        $this->assertTrue( $this->container->fetchData($this->user, $this->pass) , sprintf('Could not verify with the newly created user %s',$this->user));

        // Remove the user we just added, assumes removeUser works
        $this->container->removeUser($this->user);
    }

    function testListUsers()
    {
        $users = $this->container->listUsers();
        if (AUTH_METHOD_NOT_SUPPORTED === $users) {
            $this->markTestSkipped('This operation is not supported by '.get_class($this->container));
        }
        if (PEAR::isError($users)) {
            $this->fail($users->getUserInfo());
        }

        $opt = $this->getExtraOptions();
        $this->assertTrue(is_array($users[0]), 'First array element from result was not an array');
        $this->assertTrue($users[0]['username'] == $opt['username'], sprintf('First username was not equal to default username "%s" ', $opt['username']));
    }

    function testFetchData()
    {
        $opt = $this->getExtraOptions();
        $fetch_res = $this->container->fetchData($opt['username'], $opt['passwd']);
        if (AUTH_METHOD_NOT_SUPPORTED === $fetch_res) {
            $this->markTestSkipped("This operation is not supported by ".get_class($this->container));
        }
        if (PEAR::isError($fetch_res)) {
            $this->fail($fetch_res->getUserInfo());
        }

        // Workaround for DBLite sillieness.
        if (self::$add_supported) {
            $this->assertTrue($fetch_res,sprintf('Could not verify with the default username (%s) and passwd (%s)', $opt['username'], $opt['passwd']));
        }

        // Test for fail fetchData
        $opt = $this->getExtraOptions();
        $this->assertFalse(
            $this->container->fetchData(md5($opt['username']), $opt['passwd']),
            "fetchData returned true with invalid username and pass"
        );

    }


    /**
     * Tjis test depends on add user & remove user to work
     */
    function testFetchDataSpaceInPassword()
    {
        $user = uniqid('user');
        $pass = 'Some Pass ';

        $res = $this->container->addUser($user, $pass, array());
        if (AUTH_METHOD_NOT_SUPPORTED === $res) {
            $this->markTestSkipped("This operation is not supported by ".get_class($this->container));
        }
        if (PEAR::isError($res)) {
            $this->fail($res->getUserInfo());
        }

        $fetch_res = $this->container->fetchData($user, $pass);
        if (AUTH_METHOD_NOT_SUPPORTED === $fetch_res) {
            $this->markTestSkipped("This operation is not supported by ".get_class($this->container));
        }
        if (PEAR::isError($fetch_res)) {
            $this->fail($fetch_res->getUserInfo());
        }

        $this->assertTrue($fetch_res, 'Could not verify user with space password');

        $remove_res = $this->container->removeUser($user);
    }




    function testRemoveUser()
    {
        // Add a user to be removed when testing removeUuser method
        // Assume add user works
        $this->container->addUser('for_remove', 'for_remove');
        $cb = count($this->container->listUsers());
        $remove_res = $this->container->removeUser('for_remove');
        if (AUTH_METHOD_NOT_SUPPORTED === $remove_res) {
            $this->markTestSkipped("This operation is not supported by ".get_class($this->container));
        }
        if (PEAR::isError($remove_res)) {
            $this->fail($remove_res->getUserInfo());
        }

        $ca = count($this->container->listUsers());
        $this->assertTrue($cb === $ca+1, sprintf('Could not remove user "%s", count before:%s count after:%s ', 'for_remove', $cb, $ca));
    }

}

?>
