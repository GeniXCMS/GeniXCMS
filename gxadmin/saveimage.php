<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141003
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

define('GX_PATH', realpath(__DIR__ . '/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

function __autoload($f) {
    require GX_LIB. $f . '.class.php';
}

try {
    $sys = new System();
    new Db();
    new Site();
    Session::start();
    User::secure();
    if( User::access(2) ) {
        // A list of permitted file extensions
        $allowed = array('png', 'jpg', 'jpeg', 'gif');
        if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if(!in_array(strtolower($extension), $allowed)){
                echo '{"status":"error"}';
                exit;
            }
            if(move_uploaded_file($_FILES['file']['tmp_name'], GX_PATH.'/assets/images/uploads/'.$_FILES['file']['name'])){
                $tmp=GX_PATH.'/assets/images/uploads/'.$_FILES['file']['name'];
                echo Site::$url.'/assets/images/uploads/'.$_FILES['file']['name'];
                //echo '{"status":"success"}';
                exit;
            }
        }
    }else{
        echo '{"status":"error"}';
    }

} catch (Exception $e) {
    echo $e->getMessage();
}

