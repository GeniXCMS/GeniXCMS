<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.1.2 build date 20170912
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

<form action="index.php?page=cache" method="post">

    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <section class="content-header">
        <h1 class="clearfix">
            <div class="pull-left">
                <i class="fa fa-archive"></i> Cache Settings
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
                    Settings Cache
                </h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Enable <code>cache</code> ?</label>
<?php if ($data['cache_enabled'] === 'on') {
    $enable_cache = 'checked';
} else {
    $enable_cache = 'unchecked';
}
?>
                                <div class="input-group">
                                    <input type="checkbox" name="cache_enabled" rel="tooltip"
                                        title="Check here if you want to enable comment system" <?=$enable_cache;?>> Enable Cache
                                </div>

                                <small class="help-block">Check this if you want to enable Cache system</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Cache Timeout</label>
                                    <input type="number" name="cache_timeout" value="<?=$data['cache_timeout'];?>" class="form-control">
                                <span>Cache file timeout, in <kbd>seconds</kbd></span>
                            </div>
                        </div>

                    </div>
                    
                    <div class="col-md-6 ">
                        <div class="form-group">
                        <label>Cache Path</label>
                        <input type="text" class="form-control" name="cache_path" value="<?=$data['cache_path'];?>">
                        <span class="help-block">Cache path, don't forget it to <code>chmod</code> it <kbd>777</kbd></span>
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
