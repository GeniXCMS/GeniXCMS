<?php
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
*    filename : index.php
*    version : 0.0.1 pre
*    build : 20140925
*/

define('GX_PATH', dirname(__FILE__));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

function __autoload($f) {
    require GX_LIB. $f . '.class.php';
}

try {
    $gx = new GxMain();
    echo $gx->index();
} catch (Exception $e) {
    echo $e->getMessage();
}


/* End of file index.php */
/* Location: ./index.php */
