<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140930
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Posts extends Model
{
    protected $table = 'posts';

    public static $last_id = '';

    /** @var array Custom labels for post types */
    private static $_typeLabels = [];

    /**
     * Posts Constructor.
     *
     * @param array $attributes Initial model attributes mapping.
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Retrieves categories associated with a specific content ID.
     *
     * @param int    $id   Content ID.
     * @param string $type Content type (default: 'post').
     * @return void        (Legacy/Placeholder).
     */
    public static function categories($id, $type = 'post')
    {
    }

    /**
     * Inserts a new post into the database.
     * Automatically handles slug generation, excerpt caching, hook execution, and pinger services.
     *
     * @param array $vars {
     *     @type string $title   Post title.
     *     @type string $content Raw content.
     *     @type string $author  Author ID/name.
     *     @type string $cat     Primary category ID.
     *     @type string $type    Post type (post, page, etc).
     *     @type string $status  Publication status (1 or 0).
     *     @type string $date    Publication date.
     * }
     * @return bool|int    The result of the query or false.
     */
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

    /**
     * Registers custom UI labels for a specific post type.
     * 
     * @param string $type   Post type identifier.
     * @param array  $labels Dictionary of labels (label, create, edit, title_label, title_placeholder).
     */
    public static function setTypeLabel($type, $labels = [])
    {
        self::$_typeLabels[$type] = $labels;
    }

    /**
     * Retrieves a specific UI label for a post type, with fallback to defaults.
     * 
     * @param string $type   Post type identifier.
     * @param string $key    Label key.
     * @return string        The localized label or empty string.
     */
    public static function getTypeLabel($type, $key)
    {
        $defaults = [
            'label' => _("Publication"),
            'create' => _("New"),
            'edit' => _("Edit"),
            'repository_title' => _("Publication Repository"),
            'records_library' => _("Records Library"),
            'new_item' => _("New Publication"),
            'title_label' => _("Publication Title"),
            'title_placeholder' => _("Enter publication title here...")
        ];

        // Specific overrides for core types if not registered
        if (!isset(self::$_typeLabels[$type])) {
            if ($type === 'post') {
                $defaults['label'] = _("Post");
                $defaults['title_label'] = _("Post Title");
            } elseif ($type === 'page') {
                $defaults['label'] = _("Page");
                $defaults['title_label'] = _("Page Title");
            }
        }

        return self::$_typeLabels[$type][$key] ?? $defaults[$key] ?? '';
    }

    /**
     * Generates a dashboard row for a post object, including hooks.
     * Centralized for AJAX and server-side rendering.
     * 
     * @since 2.3.0
     */
    public static function getDashboardRow($pObj, $group, $username, $postType)
    {
        $id = $pObj->id ?? 0;
        $title = $pObj->title ?? '';
        $author = $pObj->author ?? 'Unknown';
        $status_val = $pObj->status ?? '0';
        $content = $pObj->content ?? '';
        $cat = $pObj->cat ?? 0;
        $date = $pObj->date ?? date('Y-m-d H:i:s');
        $views = $pObj->views ?? 0;

        $accessEdit = $group <= 2 ? 1 : ($author == $username ? 1 : 0);
        $accessDelete = $group < 2 ? 1 : 0;

        $status = ($status_val == '0') ? 'warning' : 'success';
        $statusLabel = ($status_val == '0') ? _("Draft") : _("Live");

        $viewsFormatted = number_format($views);
        $commentCount = Query::table('comments')->where('post_id', $id)->where('status', '1')->count();

        // Thumbnail Logic
        $post_image = Posts::getPostImage($id);
        $thumb = ($post_image != "") ? $post_image : Posts::getImage(Typo::Xclean($content), 1);
        $thumbUrl = ($thumb != '') ? Url::thumb($thumb, 'square', 100) : Site::$url . 'assets/images/noimage.png';

        // Modular Action Menu
        $previewUrl = ($postType === 'page') ? Url::page($id) : Url::post($id);
        $actionMenu = [
            'preview' => [
                'label' => _("Preview"),
                'icon' => 'bi bi-eye',
                'href' => $previewUrl,
                'class' => 'btn btn-light btn-sm rounded-circle border',
                'target' => '_blank'
            ]
        ];
        if ($accessEdit) {
            $actionMenu['edit'] = [
                'label' => _("Edit"),
                'icon' => 'bi bi-pencil-square',
                'href' => 'index.php?page=' . (($postType === 'page') ? 'pages' : 'posts') . '&act=edit&id=' . $id . (($postType !== 'page') ? '&type=' . $postType : '') . '&token=' . TOKEN,
                'class' => 'btn btn-light btn-sm rounded-circle border text-success'
            ];
        }
        if ($accessDelete) {
            $actionMenu['delete'] = [
                'label' => _("Delete"),
                'icon' => 'bi bi-trash',
                'href' => 'index.php?page=' . (($postType === 'page') ? 'pages' : 'posts') . '&act=del&id=' . $id . (($postType !== 'page') ? '&type=' . $postType : '') . '&token=' . TOKEN,
                'class' => 'btn btn-light btn-sm rounded-circle border text-danger',
                'onclick' => 'return confirm(\'' . _("Are you sure you want to delete this?") . '\');'
            ];
        }

        // Allow developers to inject extra post actions
        $hookActionMenu = ($postType === 'page') ? 'admin_pages_action_menu' : 'admin_posts_action_menu';
        $actionMenu = Hooks::filter($hookActionMenu, $actionMenu, $pObj);

        $actionsHtml = '<div class="btn-group gap-1">';
        foreach ($actionMenu as $mv) {
            $attr = '';
            if (isset($mv['onclick'])) $attr .= ' onclick="' . $mv['onclick'] . '"';
            if (isset($mv['target'])) $attr .= ' target="' . $mv['target'] . '"';
            $actionsHtml .= '<a href="' . ($mv['href'] ?? '#') . '" class="' . ($mv['class'] ?? 'btn btn-light btn-sm') . '"' . $attr . ' title="' . ($mv['label'] ?? '') . '"><i class="' . ($mv['icon'] ?? '') . '"></i></a>';
        }
        $actionsHtml .= '</div>';

        $row = [
            [
                'content' => "
                <div class='d-flex align-items-center ps-4 py-2'>
                    <div class='me-3 position-relative'>
                        <img src='{$thumbUrl}' class='rounded-3 shadow-sm border' width='50' height='50' style='object-fit: cover;'>
                        <span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white border text-dark extra-small' style='font-size: 0.6rem;'>#{$id}</span>
                    </div>
                    <div>
                        <a href='index.php?page=" . (($postType === 'page') ? 'pages' : 'posts') . "&act=edit&id={$id}" . (($postType !== 'page') ? "&type={$postType}" : "") . "&token=" . TOKEN . "' class='fw-bold text-dark text-decoration-none d-block mb-1 ls-n1' style='font-size: 0.95rem;'>" . ((strlen($title) > 60) ? substr($title, 0, 57) . '...' : $title) . "</a>
                        <div class='d-flex gap-2 align-items-center'>
                            <span class='badge bg-{$status} bg-opacity-10 text-{$status} border border-{$status} border-opacity-25 rounded-pill px-2 fw-bold text-uppercase ls-1' style='font-size: 0.65rem;'>{$statusLabel}</span>
                            " . (($postType !== 'page') ? "<span class='badge bg-light text-muted border px-2 py-1 rounded-pill fw-bold text-uppercase' style='font-size: 0.65rem;'>" . Categories::name($cat) . "</span>" : "") . "
                        </div>
                    </div>
                </div>",
                'class' => 'p-0'
            ],
            [
                'content' => "
                <div class='d-flex align-items-center justify-content-center'>
                    <img src='" . Image::getGravatar(User::email($author), 40) . "' class='rounded-circle me-2 border p-1 bg-white' width='32'>
                    <div class='text-start'>
                        <div class='small fw-bold text-dark mb-0'>{$author}</div>
                        <div class='text-muted extra-small'>" . (($postType === 'page') ? _("Curator") : _("Writer")) . "</div>
                    </div>
                </div>",
                'class' => 'text-center'
            ],
            [
                'content' => "
                <div class='d-flex flex-column align-items-center justify-content-center opacity-75'>
                    <div class='d-flex align-items-center gap-2 mb-1'>
                        <i class='bi bi-" . (($postType === 'page') ? 'activity' : 'eye') . " text-primary'></i>
                        <span class='small fw-bold text-dark'>{$viewsFormatted}</span>
                    </div>
                    " . (($postType === 'page') ? "<div class='extra-small text-muted text-uppercase fw-bold ls-1'>" . _("Visibility") . "</div>" : "<div class='d-flex align-items-center gap-2'>
                        <i class='bi bi-chat-dots text-success'></i>
                        <span class='small fw-bold text-dark'>{$commentCount}</span>
                    </div>") . "
                </div>",
                'class' => 'text-center'
            ],
            [
                'content' => "
                <div class='text-center'>
                    <div class='small fw-bold text-dark mb-0'>" . Date::format($date, 'd M Y') . "</div>
                    <div class='text-muted extra-small'>" . Date::format($date, 'H:i A') . "</div>
                </div>",
                'class' => 'text-center'
            ]
        ];

        // Allow developers to inject extra columns
        $hookRow = ($postType === 'page') ? 'admin_pages_table_row' : 'admin_posts_table_row';
        $filtered = Hooks::filter($hookRow, $row, $pObj);
        if (is_array($filtered) && isset($filtered[1]) && $filtered[1] === $pObj) {
            $row = $filtered[0];
        } else {
            $row = $filtered;
        }

        // Management column
        $row[] = ['content' => $actionsHtml, 'class' => 'text-center'];

        // Selection column
        $row[] = ['content' => "<div class='text-center pe-4'><input type='checkbox' name='post_id[]' value='{$id}' class='check form-check-input shadow-none border'></div>", 'class' => 'p-0'];

        return $row;
    }

    /**
     * Generates dashboard table headers, including hooks.
     * Centralized for AJAX and server-side rendering.
     * 
     * @since 2.3.0
     */
    public static function getDashboardHeaders($postType)
    {
        $headers = [
            ['content' => ($postType === 'page') ? _('Architecture Details') : _('Publication Details'), 'class' => 'ps-4 py-3'],
            ['content' => ($postType === 'page') ? _('Accountability') : _('Ownership'), 'class' => 'text-center'],
            ['content' => _('Engagement'), 'class' => 'text-center'],
            ['content' => _('Timeline'), 'class' => 'text-center'],
            ['content' => _('Management'), 'class' => 'text-center'],
            ['content' => '<div class="text-center pe-4"><input type="checkbox" id="selectall" class="form-check-input shadow-none border"></div>', 'class' => 'p-0', 'width' => '50px']
        ];

        $hookHeaders = ($postType === 'page') ? 'admin_pages_table_headers' : 'admin_posts_table_headers';
        $filtered = Hooks::filter($hookHeaders, $headers, $postType);
        if (is_array($filtered) && isset($filtered[1]) && $filtered[1] === $postType) {
            return $filtered[0];
        } else {
            return $filtered;
        }
    }

    /**
     * Updates an existing post record.
     * Triggers excerpt regeneration if content is modified.
     *
     * @param array $vars Dictionary of post data to update. Requires 'id' in $_GET.
     * @return bool|int    Result of the update query.
     */
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

    /**
     * Sets the publication status of a post to active ('1').
     *
     * @param int $id Post ID.
     * @return bool|int Result of the update.
     */
    public static function publish($id)
    {
        $id = Typo::int($id);
        $post = Query::table('posts')->where('id', $id)->update(['status' => '1']);
        Hooks::run('post_sqladd_action', ['status' => '1'], $id);

        return $post;
    }

    /**
     * Sets the publication status of a post to inactive ('0').
     *
     * @param int $id Post ID.
     * @return bool|int Result of the update.
     */
    public static function unpublish($id)
    {
        $id = Typo::int($id);
        $post = Query::table('posts')->where('id', $id)->update(['status' => '0']);
        Hooks::run('post_sqladd_action', ['status' => '0'], $id);

        return $post;
    }

    /**
     * Deletes a post and all associated data (params, hooks, comments).
     *
     * @param int $id Post ID.
     * @return bool|string True on success, error message on exception.
     */
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

    /**
     * Cleans and filters post content for output.
     * Strips legacy readmore tags and applies 'post_content_filter' hooks.
     *
     * @param string $vars Raw post content.
     * @return string      Filtered content.
     */
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
     * Formats post content for summary views by handling the [[--readmore--]] tag.
     * Appends a "Read More" link if the marker is present.
     *
     * @param string $post Raw post content.
     * @param int    $id   Post ID (for the URL).
     * @return string      Excerpt-style formatted content.
     */
    public static function format($post, $id)
    {
        // split post for readmore...
        $post = Typo::Xclean($post);
        $more = explode('[[--readmore--]]', $post);
        //print_r($more);
        if (count($more) > 1) {
            // $post = explode('[[--readmore--]]', $post);
            $post = $more[0] . ' <a href="' . Url::post($id) . '">' . _("Read More") . '</a>';
        }

        $post = Hooks::filter('post_content_filter', $post);

        return $post;
    }

    /**
     * Retrieves a list of recent posts filtered by type and category.
     *
     * @param array $vars {
     *     @type int    $num  Max number of posts (default: 10).
     *     @type int    $cat  Category ID filter.
     *     @type string $type Post type filter (default: 'post').
     * }
     * @return array List of post objects or error array.
     */
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

    /**
     * Retrieves only the title of a specific post.
     *
     * @param int $id Post ID.
     * @return string  The post title or error message.
     */
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
     * Generates an HTML <select> dropdown of pages or posts.
     *
     * @param array $vars {
     *     @type string $name     The 'name' attribute for the select.
     *     @type string $type     Post type filter.
     *     @type string $order_by Column to sort by (default: 'name').
     *     @type string $sort     Sort direction (ASC/DESC).
     *     @type mixed  $selected ID of the pre-selected option.
     * }
     * @return string HTML dropdown.
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

    /**
     * Attaches a metadata parameter to a post.
     * Supports special bypass for builder_* parameters to allow raw HTML/CSS.
     *
     * @param string $param   Parameter key.
     * @param mixed  $value   Parameter value.
     * @param int    $post_id Post ID.
     * @return bool            True on success, false on failure.
     */
    public static function addParam($param, $value, $post_id)
    {
        // builder_* params: allow raw HTML/CSS but strip <script> tags for security.
        // Legitimate JS is stored separately in builder_js param.
        if (strpos($param, 'builder_') === 0) {
            $cleanValue = $value;
        } else {
            if (is_array($value)) {
                $cleanValue = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $cleanValue = Typo::cleanX($value);
            }
        }


        $q = Query::table('posts_param')->insert([
            'post_id' => Typo::int($post_id),
            'param' => Typo::cleanX($param),
            'value' => $cleanValue
        ]);

        return $q ? true : false;
    }

    /**
     * Updates an existing metadata parameter for a post.
     *
     * @param string $param   Parameter key.
     * @param mixed  $value   New parameter value.
     * @param int    $post_id Post ID.
     * @return bool            True on success, false on failure.
     */
    public static function editParam($param, $value, $post_id)
    {
        // builder_* params: allow raw HTML/CSS but strip <script> tags for security.
        // Legitimate JS is stored separately in builder_js param.
        if (strpos($param, 'builder_') === 0) {
            $cleanValue = $value;
        } else {
            if (is_array($value)) {
                $cleanValue = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $cleanValue = Typo::cleanX($value);
            }
        }


        $q = Query::table('posts_param')
            ->where('post_id', Typo::int($post_id))
            ->where('param', Typo::cleanX($param))
            ->update(['value' => $cleanValue]);

        return $q ? true : false;
    }

    /**
     * Retrieves a metadata parameter for a post.
     *
     * @param string  $param   Parameter key.
     * @param int     $post_id Post ID.
     * @return string          The parameter value or empty string.
     */
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

    /**
     * Retrieves all metadata parameters for a post.
     *
     * @param int $post_id Post ID.
     * @return array        Associative array of parameters.
     */
    public static function getParams($post_id)
    {
        $q = Query::table('posts_param')
            ->where('post_id', Typo::int($post_id))
            ->get();

        $params = [];
        if ($q) {
            foreach ($q as $p) {
                $params[$p->param] = Typo::Xclean($p->value);
            }
        }
        return $params;
    }

    /**
     * Get list of published pages for a dropdown selection.
     * 
     * @return array
     */
    public static function getPageList()
    {
        $pages = Query::table('posts')
            ->where('type', 'page')
            ->where('status', '1')
            ->orderBy('title', 'ASC')
            ->get();

        $list = ['0' => _('-- Recent Posts (Default) --')];
        if ($pages) {
            foreach ($pages as $p) {
                $list[$p->id] = Typo::Xclean($p->title);
            }
        }
        return $list;
    }

    /**
     * Deletes a metadata parameter for a post.
     *
     * @param string $param   Parameter key.
     * @param int    $post_id Post ID.
     * @return bool            True on success, false on failure.
     */
    public static function delParam($param, $post_id)
    {
        $q = Query::table('posts_param')
            ->where('post_id', Typo::int($post_id))
            ->where('param', Typo::cleanX($param))
            ->delete();

        return $q ? true : false;
    }

    /**
     * Checks if a metadata parameter exists for a post.
     *
     * @param string $param   Parameter key.
     * @param int    $post_id Post ID.
     * @return bool            True if exists, false otherwise.
     */
    public static function existParam($param, $post_id)
    {
        $q = Query::table('posts_param')
            ->select('id')
            ->where('post_id', Typo::int($post_id))
            ->where('param', Typo::cleanX($param))
            ->first();

        return $q ? true : false;
    }

    /**
     * Prepares post objects for regional output.
     * Handles multi-language synchronization by overwriting default content with language parameters.
     *
     * @param array $post List of post objects.
     * @return array       Prepared list of post objects.
     */
    public static function prepare($post)
    {
        if (Options::v('multilang_enable') === 'on') {
            $langs = Language::isActive();
            if ($langs != '') {
                foreach ($post as $p) {
                    if (
                        self::existParam('multilang', $p->id)
                        && Options::v('multilang_default') !== $langs
                    ) {
                        $lang = Language::getLangParam($langs, $p->id);
                        $posts = get_object_vars($p);
                        $posts = is_array($lang) ? array_merge($posts, $lang) : $posts;
                    } else {
                        $posts = $p;
                    }

                    // Prepare title for frontend: decode entities and apply filters
                    if (is_object($posts)) {
                        $posts->title = Typo::Xclean($posts->title);
                        $posts->title = Hooks::filter('post_title_filter', $posts->title);
                    } elseif (is_array($posts)) {
                        $posts['title'] = Typo::Xclean($posts['title']);
                        $posts['title'] = Hooks::filter('post_title_filter', $posts['title']);
                    }

                    $posts_arr = array();
                    $posts_arr = json_decode(json_encode($posts), false);
                    // $posts[] = $posts;
                    $post_arr[] = $posts_arr;
                    $post = $post_arr;
                }
            }
        } else {
            foreach ($post as $p) {
                if (is_object($p)) {
                    $p->title = Typo::Xclean($p->title);
                    $p->title = Hooks::filter('post_title_filter', $p->title);
                }
            }
        }

        return $post;
    }

    /**
     * Renders an HTML list of posts with optional titles, excerpts, authors, and images.
     * Uses cached excerpts where possible.
     *
     * @param array $vars {
     *     @type int   $num         Max number of posts.
     *     @type bool  $excerpt     Whether to show excerpt (default: false).
     *     @type int   $excerpt_max Max excerpt length.
     *     @type bool  $title       Whether to show title.
     *     @type bool  $author      Whether to show author.
     *     @type bool  $date        Whether to show date.
     *     @type bool  $image       Whether to show featured image.
     *     @type int   $image_size  Size of the square thumbnail.
     *     @type array $class       CSS classes for various elements (ul, li, img, etc).
     * }
     * @return string HTML list of posts.
     */
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
                $html .= '<div class="recent-list-item d-flex align-items-center mb-3 ' . $ulClass . '">';
                // Use pre-cached excerpt param; fall back to on-the-fly generation (auto-caches too)
                if (isset($vars['excerpt']) && $vars['excerpt'] === true) {
                    $content = self::excerpt($p->id, $p->content, (int) $excerptMax);
                } else {
                    $content = '';
                }
                if (isset($vars['image']) && $vars['image'] == true) {
                    $post_image = Posts::getPostImage($p->id);
                    $img = ($post_image != "") ? $post_image : self::getImage(Typo::Xclean($p->content), 1);
                    if ($img != '') {
                        $img = Url::thumb($img, 'square', $imgSize);
                    } else {
                        $img = Url::thumb('assets/images/noimage.png', 'square', $imgSize);
                    }
                    $html .= '<div class="flex-shrink-0">
                        <a href="' . Url::post($p->id) . '">
                          <img class="' . $imgClass . '" src="' . $img . '" alt="' . Typo::Xclean($p->title) . '" width="' . $imgSize . '" height="' . $imgSize . '" style="object-fit: cover;">
                        </a>
                      </div>';
                }
                $html .= '<div class="flex-grow-1 ms-3 ' . $liClass . '">';
                $html .= (isset($vars['title']) && $vars['title'] === true) ? '<h4 class="media-heading mb-1 ' . $h4Class . '"><a href="' . Url::post($p->id) . '">' . Typo::Xclean($p->title) . '</a></h4>' : '';
                $html .= (isset($vars['date']) && $vars['date'] === true) ? '<small class="text-muted ' . $dateClass . '">' . Date::local($p->date) . ' </small> ' : '';
                $html .= (isset($vars['author']) && $vars['author'] === true) ? '<small class="text-muted">by : ' . Typo::Xclean($p->author) . '</small>' : '';
                $html .= (isset($vars['excerpt']) && $vars['excerpt'] === true) ? '<p class="mb-0 ' . $pClass . '">' . $content . '</p>' : '';
                $html .= '</div>';
                $html .= '</div>';
            }

            return $html;
        }
    }

    /**
     * Retrieves and formats the tags parameter for a post as clickable links.
     *
     * @param int    $id    Post ID.
     * @param string $title Header text (default: 'Tags').
     * @return string       Formatted HTML string of tags.
     */
    public static function tags($id, $title = 'Tags')
    {
        $tags = self::getParam('tags', $id);
        $tags_x = explode(',', $tags);
        $tag = [];
        foreach ($tags_x as $t) {
            $tag[] = '<a href="' . Url::tag($t) . '">' . Typo::Xclean($t) . '</a>';
        }
        $tag = implode(', ', $tag);

        return $title . ' : ' . $tag;
    }

    /**
     * Retrieves a list of related posts based on tags and category.
     *
     * @param int    $id    Current post ID to exclude.
     * @param int    $num   Number of related posts to fetch.
     * @param int    $cat   Category ID to match.
     * @param string $mode  Display mode: 'list' (default) or 'box'.
     * @param int    $limit Character limit for titles in 'box' mode.
     * @return string       HTML output of related posts.
     */
    public static function related($id, $num, $cat, $mode = 'list', $limit = 20)
    {
        $id = Typo::int($id);
        if (self::existParam('tags', $id)) {
            $tag = self::getParam('tags', $id);
            $tag = explode(',', $tag);
            $where_tag = ''; //"AND B.`param` = 'tags' ";
            foreach ($tag as $t) {
                $where_tag .= " OR B.`value` LIKE '%%" . $t . "%%' ";
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
            $related = '<div class="col-sm-12">' . _('No Related Post(s)') . '</div>';
        } else {
            $related = '';
            if ($mode == 'list') {
                $related .= '<ul class="list-group list-group-flush related">';
                foreach ($post as $p) {
                    if (!is_object($p))
                        continue;
                    if ($p->id != $id) {
                        $related .= '<li class="list-group-item"><a href="' . Url::post($p->id) . '">' . Typo::Xclean($p->title) . '</a></li>';
                    } else {
                        $related .= '';
                    }
                }
                $related .= '</ul>';
            } elseif ($mode == 'box') {
                $related .= '<div class="row related-box">';
                foreach ($post as $p) {
                    if (!is_object($p))
                        continue;
                    if ($p->id != $id) {
                        $title = (strlen($p->title) > $limit) ? substr($p->title, 0, $limit - 2) . '...' : $p->title;
                        $post_image = Posts::getPostImage($p->id);
                        $img = ($post_image != "") ? $post_image : Posts::getImage(Typo::Xclean($p->content), 1);
                        $imgurl = $img == "" ? Url::thumb(Site::$url . "assets/images/noimage.png", 'large', 400) : Url::thumb($img, 'large', 400);

                        $related .= '<div class="col-6 col-sm-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden related-card transition-base">
                                <a href="' . Url::post($p->id) . '" class="text-decoration-none text-dark h-100 d-flex flex-column">
                                    <div class="ratio ratio-16x9">
                                        <img src="' . $imgurl . '" class="card-img-top object-fit-cover" alt="' . Typo::Xclean($p->title) . '">
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-bold mb-0" style="font-size: 0.9rem; line-height: 1.4;">' . Typo::Xclean($title) . '</h6>
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

    /**
     * Retrieves the content type (e.g. 'post', 'page') for a specific post.
     *
     * @param int $post_id Post ID.
     * @return string      The post type.
     */
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
     * Retrieves the raw content of a specific post.
     *
     * @param int $id Post ID.
     * @return string|array Raw content string or error array.
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

    /**
     * Retrieves the author of a specific post.
     *
     * @param int $id Post ID.
     * @return string|array Author identifier or error array.
     */
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

    /**
     * Retrieves the primary category ID of a specific post.
     *
     * @param int $id Post ID.
     * @return int|array Category ID or error array.
     */
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

    /**
     * Retrieves the publication date of a specific post.
     *
     * @param int $id Post ID.
     * @return string|array Date string or error array.
     */
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
     * Retrieves a list of posts filtered by category ID.
     *
     * @param int $id  Category ID.
     * @param int $max Max number of posts.
     * @return array    List of post objects or error array.
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

    /**
     * Retrieves the value of the 'post_image' parameter for a specific post.
     *
     * @param int $post_id Post ID.
     * @return string      The image path/URL or empty string.
     */
    public static function getPostImage($post_id)
    {
        if (Posts::existParam('post_image', $post_id)) {
            $image = Posts::getParam('post_image', $post_id);
        } else {
            $image = '';
        }

        return $image;
    }

    /**
     * Extracts an image URL from the post content using regex.
     *
     * @param string $post   HTML content of the post.
     * @param int    $number Which image to extract (default: first).
     * @return string|null   The image URL or null.
     */
    public static function getImage($post, $number = 1)
    {
        preg_match_all('/<img .*?src=[\'"]([^\'"]+)[\'"].*?>/si', $post, $im);
        // print_r($im);
        if (count($im) >= 1) {
            return self::setImage($im, $number);
        }
    }

    /**
     * Logic for selecting a specific match from an array of extracted images.
     *
     * @param array $im     Regex matches from getImage().
     * @param int   $number Match index (1-based).
     * @return string       The selected image URL or empty string.
     */
    public static function setImage($im, $number)
    {
        if (isset($number)) {
            $num = $number - 1;
            if (isset($im[1][$num])) {
                return $im[1][$num];
            } else {
                return isset($im[1][0]) ? $im[1][0] : "";
            }
        } else {
            for ($i = 1; $i <= count($im); $i += 2) {
                if (isset($im[$i][0])) {
                    return $im[$i][0];
                }
            }
        }

        return '';
    }


    /**
     * Fetches a post and its associated parameters in a single merged object.
     *
     * @param array $vars {
     *     @type int    $id     Post ID.
     *     @type string $type   Post type.
     *     @type string $status Post status.
     * }
     * @return array List containing the merged post object or error array.
     */
    public static function fetch($vars)
    {
        if (is_numeric($vars)) {
            $vars = ['id' => $vars];
        }
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
                $arrA[] = [$a => $b];
            }
            // get params
            $r = Query::table('posts_param')->where('post_id', Typo::int($vars['id']))->get();
            if (!empty($r)) {
                $arr = [];
                foreach ($r as $v) {
                    $arr[] = [$v->param => $v->value];
                }

                $arrM = array_merge($arrA, $arr);
                $p = [];
                foreach ($arrM as $l) {
                    $p = array_merge($l, $p);
                }
                $res[0] = (object) $p;
            } else {
                $p = [];
                foreach ($arrA as $l) {
                    $p = array_merge($l, $p);
                }
                $res[0] = (object) $p;
            }

        } else {
            $res['error'] = _("Data not found");
        }


        return $res;
    }

    /**
     * Creates a unique URL-friendly slug from a title string.
     * Automatically appends a numeric suffix if the slug already exists.
     *
     * @param string $str The original title.
     * @return string      The unique slug.
     */
    public static function createSlug($str)
    {
        $slug = Typo::slugify($str);
        // check if slug is exist
        if (self::slugExist($slug)) {
            $slnum = (int) self::getLastSlug($slug) + 1;
            $slug = $slug . '-' . $slnum;
        }

        return $slug;
    }

    /**
     * Checks if a slug (full or partial) already exists in the posts table.
     *
     * @param string $slug The slug to check.
     * @return bool         True if exists, false otherwise.
     */
    public static function slugExist($slug)
    {
        $slug = Typo::cleanX($slug);
        $q = Query::table('posts')->select('id')->where('slug', 'LIKE', "%{$slug}%")->first();

        return $q ? true : false;
    }

    /**
     * Retrieves the numeric suffix of the last entry matching a specific slug base.
     *
     * @param string $slug The slug base.
     * @return string      The suffix found (as a string).
     */
    public static function getLastSlug($slug)
    {
        $slug = Typo::cleanX($slug);
        $q = Query::table('posts')->select('slug')->where('slug', 'LIKE', "%{$slug}%")->orderBy('id', 'DESC')->first();

        if ($q) {
            $slnum = str_replace($slug, '', $q->slug);
            $slnum = ($slnum !== '') ? str_replace('-', '', $slnum) : 0;
            return $slnum;
        }
        return 0;
    }
}

/* End of file Posts.class.php */
/* Location: ./inc/lib/Posts.class.php */
