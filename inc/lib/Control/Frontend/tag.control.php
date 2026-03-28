<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

class TagControl extends BaseControl
{
    public function run($param)
    {
        $data = Router::scrap($param);
        $data['p_type'] = 'tag';
        $data['max'] = Options::v('post_perpage');

        $tag_input = (SMART_URL) ? $data['tag'] : Typo::strip($_GET['tag']);
        $tag_id = Tags::id(Typo::cleanX($tag_input));
        
        $type = Categories::type($tag_id);
        $name = Tags::name($tag_id);
        $data['name'] = $name;

        if (Tags::exist($name)) {
            if (SMART_URL) {
                $paging = isset($data['paging']) ? $data['paging'] : 1;
            } else {
                $paging = isset($_GET['paging']) ? Typo::int($_GET['paging']) : 1;
            }

            $offset = ($paging > 1) ? ($paging - 1) * $data['max'] : 0;
            $pagingtitle = ($paging > 1) ? " - Page {$paging}" : '';

            $data['sitetitle'] = 'Post in : Tags - ' . $name . $pagingtitle;
            $data['title'] = "Tag: " . $name;
            $posts = Query::table('posts')
                ->select('`posts`.*,`posts`.`id` as `id`')
                ->join('posts_param AS B', '`posts`.`id`', '=', 'B.`post_id`')
                ->whereRaw("B.`param` = 'tags' AND B.`value` LIKE ? AND `posts`.`status` = '1'", ["%{$name}%"])
                ->orderBy('`posts`.`date`', 'DESC')
                ->limit($data['max'], $offset)
                ->get();
            $data['num'] = count($posts);
            $data['posts'] = Posts::prepare($posts);

            $url = Url::tag($name);
            $paging_arr = [
                'paging' => $paging,
                'table' => 'posts',
                'where' => "`type` = 'post' AND `status` = '1'", // Simplified where for tags
                'max' => $data['max'],
                'url' => $url,
                'type' => Options::v('pagination'),
            ];
            $data['paging'] = Paging::create($paging_arr, SMART_URL);

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

            $this->render('tag', $data);
            exit;
        } else {
            Control::error('404');
        }
    }
}

$control = new TagControl();
$control->run($param);
