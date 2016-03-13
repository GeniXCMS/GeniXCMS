<?php
error_reporting(22527);

// Keep tests from running twice when calling this file directly via PHPUnit.
$call_main = false;
if (strpos($_SERVER['argv'][0], 'phpunit') === false) {
    // Called via php, not PHPUnit.  Pass the request to PHPUnit.
    if (!defined('PHPUnit_MAIN_METHOD')) {
        /** The test's main method name */
        define('PHPUnit_MAIN_METHOD', 'Auth_AllTests::main');
        $call_main = true;
    }
}

require_once dirname(__FILE__) . '/DBContainer.php';
require_once dirname(__FILE__) . '/DBLiteContainer.php';
require_once dirname(__FILE__) . '/FileContainer.php';
require_once dirname(__FILE__) . '/IMAPContainer.php';
require_once dirname(__FILE__) . '/MDB2Container.php';
require_once dirname(__FILE__) . '/MDBContainer.php';
require_once dirname(__FILE__) . '/POP3Container.php';
require_once dirname(__FILE__) . '/POP3aContainer.php';

class Auth_AllTests
{
    public static function main()
    {

        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Auth Tests');

        $suite->addTestSuite('DBContainer');
        $suite->addTestSuite('DBLiteContainer');
        $suite->addTestSuite('FileContainer');
        $suite->addTestSuite('IMAPContainer');
        $suite->addTestSuite('MDB2Container');
        $suite->addTestSuite('MDBContainer');
        $suite->addTestSuite('POP3Container');
        $suite->addTestSuite('POP3aContainer');

        return $suite;
    }
}


// exec test suite
if ($call_main) {
    Auth_AllTests::main();
}
?>
