<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
* filename : Site.class.php
* version : 0.0.1 pre
* build : 20141004
*/
//$GLOBALS['editor'] = 'on';
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
    <link href=\"".GX_URL."/assets/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
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
            \t<script src=\"".GX_URL."/assets/js/bootstrap.min.js\"></script>
            \t<script src=\"".GX_URL."/assets/js/ie10-viewport-bug-workaround.js\"></script>";
        }

        $fa = Options::get('use_fontawesome');
        if($fa == 'on'){
            $foot .= "
            \t<link href=\"http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css\" rel=\"stylesheet\">\n";
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

            \t<link href=\"".GX_URL."/assets/css/summernote.css\" rel=\"stylesheet\">
            \t<script src=\"".GX_URL."/assets/js/summernote.min.js\"></script>
            \t<script>
              \t$(document).ready(function() {
                \t$('textarea').summernote({
                    \theight: 300,
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
            \t<link href=\"".GX_URL."/assets/css/bootstrapValidator.min.css\" rel=\"stylesheet\">
            \t<script src=\"".GX_URL."/assets/js/bootstrapValidator.min.js\"></script>
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
}

/* End of file Site.class.php */
/* Location: ./inc/lib/Site.class.php */