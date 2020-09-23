<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150202
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (isset($_GET['id'])) {
    $menuid = Typo::cleanX($_GET['id']);
} else {
    $menuid = $data['menuid'];
}

?>
<form action="" method="POST">
<h2><i class="fa fa-sitemap"></i> <span class="hidden-xs hidden-sm"><?=ADD_MENU;?></span>
<div class="pull-right">
<button type="submit" name="additem" class="btn btn-success btn-sm">
    <span class="glyphicon glyphicon-ok"></span>
    <span class="hidden-xs hidden-sm"><?=SUBMIT;?></span>
</button>
<a href="index.php?page=menus" class="btn btn-danger btn-sm">
    <span class="glyphicon glyphicon-remove"></span>
    <span class="hidden-xs hidden-sm"><?=CANCEL;?></span>
</a>

</div>
<hr />
</h2>

<div class="col-sm-12">
    <div class="col-sm-4">
        <div class="form-group">
            <label><?=MENU_PARENT;?></label>
            <select class="form-control" name="parent">
                <option></option>
            <?php
               //echo($data['abc']);
                //print_r($data['parent']);
            foreach ($data['parent'] as $p) {
                
                if ($p->parent == '0') {
                    echo "<option value=\"$p->id\">$p->name</option>";
                    $parent2 = $data['parent'];
                    foreach ($parent2 as $p2) {
                        if ($p2->parent == $p->id) {
                            echo "<option value=\"$p2->id\">&nbsp;&nbsp;&nbsp;$p2->name</option>";
                            foreach ($data['parent'] as $p3) {
                                if ($p3->parent == $p2->id) {
                                    echo "<option value=\"$p3->id\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$p3->name</option>";
                                }
                            }
                        }
                    }
                }
            }

            ?>
            </select>
            <small><?=MENU_PARENT;?></small>
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
            <input type="text" name='name' class="form-control" >
            <small><?=MENU_NAME_DESCR;?></small>
        </div>
    </div>
    <div class="col-sm-4" >
        <div class="form-group">
            <label><?=MENU_CLASS;?></label>
            <input type="text" name='class' class="form-control">
            <small><?=MENU_CLASS_DESCR;?></small>
        </div>
    </div>
    <div class="col-sm-12">
        <h3><?=MENU_TYPE;?></h3>
        <div class="col-sm-4" >
            <div class="form-group">
                <label><?=PAGE;?></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="page">
                    </span>
                    <?php
                        $vars = array(
                                    'name' => 'page',
                                    'type' => 'page',
                                    'sort' => 'ASC',
                                    'order_by' => 'title',
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
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="cat">
                    </span>
                    <?php
                        $vars = array(
                                    'name' => 'cat',
                                    'sort' => 'ASC',
                                    'order_by' => 'name',
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
                    <select name="mod" class="form-control">
                    <?=Mod::menuList();?>
                    </select>
                 </div>
                <small><?=MENU_MODULES_DESCR;?></small>
            </div>
        </div>
        <div class="col-sm-4" >
            <div class="form-group">
                <label><?=MENU_CUSTOM_LINK;?></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="custom">
                    </span>
                    <input class="form-control" name="custom">
                 </div>
                <small><?=MENU_CUSTOM_LINK_DESCR;?></small>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
