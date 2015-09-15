<?php

class ControlTest extends PHPUnit_Framework_TestCase 
{

    public function testGetControl () {
        $_GET['ajax'] = '';
        $arr = array ('ajax', 'post' ,'page', 'cat', 'mod', 'sitemap', 'rss', 'pay',
            'paidorder', 'cancelorder');
        $get = Control::get($arr);
        $this->assertTrue($get);
    }

}