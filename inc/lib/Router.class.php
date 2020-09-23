<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.7 build date 20150711
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Router Class.
 *
 * This class is for routing the smart url path
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.7
 */
class Router
{
    public static $_route;

    public function __construct()
    {
        self::map();
    }

    /**
     * Router variable.
     *
     * $router = array (
     *      '/url/' => 'control'
     * )
     */
    public static function map()
    {
        self::$_route = array(
            'thumb/type/(.*)/size/([0-9]+)/align/(.*)/(.*)' => array('thumb' => 4, 'type' => 1, 'size' => 2, 'align' => 3),
            'thumb/type/(.*)/size/([0-9]+)/(.*)' => array('thumb' => 3, 'type' => 1, 'size' => 2),
            'thumb/size/([0-9]+)/align/(.*)/(.*)' => array('thumb' => 3, 'size' => 1, 'align' => 2),
            'category/([0-9]+)/(.*)/paging/([0-9]+)/' => array('cat' => 1, 'paging' => 3),
            'category/([0-9]+)/(.*)/' => array('cat' => 1),
            'tag/(.*)/paging/([0-9]+)/' => array('tag' => 1, 'paging' => 2),
            'tag/(.*)/' => array('tag' => 1),
            '([a-z]{2})/mod/(.*)'.GX_URL_PREFIX => array('mod' => 2, 'lang' => 1),
            // '/(.[a-z]+)/(.+)'.GX_URL_PREFIX => array('page' => 2, 'lang' => 1),
            'mod/(.*)'.GX_URL_PREFIX => array('mod' => 1),
            // '/(.+)'.GX_URL_PREFIX => array('page' => 1),
            'paging/([0-9]+)/' => array('default', 'paging' => 1),
            'error/([0-9]+)/' => array('error' => 1),
            '([a-z]{2})/(.*)'.GX_URL_PREFIX => array('post' => 2, 'lang' => 1),
            '(.*)'.GX_URL_PREFIX => array('post' => 1),
            'ajax/(.*)/(.*)' => array('ajax' => 1, 'token' => 2),
            'thumb/size/([0-9]+)/(.*)' => array('thumb' => 2, 'size' => 1),
            'thumb/type/(.*)/(.*)' => array('thumb' => 2, 'type' => 1),
            'thumb/align/(.*)/(.*)' => array('thumb' => 2, 'align' => 1),
            'thumb/(.*)' => array('thumb' => 1),
            'author/(.*)/(.*)/paging/([0-9]+)/' => array('author' => 1, 'type' => 2, 'paging' => 3),
            'author/(.*)/paging/([0-9]+)/' => array('author' => 1, 'paging' => 2),
            'author/(.*)/(.*)/' => array('author' => 1, 'type' => 2),
            'author/(.*)/' => array('author' => 1),
            'sitemap.xml' => array('sitemap'),
            'sitemap/(.*).xml' => array('sitemap' => 1),
            'sitemap/' => array('sitemap'),
            'search/' => array('search'),
            'error/' => array('error'),
            'rss/' => array('rss'),
            '/' => array('default'),
        );

        return self::$_route;
    }

    /**
     * Add router route.
     *
     * @param array $var
     */
    public static function add($var)
    {
        $route = self::$_route;

        self::$_route = array_merge($var, $route);
        // new Router();
        $keys = array_map('strlen', array_keys(self::$_route));
        array_multisort($keys, SORT_DESC, self::$_route);
//         print_r(self::$_route);
        return self::$_route;
    }

    /**
     * Run the route.
     *
     * @return array
     */
    public static function run()
    {
        $m = self::match();
//         print_r($m);
        if (is_array($m)) {
            $val = self::extract($m[0], $m[1]);
//             print_r($val);
            if (isset($val) && $val != null) {
                return $val;
            } else {
                $val['error'] = '';

                return $val;
            }
        } else {
            $val['error'] = '';

            return $val;
        }
    }

    /**
     * Check if the requested uri match with available router map.
     *
     * @return array
     */
    public static function match()
    {
        $uri = self::getURI();

        if (array_key_exists($uri, self::$_route)) {
            $result = [self::$_route[$uri], $uri];

            return $result;
        } else {
            foreach (self::$_route as $k => $v) {
                $regx = str_replace('/', '\/', $k);
                if (preg_match('/^'.$regx.'$/Us', $uri, $m)) {
                    $result = [$v, $m];

                    return $result;
                }
            }
        }
    }

    /**
     * Extract the router variable.
     *
     * @param array $var
     * @param array $m
     *
     * @return array
     */
    public static function extract($var, $m)
    {
        $va = array();
        foreach ($var as $k2 => $v2) {
            if (!is_int($k2)) {
                if( !is_int($v2) ) {
                    $va[] = [$k2 => $v2];
                }
                if (is_int($v2)) {
                    $va[] = [$k2 => $m[$v2]];
                } 
            } elseif (is_int($k2)) {
                $va[] = [$v2];
            } else {
                $va = array($v2);
            }
        }

        return $va;
    }

    /**
     * Get the requested smart URI.
     *
     * @return string
     */
    public static function getURI()
    {
        $uri = $_SERVER['REQUEST_URI'];
        // echo $uri;
        // strip any $_REQUEST variable
        $uri = explode('?', $uri);

        if (count($uri) > 0) {
            unset($uri[1]);
        }
        // print_r($uri[0]);
        if (self::inFolder()) {
            $uri = self::stripFolder($uri[0]);
            // echo $uri;
        } else {
            $uri2 = explode('/', $uri[0]);
            unset($uri2[0]);
            $uri = implode('/', $uri2);
            // echo $uri;
        }
        // echo $uri;

        $uri = (Options::v('permalink_use_index_php') == 'on') ?
            str_replace('index.php/', '', $uri) : $uri;
        // echo $uri;

        return empty($uri) ? '/' : $uri; // '/'.trim($uri, '/');
    }

    /**
     * Check if it's in folder.
     */
    public static function inFolder()
    {
        $uri = explode('/', Site::$url);
        // print_r($uri);
        if (count($uri) > 4) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Scrap the parameter into the array of data.
     *
     * @param array $param
     *
     * @return array
     */
    public static function scrap($param)
    {
        if ($param != '') {
            foreach ($param as $k => $v) {
                if (is_array($v)) {
                    // print_r($v);
                    foreach ($v as $k2 => $v2) {
                        $data[$k2] = $v2;
                    }
                    // print_r($data);
                } else {
                    $data = [$v];
                }
            }
        } else {
            $data = [];
        }

        return $data;
    }

    public static function stripFolder($req_uri)
    {
        $uri = Site::$url;
        $folder = self::getFolder();

        $uri2 = str_replace($folder, '', $req_uri);
        // print_r($uri2);
        return $uri2;
    }

    public static function getFolder()
    {
        $uri = explode('/', Site::$url);

        if (count($uri) > 4) {
            unset($uri[0]);
            unset($uri[1]);
            unset($uri[2]);

            $uri = array_values($uri);

            $uris = '';
            for ($i = 0; $i < count($uri); ++$i) {
                $uris .= '/'.$uri[$i];
            }

            return $uris;
        } else {
            return '/';
        }
    }
}
