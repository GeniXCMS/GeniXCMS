<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141006
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (User::access(3)) {
    $postType = Typo::cleanX($_GET['type'] ?? 'post');
    $data['postType'] = $postType;
    $data['sitetitle'] = _("Posts") . ($postType !== 'post' ? ' - ' . ucfirst(str_replace('_', ' ', $postType)) : '');
    Theme::editor('full');
    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }
    $username = Session::val('username');
    $group = Session::val('group');
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
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
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
                        $alertDanger[] = _("Title cannot be empty.");
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
                            'modified' => $date,
                            'type' => $postType,
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
                        Tags::add($tags, $postType);

                        $post_image = Typo::cleanX($_POST['post_image']);
                        Posts::addParam('post_image', $post_image, $post_id);

                        if (isset($_POST['param'])) {
                            foreach ($_POST['param'] as $k => $v) {
                                Posts::addParam($k, $v, $post_id);
                            }
                        }

                        $data['alertSuccess'][] = _("Post") . " {$title} " . _("Added Successfully");
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
            $author = Posts::author($id);
            $accessEdit = $group <= 2 ? 1 : ($author == $username ? 1 : 0);
            if (!$accessEdit)
                header("Location: index.php?page=posts");

            switch (isset($_POST['submit'])) {
                case true:
                    // check token first
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)) {
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    }
                    if (!$accessEdit) {
                        $alertDanger[] = _("You don't have access to edit this post.");
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
                        $alertDanger[] = _("Title cannot be empty.");
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
                                if (
                                    !empty($_POST['content'][$key]) ||
                                    $_POST['content'][$key] != ''
                                ) {
                                    if (
                                        $_POST['content'][$key] != '<p><br></p>' ||
                                        $_POST['content'][$key] != '<br>'
                                    ) {
                                        $content = $_POST['content'][$key];
                                    }
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
                        Tags::add($tags, $postType);

                        $post_image = Typo::cleanX($_POST['post_image']);
                        if (!Posts::existParam('post_image', $id)) {
                            Posts::addParam('post_image', $post_image, $id);
                        } else {
                            Posts::editParam('post_image', $post_image, $id);
                        }


                        if (isset($_POST['param'])) {
                            foreach ($_POST['param'] as $k => $v) {
                                if (!Posts::existParam($k, $id)) {
                                    Posts::addParam($k, $v, $id);
                                } else {
                                    Posts::editParam($k, $v, $id);
                                }
                            }
                        }

                        $data['alertSuccess'][] = _("Post") . " {$title} " . _("Updated Successfully");
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
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    }
                    $accessDelete = $group < 2 ? 1 : 0;
                    if (!$accessDelete) {
                        $alertDanger[] = _("You don't have permission to delete the post.");
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        $title = Posts::title($id);
                        $del = Posts::delete($id);
                        if ($del !== true) {
                            $data['alertDanger'][] = (is_string($del)) ? $del : _("Failed to remove post");
                        } else {
                            $data['alertSuccess'][] = _("Post") . " {$title} " . _("Removed Successfully");
                            Hooks::run('post_delete_action', $_GET);
                        }
                    }
                    if (isset($_GET['token'])) {
                        Token::remove($token);
                    }
                } else {
                    $data['alertDanger'][] = _("No ID Selected");
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
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if ($post_id != '') {
                            foreach ($post_id as $id) {
                                $title = Posts::title($id);
                                $id = Typo::int($id);
                                Posts::publish($id);
                                $data['alertSuccess'][] = _("Post {$title} Published");
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
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if ($post_id != '') {
                            foreach ($post_id as $id) {
                                $title = Posts::title($id);
                                $id = Typo::int($id);
                                Posts::unpublish($id);
                                $data['alertSuccess'][] = _("Post {$title} Unpublished");
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
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    }
                    if (isset($alertDanger)) {
                        $data['alertDanger'] = $alertDanger;
                    } else {
                        if ($post_id != '') {
                            foreach ($post_id as $id) {
                                $title = Posts::title($id);
                                $id = Typo::int($id);
                                Posts::delete($id);
                                Hooks::run('post_delete_action', $id);
                                $data['alertSuccess'][] = _("Post {$title} Deleted");
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

            // search query - parameterized conditions
            $whereRaws = [];
            $whereBindings = [];
            $qpage = '';
            if (isset($_GET['q']) && $_GET['q'] != '') {
                $q = Typo::cleanX($_GET['q']);
                $whereRaws[] = "(`title` LIKE ? OR `content` LIKE ?)";
                $whereBindings[] = "%{$q}%";
                $whereBindings[] = "%{$q}%";
                $qpage .= "&q={$_GET['q']}";
            }
            if (isset($_GET['cat']) && $_GET['cat'] != '') {
                $cat = Typo::int($_GET['cat']);
                $whereRaws[] = "`cat` = ?";
                $whereBindings[] = $cat;
                $qpage .= "&cat={$cat}";
            }
            if (isset($_GET['from']) && $_GET['from'] != '') {
                $from = Typo::cleanX($_GET['from']);
                $whereRaws[] = "`date` >= ?";
                $whereBindings[] = $from;
                $qpage .= "&from={$from}";
            }
            if (isset($_GET['to']) && $_GET['to'] != '') {
                $to = Typo::cleanX($_GET['to']);
                $whereRaws[] = "`date` <= ?";
                $whereBindings[] = $to;
                $qpage .= "&to={$to}";
            }
            if (isset($_GET['status']) && $_GET['status'] != '') {
                $status = Typo::int($_GET['status']);
                $whereRaws[] = "`status` = ?";
                $whereBindings[] = $status;
                $qpage .= "&status={$status}";
            }

            $max = 15;
            if (isset($_GET['paging'])) {
                $paging = Typo::int($_GET['paging']);
                $offset = ($paging - 1) * $max;
            } else {
                $paging = 1;
                $offset = 0;
            }

            $q_builder = Query::table('posts')->where('type', $postType);
            if (!empty($whereRaws)) {
                $q_builder->whereRaw(implode(' AND ', $whereRaws), $whereBindings);
            }

            $countQuery = clone $q_builder;
            $totalCount = $countQuery->count();

            $data['posts'] = $q_builder->orderBy('date', 'DESC')->limit($max, $offset)->get();
            $data['num'] = count($data['posts']);
            $page = array(
                'paging' => $paging,
                'table' => 'posts',
                'total' => $totalCount,
                'max' => $max,
                'url' => 'index.php?page=posts' . $qpage,
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
