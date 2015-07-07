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
    <script src=\"".Vendor::url()."/studio-42/elfinder/jquery/jquery-ui-1.10.1.custom.min.js\" type=\"text/javascript\" charset=\"utf-8\"></script>
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/jquery/ui-themes/smoothness/jquery-ui-1.10.1.custom.css\" type=\"text/css\" media=\"screen\" title=\"no title\" charset=\"utf-8\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/common.css\"      type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/dialog.css\"      type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/toolbar.css\"     type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/navbar.css\"      type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/statusbar.css\"   type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/contextmenu.css\" type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/cwd.css\"         type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/quicklook.css\"   type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/commands.css\"    type=\"text/css\">

    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/fonts.css\"       type=\"text/css\">
    <link rel=\"stylesheet\" href=\"".Vendor::url()."/studio-42/elfinder/css/theme.css\"       type=\"text/css\">

    <!-- elfinder core -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/elFinder.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/elFinder.version.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/jquery.elfinder.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/elFinder.resources.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/elFinder.options.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/elFinder.history.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/elFinder.command.js\"></script>

    <!-- elfinder ui -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/overlay.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/workzone.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/navbar.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/dialog.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/tree.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/cwd.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/toolbar.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/button.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/uploadButton.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/viewbutton.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/searchbutton.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/sortbutton.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/panel.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/contextmenu.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/path.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/stat.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/ui/places.js\"></script>

    <!-- elfinder commands -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/back.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/forward.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/reload.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/up.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/home.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/copy.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/cut.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/paste.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/open.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/rm.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/info.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/duplicate.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/rename.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/help.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/getfile.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/mkdir.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/mkfile.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/upload.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/download.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/edit.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/quicklook.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/quicklook.plugins.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/extract.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/archive.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/search.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/view.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/resize.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/sort.js\"></script>    
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/commands/netmount.js\"></script>    

    <!-- elfinder languages -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.ar.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.bg.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.ca.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.cs.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.de.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.el.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.en.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.es.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.fa.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.fr.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.hu.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.it.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.jp.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.ko.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.nl.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.no.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.pl.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.pt_BR.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.ru.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.sl.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.sv.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.tr.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.zh_CN.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.zh_TW.js\"></script>
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/i18n/elfinder.vi.js\"></script>

    <!-- elfinder dialog -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/jquery.dialogelfinder.js\"></script>

    <!-- elfinder 1.x connector API support -->
    <script src=\"".Vendor::url()."/studio-42/elfinder/js/proxy/elFinderSupportVer1.js\"></script>

    <script>
        $(document).ready(function() {
            $('#elfinder').elfinder({
                url : '".Vendor::url()."/studio-42/elfinder/php/connector.minimal.php',
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
    </script>
        ";
        return $html;
    }
}