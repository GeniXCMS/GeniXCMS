<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.0.0 build date 20160827
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

class Pages
{
    public function __construct()
    {
    }

    public static function isPage()
    {
        global $data;
        if ($data['p_type'] == 'page') {
            return true;
        } else {
            return false;
        }
    }

    public static function isPost()
    {
        global $data;
        if ($data['p_type'] == 'post') {
            return true;
        } else {
            return false;
        }
    }

    public static function isCat()
    {
        global $data;
        if ($data['p_type'] == 'cat') {
            return true;
        } else {
            return false;
        }
    }

    public static function isTag()
    {
        global $data;
        if ($data['p_type'] == 'tag') {
            return true;
        } else {
            return false;
        }
    }

    public static function isHome()
    {
        global $data;
        if ($data['p_type'] == 'index') {
            return true;
        } else {
            return false;
        }
    }

    public static function isMod()
    {
        global $data;
        if ($data['p_type'] == 'mod') {
            return true;
        } else {
            return false;
        }
    }

    public static function checkUriPath()
    {
    }
}
