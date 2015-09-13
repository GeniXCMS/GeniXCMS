<?php

class DbTest extends PHPUnit_Framework_TestCase
{
    static $mysqli = '';

    public function setUp()
    {
        //System::config('config');
        define('DB_DRIVER', 'mysqli');
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', 'db_test');
        self::$mysqli = Db::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        //mysqli_select_db($link, 'db_test');
        Db::query("CREATE TABLE `test_table` (what VARCHAR(50) NOT NULL)");
        
        $cat = "CREATE TABLE IF NOT EXISTS `cat` (
                `id` int(11) NOT NULL,
                  `name` text NOT NULL,
                  `slug` text NOT NULL,
                  `parent` text DEFAULT NULL,
                  `desc` text DEFAULT  NULL,
                  `type` text NOT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ";
        Db::query($cat);
        $pr = "ALTER TABLE `cat` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `cat` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        $cat_param = "CREATE TABLE IF NOT EXISTS `cat_param` (
                    `id` int(11) NOT NULL,
                      `cat_id` int(11) NOT NULL,
                      `param` text CHARACTER SET utf8 NOT NULL,
                      `value` text CHARACTER SET utf8 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        Db::query($cat_param);
        $pr = "ALTER TABLE `cat_param` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `cat_param` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        $menu = "CREATE TABLE IF NOT EXISTS `menus` (
                `id` int(11) NOT NULL,
                  `name` varchar(64) NOT NULL,
                  `menuid` varchar(32) NOT NULL,
                  `parent` varchar(11) DEFAULT  NULL,
                  `sub` enum('0','1') NOT NULL,
                  `type` varchar(8) NOT NULL,
                  `value` text NOT NULL,
                  `class` varchar(64) DEFAULT NULL,
                  `order` varchar(4) DEFAULT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        Db::query($menu);
        $pr = "ALTER TABLE `menus` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `menus` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        $options = "CREATE TABLE IF NOT EXISTS `options` (
                    `id` int(11) NOT NULL,
                      `name` text CHARACTER SET utf8 NOT NULL,
                      `value` longtext CHARACTER SET utf8 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        Db::query($options);
        $pr = "ALTER TABLE `options` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `options` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        $posts = "CREATE TABLE IF NOT EXISTS `posts` (
                `id` bigint(32) NOT NULL,
                  `date` datetime NOT NULL,
                  `title` text NOT NULL,
                  `slug` text NOT NULL,
                  `content` mediumtext NOT NULL,
                  `author` text NOT NULL,
                  `type` text NOT NULL,
                  `cat` varchar(11) DEFAULT NULL,
                  `modified` datetime DEFAULT NULL,
                  `status` enum('0','1','2') NOT NULL,
                  `views` int(11) NOT NULL DEFAULT '0'
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        Db::query($posts);
        $pr = "ALTER TABLE `posts` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `posts` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        $post_param = "CREATE TABLE IF NOT EXISTS `posts_param` (
                `id` bigint(32) NOT NULL,
                  `post_id` bigint(32) NOT NULL,
                  `param` text NOT NULL,
                  `value` text NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        Db::query($post_param);
        $pr = "ALTER TABLE `posts_param` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `posts_param` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        $user = "CREATE TABLE IF NOT EXISTS `user` (
                `id` bigint(32) NOT NULL,
                  `userid` varchar(16) NOT NULL,
                  `pass` varchar(255) NOT NULL,
                  `confirm` varchar(255) DEFAULT NULL,
                  `group` enum('0','1','2','3','4','5') NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `join_date` datetime NOT NULL,
                  `status` enum('0','1') NOT NULL,
                  `activation` text
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        Db::query($user);
        $pr = "ALTER TABLE `user` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `user` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        $user_detail = "CREATE TABLE IF NOT EXISTS `user_detail` (
                `id` bigint(20) NOT NULL,
                  `userid` varchar(32)  NOT NULL,
                  `fname` varchar(32)  DEFAULT NULL,
                  `lname` varchar(255)  DEFAULT NULL,
                  `sex` varchar(2)  DEFAULT NULL,
                  `birthplace` varchar(32)  DEFAULT NULL,
                  `birthdate` date DEFAULT NULL,
                  `addr` varchar(255)  DEFAULT NULL,
                  `city` varchar(255)  DEFAULT NULL,
                  `state` varchar(255)  DEFAULT NULL,
                  `country` varchar(255)  DEFAULT NULL,
                  `postcode` varchar(32)  DEFAULT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        Db::query($user_detail);
        $pr = "ALTER TABLE `user_detail` ADD PRIMARY KEY (`id`)";
        Db::query($pr);
        $pr = "ALTER TABLE `user_detail` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT";
        Db::query($pr);
        //return self::$mysqli;
    }

    public function tearDown()
    {
        Db::query("DROP TABLE test_table");
    }

    public function testquery () {
        $expected = '';
        $result = Db::query('SELECT * FROM `test_table`');
        
        $this->assertEquals($expected, Db::$num_rows);
    }

}
