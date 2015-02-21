<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

class User
{
    public function __construct () {
    }

    public static function secure()
    {
        if (!isset($_SESSION['gxsess']['val']['loggedin']) && !isset($_SESSION['gxsess']['val']['username']) ) {
            header('location: login.php');
        } else {
            return true;
        }
    }

    public static function access ($grp='4') {
        if ( isset($_SESSION['gxsess']['val']['group']) ) {
            if($_SESSION['gxsess']['val']['group'] <= $grp) {
                return true;
            }else{
                return false;
            }
        }
    }

    public static function is_loggedin () {
        $username = Session::val('username');
        if(isset($username)) {
            $v = true;
        }else{
            $v = false;
        }
        return $v;
    }

    /**
    * Create User Function
    * This will insert certain value of user into the database.
    * <code>
    *    $vars = array(
    *                'user' => array(
    *                                'userid' => '',
    *                                'passwd' => '',
    *                                'email' => '',
    *                                'group' => ''  
    *                            ),
    *                'detail' => array(
    *                                'userid' => '',
    *                                'fname' => '',
    *                                'lname' => '',
    *                                'sex' => '',
    *                                'birthplace' => '',
    *                                'birthdate' => '',
    *                                'addr' => '',
    *                                'city' => '',
    *                                'state' => '',
    *                                'country' => '',
    *                                'postcode' => ''
    *                            )
    *            );
    * </code>
    */
    public static function create($vars) {
        if(is_array($vars)){
            
            //print_r($vars['user']);
            $u = $vars['user'];
            $sql = array(
                            'table' => 'user',
                            'key' => $u,
                        );
            Db::insert($sql);

            if(!isset($vars['detail']) || $vars['detail'] == ''){
                Db::insert("INSERT INTO `user_detail` (`userid`) VALUES ('{$vars['user']['userid']}')");
            }else{
                $u = $vars['detail'];
                $sql = array(
                                'table' => 'user_detail',
                                'key' => $u,
                            );
                Db::insert($sql);
            }
        }
    }

    public static function update($vars) {
        if(is_array($vars)){
            
            //print_r($vars);
            $u = $vars['user'];
            $sql = array(
                            'table' => 'user',
                            'id' => $_GET['id'],
                            'key' => $u,
                        );
            Db::update($sql);

            
        }
    }

    public static function delete($id){
        $vars = array(
                'table' => 'user',
                'where' => array(
                            'id' => $id
                            )
            );
        Db::delete($vars);

        $vars = array(
                'table' => 'user_detail',
                'where' => array(
                            'id' => $id
                            )
            );
        Db::delete($vars);
    }

    // $vars = array(
    //                 'userid' => '',
    //                 'passwd' => ''
    //             );
    public static function randpass($vars){
        if(is_array($vars)){
            $hash = sha1($vars['passwd'].SECURITY.$vars['userid']);
        }else{
            $hash = sha1($vars.SECURITY);
        }
        
        $hash = substr($hash, 5, 16);
        $pass = md5($hash);
        return $pass;
    }

    public static function is_exist($user) {
        if(isset($_GET['act']) && $_GET['act'] == 'edit'){
            $where = "AND `id` != '{$_GET['id']}' ";
        }else{
            $where = '';
        }
        $usr = Db::result("SELECT `userid` FROM `user` WHERE `userid` = '{$user}' {$where} ");
        $n = Db::$num_rows;
        if($n > 0 ){
            return false;
        }else{
            return true;
        }

    }

    public static function is_same($p1, $p2){
        if($p1 === $p2){
            return true;
        }else{
            return false;
        }
    }

    public static function is_email($vars){
        if(isset($_GET['act']) && $_GET['act'] == 'edit'){
            $where = "AND `id` != '{$_GET['id']}' ";
        }else{
            $where = '';
        }
        $e = Db::result("SELECT * FROM `user` WHERE `email` = '{$vars}' {$where}");
        if(Db::$num_rows > 0){
            return false;
        }else{
            return true;
        }
    }

    public static function userid($id){
        $usr = Db::result("SELECT * FROM `user` WHERE `id` = '{$id}' LIMIT 1");
        return $usr[0]->userid;
    }

    public static function email($id){
        $usr = Db::result("SELECT * FROM `user` WHERE `id` = '{$id}' LIMIT 1");
        return $usr[0]->email;
    }

    public static function group($id){
        $usr = Db::result("SELECT * FROM `user` WHERE `id` = '{$id}' LIMIT 1");
        return $usr[0]->group;
    }
       
}

/* End of file user.class.php */
/* Location: ./inc/lib/user.class.php */