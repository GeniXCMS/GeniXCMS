<?php

class CategoriesTest extends PHPUnit_Framework_TestCase 
{
    public function __construct () {
        Db::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $cat = "CREATE TABLE IF NOT EXISTS `cat` (
                `id` int(11) NOT NULL,
                  `name` text NOT NULL,
                  `slug` text NOT NULL,
                  `parent` text DEFAULT NULL,
                  `desc` text DEFAULT  NULL,
                  `type` text NOT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ";
        Db::query($cat);

        $vars = array(
            'table' => 'cat',
            'key' => array(
                'id' => '1',
                'name' => 'Categories',
                'parent' => '0',
                'type' => 'post'
            )
        );

        Db::insert($vars);
    }
    public function testAddCategories () {
        $vars = array(
            'table' => 'cat',
            'key' => array(
                'name' => 'Categories2',
                'parent' => '0',
                'type' => 'post'
            )
        );

        $db = Db::insert($vars);
        if($db) {
            $result = true;
        }else{
            $result = false;
        }
        $this->assertTrue($result);
    }
    
    public function testTypeCategories () {

        $db = Categories::type(1);
        $expected = 'post';
        $this->assertEquals($expected, $db);
    }
    
    public function testNameCategories () {

        $db = Categories::name(1);
        $expected = 'Categories';
        $this->assertEquals($expected, $db);
    }
    
    
    public function testDeleteCategories () {
        $db = Categories::delete(1);
        if($db) {
            $result = true;
        }else{
            $result = false;
        }
        $this->assertTrue($result);
    }
}
