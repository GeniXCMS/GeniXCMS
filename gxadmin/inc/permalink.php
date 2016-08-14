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
<form action="index.php?page=permalink" method="post">
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <div class="col-md-12">
        <h2 class="clearfix">
            <div class="pull-left">
                <i class="fa fa-link"></i> Permalink
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
            <h3>Settings Permalink
            <hr />
            </h3>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label>Use <code>index.php</code></label>
                        <?php if ($data['permalink_use_index_php'] === 'on') {
                            $use_index = 'checked';
} else {
    $use_index = 'unchecked';
}
                        ?>
                        <div class="input-group">
                            <input type="checkbox" name="permalink_use_index_php" rel="tooltip"
                                title="Check here if you want to use URL" <?=$use_index;?>> Use Index.php for Rewrite Rule ?
                        </div>

                        <small class="help-block">Check this if you want to use <code>index.php</code> for rewrite rule</small>
                    </div>
                    <div class="col-sm-6 form-group">

                    </div>

                </div>
            </div>

        </div><!-- Tab Pane Library END -->
    </div>

</div>
<input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
