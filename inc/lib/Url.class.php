<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140930
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Url Class.
 *
 * This class will create all the URL format automatically for Posts, Categories,
 * pages, sitemap, rss.
 *
 * @since 0.0.1
 */
class Url
{
    /**
     * Url Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Generates a URL for a post.
     *
     * Formats the URL as friendly (Smart URL) if enabled, otherwise uses a query string.
     * Supports multilingual pathing and applies the 'post_url' filter.
     *
     * @param int|string $vars Total ID of the post.
     * @return string The generated Post URL.
     * @since 0.0.1
     */
    public static function post($vars)
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                if (Options::v('multilang_enable') === 'on') {
                    $lang = Language::isActive();
                    $lang = !empty($lang) ? $lang . '/' : '';
                    $url = Site::$url . $inFold . $lang . self::slug($vars) . GX_URL_PREFIX;
                } else {
                    $url = Site::$url . $inFold . self::slug($vars) . GX_URL_PREFIX;
                }

                break;

            default:
                if (Options::v('multilang_enable') === 'on') {
                    $lang = Language::isActive();
                    $lang = !empty($lang) ? '&lang=' . $lang : '';
                    $url = Site::$url . "?post={$vars}{$lang}";
                } else {
                    $url = Site::$url . "?post={$vars}";
                }
                break;
        }

        $hookData = [
            'url' => $url,
            'id' => $vars
        ];

        $filtered = Hooks::filter('post_url', $hookData);
        if (is_array($filtered) && isset($filtered['url'])) {
            $url = $filtered['url'];
        }

        return $url;
    }

    /**
     * Generates a URL for a page.
     *
     * Formats the URL based on SMART_URL setting. Supports multilingual paths.
     *
     * @param int|string $vars Total ID or slug of the page.
     * @return string The generated Page URL.
     * @since 0.0.1
     */
    public static function page($vars)
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                if (Options::v('multilang_enable') === 'on') {
                    $lang = Language::isActive();
                    $lang = !empty($lang) ? $lang . '/' : '';
                    $url = Site::$url . $inFold . $lang . self::slug($vars) . GX_URL_PREFIX;
                } else {
                    $url = Site::$url . $inFold . self::slug($vars) . GX_URL_PREFIX;
                }
                break;

            default:
                if (Options::v('multilang_enable') === 'on') {
                    $lang = Language::isActive();
                    $lang = !empty($lang) ? '&lang=' . $lang : '';
                    $url = Site::$url . "?page={$vars}{$lang}";
                } else {
                    $url = Site::$url . "?page={$vars}";
                }

                break;
        }

        return $url;
    }

    /**
     * Generates a URL for a category.
     *
     * @param int|string $vars Category ID.
     * @return string The generated Category URL.
     * @since 0.0.1
     */
    public static function cat($vars)
    {
        if (is_array($vars)) {
            error_log("DEBUG: Url::cat received an ARRAY: " . json_encode($vars));
            $vars = $vars[0] ?? 0;
        }
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'category/' . $vars . '/' . Typo::slugify(Categories::name($vars)) . '/';
                break;

            default:
                $url = Site::$url . "?cat={$vars}";
                break;
        }

        return $url;
    }

    /**
     * Returns a custom URL as provided.
     *
     * @param string $vars The raw URL.
     * @return string
     * @since 0.0.1
     */
    public static function custom($vars)
    {
        $url = $vars;

        return $url;
    }

    /**
     * Generates a URL for the sitemap.
     *
     * @param string|null $var Specific sitemap section.
     * @return string
     * @since 0.0.1
     */
    public static function sitemap($var)
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $var = isset($var) ? '/' . $var : '';
                $url = Site::$url . $inFold . 'sitemap' . $var . '.xml';
                break;

            default:
                $url = Site::$url . 'index.php?page=sitemap';
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for the RSS feed.
     *
     * @return string
     * @since 0.0.1
     */
    public static function rss()
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'rss' . GX_URL_PREFIX;
                break;

            default:
                $url = Site::$url . '?rss';
                break;
        }

        return $url;
    }

    /**
     * Retrieves the URL slug for a post ID.
     *
     * @param int $vars Post ID.
     * @return string The slug string.
     * @since 0.0.1
     */
    public static function slug($vars)
    {
        $q = Query::table('posts')->select('slug')->where('id', Typo::int($vars))->first();
        $s = $q ? $q->slug : '';

        return $s;
    }

    /**
     * Generates a URL for switching languages.
     *
     * @param string $vars Language code.
     * @return string
     * @since 0.0.7
     */
    public static function flag($vars)
    {
        switch (SMART_URL) {
            case true:
                $lang = '?lang=' . $vars;
                if (isset($_GET['lang'])) {
                    $uri = explode('?', $_SERVER['REQUEST_URI']);
                    $uri = $uri[0];
                } else {
                    $uri = $_SERVER['REQUEST_URI'];
                }
                $url = $uri . $lang;

                break;

            default:
                // print_r($_GET);
                if (!empty($_GET)) {
                    $val = '';
                    foreach ($_GET as $key => $value) {
                        if ($key == 'lang') {
                            $val .= '&lang=' . $vars;
                        } else {
                            $val .= $key . '=' . $value;
                        }
                    }
                } else {
                    $val = 'lang=' . $vars;
                }
                $lang = !isset($_GET['lang']) ? '&lang=' . $vars : $val;
                $url = Site::$url . '?' . $lang;
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for Internal Ajax calls.
     * Includes the security TOKEN automatically.
     *
     * @param string $vars Ajax method/resource.
     * @param array $params Additional query parameters.
     * @return string
     * @since 1.0.0
     */
    public static function ajax($vars, $params = [])
    {
        $queryString = '';
        if (!empty($params)) {
            $queryString = '?' . http_build_query($params);
        }

        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'ajax/' . $vars . '/' . TOKEN;
                if ($queryString !== '') {
                    $url .= $queryString;
                }
                break;

            default:
                $url = Site::$url . "?ajax={$vars}&token=" . TOKEN;
                if (!empty($params)) {
                    $url .= '&' . http_build_query($params);
                }
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for the RESTful API.
     *
     * @param string $resource API resource name.
     * @param string $identifier Specific resource ID.
     * @param array $params Additional query parameters.
     * @return string
     */
    public static function api($resource, $identifier = '', $params = [])
    {
        $queryString = '';
        if (!empty($params)) {
            $queryString = '?' . http_build_query($params);
        }

        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'api/v1/' . $resource;
                if ($identifier !== '') {
                    $url .= '/' . $identifier;
                }
                $url .= $queryString;
                break;

            default:
                $apiPath = $resource;
                if ($identifier !== '') {
                    $apiPath .= '/' . $identifier;
                }
                $url = Site::$url . "?api=" . $apiPath;
                if (!empty($params)) {
                    $url .= '&' . http_build_query($params);
                }
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for a tag.
     *
     * @param string $vars Tag name or ID.
     * @return string
     * @since 1.0.0
     */
    public static function tag($vars)
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'tag/' . Typo::slugify($vars) . '/';
                break;

            default:
                $url = Site::$url . "?tag={$vars}";
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for a module.
     *
     * @param string $vars Module name.
     * @param string $act Module action.
     * @param array $params Additional parameters.
     * @return string
     */
    public static function mod($vars, $act = '', $params = [])
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'mod/' . $vars . GX_URL_PREFIX;
                $p = [];
                if ($act != '') {
                    $p[] = "act={$act}";
                }
                if (!empty($params)) {
                    foreach ($params as $k => $v) {
                        $p[] = "{$k}={$v}";
                    }
                }
                if (!empty($p)) {
                    $url .= "?" . implode('&', $p);
                }
                break;

            default:
                $url = Site::$url . "?mod={$vars}";
                if ($act != '') {
                    $url .= "&act={$act}";
                }
                if (!empty($params)) {
                    foreach ($params as $k => $v) {
                        $url .= "&{$k}={$v}";
                    }
                }
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for a dynamic image thumbnail.
     *
     * Processes parameters like type, size, and alignment to generate a ThumbFly path.
     *
     * @param string $vars Image source path.
     * @param string $type Resize type (e.g., 'crop').
     * @param string $size Target dimensions (e.g., '300x300').
     * @param string $align Alignment for cropping.
     * @return string
     */
    public static function thumb($vars, $type = '', $size = '', $align = '')
    {
        // $vars = urlencode($vars);
        $vars = str_replace(Site::$url, '', $vars);
        $vars = str_replace(Site::$cdn, '', $vars);
        $vars = ltrim($vars, '/');
        $vars = str_replace('thumb/', '', $vars);

        switch (SMART_URL) {
            case true:
                $type = ($type != '') ? 'type/' . $type . '/' : '';
                $size = ($size != '') ? 'size/' . $size . '/' : '';
                $align = ($align != '') ? 'align/' . $align . '/' : '';

                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$cdn . $inFold . 'thumb/' . $type . $size . $align . $vars;
                break;

            default:
                $url = Site::$cdn . "?thumb={$vars}";
                if ($type != '')
                    $url .= "&type={$type}";
                if ($size != '')
                    $url .= "&size={$size}";
                if ($align != '')
                    $url .= "&align={$align}";
                break;
        }

        $url = str_replace(':/', '://', str_replace('//', '/', $url));
        return $url;
    }

    /**
     * Returns the base directory URL for the currently active theme.
     *
     * @return string
     */
    public static function theme()
    {
        $theme = Options::v('themes');

        return Site::$cdn . 'inc/themes/' . $theme . '/';
    }

    /**
     * Generates a URL for an author profile page.
     *
     * @param string $vars Author username or ID.
     * @param string $type Sub-view type.
     * @return string
     */
    public static function author($vars, $type = '')
    {
        switch (SMART_URL) {
            case true:
                $type = ($type != '') ? $type . '/' : '';
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'author/' . $vars . '/' . $type;
                break;

            default:
                $type = ($type != '') ? '&type=' . $type : '';
                $url = Site::$url . "?author={$vars}$type";
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for the search results page.
     *
     * @return string
     */
    public static function search()
    {
        switch (SMART_URL) {
            case true:
                # code...
                $url = Site::$url . "search/";
                break;

            default:
                # code...
                $url = Site::$url . "index.php?search";
                break;

        }

        return $url;
    }


    /**
     * Generates a URL for an archive page (Year/Month).
     *
     * @param int|string $month
     * @param int|string $year
     * @return string
     */
    public static function archive($month, $year)
    {
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . $year . '/' . $month . "/";
                break;

            default:
                $url = Site::$url . "?archive&month={$month}&year={$year}";
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for the login page.
     *
     * @param string $var Optional parameters.
     * @return string
     */
    public static function login($var = '')
    {
        switch (SMART_URL) {
            case true:
                $param = ($var != '') ? "?" . $var : "";
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'login/' . $param;
                break;

            default:
                $param = ($var != '') ? "&" . $var : "";
                $url = Site::$url . "?login" . $param;
                break;
        }

        return $url;
    }

    /**
     * Generates a URL for the logout page.
     *
     * @param string $var Optional parameters.
     * @return string
     */
    public static function logout($var = '')
    {
        switch (SMART_URL) {
            case true:
                $param = ($var != '') ? "?" . $var : "";
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'logout/' . $param;
                break;

            default:
                $param = ($var != '') ? "&" . $var : "";
                $url = Site::$url . "?logout" . $param;
                break;
        }

        return $url;
    }

    /**
     * Generates HTML breadcrumbs based on the current context or provided data.
     *
     * @param array $data Contextual data for manual injection.
     * @return string HTML output.
     */
    public static function breadcrumbs($data = [])
    {
        $out = '<nav aria-label="breadcrumb">
                  <ol class="flex list-none p-0 gap-2 items-center justify-center">';
        $out .= '<li><a href="' . Site::$url . '" class="no-underline hover:text-primary transition-colors">' . _('Home') . '</a></li>';

        $mod = $_GET['mod'] ?? ($data['mod'] ?? '');
        $post = $_GET['post'] ?? ($data['post'] ?? '');
        $page = $_GET['page'] ?? ($data['page'] ?? '');
        $cat = $_GET['cat'] ?? ($data['cat'] ?? '');
        $tag = $_GET['tag'] ?? ($data['tag'] ?? '');

        // Allow caller to inject post ID from routed posts array
        if (empty($post) && !empty($data['posts']) && is_array($data['posts'])) {
            $post = $data['posts'][0]->id ?? '';
        }

        $hookData = [
            'out' => $out,
            'added' => false,
            'mod' => $mod,
            'post' => $post,
            'page' => $page,
            'cat' => $cat,
            'tag' => $tag
        ];

        $filtered = Hooks::filter('breadcrumbs_filter', $hookData);

        if (isset($filtered['added']) && $filtered['added'] === true) {
            $out = $filtered['out'];
        } else {
            if (!empty($mod)) {
                $modTitle = Mod::getTitle($mod);
                $out .= '<li class="text-slate-400 opacity-50 text-xs font-black">/</li><li><span class="text-secondary font-black">' . $modTitle . '</span></li>';
            } elseif (!empty($post)) {
                $q = Query::table('posts')->where('id', Typo::int($post))->first();
                if ($q) {
                    if ($q->type == 'post' && $q->cat > 0) {
                        $out .= '<li class="text-slate-400 opacity-50 text-xs font-black">/</li><li><a href="' . self::cat($q->cat) . '" class="no-underline text-on-surface-variant hover:text-primary transition-colors text-truncate max-w-[200px] font-black">' . Categories::name($q->cat) . '</a></li>';
                    }
                    $out .= '<li class="text-slate-400 opacity-50 text-xs font-black">/</li><li><span class="text-secondary text-truncate max-w-[250px] font-black">' . $q->title . '</span></li>';
                }
            } elseif (!empty($page)) {
                $out .= '<li class="text-slate-400 opacity-50 text-xs font-black">/</li><li><span class="text-secondary font-black">' . Typo::cleanX($page) . '</span></li>';
            } elseif (!empty($cat)) {
                $out .= '<li class="text-slate-400 opacity-50 text-xs font-black">/</li><li><span class="text-secondary font-black">' . Categories::name($cat) . '</span></li>';
            } elseif (!empty($tag)) {
                $out .= '<li class="text-slate-400 opacity-50 text-xs font-black">/</li><li><span class="text-secondary font-black">' . _('Tag') . ': ' . Typo::cleanX($tag) . '</span></li>';
            }
        }

        $out .= '</ol></nav>';

        return $out;
    }
}

/* End of file Url.class.php */
/* Location: ./inc/lib/Url.class.php */
