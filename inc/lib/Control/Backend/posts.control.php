<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141006
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

$data['sitetitle'] = POSTS;
if(isset($_GET['act'])) $act = $_GET['act']; else $act = "";
switch ($act) {
    case 'add':
        # code...
        $data[] = '';
        switch (isset($_POST['submit'])) {
            case true:
                # code...
                // check token first
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                //cleanup first
                $title = Typo::cleanX($_POST['title']);
                $title = Hooks::filter('post_submit_title_filter', $title);

                $content = Typo::cleanX($_POST['content']);
                $content = Hooks::filter('post_submit_content_filter', $content);

                if (!isset($title) || $title == "") {
                    $alertred[] = TITLE_CANNOT_EMPTY;
                }
                if(isset($alertred)){
                    $data['alertred'] = $alertred;
                }else{
                    if (!isset($_POST['date']) || $_POST['date'] == "") {
                        # code...
                        $date = date("Y-m-d H:i:s");
                    }else{
                        $date = $_POST['date'];
                    }
                    $vars = array(
                                    'title' => $title,
                                    'cat' => Typo::int($_POST['cat']),
                                    'content' => $content,
                                    'date' => $date,
                                    'type' => 'post',
                                    'author' => Session::val('username'),
                                    'status' => Typo::int($_POST['status'])
                                );
                    //print_r($vars);
                    Posts::insert($vars);
                    $data['alertgreen'][] = POST." {$_POST['title']} ".MSG_POST_ADDED;
                    Hooks::run('post_submit_add_action', $_POST);
                    Token::remove($_POST['token']);
                }

                break;
            
            default:
                # code...
                
                break;
        }
        Theme::admin('header', $data);
        System::inc('posts_form', $data);
        Theme::admin('footer');
        //echo "add";
        break;

    case 'edit':
        //echo "edit";
        switch (isset($_POST['submit'])) {
            case true:
                # code...
                // check token first
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    $alertred[] = TOKEN_NOT_EXIST;
                }

                //clean up first
                $title = Typo::cleanX($_POST['title']);
                $content = Typo::cleanX($_POST['content']);

                if (!isset($title) || $title == "") {
                    $alertred[] = TITLE_CANNOT_EMPTY;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{
                    $moddate = date("Y-m-d H:i:s");
                    $vars = array(
                                    'title' => $title,
                                    'cat' => Typo::int($_POST['cat']),
                                    'content' => $content,
                                    'modified' => $moddate,
                                    'date' => $_POST['date'],
                                    'status' => Typo::int($_POST['status'])
                                );
                    Posts::update($vars);
                    $data['alertgreen'][] = POST." {$_POST['title']} ".MSG_POST_UPDATED;
                    Hooks::run('post_submit_edit_action', $_POST);
                    Token::remove($_POST['token']);
                }

                break;
            
            default:

                break;
        }

        $data['post'] = Db::result("SELECT * FROM `posts` WHERE `id` = '{$_GET['id']}' ");
        Theme::admin('header', $data);
        System::inc('posts_form', $data);
        Theme::admin('footer');

        break;


    default:
        # code...
        if(isset($_GET['act']) && $_GET['act'] == 'del'){
            if(isset($_GET['id'])){
                if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{

                    $title = Posts::title($_GET['id']);
                    $del = Posts::delete($_GET['id']);
                    //echo $title['error'];
                    if(isset($del['error'])){
                        $data['alertred'][] = $del['error'];
                    }else{
                        $data['alertgreen'][] = POST." {$title} ".MSG_PAGE_REMOVED;
                        Hooks::run('post_delete_action', $_GET);
                    }

                }
                if(isset($_GET['token'])){ Token::remove($_GET['token']); }
            }else{
                $data['alertred'][] = MSG_USER_NO_ID_SELECTED;
            }
            
        }
        if(isset($_POST['action'])) {
            $action = $_POST['action'];
        }else{
            $action = '';
        }
        if(isset($_POST['post_id'])) { $post_id = $_POST['post_id']; } else { $post_id = ""; }
        switch ($action) {

            case 'publish':
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{
                    foreach ($post_id as $id) {
                        # code...
                        Posts::publish($id);
                    }
                }
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            case 'unpublish':
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{
                    foreach ($post_id as $id) {
                        # code...
                        Posts::unpublish($id);
                    }
                }
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            case 'delete':
                # code...
                if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                    // VALIDATE ALL
                    $alertred[] = TOKEN_NOT_EXIST;
                }
                if (isset($alertred)) {
                    # code...
                    $data['alertred'] = $alertred;
                }else{
                    foreach ($post_id as $id) {
                        # code...
                        Posts::delete($id);
                        Hooks::run('post_delete_action', $id);
                    }
                }
                if(isset($_POST['token'])){ Token::remove($_POST['token']); }
                break;
            
            default:
                # code...
                break;
        }

        // search query 
        $where = "";
        $qpage = "";
        if(isset($_GET['q']) && $_GET['q'] != ''){
            $q = Typo::cleanX($_GET['q']);
            $where .= "AND (`title` LIKE '%{$q}%' OR `content` LIKE '%{$q}%') ";
            $qpage .= "&q={$q}";
        }
        if(isset($_GET['cat']) && $_GET['cat'] != ''){
            $cat = Typo::int($_GET['cat']);
            $where .= "AND `cat` = '{$cat}' ";
            $qpage .= "&cat={$cat}";
        }
        if(isset($_GET['from']) && $_GET['from'] != ''){
            $where .= "AND `date` >= '{$_GET['from']}' ";
            $qpage .= "&from={$_GET['from']}";
        }
        if(isset($_GET['to']) && $_GET['to'] != ''){
            $where .= "AND `date` <= '{$_GET['to']}' ";
            $qpage .= "&to={$_GET['to']}";
        }
        if(isset($_GET['status']) && $_GET['status'] != ''){
            $where .= "AND `status` LIKE '%{$_GET['status']}%' ";
            $qpage .= "&status={$_GET['status']}";
        }
        

        $max = "15";
        if(isset($_GET['paging'])){
            $paging = Typo::int($_GET['paging']);
            $offset = ($paging-1)*$max;
        }else{
            $paging = 1;
            $offset = 0;
        }


        
        $data['posts'] = Db::result("SELECT * FROM `posts`
                        WHERE `type` = 'post' {$where} 
                        ORDER BY `date` DESC 
                        LIMIT {$offset},{$max}");
        $data['num'] = Db::$num_rows;
        $page = array(
                    'paging' => $paging,
                    'table' => 'posts',
                    'where' => "`type` = 'post' ".$where,
                    'max' => $max,
                    'url' => 'index.php?page=posts'.$qpage,
                    'type' => 'number'
                );
        $data['paging'] = Paging::create($page);
        Theme::admin('header', $data);
        System::inc('posts', $data);
        Theme::admin('footer');

        
        break;
}

/* End of file posts.control.php */
/* Location: ./inc/lib/Control/Backend/posts.control.php */