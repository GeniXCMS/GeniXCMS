<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140925
 * @version 2.2.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Typo Class.
 *
 * This class will process text modifier, including sanitizing, slug, strip
 * tags, create random characters.
 *
 * @since 0.0.1
 */
class Typo
{
    /**
     * Typo Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Securely cleans HTML content using HTMLPurifier.
     * Protects specific blocks like style, script, and Bootstrap attributes from being stripped.
     *
     * @param string $c The raw HTML content.
     * @return string The purified and escaped HTML content.
     */
    public static function cleanX($c)
    {
        // Protect <style> blocks from being stripped by HTMLPurifier
        $style_blocks = [];
        $c = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function ($matches) use (&$style_blocks) {
            $token = '[[gx_style_block_' . count($style_blocks) . ']]';
            $style_blocks[$token] = $matches[0];
            return $token;
        }, $c);

        // Protect <body> tags from being stripped by HTMLPurifier
        $body_tags = [];
        $c = preg_replace_callback('/<(body)\b[^>]*>/is', function ($matches) use (&$body_tags) {
            $token = '[[gx_body_open_' . count($body_tags) . ']]';
            $body_tags[$token] = $matches[0];
            return $token;
        }, $c);
        $c = str_replace('</body>', '[[gx_body_close]]', $c);

        // Protect <script> blocks from being stripped by HTMLPurifier
        $script_blocks = [];
        $c = preg_replace_callback('/<script\b[^>]*>(.*?)<\/script>/is', function ($matches) use (&$script_blocks) {
            $token = '[[gx_script_block_' . count($script_blocks) . ']]';
            $script_blocks[$token] = $matches[0];
            return $token;
        }, $c);

        // Protect data-bs-* and data-gjs-* attributes from HTMLPurifier stripping
        $data_attrs = [];
        $c = preg_replace_callback('/\s(data-(?:bs|gjs|gx)-[a-zA-Z0-9_\-]*)=([\"\']?)([^\"\'>\s]*)(\2)/', function ($matches) use (&$data_attrs) {
            $token = 'GXDATAATTR' . count($data_attrs) . 'END';
            $data_attrs[$token] = $matches[0]; // Full attribute string e.g. data-bs-ride="carousel"
            return ' placeholder-attr-' . $token . '="x"';
        }, $c);

        $val = $c;
        $val = preg_replace_callback(
            '#\<pre\>(.+?)\<\/pre\>#',
            function ($matches) {
                return "<pre>" . str_replace('"', '&quot;', $matches[1]) . "</pre>";
            },
            $val
        );

        Vendor::loadonce("ezyang/htmlpurifier/library/HTMLPurifier.auto.php");
        $config = HTMLPurifier_Config::createDefault();

        // Permissive configuration for Visual Builders
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
        $config->set('HTML.SafeObject', true);
        $config->set('HTML.SafeEmbed', true);
        $config->set('CSS.AllowTricky', true);
        $config->set('HTML.MaxImgLength', null);

        // Define allowed elements with all necessary attributes
        // Including Bootstrap data-bs-* and aria-* for interactive components
        $bsBtn = 'type|class|id|style|data-bs-target|data-bs-slide|data-bs-slide-to|data-bs-dismiss|data-bs-toggle|aria-current|aria-label|aria-hidden|aria-expanded|aria-controls|aria-selected';
        $bsDiv = 'class|id|style|data-bs-ride|data-bs-interval|data-bs-wrap|data-bs-touch|data-bs-spy|data-bs-offset|data-bs-target|data-gjs-type|data-gx-code|data-count|data-action|data-ids|data-loaded|data-columns|data-label|data-input|role|aria-label|aria-labelledby|aria-hidden|aria-expanded|aria-controls|aria-live';
        $bsA = 'href|target|rel|class|id|style|data-bs-toggle|data-bs-target|data-bs-dismiss|aria-current|aria-label|aria-expanded|aria-controls';
        $bsSpan = 'class|id|style|aria-hidden|aria-label';
        $bsLi = 'class|id|style|role|aria-current|aria-selected|data-bs-toggle|data-bs-target';
        $bsImg = 'src|alt|width|height|class|id|style|aria-hidden|aria-label|loading';
        $common = 'class|id|style';

        $config->set(
            'HTML.Allowed',
            "p[$common],b,i,em,strong," .
            "a[$bsA]," .
            "img[$bsImg]," .
            "div[$bsDiv]," .
            "span[$bsSpan]," .
            "section[$bsDiv],header[$bsDiv],footer[$bsDiv],nav[$bsDiv],main[$bsDiv],aside[$bsDiv],article[$bsDiv]," .
            "hr[$common],br," .
            "ul[$common|role],ol[$common],li[$bsLi]," .
            "h1[$common],h2[$common],h3[$common],h4[$common],h5[$common],h6[$common]," .
            "table[$common],thead[$common],tbody[$common],tr[$common],th[$common|scope],td[$common|colspan|rowspan]," .
            "sub,sup,blockquote[$common],code,pre[$common]," .
            "iframe[src|width|height|frameborder|allowfullscreen|class|id|style]," .
            "button[$bsBtn]," .
            "input[type|name|value|class|id|style|placeholder|checked|disabled|required|aria-label]," .
            "label[for|class|id|style]," .
            "select[name|class|id|style|multiple|disabled],option[value|selected|class]," .
            "form[action|method|class|id|style]"
        );

        // Properly register HTML5 elements and global data-* attributes
        $config->set('HTML.DefinitionID', 'gx-html5-builder');
        $config->set('HTML.DefinitionRev', 7);
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            // Register Modern Elements
            $def->addElement('section', 'Block', 'Flow', 'Common');
            $def->addElement('article', 'Block', 'Flow', 'Common');
            $def->addElement('aside', 'Block', 'Flow', 'Common');
            $def->addElement('header', 'Block', 'Flow', 'Common');
            $def->addElement('footer', 'Block', 'Flow', 'Common');
            $def->addElement('nav', 'Block', 'Flow', 'Common');
            $def->addElement('main', 'Block', 'Flow', 'Common');
            $def->addElement('button', 'Inline', 'Flow', 'Common', ['type' => 'Enum#button,submit,reset']);

            // Register Attributes
            $def->addAttribute('a', 'target', 'Enum#_blank,_self,_target,_top');
            $def->addAttribute('iframe', 'allowfullscreen', 'Bool');

            // Bootstrap Interactive Attributes (data-bs-*)
            $bsAttrs = [
                'data-bs-ride',
                'data-bs-target',
                'data-bs-slide',
                'data-bs-slide-to',
                'data-bs-toggle',
                'data-bs-dismiss',
                'data-bs-parent',
                'data-bs-interval',
                'data-bs-wrap',
                'data-bs-touch',
                'data-bs-spy',
                'data-bs-offset',
                'data-bs-smooth-scroll',
                'data-bs-theme',
                // GrapesJS/Builder Data Attributes
                'data-gjs-type',
                'data-gx-code',
                'data-count',
                'data-action',
                'data-ids',
                'data-loaded',
                'data-columns',
                'data-label',
                'data-input'
            ];
            $allElements = ['div', 'section', 'button', 'a', 'span', 'article', 'aside', 'header', 'footer', 'nav', 'main', 'p', 'img', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'i', 'form', 'input', 'select', 'option'];
            foreach ($bsAttrs as $attr) {
                foreach ($allElements as $el) {
                    try {
                        $def->addAttribute($el, $attr, 'Text');
                    } catch (Exception $e) {
                    }
                }
            }

            // Generic data-* via global attr
            $def->info_global_attr['data-bs-ride'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-bs-target'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-bs-slide'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-bs-slide-to'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-bs-toggle'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-bs-dismiss'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-bs-interval'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-gjs-type'] = new HTMLPurifier_AttrDef_Text();
            $def->info_global_attr['data-gx-code'] = new HTMLPurifier_AttrDef_Text();
        }

        $config = Hooks::filter('htmlpurifier_config_filter', $config);
        $purifier = new HTMLPurifier($config);
        $val = $purifier->purify($val);

        // Restore <style>, <body> and <script> blocks BEFORE htmlspecialchars for uniform encoding
        foreach ($style_blocks as $token => $code) {
            $val = str_replace($token, $code, $val);
        }
        foreach ($body_tags as $token => $code) {
            $val = str_replace($token, $code, $val);
        }
        foreach ($script_blocks as $token => $code) {
            $val = str_replace($token, $code, $val);
        }
        $val = str_replace('[[gx_body_close]]', '</body>', $val);

        // Restore data-bs-* and data-gjs-* attributes BEFORE htmlspecialchars
        foreach ($data_attrs as $token => $attr) {
            // Replace the placeholder attribute (e.g. placeholder-attr-GXDATAATTRxEND="x") with the original
            $val = str_replace(' placeholder-attr-' . $token . '="x"', $attr, $val);
        }

        $val = htmlspecialchars(
            $val,
            ENT_QUOTES | ENT_HTML5,
            'utf-8'
        );

        $val = str_replace('\\', "\\\\", $val);
        return $val;
    }

    /**
     * Reverses the cleanX process by decoding HTML entities and restoring backslashes.
     *
     * @param string|null $vars The escaped HTML content.
     * @return string The decoded HTML content.
     */
    public static function Xclean($vars)
    {
        if ($vars === null) {
            return '';
        }
        $vars = (string) $vars;
        $var = htmlspecialchars_decode($vars, ENT_QUOTES | ENT_HTML5);
        // $var = html_entity_decode($vars);
        $var = str_replace('\\\\', '\\', $var);
        return $var;
    }

    /**
     * Generates a URL-friendly slug from a string.
     *
     * @param string $text The text to slugify.
     * @return string The generated slug.
     */
    public static function slugify($text)
    {
        // strip tags
        $text = strip_tags($text);

        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        setlocale(LC_CTYPE, Options::v('country') . '.utf8');
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
     * Strips specified tags from a string.
     *
     * @param string $text The text to process.
     * @param string $tags The tags to allow or strip.
     * @param bool $invert Whether to invert the tag selection.
     * @return string The processed text.
     * @link http://php.net/manual/es/function.strip-tags.php#86964
     */
    public static function strip($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == false) {
                /*return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); */
                $text = preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>@si', '', $text);
                $text = preg_replace('@</(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>@si', '', $text);
            } else {
                /*return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); */
                $text = preg_replace('@<(' . implode('|', $tags) . ')\b.*?>@si', '', $text);
                $text = preg_replace('@</(' . implode('|', $tags) . ')\b.*?>@si', '', $text);
            }
        } elseif ($invert == false) {
            /*return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); */
            $text = preg_replace('@<(\w+)\b.*?>@si', '', $text);
            $text = preg_replace('@</(\w+)\b.*?>@si', '', $text);
        }

        return $text;
    }

    /**
     * Strips specific tags and their contents from a string.
     *
     * @param string $text The text to process.
     * @param string $tags The tags to process.
     * @param bool $invert Whether to invert the tag selection.
     * @return string The processed text.
     * @link http://php.net/manual/es/function.strip-tags.php#86964
     * @since 0.0.4
     */
    public static function strip_tags_content($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == false) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == false) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }

    /**
     * Generates a cryptographically secure random integer.
     *
     * @param int $min The minimum value.
     * @param int $max The maximum value.
     * @return int The random integer.
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

    /**
     * Simple alias for createToken.
     *
     * @param int $length The length of the token.
     * @return string The generated token.
     */
    public static function getToken($length)
    {
        $token = self::createToken($length);

        return $token;
    }

    /**
     * Generates a random token string with configurable character sets.
     *
     * @param int $length The desired length.
     * @param bool $capAlphabet Include uppercase letters.
     * @param bool $lowAlphabet Include lowercase letters.
     * @param bool $number Include numbers.
     * @param bool $symbol Include symbols.
     * @return string The generated token.
     */
    public static function createToken($length, $capAlphabet = true, $lowAlphabet = true, $number = true, $symbol = false)
    {
        $token = '';
        $codeAlphabet = '';
        if ($capAlphabet) {
            $codeAlphabet .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($lowAlphabet) {
            $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($number) {
            $codeAlphabet .= '0123456789';
        }
        if ($symbol) {
            $codeAlphabet .= '!@#$%^&*()_-+=|;:.<>~';
        }

        for ($i = 0; $i < $length; ++$i) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, strlen($codeAlphabet))];
        }

        return $token;
    }


    /**
     * Formats a variable as an integer string.
     *
     * @param mixed $var
     * @return string
     */
    public static function int($var)
    {
        $var = sprintf('%d', $var);

        return $var;
    }

    /**
     * Formats a variable as a float string with 2 decimal places.
     *
     * @param mixed $var
     * @return string
     */
    public static function float($var)
    {
        $var = number_format(sprintf('%2f', $var), 2);

        return $var;
    }

    /**
     * Escapes a string using the database escape method.
     *
     * @param string $vars
     * @return string
     */
    public static function escape($vars)
    {
        return Db::escape($vars);
    }

    /**
     * Converts newlines to HTML paragraph tags.
     *
     * @param string $string
     * @param bool $line_breaks
     * @param bool $xml
     * @return string
     * @since 0.0.4
     */
    public static function nl2p($string, $line_breaks = true, $xml = true)
    {
        $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

        // It is conceivable that people might still want single line-breaks
        // without breaking into a new paragraph.
        if ($line_breaks == true) {
            return '<p>' . preg_replace(
                array("/([\n]{2,})/i", "/([^>])\n([^<])/i"),
                array("</p>\n<p>", '$1<br' . ($xml == true ? ' /' : '') . '>$2'),
                trim($string)
            ) . '</p>';
        } else {
            return '<p>' . preg_replace(
                array("/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"),
                array("</p>\n<p>", "</p>\n<p>", '$1<br' . ($xml == true ? ' /' : '') . '>$2'),
                trim($string)
            ) . '</p>';
        }
    }

    /**
     * Converts URLs within a string to clickable HTML links.
     *
     * @param string $text
     * @return string
     */
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

    /**
     * Converts HTML paragraph and break tags to plain text newlines.
     *
     * @param string $string
     * @return string
     */
    public static function p2nl($string)
    {
        $string = str_replace(array('<p>', '<br>', '<br />'), '', $string);
        $string = str_replace('</p>', "\n", $string);

        return $string;
    }

    /**
     * Converts HTML paragraph tags to break tags.
     *
     * @param string $string
     * @return string
     */
    public static function p2br($string)
    {
        $string = str_replace(array('<p>'), '', $string);
        $string = str_replace('</p>', '<br />', $string);

        return $string;
    }

    /**
     * Formats a string for safe inclusion in a JSON structure.
     *
     * @param string $var
     * @return string
     */
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

    /**
     * Placeholder to reverse JSON formatting.
     *
     * @param string $var
     * @return string
     */
    public static function jsonDeFormat($var)
    {
        return $var;
    }

    /**
     * Validates an email address.
     *
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Filters a string for common XSS patterns by removing dangerous event handlers and javascript: protocols.
     *
     * @param string $str
     * @return string
     */
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

    /**
     * Translates a string using the system's translation engine.
     *
     * @param string $original
     * @return string
     */
    public static function translate(string $original)
    {
        $translated = _($original);

        return $translated;
    }
}

/* End of file Typo.class.php */
/* Location: ./inc/lib/Typo.class.php */
