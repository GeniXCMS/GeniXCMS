<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20150125
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Stats
{
    public function __construct()
    {
    }

    public static function totalPost($vars)
    {
        return Query::table('posts')->where('type', $vars)->count();
    }

    public static function activePost($vars)
    {
        return Query::table('posts')->where('type', $vars)->where('status', '1')->count();
    }

    public static function inactivePost($vars)
    {
        return Query::table('posts')->where('type', $vars)->where('status', '0')->count();
    }

    public static function totalCat($vars)
    {
        return Query::table('cat')->where('type', $vars)->count();
    }

    public static function totalUser()
    {
        return Query::table('user')->where('group', '>', '0')->count();
    }

    public static function totalAdmin()
    {
        return Query::table('user')->where('group', '0')->count();
    }

    public static function activeUser()
    {
        return Query::table('user')->where('group', '>', '0')->where('status', '1')->count();
    }

    public static function inactiveUser()
    {
        return Query::table('user')->where('group', '>', '0')->where('status', '0')->count();
    }

    public static function pendingUser()
    {
        return Query::table('user')
            ->where('group', '>', '0')
            ->where('status', '0')
            ->where('activation', '!=', '')
            ->count();
    }

    public static function mostViewed($count, $type = 'post')
    {
        return Query::table('posts')
            ->where('type', $type)
            ->orderBy('views', 'DESC')
            ->limit($count)
            ->get();
    }

    public static function addViews($id)
    {
        $botlist = self::botList();
        $isBot = false;
        foreach ($botlist as $bot) {
            if (preg_match("/{$bot}/", $_SERVER['HTTP_USER_AGENT'])) {
                $isBot = true;
                break;
            }
        }
        if (!$isBot) {
            $sql = "UPDATE `posts` SET `views` = `views`+1 WHERE `id` = ?";
            Db::query($sql, [$id]);
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
        return Query::table('comments')->where('status', '2')->count();
    }

    public static function totalComments()
    {
        return Query::table('comments')->count();
    }

    public static function activeComments()
    {
        return Query::table('comments')->where('status', '1')->count();
    }

    public static function inactiveComments()
    {
        return Query::table('comments')->where('status', '0')->count();
    }
}
