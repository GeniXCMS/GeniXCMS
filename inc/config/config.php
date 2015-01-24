<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : config.php
* version : 0.0.1 pre
* build : 20140925
*/

define('GX_URL' , 'http://localhost/genixcms');



// DB CONFIG
define('DB_HOST', 'localhost');
define('DB_NAME', 'genixcms');
define('DB_PASS', '');
define('DB_USER', 'root');
define('DB_DRIVER', 'mysql');


define('THEME', 'default');
define('GX_LANG', 'english');
define('SMART_URL', false); //set 'true' if you want use SMART URL (SEO Friendly URL)
define('GX_URL_PREFIX', '.html');
define('SECURITY', '9234qrioq@)(1-k23#-01iqjiq012iajdqoi1#+@kd@#4-'); // for security purpose, will be used for creating password

//require(GX_PATH.'/inc/lib/gxmain.class.php');
