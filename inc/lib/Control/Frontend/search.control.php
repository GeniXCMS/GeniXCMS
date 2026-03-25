<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

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
            $where = '';
            $pwhere = '';
            foreach ($sq as $k) {
                $where .= "AND (`title` LIKE '%{$k}%' OR `content` LIKE '%{$k}%') ";
                $pwhere .= "AND (`title` LIKE '%{$k}%' OR `content` LIKE '%{$k}%') ";
            }
            $data['q'] = $q;
        } else {
            $data['sitetitle'] = "Search: ";
            $where = '';
            $pwhere = '';
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

        $url = Url::search() . $qpage;
        $paging_arr = [
            'paging' => $paging,
            'table' => 'posts',
            'where' => "`type` = 'post' AND `status` = '1' {$pwhere}",
            'max' => $data['max'],
            'url' => $url,
            'type' => 'number'
        ];
        $data['paging'] = Paging::create($paging_arr);

        $data['posts'] = Db::result(
            sprintf(
                "SELECT * FROM `posts`
                    WHERE `type` = 'post'
                    %s
                    AND `status` = '1'
                    ORDER BY `date` 
                    DESC LIMIT %d, %d",
                $where,
                $offset,
                $data['max']
            )
        );
        $data['num'] = Db::$num_rows;

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