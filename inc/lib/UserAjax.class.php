<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class UserAjax
{
    /**
     * AJAX Endpoint for User List
     */
    public function list_users($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 10;
        $offset = isset($_GET['offset']) ? Typo::int($_GET['offset']) : 0;
        $q = Typo::cleanX($_GET['q'] ?? '');
        $group = isset($_GET['group']) && $_GET['group'] !== '' ? Typo::int($_GET['group']) : '';
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? Typo::int($_GET['status']) : '';

        $query = Query::table('user');

        if ($q != '') {
            $query->groupWhere(function($q_builder) use ($q) {
                $q_builder->where('userid', 'LIKE', "%{$q}%")
                          ->orWhere('email', 'LIKE', "%{$q}%");
            });
        }
        if ($group !== '') {
            $query->where('group', $group);
        }
        if ($status !== '') {
            $query->where('status', $status);
        }

        $countQuery = clone $query;
        $total = $countQuery->count();
        
        $users = $query->orderBy('id', 'DESC')
            ->limit($num)
            ->offset($offset)
            ->join('user_detail', 'user.userid', '=', 'user_detail.userid', 'LEFT')
            ->select(['user.*', 'user_detail.country'])
            ->get();

        $rows = [];
        if (!empty($users)) {
            foreach ($users as $u) {
                $pObj = (object) $u;
                $rows[] = User::getDashboardRow($pObj);
            }
        }

        $headers = User::getDashboardHeaders();

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
