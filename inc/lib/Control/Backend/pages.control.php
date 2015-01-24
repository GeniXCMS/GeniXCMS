<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : page.control.php
* version : 0.0.1 pre
* build : 20141006
*/

if(isset($_GET['act'])) { $act = $_GET['act']; }else{ $act = "";}
switch ($act) {
    case 'add':
        # code...
        $data[''] = "";

        switch (isset($_POST['submit'])) {
            case true:
                # code...
                if (!isset($_POST['date']) || $_POST['date'] == "") {
                    # code...
                    $date = date("Y-m-d H:i:s");
                }else{
                    $date = $_POST['date'];
                }
                $vars = array(
                                'title' => Typo::cleanX($_POST['title']),
                                'content' => Typo::cleanX($_POST['content']),
                                'date' => $date,
                                'type' => 'page',
                                'author' => Session::val('username'),
                                'status' => $_POST['status'],
                            );
                //print_r($vars);
                Posts::insert($vars);
                $data['alertgreen'][] = "Page : {$_POST['title']} Added.";
                break;
            
            default:
                # code...
                //System::inc('pages_form', $data);
                break;
        }
        System::inc('pages_form', $data);
        break;

    case 'edit':
        //echo "edit";
        switch (isset($_POST['submit'])) {
            case true:
                # code...
                if (!isset($_POST['date']) || $_POST['date'] == "") {
                    # code...
                    $date = date("Y-m-d H:i:s");
                }else{
                    $date = $_POST['date'];
                }
                $moddate = date("Y-m-d H:i:s");
                $vars = array(
                                'title' => Typo::cleanX($_POST['title']),
                                'content' => Typo::cleanX($_POST['content']),
                                'modified' => $moddate,
                                'date' => $date,
                                'status' => $_POST['status'],
                            );
                //print_r($vars);
                Posts::update($vars);
                $data['alertgreen'][] = "Page : {$_POST['title']} Updated.";
                break;
            
            default:
                # code...
                //System::inc('posts_form', $data);
                break;
        }

        $data['post'] = Db::result("SELECT * FROM `posts` WHERE `id` = '{$_GET['id']}' ");
        System::inc('pages_form', $data);

        break;


    default:
        # code...
        if(isset($_GET['act']) && $_GET['act'] == 'del'){
            if(isset($_GET['id'])){
                $title = Posts::title($_GET['id']);
                $del = Posts::delete($_GET['id']);
                //echo $title['error'];
                if(isset($del['error'])){
                    $data['alertred'][] = $del['error'];
                }else{
                    $data['alertgreen'][] = 'Post <b>'.$title.'</b> Removed';
                }
                
            }else{
                $data['alertred'][] = 'No ID Selected';
            }
            
        }
        $max = "10";
        if(isset($_GET['paging'])){
            $paging = $_GET['paging'];
            $offset = ($_GET['paging']-1)*$max;
        }else{
            $paging = 1;
            $offset = 0;
        }
        
        $data['posts'] = Db::result("SELECT * FROM `posts` WHERE `type` = 'page' ORDER BY `date` DESC LIMIT {$offset},{$max}");
        $data['num'] = Db::$num_rows;
        System::inc('pages', $data);

        $page = array(
                    'paging' => $paging,
                    'table' => 'posts',
                    'where' => "`type` = 'page'",
                    'max' => $max,
                    'url' => 'index.php?page=pages',
                    'type' => 'pager'
                );
        echo Paging::create($page);
        break;
}

/* End of file pages.control.php */
/* Location: ./inc/lib/Control/Backend/pages.control.php */