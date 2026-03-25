<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141004
 *
 * @version 2.0.0-alpha
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
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
        self::$editors = $GLOBALS;
        self::$data = $data;
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
    public static function meta($data, $location = '', $cont_desc = '',  $pre = '')
    {
        // global $data;
        // print_r($data);
        //if (empty($data['posts'][0]->title)) {

        if (is_array($data)) {
            $pre = $pre != "" ? $pre." ": "";
            $cont_title = self::title($data);
            $cont_title = "{$pre}{$cont_title} - ";
            $canonical = self::canonical();
        } else {
            $cont_title = '';
            $canonical = self::canonical();
        }
        if (is_array($data)  && isset($data['posts'][0]->content)) {
            $desc = Typo::strip($data['posts'][0]->content);
        } else {
            $desc = $cont_desc;
        }

        $site_desc = self::desc($desc);
        $cont_title = Hooks::filter('site_title_filter', $cont_title);
        $full_title = Hooks::filter('full_site_title_filter', "{$cont_title}".self::$name);
        $keyword = Hooks::filter('site_key_filter', self::keyWords($data));
        $out =  '
    <!--// Start Meta: Generated Automaticaly by GeniXCMS -->
    <meta charset="'.Options::v('charset').'">';
        $out .=  "
    <!-- SEO: Title stripped 70chars for SEO Purpose -->
    <title>{$full_title}</title>
    <meta name=\"keyword\" content=\"".$keyword."\">
    <!-- SEO: Description stripped 150chars for SEO Purpose -->
    <meta name=\"description\" content=\"".$site_desc."\">";
        if (isset($data['posts'][0]->author) && !isset($data['posts'][1]->author)) {
            $out .=  "
    <meta name=\"author\" content=\"{$data['posts'][0]->author}\">";
        }
        $out .=  "
    <meta name=\"generator\" content=\"GeniXCMS ".System::v()."\">
    <meta name=\"robots\" content=\"".self::indexing($data)."\">
    <link rel=\"canonical\" href=\"".$canonical."\" />
    <link rel=\"shortcut icon\" href=\"".Options::v('siteicon')."\" />
    <link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS Feed for ".self::$name."\" href=\"".self::$url."rss/\" />
    
    <!-- Open Graph / Facebook -->
    <meta property=\"og:type\" content=\"website\">
    <meta property=\"og:url\" content=\"".$canonical."\">
    <meta property=\"og:title\" content=\"{$full_title}\">
    <meta property=\"og:description\" content=\"".$site_desc."\">";
        
        if (isset($data['imgurl'])) {
            $out .= "
    <meta property=\"og:image\" content=\"".$data['imgurl']."\">";
        }

        $out .= "
    <!-- Twitter -->
    <meta property=\"twitter:card\" content=\"summary_large_image\">
    <meta property=\"twitter:url\" content=\"".$canonical."\">
    <meta property=\"twitter:title\" content=\"{$full_title}\">
    <meta property=\"twitter:description\" content=\"".$site_desc."\">";

        if (isset($data['imgurl'])) {
            $out .= "
    <meta property=\"twitter:image\" content=\"".$data['imgurl']."\">";
        }

        $out .= self::jsonLD($data);

        $out .= ($location == 'backend') ? Hooks::run('header_load_admin_meta', $data) : Hooks::run('header_load_meta', $data);
        $out .=  '
    <!-- Generated Automaticaly by GeniXCMS :End Meta //-->';
        // echo $meta;

        return $out;
    }

    public static function jsonLD($data)
    {
        $payload = [];
        if (isset($data['posts'][0]) && !isset($data['posts'][1])) {
            $post = $data['posts'][0];
            $payload = [
                "@context" => "https://schema.org",
                "@type" => (isset($data['p_type']) && $data['p_type'] == 'page') ? "WebPage" : "BlogPosting",
                "headline" => $post->title,
                "description" => self::desc(Typo::strip($post->content)),
                "author" => [
                    "@type" => "Person",
                    "name" => $post->author
                ],
                "datePublished" => $post->date,
                "url" => self::canonical()
            ];

            if (isset($data['imgurl'])) {
                $payload["image"] = $data['imgurl'];
            }
        } else {
            $payload = [
                "@context" => "https://schema.org",
                "@type" => "WebSite",
                "name" => self::$name,
                "url" => self::$url,
                "description" => self::$desc
            ];
        }

        return '
    <script type="application/ld+json">
    '.json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).'
    </script>';
    }

    public static function indexing($data) 
    {
        $noindex = [
            'tag',
            'search',
            'archive',
            'author'
        ];
        $indexfollow = [
            'post', 'page', 'index', 'cat'
        ];
        $indexing = Options::v('robots');
        if(isset($data['p_type']) && in_array($data['p_type'], $noindex) ) {
            $indexing = "noindex, follow";
        }
        if( isset($data['p_type']) && in_array($data['p_type'], $indexfollow)  ) {
            $indexing = "index, follow";
        }

        return $indexing;
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

        if( defined( 'DEBUG' ) && DEBUG) {

            if( isset($data)) {
                echo "<pre>";
                print_r($data);
                echo "</pre>";
            }
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
            ob_start();
            phpinfo();
            $phpinfo = ob_get_contents();
            ob_end_clean();
            $phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);
            echo "
                <style type='text/css'>
                    #phpinfo {}
                    #phpinfo pre {margin: 0; font-family: monospace;}
                    #phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
                    #phpinfo a:hover {text-decoration: underline;}
                    #phpinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
                    #phpinfo .center {text-align: center;}
                    #phpinfo .center table {margin: 1em auto; text-align: left;}
                    #phpinfo .center th {text-align: center !important;}
                    #phpinfo td, th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
                    #phpinfo h1 {font-size: 150%;}
                    #phpinfo h2 {font-size: 125%;}
                    #phpinfo .p {text-align: left;}
                    #phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
                    #phpinfo .h {background-color: #99c; font-weight: bold;}
                    #phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
                    #phpinfo .v i {color: #999;}
                    #phpinfo img {float: right; border: 0;}
                    #phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
                </style>
                <div id='phpinfo'>
                    $phpinfo
                </div>
                ";  
        }

        return Hooks::run('footer_load_lib', $data);
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

    public static function keyWords($data) {
        // print_r($data);
        $keys = explode(",", self::$key);
        if( isset($data['p_type']) && $data['p_type'] == "index" && count($keys ) > 0 ) {
            return self::$key;
        } else {
            if( isset($data['posts'][0]) ) {
                $post = $data['posts'][0];
                $keys = Posts::getParam('tags', $post->id);
                return $keys;
            }
        }
        
    }

    public static function logo($width = '', $height = '', $class = '')
    {
        // check which logo is used, logourl or uploaded files.
        if (Options::v('is_logourl') == 'on' && Options::v('logourl') != '') {
            $logo = '<img src="'.Options::v('logourl')."\"
                    style=\"width: $width; height: $height; margin: 1px;\" class=\"{$class}\" alt=\"".Site::$name."\">";
        } elseif (Options::v('is_logourl') == 'off' && Options::v('logo') != '') {
            $logo = '<img src="'.self::$cdn.Options::v('logo')."\"
                    style=\"width: $width; height: $height; margin: 1px;\" class=\"{$class}\" alt=\"".Site::$name."\">";
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
        // $request_uri =  ($_SERVER['REQUEST_URI'] == "/") ? "/": urlencode($_SERVER['REQUEST_URI']);
        
        return $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">';
        }
        $fa = Options::v('use_fontawesome');
        if ($fa == 'on') {
            $lib .= "
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css\" integrity=\"sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />";
        }

        

        return $lib;
    }

    public static function loadLibFooter()
    {
        $foot = '';

        $jquery = Options::v('use_jquery');
        $jquery_v = Options::v('jquery_v');
        if ($jquery == 'on') {
            $foot .= '
    <script src="https://code.jquery.com/jquery-'.$jquery_v.'.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>';
        }

        $bs = Options::v('use_bootstrap');
        if ($bs == 'on') {
            $foot .= '
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <!-- These files are included by default by GeniXCMS. You can set it at the dashboard -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';

            $foot .= '<script>
            toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
            }
            </script>';
        }

        if (isset($GLOBALS['editor']) && $GLOBALS['editor'] == true) {
            Hooks::attach('footer_load_lib', array('Files', 'elfinderLib'));

            $height = $GLOBALS['editor_height'];

            // $url = (SMART_URL)? Site::$cdn . '/ajax/saveimage?token=' . TOKEN : Site::$cdn . "/index.php?ajax=saveimage&token=" . TOKEN;
            $url = Url::ajax('saveimage');
            $foot .= '

    <link href="'.self::$cdn.'assets/vendor/summernote/summernote-bs5.min.css" rel="stylesheet">
    <script src="'.self::$cdn.'assets/vendor/summernote/summernote-bs5.min.js"></script>
    <script src="'.self::$cdn.'assets/vendor/summernote/plugin/summernote-image-attributes.js"></script>
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

        return $foot;
    }
}

/* End of file Site.class.php */
/* Location: ./inc/lib/Site.class.php */
