<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150202
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/?>
<div class="row">
    <div class="col-md-12">

        <h1><i class="fa fa-sitemap"></i> Menus
            <div class="pull-right">
                <button class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                    <span class="glyphicon glyphicon-plus"></span> Add Menu
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
                                    <div class=\"col-md-4\">
                                        <div class=\"input-group\">
                                            <input type=\"text\" value=\"$m->class\" placeholder=\"Class Style\" class=\"form-control\">
                                            <span class=\"input-group-btn\">
                                            <button name=\"editclass\" type=\"submit\" class=\"btn btn-default\">
                                                Go!
                                            </button>
                                            </span>

                                        </div>
                                    </div>

                              </div>
                            </div>
                            <div id=\"$k\" class=\"panel-collapse collapse\">
                                <div class=\"panel-body\">
                                    <!-- Nav tabs -->
                                        <ul class=\"nav nav-tabs\" role=\"tablist\">
                                            <li class=\"active\"><a href=\"#{$k}menuitem\" role=\"tab\" data-toggle=\"tab\">Menu Items</a></li>
                                            <li><a href=\"#{$k}additem\" role=\"tab\" data-toggle=\"tab\">Add Item</a></li>
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
                                              $data['parent'] = Menus::getParent('', $k);
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
            <form action="" method="post">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Add Menu</h4>
          </div>
          <div class="modal-body clearfix">

                <div class="col-sm-12" >
                    <div class="form-group">
                        <label>Menu ID</label>
                        <input type="text" name='id' class="form-control">
                        <small>ID of the menu, eg. <code>mainmenu</code></small>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="form-group">
                        <label>Menu Name</label>
                        <input type="text" name='name' class="form-control">
                        <small>Name of the menu</small>
                    </div>
                </div>
                <div class="col-sm-12" >
                    <div class="form-group">
                        <label>Menu Class</label>
                        <input type="text" name='class' class="form-control">
                        <small>Class Style of the menu. <code>.class</code> means menu class is <em>class</em></small>
                    </div>
                </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success" name="submit">Save changes</button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
