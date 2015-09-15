<?php

class ControlTest extends PHPUnit_Framework_TestCase 
{

    public function testGetControl () {
        $_GET['ajax'] = '';
        $arr = array ('ajax', 'post' ,'page', 'cat', 'mod', 'sitemap', 'rss', 'pay',
            'paidorder', 'cancelorder');
        if(Control::get($arr)) {
            $get = true;
        }else{
            $get = false;
        }
        $this->assertTrue($get);
    }

}