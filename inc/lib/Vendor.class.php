<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.3 build date 20150322
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
class Vendor
{
    public function __construct()
    {
        self::autoload();
    }
    public static function loadonce($var)
    {
        require_once GX_LIB.'Vendor/'.$var;
    }

    public static function autoload()
    {
        include GX_LIB.'Vendor/autoload.php';
    }

    public static function url()
    {
        return Site::$url.'/inc/lib/Vendor';
    }

    public static function path($var)
    {
        return GX_LIB.'Vendor/'.$var.'/';
    }
}
