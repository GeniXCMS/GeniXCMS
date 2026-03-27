<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

class Migration_20260327_02_UpdateCategoriesTable extends Migration
{
    public function up()
    {
        // Add image column to cat table
        Db::query("ALTER TABLE `cat` ADD COLUMN `image` TEXT NULL AFTER `parent` ");
        
        // Ensure description column (if it exists) is large enough. 
        // Based on previous analysis, there's a column at index 4 (0-based) which is likely 'desc' or 'description'.
        // Let's check the actual name. Most GeniXCMS tables use 'desc'.
        // We'll try to rename it or add it if missing, but usually it exists.
        // To be safe, we'll try to add it only if it doesn't exist, but ALTER TABLE usually fails if it's already there.
        // We'll just add 'image' for now.
    }

    public function down()
    {
        Db::query("ALTER TABLE `cat` DROP COLUMN `image` ");
    }
}
