<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150125
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Stats
{
    public function __construct()
    {
    }

    public static function totalPost($vars)
    {
        $posts = Db::result("SELECT `id` FROM `posts` WHERE `type` = '{$vars}'");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function totalCat($vars)
    {
        $posts = Db::result("SELECT `id` FROM `cat` WHERE `type` = '{$vars}'");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function totalUser()
    {
        $posts = Db::result("SELECT `id` FROM `user` WHERE `group` > '0' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function addViews($id)
    {
        $botlist = self::botlist();
        $nom = 0;
        foreach ($botlist as $bot) {
            if (preg_match("/{$bot}/", $_SERVER['HTTP_USER_AGENT'])) {
                $nom = 1 + $nom;
            } else {
                $nom = 0;
            }
        }
        if ($nom == 0) {
            # code...
            $sql = "UPDATE `posts` SET `views` = `views`+1 WHERE `id` = '{$id}' LIMIT 1";
            $q = Db::query($sql);
        }
    }

    public static function botlist()
    {
        $botlist = array(
                'Teoma',
                'alexa',
                'froogle',
                'inktomi',
                'looksmart',
                'URL_Spider_SQL',
                'Firefly',
                'NationalDirectory',
                'Ask Jeeves',
                'TECNOSEEK',
                'InfoSeek',
                'WebFindBot',
                'girafabot',
                'crawler',
                'www.galaxy.com',
                'Googlebot',
                'Scooter',
                'Slurp',
                'appie',
                'FAST',
                'WebBug',
                'Spade',
                'ZyBorg',
                'rabaz',
                'Twitterbot',
                'MJ12bot',
                'AhrefsBot',
                'bingbot',
                'YandexBot',
                'spbot',
                        );

        return $botlist;
    }
}

/* End of file Stats.class.php */
/* Location: ./inc/lib/Stats.class.php */
