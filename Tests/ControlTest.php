<?php

class ControlTest extends \PHPUnit_Framework_TestCase 
{

    public function getControlTest () {
        $arr = array('ajax', 'post');
        if(Control::get($arr)){
            $result = true;
        }else{
            $result = false;
        }

        $this->assertTrue($result);

    }

}