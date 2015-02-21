<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


if(isset($_GET['act'])){ $act = $_GET['act']; }else{$act="";}
switch ($act) {
    case 'edit':
        # code...
        $data[] = "";
        switch (isset($_POST['edituser'])) {
            case true:
                # code...
                // VALIDATE ALL
                if(!User::is_exist($_POST['userid'])){
                    $alertred[] = "User Exist!! Choose another userid.";
                }
                
                if(!User::is_email($_POST['email'])){
                    $alertred[] = "Email already used. Please use another email.";
                }

                if(!isset($alertred)){

                    $vars = array(
                                    'user' => array(
                                                    'userid' => $_POST['userid'],
                                                    'email' => $_POST['email'],
                                                    'group' => $_POST['group']
                                                )
                                    
                                ); 
                    if(!empty($_POST['pass']) || $_POST['pass'] != ""){
                        $pass = array(
                                    'pass' => User::randpass($_POST['pass'])
                                );
                        $vars['user'] =  array_merge($vars['user'], $pass);
                        //print_r($vars);
                    }  
                    User::update($vars);
                    $alertgreen[] = "User : ".User::userid($_GET['id'])." Updated";
                    
                    if (isset($alertgreen)) {
                        # code...
                        $data['alertgreen'] = $alertgreen;
                    }
                }else{
                    $data['alertred'] = $alertred;
                }
                break;
            
            default:
                # code...
                break;
            }
        System::inc('user_form', $data);
        break;
    case 'del':
            if(isset($_GET['id'])){
                $user = User::userid($_GET['id']);
                User::delete($_GET['id']);
                $data['alertgreen'][] = "User : ".$user." Removed";
            }else{
                $data['alertred'][] = "No ID selected";
            }
            $data['usr'] = Db::result("SELECT * FROM `user` ORDER BY `userid` ASC LIMIT 10");
            $data['num'] = Db::$num_rows;
            System::inc('user', $data);
        break;
    default:
        # code...
        $data[] = "";
        switch (isset($_POST['adduser'])) {
            case true:
                # code...
                // VALIDATE ALL
                if(!User::is_exist($_POST['userid'])){
                    $alertred[] = "User Exist!! Choose another userid.";
                }
                if(!User::is_same($_POST['pass1'], $_POST['pass1'])){
                    $alertred[] = "Password Didn't Match!! Retype Your Password again.";
                }
                if(!User::is_email($_POST['email'])){
                    $alertred[] = "Email already used. Please use another email.";
                }

                if(!isset($alertred)){

                    $vars = array(
                                    'user' => array(
                                                    'userid' => $_POST['userid'],
                                                    'pass' => User::randpass($_POST['pass1']),
                                                    'email' => $_POST['email'],
                                                    'group' => $_POST['group']
                                                ),
                                    
                                );   
                    User::create($vars);
                }else{
                    $data['alertred'] = $alertred;
                }
                break;
            
            default:
                # code...
                break;
        }
        $data['usr'] = Db::result("SELECT * FROM `user` ORDER BY `userid` ASC LIMIT 10");
        $data['num'] = Db::$num_rows;
        System::inc('user', $data);
        break;
}


/* End of file users.control.php */
/* Location: ./inc/lib/Control/Backend/users.control.php */