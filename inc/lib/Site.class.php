<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141004
* @version 0.0.3
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

        $meta = "
    <!--// Start Meta: Generated Automaticaly by GeniXCMS -->
    <meta charset=\"".Options::get('charset')."\">";
        $meta .= "
    <!-- SEO: Title stripped 70chars for SEO Purpose -->
    <title>{$cont_title}".self::$name."</title>
    <meta name=\"Keyword\" content=\"".self::$key."\">
    <!-- SEO: Description stripped 150chars for SEO Purpose -->
    <meta name=\"Description\" content=\"".self::desc($desc)."\">";
    if (isset($data['posts'][0]->author) && !isset($data['posts'][1]->author)) {
         $meta .= "
    <meta name=\"Author\" content=\"{$data['posts'][0]->author}\">";
    }
        $meta .= "
    <meta name=\"Generator\" content=\"GeniXCMS ".System::v()."\">
    <meta name=\"robots\" content=\"".Options::get('robots')."\">
    <link rel=\"shortcut icon\" href=\"".Options::get('siteicon')."\" />            
        ";
        
        $meta .= "
    <!-- Generated Automaticaly by GeniXCMS :End Meta //-->";
        echo $meta;
    }

    public static function footer(){
       //global $editors;
        //echo $GLOBALS['editor'].' one '. self::$editors;
        $foot ="";
        $bs = Options::get('use_bootstrap');
        if($bs == 'on'){
            $foot .= "
    <link href=\"".self::$url."/assets/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
            $foot .= "
    <link href=\"".self::$url."/assets/css/bootstrap-theme.min.css\" rel=\"stylesheet\">\n";
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
            \t<script src=\"".self::$url."/assets/js/bootstrap.min.js\"></script>
            \t<script src=\"".self::$url."/assets/js/ie10-viewport-bug-workaround.js\"></script>";
        }

        $fa = Options::get('use_fontawesome');
        if($fa == 'on'){
            $foot .= "
            \t<link href=\"https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css\" rel=\"stylesheet\">\n";
        }

        if(isset($GLOBALS['editor']) && $GLOBALS['editor'] == true){
            $foot .= "
            \t<!-- include codemirror (codemirror.css, codemirror.js, xml.js, formatting.js)-->
            \t<link rel=\"stylesheet\" type=\"text/css\" href=\"http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.min.css\" />
            \t<link rel=\"stylesheet\" href=\"http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/blackboard.min.css\">
            \t<link rel=\"stylesheet\" href=\"http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/monokai.min.css\">
            \t<script type=\"text/javascript\" src=\"http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js\"></script>
            \t<script src=\"http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.min.js\"></script>
            \t<script src=\"http://cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.min.js\"></script>

            \t<link href=\"".self::$url."/assets/css/summernote.css\" rel=\"stylesheet\">
            \t<script src=\"".self::$url."/assets/js/summernote.min.js\"></script>
            \t<script src=\"".self::$url."/assets/js/plugins/summernote-ext-fontstyle.js\"></script>
            \t<script src=\"".self::$url."/assets/js/plugins/summernote-ext-video.js\"></script>
            \t<script>
              \t$(document).ready(function() {
                \t$('.editor').summernote({
                    \theight: 300,
                    \ttoolbar: [
                            \t['style', ['style']],
                            \t['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                            \t['fontname', ['fontname']],
                            \t['fontsize', ['fontsize']],
                            \t['color', ['color']],
                            \t['para', ['ul', 'ol', 'paragraph']],
                            \t['height', ['height']],
                            \t['table', ['table']],
                            \t['insert', ['link', 'picture', 'video', 'hr']],
                            \t['view', ['fullscreen', 'codeview']],
                            \t['help', ['help']]
                        \t],
                    \tonImageUpload: function(files, editor, welEditable) {
                            \tsendFile(files[0],editor,welEditable);
                        \t}
                \t});
                
                \tfunction sendFile(file,editor,welEditable) {
                  \tdata = new FormData();
                  \tdata.append(\"file\", file);
                    \t$.ajax({
                        \turl: \"saveimage.php\",
                        \tdata: data,
                        \tcache: false,
                        \tcontentType: false,
                        \tprocessData: false,
                        \ttype: 'POST',
                        \tsuccess: function(data){
                        \talert(data);
                          \teditor.insertImage(welEditable, data);
                        \t},
                       \terror: function(jqXHR, textStatus, errorThrown) {
                         \tconsole.log(textStatus+\" \"+errorThrown);
                       \t}
                    \t});
                  \t}

                \t $(\".alert\").alert();
              \t});


            \t</script>
              ";
        }

        if(isset($GLOBALS['validator']) && $GLOBALS['validator'] == true){
            $foot .= "
            \t<link href=\"".self::$url."/assets/css/bootstrapValidator.min.css\" rel=\"stylesheet\">
            \t<script src=\"".self::$url."/assets/js/bootstrapValidator.min.js\"></script>
            ";

            $foot .= $GLOBALS['validator_js'];

        }

        
        echo $foot;
    }

    public static function desc($vars){
        if(!empty($vars)){
            $desc = substr(strip_tags(htmlspecialchars_decode($vars).". ".self::$desc),0,150);
        }else{
            $desc = substr(self::$desc,0,150);
        }
        
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