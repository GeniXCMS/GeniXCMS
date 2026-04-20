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

class PostsAjax
{
    /**
     * AJAX Endpoint for Post List (Desktop-based render)
     * Used by the main Posts Dashboard for all post types.
     */
    public function list_posts($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $type = Typo::cleanX($_GET['type'] ?? 'post');
        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 10;
        $offset = isset($_GET['offset']) ? Typo::int($_GET['offset']) : 0;
        $q = Typo::cleanX($_GET['q'] ?? '');
        $cat = Typo::int($_GET['cat'] ?? '');
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? Typo::int($_GET['status']) : '';
        $from = Typo::cleanX($_GET['from'] ?? '');
        $to = Typo::cleanX($_GET['to'] ?? '');

        $query = Query::table('posts')->where('type', $type);

        if ($q != '') {
            $query->groupWhere(function($q_builder) use ($q) {
                $q_builder->where('title', 'LIKE', "%{$q}%")
                          ->orWhere('content', 'LIKE', "%{$q}%");
            });
        }
        if ($cat != '' && $cat != '0') {
            $query->where('cat', $cat);
        }
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($from != '') {
            $query->where('date', '>=', "{$from} 00:00:00");
        }
        if ($to != '') {
            $query->where('date', '<=', "{$to} 23:59:59");
        }

        $countQuery = clone $query;
        $total = $countQuery->count();
        
        $posts = $query->orderBy('id', 'DESC')
            ->limit($num)
            ->offset($offset)
            ->get();

        $rows = [];
        if (!empty($posts)) {
            $username = Session::val('username');
            $group = Session::val('group');

            foreach ($posts as $p) {
                $pObj = (object) $p;
                
                // Use the centralized row generator to maintain hook compatibility
                $row = Posts::getDashboardRow($pObj, $group, $username, $type);
                $rows[] = $row;
            }
        }

        // Get headers with hooks
        $headers = Posts::getDashboardHeaders($type);

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
