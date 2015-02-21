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

    if(isset($_GET['id'])){
        $menuid = $_GET['id'];
    }else{
        $menuid = $data['menuid'];
    }
?>
<form action="" method="POST">
<h1><i class="fa fa-sitemap"></i> Add Menu
<div class="pull-right">
<button type="submit" name="additem" class="btn btn-success">
    <span class="glyphicon glyphicon-ok"></span>
    Submit
</button>
<a href="index.php?page=menus" class="btn btn-danger">
    <span class="glyphicon glyphicon-remove"></span>
    Cancel
</a>
</div>
</h1>
<div class="col-sm-12">
    <div class="col-sm-4">
        <div class="form-group">
            <label>Parent Menu</label>
            <select class="form-control" name="parent">
                <option></option>
            <?php
               //echo($data['abc']);
                //print_r($data['parent']);
                foreach ($data['parent'] as $p) {
                    # code...
                    echo "<option value=\"$p->id\">$p->name</option>";
                }
                
            ?>
            </select>
            <small>Parent menu</small>
        </div>
    </div>
    <div class="col-sm-4" >
        <div class="form-group">
            <label>Menu ID</label>
            <input type="text" name='id' class="form-control" value="<?=$menuid;?>" readonly >
            <small>ID of the menu, eg. <code>mainmenu</code></small>
        </div>
    </div>
    <div class="col-sm-4" >
        <div class="form-group">
            <label>Menu Name</label>
            <input type="text" name='name' class="form-control" >
            <small>Name of the menu</small>
        </div>
    </div>
    <div class="col-sm-4" >
        <div class="form-group">
            <label>Menu Class</label>
            <input type="text" name='class' class="form-control">
            <small>Class Style of the menu. <code>.class</code> means menu class is <em>class</em></small>
        </div>
    </div>
    <div class="col-sm-12">
        <h3>Menu Type</h3>
        <div class="col-sm-4" >
            <div class="form-group">
                <label>Page</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="page">
                    </span>
                    <?php
                        $vars = array(
                                    'name' => 'page',
                                    'type' => 'page',
                                    'sort' => 'ASC',
                                    'order_by' => 'title'
                                );
                        echo Posts::dropdown($vars);
                    ?>
                 </div>
                <small>Choose This if you want menu for Pages. Pick Page Name</small>
            </div>
        </div>
        <div class="col-sm-4" >
            <div class="form-group">
                <label>Categories</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="cat">
                    </span>
                    <?php
                        $vars = array(
                                    'name' => 'cat',
                                    'sort' => 'ASC',
                                    'order_by' => 'name'
                                );
                        echo Categories::dropdown($vars);
                    ?>
                 </div>
                <small>Choose This if you want menu for Categories. Pick Category Name</small>
            </div>
        </div>
        <div class="col-sm-4" >
            <div class="form-group">
                <label>Mod</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="mod">
                    </span>
                    <select class="form-control">
                        
                    </select>
                 </div>
                <small>Choose This if you want menu for Modules(Mod). Pick Module Name</small>
            </div>
        </div>
        <div class="col-sm-4" >
            <div class="form-group">
                <label>Custom Link</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="custom">
                    </span>
                    <input class="form-control" name="custom">
                 </div>
                <small>Choose This if you want menu with Custom Link Categories. Insert Custom Link</small>
            </div>
        </div>
    </div>
</div>
</form>