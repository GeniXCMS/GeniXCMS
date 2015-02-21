<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : categories.control.php
* version : 0.0.1 pre
* build : 20141006
*/

$data[] = "";
switch (isset($_POST['addcat'])) {
    case true:
        # code...
        $slug = Typo::slugify($_POST['cat']);
        $cat = Db::insert("INSERT INTO `cat` VALUES ('', '{$_POST['cat']}', '{$slug}', '{$_POST['parent']}', '' )");
        break;
    
    default:
        # code...
        break;
}

switch (isset($_POST['updatecat'])) {
    case true:
        # code...
        $vars = array(
                    'table' => 'cat',
                    'id' => $_POST['id'],
                    'key' => array(
                                'name' => $_POST['cat']
                            )
                );
        $cat = Db::update($vars);
        break;
    
    default:
        # code...
        break;
}

if(isset($_GET['act']) == 'del'){
    Categories::delete($_GET['id']);
}
$data['cat'] = Db::result("SELECT * FROM `cat` ORDER BY `id` DESC");
$data['num'] = Db::$num_rows;
System::inc('categories', $data);


/* End of file categories.control.php */
/* Location: ./inc/lib/Control/Backend/categories.control.php */