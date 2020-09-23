<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141005
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link https://genix.id
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Sitemap
{
    public static $_map;

    public function __construct()
    {
        self::$_map = self::map();
    }

    public static function create( $type = 'post', $count = 20, $url = 'post', $class = 'Url', $cat = '')
    {
        $cat = ($cat != '') ? ['cat' => $cat] : '';
        $var = array(
            'num' => $count,
            'type' => $type
        );
        $var = is_array($cat) ? array_merge($var, $cat): $var;

        $posts = Posts::recent($var);
        header('Content-Type: text/xml');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            ';
        //print_r($posts);
        if (!isset($posts['error'])) {
            foreach ($posts as $p) {
                $xml .= '
                    <url>
                        <loc>'.$class::$url($p->id).'</loc>
                        <lastmod>'.date('Y-m-d').'</lastmod>
                        <changefreq>daily</changefreq>
                        <priority>1</priority>
                    </url>
                    ';
            }
        }

        $xml .= '
            </urlset>
                ';
        echo $xml;
    }

    public static function createIndex()
    {
        $sql = "SELECT * FROM `cat` WHERE `type` != 'tag'";
        $q = Db::result($sql);
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>
   <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($q as $k => $v) {
//            print_r($v);
            echo'
        <sitemap>
            <loc>'.Url::sitemap($v->slug).'</loc>
            <lastmod>'.date("Y-m-d").'</lastmod>
        </sitemap>
   ';
        }
        echo '</sitemapindex>';
    }

    public static function map()
    {
        $map = array(
            'post' => array(
                'class' => 'Url',
                'url' => 'post'
            )
        );

        return $map;
    }

    public static function addMap($map)
    {
        self::$_map = array_merge($map, self::$_map);

        return self::$_map;
    }
}

/* End of file Sitemap.class.php */
/* Location: ./inc/lib/Sitemap.class.php */
