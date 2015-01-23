<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
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