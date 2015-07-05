<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
*
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* Typo Class
*
* This class will proccess text modifier, including sanitizing, slug, strip tags,
* create random characters.
*
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class Typo
{
    public function __construct () {

    }

    public static function cleanX ($c) {
        $val = self::strip_tags_content($c, '<script>', TRUE);
        $val = htmlspecialchars(
                $val,
                ENT_QUOTES|ENT_HTML5,
                "utf-8"
            );
        // $val = htmlentities(
        //             $c,
        //             ENT_QUOTES | ENT_IGNORE, "UTF-8");
        return $val;
    }

    public static function Xclean($vars) {
        $var = htmlspecialchars_decode($vars);
        // $var = html_entity_decode($vars);
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
      setlocale(LC_CTYPE, Options::get('country').'.utf8');
      $text = iconv('utf-8', 'ASCII//TRANSLIT', $text);
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
    * Remove Tags
    * @link http://php.net/manual/es/function.strip-tags.php#86964
    */
    public static function strip($text, $tags = '', $invert = FALSE) {

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if(is_array($tags) AND count($tags) > 0) {
            if($invert == FALSE) {
                /*return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); */
                $text = preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>@si', '', $text);
                $text = preg_replace('@</(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>@si', '', $text);
            }
            else {
                /*return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); */
                $text = preg_replace('@<('. implode('|', $tags) .')\b.*?>@si', '', $text);
                $text = preg_replace('@</('. implode('|', $tags) .')\b.*?>@si', '', $text);
            }
            }
        elseif($invert == FALSE) {
            /*return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); */
            $text = preg_replace('@<(\w+)\b.*?>@si', '', $text);
            $text = preg_replace('@</(\w+)\b.*?>@si', '', $text);
        }
        return $text;
    }

    /**
    * Remove Tags and Content inside tags
    * @link http://php.net/manual/es/function.strip-tags.php#86964
    * @since 0.0.4
    */
    public static function strip_tags_content($text, $tags = '', $invert = FALSE) {

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if(is_array($tags) AND count($tags) > 0) {
            if($invert == FALSE) {
                return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            }
            else {
                return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
            }
        }
        elseif($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    /**
    * Cryptography random characters
    * @link http://stackoverflow.com/a/13733588
    */
    public static function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
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

    public static function getToken($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[self::crypto_rand_secure(0,strlen($codeAlphabet))];
        }
        return $token;
    }

    public static function int($var) {
        $var = sprintf('%d', $var);
        return $var;
    }

    public static function float($var) {
        $var = sprintf('%2f', $var);
        return $var;
    }

    public static function escape($vars) {
        return Db::escape($vars);
    }

    /**
    * Change New Line (nl) to Paragraph
    *
    * @since 0.0.4
    */
    public static function nl2p($string, $line_breaks = true, $xml = true) {

        $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

        // It is conceivable that people might still want single line-breaks
        // without breaking into a new paragraph.
        if ($line_breaks == true)
            return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"),
            array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'), trim($string)).'</p>';
        else
            return '<p>'.preg_replace(
            array("/([\n]{2,})/i", "/([\r\n]{3,})/i","/([^>])\n([^<])/i"),
            array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),

            trim($string)).'</p>';
    }

    public static function url2link($text){
        // The Regular Expression filter
        $reg_exUrl = preg_replace('@((https?://)(www\.|[-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>',
            $text);
        return $reg_exUrl;
    }
}

/* End of file Typo.class.php */
/* Location: ./inc/lib/Typo.class.php */
