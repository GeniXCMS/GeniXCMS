<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150202
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
?>
<form action="" method="post">

    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <section class="content-header">
        <h1><i class="fa fa-group"></i> Edit User
            <div class="pull-right">
                <button  class="btn btn-success btn-sm" type="submit" name="edituser">
                    <span class="glyphicon glyphicon-ok"></span>
                    <span class="hidden-xs hidden-sm">Update</span>
                </button>
                <a class="btn btn-danger  btn-sm" href="<?=(User::access(2)) ? 'index.php?page=users' : 'index.php';?>">
                    <span class="glyphicon glyphicon-remove"></span>
                    <span class="hidden-xs hidden-sm">Cancel</span>
                </a>
            </div>
        </h1>
    </section>
    <section class="content">
        <!-- Default box -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Modify User
                </h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body">
    <div class="row">

    <div class="col-sm-6">
        <div class="form-group">
            <label>Userid</label>
            <?php if (User::access(0)) {
                $id = isset($_GET['id']) ? Typo::int($_GET['id']): '';
                $userid = User::userid($id);
    ?>
                <input type="text" name="userid" class="form-control" value="<?=$userid; ?>">
                <input type="hidden" name="olduserid" class="form-control" value="<?=$userid; ?>">
            <?php
} else {
    echo '<div class="form-control">'.$userid.'</div>';
} ?>
            <small>Only Admin can edit userid</small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?=User::email($id);?>">
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
            $var = array(
                    'name' => 'group',
                    'selected' => User::group($id),
                    'update' => true,
                );
        echo User::dropdown($var); ?>
            
            <small>Group Level of the user.</small>
        </div>
    <?php
} ?>
    </div>


    </div>

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                Footer
            </div>
            <!-- /.box-footer-->
        </div>
        <!-- /.box -->
    </section>

<input type="hidden" name="token" value="<?=TOKEN?>">
</form>
