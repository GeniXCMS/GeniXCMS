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
        <h1><i class="fa fa-cubes"></i>  Categories 
            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                <span class="glyphicon glyphicon-plus"></span> Add Category
            </button>
        </h1>
        <hr />
    </div>

    <div class="col-sm-12">
        <div class="row">
            <?php
                if($data['num'] > 0) {
                    foreach ($data['cat'] as $c) {
                        # code...
                        // echo "<td>".$c->id."</td>";
                        // echo "<td>".$c->name."</td>";
                        // echo "<td>".$c->parent."</td>";
                        // echo "<td></td>";

                        if($c->parent == ""){
                            echo "<div class=\"col-md-4 item\" >
                            <div class=\"panel panel-default\">
                                <div class=\"panel-heading\">
                                  <h3 class=\"panel-title\">{$c->name} 
                                  <a href=\"?page=categories&act=del&id={$c->id}\" class=\"pull-right\"
                                  onclick=\"return confirm('Are you sure you want to delete this item?');\">
                                  <span class=\"glyphicon glyphicon-remove\"></span></a></h3>
                                </div>
                                <div class=\"panel-body\">
                                <ul class=\"list-group\">";
                                foreach ($data['cat'] as $c2) {
                                    if($c2->parent == $c->id){
                                        echo "<li class=\"list-group-item\">
                                        <form action=\"\" method=\"POST\" name=\"updatecat\">
                                        <div class=\"input-group\">
                                            <a href=\"?page=categories&act=del&id={$c2->id}\" class=\"input-group-addon\"
                                            onclick=\"return confirm('Are you sure you want to delete this item?');\"
                                            ><span class=\"glyphicon glyphicon-remove\"></span></a>
                                            <input type=\"text\" name=\"cat\" class=\"form-control\" value=\"{$c2->name}\">
                                            <input type=\"hidden\" name=\"id\" value=\"{$c2->id}\">
                                            <span class=\"input-group-btn\">
                                                <button class=\"btn btn-default\" type=\"submit\" name=\"updatecat\">Go!</button>
                                            </span>
                                        </div>
                                        </form>
                                         </li>";
                                    }
                                }
                            echo "</ul></div>";
                            echo "</div></div>";
                        }
                    }
                }else{
                    echo "<div class=\"col-md-12\">No Categories Found</div>";
                }
            ?>
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
            <h4 class="modal-title" id="myModalLabel">Categories</h4>
          </div>
          <div class="modal-body">
            
                <div class="form-group">
                    <label>Parent</label>
                    <?php
                        $vars = array(
                                    'parent' => '',
                                    'name' => 'parent',
                                    'sort' => 'ASC',
                                    'order_by' => 'name'
                                );
                        echo Categories::dropdown($vars);
                    ?>
                </div>
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="cat" class="form-control">
                </div>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success" name="addcat">Save changes</button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->