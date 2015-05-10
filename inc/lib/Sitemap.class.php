<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141005
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class Sitemap
{
    public function __construct() {

    }


    public static function create($url="post", $type='post', $class='Url'){
        $posts = Posts::recent(20,$type);
        header("Content-Type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .= "
            <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
            ";
            //print_r($posts);
        if (!isset($posts['error'])) {
                # code...
            
            foreach ($posts as $p) {
                # code...
                $xml .= "
                    <url>
                        <loc>".$class::$url($p->id)."</loc>
                    </url>
                    ";
            }
        }
        
        $xml .= "
            </urlset>
                ";
        echo $xml;
    }
}

/* End of file Sitemap.class.php */
/* Location: ./inc/lib/Sitemap.class.php */