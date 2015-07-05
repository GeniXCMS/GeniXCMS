<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.4-patch build date 20150702
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

class Xaptcha
{

    private static $key = '';
    private static $secret = '';
    private static $lang = '';

    public function __construct() {
        self::$key = Options::get('google_captcha_sitekey');
        self::$secret = Options::get('google_captcha_secret');
        self::$lang = Options::get('google_captcha_lang');
    }

    public static function verify($gresponse) {
        new Xaptcha();
        $recaptcha = new \ReCaptcha\ReCaptcha(self::$secret);
        $resp = $recaptcha->verify($gresponse, $_SERVER['REMOTE_ADDR']);
        if ($resp->isSuccess()) {
            return true;
        }else{
            return false;
        }
    }

    public static function html() {
        new Xaptcha();
        $html = "<div class=\"form-group\">
            <div class=\"g-recaptcha\" data-sitekey=\"".self::$key."\"></div></div>
            <script type=\"text/javascript\"
                    src=\"https://www.google.com/recaptcha/api.js?hl=".self::$lang."\" async defer>
            </script>";
        if (self::isEnable()) {
            return $html;
        }else{
            return '';
        }
    }

    public static function isEnable() {
        if (Options::get('google_captcha_enable') === 'on') {
            return true;
        }else{
            return false;
        }
    }
}