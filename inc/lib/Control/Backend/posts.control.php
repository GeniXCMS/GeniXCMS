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
    $data['sitetitle'] = POSTS;
    Theme::editor('full');
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }
    switch ($act) {
        case 'add':
            $data[] = '';
            switch (isset($_POST['submit'])) {
                case true:
                    // print_r($_POST);
                    $token = Typo::cleanX($_POST['token']);
                    // check token first
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

                    if (!isset($title) || $title == '') {
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
                                        'cat' => Typo::int($_POST['cat']),
                                        'content' => $content,
                                        'date' => $date,
                                        'type' => 'post',
                                        'author' => Session::val('username'),
                                        'status' => Typo::int($_POST['status']),
                                    );
                        // print_r($vars);
                        Posts::insert($vars);
                        $post_id = Posts::$last_id;
                        if (Options::v('multilang_enable') === 'on') {
                            // insert param multilang
                            unset($_POST['title'][$def]);
                            foreach ($_POST['title'] as $key => $value) {
                                $title = !empty($_POST['title'][$key]) ? Typo::cleanX($_POST['title'][$key]) : $title;
                                $title = Hooks::filter('post_submit_title_filter', $title);

                                $content = !empty($_POST['content'][$key]) ? Typo::cleanX($_POST['content'][$key]) : $content;
                                $content = Hooks::filter('post_submit_content_filter', $content);

                                $multilang[] = array(
                                                    $key => array(
                                                            'title' => $title,
                                                            'content' => Typo::jsonFormat($content),
                                                        ),
                                                );
                            }
                            $multilang = json_encode($multilang, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                            Posts::addParam('multilang', $multilang, $post_id);

                            // print_r($multilang);
                        }
                        $tags = Typo::cleanX($_POST['tags']);
                        Posts::addParam('tags', $tags, $post_id);
                        Tags::add($tags);
                        if (isset($_POST['param'])){
                            foreach ($_POST['param'] as $k => $v) {
                                Posts::addParam($k, $v, $post_id);
                            }
                        }


                        $data['alertSuccess'][] = POST." {$title} ".MSG_POST_ADDED;
                        Hooks::run('post_submit_add_action', $_POST);
                        // Token::remove($_POST['token']);
                    }

                    break;

                default:
                    break;
            }
            Theme::admin('header', $data);
            System::inc('posts_form', $data);
            Theme::admin('footer');
            break;

        case 'edit':
            $id = Typo::int($_GET['id']);

            switch (isset($_POST['submit'])) {
                case true:
                    // check token first
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
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

                    if (!isset($title) || $title == '') {
                        $alertDanger[] = TITLE_CANNOT_EMPTY;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $moddate = date('Y-m-d H:i:s');
                        $vars = array(
                                        'title' => $title,
                                        'cat' => Typo::int($_POST['cat']),
                                        'content' => $content,
                                        'modified' => $moddate,
                                        'date' => $_POST['date'],
                                        'status' => Typo::int($_POST['status']),
                                    );
                        Posts::update($vars);

                        if (Options::v('multilang_enable') === 'on') {
                            // insert param multilang
                            unset($_POST['title'][$def]);
                            $multilang = array();
                            foreach ($_POST['title'] as $key => $value) {
                                $title = !empty($_POST['title'][$key]) ? Typo::cleanX($_POST['title'][$key]) : $title;
                                $title = Hooks::filter('post_submit_title_filter', $title);
                                // $_POST['content'][$key] = str_replace("<br>","",$_POST['content'][$key]);
                                if (!empty($_POST['content'][$key]) ||
                                    $_POST['content'][$key] != ''
                                ) {
                                    if ($_POST['content'][$key] == '<p><br></p>' ||
                                        $_POST['content'][$key] == '<br>'
                                    ) {
                                        $content = $content;
                                    } else {
                                        $content = $_POST['content'][$key];
                                    }
                                } else {
                                    $content = $content;
                                }
                                $content = Hooks::filter('post_submit_content_filter', $content);

                                $multilang[] = array(
                                                    $key => array(
                                                            'title' => $title,
                                                            'content' => Typo::jsonFormat($content),
                                                        ),
                                                );
                            }
                            $multilang = json_encode($multilang, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                            if (!Posts::existParam('multilang', $id)) {
                                Posts::addParam('multilang', $multilang, $id);
                            } else {
                                Posts::editParam('multilang', $multilang, $id);
                            }

                            // print_r($multilang);
                        }

                        $tags = Typo::cleanX($_POST['tags']);
                        if (!Posts::existParam('tags', $id)) {
                            Posts::addParam('tags', $tags, $id);
                        } else {
                            Posts::editParam('tags', $tags, $id);
                        }
                        Tags::add($tags);

                        if (isset($_POST['param'])){
                            foreach ($_POST['param'] as $k => $v) {
                                if (!Posts::existParam($k, $id)) {
                                    Posts::addParam($k, $v, $id);
                                } else {
                                    Posts::editParam($k, $v, $id);
                                }
                            }
                        }

                        $data['alertSuccess'][] = POST." {$title} ".MSG_POST_UPDATED;
                        Hooks::run('post_submit_edit_action', $_POST);
                        // Token::remove($token);

                    }

                    break;

                default:
                    break;
            }

            $vars = array(
                'id' => $id
            );
            $data['post'] = Posts::fetch($vars); //Db::result("SELECT * FROM `posts` WHERE `id` = '{$_GET['id']}' ");

            Theme::admin('header', $data);
            System::inc('posts_form', $data);
            Theme::admin('footer');

            break;

        default:
            if (isset($_GET['act']) && $_GET['act'] == 'del' && !isset($_POST['action'])) {
                if (isset($_GET['id'])) {
                    $id = Typo::int($_GET['id']);
                    $token = Typo::cleanX($_GET['token']);
                    if (!isset($_GET['token']) || !Token::validate($token)) {
                        // VALIDATE ALL
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $title = Posts::title($id);
                        $del = Posts::delete($id);
                        //echo $title['error'];
                        if (isset($del['error'])) {
                            $data['alertDanger'][] = $del['error'];
                        } else {
                            $data['alertSuccess'][] = POST." {$title} ".MSG_PAGE_REMOVED;
                            Hooks::run('post_delete_action', $_GET);
                        }
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($token);
                    }
                } else {
                    $data['alertDanger'][] = MSG_USER_NO_ID_SELECTED;
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
                        Token::remove($_POST['token']);
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
                $where .= "AND `status` LIKE '%{$status}%' ";
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
                        'type' => 'number',
                    );
            $data['paging'] = Paging::create($page);

            Theme::admin('header', $data);
            System::inc('posts', $data);
            Theme::admin('footer');

            break;
    }
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file posts.control.php */
/* Location: ./inc/lib/Control/Backend/posts.control.php */
