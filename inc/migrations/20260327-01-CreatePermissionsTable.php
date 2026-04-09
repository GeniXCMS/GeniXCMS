<?php

class Migration_20260327_01_CreatePermissionsTable extends Migration
{
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `permissions` (
            `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `group_id` int(11) NOT NULL,
            `permission` varchar(100) NOT NULL,
            `status` tinyint(1) NOT NULL DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        Db::query($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE IF EXISTS `permissions` ";
        Db::query($sql);
    }
}
