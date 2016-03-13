<?php
/**
 * Unit tests for HTTP_Request2 package
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to BSD 3-Clause License that is bundled
 * with this package in the file LICENSE and available at the URL
 * https://raw.github.com/pear/HTTP_Request2/trunk/docs/LICENSE
 *
 * @category  HTTP
 * @package   HTTP_Request2
 * @author    Alexey Borzov <avb@php.net>
 * @copyright 2008-2016 Alexey Borzov <avb@php.net>
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link      http://pear.php.net/package/HTTP_Request2
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    if (strpos($_SERVER['argv'][0], 'phpunit') === false) {
        define('PHPUnit_MAIN_METHOD', 'Request2_Adapter_AllTests::main');
    } else {
        define('PHPUnit_MAIN_METHOD', false);
    }
}

require_once dirname(__FILE__) . '/MockTest.php';
require_once dirname(__FILE__) . '/SkippedTests.php';
require_once dirname(__FILE__) . '/SocketTest.php';
require_once dirname(__FILE__) . '/SocketProxyTest.php';
require_once dirname(__FILE__) . '/CurlTest.php';

class Request2_Adapter_AllTests
{
    public static function main()
    {
        if (!class_exists('PHPUnit_TextUI_TestRunner', true)) {
            require_once 'PHPUnit/TextUI/TestRunner.php';
        }
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HTTP_Request2 package - Request2 - Adapter');

        $suite->addTestSuite('HTTP_Request2_Adapter_MockTest');
        if (defined('HTTP_REQUEST2_TESTS_BASE_URL') && HTTP_REQUEST2_TESTS_BASE_URL) {
            $suite->addTestSuite('HTTP_Request2_Adapter_SocketTest');
        } else {
            $suite->addTestSuite('HTTP_Request2_Adapter_Skip_SocketTest');
        }
        if (defined('HTTP_REQUEST2_TESTS_PROXY_HOST') && HTTP_REQUEST2_TESTS_PROXY_HOST
            && defined('HTTP_REQUEST2_TESTS_BASE_URL') && HTTP_REQUEST2_TESTS_BASE_URL
        ) {
            $suite->addTestSuite('HTTP_Request2_Adapter_SocketProxyTest');
        } else {
            $suite->addTestSuite('HTTP_Request2_Adapter_Skip_SocketProxyTest');
        }
        if (defined('HTTP_REQUEST2_TESTS_BASE_URL') && HTTP_REQUEST2_TESTS_BASE_URL
            && extension_loaded('curl')
        ) {
            $suite->addTestSuite('HTTP_Request2_Adapter_CurlTest');
        } else {
            $suite->addTestSuite('HTTP_Request2_Adapter_Skip_CurlTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Request2_Adapter_AllTests::main') {
    Request2_Adapter_AllTests::main();
}
?>
