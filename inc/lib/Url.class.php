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
                    $lang = !empty($lang) ? $lang.'/' : '';
                    $url = Site::$url.$inFold.$lang.self::slug($vars).GX_URL_PREFIX;
                } else {
                    $url = Site::$url.$inFold.self::slug($vars).GX_URL_PREFIX;
                }

                break;

            default:
                if (Options::v('multilang_enable') === 'on') {
                    $lang = Language::isActive();
                    $lang = !empty($lang) ? '&lang='.$lang : '';
                    $url = Site::$url."?post={$vars}{$lang}";
                } else {
                    $url = Site::$url."?post={$vars}";
                }
                break;
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
                    $lang = !empty($lang) ? $lang.'/' : '';
                    $url = Site::$url.$inFold.$lang.self::slug($vars).GX_URL_PREFIX;
                } else {
                    $url = Site::$url.$inFold.self::slug($vars).GX_URL_PREFIX;
                }
                break;

            default:
                if (Options::v('multilang_enable') === 'on') {
                    $lang = Language::isActive();
                    $lang = !empty($lang) ? '&lang='.$lang : '';
                    $url = Site::$url."?page={$vars}{$lang}";
                } else {
                    $url = Site::$url."?page={$vars}";
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
                $url = Site::$url.$inFold.'category/'.$vars.'/'.Typo::slugify(Categories::name($vars)).'/';
                break;

            default:
                $url = Site::$url."?cat={$vars}";
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
                $var = isset($var) ? '/'.$var: '';
                $url = Site::$url.$inFold.'sitemap'.$var.'.xml';
                break;

            default:
                $url = Site::$url.'index.php?page=sitemap';
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
                $url = Site::$url.$inFold.'rss'.GX_URL_PREFIX;
                break;

            default:
                $url = Site::$url.'index.php?rss';
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
                $lang = '?lang='.$vars;
                if (isset($_GET['lang'])) {
                    $uri = explode('?', $_SERVER['REQUEST_URI']);
                    $uri = $uri[0];
                } else {
                    $uri = $_SERVER['REQUEST_URI'];
                }
                $url = $uri.$lang;

                break;

            default:
                // print_r($_GET);
                if (!empty($_GET)) {
                    $val = '';
                    foreach ($_GET as $key => $value) {
                        if ($key == 'lang') {
                            $val .= '&lang='.$vars;
                        } else {
                            $val .= $key.'='.$value;
                        }
                    }
                } else {
                    $val = 'lang='.$vars;
                }
                $lang = !isset($_GET['lang']) ? '&lang='.$vars : $val;
                $url = Site::$url.'?'.$lang;
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
                $url = Site::$url.$inFold.'ajax/'.$vars.'/'.TOKEN;
                break;

            default:
                $url = Site::$url."?ajax={$vars}&token=".TOKEN;
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
                $url = Site::$url.$inFold.'tag/'.Typo::slugify($vars).'/';
                break;

            default:
                $url = Site::$url."?tag={$vars}";
                break;
        }

        return $url;
    }

    public static function mod($vars)
    {
        switch (SMART_URL) {
            case true:
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url.$inFold.'mod/'.$vars.GX_URL_PREFIX;
                break;

            default:
                $url = Site::$url."?mod={$vars}";
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
                $type = ($type != '') ? 'type/'.$type.'/' : '';
                $size = ($size != '') ? 'size/'.$size.'/' : '';
                $align = ($align != '') ? 'align/'.$align.'/' : '';

                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$cdn.$inFold.'thumb/'.$type.$size.$align.$vars;
                break;

            default:
                $url = Site::$cdn."?thumb={$vars}&type={$type}&size={$size}&align={$align}";
                break;
        }

        $url = str_replace(':/', '://', str_replace('//', '/', $url));
        return $url;
    }

    public static function theme()
    {
        $theme = Options::v('themes');

        return Site::$cdn.'inc/themes/'.$theme.'/';
    }

    public static function author($vars, $type='')
    {
        switch (SMART_URL) {
            case true:
                $type = ($type != '') ? $type.'/': '';
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url.$inFold.'author/'.$vars.'/'.$type;
                break;

            default:
                $type = ($type != '') ? '&type='.$type: '';
                $url = Site::$url."?author={$vars}$type";
                break;
        }

        return $url;
    }

    public static function search() {
        switch (SMART_URL) {
            case true:
                # code...
                $url = Site::$url."search/";
                break;
            
            default:
                # code...
                $url = Site::$url."index.php?search";
                break;

        }

        return $url;
    }


    public static function archive($month, $year)
    {
        switch (SMART_URL) {
            case true:
                $month = strlen($month) == 1 ? (string) "0".$month: $month;
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url.$inFold.$year.'/'.$month."/";
                break;

            default:
                $url = Site::$url."?archive&month={$month}&year={$year}";
                break;
        }

        return $url;
    }

    public static function login($var = '')
    {
        switch (SMART_URL) {
            case true:
                $param = ($var != '') ? "?".$var: "";
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $url = Site::$url.$inFold.'login/'.$param;
                break;

            default:
                $param = ($var != '') ? "&".$var: "";
                $url = Site::$url."?login".$param;
                break;
        }

        return $url;
    }

    public static function breadcrumbs($data)
    {
        $out = '<nav aria-label="breadcrumb">
                  <ol class="breadcrumb">';
        $out .= '<li class="breadcrumb-item"><a href="'.Site::$url.'">' . _('Home') . '</a></li>';

        if (isset($data['p_type'])) {
            if ($data['p_type'] == 'page' && isset($data['posts'][0]->title)) {
                $out .= '<li class="breadcrumb-item active" aria-current="page">'.$data['posts'][0]->title.'</li>';
            } elseif ($data['p_type'] == 'post' && isset($data['posts'][0]->title)) {
                $cat_id = isset($data['posts'][0]->cat) ? $data['posts'][0]->cat : 0;
                if ($cat_id > 0) {
                    $out .= '<li class="breadcrumb-item"><a href="'.self::cat($cat_id).'">'.Categories::name($cat_id).'</a></li>';
                }
                $out .= '<li class="breadcrumb-item active" aria-current="page">'.$data['posts'][0]->title.'</li>';
            } elseif ($data['p_type'] == 'cat' && isset($data['cat'])) {
                $out .= '<li class="breadcrumb-item active" aria-current="page">'.Categories::name($data['cat']).'</li>';
            }
        }

        $out .= '</ol></nav>';

        return $out;
    }
}

/* End of file Url.class.php */
/* Location: ./inc/lib/Url.class.php */
