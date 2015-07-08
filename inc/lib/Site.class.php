<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
*
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141004
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class Site
{
    static $editors;
    static $data;
    static $url;
    static $domain;
    static $name;
    static $key;
    static $desc;
    static $email;
    static $slogan;

    public function __construct() {
        global $GLOBALS, $data;
        self::$editors =& $GLOBALS;
        self::$data =& $data;
        self::$url = Options::get('siteurl');
        self::$domain = Options::get('sitedomain');
        self::$name = Options::get('sitename');
        self::$key = Options::get('sitekeywords');
        self::$desc = Options::get('sitedesc');
        self::$email = Options::get('siteemail');
        self::$slogan = Options::get('siteslogan');
    }

    /* Call all Website Meta at Header
    *
    */
    public static function meta($cont_title='', $cont_desc='', $pre =''){
        global $data;
        //print_r($data);
        //if(empty($data['posts'][0]->title)){

        if( is_array($data) ){

            $sitenamelength = strlen(self::$name);
            $limit = 70-$sitenamelength-6;
            if(isset($data['sitetitle'])){
                $cont_title = substr(Typo::Xclean(Typo::strip($data['sitetitle'])),0,$limit);
                $titlelength = strlen($data['sitetitle']);
            }elseif(isset($data['posts'][0]->title) && !isset($data['posts'][1]->title)){
                $cont_title = substr(Typo::Xclean(Typo::strip($data['posts'][0]->title)),0,$limit);
                $titlelength = strlen($data['posts'][0]->title);
            }else{
                $cont_title = substr(Typo::Xclean(Typo::strip(Options::get('siteslogan'))),0,$limit);
                $titlelength = strlen(Options::get('siteslogan'));
            }
            if($titlelength > $limit+3) { $dotted = "...";} else {$dotted = "";}
            $cont_title = "{$pre} {$cont_title}{$dotted} - ";
        }else{
            $cont_title = "";
        }
        if(is_array($data)  && isset($data['posts'][0]->content)){
            $desc = Typo::strip($data['posts'][0]->content);
        }else{
            $desc = "";
        }
        $cont_title = Hooks::filter('site_title_filter', $cont_title);
        $keyword = Hooks::filter('site_key_filter', self::$key);
        echo "
    <!--// Start Meta: Generated Automaticaly by GeniXCMS -->
    <meta charset=\"".Options::get('charset')."\">";
        echo "
    <!-- SEO: Title stripped 70chars for SEO Purpose -->
    <title>{$cont_title}".self::$name."</title>
    <meta name=\"Keyword\" content=\"".$keyword."\">
    <!-- SEO: Description stripped 150chars for SEO Purpose -->
    <meta name=\"Description\" content=\"".self::desc($desc)."\">";
    if (isset($data['posts'][0]->author) && !isset($data['posts'][1]->author)) {
         echo "
    <meta name=\"Author\" content=\"{$data['posts'][0]->author}\">";
    }
        echo "
    <meta name=\"Generator\" content=\"GeniXCMS ".System::v()."\">
    <meta name=\"robots\" content=\"".Options::get('robots')."\">
    <link rel=\"shortcut icon\" href=\"".Options::get('siteicon')."\" />
        ";
        echo Hooks::run('header_load_meta', $data);
        echo "
    <!-- Generated Automaticaly by GeniXCMS :End Meta //-->";
        // echo $meta;

    }



    public static function footer(){
        global $data;
        //echo $GLOBALS['editor'].' one '. self::$editors;
        $foot ="";
        $bs = Options::get('use_bootstrap');
        if($bs == 'on'){
            $foot .= "
    <link href=\"".self::$url."/assets/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
        }

        $jquery = Options::get('use_jquery');
        $jquery_v = Options::get('jquery_v');
        if($jquery == 'on'){
            $foot .= "
    <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/".$jquery_v."/jquery.min.js\"></script>";
        }

        $bs = Options::get('use_bootstrap');
        if($bs == 'on'){
            $foot .= "
            <!-- These files are included by default by GeniXCMS. You can set it at the dashboard -->
            <script src=\"".self::$url."/assets/js/bootstrap.min.js\"></script>
            <script src=\"".self::$url."/assets/js/ie10-viewport-bug-workaround.js\"></script>";
        }

        $fa = Options::get('use_fontawesome');
        if($fa == 'on'){
            $foot .= "
            <link href=\"https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css\" rel=\"stylesheet\">\n";
        }

        if(isset($GLOBALS['editor']) && $GLOBALS['editor'] == true){
            Hooks::attach('footer_load_lib', array('Files','elfinderLib'));
            if ($GLOBALS['editor_mode'] == 'light') {
                $toolbar = "['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore']],
                    ['view', ['fullscreen']]";
            }elseif ($GLOBALS['editor_mode'] == 'full') {
                $toolbar = "['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr', 'readmore']],
                    ['genixcms', ['elfinder']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']]";
            }

            $foot .= "

    <link href=\"".self::$url."/assets/css/summernote.css\" rel=\"stylesheet\">
    <script src=\"".self::$url."/assets/js/summernote.min.js\"></script>
    <script src=\"".self::$url."/assets/js/plugins/summernote-ext-hint.js\"></script>
    <script src=\"".self::$url."/assets/js/plugins/summernote-ext-video.js\"></script>
    <script src=\"".self::$url."/assets/js/plugins/summernote-ext-genixcms.js\"></script>
    <script>
      $(document).ready(function() {
        $('.editor').summernote({
            height: 300,
            toolbar: [
                    ".$toolbar."
                ],
            onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0],editor,welEditable);
                }
        });

        function sendFile(file,editor,welEditable) {
          data = new FormData();
          data.append(\"file\", file);
            $.ajax({
                url: \"".Site::$url."/index.php?ajax=saveimage&token=".TOKEN."\",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                //alert(data);
                  $('.editor').summernote('editor.insertImage', data);
                },
               error: function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus+\" \"+errorThrown);
               }
            });
          }

         $(\".alert\").alert();
      });


    </script>
              ";
        }

        if(isset($GLOBALS['validator']) && $GLOBALS['validator'] == true){
            $foot .= "
            <link href=\"".self::$url."/assets/css/bootstrapValidator.min.css\" rel=\"stylesheet\">
            <script src=\"".self::$url."/assets/js/bootstrapValidator.min.js\"></script>
            ";

            $foot .= $GLOBALS['validator_js'];

        }


        echo $foot;
        echo Hooks::run('footer_load_lib', $data);
    }

    public static function desc($vars){
        if(!empty($vars)){
            $desc = substr(strip_tags(htmlspecialchars_decode($vars).". ".self::$desc),0,150);
        }else{
            $desc = substr(self::$desc,0,150);
        }
        $desc = Hooks::filter('site_desc_filter', $desc);
        return $desc;
    }

    public static function logo ($width='', $height='') {
        // check which logo is used, logourl or uploaded files.
        if( Options::get('is_logourl') == "on" && Options::get('logourl') != "" ) {
            $logo = "<img src=\"".self::$url.Options::get('logourl')."\"
                    style=\"width: $width; height: $height; margin: 1px;\">";
        }elseif( Options::get('is_logourl') == "off" && Options::get('logo') != "" ){
            $logo = "<img src=\"".self::$url.Options::get('logo')."\"
                    style=\"width: $width; height: $height; margin: 1px;\">";
        }else{
            $logo = "<span class=\"mg genixcms-logo\"></span>";
        }
        return $logo;
    }

    public static function generated(){
        $end_time = microtime(TRUE);
        $time_taken = $end_time - $GLOBALS['start_time'];
        $time_taken = round($time_taken,5);
        echo '<center><small>Page generated in '.$time_taken.' seconds.</small></center>';
    }
}

/* End of file Site.class.php */
/* Location: ./inc/lib/Site.class.php */
