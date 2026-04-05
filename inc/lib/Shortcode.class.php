<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 2.0.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Shortcode 
{
    /**
     * Parse shortcode in content
     * 
     * @param string $tag The shortcode tag to find (e.g. 'toc', 'image')
     * @param string $content The haystack content string
     * @param callable $callback Function receiving array of attributes and original string
     * @return string Processed content
     */
    public static function parse($tag, $content, $callback)
    {
        if (strpos($content, '[' . $tag) === false) {
            return $content;
        }

        // Match [tag key="val" key2='val2']
        $regex = '/\[' . preg_quote($tag, '/') . '\b([^\]]*)\]/is';
        
        return preg_replace_callback($regex, function($matches) use ($callback) {
            $attrStr = $matches[1];
            $attrs = self::parseAttributes($attrStr);
            return $callback($attrs, $matches[0]);
        }, $content);
    }

    /**
     * Internal helper to extract key="value" from attribute string
     */
    public static function parseAttributes($attrStr)
    {
        $attrs = [];
        // Match key="val" or key='val' or key=val
        $pattern = '/(\w+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i';
        
        if (preg_match_all($pattern, $attrStr, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                // $m[2] is double quotes, $m[3] is single, $m[4] is no quotes
                if (!empty($m[2])) $attrs[$m[1]] = $m[2];
                elseif (!empty($m[3])) $attrs[$m[1]] = $m[3];
                elseif (isset($m[4])) $attrs[$m[1]] = $m[4];
                else $attrs[$m[1]] = '';
            }
        }
        return $attrs;
    }

    /**
     * Strip all shortcodes from content
     * Useful for extracting clean text excerpts without exposing rendering block syntax.
     */
    public static function strip($content)
    {
        if (empty($content)) return $content;
        return preg_replace('/\[[a-zA-Z0-9_-]+[^\[\]]*\]/is', '', $content);
    }
}
