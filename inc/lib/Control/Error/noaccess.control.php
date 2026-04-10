<?php defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
header('HTTP/1.0 403 Forbidden');
$h_file = file_exists(GX_THEME . Theme::$active . '/header.latte') ? '/header.latte' : '/header.php';
$f_file = file_exists(GX_THEME . Theme::$active . '/footer.latte') ? '/footer.latte' : '/footer.php';

if (Theme::exist('noaccess')) {
    $v_file = file_exists(GX_THEME . Theme::$active . '/noaccess.latte') ? '/noaccess.latte' : '/noaccess.php';
    $latte->render(GX_THEME . Theme::$active . $v_file, $data);
} else {
    ?>
        <div class="container-fluid">
            <section class="app-content-header">
                <div class="col-sm-12">
                    <h1><i class="fa fa-ban text-danger"></i> Not Allowed !!</h1>
                    <hr>

                </div>

            </section>
            <section class="app-content">
                <div class="alert alert-danger">You don't have Access to this page. Maybe You want to go to <a
                        href="<?= Site::$url; ?>">frontpage</a> or just <a href="<?= Site::$url; ?>logout/">Logout</a></div>
            </section>

        </div>

        <?php
}
?>
