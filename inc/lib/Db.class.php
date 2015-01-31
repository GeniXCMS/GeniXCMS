<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Db.class.php
* version : 0.0.1 pre
* build : 20140925
*/

class Db
{
    static $num_rows = "";
    static $last_id = "";
    static $link = '';

    public function __construct () {
        global $vars;
        if(DB_DRIVER == 'mysql') {
            mysql_connect(DB_HOST, DB_USER, DB_PASS);
            mysql_select_db(DB_NAME);
        }elseif(DB_DRIVER == 'mysqli') {
            self::$link = mysqli_connect(DBHOST, DBUSER, DB_PASS, DB_NAME);
            return self::$link;
        }
    }

    public static function query ($vars) {
        if(DB_DRIVER == 'mysql') {
            mysql_query('SET CHARACTER SET utf8');
            $q = mysql_query($vars)  or die(mysql_error());
        } 
        elseif(DB_DRIVER == 'mysqli') {
            $q = mysqli_query(self::$link, $vars) or die(mysql_error());
        }
        self::$last_id = mysql_insert_id();
        return $q;
    }

    

    public static function result ($vars) {
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
            $q = mysqli_query(self::$link, $vars) or die(mysqli_error());
            $r[] = mysqli_fetch_object($q);

        }
        self::$num_rows = $n;
        return $r;
    }

    /*
    *    delete sql
    *    $vars = array(
    *                'table' =>    'table', // table name
    *                'where'    =>    array(), // where
    *            )
    */
    public static function delete ($vars) {
        if(is_array($vars)){
            $where = '';
            foreach ($vars['where'] as $key => $val) {
                $where .= "`$key` = '$val' AND ";
            }
            $where = $where." 1";
            $sql = sprintf("DELETE FROM `%s` WHERE %s LIMIT 1", $vars['table'], $where);
        }else{
            $sql = $vars;
        }
        mysql_query('SET CHARACTER SET utf8');
        $q = mysql_query($sql) or die(mysql_error());
        return true;

    }

    /*
    *    update sql
    *    $vars = array(
    *                'table' =>    'table', // table name
    *                'id'    =>    'id', // item id
    *                'key'    =>    array(
    *                                    'col1' => 'col1_val',
    *                                    'col2' => 'col2_val',
    *                                )
    *            )
    */
    public static function update ($vars) {
        if(is_array($vars)){
            $set = "";
            foreach ($vars['key'] as $key => $val) {
                $set .= "`$key` = '$val',";
            }
            

            $set = substr($set, 0,-1);
            //echo $set;
            $sql = sprintf("UPDATE `%s` SET %s WHERE `id` = '%d' LIMIT 1", $vars['table'], $set, $vars['id']);
        }else{
            $sql = $vars;
        }
        mysql_query('SET CHARACTER SET utf8');
        $q = mysql_query($sql) or die(mysql_error());
        return true;
    }

    /*
    *    inert sql
    *    $vars = array(
    *                'table' =>    'table', // table name
    *                'key'    =>    array(
    *                                    'col1' => 'col1_val',
    *                                    'col2' => 'col2_val',
    *                                )
    *            )
    */
    public static function insert ($vars) {
        if(is_array($vars)){
            $set = "";
            $k = "";
            foreach ($vars['key'] as $key => $val) {
                $set .= "'$val',";
                $k .= "`$key`,";
            }
            
            //echo $set;
            //echo $k;
            $set = substr($set, 0,-1);
            $k = substr($k, 0,-1);
            
            $sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s) ", $vars['table'], $k, $set) ;
        }else{
            $sql = $vars;
        }
        mysql_query('SET CHARACTER SET utf8');
        $q = mysql_query($sql) or die(mysql_error());
        self::$last_id = mysql_insert_id();
        return true;
    }
}

#global $db;
#$db = new Db();

/* End of file Db.class.php */
/* Location: ./inc/lib/Db.class.php */