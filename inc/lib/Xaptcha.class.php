<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.4-patch build date 20150702
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
class Xaptcha
{
    private static $key = '';
    private static $secret = '';
    private static $lang = '';

    public function __construct()
    {
        self::$key = Options::v('google_captcha_sitekey');
        self::$secret = Options::v('google_captcha_secret');
        self::$lang = Options::v('google_captcha_lang');
    }

    public static function verify($gresponse)
    {
        new self();
        $recaptcha = new \ReCaptcha\ReCaptcha(self::$secret);
        $resp = $recaptcha->verify($gresponse, $_SERVER['REMOTE_ADDR']);
        if ($resp->isSuccess()) {
            return true;
        } else {
            return false;
        }
    }

    public static function html()
    {
        new self();
        $html = '<div class="form-group">
            <div class="g-recaptcha" data-sitekey="'.self::$key.'"></div></div>
            <script type="text/javascript"
                    src="https://www.google.com/recaptcha/api.js?hl='.self::$lang.'" async defer>
            </script>';
        if (self::isEnable()) {
            return $html;
        } else {
            return '';
        }
    }

    public static function isEnable()
    {
        if (Options::v('google_captcha_enable') === 'on') {
            return true;
        } else {
            return false;
        }
    }
}
