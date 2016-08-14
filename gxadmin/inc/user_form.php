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
<form action="" method="post">
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <div class="col-md-12">
        <h1><i class="fa fa-group"></i> Edit User
            <div class="pull-right">
                <button  class="btn btn-success " type="submit" name="edituser">
                    <span class="glyphicon glyphicon-ok"></span>
                    <span class="hidden-xs hidden-sm">Update</span>
                </button>
                <a class="btn btn-danger  " href="<?=(User::access(2)) ? 'index.php?page=users' : 'index.php';?>">
                    <span class="glyphicon glyphicon-remove"></span>
                    <span class="hidden-xs hidden-sm">Cancel</span>
                </a>

            </div>
        </h1>
        <hr />
    </div>
    <div class="col-sm-12">
    <div class="row">

    <div class="col-sm-6">
        <div class="form-group">
            <label>Userid</label>
            <?php if (User::access(0)) {
    ?>
            <input type="text" name="userid" class="form-control" value="<?=User::userid($_GET['id']); ?>">
            <?php
} else {
    echo '<div class="form-control">'.User::userid($_GET['id']).'</div>';
} ?>
            <small>Only Admin can edit userid</small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?=User::email($_GET['id']);?>">
            <small>Email must be different with another.</small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="pass" class="form-control" value="">
            <small>Type password to change it. Left it blank to use old password.</small>
        </div>
    </div>
    <div class="col-sm-6">
    <?php if (User::access(1)) {
    ?>
        <div class="form-group">
            <label>Group Level</label>
            <?php
            if (User::group($_GET['id']) == 0) {
                $adm = 'SELECTED';
                $edt = '';
                $spv = '';
                $mem = '';
                $aut = '';
            } elseif (User::group($_GET['id']) == 1) {
                $aut = '';
                $edt = '';
                $spv = 'SELECTED';
                $adm = '';
                $mem = '';
            } elseif (User::group($_GET['id']) == 2) {
                $aut = '';
                $edt = 'SELECTED';
                $spv = '';
                $adm = '';
                $mem = '';
            } elseif (User::group($_GET['id']) == 3) {
                $aut = 'SELECTED';
                $edt = '';
                $spv = '';
                $adm = '';
                $mem = '';
            } elseif (User::group($_GET['id']) == 4) {
                $mem = 'SELECTED';
                $edt = '';
                $spv = '';
                $adm = '';
                $aut = '';
            } ?>
            <select name="group" class="form-control">
                <?=(User::access(0)) ? '<option value="0" <?=$adm;?>Administrator</option>' : ''; ?>
                <option value="1" <?=$spv; ?>>Supervisor</option>
                <option value="2" <?=$edt; ?>>Editor</option>
                <option value="3" <?=$aut; ?>>Author</option>
                <option value="4" <?=$mem; ?>>General Members</option>
            </select>
            <small>Group Level of the user.</small>
        </div>
    <?php
} ?>
    </div>


    </div>
    </div>
</div>
<input type="hidden" name="token" value="<?=$_GET['token'];?>">
</form>
