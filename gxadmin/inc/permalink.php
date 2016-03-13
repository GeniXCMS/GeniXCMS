<?php
/**
* GeniXCMS - Content Management System
*
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.8 build date 20160313
* @version 0.0.8
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2016 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
if (isset($data['alertgreen'])) {
    # code...
    echo "<div class=\"alert alert-success\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\">Close</span>
    </button>
    ";
    foreach ($data['alertgreen'] as $alert) {
        # code...
        echo "$alert\n";
    }
    echo "</div>";
}
if (isset($data['alertred'])) {
    # code...
    echo "<div class=\"alert alert-danger\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\">Close</span>
    </button>";
    foreach ($data['alertred'] as $alert) {
        # code...
        echo "$alert\n";
    }
    echo "</div>";
}
?>
<form action="index.php?page=permalink" method="post">
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <div class="col-md-12">
        <h1 class="clearfix">
            <div class="pull-left">
                <i class="fa fa-link"></i> Permalink
            </div>
            <div class="pull-right">
                <button type="submit" name="change" class="btn btn-success" value="Change">
                    <span class="glyphicon glyphicon-ok"></span>
                    <?=CHANGE;?>
                </button>
                <button type="reset" class="btn btn-danger" value="Cancel">
                    <span class="glyphicon glyphicon-remove"></span>
                    <?=CANCEL;?>
                </button>
            </div>
        </h1>
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
                        <?php if(Options::v('permalink_use_index_php') === 'on') { $use_index = 'checked'; }
                        else{ $use_index = 'off';}
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
