<?php

class DateTest extends PHPUnit_Framework_TestCase 
{

    public function testFormat () {
        $date = "2015-09-16 02:29:30";
        echo Date::format($date);

    }

}