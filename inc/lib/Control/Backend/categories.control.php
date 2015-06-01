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

$data['sitetitle'] = CATEGORIES;
switch (isset($_POST['addcat'])) {
    case true:
        # code...
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            // VALIDATE ALL
            $alertred[] = TOKEN_NOT_EXIST;
        }
        if (!isset($_POST['cat']) || $_POST['cat'] == "") {
            $alertred[] = CATEGORY_CANNOT_EMPTY;
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
            $data['alertgreen'][] = MSG_CATEGORY_ADDED." ".$_POST['cat'];
        }
        if(isset($_POST['token'])){ Token::remove($_POST['token']); }
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
            $alertred[] = TOKEN_NOT_EXIST;
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
            $data['alertgreen'][] = MSG_CATEGORY_UPDATED." ".$_POST['cat'];
        }
        if(isset($_POST['token'])){ Token::remove($_POST['token']); }
        break;
    
    default:
        # code...
        break;
}

if(isset($_GET['act']) == 'del'){
    if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
        // VALIDATE ALL
        $alertred[] = TOKEN_NOT_EXIST;
    }
    if(isset($alertred)){
        $data['alertred'] = $alertred;
    }else{
        Categories::delete($_GET['id']);
        $data['alertgreen'][] = MSG_CATEGORY_REMOVED;
    }
    if(isset($_GET['token'])){ Token::remove($_GET['token']); }
}
$data['cat'] = Db::result("SELECT * FROM `cat` ORDER BY `id` DESC");
$data['num'] = Db::$num_rows;
Theme::admin('header', $data);
System::inc('categories', $data);
Theme::admin('footer');


/* End of file categories.control.php */
/* Location: ./inc/lib/Control/Backend/categories.control.php */