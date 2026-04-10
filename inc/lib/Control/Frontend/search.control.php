<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class SearchControl extends BaseControl
{
    public function run($param)
    {
        $data = Router::scrap($param);
        $data['p_type'] = "search";
        $data['max'] = Options::v('post_perpage');
        $token = isset($_GET['token']) ? $_GET['token'] : "";

        if (isset($_GET['q'])) {
            $q = Typo::cleanX($_GET['q']);
            $data['sitetitle'] = "Search: " . $q;
            $sq = explode(' ', $q);
            $whereConditions = [];
            $whereBindings = [];
            foreach ($sq as $k) {
                $whereConditions[] = "(`title` LIKE ? OR `content` LIKE ?)";
                $whereBindings[] = "%{$k}%";
                $whereBindings[] = "%{$k}%";
            }
            $data['q'] = $q;
        } else {
            $data['sitetitle'] = "Search: ";
            $whereConditions = [];
            $whereBindings = [];
            $data['q'] = '';
            $q = '';
        }

        if (isset($_GET['paging'])) {
            $paging = Typo::int($_GET['paging']);
            $offset = ($paging > 1) ? ($paging - 1) * $data['max'] : 0;
        } else {
            $paging = 1;
            $offset = 0;
        }
        $qpage = "?&q={$q}&token={$token}";

        $search_types = ['post'];
        $raw_types = Hooks::filter('search_type_filter', $search_types);

        // Flatten the array in case Hooks::filter nested it
        $flattened = [];
        array_walk_recursive($raw_types, function ($a) use (&$flattened) {
            $flattened[] = $a;
        });
        $search_types = $flattened;

        $search_types_sql = "'" . implode("','", (array) $search_types) . "'";

        $url = Url::search() . $qpage;
        $where_sql = "`type` IN ({$search_types_sql}) AND `status` = '1'";
        if (!empty($sq)) {
            $sq_sql = [];
            foreach ($sq as $k) {
                $ek = Db::escape("%{$k}%");
                $sq_sql[] = "(`title` LIKE '{$ek}' OR `content` LIKE '{$ek}')";
            }
            $where_sql .= " AND " . implode(" AND ", $sq_sql);
        }

        $paging_arr = [
            'paging' => $paging,
            'table' => 'posts',
            'where' => $where_sql,
            'max' => $data['max'],
            'url' => $url,
            'type' => 'number'
        ];
        $data['paging'] = Paging::create($paging_arr);

        $q_builder = Query::table('posts')
            ->whereIn('type', (array) $search_types)
            ->where('status', '1');
        if (!empty($whereConditions)) {
            $q_builder->whereRaw(implode(' AND ', $whereConditions), $whereBindings);
        }
        $posts = $q_builder->orderBy('date', 'DESC')->limit($data['max'], $offset)->get();
        $data['posts'] = $posts;
        $data['num'] = count($posts);

        $data['recent_posts'] = Posts::lists([
            'num' => 5,
            'image' => true,
            'image_size' => 100,
            'title' => true,
            'date' => true,
            'type' => "post",
            'class' => [
                'row' => 'd-flex align-items-center mb-3 border-bottom pb-3',
                'img' => 'rounded flex-shrink-0',
                'list' => 'flex-grow-1 ms-3',
                'h4' => 'fs-5 mb-0 text-dark',
                'date' => 'text-body-secondary mt-0'
            ]
        ]);

        $this->render('search', $data);
    }
}

$control = new SearchControl();
$control->run($param);
