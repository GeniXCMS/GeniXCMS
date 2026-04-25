<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 1.0.0 build date 20160827
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Pages extends Model
{
    protected $table = 'posts';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function content($id)
    {
        global $data;
        if (isset($data['p_type']) && $data['p_type'] == 'page') {
            return true;
        } else {
            return false;
        }
    }

    public static function isPage()
    {
        global $data;
        if (isset($data['p_type']) && $data['p_type'] == 'page') {
            return true;
        } else {
            return false;
        }
    }

    public static function isPost()
    {
        global $data;
        if (isset($data['p_type']) && $data['p_type'] == 'post') {
            return true;
        } else {
            return false;
        }
    }

    public static function isCat()
    {
        global $data;
        if (isset($data['p_type']) && $data['p_type'] == 'cat') {
            return true;
        } else {
            return false;
        }
    }

    public static function isTag()
    {
        global $data;
        if (isset($data['p_type']) && $data['p_type'] == 'tag') {
            return true;
        } else {
            return false;
        }
    }

    public static function isHome()
    {
        global $data;
        if (isset($data['p_type']) && $data['p_type'] == 'index') {
            return true;
        } else {
            return false;
        }
    }

    public static function isMod()
    {
        global $data;
        if (isset($data['p_type']) && $data['p_type'] == 'mod') {
            return true;
        } else {
            return false;
        }
    }

    public static function checkUriPath()
    {
    }
}
