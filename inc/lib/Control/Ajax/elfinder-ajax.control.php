<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - elFinder AJAX
 * 
 * @since 2.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
$data = Router::scrap($param);
//print_r($data);

$gettoken = (SMART_URL) ? $data['token'] : Typo::cleanX($_GET['token']);
$token = (true === Token::validate($gettoken, true)) ? $gettoken : '';
$url = Site::canonical();
if ($token != '') {
    $vendorPath = Vendor::path('studio-42/elfinder');
    include_once $vendorPath . 'php/elFinderConnector.class.php';
    include_once $vendorPath . 'php/elFinder.class.php';
    include_once $vendorPath . 'php/elFinderVolumeDriver.class.php';
    include_once $vendorPath . 'php/elFinderVolumeLocalFileSystem.class.php';
    include_once $vendorPath . 'php/elFinderPlugin.php';
    // Load Core Plugins
    include_once $vendorPath . 'php/plugins/AutoResize/plugin.php';
    include_once $vendorPath . 'php/plugins/Normalizer/plugin.php';
    include_once $vendorPath . 'php/plugins/Sanitizer/plugin.php';
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
        return strpos(basename($path), '.') === 0 // if file/folder begins with '.' (dot)
            ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
            : null; // else elFinder decide it itself
    }

    function autoSortUpload(&$thash, &$name, $src, $elfinder, $volume)
    {
        if (isset($_GET['auto_sort'])) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $target_sub = '';

            // Admin root is assets/, normal user is assets/media/
            $user_root = (User::access(0) || User::access(1)) ? 'media/' : '';

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'])) {
                $target_sub = $user_root . 'images';
            } elseif (in_array($ext, ['mp4', 'm4v', 'webm', 'ogg', 'mov', 'avi', 'mkv'])) {
                $target_sub = $user_root . 'videos';
            } elseif (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'flac'])) {
                $target_sub = $user_root . 'audios';
            }

            if ($target_sub != '') {
                $new_hash = $volume->getHash($target_sub);
                if ($new_hash) {
                    $thash = $new_hash;
                }
            }
        }
    }

    function uploadPost($cmd, &$result, $args, $elfinder)
    {
        $log = sprintf('[%s] %s:', date('r'), strtoupper($cmd));
        foreach ($result as $key => &$value) {
            if (empty($value)) {
                continue;
            }
            $data = array();
            if (in_array($key, array('error', 'warning'))) {
                array_push($data, implode(' ', $value));
            } else {
                if (is_array($value)) { // changes made to files
                    foreach ($value as &$file) {
                        $filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
                        // array_push($data, $filepath);

                        $mime_type = mime_content_type($filepath);

                        $media_autoresize_image = Options::v('media_autoresize_image');
                        $media_autoresize_width = Options::v('media_autoresize_width');
                        if ($media_autoresize_image == "on") {
                            @Image::resize($filepath, $filepath, $media_autoresize_width, $media_autoresize_width);
                        }

                        $media_autogenerate_webp = Options::v('media_autogenerate_webp');

                        if ($media_autogenerate_webp == "on" && $mime_type != 'image/webp') {
                            $webpFile = @Image::convertWebp($filepath);
                            if ($webpFile && isset($file['url'])) {
                                $file['url'] = preg_replace('/\.(jpg|jpeg|png|gif|bmp)$/i', '.webp', $file['url']);
                            }
                        }

                        if (isset($file['url'])) {
                            $file['url'] = str_replace(':/', '://', str_replace('//', '/', $file['url']));
                        }




                    }
                }
            }
        }
    }

    $storageBackend = Options::v('media_storage_backend') ?: 'local';

    // set path for specific access
    // admin
    if (User::access(0)) {
        $path = 'assets/';
        $allowed = array(
            'image',
            'audio',
            'video',
            'text/plain',
            'text/javascript',
            'text/css',
            'text/html',
        );
        $tmbpath = GX_PATH . '/assets/cache/thumbs/';
    } elseif (User::access(1)) {
        $path = 'assets/';
        $allowed = array('image', 'audio', 'video');
        $tmbpath = GX_PATH . '/assets/cache/thumbs/';
    } else {
        $path = (Options::v('media_local_path') ?: 'assets/media/');
        $allowed = array('image', 'audio', 'video');
        $tmbpath = GX_PATH . '/assets/media/cache/thumbs/';
    }

    if ($storageBackend == 'local') {
        $rootConfig = array(
            'driver' => 'LocalFileSystem',
            'path' => rtrim(GX_PATH . '/' . $path, '/'),
            'URL' => rtrim(Url::thumb($path), '/') . '/',
            'accessControl' => 'access',
            'uploadAllow' => $allowed,
            'uploadDeny' => array('application'),
            'uploadOrder' => array('allow', 'deny'),
            'alias' => 'Home',
            'tmbPath' => rtrim($tmbpath, '/'),
            'tmbURL' => rtrim(Url::thumb(str_replace(GX_PATH, '', $tmbpath)), '/') . '/',
        );
    }

    // Common plugins config
    $rootConfig['plugin'] = array(
        'AutoResize' => array('enable' => false, 'maxWidth' => 1000, 'maxHeight' => 1000, 'quality' => 30, 'forceEffect' => true),
        'Sanitizer' => array('enable' => true, 'targets' => array('\\', '/', ':', '*', '?', '"', '<', '>', '|', '(', ')', ' '), 'replace' => '_'),
        'Normalizer' => array('enable' => true, 'convmap' => array(' ' => '_', ',' => '_', '-' => '_', '^' => '_'))
    );

    $opts = array(
        'bind' => array(
            'upload' => 'uploadPost',
            'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre ls.pre' => array('Plugin.Normalizer.cmdPreprocess', 'Plugin.Sanitizer.cmdPreprocess'),
            'ls' => array('Plugin.Normalizer.cmdPostprocess', 'Plugin.Sanitizer.cmdPostprocess'),
            'upload.presave' => array('Plugin.AutoResize.onUpLoadPreSave', 'Plugin.Normalizer.onUpLoadPreSave', 'Plugin.Sanitizer.onUpLoadPreSave', 'autoSortUpload'),
        ),
        'roots' => array($rootConfig),
    );

    // run elFinder
    $connector = new elFinderConnector(new elFinder($opts));
    $connector->run();
} else {
    echo json_encode(array('error' => $_SERVER['REQUEST_URI']));
}
// echo "TOKEN EXIST";
