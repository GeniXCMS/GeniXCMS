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
        <span class=\"sr-only\">Close</span>
    </button>";
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
<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-cubes"></i>  <?=CATEGORIES;?> 
            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                <span class="glyphicon glyphicon-plus"></span> <?=ADD_CATEGORY;?>
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

                        if($c->parent == "" || $c->parent == 0){
                            echo "<div class=\"col-md-4 item\" >
                            <div class=\"panel panel-default\">
                                <div class=\"panel-heading\">
                                  <h3 class=\"panel-title\">{$c->name} 
                                  <a href=\"?page=categories&act=del&id={$c->id}&token=".TOKEN."\" class=\"pull-right\"
                                  onclick=\"return confirm('Are you sure you want to delete this item?');\">
                                  <span class=\"glyphicon glyphicon-remove\"></span></a></h3>
                                </div>
                                <div class=\"panel-body\">
                                <ul class=\"list-group\">";
                                foreach ($data['cat'] as $c2) {
                                    if($c2->parent == $c->id){
                                        echo "<li class=\"list-group-item\">
                                        <form action=\"index.php?page=categories\" method=\"POST\" name=\"updatecat\">
                                        <div class=\"input-group\">
                                            <a href=\"?page=categories&act=del&id={$c2->id}&token=".TOKEN."\" class=\"input-group-addon\"
                                            onclick=\"return confirm('Are you sure you want to delete this item?');\"
                                            ><span class=\"glyphicon glyphicon-remove\"></span></a>
                                            <input type=\"text\" name=\"cat\" class=\"form-control\" value=\"{$c2->name}\">
                                            <input type=\"hidden\" name=\"id\" value=\"{$c2->id}\">
                                            <input type=\"hidden\" name=\"token\" value=\"".TOKEN."\">
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
                                    'order_by' => 'name'
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