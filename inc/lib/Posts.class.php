<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140930
 *
 * @version 2.0.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Posts extends Model
{
    protected $table = 'posts';

    public static $last_id = '';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function categories($id, $type = 'post')
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
            $vars = Hooks::filter('post_pre_insert_filter', $vars);
            $slug = self::createSlug($vars['title']);
            $vars = array_merge($vars, array('slug' => $slug));
            
            $post = Query::table('posts')->insert($vars);
            self::$last_id = Db::$last_id; // Db class still maintains the last_id property
            // Auto-generate and cache excerpt on insert
            $excerpt = self::generateExcerpt($vars['content'] ?? '');
            self::addParam('excerpt', $excerpt, self::$last_id);
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
            $vars = Hooks::filter('post_pre_update_filter', $vars);
            $id = Typo::int($_GET['id']);
            $post = Query::table('posts')->where('id', $id)->update($vars);
            // Regenerate and cache excerpt when content is updated
            if (!empty($vars['content'])) {
                $excerpt = self::generateExcerpt($vars['content']);
                if (self::existParam('excerpt', $id)) {
                    self::editParam('excerpt', $excerpt, $id);
                } else {
                    self::addParam('excerpt', $excerpt, $id);
                }
            }
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
        $post = Query::table('posts')->where('id', $id)->update(['status' => '1']);

        return $post;
    }

    public static function unpublish($id)
    {
        $id = Typo::int($id);
        $post = Query::table('posts')->where('id', $id)->update(['status' => '0']);

        return $post;
    }

    public static function delete($id)
    {
        $id = Typo::int($id);
        try {
            Query::table('posts')->where('id', $id)->delete();
            Query::table('posts_param')->where('post_id', $id)->delete();
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
        $type = isset($vars['type']) ? Typo::cleanX($vars['type']) : 'post';
        $num = isset($vars['num']) ? Typo::int($vars['num']) : '10';
        
        $q = Query::table('posts')->where('type', $type)->where('status', '1');
        if (isset($vars['cat'])) {
            $q->where('cat', Typo::int($vars['cat']));
        }
        $posts = $q->orderBy('date', 'DESC')->limit($num)->get();

        if (empty($posts)) {
            $posts = ['error' => _('Error: No Posts found.')];
        } else {
            $posts = self::prepare($posts);
        }

        return $posts;
    }

    public static function title($id)
    {
        try {
            $r = Query::table('posts')->select('title')->where('id', Typo::int($id))->first();
            if (!$r) {
                $title['error'] = _('Data not found');
            } else {
                $title = $r->title;
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
            $name = $vars['name'];
            $q = Query::table('posts')->where('status', '1');
            if (isset($vars['type'])) {
                $q->where('type', Typo::cleanX($vars['type']));
            }
            $orderBy = isset($vars['order_by']) ? Typo::cleanX($vars['order_by']) : 'name';
            $sort = isset($vars['sort']) ? Typo::cleanX($vars['sort']) : 'ASC';
            $cat = $q->orderBy($orderBy, $sort)->get();
            
            $drop = "<select name=\"{$name}\" class=\"form-control\"><option></option>";
            if (!empty($cat) && is_array($cat)) {
                foreach ($cat as $c) {
                    if (isset($vars['selected']) && $c->id == $vars['selected']) {
                        $sel = 'SELECTED';
                    } else {
                        $sel = '';
                    }
                    $drop .= "<option value=\"{$c->id}\" $sel style=\"padding-left: 10px;\">{$c->title}</option>";
                }
            }
            $drop .= '</select>';

            return $drop;
        }

        return '';
    }

    /**
     * Generate a clean plain-text excerpt from raw post content.
     * Strips all HTML tags, shortcodes, and readmore markers.
     *
     * @param string $content  Raw post content
     * @param int    $length   Max character length (default: 200)
     * @return string
     */
    public static function generateExcerpt($content, $length = 200)
    {
        $text = Typo::Xclean($content);
        // Remove readmore marker
        $text = str_replace('[[--readmore--]]', '', $text);
        // Strip GxEditor shortcodes (e.g. [image id="1"])
        $text = Shortcode::strip($text);
        // Strip remaining HTML
        $text = strip_tags($text);
        // Normalise whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        return mb_substr($text, 0, $length);
    }

    /**
     * Get the cached excerpt for a post, generating and persisting it on demand.
     *
     * @param int    $post_id
     * @param string $content  Raw content (used only when generating for the first time)
     * @param int    $length
     * @return string
     */
    public static function excerpt($post_id, $content = null, $length = 200)
    {
        $post_id = Typo::int($post_id);

        // Return cached value if it exists
        if (self::existParam('excerpt', $post_id)) {
            return self::getParam('excerpt', $post_id);
        }

        // Need to generate – fetch content from DB if not provided
        if ($content === null) {
            $row = Query::table('posts')->select('content')->where('id', $post_id)->first();
            $content = $row ? $row->content : '';
        }

        $excerpt = self::generateExcerpt($content, $length);
        self::addParam('excerpt', $excerpt, $post_id);

        return $excerpt;
    }

    public static function addParam($param, $value, $post_id)
    {
        $q = Query::table('posts_param')->insert([
            'post_id' => Typo::int($post_id),
            'param' => Typo::cleanX($param),
            'value' => Typo::cleanX($value)
        ]);
        
        return $q ? true : false;
    }

    public static function editParam($param, $value, $post_id)
    {
        $q = Query::table('posts_param')
            ->where('post_id', Typo::int($post_id))
            ->where('param', Typo::cleanX($param))
            ->update(['value' => Typo::cleanX($value)]);
            
        return $q ? true : false;
    }

    public static function getParam($param, $post_id)
    {
        $q = Query::table('posts_param')
            ->where('post_id', Typo::int($post_id))
            ->where('param', Typo::cleanX($param))
            ->first();
            
        if ($q) {
            return Typo::Xclean($q->value);
        } else {
            return '';
        }
    }

    public static function delParam($param, $post_id)
    {
        $q = Query::table('posts_param')
            ->where('post_id', Typo::int($post_id))
            ->where('param', Typo::cleanX($param))
            ->delete();
            
        return $q ? true : false;
    }

    public static function existParam($param, $post_id)
    {
        $q = Query::table('posts_param')
            ->select('id')
            ->where('post_id', Typo::int($post_id))
            ->where('param', Typo::cleanX($param))
            ->first();
            
        return $q ? true : false;
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
                // Use pre-cached excerpt param; fall back to on-the-fly generation (auto-caches too)
                if (isset($vars['excerpt']) && $vars['excerpt'] === true) {
                    $content = self::excerpt($p->id, $p->content, (int)$excerptMax);
                } else {
                    $content = '';
                }
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
        
        $randFn = (defined('DB_DRIVER') && DB_DRIVER === 'mysql') ? 'RAND()' : 'RANDOM()';
        
        $post = Query::table('posts')
            ->select("DISTINCT B.post_id, posts.id, posts.date, posts.title, posts.content, posts.author, posts.cat, posts.type")
            ->join('posts_param AS B', 'posts.id', '=', 'B.post_id')
            ->whereRaw("(posts.cat = ? $where_tag) AND posts.id != ? AND posts.status = '1' AND posts.type = ?", [$cat, $id, $post_type])
            ->orderByRaw($randFn)
            ->limit($num, 0)
            ->get();
            
        if (empty($post)) {
            $post = ['error' => _('No Related Post(s)')];
        }
        if (isset($post['error'])) {
            $related = '<div class="col-sm-12">'._('No Related Post(s)').'</div>';
        } else {
            $related = '';
            if ($mode == 'list') {
                $related .= '<ul class="list-group list-group-flush related">';
                foreach ($post as $p) {
                    if (!is_object($p)) continue;
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
                    if (!is_object($p)) continue;
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
        $q = Query::table('posts')->select('type')->where('id', Typo::cleanX($post_id))->first();
        if ($q) {
            return $q->type;
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
        $q = Query::table('posts')->select('id')->where('slug', Typo::cleanX($slug))->first();
        if ($q) {
            return $q->id;
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
        $q = Query::table('posts')->select('content')->where('id', Typo::int($id))->orderBy('date', 'DESC')->first();
        if ($q) {
            $r = Typo::Xclean($q->content);
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    public static function author($id)
    {
        $q = Query::table('posts')->select('author')->where('id', Typo::int($id))->first();
        if ($q) {
            $r = $q->author;
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    public static function cat($id)
    {
        $q = Query::table('posts')->select('cat')->where('id', Typo::int($id))->orderBy('date', 'DESC')->first();
        if ($q) {
            $r = $q->cat;
        } else {
            $r['error'] = _('Error: No Post to Show');
        }

        return $r;
    }

    public static function date($id)
    {
        $q = Query::table('posts')->select('date')->where('id', Typo::int($id))->orderBy('date', 'DESC')->first();
        if ($q) {
            $r = $q->date;
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
        $q = Query::table('posts')
            ->where('cat', Typo::int($id))
            ->where('status', '1')
            ->orderBy('date', 'DESC')
            ->limit($max, 0)
            ->get();
            
        if (!empty($q)) {
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
        $q = Query::table('posts');
        if (isset($vars['id'])) {
            $q->where('id', Typo::int($vars['id']));
        }
        if (isset($vars['type'])) {
            $q->where('type', Typo::cleanX($vars['type']));
        }
        if (isset($vars['status'])) {
            $q->where('status', Typo::cleanX($vars['status']));
        }

        $post = $q->first();
        if ($post) {
            $arrA = [];
            foreach ($post as $a => $b) {
                $arrA []= [ $a => $b ];
            }
            // get params
            $r = Query::table('posts_param')->where('post_id', Typo::int($vars['id']))->get();
            if (!empty($r)) {
                $arr = [];
                foreach ($r as $v) {
                    $arr[] = [ $v->param => $v->value ];
                }

                $arrM = array_merge($arrA, $arr);
                $p = [];
                foreach ($arrM as $l) {
                    $p = array_merge($l, $p);
                }
                $res[0] = (object)$p;
            } else {
                $p = [];
                foreach ($arrA as $l) {
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
        $q = Query::table('posts')->select('id')->where('slug', 'LIKE', "%{$slug}%")->first();
        
        return $q ? true : false;
    }

    public static function getLastSlug($slug)
    {
        $slug = Typo::cleanX($slug);
        $q = Query::table('posts')->select('slug')->where('slug', 'LIKE', "%{$slug}%")->orderBy('id', 'DESC')->first();

        if ($q) {
            $slnum = str_replace($slug, '', $q->slug);
            $slnum = ($slnum !== '') ? str_replace('-', '', $slnum): 0;
            return $slnum;
        }
        return 0;
    }
}

/* End of file Posts.class.php */
/* Location: ./inc/lib/Posts.class.php */
