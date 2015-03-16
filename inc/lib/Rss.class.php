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

class Rss
{
    function __construct () {

    }

    public static function create ($url = 'post', $type='post', $count='20') {
        $posts = Posts::recent($count,$type);
        header("Content-Type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .= "
            <rss version=\"2.0\">
                <channel>
                    <title>".Site::$name."</title>
                    <link>".Site::$url."</link>
                    <description>".Site::$desc."</description>
            ";
        foreach ($posts as $p) {
            # code...
            $xml .= "
                <item>
                    <title>".$p->title."</title>
                    <link>".Url::$url($p->id)."</link>
                    <description>".substr(strip_tags(Typo::Xclean($p->content)), 0, 260)."</description>
                </item>
                ";
        }
        
        $xml .= "
                </channel>
            </rss>
                ";
        echo $xml;
    }
}