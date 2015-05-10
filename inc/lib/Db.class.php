<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* Database Class
*
* This class will process the database queries, including Create, Edit, Delete
* 
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class Db
{
    /** Num Rows Variable */
    static $num_rows = "";

    /** Last Accessed ID Variable */
    static $last_id = "";

    /** Mysqli db driver variable */
    static $mysqli = '';


    /**
    * Database Initiation.
    * This will initiate database connection before all process.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public function __construct () {
        global $vars;
        if(DB_DRIVER == 'mysql') {
            mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
        }elseif(DB_DRIVER == 'mysqli') {
            try {
                self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if (self::$mysqli->connect_error) {
                    Control::error('db', self::$mysqli->connect_error);
                    exit;
                }else{
                    return true;
                }
            } catch (exception $e) {
                Control::error('db', $e->getMessage() );
            }
            
            //return self::$mysqli;
        }
    }


    /**
    * Database Connect Function.
    * This will do a connection with the database. This is called during the 
    * installation process. Using mysqli because of the deprecation of mysql.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function connect ($dbhost=DB_HOST, $dbuser=DB_USER, 
        $dbpass=DB_PASS, $dbname=DB_NAME) {
        
        self::$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        
        if (self::$mysqli->connect_error) {
            return false;
        }else{
            return true;
        }

    }


    /**
    * Database Query Function.
    * This will proccess database query. 
    *
    * @param string $vars 
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function query ($vars) {
        if(DB_DRIVER == 'mysql') {
            mysql_query('SET CHARACTER SET utf8');
            $q = mysql_query($vars)  or die(mysql_error());
        } 
        elseif(DB_DRIVER == 'mysqli') {
            self::$mysqli->set_charset("utf8");
            $q = self::$mysqli->query($vars) ;
            if($q === false) {
                Control::error('db',"Query failed: ".self::$mysqli->error."<br />\n"); 
            }
        }
        
        return $q;
    }

    
    /**
    * Database Result Function.
    * This will query the database and output the result as object.
    * 
    * @param string $vars 
    * 
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function result ($vars) {
        //print_r($vars);
        if(DB_DRIVER == 'mysql') {
            mysql_query('SET CHARACTER SET utf8');
            $q = mysql_query($vars)  or die(mysql_error());
            $n = mysql_num_rows($q);
            if($n > 0){
                for($i=0;$i<$n;$i++){
                    $r[] = mysql_fetch_object($q);
                }
            }else{
                $r['error'] = 'data not found';
            }
            
           
        } 
        elseif(DB_DRIVER == 'mysqli') {
           //echo $vars;
            $q = self::query($vars);
            $n = $q->num_rows;
            if($n > 0){
                for($i=0;$i<$n;$i++){
                    $r[] = $q->fetch_object();
                }
            }else{
                $r['error'] = 'data not found';
            }

            $q->close();

        }

        self::$num_rows = $n;
        return $r;
    }

    /**
    * Delete Database Function.
    * This will delete rows in the database with the certain 'where' value.
    * <code>
    *    $vars = array(
    *                'table' =>    'table', // table name
    *                'where'    =>    array(), // where
    *            );
    * </code>
    *
    * @param array $vars
    * 
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function delete ($vars) {
        if(is_array($vars)){
            $where = '';
            foreach ($vars['where'] as $key => $val) {
                $val = self::escape($val);
                $where .= "`$key` = '$val' AND ";
            }
            $where = $where." 1";
            $sql = sprintf("DELETE FROM `%s` WHERE %s LIMIT 1", $vars['table'], $where);
        }else{
            $sql = $vars;
        }
        if(DB_DRIVER == 'mysql') {
            mysql_query('SET CHARACTER SET utf8');
            $q = mysql_query($sql) or die(mysql_error());
        }elseif(DB_DRIVER == 'mysqli'){
            $q = self::query($sql);
        }
        return true;

    }

    /**
    * Update Database Function. 
    * <code>
    *    $vars = array(
    *                'table' =>    'table', // table name
    *                'id'    =>    'id', // item id
    *                'key'    =>    array(
    *                                    'col1' => 'col1_val',
    *                                    'col2' => 'col2_val',
    *                                )
    *            )
    * </code>
    *
    * @param array $vars
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function update ($vars) {
        if(is_array($vars)){
            $set = "";
            foreach ($vars['key'] as $key => $val) {
                $val = self::escape($val);
                $set .= "`$key` = '$val',";
            }
            

            $set = substr($set, 0,-1);
            //echo $set;
            $sql = sprintf("UPDATE `%s` SET %s WHERE `id` = '%d' LIMIT 1", $vars['table'], $set, $vars['id']);
        }else{
            $sql = $vars;
        }
        if(DB_DRIVER == 'mysql') {
            mysql_query('SET CHARACTER SET utf8');
            $q = mysql_query($sql) or die(mysql_error());
        }elseif(DB_DRIVER == 'mysqli'){
            $q = self::query($sql);
        }
        return true;
    }

    /**
    * Insert Database Function.
    * This function will do insert the value into the database.
    * <code>
    *    $vars = array(
    *                'table' =>    'table', // table name
    *                'key'    =>    array(
    *                                    'col1' => 'col1_val',
    *                                    'col2' => 'col2_val',
    *                                )
    *            )
    * </code>
    *
    * @param array $vars
    *
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function insert ($vars) {
        if(is_array($vars)){
            $set = "";
            $k = "";
            //print_r($vars['key']);
            foreach ($vars['key'] as $key => $val) {
                //print_r($val);
                $val = self::escape($val);
                $set .= "'{$val}',";
                $k .= "`{$key}`,";
            }
            
            $set = substr($set, 0,-1);
            $k = substr($k, 0,-1);
            
            $sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s) ", $vars['table'], $k, $set) ;
        }else{
            $sql = $vars;
        }
        if(DB_DRIVER == 'mysql') {
            mysql_query('SET CHARACTER SET utf8');
            $q = mysql_query($sql) or die(mysql_error());
            self::$last_id = mysql_insert_id();
        }elseif(DB_DRIVER == 'mysqli'){
            try {
                if(!self::query($sql)){
                    // printf("<div class=\"alert alert-danger\">Errormessage: %s</div>\n", self::$mysqli->error);
                    //Control::error('db',self::$mysqli->error);
                    return false;
                }else{
                    self::$last_id = self::$mysqli->insert_id;
                    return true;
                }
                
            } catch (exception $e) {
                echo $e->getMessage();
            }
            
        }
        
        //return true;
    }

    public static function escape($vars) {
        if(DB_DRIVER == 'mysql') {
            $vars = mysql_escape_string($vars);
        }elseif(DB_DRIVER == 'mysqli'){
            $vars = self::$mysqli->escape_string($vars);
        }else{
            $vars = $vars;
        }
        return $vars;
    }
}


/* End of file Db.class.php */
/* Location: ./inc/lib/Db.class.php */