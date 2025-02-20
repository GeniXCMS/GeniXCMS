<?php defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
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
header('HTTP/1.0 403 Forbidden');
if (Theme::exist('noaccess')) {
    Theme::theme('noaccess');
} else {
    ?>
    <div class="row">
        <div class="col-sm-12">
            <h1><i class="fa fa-ban text-danger"></i> Not Allowed !!</h1>
            <hr>
            <div class="alert alert-danger">You don't have Access to this page. Maybe You want to go to <a
                        href="<?= Options::v('siteurl'); ?>">frontpage</a> or just <a href="logout.php">Logout</a></div>
        </div>
    </div>

    <?php
}
?>