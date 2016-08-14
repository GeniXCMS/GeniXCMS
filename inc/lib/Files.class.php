<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.2 build date 20150313
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Files
{
    public static function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }

    public static function elfinderLib()
    {
        // $url = (SMART_URL)? Site::$url . '/ajax/elfinder?token=' . TOKEN : Site::$url . "/index.php?ajax=elfinder&token=" . TOKEN;
        $url = Url::ajax('elfinder');
        $html = '
    <script src="'.Site::$url.'/assets/js/jquery-ui/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="'.Site::$url.'/assets/js/jquery-ui/jquery-ui.structure.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="'.Site::$url.'/assets/js/jquery-ui/jquery-ui.theme.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="'.Vendor::url().'/studio-42/elfinder/css/elfinder.min.css"      type="text/css">
    <link rel="stylesheet" href="'.Site::$url.'/assets/css/theme-bootstrap-libreicons-svg.css"      type="text/css">

    <!-- elfinder core -->
    <script src="'.Vendor::url().'/studio-42/elfinder/js/elfinder.min.js"></script>
    <!-- elfinder 1.x connector API support -->
    <script src="'.Vendor::url()."/studio-42/elfinder/js/proxy/elFinderSupportVer1.js\"></script>

    <script>
        $(document).ready(function() {
            $('#elfinder').elfinder({
                url : '".$url."',
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

        function elfinderDialog() {
            var fm = $('<div/>').dialogelfinder({
                url : '".$url."',
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
