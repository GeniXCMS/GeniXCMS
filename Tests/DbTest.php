<?php

class DbTest extends PHPUnit_Framework_TestCase
{

    public function __construct() {
        Db::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        Db::query("CREATE TABLE IF NOT EXIST `test_table` (what VARCHAR(50) NOT NULL)");
    }
    

    public function testQuery () {
        $expected = 4;
        $result = Db::query('SELECT * FROM `test_table`');
        
        $this->assertEquals($expected, Db::$num_rows);
    }


    public function testTearDown()
    {
        Db::query("DROP TABLE `test_table`");
    }

}
