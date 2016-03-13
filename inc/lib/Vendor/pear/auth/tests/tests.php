<?php

include_once 'Auth.php';
include_once 'TestAuthContainer.php';
include_once 'FileContainer.php';
include_once 'DBContainer.php';
include_once 'DBLiteContainer.php';
include_once 'MDBContainer.php';
include_once 'MDB2Container.php';
include_once 'POP3Container.php';
include_once 'POP3aContainer.php';
include_once 'IMAPContainer.php';
include_once 'PHPUnit.php';


function error($err){
    print "Error\n";
    print "Code:".trim($err->getCode())."\n";
    print "Message:".trim($err->getMessage())."\n";
    #print "UserInfo:".trim($err->getUserInfo())."\n";
    #print "DebugInfo:".trim($err->getDebugInfo())."\n";

}

#error_reporting(0);
PEAR::setErrorHandling(PEAR_ERROR_PRINT, "\nPear Error:%s \n");
#PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, "error");

set_time_limit(0);

$suite = new PHPUnit_TestSuite();

// File Container
#$suite->addTest(new PHPUnit_TestSuite('IMAPContainer'));
$suite->addTest(new PHPUnit_TestSuite('FileContainer'));
$suite->addTest(new PHPUnit_TestSuite('DBContainer'));
//$suite->addTest(new PHPUnit_TestSuite('DBLiteContainer'));
// MDB Container
$suite->addTest(new PHPUnit_TestSuite('MDBContainer'));
// MDB2 Container
$suite->addTest(new PHPUnit_TestSuite('MDB2Container'));
// POP3 Container
$suite->addTest(new PHPUnit_TestSuite('POP3Container'));

$result = PHPUnit::run($suite);
echo $result->toString();

?>
