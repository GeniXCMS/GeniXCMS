<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.2
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
//error_reporting(E_ALL);
// DB CONFIG
define('DB_HOST', 'localhost');
define('DB_NAME', 'genixcms');
define('DB_PASS', '');
define('DB_USER', 'root');
define('DB_DRIVER', 'mysqli');


define('THEME', 'default');
define('GX_LANG', 'english');
define('SMART_URL', false); //set 'true' if you want use SMART URL (SEO Friendly URL)
define('GX_URL_PREFIX', '.html');
define('SECURITY', 'qwP5ZHzxOEHtqg5RFX5YiXHEZzibj3sTzCrYB8mIY5HQ8Rn1LYyzVb9LphX8ZSvtjLdvb6IoKxeRtC5N5rqsMRT357GwINvIp3dcGqcGcgMuwmKHpZavGAnGvSYNYX2rBujgrd8SkJ4T2ClbXGPLp1iD1E75YybTB9Yg0tnrZ6iMYJ8aJfEkV6hFfyk3G1tp0Grqn0nO'); // for security purpose, will be used for creating password

        