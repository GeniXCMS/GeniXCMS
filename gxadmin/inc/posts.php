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
        <h1><i class="fa fa-file-text-o"></i> <?=POSTS;?> 
            <a href="index.php?page=posts&act=add&token=<?=TOKEN;?>" 
            class="btn btn-primary pull-right">
                <i class="fa fa-plus"></i> <?=ADD_NEW_POST;?>
            </a>
        </h1>
        <hr />
    </div>
    <div class="col-sm-12">
        <form action="index.php?page=posts" method="get">
            <input type="hidden" name="page" value="posts">
            <div class="row">
                <div class="col-sm-12">
                    <h5><?=FIND_POSTS;?></h5>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <input type="text" name="q" class="form-control" placeholder="<?=SEARCH_POSTS;?>">
                    </div>
                    
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                    <?php 
                        $vars = array(
                                    'name' => 'cat',

                                    ); 
                        echo Categories::dropdown($vars);
                    ?>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <input type="date" class="form-control" name="from">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <input type="date" class="form-control" name="to">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="1"><?=PUBLISHED;?></option>
                            <option value="0"><?=UNPUBLISHED;?></option>
                            
                        </select>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <span class="glyphicon glyphicon-search"></span> <?=FIND_POSTS;?>
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="token" value="<?=TOKEN;?>">
        </form>
    </div>
    <div class="col-sm-12">

    <div class="table-responsive">
    <table class="table table-hover">
        <form action="" method="post">
        <thead>
            <tr>
                <th><?=ID;?></th>
                <th><?=TITLE;?></th>
                <th><?=CATEGORY;?></th>
                <th><?=DATE;?></th>
                <th><?=STATUS;?></th>
                <th><?=ACTION;?></th>
                <th><?=ALL;?> <input type="checkbox" id="selectall"></th>
            </tr>
        </thead>
        <tbody>
            <?php
                //print_r($data);
                if($data['num'] > 0){
                    foreach ($data['posts'] as $p) {
                        # code...
                        //print_r($p);
                        //echo $p->id;
                        if($p->status == '0'){
                            $status = UNPUBLISHED;
                        }else{
                            $status = PUBLISHED;
                        }
                        echo "
                        <tr>
                            <td>{$p->id}</td>
                            <td><a href=\"".Url::post($p->id)."\" target=\"_new\">{$p->title}</a></td>
                            <td>".Categories::name($p->cat)."</td>
                            <td>".Date::format($p->date)."</td>
                            <td>{$status}</td>
                            <td>
                                <a href=\"index.php?page=posts&act=edit&id={$p->id}&token=".TOKEN."\" class=\"label label-success\">".EDIT."</a> 
                                <a href=\"index.php?page=posts&act=del&id={$p->id}&token=".TOKEN."\" class=\"label label-danger\" 
                                onclick=\"return confirm('Are you sure you want to delete this item?');\">".DELETE."</a>
                            </td>
                            <td>
                                <input type=\"checkbox\" name=\"post_id[]\" value=\"{$p->id}\" id=\"select\">
                            </td>
                        </tr>
                        ";
                    }
                }else{
                    echo "
                    <tr>
                        <td>
                        ".NO_POST_FOUND."
                        </td>
                    </tr>";
                }
            ?>
            
        </tbody>
        <tfoot>
            <th><?=ID;?></th>
            <th><?=TITLE;?></th>
            <th><?=CATEGORY;?></th>
            <th><?=DATE;?></th>
            <th colspan="2">
            <select name="action" class="form-control">
                <option value="publish"><?=PUBLISH;?></option>
                <option value="unpublish"><?=UNPUBLISH;?></option>
                <option value="delete"><?=DELETE;?></option>
            </select>
            <input type="hidden" name="token" value="<?=TOKEN;?>">
            </th>
            <th>
                <button type="submit" name="doaction" class="btn btn-danger">
                    <span class="glyphicon glyphicon-ok"></span> <?=SUBMIT;?>
                </button>
            </th>
        </tfoot>
        </form>
    </table>
    </div>
    </div>
</div>