<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.2 build date 20150313
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
class Files
{
    public static function delTree($dir)
    {
        try {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ?self::delTree("$dir/$file") : @unlink("$dir/$file");
            }
            rmdir($dir);
        }
        catch (Exception $e) {
            return false;
        }
    }

    public static function elfinderLib()
    {
        $url = Url::ajax('elfinder');
        $vendorUrl = Vendor::url();
        $editorType = $GLOBALS['editor_type'] ?? Options::v('editor_type') ?: 'summernote';

        $html = <<<HTML
    <link rel="stylesheet" href="{$vendorUrl}/studio-42/elfinder/css/elfinder.min.css" type="text/css">
    <link rel="stylesheet" href="{$vendorUrl}/studio-42/elfinder/css/theme.css" type="text/css">
    <style>
        :root {
            --elfinder-primary: #3b82f6;
            --elfinder-radius: 10px;
        }
        .elfinder { border: 1px solid #e2e8f0 !important; border-radius: var(--elfinder-radius) !important; font-family: 'Outfit', sans-serif !important; box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important; background: #fff !important; }
        .elfinder-navbar { background: #f8fafc !important; border-right: 1px solid #e2e8f0 !important; }
        .elfinder-toolbar { background: #fff !important; border-bottom: 1px solid #e2e8f0 !important; padding: 10px !important; }
        .elfinder-button { background: #f1f5f9 !important; border: 1px solid #e2e8f0 !important; border-radius: 6px !important; margin-right: 5px !important; }
        .elfinder-button:hover { background: #e2e8f0 !important; }
        .elfinder-button-active { background: var(--elfinder-primary) !important; color: #fff !important; }
        .elfinder-cwd-view-icons .elfinder-cwd-file .elfinder-cwd-filename { border-radius: 4px !important; font-size: 13px !important; margin-top: 5px !important; }
        .ui-state-hover, .ui-widget-content .ui-state-hover { border-color: #cbd5e1 !important; background: #f1f5f9 !important; }
        .ui-state-active, .ui-widget-content .ui-state-active { background: var(--elfinder-primary) !important; border-color: var(--elfinder-primary) !important; color: #fff !important; }
        .elfinder-drag-helper { border-radius: var(--elfinder-radius) !important; }
        .elfinder-statusbar { background: #fff !important; border-top: 1px solid #e2e8f0 !important; padding: 5px 15px !important; font-size: 12px !important; color: #64748b !important; }
        .elfinder-navbar .ui-state-active { background: var(--elfinder-primary) !important; font-weight: 600 !important; }
        .elfinder-navbar .ui-state-hover { background: #f1f5f9 !important; color: #1e293b !important; border-width: 0 !important; }
        .elfinder-tree .elfinder-navbar-arrow { font-family: "bootstrap-icons" !important; speak: none; font-style: normal; font-weight: normal; font-variant: normal; text-transform: none; line-height: 1; -webkit-font-smoothing: antialiased; }
        .elfinder-tree .elfinder-navbar-arrow:before { content: "\f282"; }
        .elfinder-tree .ui-state-active .elfinder-navbar-arrow:before { color: #fff !important; }
        .elfinder-statusbar .elfinder-stat-selected { color: var(--elfinder-primary) !important; font-weight: 700 !important; }
        .elfinder-button-search input { border-radius: 20px !important; padding: 4px 12px !important; background: #f8fafc !important; border: 1px solid #e2e8f0 !important; }
        .dialogelfinder { border-radius: var(--elfinder-radius) !important; overflow: hidden !important; border: 0 !important; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important; }
        .ui-dialog-titlebar { background: #1e293b !important; color: #fff !important; border: 0 !important; border-radius: 0 !important; padding: 15px 20px !important; font-weight: 600 !important; }
        .ui-dialog-titlebar-close { filter: invert(1) !important; top: 18px !important; right: 20px !important; }
        
        /* Context Menu Contrast Fix */
        .elfinder-contextmenu { border-radius: 8px !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; padding: 5px 0 !important; background: #fff !important; }
        .elfinder-contextmenu .elfinder-contextmenu-item { border-radius: 0 !important; padding: 2px 0 !important; transition: none !important; }
        .elfinder-contextmenu .elfinder-contextmenu-item .elfinder-contextmenu-label { padding: 4px 20px 4px 35px !important; color: #1e293b !important; }
        .elfinder-contextmenu .elfinder-contextmenu-item.ui-state-hover { background: var(--elfinder-primary) !important; color: #fff !important; border: 0 !important; }
        .elfinder-contextmenu .ui-state-hover .elfinder-contextmenu-label { color: #fff !important; }
        .elfinder-contextmenu .ui-state-hover .elfinder-contextmenu-arrow { border-left-color: #fff !important; }
        .elfinder-contextmenu-item .elfinder-button-icon { left: 10px !important; top: 50% !important; transform: translateY(-50%) !important; opacity: 0.8 !important; }
        .elfinder-contextmenu .ui-state-hover .elfinder-button-icon { filter: brightness(0) invert(1) !important; opacity: 1 !important; }
        .elfinder-contextmenu-separator { background-color: #f1f5f9 !important; height: 1px !important; margin: 5px 0 !important; }
    </style>

    <!-- elfinder core -->
    <script src="{$vendorUrl}/studio-42/elfinder/js/elfinder.min.js"></script>
    <script src="{$vendorUrl}/studio-42/elfinder/js/proxy/elFinderSupportVer1.js"></script>

    <script shadow>
        $(document).ready(function() {
            // Compatibility shim for jQuery UI 1.12+ (deprecated buttonset)
            if ($.fn.buttonset === undefined) {
                $.fn.buttonset = function() {
                    return this.each(function() {
                        var el = $(this);
                        if (el.is("div")) {
                            el.controlgroup();
                        } else {
                            el.checkboxradio();
                        }
                    });
                };
            }

            $('#elfinder').elfinder({
                url : '{$url}',
                baseUrl : '{$vendorUrl}/studio-42/elfinder/',
                height : '100%',
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
            var gxEditorType = '{$editorType}';
            var fm = $('<div/>').dialogelfinder({
                url : '{$url}',
                lang : 'en',
                width : 840,
                height: 450,
                destroyOnClose : true,
                getFileCallback : function(files, fm) {
                    console.log(files);
                    if (gxEditorType === 'editorjs') {
                        // Insert image block into active EditorJS instance
                        if (window.__gxEditors) {
                            var idx = Object.keys(window.__gxEditors)[0];
                            if (window.__gxEditors[idx]) {
                                window.__gxEditors[idx].blocks.insert('image', {
                                    file: { url: files.url },
                                    caption: '',
                                    withBorder: false,
                                    withBackground: false,
                                    stretched: false
                                });
                            }
                        }
                    } else {
                        // Summernote
                        $('.editor').summernote('editor.insertImage', files.url);
                    }
                },
                commandsOptions : {
                    getfile : {
                        oncomplete : 'close',
                        folders : false
                    }
                }
            }).dialogelfinder('instance');
        }

        function elfinderDialog2() {
            var fm = $('<div/>').dialogelfinder({
                url : '{$url}',
                lang : 'en',
                width : 840,
                height: 450,
                destroyOnClose : true,
                getFileCallback : function(files, fm) {
                    $('#post_image').val(files.url);
                    $('#post_image_preview').attr('src', files.url);
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
HTML;

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
    public static function isClean($file): bool
    {
        if (!is_string($file) || empty($file)) {
            return false;
        }

        if (self::isRemote($file)) {
            if (!self::remoteExist($file)) {
                return false;
            }
        }
        elseif (!file_exists($file)) {
            return false;
        }

        $handle = @fopen($file, 'r');
        if (!$handle) {
            return false;
        }

        if (self::isRemote($file)) {
            # code...
            if (self::remoteExist($file)) {
                $contents = fread($handle, 9064);
            }
        }
        else {
            if (file_exists($file)) {
                $contents = fread($handle, 9064);
            }
        }

        fclose($handle);

        if (preg_match('/(base64_|eval|system|shell_|exec|php_)/i', $contents)) {
            return false;
        }
        elseif (preg_match("#&\#x([0-9a-f]+);#i", $contents)) {
            return false;
        }
        elseif (preg_match('#&\#([0-9]+);#i', $contents)) {
            return false;
        }
        elseif (preg_match("#([a-z]*)=([\`\'\"]*)script:#iU", $contents)) {
            return false;
        }
        elseif (preg_match("#([a-z]*)=([\`\'\"]*)javascript:#iU", $contents)) {
            return false;
        }
        elseif (preg_match("#([a-z]*)=([\'\"]*)vbscript:#iU", $contents)) {
            return false;
        }
        elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU", $contents)) {
            return false;
        }
        elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU", $contents)) {
            return false;
        }
        elseif (preg_match('#</*(applet|link|style|script|iframe|frame|frameset|html|body|title|div|p|form)*>#i', $contents)) {
            return false;
        }
        elseif (preg_match('#<\?(.*)\?>#i', $contents)) {
            return false;
        }
        else {
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
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }
}
