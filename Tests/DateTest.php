<?php

class DateTest extends PHPUnit_Framework_TestCase 
{

    public function __construct () {
        Db::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $options = "CREATE TABLE IF NOT EXISTS `options` (
                    `id` int(11) NOT NULL,
                      `name` text CHARACTER SET utf8 NOT NULL,
                      `value` longtext CHARACTER SET utf8 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        Db::query($options);
        $options = "INSERT INTO `options` (`id`, `name`, `value`) VALUES 
        (1, 'timezone', 'Asia/Jakarta')";
        Db::query($options);
    }

    public function testFormat () {
        $date = "2015-09-16 02:29:30";
        $dformat = Date::format($date);
        $this->assertEquals("16 September 2015 09:29 AM WIB", $dformat);

    }

    public function testLocal () {
        $date = "2015-09-16 02:29:30";
        $dformat = Date::format($date);
        $this->assertEquals("16 September 2015 09:29 AM WIB", $dformat);
    }

}