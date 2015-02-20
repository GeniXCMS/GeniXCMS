<?php
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
*    filename : register.php
*    version : 0.0.1 pre
*    build : 20141003
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
    
} catch (Exception $e) {
    echo $e->getMessage();
}

User::access(2);
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
        echo GX_URL.'/assets/images/uploads/'.$_FILES['file']['name'];
        //echo '{"status":"success"}';
        exit;
    }
}

// echo '{"status":"error"}';
// exit;