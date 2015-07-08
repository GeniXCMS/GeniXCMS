<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.2 build date 20150313
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/



class Files
{
    public static function delTree($dir) { 
        $files = array_diff(scandir($dir), array('.','..')); 
        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file"); 
        } 
        return rmdir($dir);
    }

    public static function elfinderLib() {

        $html = "
    <script src=\"".Vendor::url()."/studio-42/elfinder/jquery/jquery-ui.min.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/jquery/ui-themes/smoothness/jquery-ui.min.css\" type=\"text/css\" media=\"screen\" title=\"no title\" charset=\"utf-8\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/elFinder.min.css\"      type=\"text/css\">

    <!-- elfinder core -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/elFinder.min.js\"></script>
    <!-- elfinder languages -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.en.js\"></script>
    <!-- elfinder 1.x connector API support -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/proxy/elFinderSupportVer1.js\"></script>

    <script>
        $(document).ready(function() {
            $('#elfinder').elfinder({
                url : '".Site::$url."/index.php?ajax=elfinder&token=".TOKEN."',
                height : '500',
                handlers : {
                    select : function(event, elfinderInstance) {
                        var selected = event.data.selected;
                    },
                    
                },
                
                lang : 'en',
                customData : {answer : 42},
                
            });
        });

        function elfinderDialog(){
            var fm = $('<div/>').dialogelfinder({
                url : '".Site::$url."/index.php?ajax=elfinder&token=".TOKEN."',
                lang : 'en',
                width : 840,
                height: 450,
                destroyOnClose : true,
                getFileCallback : function(files, fm) {
                    console.log(files);
                    $('.editor').summernote('editor.insertImage',files.url);
                },
                commandsOptions : {
                    getfile : {
                        oncomplete : 'close',
                        folders : false
                    }
                }
                
            }).dialogelfinder('instance');
        }

    </script>
        ";
        return $html;
    }
}