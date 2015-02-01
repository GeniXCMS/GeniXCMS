<?php

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
                    <title>".Options::get('sitename')."</title>
                    <link>".Options::get('siteurl')."</link>
                    <description>".Options::get('sitedesc')."</description>
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