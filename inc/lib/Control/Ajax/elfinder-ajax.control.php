<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
$data = Router::scrap($param);
//print_r($data);

$gettoken = (SMART_URL) ? $data['token'] : Typo::cleanX($_GET['token']);
$token = (true === Token::validate($gettoken, true)) ? $gettoken: '';
$url = Site::canonical();
if ($token != '' && Http::validateUrl($url)) {
    $vendorPath = Vendor::path('studio-42/elfinder');
    include_once $vendorPath.'php/elFinderConnector.class.php';
    include_once $vendorPath.'php/elFinder.class.php';
    include_once $vendorPath.'php/elFinderVolumeDriver.class.php';
    include_once $vendorPath.'php/elFinderVolumeLocalFileSystem.class.php';
    // Required for MySQL storage connector
    // include_once $vendorPath.'php/elFinderVolumeMySQL.class.php';
    // Required for FTP connector support
    // include_once $vendorPath.'php/elFinderVolumeFTP.class.php';

    /**
     * Simple function to demonstrate how to control file access using "accessControl" callback.
     * This method will disable accessing files/folders starting from '.' (dot).
     *
     * @param string $attr attribute name (read|write|locked|hidden)
     * @param string $path file path relative to volume root directory started with directory separator
     *
     * @return bool|null
     **/
    function access($attr, $path, $data, $volume)
    {
        return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
            ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
            : null;                                    // else elFinder decide it itself
    }

    function uploadPost($cmd, $result, $args, $elfinder)
    {
        $log = sprintf('[%s] %s:', date('r'), strtoupper($cmd));
        foreach ($result as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $data = array();
            if (in_array($key, array('error', 'warning'))) {
                array_push($data, implode(' ', $value));
            } else {
                if (is_array($value)) { // changes made to files
                    foreach ($value as $file) {
                        $filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
                        // array_push($data, $filepath);

                        if (Image::isPng($filepath)) {

                            if (Files::isClean($filepath)){
                                @Image::compressPng($filepath);
                            } else {
                                // @unlink($filepath);
                            }
                        } elseif (Image::isJpg($filepath)) {
                            if (!Files::isClean($filepath)){
                                // @unlink($filepath);
                            } else {
                                @Image::compressJpg($filepath);
                            }
                        }

                        $mime_type = mime_content_type($filepath);

                        $media_autoresize_image = Options::v('media_autoresize_image');
                        $media_autoresize_width = Options::v('media_autoresize_width');
                        if( $media_autoresize_image == "on" ) {
                            @Image::resize($filepath, $filepath, $media_autoresize_width, $media_autoresize_width);
                        }

                        $media_autogenerate_webp = Options::v('media_autogenerate_webp');

                        if( $media_autogenerate_webp == "on" && $mime_type != 'image/webp' ) {
                            @Image::convertWebp($filepath);
                        }                        
                        
                    }
                } else { // other value (ex. header)
                    

                    // if (Image::isPng($value)) {
                    //     if (!Files::isClean($value)){
                    //         // unlink($value);
                    //     } else {
                    //         Image::compressPng($value);
                    //     }
                    // } elseif (Image::isJpg($value)) {
                    //     if (!Files::isClean($value)){
                    //         // unlink($value);
                    //     } else {
                    //         Image::compressJpg($value);
                    //     }
                    // }
                    // Image::convertWebp($value);
                    // array_push($data, $value);
                }
            }
            // $log .= sprintf(' %s(%s)', $key, implode(', ', $data));
        }

        
    }

    // set path for specific access
    // admin
    if (User::access(0)) {
        $path = 'assets/';
        $allowed = array('image', 'audio', 'video', 'text/plain',
            'text/javascript', 'text/css', 'text/html', );
        $tmbpath = GX_PATH.'/assets/cache/thumbs/';
    } elseif (User::access(1)) {
        $path = 'assets/';
        $allowed = array('image', 'audio', 'video');
        $tmbpath = GX_PATH.'/assets/cache/thumbs/';
    } else {
        $path = 'assets/media/';
        $allowed = array('image', 'audio', 'video');
        $tmbpath = GX_PATH.'/assets/media/cache/thumbs/';
    }
    // Documentation for connector options:
    // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
    $opts = array(
        // 'debug' => true,
        'bind' => array(
            'upload' => 'uploadPost',
            'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre ls.pre' => array(
                'Plugin.Normalizer.cmdPreprocess',
                'Plugin.Sanitizer.cmdPreprocess'
            ),  
            'ls' => array(
                'Plugin.Normalizer.cmdPostprocess',
                'Plugin.Sanitizer.cmdPostprocess'
            ),
            'upload.presave' => array(
                'Plugin.AutoResize.onUpLoadPreSave',
                'Plugin.Normalizer.onUpLoadPreSave',
                'Plugin.Sanitizer.onUpLoadPreSave'
            ),
        ),
        'roots' => array(
            array(
                'driver' => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
                'path' => GX_PATH.'/'.$path,         // path to files (REQUIRED)
                'URL' => Url::thumb($path), //Site::$url.$path, // URL to files (REQUIRED)
                'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)
                'uploadAllow' => $allowed,
                'uploadDeny' => array('application'),
                'uploadOrder' => array('allow', 'deny'),
                'alias' => 'Home',
                'tmbPath' => $tmbpath,
                'plugin' => array(
                    'AutoResize' => array(
                        'enable' => false,
                        'maxWidth'  => 1000,
                        'maxHeight'  => 1000,
                        'quality' => 30,
                        'forceEffect' => true,
                    ),
                    'Sanitizer' => array(
                        'enable' => true,
                        'targets'  => array('\\','/',':','*','?','"','<','>','|','(',')'), // target chars
                        'replace'  => '_', // replace to this
                        'callBack' => null // Or @callable sanitize function
                    ),
                     'Normalizer' => array(
                        'convmap' => array(
                            ' ' => '_',
                            ',' => '_',
                            '-' => '_',
                            '^' => '_',
                            'à' => 'a',
                            'ä' => 'a',
                            'é' => 'e',
                            'è' => 'e',
                            'ü' => 'u',
                            'ö' => 'o'
                        )
                    ),
                ),
            ),
        ),
    );

    // run elFinder
    $connector = new elFinderConnector(new elFinder($opts));
    $connector->run();
}else{
    echo json_encode(array( 'error' => $_SERVER['REQUEST_URI']));
}
// echo "TOKEN EXIST";
