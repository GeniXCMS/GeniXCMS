<?php defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
 *
 * @version 2.0.0
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
?>
<div class="col-sm-12">
<h2><i class="fa fa-warning text-danger"></i> <?=_("Unknown Error Occured");?></h2>
<?php if (isset($val) && $val != '') {
    ?>
    <div class="container-fluid">
        <div class="alert alert-danger">
            <?=$val; ?>
        </div>
    </div>


<?php
}
    Site::footer();
?>

</div>