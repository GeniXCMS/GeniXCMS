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
    $menuid = $data['menus'][0]->menuid;
}

//     print_r($data);

if (isset($_GET['token'])
    && Token::validate(Typo::cleanX($_GET['token']))) {
    $token = TOKEN;
} else {
    $token = '';
}
?>
<form action="" method="POST">

    <div class="col-md-12">
            <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?=MENU_EDIT;?>
        <div class="pull-right">
        <button type="submit" name="edititem" class="btn btn-success btn-sm">
            <span class="glyphicon glyphicon-ok"></span>
            <span class="hidden-xs hidden-sm"><?=SUBMIT;?></span>
        </button>
        <a href="index.php?page=menus" class="btn btn-danger btn-sm">
            <span class="glyphicon glyphicon-remove"></span>
            <span class="hidden-xs hidden-sm"><?=CANCEL;?></span>
        </a>
        </div>
        </h1>
    </section>
<section class="content">
    <div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label><?=MENU_PARENT;?></label>

            <select class="form-control" name="parent">
                <option></option>
            <?php
               //echo($data['abc']);
                //print_r($data['menus']);

            foreach ($data['parent'] as $p) {
                
                if ($p->parent == '0') {
                    if ($data['menus'][0]->parent == $p->id) {
                        $sel = 'SELECTED';
                    } else {
                        $sel = '';
                    }
                    echo "<option value=\"$p->id\" $sel>$p->name</option>";
                    $parent2 = $data['parent'];
                    foreach ($parent2 as $p2) {
                        if ($p2->parent == $p->id) {
                            if ($data['menus'][0]->parent == $p2->id) {
                                $sel = 'SELECTED';
                            } else {
                                $sel = '';
                            }
                            echo "<option value=\"$p2->id\" $sel>&nbsp;&nbsp;&nbsp;$p2->name</option>";
                            foreach ($data['parent'] as $p3) {
                                if ($p3->parent == $p2->id) {
                                    if ($data['menus'][0]->parent == $p3->id) {
                                        $sel = 'SELECTED';
                                    } else {
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
                    if ($data['menus'][0]->type == 'page') {
                        $on = 'checked';
                    } else {
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
                    <?php
                    if ($data['menus'][0]->type == 'cat') {
                        $on = 'checked';
                    } else {
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
                    <?php
                    if ($data['menus'][0]->type == 'mod') {
                        $on = 'checked';
                        $val = $data['menus'][0]->value;
                    } else {
                        $on = '';
                        $val = '';
                    }
                    ?>
                    <span class="input-group-addon">
                        <input type="radio" name='type' class="" value="mod" <?=$on;?>>
                    </span>
                    <select name="mod" class="form-control">
                    <?=Mod::menuList($val);?>
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
                    if ($data['menus'][0]->type == 'custom') {
                        $on = 'checked';
                        $val = $data['menus'][0]->value;
                    } else {
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
</section>
<input type="hidden" name="token" value="<?=$token;?>">
</form>

