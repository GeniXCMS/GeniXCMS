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

    if(isset($data['post'])) {
        foreach ($data['post'] as $p) {
            # code...
            $title = $p->title;
            $content = $p->content;
            $date = $p->date;
            $status = $p->status;
            $cat = $p->cat;
        }
        if($status == 1) {
            $pub = "SELECTED"; 
            $unpub = ""; 
        }elseif ($status == 0) {
            $pub = ""; 
            $unpub = "SELECTED";
        }
    }else{
        $title = "";
        $content = "";
        $date = "";
        $status = "";
        $cat = "";
        $pub = "";
        $unpub = "";
    }
?>
<form action="" method="post" role="form" class="">
<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-file-text-o"></i> New Post
            <div class="pull-right">
                <button type="submit" name="submit" class="btn btn-success">
                    <span class="glyphicon glyphicon-ok"></span>
                    Publish
                </button>

                <a href="index.php?page=posts" class="btn btn-danger">
                    <span class="glyphicon glyphicon-remove"></span>
                    Cancel
                </a>
            </div>
        </h1>
        <hr />
    </div>
    <div class="col-sm-12">
        <div class="row">
            
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="title" name="title" class="form-control" id="title" placeholder="Post Title" value="<?=$title;?>">
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea name="content" class="form-control content editor" id="content" rows="20"><?=$content;?></textarea>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Options</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>Category</label>
                                <?php 
                                    $vars = array(
                                                'order_by'    =>    'name',
                                                'name'    =>    'cat',
                                                'sort'    =>    'ASC',
                                            );
                                    if(isset($cat)) {
                                        $vars = array_merge($vars, array('selected' => $cat));
                                    }
                                    //echo $cat;
                                    echo Categories::dropdown($vars); 
                                ?>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?=$pub;?>>Publish</option>
                                    <option value="0" <?=$unpub;?>>Unpublish</option>
                                </select>
                                <small>published or unpublished</small>
                            </div>

                            <div class="form-group">
                                <label>Post Date</label>
                                <input type="text" name="date" class="form-control" value="<?=$date;?>">
                                <small>left it blank to make it now</small>
                            </div>
                        </div>
                    </div>
                </div>
            
        </div>
        
    </div>
</div>
</form>

