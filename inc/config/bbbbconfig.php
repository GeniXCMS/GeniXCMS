<?php if (defined('GX_LIB') === false)
    die("Direct Access Not Allowed!");
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
!defined('DB_HOST') ? define('DB_HOST', 'localhost') : null;
!defined('DB_NAME') ? define('DB_NAME', 'genixcms.db') : null;
!defined('DB_PASS') ? define('DB_PASS', '') : null;
!defined('DB_USER') ? define('DB_USER', 'root') : null;
!defined('DB_DRIVER') ? define('DB_DRIVER', 'sqlite') : null;

!defined('SMART_URL') ? define('SMART_URL', true) : null;
!defined('GX_URL_PREFIX') ? define('GX_URL_PREFIX', '.html') : null;

!defined('ADMIN_DIR') ? define('ADMIN_DIR', 'gxadmin') : null;
!defined('USE_MEMCACHED') ? define('USE_MEMCACHED', false) : null;
!defined('SITE_ID') ? define('SITE_ID', 'ivw2kzWJnKHnSnPSaic6') : null;

!defined('DEBUG') ? define('DEBUG', false) : null;

!defined('SESSION_EXPIRES') ? define('SESSION_EXPIRES', 1) : null;
!defined('SESSION_DB') ? define('SESSION_DB', false) : null;

##################################// 
# DON't REMOVE or EDIT THIS. 
# ==================================
# YOU WON'T BE ABLE TO LOG IN 
# IF IT CHANGED. PLEASE BE AWARE
##################################//
!defined('SECURITY_KEY') ? define('SECURITY_KEY', 'qYeX1yx6TkpXgROanD8UwSuAAa6JyUCjrCDLyuINVS73t0ToESqglRbaUgOOMrBsSaUB8mpz6JYOAISbWMpqTnci55VIspoICvBVvaohyfe32ZZHQmAVlZ7i0UIdJnIIyrB3F0tY5hxPr4dyDQgeE4d0zQXkdQ3EIJ99JBTq2iJtP9EWoJzhNhz0zGFjVYzeFsT5ZVS0') : null;

