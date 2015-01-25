<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Stats.class.php
* version : 0.0.1 pre
* build : 20150125
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

}

/* End of file Stats.class.php */
/* Location: ./inc/lib/Stats.class.php */