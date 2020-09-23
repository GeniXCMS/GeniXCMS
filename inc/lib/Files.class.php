<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.2 build date 20150313
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
class Files
{
    public static function delTree($dir)
    {
        try {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : @unlink("$dir/$file");
            }
            rmdir($dir);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function elfinderLib()
    {
        // $url = (SMART_URL)? Site::$url . '/ajax/elfinder?token=' . TOKEN : Site::$url . "/index.php?ajax=elfinder&token=" . TOKEN;
        $url = Url::ajax('elfinder');
        $html = '
    <!--<script src="'.Site::$url.'/assets/js/jquery-ui/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="'.Site::$url.'/assets/js/jquery-ui/jquery-ui.structure.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="'.Site::$url.'/assets/js/jquery-ui/jquery-ui.theme.min.css" type="text/css" media="screen" title="no title" charset="utf-8">-->
    <link rel="stylesheet" href="'.Vendor::url().'/studio-42/elfinder/css/elfinder.min.css"      type="text/css">
    <link rel="stylesheet" href="'.Site::$url.'/assets/css/theme-bootstrap-libreicons-svg.css"      type="text/css">

    <!-- elfinder core -->
    <script src="'.Vendor::url().'/studio-42/elfinder/js/elfinder.min.js"></script>
    <!-- elfinder 1.x connector API support -->
    <script src="'.Vendor::url()."/studio-42/elfinder/js/proxy/elFinderSupportVer1.js\"></script>

    <script>
        
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

    /**
     * A simple function to check file from bad codes.
     *
     * @param (string) $file - file path.
     * @return  (boolean)
     *
     * @author Yousef Ismaeil - Cliprz[at]gmail[dot]com.
     */
    public static function isClean($file)
    {
        $handle = fopen($file, 'r');
        if (self::isRemote($file)) {
            # code...
            if (self::remoteExist($file)) {
                $contents = fread($handle, 9064);
            }
        } else {
            if (file_exists($file)) {
                // $contents = file_get_contents($file);
//                $contents = fread($handle, filesize($file));
                $contents = fread($handle, 9064);
            }
        }

        if (preg_match('/(base64_|eval|system|shell_|exec|php_)/i', $contents)) {
            return false;
        } elseif (preg_match("#&\#x([0-9a-f]+);#i", $contents)) {
            return false;
        } elseif (preg_match('#&\#([0-9]+);#i', $contents)) {
            return false;
        } elseif (preg_match("#([a-z]*)=([\`\'\"]*)script:#iU", $contents)) {
            return false;
        } elseif (preg_match("#([a-z]*)=([\`\'\"]*)javascript:#iU", $contents)) {
            return false;
        } elseif (preg_match("#([a-z]*)=([\'\"]*)vbscript:#iU", $contents)) {
            return false;
        } elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU", $contents)) {
            return false;
        } elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU", $contents)) {
            return false;
        } elseif (preg_match('#</*(applet|link|style|script|iframe|frame|frameset|html|body|title|div|p|form)*>#i', $contents)) {
            return false;
        } elseif (preg_match('#<\?(.*)\?>#i', $contents)) {
            return false;
        } else {
            return true;
        }
    }

    public static function remoteExist($url)
    {
        $curl = curl_init($url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //do request
        $result = curl_exec($curl);

        $ret = false;

        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $ret = true;
            }
        }

        curl_close($curl);

        return $ret;
    }

    public static function isRemote($path)
    {
        if (strpos($path, '//') !== false) {
            if (strpos($path, '//') >= max(strpos($path, '.'), strpos($path, '/'))) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}
