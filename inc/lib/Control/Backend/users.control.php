<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

$data['sitetitle'] = USERS;
if(isset($_GET['act'])){ $act = $_GET['act']; }else{$act="";}
switch ($act) {
    case 'edit':
        # code...
        $data[] = "";
        switch (isset($_POST['edituser'])) {
            case true:
                # code...
                //check token first 
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                

                // VALIDATE ALL
                if(!User::is_exist($_POST['userid'])){
                    $alertred[] = MSG_USER_EXIST;
                }
                
                if(!User::is_email($_POST['email'])){
                    $alertred[] = MSG_USER_EMAIL_EXIST;
                }

                if(!isset($alertred)){

                    $vars = array(
                                    'id' => sprintf('%d',$_GET['id']),
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
                
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            
            default:
                # code...
                break;
            }
        Theme::admin('header', $data);
        System::inc('user_form', $data);
        Theme::admin('footer');
        break;
    case 'del':
            if(isset($_GET['id'])){
                $user = User::userid($_GET['id']);
                if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                    // VALIDATE ALL
                    $data['alertred'][] = TOKEN_NOT_EXIST;
                }else{
                    User::delete($_GET['id']);
                    $data['alertgreen'][] = USER." ".$user." ".MSG_USER_REMOVED;
                }
                if(isset($_GET['token'])){ Token::remove($_GET['token']); }
            }else{
                $data['alertred'][] = MSG_USER_NO_ID_SELECTED;
            }
            $data['usr'] = Db::result("SELECT * FROM `user` ORDER BY `userid` ASC LIMIT 10");
            $data['num'] = Db::$num_rows;
            Theme::admin('header', $data);
            System::inc('user', $data);
            Theme::admin('footer');
        break;
    case 'active':
            if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                // VALIDATE ALL
                $data['alertred'][] = TOKEN_NOT_EXIST;
            }else{
                if(User::activate($_GET['id'])){
                    $data['alertgreen'][] = USER." ".User::userid($_GET['id'])."".MSG_USER_ACTIVATED;
                }else{
                    $data['alertred'][] = USER." ".User::userid($_GET['id'])."".MSG_USER_ACTIVATION_FAIL;
                }

            }
            if(isset($_GET['token'])){ Token::remove($_GET['token']); }
            $data['usr'] = Db::result("SELECT * FROM `user` ORDER BY `userid` ASC LIMIT 10");
            $data['num'] = Db::$num_rows;
            Theme::admin('header', $data);
            System::inc('user', $data);
            Theme::admin('footer');
        break;
        
    case 'inactive':
            if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                // VALIDATE ALL
                $data['alertred'][] = TOKEN_NOT_EXIST;
            }else{
                if(User::deactivate($_GET['id'])){
                    $data['alertgreen'][] = USER." ".User::userid($_GET['id'])."".MSG_USER_DEACTIVATED;
                }else{
                    $data['alertred'][] = USER." ".User::userid($_GET['id'])."".MSG_USER_DEACTIVATION_FAIL;
                }
            }
            if(isset($_GET['token'])){ Token::remove($_GET['token']); }
            $data['usr'] = Db::result("SELECT * FROM `user` ORDER BY `userid` ASC LIMIT 10");
            $data['num'] = Db::$num_rows;
            Theme::admin('header', $data);
            System::inc('user', $data);
            Theme::admin('footer');
        break;
        

    default:
        # code...
        $data[] = "";
        switch (isset($_POST['adduser'])) {
            case true:
                # code...
                // CHECK TOKEN FIRST 
                //echo Token::isExist($_POST['token']);
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }

                if (!isset($_POST['userid']) || $_POST['userid'] == "") {
                    // VALIDATE ALL
                    $alertred[] = USERID_CANNOT_EMPTY;
                }
                if (!isset($_POST['pass1']) || $_POST['pass1'] == "") {
                    // VALIDATE ALL
                    $alertred[] = PASS1_CANNOT_EMPTY;
                }
                if (!isset($_POST['pass2']) || $_POST['pass2'] == "") {
                    // VALIDATE ALL
                    $alertred[] = PASS2_CANNOT_EMPTY;
                }

                if(!User::is_exist($_POST['userid'])){
                    $alertred[] = MSG_USER_EXIST;
                }
                if(!User::is_same($_POST['pass1'], $_POST['pass2'])){
                    $alertred[] = MSG_USER_PWD_MISMATCH;
                }
                if(!User::is_email($_POST['email'])){
                    $alertred[] = MSG_USER_EMAIL_EXIST;
                }

                if(!isset($alertred)){

                    $vars = array(
                                    'user' => array(
                                                    'userid' => $_POST['userid'],
                                                    'pass' => User::randpass($_POST['pass1']),
                                                    'email' => $_POST['email'],
                                                    'group' => $_POST['group'],
                                                    'status' => '1',
                                                    'join_date' => date("Y-m-d H:i:s")
                                                ),
                                    
                                );   
                    User::create($vars);
                    Token::remove($_POST['token']);
                    $data['alertgreen'][] = USER." {$_POST['userid']}, ".MSG_USER_ADDED;
                }else{
                    $data['alertred'] = $alertred;
                }
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            
            default:
                # code...
                break;
        }

        if(isset($_POST['action'])) {
            $action = $_POST['action'];
        }else{
            $action = '';
        }
        if(isset($_POST['user_id'])) { $user_id = $_POST['user_id']; } else { $user_id = ""; }
        switch ($action) {

            case 'activate':
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{
                    foreach ($user_id as $id) {
                        # code...
                        User::activate($id);
                    }
                }
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            case 'deactivate':
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{
                    foreach ($user_id as $id) {
                        # code...
                        User::deactivate($id);
                    }
                }
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            case 'delete':
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{
                    foreach ($user_id as $id) {
                        # code...
                        User::delete($id);
                    }
                }
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            
            default:
                # code...
                break;
        }


        // search query 
        $where = " 1 ";
        $qpage = "";
        if(isset($_GET['q']) && $_GET['q'] != ''){
            $where .= "AND (`userid` LIKE '%%{$_GET['q']}%%' OR `email` LIKE '%%{$_GET['q']}%%') ";
            $qpage .= "&q={$_GET['q']}";
        }
        if(isset($_GET['from']) && $_GET['from'] != ''){
            $where .= "AND `join_date` >= '{$_GET['from']}' ";
            $qpage .= "&from={$_GET['from']}";
        }
        if(isset($_GET['to']) && $_GET['to'] != ''){
            $where .= "AND `join_date` <= '{$_GET['to']}' ";
            $qpage .= "&to={$_GET['to']}";
        }
        if(isset($_GET['status']) && $_GET['status'] != ''){
            $where .= "AND `status` LIKE '%%{$_GET['status']}%%' ";
            $qpage .= "&status={$_GET['status']}";
        }


        $max = "10";
        if(isset($_GET['paging'])){
            $paging = $_GET['paging'];
            $offset = ($_GET['paging']-1)*$max;
        }else{
            $paging = 1;
            $offset = 0;
        }

        $data['usr'] = Db::result("SELECT * FROM `user` WHERE {$where} ORDER BY `userid` ASC LIMIT {$offset}, {$max}");
        $data['num'] = Db::$num_rows;
        Theme::admin('header', $data);
        System::inc('user', $data);
        Theme::admin('footer');

        $page = array(
                    'paging' => $paging,
                    'table' => 'user',
                    'where' => $where,
                    'max' => $max,
                    'url' => 'index.php?page=users'.$qpage,
                    'type' => 'pager'
                );
        echo Paging::create($page);

        break;
}


/* End of file users.control.php */
/* Location: ./inc/lib/Control/Backend/users.control.php */