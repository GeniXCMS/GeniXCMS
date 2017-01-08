<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150202
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
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <div class="col-md-12">
        <h1><i class="fa fa-photo"></i>  Media

        </h1>
        <hr />
    </div>

    <div class="col-sm-12">
        <div id="elfinder"></div>
    </div>
</div>
