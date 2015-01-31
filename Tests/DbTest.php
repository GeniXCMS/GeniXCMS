<?php

class DbTest extends PHPUnit_Framework_TestCase
{
    private $pdo;

    public function setUp()
    {
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