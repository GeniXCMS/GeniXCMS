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
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            // VALIDATE ALL
            $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
        }
        if(isset($alertred)){
            $data['alertred'] = $alertred;
        }else{
            $slug = Typo::slugify(Typo::cleanX($_POST['cat']));
            $cat = Typo::cleanX($_POST['cat']);
            $cat = Db::insert(
                        sprintf("INSERT INTO `cat` VALUES (null, '%s', '%s', '%d', '' )", 
                            $cat, $slug, $_POST['parent']
                        )
                    );
            //print_r($cat);
            $data['alertgreen'][] = "Category Added: ".$_POST['cat'];
        }
        break;
    
    default:
        # code...
        break;
}

switch (isset($_POST['updatecat'])) {
    case true:
        # code...
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            // VALIDATE ALL
            $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
        }
        if(isset($alertred)){
            $data['alertred'] = $alertred;
        }else{
            $vars = array(
                        'table' => 'cat',
                        'id' => $_POST['id'],
                        'key' => array(
                                    'name' => Typo::cleanX($_POST['cat'])
                                )
                    );
            $cat = Db::update($vars);
            $data['alertgreen'][] = "Category Updated: ".$_POST['cat'];
        }
        break;
    
    default:
        # code...
        break;
}

if(isset($_GET['act']) == 'del'){
    if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
        // VALIDATE ALL
        $alertred[] = "Token not exist, or your time has expired. Please refresh your browser to get a new token.";
    }
    if(isset($alertred)){
        $data['alertred'] = $alertred;
    }else{
        Categories::delete($_GET['id']);
        $data['alertgreen'][] = "Category Removed";
    }
}
$data['cat'] = Db::result("SELECT * FROM `cat` ORDER BY `id` DESC");
$data['num'] = Db::$num_rows;
System::inc('categories', $data);


/* End of file categories.control.php */
/* Location: ./inc/lib/Control/Backend/categories.control.php */