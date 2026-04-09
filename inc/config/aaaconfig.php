<?php if (defined('GX_LIB') === false) die("Direct Access Not Allowed!");
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @package GeniXCMS
 * @since 0.0.1 build date 20140925
 * @version 2.0.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 * @author Puguh Wijayanto (www.metalgenix.com)
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
*/
error_reporting(E_ALL);

// DB CONFIG
define('DB_HOST', 'localhost');
define('DB_NAME', 'genixcms2');
define('DB_PASS', '');
define('DB_USER', 'root');
!defined('DB_DRIVER') ? define('DB_DRIVER', 'mysqli') : '';

define('SMART_URL', true); //set 'true' if you want use SMART URL (SEO Friendly URL)
define('GX_URL_PREFIX', '.html');

define('ADMIN_DIR', 'gxadmin');
define('USE_MEMCACHED', false);
define('SITE_ID', 'n6Se4NGlgpixkEiomMy9');

define('DEBUG', false);


define('SESSION_EXPIRES', 1); // DEFAULT 24 HOURS
define('SESSION_DB', false); // DEFAULT false






##################################// 
# DON't REMOVE or EDIT THIS. 
# ==================================
# YOU WON'T BE ABLE TO LOG IN 
# IF IT CHANGED. PLEASE BE AWARE
##################################//
define('SECURITY_KEY', 'jEzxYZJyMpg4cspZp9x6MJNDIwtARAwYM8V8jzcCKKp7Y7k2CpTPTz2LjSKQldOskVEsbVTOqeuYSozrSpGduucCGJJSj7GPh3URLXgJSWfsH9ZM5yFkdZZ80li1P6HqfGeCzSvGfzw9gjd3pmWbFp3N2UHpI5Tij1cYhGmEuJwwP4jEcft9sQV8UBm8j1g8V9U5fD9j'); // for security purpose, will be used for creating password

        
