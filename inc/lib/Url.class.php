<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140930
 *
 * @version 2.0.1
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

/**
 * Url Class.
 *
 * This class will create all the URL format automatically for Posts, Categories,
 * pages, sitemap, rss.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 *
 * @since 0.0.1
 */
class Url
{
    public function __construct()
    {
    }

    /**
     * Post URL Function.
     *
     * This will create the posts url automatically based on
     * the SMART_URL will formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
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
     * Page URL Function.
     *
     * This will create the pages url automatically based on the SMART_URL will
     * formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
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
     * Categories URL Function.
     *
     * This will create the categories url automatically based on the SMART_URL
     * will formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
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
     * Custom URL Function.
     *
     * This will create the custom url. It will result as is.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function custom($vars)
    {
        $url = $vars;

        return $url;
    }

    /**
     * Sitemap URL Function.
     *
     * This will create the sitemap url automatically based on the SMART_URL will
     * formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
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
     * RSS URL Function.
     *
     * This will create the rss url automatically based on the SMART_URL will
     * formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
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
     * URL Slug Function.
     *
     * This will load the url slug from the database according to the posts id.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 0.0.1
     */
    public static function slug($vars)
    {
        $q = Query::table('posts')->select('slug')->where('id', Typo::int($vars))->first();
        $s = $q ? $q->slug : '';

        return $s;
    }

    /**
     * FLag URL Function.
     *
     * This will create the flag url automatically based on the SMART_URL will
     * formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
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
     * Ajax URL Function.
     *
     * This will create the ajax url automatically based on the SMART_URL will
     * formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
     * @since 1.0.0
     */
    public static function ajax($vars)
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url . $inFold . 'ajax/' . $vars . '/' . TOKEN;
                break;

            default:
                $url = Site::$url . "?ajax={$vars}&token=" . TOKEN;
                break;
        }

        return $url;
    }

    /**
     * API URL Function.
     *
     * This will create the API url automatically based on the SMART_URL configuration.
     *
     * @param string $resource e.g., 'marketplace'
     * @param string $identifier e.g., 'validate-license' (optional)
     * @param array $params Additional GET parameters (optional)
     *
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
     * Tag URL Function.
     *
     * This will create the tags url automatically based on the SMART_URL will
     * formatted as friendly url if SMART_URL is set to true.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     * @author GenixCMS <genixcms@gmail.com>
     *
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
                $url = Site::$cdn . "?thumb={$vars}&type={$type}&size={$size}&align={$align}";
                break;
        }

        $url = str_replace(':/', '://', str_replace('//', '/', $url));
        return $url;
    }

    public static function theme()
    {
        $theme = Options::v('themes');

        return Site::$cdn . 'inc/themes/' . $theme . '/';
    }

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
