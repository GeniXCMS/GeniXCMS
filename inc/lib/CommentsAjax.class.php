<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class CommentsAjax
{
    /**
     * AJAX Endpoint for Comments List
     */
    public function list_comments($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 10;
        $offset = isset($_GET['offset']) ? Typo::int($_GET['offset']) : 0;
        $q = Typo::cleanX($_GET['q'] ?? '');
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? Typo::int($_GET['status']) : '';
        $post_id = isset($_GET['post_id']) ? Typo::int($_GET['post_id']) : '';

        $query = Query::table('comments');

        if ($q != '') {
            $query->groupWhere(function($q_builder) use ($q) {
                $q_builder->where('comment', 'LIKE', "%{$q}%")
                          ->orWhere('name', 'LIKE', "%{$q}%")
                          ->orWhere('email', 'LIKE', "%{$q}%")
                          ->orWhere('ipaddress', 'LIKE', "%{$q}%");
            });
        }
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($post_id !== '') {
            $query->where('post_id', $post_id);
        }

        $countQuery = clone $query;
        $total = $countQuery->count();
        
        $comments = $query->orderBy('id', 'DESC')
            ->limit($num)
            ->offset($offset)
            ->get();

        $rows = [];
        if (!empty($comments)) {
            foreach ($comments as $c) {
                $pObj = (object) $c;
                $rows[] = Comments::getDashboardRow($pObj);
            }
        }

        $headers = Comments::getDashboardHeaders();

        return Ajax::response([
            'status' => 'success',
            'headers' => $headers,
            'data' => $rows,
            'total' => $total,
            'limit' => $num,
            'offset' => $offset
        ]);
    }
}
