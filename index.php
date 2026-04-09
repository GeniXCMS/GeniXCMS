<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
 *
 * @version 2.2.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
error_reporting(-1);
date_default_timezone_set('UTC');

define('GX_PATH', realpath(__DIR__ . '/'));
define('GX_LIB', GX_PATH . '/inc/lib/');
define('GX_MOD', GX_PATH . '/inc/mod/');
define('GX_THEME', GX_PATH . '/inc/themes/');
define('GX_ASSET', GX_PATH . '/assets/');
define('GX_CACHE', GX_PATH . '/assets/cache/');



require 'autoload.php';

try {
    $gx = new GxMain();

    // Temporary Cleanup: Reset Archives cache because of data structure change
    if (isset($_GET['clean_archives'])) {
        Db::query("DELETE FROM `options` WHERE `name` IN ('archives_list', 'archives_last_update')");
        echo "Archives Cache Reset Successful!";
        exit;
    }

    $gx->index();
} catch (Exception $e) {
    echo $e->getMessage();
}

/* End of file index.php */
/* Location: ./index.php */
