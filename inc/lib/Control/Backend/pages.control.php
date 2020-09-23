<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
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

if (User::access(2)) {
    $data['sitetitle'] = PAGES;
    Theme::editor('full');
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }
    switch ($act) {
        case 'add':
            $data[''] = '';
            switch (isset($_POST['submit'])) {
                case true:
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    //clean up first
                    if (Options::v('multilang_enable') === 'on') {
                        $def = Options::v('multilang_default');
                        //cleanup first
                        $title = Typo::cleanX($_POST['title'][$def]);
                        $title = Hooks::filter('post_submit_title_filter', $title);

                        $content = Typo::cleanX($_POST['content'][$def]);
                        $content = Hooks::filter('post_submit_content_filter', $content);
                    } else {
                        //cleanup first
                        $title = Typo::cleanX($_POST['title']);
                        $title = Hooks::filter('post_submit_title_filter', $title);

                        $content = Typo::cleanX($_POST['content']);
                        $content = Hooks::filter('post_submit_content_filter', $content);
                    }
                    if (!isset($_POST['title']) || $_POST['title'] == '') {
                        $alertDanger[] = TITLE_CANNOT_EMPTY;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if (!isset($_POST['date']) || $_POST['date'] == '') {
                            $date = date('Y-m-d H:i:s');
                        } else {
                            $date = Typo::cleanX($_POST['date']);
                        }
                        $vars = array(
                                    'title' => $title,
                                    'content' => $content,
                                    'date' => $date,
                                    'type' => 'page',
                                    'author' => Session::val('username'),
                                    'status' => Typo::int($_POST['status']),
                                );

                        Posts::insert($vars);
                        $post_id = Posts::$last_id;
                        if (Options::v('multilang_enable') === 'on') {
                            // insert param multilang
                            unset($_POST['title'][$def]);
                            foreach ($_POST['title'] as $key => $value) {
                                $multilang[] = array(
                                                $key => array(
                                                        'title' => Typo::cleanX($_POST['title'][$key]),
                                                        'content' => Typo::cleanX($_POST['content'][$key]),
                                                    ),
                                            );
                            }
                            $multilang = json_encode($multilang);
                            Posts::addParam('multilang', $multilang, $post_id);
                            // print_r($multilang);
                        }

                        if (isset($_POST['param'])){
                            foreach ($_POST['param'] as $k => $v) {
                                Posts::addParam($k, $v, $post_id);
                            }
                        }

                        $data['alertSuccess'][] = PAGE." {$title} ".MSG_PAGE_ADDED;
                        Hooks::run('post_submit_add_action', $_POST);
                        isset($_POST['token']) ? Token::remove($token): '';
                    }

                    break;

                default:
                    break;
            }
            Theme::admin('header', $data);
            System::inc('pages_form', $data);
            Theme::admin('footer');
            break;

        case 'edit':
            //echo "edit";
            $id = isset($_GET['id']) ? Typo::int($_GET['id']): '';
            switch (isset($_POST['submit'])) {
                case true:
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (Options::v('multilang_enable') === 'on') {
                        $def = Options::v('multilang_default');
                        //cleanup first
                        $title = Typo::cleanX($_POST['title'][$def]);
                        $title = Hooks::filter('post_submit_title_filter', $title);

                        $content = Typo::cleanX($_POST['content'][$def]);
                        $content = Hooks::filter('post_submit_content_filter', $content);
                    } else {
                        //cleanup first
                        $title = Typo::cleanX($_POST['title']);
                        $title = Hooks::filter('post_submit_title_filter', $title);

                        $content = Typo::cleanX($_POST['content']);
                        $content = Hooks::filter('post_submit_content_filter', $content);
                    }
                    if (!isset($_POST['title']) || $_POST['title'] == '') {
                        $alertDanger[] = TITLE_CANNOT_EMPTY;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if (!isset($_POST['date']) || $_POST['date'] == '') {
                            $date = date('Y-m-d H:i:s');
                        } else {
                            $date = Typo::cleanX($_POST['date']);
                        }
                        $moddate = date('Y-m-d H:i:s');
                        $vars = array(
                                    'title' => $title,
                                    'content' => $content,
                                    'modified' => $moddate,
                                    'date' => $date,
                                    'status' => Typo::int($_POST['status']),
                                );
                        //print_r($vars);
                        Posts::update($vars);
                        if (Options::v('multilang_enable') === 'on') {
                            // insert param multilang
                            unset($_POST['title'][$def]);
                            foreach ($_POST['title'] as $key => $value) {
                                $multilang[] = array(
                                                $key => array(
                                                        'title' => Typo::cleanX($_POST['title'][$key]),
                                                        'content' => Typo::cleanX($_POST['content'][$key]),
                                                    ),
                                            );
                            }
                            $multilang = json_encode($multilang);
                            if (Posts::existParam('multilang', $id)) {
                                Posts::editParam('multilang', $multilang, $id);
                            } else {
                                Posts::addParam('multilang', $multilang, $id);
                            }

                            // print_r($multilang);
                        }

                        if (isset($_POST['param'])){
                            foreach ($_POST['param'] as $k => $v) {
                                if (!Posts::existParam($k, $id)) {
                                    Posts::addParam($k, $v, $id);
                                } else {
                                    Posts::editParam($k, $v, $id);
                                }
                            }
                        }

                        $data['alertSuccess'][] = PAGE."  {$title} ".MSG_PAGE_UPDATED;
                        Token::remove($token);
                    }

                    break;

                default:
                    //System::inc('posts_form', $data);
                    break;
            }

            $data['post'] = Db::result("SELECT * FROM `posts` AS A 
                                        LEFT JOIN `posts_param` AS B
                                        ON A.`id` = B.`post_id` 
                                        WHERE A.`id` = '{$id}' ");
            Theme::admin('header', $data);
            System::inc('pages_form', $data);
            Theme::admin('footer');

            break;

        default:
            if (isset($_GET['act']) && $_GET['act'] == 'del' && !isset($_POST['action'])) {

                if (isset($_GET['id'])) {
                    $id = Typo::int($_GET['id']);
                    $title = Posts::title($id);
                    $token = Typo::cleanX($_GET['token']);
                    if (!isset($_GET['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $del = Posts::delete($id);
                    }
                    //echo $title['error'];
                    if (isset($del['error'])) {
                        $data['alertDanger'][] = $del['error'];
                    } else {
                        $data['alertSuccess'][] = PAGE." {$title} ".MSG_PAGE_REMOVED;
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($token);
                    }
                } else {
                    $data['alertDanger'][] = 'No ID Selected';
                }
            }
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
            } else {
                $action = '';
            }
            if (isset($_POST['post_id'])) {
                $post_id = $_POST['post_id'];
            } else {
                $post_id = '';
            }
            switch ($action) {
                case 'publish':
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if ($post_id != '') {
                            foreach ($post_id as $id) {
                                $id = Typo::int($id);
                                Posts::publish($id);
                            }
                        }
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($token);
                    }
                    break;
                case 'unpublish':
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if ($post_id != '') {
                            foreach ($post_id as $id) {
                                $id = Typo::int($id);
                                Posts::unpublish($id);
                            }
                        }
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($token);
                    }
                    break;
                case 'delete':
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if ($post_id != '') {
                            foreach ($post_id as $id) {
                                $id = Typo::int($id);
                                Posts::delete($id);
                                Hooks::run('post_delete_action', $id);
                            }
                        }
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($token);
                    }
                    break;

                default:
                    break;
            }

                // search query
                $where = '';
                $qpage = '';
            if (isset($_GET['q']) && $_GET['q'] != '') {
                $q = Typo::cleanX($_GET['q']);
                $where .= "AND (`title` LIKE '%{$q}%' OR `content` LIKE '%{$q}%') ";
                $qpage .= "&q={$_GET['q']}";
            }
            if (isset($_GET['cat']) && $_GET['cat'] != '') {
                $cat = Typo::int($_GET['cat']);
                $where .= "AND `cat` = '{$cat}' ";
                $qpage .= "&cat={$cat}";
            }
            if (isset($_GET['from']) && $_GET['from'] != '') {
                $from = Typo::cleanX($_GET['from']);
                $where .= "AND `date` >= '{$from}' ";
                $qpage .= "&from={$from}";
            }
            if (isset($_GET['to']) && $_GET['to'] != '') {
                $to = Typo::cleanX($_GET['to']);
                $where .= "AND `date` <= '{$to}' ";
                $qpage .= "&to={$to}";
            }
            if (isset($_GET['status']) && $_GET['status'] != '') {
                $status = Typo::int($_GET['status']);
                $where .= "AND `status` LIKE '%%{$status}%%' ";
                $qpage .= "&status={$status}";
            }

                $max = '15';
            if (isset($_GET['paging'])) {
                $paging = Typo::int($_GET['paging']);
                $offset = ($paging - 1) * $max;
            } else {
                $paging = 1;
                $offset = 0;
            }

            $data['posts'] = Db::result(
                sprintf("SELECT * FROM `posts` 
                                        WHERE `type` = 'page' %s
                                        ORDER BY `date` DESC 
                                        LIMIT %d,%d", $where, $offset, $max)
            );
            $data['num'] = Db::$num_rows;

            $page = array(
                'paging' => $paging,
                'table' => 'posts',
                'where' => "`type` = 'page' ".$where,
                'max' => $max,
                'url' => 'index.php?page=pages'.$qpage,
                'type' => 'pager',
            );
            $data['paging'] = Paging::create($page);

            Theme::admin('header', $data);
            System::inc('pages', $data);
            Theme::admin('footer');

            break;
    }
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}

/* End of file pages.control.php */
/* Location: ./inc/lib/Control/Backend/pages.control.php */
