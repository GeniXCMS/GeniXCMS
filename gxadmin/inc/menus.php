<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150202
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

if (isset($data['alertgreen'])) {
    # code...
    echo "<div class=\"alert alert-success\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\"><?=CLOSE;?></span>
    </button>
    <ul>";
    foreach ($data['alertgreen'] as $alert) {
        # code...
        echo "<li>$alert</li>\n";
    }
    echo "</ul></div>";
}elseif (isset($data['alertred'])) {
    # code...
    //print_r($data['alertred']);
    echo "<div class=\"alert alert-danger\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\"><?=CLOSE;?></span>
    </button>
    <ul>";
    foreach ($data['alertred'] as $alert) {
        # code...
        echo "<li>$alert</li>\n";
    }
    echo "</ul></div>";
}
?>
<div class="row">
    <div class="col-md-12">

        <h1><i class="fa fa-sitemap"></i> <?=MENUS;?>
            <div class="pull-right">
                <button class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                    <span class="glyphicon glyphicon-plus"></span> <?=ADD_MENU;?>
                </button>
            </div>
        </h1>
        <hr />
    </div>
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
            <?php
                if (isset($data['menus']) && $data['menus'] != '') {
                    # code...
                     foreach (json_decode($data['menus']) as $k => $m) {
                        # code...
                        echo "
                        <div class=\"panel-group\" id=\"accordion\">
                          <div class=\"panel panel-default\">
                            <div class=\"panel-heading\">
                              <div class=\"panel-title clearfix\">
                                <a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#$k\">
                                    <div class=\"col-md-4\">
                                        <h4><strong>$m->name </strong></h4>
                                    </div>
                                    <div class=\"col-md-4\">
                                        <h4>
                                        <small>
                                            <em>$k</em>
                                        </small>
                                        </h4>
                                    </div>
                                </a>
                                <div class=\"col-md-3\">
                                    <div class=\"input-group\">
                                        <input type=\"text\" value=\"$m->class\" placeholder=\"Class Style\" class=\"form-control\">
                                        <span class=\"input-group-btn\">
                                        <button name=\"editclass\" type=\"submit\" class=\"btn btn-default\">
                                            Go!
                                        </button>
                                        </span>

                                    </div>
                                </div>
                                <div class=\"col-md-1\">
                                    <h5><a href=\"index.php?page=menus&act=remove&menuid={$k}&token=".TOKEN."\"><i class=\"fa fa-remove\"></i></a></h5>
                                </div>
                              </div>
                            </div>
                            <div id=\"$k\" class=\"panel-collapse collapse\">
                                <div class=\"panel-body\">
                                    <!-- Nav tabs -->
                                        <ul class=\"nav nav-tabs\" role=\"tablist\">
                                            <li class=\"active\"><a href=\"#{$k}menuitem\" role=\"tab\" data-toggle=\"tab\">".MENU_ITEMS."</a></li>
                                            <li><a href=\"#{$k}additem\" role=\"tab\" data-toggle=\"tab\">".ADD_MENU_ITEM."</a></li>
                                        </ul>
                                        <!-- Tab panes -->
                                        <div class=\"tab-content\">
                                          <div class=\"tab-pane active\" id=\"{$k}menuitem\">
                                          <br />
                                              <div class=\"col-md-12\">
                                           ";
                                            echo Menus::getMenuAdmin($k,'nav nav-pills nav-stacked');

                            echo "
                                              </div>
                                          </div>
                                          <div class=\"tab-pane\" id=\"{$k}additem\">
                                          ";
                                              $data['parent'] = Menus::isHadParent('', $k);
                                              //print_r($data['parent']);
                                              $data['menuid'] = $k;
                                              System::inc('menus_form', $data);
                            echo "
                                          </div>
                                        </div>


                                </div>
                            </div>
                        </div>
                      </div>


                    ";
                    }
                }

                    //echo "<pre>"; print_r(json_decode($data['menus'])); echo "</pre>";
                ?>
                </div>
        </div>

    </div>
</div>

<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=menus" method="post">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?=ADD_MENU;?></h4>
          </div>
          <div class="modal-body clearfix">

                <div class="col-sm-12" >
                    <div class="form-group">
                        <label><?=MENU_ID;?></label>
                        <input type="text" name='id' class="form-control">
                        <small><?=MENU_ID_DESC;?></small>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="form-group">
                        <label><?=MENU_NAME;?></label>
                        <input type="text" name='name' class="form-control">
                        <small><?=MENU_NAME_DESC;?></small>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="form-group">
                        <label><?=MENU_CLASS;?></label>
                        <input type="text" name='class' class="form-control">
                        <small<?=MENU_CLASS_DESC;?></small>
                    </div>
                </div>

          </div>
          <div class="modal-footer">
            <input type="hidden" name="token" value="<?=TOKEN;?>">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success" name="submit">Save changes</button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
