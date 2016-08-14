<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(2)) {
    # code...

    $data['sitetitle'] = PAGES;
    Theme::editor('full');
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }
    switch ($act) {
        case 'add':
            # code...
            $data[''] = '';

            switch (isset($_POST['submit'])) {
                case true:
                    # code...

                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                            # code...
                            $date = date('Y-m-d H:i:s');
                        } else {
                            $date = $_POST['date'];
                        }
                        $vars = array(
                                    'title' => $title,
                                    'content' => $content,
                                    'date' => $date,
                                    'type' => 'page',
                                    'author' => Session::val('username'),
                                    'status' => Typo::int($_POST['status']),
                                );
                        //print_r($vars);
                        Posts::insert($vars);
                        $post_id = Posts::$last_id;
                        if (Options::v('multilang_enable') === 'on') {
                            // insert param multilang
                            unset($_POST['title'][$def]);
                            foreach ($_POST['title'] as $key => $value) {
                                $multilang[] = array(
                                                $key => array(
                                                        'title' => $_POST['title'][$key],
                                                        'content' => Typo::cleanX($_POST['content'][$key]),
                                                    ),
                                            );
                            }
                            $multilang = json_encode($multilang);
                            Posts::addParam('multilang', $multilang, $post_id);
                            // print_r($multilang);
                        }
                        $data['alertSuccess'][] = PAGE." {$title} ".MSG_PAGE_ADDED;
                        Hooks::run('post_submit_add_action', $_POST);
                        Token::remove($_POST['token']);
                    }

                    break;

                default:
                    # code...
                    //System::inc('pages_form', $data);
                    break;
            }
            Theme::admin('header', $data);
            System::inc('pages_form', $data);
            Theme::admin('footer');
            break;

        case 'edit':
            //echo "edit";
            switch (isset($_POST['submit'])) {
                case true:
                    # code...

                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
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
                            # code...
                            $date = date('Y-m-d H:i:s');
                        } else {
                            $date = $_POST['date'];
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
                                                        'title' => $_POST['title'][$key],
                                                        'content' => Typo::cleanX($_POST['content'][$key]),
                                                    ),
                                            );
                            }
                            $multilang = json_encode($multilang);
                            if (Posts::existParam('multilang', $_GET['id'])) {
                                Posts::editParam('multilang', $multilang, $_GET['id']);
                            } else {
                                Posts::addParam('multilang', $multilang, $_GET['id']);
                            }

                            // print_r($multilang);
                        }
                        $data['alertSuccess'][] = PAGE."  {$title} ".MSG_PAGE_UPDATED;
                        Token::remove($_POST['token']);
                    }

                    break;

                default:
                    # code...
                    //System::inc('posts_form', $data);
                    break;
            }
            $id = Typo::int($_GET['id']);
            $data['post'] = Db::result("SELECT * FROM `posts` AS A 
            LEFT JOIN `posts_param` AS B
            ON A.`id` = B.`post_id` 
            WHERE A.`id` = '{$id}' ");
            Theme::admin('header', $data);
            System::inc('pages_form', $data);
            Theme::admin('footer');

            break;

        default:
            # code...
            if (isset($_GET['act']) && $_GET['act'] == 'del') {
                if (isset($_GET['id'])) {
                    $title = Posts::title(Typo::int($_GET['id']));
                    if (!isset($_GET['token']) || !Token::isExist($_GET['token'])) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $del = Posts::delete($_GET['id']);
                    }
                    //echo $title['error'];
                    if (isset($del['error'])) {
                        $data['alertDanger'][] = $del['error'];
                    } else {
                        $data['alertSuccess'][] = PAGE." {$title} ".MSG_PAGE_REMOVED;
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($_GET['token']);
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
                $post_id = Typo::int($_POST['post_id']);
            } else {
                $post_id = '';
            }
            switch ($action) {
                case 'publish':
                    # code...
                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        foreach ($post_id as $id) {
                            # code...
                            Posts::publish($id);
                        }
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($_POST['token']);
                    }
                    break;
                case 'unpublish':
                    # code...
                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        foreach ($post_id as $id) {
                            # code...
                            Posts::unpublish($id);
                        }
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($_POST['token']);
                    }
                    break;
                case 'delete':
                    # code...
                    if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        foreach ($post_id as $id) {
                            # code...
                            Posts::delete($id);
                        }
                    }
                    if (isset($_POST['token'])) {
                        Token::remove($_POST['token']);
                    }
                    break;

                default:
                    # code...
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
                $where .= "AND `date` >= '{$_GET['from']}' ";
                $qpage .= "&from={$_GET['from']}";
            }
            if (isset($_GET['to']) && $_GET['to'] != '') {
                $where .= "AND `date` <= '{$_GET['to']}' ";
                $qpage .= "&to={$_GET['to']}";
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

                $data['posts'] = Db::result("SELECT * FROM `posts` 
            WHERE `type` = 'page' {$where} 
            ORDER BY `date` DESC 
            LIMIT {$offset},{$max}");
                $data['num'] = Db::$num_rows;

                $page = array(
                    'paging' => $paging,
                    'table' => 'posts',
                    'where' => "`type` = 'page'".$where,
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
