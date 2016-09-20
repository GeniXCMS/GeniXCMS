<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.8 build date 20160313
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
<form action="index.php?page=comments-settings" method="post">
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <div class="col-md-12">
        <h2 class="clearfix">
            <div class="pull-left">
                <i class="fa fa-comments"></i> Comments Settings
            </div>
            <div class="pull-right">
                <button type="submit" name="change" class="btn btn-success" value="Change">
                    <span class="glyphicon glyphicon-ok"></span>
                    <span class="hidden-xs hidden-sm"><?=CHANGE;?></span>
                </button>
                <button type="reset" class="btn btn-danger" value="Cancel">
                    <span class="glyphicon glyphicon-remove"></span>
                    <span class="hidden-xs hidden-sm"><?=CANCEL;?></span>
                </button>
            </div>
        </h2>
        <hr>
    </div>

    <div class="col-md-12">
        <!-- Tab Pane Library -->
        <div class="tab-pane" id="library">
            <h3>Settings Comments
            <hr />
            </h3>
            <div class="col-sm-12">
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

        </div><!-- Tab Pane Library END -->
    </div>

</div>
<input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
