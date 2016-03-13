--TEST--
Regression test for bug #16044: Auth_Container_RADIUS and MSCHAPv1
--FILE--
<?php
set_include_path(
    dirname(dirname(__FILE__))
    . PATH_SEPARATOR . get_include_path()
);

//require_once 'PEAR.php';
require_once 'Auth.php';
require_once 'Auth/Container/RADIUS.php';

//simulate radius extension enough to run the test
if (!function_exists('radius_auth_open')) {
    define('RADIUS_ACCESS_REQUEST', 1);
    define('RADIUS_NAS_IDENTIFIER', 2);
    define('RADIUS_NAS_PORT_TYPE', 3);
    define('RADIUS_VIRTUAL', 4);
    define('RADIUS_SERVICE_TYPE', 5);
    define('RADIUS_FRAMED', 6);
    define('RADIUS_FRAMED_PROTOCOL', 7);
    define('RADIUS_PPP', 8);
    define('RADIUS_CALLING_STATION_ID', 9);
    define('RADIUS_USER_NAME', 10);
    define('RADIUS_VENDOR_MICROSOFT', 11);
    define('RADIUS_MICROSOFT_MS_CHAP_CHALLENGE', 12);
    define('RADIUS_MICROSOFT_MS_CHAP_RESPONSE', 13);
    define('RADIUS_MICROSOFT_MS_CHAP2_RESPONSE', 14);
    define('RADIUS_ACCESS_ACCEPT', 15);

    function radius_auth_open()
    {
        return true;
    }
    function radius_create_request()
    {
        return true;
    }
    function radius_put_attr()
    {
        return true;
    }
    function radius_put_vendor_attr()
    {
        return true;
    }
    function radius_put_int()
    {
        return true;
    }
    function radius_send_request()
    {
        return true;
    }
    function radius_get_attr()
    {
        return true;
    }
}

$acr = new Auth_Container_RADIUS(
    array(
        'servers'  => array(),
        'authtype' => 'MSCHAPv1'
    )
);
$acr->fetchData('username', 'password');

$acr = new Auth_Container_RADIUS(
    array(
        'servers'  => array(),
        'authtype' => 'MSCHAPv2'
    )
);
$acr->fetchData('username', 'password');

echo "done\n";
--EXPECT--
done
