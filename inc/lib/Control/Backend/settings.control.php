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

$data['sitetitle'] = SETTINGS;
switch (isset($_POST['change'])) {
    case '1':
        # code...
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            // VALIDATE ALL
            $alertred[] = TOKEN_NOT_EXIST;
        }
        if(isset($alertred)){
            $data['alertred'] = $alertred;
        }else{
            $vars = array();
            if(isset($_FILES['logo']) && $_FILES['logo'] != ''){
                $path = "/assets/images/";
                $allowed = array('png', 'jpg', 'gif');
                $upload = Upload::go('logo', $path, $allowed );
                if(isset($upload['error']) != ''){
                    echo $upload['error'];
                }else{
                    $vars['logo'] = $upload['path'];
                }
            }else{
                unset($_POST['logo']);
            }

            
            
            //print_r($_POST);
            $flip = array_flip($_POST);
            $sql = "SELECT * FROM `options` WHERE `value` = 'on'";        
            $q = Db::result($sql);
            foreach($q as $ob) {
                if( isset( $flip[$ob->name] ) ) {
                    $vars[$ob->name] = 'on';
                    //echo $ob->name;
                }else{
                    $vars[$ob->name] = 'off';
                    //echo $ob->name;
                }
            }
            //print_r($ob);
            foreach ($_POST as $key => $val) {
                # code...
                $vars[$key] = $val;
            }
            unset($vars['change']);
            //print_r($vars);
            Options::update($vars);
            $data['alertgreen'][] = MSG_SETTINGS_SAVED;
        }
        if(isset($_POST['token'])){ Token::remove($_POST['token']); }
        break;
    
    default:
        # code...
        //print_r($data);
        break;
}
Theme::admin('header', $data);
System::inc('settings',$data);
Theme::admin('footer');

/* End of file settings.control.php */
/* Location: ./inc/lib/Control/Backend/settings.control.php */