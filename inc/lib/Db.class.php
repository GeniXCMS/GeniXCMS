<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 2.0.0-alpha
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Database Class.
 *
 * This class will process the database queries, including Create, Edit, Delete
 * Now enhanced with PDO support for MySQL, PostgreSQL, and SQLite.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 *
 * @since 0.0.1
 */
class Db
{
    /** Num Rows Variable */
    public static $num_rows = 0;

    /** Last Accessed ID Variable */
    public static $last_id = '';

    /** PDO DB Driver variable */
    public static $pdo = null;

    /** Memcached var */
    public static $mem = null;

    /**
     * Database Initiation.
     *
     * This will initiate database connection before all
     * process.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public function __construct()
    {
        if (defined('USE_MEMCACHED') && USE_MEMCACHED == true && class_exists('Memcached')) {
            self::$mem = new Memcached();
            self::$mem->addServer('127.0.0.1', '11211');
        }

        if (self::existConf()) {
            self::connect();
        }
    }

    private static function existConf()
    {
        return defined('DB_NAME') && defined('DB_DRIVER');
    }

    /**
     * Database Connect Function.
     *
     * This will do a connection with the database using PDO.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function connect(
        $dbhost = DB_HOST,
        $dbuser = DB_USER,
        $dbpass = DB_PASS,
        $dbname = DB_NAME,
        $driver = DB_DRIVER
    ) {
        if (self::$pdo !== null) {
            return true;
        }

        try {
            switch ($driver) {
                case 'pgsql':
                    $dsn = "pgsql:host=$dbhost;dbname=$dbname";
                    self::$pdo = new PDO($dsn, $dbuser, $dbpass);
                    break;
                case 'sqlite':
                    // For SQLite, dbname is the path to the database file
                    $dsn = "sqlite:$dbname";
                    self::$pdo = new PDO($dsn);
                    break;
                case 'mysql':
                case 'mysqli':
                default:
                    $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4";
                    self::$pdo = new PDO($dsn, $dbuser, $dbpass);
                    self::$pdo->exec("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
                    break;
            }

            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            return true;
        } catch (PDOException $e) {
            if (defined('INSTALL')) {
                return false;
            }
            Control::error('db', $e->getMessage());
            return false;
        }
    }

    /**
     * Database Query Function.
     *
     * This will process database query using prepared statements for security.
     *
     * @param string $sql
     * @param array  $params
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function query($sql, $params = [])
    {
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);

            self::$num_rows = $stmt->rowCount();
            return $stmt;
        } catch (PDOException $e) {
            $msg = (defined('DEBUG') && DEBUG)
                ? 'Query failed: ' . $e->getMessage() . "<br />\nSQL: " . $sql
                : 'Database error occurred.';
            Control::error('db', $msg);
            return false;
        }
    }

    /**
     * Database Result Function.
     *
     * This will query the database and output the
     * result as object.
     *
     * @param string $sql
     * @param array  $params
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function result($sql, $params = [])
    {
        $key = 'db_res_' . md5($sql . serialize($params)) . '-' . (defined('SITE_ID') ? substr(SITE_ID, 0, 5) : '');
        $use_memcached = defined('USE_MEMCACHED') ? USE_MEMCACHED : false;

        if ($use_memcached && self::$mem) {
            if ($r = self::$mem->get($key)) {
                return $r;
            }
        }

        $stmt = self::query($sql, $params);
        if ($stmt) {
            $r = $stmt->fetchAll();
            if (empty($r)) {
                $r = [];
                self::$num_rows = 0;
            } else {
                // We keep num_rows consistent with the result count for selects
                self::$num_rows = count($r);
            }

            if ($use_memcached && self::$mem && !isset($r['error'])) {
                self::$mem->add($key, $r, time() + 300);
            }
            return $r;
        }

        return [];
    }

    /**
     * Delete Database Function.
     *
     * This will delete rows in the database with the certain 'where' value.
     * Now uses prepared statements.
     *
     * @param array|string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function delete($vars)
    {
        if (is_array($vars)) {
            $where = '';
            $params = [];
            foreach ($vars['where'] as $key => $val) {
                $where .= self::quoteIdentifier($key) . " = ? AND ";
                $params[] = $val;
            }
            $where = rtrim($where, ' AND ');
            $sql = sprintf('DELETE FROM %s WHERE %s', self::quoteIdentifier($vars['table']), $where);
            return self::query($sql, $params);
        } else {
            return self::query($vars);
        }
    }

    /**
     * Update Database Function.
     *
     * Now uses prepared statements.
     *
     * @param array|string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function update($vars)
    {
        if (is_array($vars)) {
            $set = '';
            $params = [];
            foreach ($vars['key'] as $key => $val) {
                $set .= self::quoteIdentifier($key) . " = ?, ";
                $params[] = $val;
            }
            $set = rtrim($set, ', ');

            $where = '1=1';
            if (isset($vars['where'])) {
                foreach ($vars['where'] as $key => $val) {
                    $where .= " AND " . self::quoteIdentifier($key) . " = ?";
                    $params[] = $val;
                }
            }

            if (isset($vars['id'])) {
                $where .= " AND " . self::quoteIdentifier('id') . " = ?";
                $params[] = $vars['id'];
            }

            $sql = sprintf("UPDATE %s SET %s WHERE %s", self::quoteIdentifier($vars['table']), $set, $where);
            return self::query($sql, $params);
        } else {
            return self::query($vars);
        }
    }

    /**
     * Insert Database Function.
     *
     * Now uses prepared statements.
     *
     * @param array|string $vars
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function insert($vars)
    {
        if (is_array($vars)) {
            $cols = [];
            $placeholders = [];
            $params = [];
            foreach ($vars['key'] as $key => $val) {
                $cols[] = self::quoteIdentifier($key);
                $placeholders[] = '?';
                $params[] = $val;
            }

            $sql = sprintf(
                'INSERT INTO %s (%s) VALUES (%s)',
                self::quoteIdentifier($vars['table']),
                implode(', ', $cols),
                implode(', ', $placeholders)
            );
            
            $stmt = self::query($sql, $params);
            if ($stmt) {
                self::$last_id = self::$pdo->lastInsertId();
                return true;
            }
            return false;
        } else {
            return self::query($vars);
        }
    }

    /**
     * Escape variable.
     *
     * @deprecated Use prepared statements instead.
     */
    public static function escape($vars)
    {
        return $vars;
    }

    /**
     * Quote identifiers (tables, columns) based on driver.
     */
    public static function quoteIdentifier($identifier)
    {
        $driver = defined('DB_DRIVER') ? DB_DRIVER : 'mysql';
        $quote = ($driver == 'mysql' || $driver == 'mysqli') ? '`' : '"';
        // Handle dotted (table.column) notation
        if (strpos($identifier, '.') !== false) {
            return implode('.', array_map(function($part) use ($quote) {
                return $quote . trim($part, $quote) . $quote;
            }, explode('.', $identifier)));
        }
        return $quote . trim($identifier, $quote) . $quote;
    }

    public static function close()
    {
        self::$pdo = null;
        return true;
    }
}

/* End of file Db.class.php */
/* Location: ./inc/lib/Db.class.php */
