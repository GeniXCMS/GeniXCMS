<?php if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
?>
<div class="row">
    <div class="col-sm-12">
        <h1><i class="fa fa-ban text-danger"></i> Not Allowed !!</h1>
        <hr>
        <div class="alert alert-danger">You don't have Access to this page. Maybe You want to go to <a href="<?=Options::v('siteurl');?>">frontpage</a> or just <a href="logout.php">Logout</a></div>
    </div>
</div>

