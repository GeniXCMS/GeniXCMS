<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.0.0 build date 20160830
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
    $data['sitetitle'] = COMMENTS;




    if (isset($_POST['action'])) {
        $action = Typo::cleanX($_POST['action']);
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
                        Comments::publish($id);
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
                        Comments::unpublish($id);
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
                        Comments::delete($id);
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
                $del = Comments::delete($id);
                //echo $title['error'];
                if (isset($del['error'])) {
                    $data['alertDanger'][] = $del['error'];
                } else {
                    $data['alertSuccess'][] = COMMENTS.'  '.MSG_PAGE_REMOVED;
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

    if (isset($_GET['act'])) {
        $act = $_GET['act'];
    } else {
        $act = '';
    }

    // search query
    $where = '';
    $qpage = '';
    if (isset($_GET['q']) && $_GET['q'] != '') {
        $q = Typo::cleanX($_GET['q']);
        $where .= "AND (`comment` LIKE '%{$q}%' OR `email` LIKE '%{$q}%') ";
        $qpage .= "&q={$_GET['q']}";
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
        $status = Typo::cleanX($_GET['status']);
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

    $data['posts'] = Db::result(
        sprintf("SELECT * FROM `comments`
                    WHERE `type` = 'post' %s
                    ORDER BY `date` DESC
                    LIMIT %d, %d", $where, $offset, $max)
    );
    $data['num'] = Db::$num_rows;
    $page = array(
                'paging' => $paging,
                'table' => 'comments',
                'where' => "`type` = 'post' ".$where,
                'max' => $max,
                'url' => 'index.php?page=comments'.$qpage,
                'type' => 'number',
            );
    $data['paging'] = Paging::create($page);

    Theme::admin('header', $data);
    System::inc('comments', $data);
    Theme::admin('footer');
} else {
    Theme::admin('header');
    Control::error('noaccess');
    Theme::admin('footer');
}
/* End of file posts.control.php */
/* Location: ./inc/lib/Control/Backend/posts.control.php */
