<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150125
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
class Stats
{
    public function __construct()
    {
    }

    public static function totalPost($vars)
    {
        $type = Typo::cleanX($vars);
        $posts = Db::result("SELECT `id` FROM `posts` WHERE `type` = '{$type}'");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function activePost($vars)
    {
        $type = Typo::cleanX($vars);
        $posts = Db::result("SELECT `id` FROM `posts` WHERE `type` = '{$type}' AND `status` = '1' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function inactivePost($vars)
    {
        $type = Typo::cleanX($vars);
        $posts = Db::result("SELECT `id` FROM `posts` WHERE `type` = '{$type}' AND `status` = '0' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function totalCat($vars)
    {
        $type = Typo::cleanX($vars);
        $posts = Db::result("SELECT `id` FROM `cat` WHERE `type` = '{$type}'");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function totalUser()
    {
        $posts = Db::result("SELECT `id` FROM `user` WHERE `group` > '0' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function totalAdmin()
    {
        $posts = Db::result("SELECT `id` FROM `user` WHERE `group` = '0' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function activeUser()
    {
        $posts = Db::result("SELECT `id` FROM `user` WHERE `group` > '0' AND `status` = '1' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function inactiveUser()
    {
        $posts = Db::result("SELECT `id` FROM `user` WHERE `group` > '0' AND `status` = '0' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function pendingUser()
    {
        $posts = Db::result("SELECT `id` FROM `user` WHERE `group` > '0' AND `status` = '0' AND `activation` != '' ");
        $npost = Db::$num_rows;

        return $npost;
    }

    public static function mostViewed($count, $type = 'post')
    {
        $count = Typo::int($count);
        return Db::result(sprintf("SELECT * FROM `posts` WHERE `type` = '{$type}' ORDER BY `views` DESC LIMIT 0,%d", $count));
    }

    public static function addViews($id)
    {
        $botlist = self::botList();
        $nom = 0;
        foreach ($botlist as $bot) {
            if (preg_match("/{$bot}/", $_SERVER['HTTP_USER_AGENT'])) {
                $nom = 1 + $nom;
            } else {
                $nom = 0;
            }
        }
        if ($nom == 0) {
            $sql = "UPDATE `posts` SET `views` = `views`+1 WHERE `id` = '{$id}' LIMIT 1";
            $q = Db::query($sql);
        }
    }

    public static function botList()
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

    public static function pendingComments()
    {
        $sql = sprintf("SELECT * FROM `comments` WHERE `status` = '2'");
        Db::result($sql);

        return Db::$num_rows;
    }

    public static function totalComments()
    {
        $sql = sprintf('SELECT * FROM `comments`');
        Db::result($sql);

        return Db::$num_rows;
    }

    public static function activeComments()
    {
        $sql = sprintf("SELECT * FROM `comments` WHERE `status` = '1'");
        Db::result($sql);

        return Db::$num_rows;
    }

    public static function inactiveComments()
    {
        $sql = sprintf("SELECT * FROM `comments` WHERE `status` = '0'");
        Db::result($sql);

        return Db::$num_rows;
    }
}

/* End of file Stats.class.php */
/* Location: ./inc/lib/Stats.class.php */
