<?php

class DbTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        //System::config('config');
        define('DB_DRIVER', 'mysqli');
        define('DBHOST', 'localhost');
        define('DBUSER', 'root');
        $link = mysqli_connect(DBHOST, DBUSER, '', TRUE);
        mysqli_select_db($link, 'db_test');
        Db::query("CREATE TABLE test_table (what VARCHAR(50) NOT NULL)");
    }

    public function tearDown()
    {
        Db::query("DROP TABLE test_table");
    }

    public function testquery () {

        $this->assertEquals('SELECT * FROM `test_table`', Db::query());
    }

}