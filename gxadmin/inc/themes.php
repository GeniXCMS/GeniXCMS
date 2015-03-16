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
        <h1><i class="fa fa-paint-brush"></i>  Themes 
            <button class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                <span class="glyphicon glyphicon-plus"></span> Upload Themes
            </button>
        </h1>
        <hr />
    </div>

    <div class="col-sm-12">
        <div class="row">
            <?php
                $active = Options::get('themes');
                $adata = Theme::data($active);
                echo "
                <div class=\"col-sm-12\">
                <label>Active Theme</label>
                    <div class=\"row\">

                    <div class=\"col-sm-3\">
                        <div class=\"thumbnail\">";
                if(file_exists(GX_THEME."/".$active."/screenshot.png")){
                    echo "<img src=\"".Site::$url."/inc/themes/".$active."/screenshot.png\" 
                    class=\"img-responsive\">";
                }else{
                    echo "<img src=\"".Site::$url."/assets/images/noimagetheme.png\">";
                }
                echo "  </div>
                ";
                echo "</div>
                    <strong>".$adata['name']."</strong><br />
                    <i class=\"fa fa-user\"></i> <a href=\"{$adata['url']}\" target=\"_new\">{$adata['developer']}</a><br />
                ";
                if(!Theme::isActive($active)){
                    echo "
                    <a href=\"index.php?page=themes&act=activate&themes={$active}&token=".TOKEN."\" class=\"label label-success\">
                        Activate
                    </a>";
                }
                if(count($data['themes']) > 1 && !Theme::isActive($active)) {
                    
                    echo "
                    <a href=\"index.php?page=themes&act=remove&themes={$active}&token=".TOKEN."\" class=\"label label-danger\">
                        Remove
                    </a>";
                }
                echo "</div></div>";

                if($data['themes'] > 0) {
                    //print_r($data['themes']);
                    for ($i=0; $i < count($data['themes']); $i++) { 
                        if($data['themes'][$i] == $active){
                            unset($data['themes'][$i]);
                        }else{
                            //$data['themes'][] = $data['themes'][$i];
                        }
                    }
                    echo "&nbsp;<hr>";
                    foreach ($data['themes'] as $thm) {
                        $t = Theme::data($thm);
                        echo "
                            <div class=\"col-sm-3\">
                                <div class=\"thumbnail\">";
                        if(file_exists(GX_THEME."/".$thm."/screenshot.png")){
                            echo "<img src=\"".Site::$url."/inc/themes/".$thm."/screenshot.png\" 
                            class=\"img-responsive\">";
                        }else{
                            echo "<img src=\"".Site::$url."/assets/images/noimagetheme.png\">";
                        }
                        echo "  </div>
                        ";
                        echo "
                            <strong>".$t['name']."</strong><br />
                            <i class=\"fa fa-user\"></i> <a href=\"{$t['url']}\" target=\"_new\">{$t['developer']}</a><br />
                        ";
                        if(!Theme::isActive($thm)){
                            echo "
                            <a href=\"index.php?page=themes&act=activate&themes={$thm}&token=".TOKEN."\" class=\"label label-success\">
                                Activate
                            </a>";
                        }
                        if(count($data['themes']) > 1 && !Theme::isActive($thm)) {
                            
                            echo "
                            <a href=\"index.php?page=themes&act=remove&themes={$thm}&token=".TOKEN."\" class=\"label label-danger\">
                                Remove
                            </a>";
                        }
                        echo "</div>";
                    }
                }else{
                    echo "<div class=\"col-md-12\">No Themes Found</div>";
                }
            ?>
        </div>
        


    </div>
</div>
<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=themes" method="post" enctype="multipart/form-data">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Install Themes</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <label>Browse Theme</label>
                    <input type="file" name="theme" class="form-control">
                    <small>choose the theme file. in zip compression</small>
                </div>
            
          </div>
          <div class="modal-footer">
            <input type="hidden" name="token" value="<?=TOKEN;?>">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success" name="upload">Upload</button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->