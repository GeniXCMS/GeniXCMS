<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Typo.class.php
* version : 0.0.1 pre
* build : 20140925
*/

class Typo
{
    public function __construct () {

    }

    public static function cleanX ($c) {
        // $val = htmlspecialchars(
        //         mysql_real_escape_string($c), 
        //         ENT_QUOTES, 
        //         "utf-8"
        //     );
        $val = htmlentities($c, ENT_QUOTES | ENT_IGNORE, "UTF-8");
        return $val;
    }

    public static function Xclean($vars) {
        // $var = htmlspecialchars_decode($vars);
      $var = html_entity_decode($vars);
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
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // lowercase
      $text = strtolower($text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }


    public static function strip($text, $tags = '', $invert = FALSE) { 

      preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags); 
      $tags = array_unique($tags[1]); 
        
      if(is_array($tags) AND count($tags) > 0) { 
        if($invert == FALSE) { 
          /*return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); */
          return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>@si', '', $text); 
          return preg_replace('@</(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>@si', '', $text); 
        } 
        else { 
          /*return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); */
          return preg_replace('@<('. implode('|', $tags) .')\b.*?>@si', '', $text); 
          return preg_replace('@</('. implode('|', $tags) .')\b.*?>@si', '', $text); 
        } 
      } 
      elseif($invert == FALSE) { 
        /*return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); */
        return preg_replace('@<(\w+)\b.*?>@si', '', $text); 
        return preg_replace('@</(\w+)\b.*?>@si', '', $text); 
      } 
      return $text; 
    } 
}

/* End of file Db.class.php */
/* Location: ./inc/lib/Db.class.php */