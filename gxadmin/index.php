<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140928
 *
 * @version 1.1.12
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
date_default_timezone_set('UTC');

/*
 * Set Directories. This will set the directories first before load all
 * libraries.
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @since 0.0.1
 */
define('GX_PATH', realpath(__DIR__.'/../'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

/**
 * Libraries Loader Function. This will load all libraries inside the inc/lib
 * directory.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 *
 * @since 0.0.1
 */
require '../autoload.php';

/*
 * Run the Main caller at GxMain Class. This will call the Backend Controller.
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @since 0.0.1
 */
try {
    $gx = new GxMain();
    $gx->admin();
} catch (Exception $e) {
    echo $e->getMessage();
}

/* End of file index.php */
/* Location: ./gxadmin/index.php */
