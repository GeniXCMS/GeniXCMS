<?php

class CategoriesTest extends PHPUnit_Framework_TestCase 
{
    public function __construct () {
        new Db();
    }
    public function testAddCategories () {
        $vars = array(
            'table' => 'cat',
            'key' => array(
                'name' => 'Categories',
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
        $this->assertEqual($expected, $db);
    }
    
    public function testNameCategories () {
        $db = Categories::name(1);
        $expected = 'Categories';
        $this->assertEqual($expected, $db);
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
