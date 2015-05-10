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
?>
<form action="" method="post">
<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-group"></i> Edit User 
            <div class="pull-right">
                <button  class="btn btn-success " type="submit" name="edituser">
                    <span class="glyphicon glyphicon-ok"></span>
                    Update
                </button>
                <a class="btn btn-danger  " href="index.php?page=users">
                    <span class="glyphicon glyphicon-remove"></span>
                    Cancel
                </a>
                
            </div>
        </h1>
        <hr />
    </div>
    <div class="col-sm-12">
    <div class="row">

    <div class="col-sm-6">
        <div class="form-group">
            <label>Userid</label>
            <input type="text" name="userid" class="form-control" value="<?=User::userid($_GET['id']);?>">
            <small>Only Admin can edit userid</small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?=User::email($_GET['id']);?>">
            <small>Email must be different with another.</small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="pass" class="form-control" value="">
            <small>Type password to change it. Left it blank to use old password.</small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Group Level</label>
            <?php 
            if(User::group($_GET['id']) == 0){
                $adm = "SELECTED";
                $mem = "";
                $aut = "";
            }elseif(User::group($_GET['id']) == 3){
                $aut = "SELECTED";
                $adm = "";
                $mem = "";
            }elseif(User::group($_GET['id']) == 4){
                $mem = "SELECTED";
                $adm = "";
                $aut = "";
            }

            ?>
            <select name="group" class="form-control">
                <option value="0" <?=$adm;?>>Administrator</option>
                <option value="3" <?=$aut;?>>Author</option>
                <option value="4" <?=$mem;?>>General Members</option>
            </select> 
            <small>Group Level of the user.</small>
        </div>
    </div>
        

    </div>
    </div>
</div>
<input type="hidden" name="token" value="<?=$_GET['token'];?>">
</form>
