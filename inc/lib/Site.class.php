<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20141004
* @version 0.0.1
* @link https://github.com/semplon/GeniXCMS
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/


class Site
{
    static $editors;
    static $data;

    public function __construct() {
        global $GLOBALS, $data;
        self::$editors =& $GLOBALS;
        self::$data =& $data;
    }

    /* Call all Website Meta at Header
    *
    */
    public static function meta($cont_title='', $cont_desc='', $pre =''){
        global $data;
        //print_r($data);
        //if(empty($data['posts'][0]->title)){ 

        if(is_array($data) && isset($data['posts'][0]->title)){
            
            $sitenamelength = strlen(Options::get('sitename'));
            $limit = 70-$sitenamelength-6;
            $cont_title = substr(Typo::Xclean(Typo::strip($data['posts'][0]->title)),0,$limit);
            $titlelength = strlen($data['posts'][0]->title);
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
    <!-- SEO: Title stripped 70chars for SEO Purpose -->
    <title>{$cont_title}".Options::get('sitename')."</title>
    <meta name=\"Keyword\" content=\"".Options::get('sitekeywords')."\">
    <!-- SEO: Description stripped 150chars for SEO Purpose -->
    <meta name=\"Description\" content=\"".self::desc($desc)."\">
    <meta name=\"Author\" content=\"Puguh Wijayanto | MetalGenix IT Solutions - www.metalgenix.com\">
    <meta name=\"Generator\" content=\"GeniXCMS\">
    <meta name=\"robots\" content=\"".Options::get('robots')."\">
    <meta name=\"revisit-after\" content=\" days\">
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
    <link href=\"".Options::get('siteurl')."/assets/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
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
            \t<script src=\"".Options::get('siteurl')."/assets/js/bootstrap.min.js\"></script>
            \t<script src=\"".Options::get('siteurl')."/assets/js/ie10-viewport-bug-workaround.js\"></script>";
        }

        $fa = Options::get('use_fontawesome');
        if($fa == 'on'){
            $foot .= "
            \t<link href=\"http://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css\" rel=\"stylesheet\">\n";
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

            \t<link href=\"".Options::get('siteurl')."/assets/css/summernote.css\" rel=\"stylesheet\">
            \t<script src=\"".Options::get('siteurl')."/assets/js/summernote.min.js\"></script>
            \t<script src=\"".Options::get('siteurl')."/assets/js/plugins/summernote-ext-fontstyle.js\"></script>
            \t<script src=\"".Options::get('siteurl')."/assets/js/plugins/summernote-ext-hello.js\"></script>
            \t<script src=\"".Options::get('siteurl')."/assets/js/plugins/summernote-ext-video.js\"></script>
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
            \t<link href=\"".Options::get('siteurl')."/assets/css/bootstrapValidator.min.css\" rel=\"stylesheet\">
            \t<script src=\"".Options::get('siteurl')."/assets/js/bootstrapValidator.min.js\"></script>
            ";

            $foot .= $GLOBALS['validator_js'];

        }

        
        echo $foot;
    }

    public static function desc($vars){
        if(!empty($vars)){
            $desc = substr(strip_tags(htmlspecialchars_decode($vars).". ".Options::get('sitedesc')),0,150);
        }else{
            $desc = substr(Options::get('sitedesc'),0,150);
        }
        
        return $desc;
    }

    public static function logo ($width='', $height='') {
        // check which logo is used, logourl or uploaded files.
        if( Options::get('is_logourl') == "on" && Options::get('logourl') != "" ) {
            $logo = "<img src=\"".Options::get('siteurl').Options::get('logourl')."\"
                    style=\"width: $width; height: $height; margin: 1px;\">";
        }elseif( Options::get('is_logourl') == "off" && Options::get('logo') != "" ){
            $logo = "<img src=\"".Options::get('siteurl').Options::get('logo')."\"
                    style=\"width: $width; height: $height; margin: 1px;\">";
        }else{
            $logo = "<span class=\"mg genixcms-logo\"></span>";
        }
        return $logo;
    }
}

/* End of file Site.class.php */
/* Location: ./inc/lib/Site.class.php */