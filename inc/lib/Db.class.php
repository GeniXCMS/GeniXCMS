<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Database Class.
 *
 * This class will process the database queries, including Create, Edit, Delete
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.1
 */
class Db
{
    /** Num Rows Variable */
    public static $num_rows = '';

    /** Last Accessed ID Variable */
    public static $last_id = '';

    /** Mysqli db driver variable */
    public static $mysqli = '';

    /** PDO DB Driver variable */
    public static $pdo = '';

    /** Memcached var */
    public static $mem = '';

    /**
     * Database Initiation.
     *
     * This will initiate database connection before all
     * process.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        global $vars;
        if (defined('USE_MEMCACHED') && USE_MEMCACHED == true) {
            self::$mem = new Memcached();
            // self::cacheConnect('127.0.0.1', '11211');
            self::$mem->addServer('127.0.0.1', '11211');
            // $servers = self::$mem->getServerList();
            // var_dump($servers);
        }

        !defined('DB_DRIVER') ? define('DB_DRIVER', 'mysqli') : '';
        if (DB_DRIVER == 'mysqli') {
            try {
                self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if (self::$mysqli->connect_error) {
                    Control::error('db', self::$mysqli->connect_error);
                    exit;
                } else {
                    self::query("SET SESSION `sql_mode` = 'STRICT_ALL_TABLES'");

                    return true;
                }
            } catch (exception $e) {
                Control::error('db', $e->getMessage());
            }

            //return self::$mysqli;
        } elseif (DB_DRIVER == 'pdo') {
            # code...

            try {
                self::$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                Control::error('db', $e->getMessage());
            }
        }
    }

    /**
     * Database Connect Function.
     *
     * This will do a connection with the database.
     * This is called during the installation process. Using mysqli because of
     * the deprecation of mysql.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function connect(
        $dbhost = DB_HOST,
        $dbuser = DB_USER,
        $dbpass = DB_PASS,
        $dbname = DB_NAME
    ) {
        !defined('DB_DRIVER') ? define('DB_DRIVER', 'mysqli') : '';
        if (DB_DRIVER == 'mysqli') {
            self::$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

            if (self::$mysqli->connect_error) {
                return false;
            } else {
                return true;
            }
        } elseif (DB_DRIVER == 'pdo') {
            # code...

            try {
                self::$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                Control::error('db', $e->getMessage());
            }
        }
    }

    /**
     * Database Query Function.
     *
     * This will proccess database query.
     *
     * @param string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function query($vars)
    {
        if (DB_DRIVER == 'mysqli') {
            self::$mysqli->set_charset('utf8mb4');
            $q = self::$mysqli->query($vars);
            if ($q === false) {
                Control::error('db', 'Query failed: '.self::$mysqli->error."<br />\n");
            }
        } elseif (DB_DRIVER == 'pdo') {
            $q = self::$pdo->query($vars);
                // if ($q === false) {
                //     Control::error('db', 'Query failed: '.self::$pdo->error."<br />\n");
                // }
        }

        return $q;
    }

    /**
     * Database Result Function.
     *
     * This will query the database and output the
     * result as object.
     *
     * @param string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function result($vars)
    {
        //print_r($vars);
        $key = md5($vars).'-'.substr(SITE_ID, 0, 5);
        $key_n = 'num_'.md5($vars).'-'.substr(SITE_ID, 0, 5);
        // check memcached
        // $keys = self::$mem->getAllKeys();
        // var_dump($keys);
        $use_memcached = defined(USE_MEMCACHED) ? USE_MEMCACHED : false;
        if ($use_memcached) {
            # code...
            if ($r = self::$mem->get($key)) {
                # code...
                $n = self::$mem->get($key_n);
            } else {
                $res = self::fetch($vars);
                $r = $res['r'];
                $n = $res['n'];
                self::$mem->add($key, $r, time() + 300);
                self::$mem->add($key_n, $n, time() + 300);
            }
        } else {
            $res = self::fetch($vars);
            $r = $res['r'];
            $n = $res['n'];
            // print_r($r);
        }

        self::$num_rows = $n;

        return $r;
    }

    public static function fetch($vars)
    {
        if (DB_DRIVER == 'mysqli') {
            //echo $vars;
            $q = self::query($vars);
            $n = $q->num_rows;
            if ($n > 0) {
                for ($i = 0; $i < $n; ++$i) {
                    $r[] = $q->fetch_object();
                }
            } else {
                $r['error'] = 'data not found';
            }

            $q->close();
        } elseif (DB_DRIVER == 'pdo') {
            $stmt = self::query($vars);
            $r = $stmt->fetchAll(PDO::FETCH_OBJ);
            $n = $stmt->rowCount();
        }
        $res['n'] = $n;
        $res['r'] = $r;

        return $res;
    }

    /**
     * Delete Database Function.
     *
     * This will delete rows in the database with the certain 'where' value.
     * <code>
     *     $vars = array(
     *             'table' => 'table', // table name
     *             'where' => array(), // where
     *         );
     * </code>
     *
     * @param array $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function delete($vars)
    {
        if (is_array($vars)) {
            $where = '';
            foreach ($vars['where'] as $key => $val) {
                $val = self::escape($val);
                $where .= "`$key` = '$val' AND ";
            }
            $where = $where.' 1';
            $sql = sprintf('DELETE FROM `%s` WHERE %s ', $vars['table'], $where);
        } else {
            $sql = $vars;
        }
        if (DB_DRIVER == 'mysqli') {
            $q = self::query($sql);
        } elseif (DB_DRIVER == 'pdo') {
            $q = self::$pdo->exec($sql);
        }

        return true;
    }

    /**
     * Update Database Function.
     *
     * <code>
     *     $vars = array(
     *             'table' => 'table', // table name
     *             'id' => 'id', // item id
     *             'key' => array(
     *                         'col1' => 'col1_val',
     *                         'col2' => 'col2_val',
     *                         )
     *             )
     * </code>
     *
     * @param array $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function update($vars)
    {
        if (is_array($vars)) {
            $set = '';
            foreach ($vars['key'] as $key => $val) {
                $val = self::escape($val);
                $key = self::escape($key);
                $set .= "`$key` = '$val',";
            }

            $where = '1 ';
            if (isset($vars['where'])) {
                foreach ($vars['where'] as $key => $val) {
                    $val = self::escape($val);
                    $key = self::escape($key);
                    $where .= "AND `{$key}` = '{$val}' ";
                }
            }

            if (isset($vars['id'])) {
                $where .= "AND `id` = '{$vars['id']}' ";
            }

            $set = substr($set, 0, -1);
            $sql = sprintf("UPDATE `%s` SET %s WHERE %s LIMIT 1", $vars['table'], $set, $where);
        } else {
            $sql = $vars;
        }
        if (DB_DRIVER == 'mysqli') {
            $q = self::query($sql);
        } elseif (DB_DRIVER == 'pdo') {
            $q = self::$pdo->exec($sql);
        }

        return $q;
    }

    /**
     * Insert Database Function.
     *
     * This function will do insert the value into the
     * database.
     * <code>
     *     $vars = array(
     *                 'table' => 'table', // table name
     *                 'key' => array(
     *                         'col1' => 'col1_val',
     *                         'col2' => 'col2_val',
     *                         )
     *             )
     * </code>
     *
     * @param array $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function insert($vars)
    {
        if (is_array($vars)) {
            $set = '';
            $k = '';
            foreach ($vars['key'] as $key => $val) {
                $val = self::escape($val);
                $key = self::escape($key);
                $set .= "'{$val}',";
                $k .= "`{$key}`,";
            }

            $set = substr($set, 0, -1);
            $k = substr($k, 0, -1);

            $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s) ', $vars['table'], $k, $set);
        } else {
            $sql = $vars;
        }
        if (DB_DRIVER == 'mysqli') {
            try {
                if (!self::query($sql)) {
                    return false;
                } else {
                    self::$last_id = self::$mysqli->insert_id;

                    return true;
                }
            } catch (exception $e) {
                echo $e->getMessage();
            }
        } elseif (DB_DRIVER == 'pdo') {
            $q = self::$pdo->exec($sql);
            try {
                if (!$q) {
                    return false;
                } else {
                    self::$last_id = self::$pdo->lastInsertId();

                    return true;
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        //return true;
    }

    public static function escape($vars)
    {
        if (DB_DRIVER == 'mysqli') {
            $vars = self::$mysqli->escape_string($vars);
        } elseif (DB_DRIVER == 'pdo') {
            $vars = self::$pdo->quote($vars);
        } else {
            $vars = $vars;
        }

        return $vars;
    }

    public static function cacheConnect($host, $port)
    {
        $servers = self::$mem->getServerList();
        var_dump($servers);
        if (is_array($servers)) {
            foreach ($servers as $server) {
                if ($server['host'] == $host and $server['port'] == $port) {
                    return true;
                }
            }
        }

        return self::$mem->addServer($host, $port);
    }
}

/* End of file Db.class.php */
/* Location: ./inc/lib/Db.class.php */
