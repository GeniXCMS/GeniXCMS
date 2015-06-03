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
            $db = Db::insert($sql);

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

        return $db;
    }


    /**
    * Update User Function.
    * This will insert certain value of user into the database.
    * <code>
    *    $vars = array(
    *                'id' => '',
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
    public static function update($vars) {
        if(is_array($vars)){
            
            //print_r($vars);
            $u = $vars['user'];
            $sql = array(
                            'table' => 'user',
                            'id' => $vars['id'],
                            'key' => $u,
                        );
            Db::update($sql);
            if(isset($vars['detail']) && $vars['detail'] != ''){
                
                $u = $vars['detail'];
                $sql = array(
                                'table' => 'user_detail',
                                'id' => $vars['id'],
                                'key' => $u,
                            );
                Db::update($sql);
            }
            
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

    public static function generatePass(){
        $vars = microtime().Site::$name.rand();
        $hash = sha1($vars.SECURITY);        
        $pass = substr($hash, 5, 8);
        return $pass;
    }

    public static function is_exist($user) {

        if(isset($_GET['act']) && $_GET['act'] == 'edit'){
            $id = Typo::int($_GET['id']);
            $where = "AND `id` != '{$id}' ";
        }else{
            $where = '';
        }
        $user = sprintf('%s', Typo::cleanX($user));
        $sql = sprintf("SELECT `userid` FROM `user` WHERE `userid` = '%s' %s ", $user, $where);
        $usr = Db::result($sql);
        $n = Db::$num_rows;
        if($n > 0 ){
            return false;
        }else{
            return true;
        }

    }

    public static function is_same($p1, $p2){
        if($p1 == $p2){
            return true;
        }else{
            return false;
        }
    }

    public static function is_email($vars){
        
        if(isset($_GET['act']) && $_GET['act'] == 'edit'){
            $id = Typo::int($_GET['id']);
            $where = "AND `id` != '{$id}' ";
        }else{
            $where = '';
        }
        $vars = sprintf('%s', Typo::cleanX($vars));
        $sql = sprintf("SELECT * FROM `user` WHERE `email` = '%s' %s", $vars, $where );
        $e = Db::result($sql);
        if(Db::$num_rows > 0){
            return false;
        }else{
            return true;
        }
    }

    public static function id($userid){
        $usr = Db::result(
            sprintf("SELECT * FROM `user` WHERE `userid` = '%s' LIMIT 1", 
                Typo::cleanX($userid)
                )
            );
        return $usr[0]->id;
    }

    public static function userid($id){
        $usr = Db::result(
            sprintf("SELECT * FROM `user` WHERE `id` = '%d' LIMIT 1", 
                Typo::int($id)
                )
            );
        return $usr[0]->userid;
    }

    public static function email($id){
        $usr = Db::result(
            sprintf("SELECT * FROM `user` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1", 
                Typo::int($id), 
                Typo::cleanX($id)
                )
            );
        return $usr[0]->email;
    }

    public static function group($id){
        $usr = Db::result(
            sprintf("SELECT * FROM `user` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1", 
                Typo::int($id), 
                Typo::cleanX($id)
                )
            );
        return $usr[0]->group;
    }

    public static function regdate($id){
        $usr = Db::result(
            sprintf("SELECT * FROM `user` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1", 
                Typo::int($id), 
                Typo::cleanX($id)
                )
            );
        return $usr[0]->join_date;
    }

    public static function avatar($id){
        $usr = Db::result(
            sprintf("SELECT * FROM `user_detail` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1", 
                Typo::int($id), 
                Typo::cleanX($id)
                )
            );
        return $usr[0]->avatar;
    }

    public static function activate($id){
        $act = Db::query(
            sprintf("UPDATE `user` SET `status` = '1' WHERE `id` = '%d'", 
                Typo::int($id)
                )
            );
        if($act){
            return true;
        }else{
            return false;
        }
    }

    public static function deactivate($id){
        $act = Db::query(
            sprintf("UPDATE `user` SET `status` = '0' WHERE `id` = '%d'", 
                Typo::int($id)
                )
            );
        if($act){
            return true;
        }else{
            return false;
        }
    }
       
}

/* End of file user.class.php */
/* Location: ./inc/lib/user.class.php */