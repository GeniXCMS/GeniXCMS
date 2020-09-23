<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141004
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
class Site
{
    public static $editors;
    public static $data;
    public static $url;
    public static $cdn;
    public static $domain;
    public static $name;
    public static $key;
    public static $desc;
    public static $email;
    public static $slogan;
    public static $isOffline;

    public function __construct()
    {
        global $GLOBALS, $data;
        self::$editors = &$GLOBALS;
        self::$data = &$data;
        self::$url = Options::v('siteurl');
        self::$cdn = Options::v('cdn_url');
        self::$domain = Options::v('sitedomain');
        self::$name = Options::v('sitename');
        self::$key = Options::v('sitekeywords');
        self::$desc = Options::v('sitedesc');
        self::$email = Options::v('siteemail');
        self::$slogan = Options::v('siteslogan');
        if(Http::isLocal(self::$url)) {
            self::$isOffline = false;
        } else {
            self::$isOffline = true;
        }

        Hooks::attach('header_load_meta', array(__CLASS__, 'loadLibHeader'));
        Hooks::attach('footer_load_lib', array(__CLASS__, 'loadLibFooter'));
    }

    /* Call all Website Meta at Header
    *
    */
    public static function meta($location = '', $cont_desc = '', $pre = '')
    {
        global $data;
//        print_r($data);
        //if (empty($data['posts'][0]->title)) {

        if (is_array($data)) {

            $cont_title = self::title($data);
            $cont_title = "{$pre} {$cont_title} - ";
            $canonical = self::canonical();
        } else {
            $cont_title = '';
            $canonical = '';
        }
        if (is_array($data)  && isset($data['posts'][0]->content)) {
            $desc = Typo::strip($data['posts'][0]->content);
        } else {
            $desc = '';
        }
        $cont_title = Hooks::filter('site_title_filter', $cont_title);
        $keyword = Hooks::filter('site_key_filter', self::$key);
        echo '
    <!--// Start Meta: Generated Automaticaly by GeniXCMS -->
    <meta charset="'.Options::v('charset').'">';
        echo "
    <!-- SEO: Title stripped 70chars for SEO Purpose -->
    <title>{$cont_title}".self::$name.'</title>
    <meta name="Keyword" content="'.$keyword.'">
    <!-- SEO: Description stripped 150chars for SEO Purpose -->
    <meta name="Description" content="'.self::desc($desc).'">';
        if (isset($data['posts'][0]->author) && !isset($data['posts'][1]->author)) {
            echo "
    <meta name=\"Author\" content=\"{$data['posts'][0]->author}\">";
        }
        echo "
    <meta name=\"Generator\" content=\"GeniXCMS ".System::v()."\">
    <meta name=\"robots\" content=\"".Options::v('robots')."\">
    <link rel=\"canonical\" href=\"".$canonical."\" />
    <link rel=\"shortcut icon\" href=\"".Options::v('siteicon')."\" />
    <link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS Feed for ".self::$name."\" href=\"".self::$url."rss/\" />
        ";

        ($location == 'backend') ? Hooks::run('header_load_admin_meta', $data) : Hooks::run('header_load_meta', $data);
        echo '
    <!-- Generated Automaticaly by GeniXCMS :End Meta //-->';
        // echo $meta;
    }

    public static function title($data)
    {
//        print_r($data);
        $sitenamelength = strlen(self::$name);
        $limit = 70 - $sitenamelength - 6;
        if (isset($data['sitetitle'])) {
            $cont_title = substr(Typo::Xclean(Typo::strip($data['sitetitle'])), 0, $limit);
            $titlelength = strlen($data['sitetitle']);
        } elseif (isset($data['posts'][0]->title) && !isset($data['posts'][1]->title)) {
            $cont_title = substr(Typo::Xclean(Typo::strip($data['posts'][0]->title)), 0, $limit);
            $titlelength = strlen($data['posts'][0]->title);
        } else {
            $cont_title = substr(Typo::Xclean(Typo::strip(Options::v('siteslogan'))), 0, $limit);
            $titlelength = strlen(Options::v('siteslogan'));
        }
        if ($titlelength > $limit + 3) {
            $dotted = '...';
        } else {
            $dotted = '';
        }

        return $cont_title.$dotted;
    }

    public static function footer()
    {
        global $data;

        Hooks::run('footer_load_lib', $data);
    }

    public static function desc($vars)
    {
        if (!empty($vars)) {
            $desc = substr(strip_tags(htmlspecialchars_decode($vars).'. '.self::$desc), 0, 150);
        } else {
            $desc = substr(self::$desc, 0, 150);
        }
        $desc = Hooks::filter('site_desc_filter', $desc);

        return $desc;
    }

    public static function logo($width = '', $height = '')
    {
        // check which logo is used, logourl or uploaded files.
        if (Options::v('is_logourl') == 'on' && Options::v('logourl') != '') {
            $logo = '<img src="'.Options::v('logourl')."\"
                    style=\"width: $width; height: $height; margin: 1px;\">";
        } elseif (Options::v('is_logourl') == 'off' && Options::v('logo') != '') {
            $logo = '<img src="'.self::$cdn.Options::v('logo')."\"
                    style=\"width: $width; height: $height; margin: 1px;\">";
        } else {
            $logo = '<span class="mg genixcms-logo"></span>';
        }

        return $logo;
    }

    public static function generated()
    {
        $end_time = microtime(true);
        $time_taken = $end_time - $GLOBALS['start_time'];
        $time_taken = round($time_taken, 5);
        echo '<center><small>Page generated in '.$time_taken.' seconds.</small></center>';
    }

    public static function canonical()
    {
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';

        return $protocol.$_SERVER['HTTP_HOST'].urlencode($_SERVER['REQUEST_URI']);
    }

    public static function minifyHTML($input)
    {
        if (trim($input) === '') {
            return $input;
        }
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
            return '<'.$matches[1].preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]).$matches[3].'>';
        }, str_replace("\r", '', $input));
        // Minify inline CSS declaration(s)
        if (strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
                return '<'.$matches[1].' style='.$matches[2].self::minifyCSS($matches[3]).$matches[2];
            }, $input);
        }

        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s',
            ),
            array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                '',
            ),
            $input
        );
    }

    public static function minifyCSS($input)
    {
        if (trim($input) === '') {
            return $input;
        }

        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s',
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2',
            ),
            $input
        );
    }
    // JavaScript Minifier
    public static function minifyJS($input)
    {
        if (trim($input) === '') {
            return $input;
        }

        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i',
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3',
            ),
            $input
        );
    }

    public static function minifIed($input)
    {
        // $input = self::minifyJS($input);
        // $input = self::minifyCSS($input);
        $input = self::minifyHTML($input);

        return $input;
    }

    public static function loadLibHeader()
    {
        $lib = '';
        $bs = Options::v('use_bootstrap');
        if ($bs == 'on') {
            $lib .= '
    <link href="'.self::$cdn."assets/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
        }
        $fa = Options::v('use_fontawesome');
        if ($fa == 'on') {
            $lib .= "
    <link href=\"https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css\" rel=\"stylesheet\">\n";
        }

        echo $lib;
    }

    public static function loadLibFooter()
    {
        $foot = '';

        $jquery = Options::v('use_jquery');
        $jquery_v = Options::v('jquery_v');
        if ($jquery == 'on') {
            $foot .= '
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/'.$jquery_v.'/jquery.min.js"></script>';
        }

        $bs = Options::v('use_bootstrap');
        if ($bs == 'on') {
            $foot .= '
            <!-- These files are included by default by GeniXCMS. You can set it at the dashboard -->
            <script src="'.self::$cdn.'assets/js/bootstrap.min.js"></script>
            <script src="'.self::$cdn.'assets/js/ie10-viewport-bug-workaround.js"></script>';
        }

        if (isset($GLOBALS['editor']) && $GLOBALS['editor'] == true) {
            Hooks::attach('footer_load_lib', array('Files', 'elfinderLib'));

            $height = $GLOBALS['editor_height'];

            // $url = (SMART_URL)? Site::$cdn . '/ajax/saveimage?token=' . TOKEN : Site::$cdn . "/index.php?ajax=saveimage&token=" . TOKEN;
            $url = Url::ajax('saveimage');
            $foot .= '

    <link href="'.self::$cdn.'assets/css/summernote.css" rel="stylesheet">
    <script src="'.self::$cdn.'assets/js/summernote.min.js"></script>
    <script src="'.self::$cdn.'assets/js/plugins/summernote-ext-genixcms.js"></script>
    <script src="'.self::$cdn.'assets/js/plugins/summernote-image-attributes.js"></script>
    <script src="'.self::$cdn.'assets/js/plugins/summernote-floats-bs.min.js"></script>
    <script>
      $(document).ready(function() {
        $(".editor").summernote({
            minHeight: '.$height.',
            maxHeight: ($(window).height() - 150),
            toolbar: [
                    '.System::$toolbar.'
                ],
            callbacks: {
                onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0],editor,welEditable);
                },
                onPaste: function (e) {
                    var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData(\'Text\');
                    e.preventDefault();
                    document.execCommand(\'insertText\', false, bufferText);
                },
                onChange: function(e) {
                    var characteres = $(".note-editable").text();
                    var wordCount = characteres.trim().split(\' \').length;
                    if (characteres.length == 0) {
                        $(\'.note-statusbar\').html(\'&nbsp; 0 word <div class="note-resizebar">    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>  </div>\');
                        return;
                    }
                    //Update value
                    $(".note-statusbar").html(\'&nbsp; \'+wordCount+\' words <div class="note-resizebar">    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>    <div class="note-icon-bar"></div>  </div>\');
     
                }
            },
            popover: {
                image: [
                    [\'imagesize\', [\'imageSize100\', \'imageSize50\', \'imageSize25\']],
                    // [\'float\', [\'floatLeft\', \'floatRight\', \'floatNone\']],
                    [\'floatBS\', [\'floatBSLeft\', \'floatBSNone\', \'floatBSRight\']],
                    [\'custom\', [\'imageAttributes\', \'imageShape\']],
                    [\'remove\', [\'removeMedia\']]
                ],
            },
        });

        function sendFile(file,editor,welEditable) {
          data = new FormData();
          data.append("file", file);
            $.ajax({
                url: "'.$url.'",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: \'POST\',
                success: function(data) {
                //alert(data);
                  $(\'.editor\').summernote(\'editor.insertImage\', data);
                },
               error: function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus+" "+errorThrown);
               }
            });
          }

         $(".alert").alert();
      });


    </script>
              ';
        }

        if (isset($GLOBALS['validator']) && $GLOBALS['validator'] == true) {
            $foot .= '
            <link href="'.self::$cdn.'assets/css/bootstrapValidator.min.css" rel="stylesheet">
            <script src="'.self::$cdn.'assets/js/bootstrapValidator.min.js"></script>
            ';

            $foot .= $GLOBALS['validator_js'];
        }

        echo $foot;
    }
}

/* End of file Site.class.php */
/* Location: ./inc/lib/Site.class.php */
