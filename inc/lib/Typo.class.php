<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
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

/**
 * Typo Class.
 *
 * This class will process text modifier, including sanitizing, slug, strip
 * tags, create random characters.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.1
 */
class Typo
{
    public function __construct()
    {
    }

    public static function cleanX($c)
    {
        // $c = urldecode($c);
        // echo $c;
        $val = self::strip_tags_content($c, '<script>', true);
        $val = preg_replace_callback(
            '#\<pre\>(.+?)\<\/pre\>#',
            function($matches) {
                return "<pre>".str_replace('"', '&quot;', $matches[1])."</pre>";
            },
            $val
        );
        Vendor::loadonce("ezyang/htmlpurifier/library/HTMLPurifier.auto.php");
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $val = $purifier->purify($val);
        // $val = self::filterXss($val);
        $val = htmlspecialchars(
            $val,
            ENT_QUOTES | ENT_HTML5,
            'utf-8'
        );
        $val = str_replace('\\', "\\\\", $val);
        // echo $val;
        // $val = htmlentities(
        //             $c,
        //             ENT_QUOTES | ENT_IGNORE, "UTF-8");
        return $val;
    }

    public static function Xclean($vars)
    {
        $var = htmlspecialchars_decode($vars, ENT_QUOTES | ENT_HTML5);
        // $var = html_entity_decode($vars);
        $var = str_replace('\\\\', '\\', $var);
        return $var;
    }

    public static function slugify($text)
    {
        // strip tags
        $text = strip_tags($text);

      // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

      // trim
        $text = trim($text, '-');

      // transliterate
        setlocale(LC_CTYPE, Options::v('country').'.utf8');
        $text = iconv('utf-8', 'utf-8//TRANSLIT', $text);
      // lowercase
        $text = strtolower($text);

      // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Remove Tags.
     *
     * @link http://php.net/manual/es/function.strip-tags.php#86964
     */
    public static function strip($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == false) {
                /*return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); */
                $text = preg_replace('@<(?!(?:'.implode('|', $tags).')\b)(\w+)\b.*?>@si', '', $text);
                $text = preg_replace('@</(?!(?:'.implode('|', $tags).')\b)(\w+)\b.*?>@si', '', $text);
            } else {
                /*return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); */
                $text = preg_replace('@<('.implode('|', $tags).')\b.*?>@si', '', $text);
                $text = preg_replace('@</('.implode('|', $tags).')\b.*?>@si', '', $text);
            }
        } elseif ($invert == false) {
            /*return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); */
            $text = preg_replace('@<(\w+)\b.*?>@si', '', $text);
            $text = preg_replace('@</(\w+)\b.*?>@si', '', $text);
        }

        return $text;
    }

    /**
     * Remove Tags and Content inside tags.
     *
     * @link http://php.net/manual/es/function.strip-tags.php#86964
     * @since 0.0.4
     */
    public static function strip_tags_content($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == false) {
                return preg_replace('@<(?!(?:'.implode('|', $tags).')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<('.implode('|', $tags).')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == false) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }

    /**
     * Cryptography random characters.
     *
     * @link http://stackoverflow.com/a/13733588
     */
    public static function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 0) {
            return $min;
        } // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);

        return $min + $rnd;
    }

    public static function getToken($length)
    {
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        for ($i = 0; $i < $length; ++$i) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, strlen($codeAlphabet))];
        }

        return $token;
    }

    public static function int($var)
    {
        $var = sprintf('%d', $var);

        return $var;
    }

    public static function float($var)
    {
        $var = number_format(sprintf('%2f', $var), 2);

        return $var;
    }

    public static function escape($vars)
    {
        return Db::escape($vars);
    }

    /**
     * Change New Line (nl) to Paragraph.
     *
     * @since 0.0.4
     */
    public static function nl2p($string, $line_breaks = true, $xml = true)
    {
        $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

        // It is conceivable that people might still want single line-breaks
        // without breaking into a new paragraph.
        if ($line_breaks == true) {
            return '<p>'.preg_replace(
                array("/([\n]{2,})/i", "/([^>])\n([^<])/i"),
                array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),
                trim($string)
            ).'</p>';
        } else {
            return '<p>'.preg_replace(
                array("/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"),
                array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),
                trim($string)
            ).'</p>';
        }
    }

    public static function url2link($text)
    {
        // The Regular Expression filter
        $reg_exUrl = preg_replace(
            '@((https?://)(www\.|[-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank" rel="nofollow">$1</a>',
            $text
        );

        return $reg_exUrl;
    }

    public static function p2nl($string)
    {
        $string = str_replace(array('<p>', '<br>', '<br />'), '', $string);
        $string = str_replace('</p>', "\n", $string);

        return $string;
    }

    public static function p2br($string)
    {
        $string = str_replace(array('<p>'), '', $string);
        $string = str_replace('</p>', '<br />', $string);

        return $string;
    }

    public static function jsonFormat($var)
    {
        // $var = self::cleanX($var);
        $var = str_replace("\r\n", "\n", $var);
        $var = str_replace("\r", "\n", $var);

        // // // JSON requires new line characters be escaped
        $var = str_replace("\n", '\\n', $var);
        $var = str_replace("'", '\\u0027', $var);
        // $var = str_replace('"', '\\u0022', $var);
        $var = preg_replace_callback(
            '/<([^<>]+)>/',
            function ($matches) {
                return str_replace('"', '\"', $matches[0]);
            },
            $var
        );
        // $var = preg_replace_callback(
        //     '/([^<>]+)/',
        //     function ($matches) {
        //         return str_replace("'", '&apos;', $matches[0]);
        //     },
        //     $var
        // );

        $var = str_replace('/>', ' />', $var);
        $var = str_replace('</', '<\/', $var);

        
        $var = str_replace('\&', '&', $var);

        return $var;
    }

    public static function jsonDeFormat($var)
    {
        return utf8_decode($var);
    }

    public static function validateEmail($email)
    {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function filterXss($str)
    {
//        $str = preg_replace('#on.*=["|\'](.*)["|\']#', '', $str);
        $str = preg_replace('#(?!<pre>.*?)(onload|onerror|onblur|onchange|onscroll|oninput|
        onfocus|onbeforescriptexecute|ontoggle|onratechange|onreadystatechange|onpropertychange|
        onqt_error|onpageshow|onclick|onmouseover|onunload|event|formaction|actiontype|background|oncut)=("|\')(.*)("|\')(?!.*?</pre>)#', '', $str);
        $str = preg_replace('#(.*?)(javascript:.*)(.*?)#', '', $str);
        $str = preg_replace('#(.*?)(onload|onerror|onblur|onchange|onscroll|oninput|
        onfocus|onbeforescriptexecute|ontoggle|onratechange|onreadystatechange|onpropertychange|
        onqt_error|onpageshow|onclick|onmouseover|onunload|event|formaction|actiontype|background|oncut)=("|\')(.*)("|\')(.*?)#', '', $str);
        //$str = preg_replace('#&lt;(.*?)script&gt;#', '', $str);
        return $str;
    }
}

/* End of file Typo.class.php */
/* Location: ./inc/lib/Typo.class.php */
