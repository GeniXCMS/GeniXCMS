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

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-cubes"></i>  <?=CATEGORIES;?>
            <small>
                Categories Manager
            </small>
            <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#myModal">
                <span class="glyphicon glyphicon-plus"></span>
                <span class="hidden-xs hidden-sm"><?=ADD_CATEGORY;?></span>
            </button>
        </h1>

    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <small class="label label-default pull-left"><?=Stats::totalCat('post');?> total</small>
                </h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <?php
                    if ($data['num'] > 0) {
                        foreach ($data['cat'] as $c) {
                            // echo "<td>".$c->id."</td>";
                            // echo "<td>".$c->name."</td>";
                            // echo "<td>".$c->parent."</td>";
                            // echo "<td></td>";

                            if ($c->parent == '' || $c->parent == 0) {
                                echo "<div class=\"col-sm-6 col-md-4 item\" >
                            <div class=\"box box-warning\">
                                <div class=\"box-header\">
                                    <form action=\"index.php?page=categories\" method=\"POST\" name=\"updatecat\">
                                    <div class=\"input-group\">
                                        <a href=\"?page=categories&act=del&id={$c->id}&token=".TOKEN."\" class=\"input-group-addon\"
                                        onclick=\"return confirm('Are you sure you want to delete this item?');\"
                                        ><span class=\"glyphicon glyphicon-remove\"></span></a>
                                        <input type=\"text\" name=\"cat\" class=\"form-control\" value=\"{$c->name}\">
                                        <input type=\"hidden\" name=\"id\" value=\"{$c->id}\">
                                        <input type=\"hidden\" name=\"token\" value=\"".TOKEN.'">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="submit" name="updatecat">Go!</button>
                                        </span>
                                    </div>
                                    </form>

                                </div>
                                <div class="box-body">
                                <ul class="list-group">';
                                foreach ($data['cat'] as $c2) {
                                    if ($c2->parent == $c->id) {
                                        echo "<li class=\"list-group-item\">
                                        <form action=\"index.php?page=categories\" method=\"POST\" name=\"updatecat\">
                                        <div class=\"input-group\">
                                            <a href=\"?page=categories&act=del&id={$c2->id}&token=".TOKEN."\" class=\"input-group-addon\"
                                            onclick=\"return confirm('Are you sure you want to delete this item?');\"
                                            ><span class=\"glyphicon glyphicon-remove\"></span></a>
                                            <input type=\"text\" name=\"cat\" class=\"form-control\" value=\"{$c2->name}\">
                                            <input type=\"hidden\" name=\"id\" value=\"{$c2->id}\">
                                            <input type=\"hidden\" name=\"token\" value=\"".TOKEN.'">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit" name="updatecat">Go!</button>
                                            </span>
                                        </div>
                                        </form>
                                         </li>';
                                    }
                                }
                                echo '</ul></div>';
                                echo '</div></div>';
                            }
                        }
                    } else {
                        echo '<div class="col-md-12">No Categories Found</div>';
                    }
                    ?>
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

<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=categories" method="post">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?=ADD_CATEGORY;?></h4>
          </div>
          <div class="modal-body">

                <div class="form-group">
                    <label><?=PARENTS;?></label>
                    <?php
                        $vars = array(
                                    'parent' => '0',
                                    'name' => 'parent',
                                    'sort' => 'ASC',
                                    'order_by' => 'name',
                                    'type' => 'post',
                                );
                        echo Categories::dropdown($vars);
                    ?>
                </div>
                <div class="form-group">
                    <label><?=CATEGORY_NAME;?></label>
                    <input type="text" name="cat" class="form-control">
                </div>

          </div>
          <div class="modal-footer">
            <input type="hidden" name="token" value="<?=TOKEN;?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?=CLOSE;?></button>
            <button type="submit" class="btn btn-success" name="addcat"><?=SUBMIT;?></button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
