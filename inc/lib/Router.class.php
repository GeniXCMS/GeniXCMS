<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
*
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.7 build date 20150711
* @version 0.0.8
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2016 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
 * Router Class
 *
 * This class is for routing the smart url path
 *
 * @author Puguh Wijayanto (www.metalgenix.com)
 * @since 0.0.7
 */
class Router
{

    private static $_route;

    public function __construct() {

        self::map();

    }

    /**
     * Router variable
     *
     * $router = array (
     *      '/url/' => 'control'
     * )
     */
    public static function map(){

        self::$_route = array (
            '/category/([0-9]+)/(.*)/paging/([0-9]+)' => array('cat' => '1', 'paging' => '3'),
            '/category/([0-9]+)/(.*)' => array('cat' => '1'),
            '/(.+)/(.+)'.GX_URL_PREFIX => array('page' => '2', 'lang' => '1'),
            '/(.+)'.GX_URL_PREFIX => array('page' => '1'),
            '/paging/([0-9]+)' => array('default', 'paging' => '1'),
            '/error/([0-9]+)' => array('error' => '1'),
            '/(.+)/(.*)/([0-9]+)' => array('post' => '3', 'lang' => '1'),
            '/(.*)/([0-9]+)' => array('post' => '2'),
            '/ajax/(.*)' => array('ajax' => '1'),
            '/error' => array('error'),
            '/' => array('default'),
        );

        return self::$_route;

    }

    /**
     * Add router route
     *
     * @param arr $var
     */
    public static function add($var) {

        $route = self::$_route;

        self::$_route = array_merge($route, $var);

        return self::$_route;

    }

    /**
     * Run the route
     *
     * @return array
     */
    public static function run () {

        $m = self::match();
        // print_r($m);
        if (is_array($m)) {
            # code...

            $val = self::extract($m[0], $m[1]);

            if (isset($val) && $val != null ) {

                return $val;

            }else{
                $val = ['error'];
                return $val;

            }

        }else{

            $val = ['error'];
            return $val;

        }
    }

    /**
     * Check if the requested uri match with available router map
     *
     * @return array
     */
    public static function match () {
        $uri = self::getURI();

        foreach (self::$_route as $k => $v) {

            $regx = str_replace('/','\/', $k);

            if ( preg_match('/^'.$regx.'$/Usi', $uri, $m) ) {
                $result = [$v,$m];
                return $result;

            }

        }
    }

    /**
     * Extract the router variable
     *
     * @param arr $var
     * @param arr $m
     * @return type
     */
    public static function extract ($var, $m) {

        foreach ($var as $k2 => $v2) {

            if ($k2 != '0') {

                $va[] = [$k2 => $m[$v2]];

            }elseif($k2 == ''){

                $va = ['default'];

            }else{

                $va = array($k2) ;

            }
        }

        return $va;

    }

    /**
     * Get the requested smart URI
     *
     * @return string
     */
    public static function getURI () {
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


        }else{

            $uri2 = explode('/', $uri[0]);
            unset($uri2[0]);
            $uri = implode('/', $uri2);

        }

        return '/' . trim($uri, '/');
    }

    /**
     * Check if it's in folder
     */
    public static function inFolder() {

        $uri = explode('/', Site::$url);

        if(count($uri) > 3) {

            return true;

        }else{

            return false;

        }

    }

    /**
     * Scrap the parameter into the array of data
     *
     * @param array $param
     * @return array
     */
    public static function scrap($param) {
        if ($param != '') {

            foreach ($param as $k => $v) {

                if (is_array($v)) {

                    foreach ($v as $k2 => $v2) {

                        $data[$k2] = $v2;

                    }

                }else{

                    $data = '';

                }

            }

        } else {

            $data = '';

        }

        return $data;
    }

    public static function stripFolder($req_uri) {
        $uri = Site::$url;
        $folder = self::getFolder();

        $uri2 = str_replace($folder, "", $req_uri);
        // print_r($uri2);
        return $uri2;
    }

    public static function getFolder() {
        $uri = explode('/', Site::$url);

        if(count($uri) > 3) {

            unset($uri[0]);
            unset($uri[1]);
            unset($uri[2]);

            $uri = array_values($uri);

            $uris = "";
            for($i=0; $i<count($uri); $i++){
                $uris .= "/".$uri[$i];
            }
            return $uris;

        }else{

            return "/";

        }
    }
}
