<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.8 build date 20160313
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
<form action="index.php?page=comments-settings" method="post">

    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <section class="content-header">
        <h1 class="clearfix">
            <div class="pull-left">
                <i class="fa fa-comments"></i> Comments Settings
            </div>
            <div class="pull-right">
                <button type="submit" name="change" class="btn btn-success btn-sm" value="Change">
                    <span class="glyphicon glyphicon-ok"></span>
                    <span class="hidden-xs hidden-sm"><?=CHANGE;?></span>
                </button>
                <button type="reset" class="btn btn-danger btn-sm" value="Cancel">
                    <span class="glyphicon glyphicon-remove"></span>
                    <span class="hidden-xs hidden-sm"><?=CANCEL;?></span>
                </button>
            </div>
        </h1>
    </section>

    <section class="content">
        <!-- Default box -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Settings Comments
                </h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Enable <code>comments</code> ?</label>
<?php if ($data['comments_enable'] === 'on') {
    $enable_comment = 'checked';
} else {
    $enable_comment = 'unchecked';
}
?>
                                <div class="input-group">
                                    <input type="checkbox" name="comments_enable" rel="tooltip"
                                        title="Check here if you want to enable comment system" <?=$enable_comment;?>> Enable Comment
                                </div>

                                <small class="help-block">Check this if you want to enable commenting system</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Comments Per Page</label>
                                    <input type="number" name="comments_perpage" value="<?=$data['comments_perpage'];?>" class="form-control">
                                <span>Comments Count per page</span>
                            </div>
                        </div>

                    </div>
                    
                    <div class="col-md-6 ">
                        <div class="form-group">
                        <label>SpamWords</label>
                        <textarea class="form-control" name="spamwords"><?=$data['spamwords'];?></textarea>
                        <span class="help-block">one word per line. you can get spamwords blacklist from <code><a href="https://github.com/splorp/wordpress-comment-blacklist" target="_blank">here</a></code></span>
                        </div>
                    </div>

                </div>

            </div>
            <!-- /.box-body -->
<!--            <div class="box-footer">-->
<!---->
<!--            </div>-->
            <!-- /.box-footer-->
        </div>
        <!-- /.box -->
    </section>


<input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
