<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150125
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class Stats
{
    public function __construct() {
    }

    public static function totalPost($vars) {
        $posts = Db::result("SELECT `id` FROM `posts` WHERE `type` = '{$vars}'");
        $npost = Db::$num_rows;
        return $npost;
    }
	
	public static function totalCat($vars) {
        $posts = Db::result("SELECT `id` FROM `cat`");
        $npost = Db::$num_rows;
        return $npost;
    }
	
	public static function totalUser($vars) {
        $posts = Db::result("SELECT `id` FROM `user`");
        $npost = Db::$num_rows;
        return $npost;
    }

}

/* End of file Stats.class.php */
/* Location: ./inc/lib/Stats.class.php */