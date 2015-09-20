<?php

class DbTest extends PHPUnit_Framework_TestCase
{

    public function __construct() {
        Db::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        Db::query("DROP TABLE IF EXISTS `test_table`");
        Db::query("CREATE TABLE IF NOT EXISTS `test_table` (what VARCHAR(50) NOT NULL)");
        Db::query("TRUNCATE TABLE `test_table`");
    }
    

    public function testQuery () {

        $result = Db::query('SELECT * FROM `test_table`');
        if ($result->num_rows > 0) {
            $return = true;
        }else{
            $return = false;
        }
        $this->assertTrue($return);
    }


    public function testTearDown()
    {
        Db::query("DROP TABLE `test_table`");
    }

}
