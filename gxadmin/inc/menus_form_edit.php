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

if(isset($_GET['id'])){
    $menuid = $_GET['id'];
}else{
    $menuid = $data['menuid'];
}

    // print_r($data['menus']);
if (isset($data['alertgreen'])) {
    # code...
    echo "<div class=\"alert alert-success\" >
    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
        <span aria-hidden=\"true\">&times;</span>
        <span class=\"sr-only\">Close</span>
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
        <span class=\"sr-only\">Close</span>
    </button>
    <ul>";
    foreach ($data['alertred'] as $alert) {
        # code...
        echo "<li>$alert</li>\n";
    }
    echo "</ul></div>";
}

if (isset($_GET['token']) 
    && Token::isExist($_GET['token'])) {
    $token = TOKEN;
}else{
    $token = '';
}
?>
<form action="" method="POST">
<div class="row">
    <div class="col-md-12">
<h1><i class="fa fa-sitemap"></i> <?=MENU_EDIT;?>
<div class="pull-right">
<button type="submit" name="edititem" class="btn btn-success">
    <span class="glyphicon glyphicon-ok"></span>
    <?=SUBMIT;?>
</button>
<a href="index.php?page=menus" class="btn btn-danger">
    <span class="glyphicon glyphicon-remove"></span>
    <?=CANCEL;?>
</a>
</div>
</h1>
</div>
<div class="col-sm-12">
    <div class="col-sm-4">
        <div class="form-group">
            <label><?=MENU_PARENT;?></label>
            
            <select class="form-control" name="parent">
                <option></option>
            <?php
               //echo($data['abc']);
                //print_r($data['menus']);
                
                foreach ($data['parent'] as $p) {
                    # code...
                    if($p->parent == ''){
                        if($data['menus'][0]->parent == $p->id){
                            $sel = 'SELECTED';
                        }else{
                            $sel = '';
                        }
                        echo "<option value=\"$p->id\" $sel>$p->name</option>";
                        $parent2 = $data['parent'];
                        foreach ( $parent2 as $p2) {
                            if ($p2->parent == $p->id) {
                                if($data['menus'][0]->parent == $p2->id){
                                    $sel = 'SELECTED';
                                }else{
                                    $sel = '';
                                }
                                echo "<option value=\"$p2->id\" $sel>&nbsp;&nbsp;&nbsp;$p2->name</option>";
                                foreach ($data['parent'] as $p3) {
                                    if ($p3->parent == $p2->id) {
                                        if($data['menus'][0]->parent == $p3->id){
                                            $sel = 'SELECTED';
                                        }else{
                                            $sel = '';
                                        }
                                        echo "<option value=\"$p3->id\" $sel>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$p3->name</option>";
                                    }
                                }
                            }
                        }
                    }
                    
                }
            ?>
            </select>
            <small><?=MENU_PARENT_DESCR;?></small>
        </div>
    </div>
    <div class="col-sm-4" >
        <div class="form-group">
            <label><?=MENU_ID;?></label>
            <input type="text" name='id' class="form-control" value="<?=$menuid;?>" readonly >
            <small><?=MENU_ID_DESCR;?></small>
        </div>
    </div>
    <div class="col-sm-4" >
        <div class="form-group">
            <label><?=MENU_NAME;?></label>
            <input type="text" name='name' class="form-control" value="<?=$data['menus'][0]->name;?>">
            <small><?=MENU_NAME_DESCR;?></small>
        </div>
    </div>
    <div class="col-sm-4" >
        <div class="form-group">
            <label><?=MENU_CLASS;?></label>
            <input type="text" name='class' class="form-control" value="<?=$data['menus'][0]->class;?>">
            <small><?=MENU_CLASS_DESCR;?></small>
        </div>
    </div>
    <div class="col-sm-12">
        <h3><?=MENU_TYPE;?></h3>
        <div class="col-sm-4" >
            <div class="form-group">
                <label><?=PAGE;?></label>
                <div class="input-group">
                    <?php
                        if($data['menus'][0]->type == 'page'){
                            $on = 'checked';
                        }else{
                            $on = '';
                        }
                    ?>
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="page" <?=$on;?>>
                    </span>
                    <?php
                        $vars = array(
                                    'name' => 'page',
                                    'type' => 'page',
                                    'sort' => 'ASC',
                                    'selected' => $data['menus'][0]->value,
                                    'order_by' => 'title'
                                );
                        echo Posts::dropdown($vars);
                    ?>
                 </div>
                <small><?=MENU_PAGE_DESCR;?></small>
            </div>
        </div>
        <div class="col-sm-4" >
            <div class="form-group">
                <label><?=CATEGORIES;?></label>
                <div class="input-group">
                    <?php
                        if($data['menus'][0]->type == 'cat'){
                            $on = 'checked';
                        }else{
                            $on = '';
                        }
                    ?>
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="cat" <?=$on;?>>
                    </span>
                    <?php
                        $vars = array(
                                    'name' => 'cat',
                                    'sort' => 'ASC',
                                    'selected' => $data['menus'][0]->value,
                                    'order_by' => 'name'
                                );
                        echo Categories::dropdown($vars);
                    ?>
                 </div>
                <small><?=MENU_CATEGORIES_DESCR;?></small>
            </div>
        </div>
        <div class="col-sm-4" >
            <div class="form-group">
                <label><?=MODULES;?></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="mod">
                    </span>
                    <select class="form-control">
                        
                    </select>
                 </div>
                <small><?=MENU_MODULES_DESCR;?></small>
            </div>
        </div>
        <div class="col-sm-4" >
            <div class="form-group">
                <label><?=MENU_CUSTOM_LINK;?></label>
                <div class="input-group">
                    <?php
                        if($data['menus'][0]->type == 'custom'){
                            $on = 'checked';
                            $val = $data['menus'][0]->value;
                        }else{
                            $on = '';
                            $val = '';
                        }
                    ?>
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="custom" <?=$on;?>>
                    </span>
                    <input class="form-control" name="custom" value="<?=$val;?>">
                 </div>
                <small><?=MENU_CUSTOM_LINK_DESCR;?></small>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="token" value="<?=$token;?>">
</form>
</div>