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
        <h1><i class="fa fa-plug"></i>  Modules 
            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                <span class="glyphicon glyphicon-plus"></span> Upload Modules
            </button>
        </h1>
        <hr />
    </div>

    <div class="col-sm-12">
        <div class="row">
            <table class="table table-responsive">
                <thead>
                    <th>Name</th>
                    <th>Descriptions</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    <?php
                        if(count($data['mods']) > 0) {
                            foreach ($data['mods'] as $mod) {
                                $m = Mod::data($mod);
                                if (Mod::isActive($mod)) {
                                    # code...
                                    $btnact = "warning";
                                    $act = "deactivate";
                                }else{
                                    $btnact = "success";
                                    $act = "activate";
                                }
                                echo "
                                <tr>
                                    <td>
                                        {$m['icon']} <strong>{$m['name']}</strong><br />
                                        <small>version: {$m['version']} - license: {$m['license']}</small>
                                    </td>
                                    <td>
                                        <p title=\"{$m['desc']}\">".substr($m['desc'], 0,180)."</p>
                                        <small>author: <a href=\"{$m['url']}\">{$m['developer']}</a></small>
                                    </td>
                                    <td>
                                        <a href=\"index.php?page=modules&act={$act}&modules={$mod}&token=".TOKEN."\" class=\"label label-{$btnact}\">{$act}</a> 
                                        ";
                                if (!Mod::isActive($mod)) {
                                    echo "<a href=\"index.php?page=modules&act=remove&modules={$mod}&token=".TOKEN."\" class=\"label label-danger\" disable>remove</a>";
                                }
                                echo"
                                    </td>
                                </tr>";
                                //echo $m;
                                
                            }
                        }else{
                            echo "<div class=\"col-md-12\">No Categories Found</div>";
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
        


    </div>
</div>
<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=modules" method="post" enctype="multipart/form-data">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Upload Module</h4>
          </div>
          <div class="modal-body">

                <div class="form-group">
                    <label>Choose Module</label>
                    <input type="file" name="module" class="form-control">
                    <small>choose the modules file. in zip compression</small>
                </div>
            
          </div>
          <div class="modal-footer">
            <input type="hidden" name="token" value="<?=TOKEN;?>">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success" name="upload">Save changes</button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->