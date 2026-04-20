<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *


 *
 * @since 2.0.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Shortcode
{
    /**
     * Parses a specific shortcode within the provided content.
     *
     * It identifies the shortcode tag and its attributes, then processes them through the given callback.
     * Handles both single-tag [tag] and wrapping [tag]content[/tag] formats.
     *
     * @param string   $tag      The shortcode tag to search for (e.g., 'image', 'slider').
     * @param string   $content  The input content string containing shortcodes.
     * @param callable $callback The processing function that receives (array $attributes, string $innerContent).
     * @return string            The content with the shortcode replaced by the callback return value.
     */
    public static function parse($tag, $content, $callback)
    {
        if (strpos($content, '[' . $tag) === false) {
            return $content;
        }

        // Match [tag attrs]content[/tag] OR [tag attrs]
        // Group 1: Attributes string
        // Group 2: Inner content (if wrapping tag)
        $regex = '/\[' . preg_quote($tag, '/') . '\b([^\]]*)\](?:([\s\S]*?)\[\/' . preg_quote($tag, '/') . '\])?/is';

        return preg_replace_callback($regex, function ($matches) use ($callback, $tag) {
            $attrStr = $matches[1];
            $attrs = self::parseAttributes($attrStr);

            // If it's a wrapping shortcode, $matches[2] will contain the inner content.
            // Otherwise, we pass the full match as the second argument for backward compatibility.
            $inner = isset($matches[2]) ? $matches[2] : $matches[0];

            return $callback($attrs, $inner);
        }, $content);
    }

    /**
     * Extracts attributes from a shortcode attribute string.
     * Works with formats like key="value", key='value', or key=value.
     *
     * @param string $attrStr The raw attribute string from inside the shortcode brackets.
     * @return array          An associative array of key-value attribute pairs.
     */
    public static function parseAttributes($attrStr)
    {
        $attrs = [];
        // Support both literal and encoded quotes (common in cleaned content)
        $attrStr = str_replace(['&quot;', '&apos;', '&#039;'], ['"', "'", "'"], $attrStr);

        // Match key="val" or key='val' or key=val
        $pattern = '/(\w+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i';

        if (preg_match_all($pattern, $attrStr, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                // $m[2] is double quotes, $m[3] is single, $m[4] is no quotes
                if (!empty($m[2]))
                    $attrs[$m[1]] = $m[2];
                elseif (!empty($m[3]))
                    $attrs[$m[1]] = $m[3];
                elseif (isset($m[4]))
                    $attrs[$m[1]] = $m[4];
                else
                    $attrs[$m[1]] = '';
            }
        }
        return $attrs;
    }

    /**
     * Removes all shortcodes from the content string.
     * Used primarily for creating clean text excerpts or summaries.
     *
     * @param string $content The content string to strip.
     * @return string         Content with all shortcode tags ([...]) removed.
     */
    public static function strip($content)
    {
        if (empty($content))
            return $content;
        return preg_replace('/\[[a-zA-Z0-9_-]+[^\[\]]*\]/is', '', $content);
    }
}
