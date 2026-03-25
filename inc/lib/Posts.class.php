<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140930
 *
 * @version 2.0.0-alpha
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Posts
{
    public static $last_id = '';

    public function __construct()
    {
    }

    //
    // $vars = array(
    //             'title' => '',
    //             'cat' => '',
    //             'content' => '',
    //             'date' => '',
    //             'author' => '',
    //             'type' => '',
    //             'status' => ''
    //         );
    public static function insert($vars)
    {
        if (is_array($vars)) {
            $slug = self::createSlug($vars['title']);
            $vars = array_merge($vars, array('slug' => $slug));
            //print_r($vars);
            $ins = array(
                        'table' => 'posts',
                        'key' => $vars,
                    );
            $post = Db::insert($ins);
            self::$last_id = Db::$last_id;
            Hooks::run('post_sqladd_action', $vars, self::$last_id);

            if (Pinger::isOn()) {
                $pinger = Options::v('pinger');
                Pinger::run($pinger);
            }
        } else {
            $post = false;
        }

        return $post;
    }

    public static function update($vars)
    {
        if (is_array($vars)) {
            //$slug = Typo::slugify($vars['title']);
            //$vars = array_merge($vars, array('slug' => $slug));
            //print_r($vars);
            $id = Typo::int($_GET['id']);
            $ins = array(
                        'table' => 'posts',
                        'id' => $id,
                        'key' => $vars,
                    );
            $post = Db::update($ins);
            Hooks::run('post_sqladd_action', $vars, $id);

            if (Pinger::isOn()) {
                $pinger = Options::v('pinger');
                Pinger::run($pinger);
            }
        } else {
            $post = false;
        }

        return $post;
    }

    public static function publish($id)
    {
        $id = Typo::int($id);
        $ins = array(
                    'table' => 'posts',
                    'id' => $id,
                    'key' => array(
                                'status' => '1',
                            ),
                );
        $post = Db::update($ins);

        return $post;
    }

    public static function unpublish($id)
    {
        $id = Typo::int($id);
        $ins = array(
                    'table' => 'posts',
                    'id' => $id,
                    'key' => array(
                                'status' => '0',
                            ),
                );
        $post = Db::update($ins);

        return $post;
    }

    public static function delete($id)
    {
        $id = Typo::int($id);
        try {
            $vars1 = array(
                        'table' => 'posts',
                        'where' => array(
                                    'id' => $id,
                                    ),
                        );
            $d = Db::delete($vars1);

            $vars2 = array(
                        'table' => 'posts_param',
                        'where' => array(
                                    'post_id' => $id,
                                    ),
                        );
            $d = Db::delete($vars2);
            Hooks::run('post_sqldel_action', $id);
            if (Comments::postExist($id)) {
                Comments::deleteWithPost($id);
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function content($vars)
    {
        $post = Typo::Xclean($vars);

        preg_match_all("[[\-\-readmore\-\-]]", $post, $more);

        if (is_array($more[0])) {
            $post = str_replace('[[--readmore--]]', '', $post);
            // return $post;
        }

        $post = Hooks::filter('post_content_filter', $post);

        return $post;
    }

    /**
     * @param $post
     * @param $id
     *
     * @return array|mixed|string
     */
    public static function format($post, $id)
    {
        // split post for readmore...
        $post = Typo::Xclean($post);
        $more = explode('[[--readmore--]]', $post);
        //print_r($more);
        if (count($more) > 1) {
            // $post = explode('[[--readmore--]]', $post);
            $post = $more[0].' <a href="'.Url::post($id).'">'._("Read More").'</a>';
        }

        $post = Hooks::filter('post_content_filter', $post);

        return $post;
    }

    // $vars = array(
    //     'num' => '',
    //     'cat' => '',
    //     'type' => 'post'
    // );

    public static function recent($vars)
    {
        $catW = isset($vars['cat']) ? " AND `cat` = '".Typo::int($vars['cat'])."'" : '';
        $type = isset($vars['type']) ? Typo::cleanX($vars['type']) : 'post';
        $num = isset($vars['num']) ? Typo::int($vars['num']) : '10';
        $sql = "SELECT * FROM `posts`
                WHERE `type` = '{$type}' {$catW} AND `status` = '1'
                ORDER BY `date` DESC LIMIT {$num}";
        $posts = Db::result($sql);

        if (isset($posts['error'])) {
            $posts['error'] = _('Error: No Posts found.');
        } else {
            $posts = self::prepare($posts);
        }

        return $posts;
    }

    public static function title($id)
    {
        $sql = sprintf("SELECT `title` FROM `posts` WHERE `id` = '%d'", $id);
        try {
            $r = Db::result($sql);
            if (isset($r['error'])) {
                $title['error'] = $r['error'];
                //echo $title['error'];
            } else {
                $title = $r[0]->title;
            }
        } catch (Exception $e) {
            $title = $e->getMessage();
        }

        return $title;
    }

    /**
     * Page Dropdown.
     *
     * $vars = array(
     *     'name' => 'input_name',
     *     'type' => 'type',
     *     'parent' => 'parent',
     *     'order_by' => '',
     *     'sort' => 'ASC',
     *     'selected' => ''
     *     );
     */
    public static function dropdown($vars)
    {
        if (is_array($vars)) {
            //print_r($vars);
            $name = $vars['name'];
            $where = "WHERE `status` = '1' ";
            if (isset($vars['type'])) {
                $type = Typo::cleanX($vars['type']);
                $where .= " AND `type` = '{$type}' ";
            } else {
                $where .= ' ';
            }

            $order_by = 'ORDER BY ';
            if (isset($vars['order_by'])) {
                $orderBy = Typo::cleanX($vars['order_by']);
                $order_by .= " `{$orderBy}` ";
            } else {
                $order_by .= ' `name` ';
            }
            if (isset($vars['sort'])) {
                $sort = " ".Typo::cleanX($vars['sort']). " ";
            } else {
                $sort = 'ASC';
            }
        }
        $cat = Db::result("SELECT * FROM `posts` {$where} {$order_by} {$sort}");
        $num = Db::$num_rows;
        $drop = "<select name=\"{$name}\" class=\"form-control\"><option></option>";
        if ($num > 0) {
            foreach ($cat as $c) {
                // if ($c->parent == '') {
                if (isset($vars['selected']) && $c->id == $vars['selected']) {
                    $sel = 'SELECTED';
                } else {
                    $sel = '';
                }
                $drop .= "<option value=\"{$c->id}\" $sel style=\"padding-left: 10px;\">{$c->title}</option>";
                    // foreach ($cat as $c2) {
                    //
                    //     if ($c2->parent == $c->id) {
                    //         if (isset($vars['selected']) && $c2->id == $vars['selected']) $sel = "SELECTED"; else $sel = "";
                    //         $drop .= "<option value=\"{$c2->id}\" $sel style=\"padding-left: 10px;\">&nbsp;&nbsp;&nbsp;{$c2->name}</option>";
                    //     }
                    // }
                // }
            }
        }
        $drop .= '</select>';

        return $drop;
    }

    public static function addParam($param, $value, $post_id)
    {
        $sql = array(
                'table' => 'posts_param',
                'key' => array(
                        'post_id' => Typo::int($post_id),
                        'param' => Typo::cleanX($param),
                        'value' => Typo::cleanX($value),
                    ),
            );
        $q = Db::insert($sql);
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    public static function editParam($param, $value, $post_id)
    {
        $post_id = Typo::int($post_id);
        $param = Typo::cleanX($param);
        $value = Typo::cleanX($value);
        $sql = "UPDATE `posts_param` SET `value` = '{$value}' WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' ";
        $q = Db::query($sql);
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    public static function getParam($param, $post_id)
    {
        $post_id = Typo::int($post_id);
        $param = Typo::cleanX($param);
        $sql = "SELECT * FROM `posts_param` WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' LIMIT 1";
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            return Typo::Xclean($q[0]->value);
        } else {
            return '';
        }
    }

    public static function delParam($param, $post_id)
    {
        $post_id = Typo::int($post_id);
        $param = Typo::cleanX($param);
        $sql = "DELETE FROM `posts_param` WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' LIMIT 1";
        $q = Db::query($sql);
        if ($q) {
            return true;
        } else {
            return false;
        }
    }

    public static function existParam($param, $post_id)
    {
        $post_id = Typo::int($post_id);
        $param = Typo::cleanX($param);
        $sql = "SELECT `id` FROM `posts_param` WHERE `post_id` = '{$post_id}' AND `param` = '{$param}' LIMIT 1";
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function prepare($post)
    {
        if (Options::v('multilang_enable') === 'on') {
            $langs = Language::isActive();
            if ($langs != '') {
                foreach ($post as $p) {
                    if (self::existParam('multilang', $p->id)
                        && Options::v('multilang_default') !== $langs) {
                        $lang = Language::getLangParam($langs, $p->id);
                        $posts = get_object_vars($p);
                        $posts = is_array($lang) ? array_merge($posts, $lang) : $posts;
                    } else {
                        $posts = $p;
                    }
                    $posts_arr = array();
                    $posts_arr = json_decode(json_encode($posts), false);
                    // $posts[] = $posts;
                    $post_arr[] = $posts_arr;
                    $post = $post_arr;
                }
            }
        }

        return $post;
    }

    // $vars = array(
    //     'num' => '',
    //     'cat' => '',
    //     'type' => 'post',
    //     'excerpt' => 'true',
    //     'excerpt_max' => '200',
    //     'title' => 'true',
    //     'author' => 'true',
    //     'date' => 'true',
    //     'image' => 'true',
    //     'class' => array(
    //                    'ul' => '',
    //                    'li' => '',
    //                    'p' => '',
    //                    'h4' => '',
    //                )
    // );

    public static function lists($vars)
    {
        $imgSize = isset($vars['image_size']) ? $vars['image_size'] : 60;
        $class = isset($vars['class']) ? $vars['class'] : '';

        $imgClass = isset($class['img']) ? $class['img'] : '';
        $ulClass = isset($class['row']) ? $class['row'] : '';
        $liClass = isset($class['list']) ? $class['list'] : '';
        $pClass = isset($class['p']) ? $class['p'] : '';
        $h4Class = isset($class['h4']) ? $class['h4'] : '';
        $dateClass = isset($class['date']) ? $class['date'] : '';
        $excerptMax = isset($vars['excerpt_max']) ? $vars['excerpt_max'] : '200';

        $pcat = self::recent($vars);
        if (isset($pcat['error'])) {
            return _('No Post(s) found.');
        } else {
            $pcat = self::prepare($pcat);
            $html = "";
            foreach ($pcat as $p) {
                $html .= '<div class="recent-list-item d-flex align-items-center mb-3 '.$ulClass.'">';
                $content = (isset($vars['excerpt']) && $vars['excerpt'] === true) ? substr(
                    strip_tags(
                        Typo::Xclean($p->content)
                    ),
                    0,
                    $excerptMax
                ) : '';
                if (isset($vars['image']) && $vars['image'] == true) {
                    $post_image = Posts::getPostImage($p->id);
                    $img = ( $post_image != "" ) ? $post_image: self::getImage(Typo::Xclean($p->content), 1);
                    if ($img != '') {
                        $img = Url::thumb($img, 'square', $imgSize);
                    } else {
                        $img = Url::thumb('assets/images/noimage.png', 'square', $imgSize);
                    }
                    $html .= '<div class="flex-shrink-0">
                        <a href="'.Url::post($p->id).'">
                          <img class="'.$imgClass.'" src="'.$img.'" alt="'.$p->title.'" width="'.$imgSize.'" height="'.$imgSize.'" style="object-fit: cover;">
                        </a>
                      </div>';
                }
                $html .= '<div class="flex-grow-1 ms-3 '.$liClass.'">';
                $html .= (isset($vars['title']) && $vars['title'] === true) ? '<h4 class="media-heading mb-1 '.$h4Class.'"><a href="'.Url::post($p->id)."\">{$p->title}</a></h4>" : '';
                $html .= (isset($vars['date']) && $vars['date'] === true) ? '<small class="text-muted '.$dateClass.'">'.Date::local($p->date).' </small> ' : '';
                $html .= (isset($vars['author']) && $vars['author'] === true) ? '<small class="text-muted">by : '.$p->author.'</small>' : '';
                $html .= (isset($vars['excerpt']) && $vars['excerpt'] === true) ? '<p class="mb-0 '.$pClass.'">'.$content.'</p>' : '';
                $html .= '</div>';
                $html .= '</div>';
            }

            return $html;
        }
    }

    public static function tags($id, $title = 'Tags')
    {
        $tags = self::getParam('tags', $id);
        $tags_x = explode(',', $tags);
        $tag = [];
        foreach ($tags_x as $t) {
            $tag[] = '<a href="'.Url::tag($t)."\">{$t}</a>";
        }
        $tag = implode(', ', $tag);

        return $title.' : '.$tag;
    }

    public static function related($id, $num, $cat, $mode = 'list', $limit=20)
    {
        $id = Typo::int($id);
        if (self::existParam('tags', $id)) {
            $tag = self::getParam('tags', $id);
            $tag = explode(',', $tag);
            $where_tag = ''; //"AND B.`param` = 'tags' ";
            foreach ($tag as $t) {
                $where_tag .= " OR B.`value` LIKE '%%".$t."%%' ";
            }
        } else {
            $where_tag = '';
        }
        $post_type = self::type($id);
        $post = Db::result(
            sprintf(
                "SELECT DISTINCT B.`post_id`, A.`id`, A.`date`, A.`title`, A.`content`,
                        A.`author`, A.`cat`, A.`type`
                        FROM `posts` AS A
                        JOIN `posts_param` AS B
                        ON A.`id` = B.`post_id`
                        WHERE (A.`cat` = '%d' %s)
                        AND A.`id` != '%d'
                        AND A.`status` = '1'
                        AND A.`type` = '%s'
                        ORDER BY
                        RAND() LIMIT %d, %d",
                $cat,
                $where_tag,
                $id,
                $post_type,
                0,
                $num
            )
        );
        if (isset($post['error'])) {
            $related = '<div class="col-sm-12">'._('No Related Post(s)').'</div>';
        } else {
            $related = '';
            if ($mode == 'list') {
                $related .= '<ul class="list-group list-group-flush related">';
                foreach ($post as $p) {
                    if ($p->id != $id) {
                        $related .= '<li class="list-group-item"><a href="'.Url::post($p->id)."\">$p->title</a></li>";
                    } else {
                        $related .= '';
                    }
                }
                $related .= '</ul>';
            } elseif ($mode == 'box') {
                $related .= '<div class="row related-box">';
                foreach ($post as $p) {
                    if ($p->id != $id) {
                        $title = (strlen($p->title) > $limit) ? substr($p->title, 0, $limit-2).'...' : $p->title;
                        $post_image = Posts::getPostImage($p->id);
                        $img = ( $post_image != "" ) ? $post_image: Posts::getImage(Typo::Xclean($p->content), 1);
                        $imgurl = $img == "" ? Url::thumb(Site::$url."assets/images/noimage.png", 'large', 400): Url::thumb($img, 'large', 400);

                        $related .= '<div class="col-6 col-sm-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden related-card transition-base">
                                <a href="'.Url::post($p->id).'" class="text-decoration-none text-dark h-100 d-flex flex-column">
                                    <div class="ratio ratio-16x9">
                                        <img src="'.$imgurl.'" class="card-img-top object-fit-cover" alt="'.$p->title.'">
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-bold mb-0" style="font-size: 0.9rem; line-height: 1.4;">'.$title.'</h6>
                                    </div>
                                </a>
                            </div>
                        </div>';
                    }
                }
                $related .= '</div>';
            }
        }

        return $related;
    }

    public static function type($post_id)
    {
        $sql = "SELECT `type` FROM `posts` WHERE `id` = '{$post_id}' LIMIT 1";
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            return $q[0]->type;
        } else {
            return '';
        }
    }

    /**
     * Get Post ID from Slug
     * @param string $slug
     * @return mixed
     */
    public static function idSlug($slug)
    {
        $sql = "SELECT `id` FROM `posts` WHERE `slug` = '{$slug}' LIMIT 1";
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            return $q[0]->id;
        } else {
            return '';
        }
    }

    /**
     * Get Post Content by Post ID
     * @param int $id
     * @return mixed
     */
    public static function getPostContent($id)
    {
        $sql = sprintf("SELECT `content` FROM `posts` WHERE `id` = '%d' ORDER BY `date` DESC LIMIT 1", $id);
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            $r = Typo::Xclean($q[0]->content);
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    public static function author($id)
    {
        $sql = sprintf("SELECT `author` FROM `posts` WHERE `id` = '%d' LIMIT 1", $id);
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            $r = $q[0]->author;
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    public static function cat($id)
    {
        $sql = sprintf("SELECT `cat` FROM `posts` WHERE `id` = '%d' ORDER BY `date` DESC LIMIT 1", $id);
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            $r = $q[0]->cat;
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    public static function date($id)
    {
        $sql = sprintf("SELECT `date` FROM `posts` WHERE `id` = '%d' ORDER BY `date` DESC LIMIT 1", $id);
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            $r = $q[0]->date;
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    /**
     * Get Post by Cat ID
     * @param int $id
     * @param int $max
     * @return mixed
     */
    public static function getPostByCat($id, $max)
    {
        $sql = sprintf("SELECT * FROM `posts` WHERE `cat` = '%d' AND `status` = '1' ORDER BY `date` DESC LIMIT 0, %d", $id, $max);
        $q = Db::result($sql);
        if (Db::$num_rows > 0) {
            $r = $q;
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    public static function getPostImage($post_id)
    {
        if (Posts::existParam('post_image', $post_id)) {
            $image = Posts::getParam('post_image', $post_id);
        } else {
            $image = '';
        }

        return $image;
    }

    public static function getImage($post, $number=1)
    {
        preg_match_all('/<img .*?src=[\'"]([^\'"]+)[\'"].*?>/si', $post, $im);
        // print_r($im);
        if (count($im) >= 1) {
            return self::setImage($im, $number);
        }
    }

    public static function setImage($im, $number) {
        if(isset($number)) {
            $num = $number-1;
            if(isset($im[1][$num])) {
                return $im[1][$num];
            } else {
                return isset($im[1][0]) ? $im[1][0]: "";
            }
        } else {
            for ($i = 1; $i <= count($im); $i += 2) {
                if (isset($im[$i][0])) {
                    return $im[$i][0];
                }
            }
        }
    }


    /**
     * $vars = [
     *      'id'        => '',
     *      'type'      => '',
     *      'status'    => '',
     *      'slug'      => '',
     *      'where'     => ''
     * ]
     * @param $vars
     * @return mixed
     */
    public static function fetch($vars)
    {

        $where = '1 ';
        if (isset($vars['id'])) {
            $where .= " AND `id` = '{$vars['id']}' ";
        }
//        if (isset($vars['slug']) && $vars['slug'] != '') {
//            $where .= "OR `slug` = '{$vars['slug']}' ) ";
//        } else {
//            $where .= ") ";
//        }
        if (isset($vars['type'])) {
            $where .= " AND `type` = '{$vars['type']}' ";
        }
        if (isset($vars['status'])) {
            $where .= " AND `status` = '{$vars['status']}' ";
        }
        if (isset($vars['where']) && $vars['where'] != '') {
            $where .= $vars['where'];
        }

        $sql = "SELECT * FROM `posts` WHERE {$where}";
        $q = Db::result($sql);
        if (!isset($q['error'])){
            $arrA = array();
            foreach ($q[0] as $a => $b) {
                $arrA []= [ $a => $b ];
            }
            // get params
            $sql = "SELECT * FROM `posts_param` WHERE `post_id` = '{$vars['id']}'";
            $r = Db::result($sql);
            if (!isset($r['error'])){
                $arr = array();
                foreach ($r as $k => $v) {
                    $arr[] = [ $v->param => $v->value ];
                }

                $arrM = array_merge($arrA, $arr);
                $p = array();
                foreach ($arrM as $i => $l) {
                    $p = array_merge($l, $p);
                }
                $res[0] = (object)$p;
            } else {
                $p = array();
                foreach ($arrA as $i => $l) {
                    $p = array_merge($l, $p);
                }
                $res[0] = (object)$p;
            }
            
        } else {
            $res['error'] = _("Data not found");
        }


        return $res;
    }

    public static function createSlug($str)
    {
        $slug = Typo::slugify($str);
        // check if slug is exist
        if (self::slugExist($slug)) {
            $slnum = (int) self::getLastSlug($slug)+1;
            $slug = $slug.'-'.$slnum;
        }

        return $slug;
    }

    /**
     * Check if Slug is Exist
     * @param string $slug
     * @return bool
     */
    public static function slugExist($slug)
    {
        $slug = Typo::cleanX($slug);
        $sql = "SELECT `id` FROM `posts` WHERE `slug` LIKE '%{$slug}%' ";
        Db::result($sql);
        if (Db::$num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getLastSlug($slug)
    {
        $sql = "SELECT `slug` FROM `posts` WHERE `slug` LIKE '%{$slug}%' ORDER BY `id` DESC LIMIT 1";
        $q = Db::result($sql);

        $slnum = str_replace($slug, '', $q[0]->slug);
        $slnum = ($slnum !== '') ? str_replace('-', '', $slnum): 0;

        return $slnum;

    }
}

/* End of file Posts.class.php */
/* Location: ./inc/lib/Posts.class.php */
