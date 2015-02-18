<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : settings.control.php
* version : 0.0.1 pre
* build : 20141006
*/

switch (isset($_POST['change'])) {
    case '1':
        # code...
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

        break;
    
    default:
        # code...
        //print_r($data);
        break;
}
System::inc('settings');

/* End of file settings.control.php */
/* Location: ./inc/lib/Control/Backend/settings.control.php */