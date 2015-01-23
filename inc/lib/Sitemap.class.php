<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
* filename : Sitemap.class.php
* version : 0.0.1 pre
* build : 20141005
*/

class Sitemap
{
    public function __construct() {

    }


    public static function create($url="post", $type='post'){
        $posts = Posts::recent(20,$type);
        header("Content-Type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .= "
            <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">
            ";
        foreach ($posts as $p) {
            # code...
            $xml .= "
                <url>
                    <loc>".Url::$url($p->id)."</loc>
                </url>
                ";
        }
        
        $xml .= "
            </urlset>
                ";
        echo $xml;
    }
}

/* End of file Sitemap.class.php */
/* Location: ./inc/lib/Sitemap.class.php */