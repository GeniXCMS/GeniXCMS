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

    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <section class="content-header">
        <h1><i class="fa fa-plug"></i>  <?=MODULES;?>
            <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#myModal">
                <span class="glyphicon glyphicon-plus"></span> <span class="hidden-xs hidden-sm"><?=UPLOAD_MODULES;?></span>
            </button>
        </h1>
    </section>

    <section class="content">

        <!-- Default box -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Modules List
                </h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body no-padding">
        <table class="table">
            <thead>
                <th><?=NAME;?></th>
                <th><?=DESC;?></th>
                <th><?=ACTION;?> </th>
            </thead>
            <tbody>
                <?php
                if (count($data['mods']) > 0) {
                    foreach ($data['mods'] as $mod) {
                        $m = Mod::data($mod);
                        if (Mod::isActive($mod)) {
                            $btnact = 'warning';
                            $act = '<i class="fa fa-toggle-off"></i> '.DEACTIVATE;
                            $actUri = DEACTIVATE;
                        } else {
                            $btnact = 'success';
                            $act = '<i class="fa fa-toggle-on"></i> '.ACTIVATE;
                            $actUri = ACTIVATE;
                        }
                        echo "
                            <tr>
                                <td>
                                    {$m['icon']} <strong>{$m['name']}</strong><br />
                                    <small>".VERSION.": {$m['version']} - ".LICENSE.": {$m['license']}</small>
                                </td>
                                <td>
                                    <p title=\"{$m['desc']}\">".substr($m['desc'], 0, 180)."</p>
                                    <small>author: <a href=\"{$m['url']}\">{$m['developer']}</a></small>
                                </td>
                                <td>
                                    <a href=\"index.php?page=modules&act={$actUri}&modules={$mod}&token=".TOKEN."\" class=\"label label-{$btnact}\">{$act}</a>
                                    ";
                        if (!Mod::isActive($mod)) {
                            echo "<a href=\"index.php?page=modules&act=remove&modules={$mod}&token=".TOKEN.'" class="label label-danger" onclick="return confirm(\''.DELETE_CONFIRM.'\');" disable><i class="fa fa-remove"></i> '.REMOVE.'</a>';
                        }
                        echo'
                                </td>
                            </tr>';
                        //echo $m;
                    }
                } else {
                    echo '<div class="col-md-12">'.NO_MODULES_FOUND.'</div>';
                }
                ?>

            </tbody>
            <tfoot>
                <td></td>
                <td></td>
                <td></td>

            </tfoot>
        </table>
            </div>
            <!-- /.box-body -->
<!--            <div class="box-footer">-->
<!---->
<!--            </div>-->
            <!-- /.box-footer-->
        </div>
        <!-- /.box -->
    </section>

<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=modules" method="post" enctype="multipart/form-data">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?=UPLOAD_MODULES;?></h4>
          </div>
          <div class="modal-body">

                <div class="form-group">
                    <label><?=BROWSE_MODULES;?></label>
                    <input type="file" name="module" class="form-control">
                    <small><?=BROWSE_MODULES_DESC;?></small>
                </div>

          </div>
          <div class="modal-footer">
            <input type="hidden" name="token" value="<?=TOKEN;?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?=CLOSE;?></button>
            <button type="submit" class="btn btn-success" name="upload"><?=UPLOAD_MODULES;?></button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
