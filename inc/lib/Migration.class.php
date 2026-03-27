<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.0
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

abstract class Migration
{
    abstract public function up();
    abstract public function down();

    public static function init()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `migrations` (
            `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
            `migration` varchar(255) NOT NULL,
            `batch` int(11) NOT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        Db::query($sql);
    }

    public static function run()
    {
        self::init();
        
        $ran = Db::result("SELECT `migration` FROM `migrations`") ?? [];
        $ran_list = [];
        if(!isset($ran['error'])) {
            foreach($ran as $r) {
                $ran_list[] = $r->migration;
            }
        }

        $migration_files = glob(GX_PATH . '/inc/migrations/*.php');
        sort($migration_files);

        $batch = self::getLastBatch() + 1;
        $count = 0;

        foreach ($migration_files as $file) {
            $name = basename($file, '.php');
            if (!in_array($name, $ran_list)) {
                require_once $file;
                // Convention: Migration_20240327_CreateNewTable
                $class = 'Migration_' . str_replace('-', '_', $name);
                if (class_exists($class)) {
                    $m = new $class();
                    try {
                        $m->up();
                        Db::query("INSERT INTO `migrations` (`migration`, `batch`) VALUES (?, ?)", [$name, $batch]);
                        echo "Migrating: $name [DONE]\n";
                        $count++;
                    } catch (Exception $e) {
                        echo "Error migrating $name: " . $e->getMessage() . "\n";
                        break;
                    }
                }
            }
        }

        if ($count === 0) {
            echo "Nothing to migrate.\n";
        }
    }

    public static function getLastBatch()
    {
        $res = Db::result("SELECT MAX(`batch`) as max_batch FROM `migrations` LIMIT 1");
        return (isset($res[0]->max_batch)) ? (int)$res[0]->max_batch : 0;
    }

    public static function rollback()
    {
        self::init();
        $batch = self::getLastBatch();
        if ($batch === 0) {
            echo "Nothing to rollback.\n";
            return;
        }

        $migrations = Db::result("SELECT `migration` FROM `migrations` WHERE `batch` = ? ORDER BY `id` DESC", [$batch]);
        if (!isset($migrations['error'])) {
            foreach ($migrations as $m) {
                $name = $m->migration;
                $file = GX_PATH . '/inc/migrations/' . $name . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    $class = 'Migration_' . str_replace('-', '_', $name);
                    if (class_exists($class)) {
                        $mo = new $class();
                        try {
                            $mo->down();
                            Db::query("DELETE FROM `migrations` WHERE `migration` = ?", [$name]);
                            echo "Rolled back: $name [DONE]\n";
                        } catch (Exception $e) {
                            echo "Error rolling back $name: " . $e->getMessage() . "\n";
                        }
                    }
                }
            }
        }
    }
}
