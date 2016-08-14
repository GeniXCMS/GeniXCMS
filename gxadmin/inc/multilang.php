<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.7 build date 20150718
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
<form action="index.php?page=multilang" method="post">
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>
    <div class="col-md-12">
        <h2 class="clearfix">
            <div class="pull-left">
                <i class="fa fa-flag"></i> Multilanguage
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
            <h3>Settings Multilanguage
            <a class="btn btn-success pull-right" data-toggle="modal" data-target="#addcountry">
                <span class="glyphicon glyphicon-plus"></span> <span class="hidden-xs hidden-sm">Add Language</span>
            </a>
            <hr />
            </h3>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label>Enable Multilanguage</label>
                        <?php if (Options::v('multilang_enable') === 'on') {
                            $multilang_enable = 'checked';
} else {
    $multilang_enable = 'off';
}
                        ?>
                        <div class="input-group">
                            <input type="checkbox" name="multilang_enable" rel="tooltip"
                                title="Check here if you want to use URL" <?=$multilang_enable;?>> Enable Multilanguage ?
                        </div>

                        <small class="help-block">Check this if you want to enable multilanguage</small>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>Default Language</label>
                        <select name="multilang_default" class="form-control">
                            <?php
                            foreach ($data['list_lang'] as $key => $value) {
                                $sel = ($key == $data['default_lang']) ? 'selected' : '';
                                echo "<option value=\"{$key}\" $sel>{$value['country']}</option>";
                            }
                            ?>
                        </select>
                        <small class="help-block">Multilanguage default country. Choose one.</small>
                    </div>
                    <div class="col-sm-12 form-group">
                        <label>Available Language</label>
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-group">
                                <?php
                                if (count($data['list_lang']) > 0) {
                                    # code...
                                    $list_lang = $data['list_lang'];
                                    foreach ($list_lang as $key => $value) {
                                        $flag = strtolower($value['flag']);
                                        echo "
                                            <li class=\"list-group-item col-xs-6 col-sm-4 col-md-2\">
                                                <span class=\"flag-icon flag-icon-{$flag}\"></span>
                                                {$value['country']} ({$key})
                                                <a href=\"index.php?page=multilang&del={$key}&token=".TOKEN.'" class="pull-right"><i class="fa fa-remove"></i></a>
                                            </li>';
                                    }
                                }
                                ?>
                                </ul>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div><!-- Tab Pane Library END -->
    </div>

</div>
<input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
<!-- Modal -->
<div class="modal fade" id="addcountry" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form action="index.php?page=multilang" method="post">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add Language Country</h4>
      </div>
      <div class="modal-body">

            <div class="form-group">
                <label>Country Language</label>
                <input type="text" name='multilang_country_name' class="form-control">
                <small class="help-block">Type Full country language, eg: English, Japanese, etc.</small>
            </div>
            <div class="form-group">
                <label>Country Language Code</label>
                <input type="text" name="multilang_country_code" class="form-control">
                <small class="help-block">Set the country code, in lowecase. eg: en, id, jp, etc.</small>
            </div>
            <div class="form-group">
                <label>Country Flag</label>
                <select name="multilang_country_flag" class="form-control">
                    <?=Date::optCountry();?>
                </select>
                <small class="help-block">Set the country flag code, in lowecase. eg: us, id, jp, etc.</small>
            </div>
            <div class="form-group">
                <label>System Language</label>
                <select name="multilang_system_lang" class="form-control">
                    <?=Language::optDropdown();?>
                </select>
                <small class="help-block">Choose the system language for prefered language.</small>
            </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=CLOSE;?></button>
        <button type="submit" class="btn btn-success" name="addcountry"><?=SUBMIT;?></button>
      </div>
      <input type="hidden" name="token" value="<?=TOKEN;?>">
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
