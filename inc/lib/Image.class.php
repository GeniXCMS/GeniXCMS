<?php
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150214
 *
 * @version 2.1.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Image Processor Class.
 *
 * This class will run the image modifier.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 *
 * @since 0.0.1
 */
class Image
{
    public function __construct()
    {
    }

    /**
     * Image Resize function.
     *
     * This function will resize and crop images.
     *
     * @param string $src    The source of the images.
     * @param string $dst    The destination of the output image.
     * @param int    $width  The width dimension of the output images.
     * @param int    $height The height dimension of the output images.
     * @param bool   $crop   0 or 1, 0 for not cropped. and 1 for cropped.
     *
     * @return true
     *
     * @author promaty@gmail.com
     *
     * @link http://php.net/manual/en/function.imagecopyresampled.php#104028
     */
    public static function resize($src, $dst, $width, $height, $crop = 0)
    {
        if (!list($w, $h) = getimagesize($src)) {
            return false; //'Unsupported picture type!';
        }

        $type = strtolower(substr(strrchr($src, '.'), 1));
        if ($type == 'jpeg') {
            $type = 'jpg';
        }
        switch ($type) {
            case 'bmp':
                $img = imagecreatefromwbmp($src);
                break;
            case 'gif':
                $img = imagecreatefromgif($src);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = imagecreatefrompng($src);
                break;
            default:
                return false; // 'Unsupported picture type!';
        }

        // resize
        if ($crop) {
            if ($w < $width or $h < $height) {
                return false; // 'Picture is too small!';
            }
            $ratio = max($width / $w, $height / $h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        } else {
            if ($w < $width and $h < $height) {
                return false; // 'Picture is too small!';
            }
            $ratio = min($width / $w, $height / $h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
        }

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if ($type == 'gif' or $type == 'png') {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

        switch ($type) {
            case 'bmp':
                imagewbmp($new, $dst);
                break;
            case 'gif':
                imagegif($new, $dst);
                break;
            case 'jpg':
                imagejpeg($new, $dst);
                break;
            case 'png':
                imagepng($new, $dst);
                break;
        }

        return true;
    }

    public static function compressPng($img)
    {
        $im = imagecreatefrompng($img);
        $quality = 9; //0 - 9 (0= no compression, 9 = high compression)
        imageAlphaBlending($im, true);
        imageSaveAlpha($im, true);
        imagepng($im, $img, $quality);  //leave out filename if you want it to output to the buffer
        imagedestroy($im);
    }

    public static function compressPng2($path, $max_quality = 80)
    {
        $check = function_exists("shell_exec") ? shell_exec('pngquant --version') : false;
        if (false == $check) {
            return false;
        } else {
            // guarantee that quality won't be worse than that.
            $min_quality = 70;

            // '-' makes it use stdout, required to save to $compressed_png_content variable
            // '<' makes it read from the given file path
            // escapeshellarg() makes this safe to use with any path
            $compressed_png_content = exec("pngquant -f --quality $min_quality-$max_quality -o " . escapeshellarg($path) . ' -- ' . escapeshellarg($path));

            if (!$compressed_png_content) {
                // throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
                return false;
            } else {
                file_put_contents($path, $compressed_png_content);

                return true;
            }
        }
    }

    public static function compressJpg($path, $quality = 80)
    {
        if (extension_loaded('imagick')) {
            $img = new Imagick();
            $img->readImage($path);
            $img->setImageCompression(Imagick::COMPRESSION_JPEG);
            $img->setImageCompressionQuality($quality);
            $img->stripImage();
            $img->writeImage($path);
        }
    }

    public static function isJpg(&$pict)
    {
        $type = strtolower(substr(strrchr($pict, '.'), 1));
        if ($type == 'jpg') {
            return true;
        }
    }

    public static function isPng(&$pict)
    {
        $type = strtolower(substr(strrchr($pict, '.'), 1));
        if ($type == 'png') {
            return true;
        }
    }

    public static function getGravatar($email, $s = 60, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {

        if (false == Site::$isOffline) {
            $url = 'https://www.gravatar.com/avatar/';
            $url .= md5(strtolower(trim($email)));
            $url .= "?s=$s&d=$d&r=$r";
        } else {
            $url = Site::$cdn . 'assets/images/user1-60x60.png';
        }
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }

        return $url;
    }

    public static function convertWebp($img)
    {
        $manager = new ImageManager(
            Driver::class
        );

        if (file_exists($img)) {
            $pathInfo = pathinfo($img);
            $ext = strtolower($pathInfo['extension']);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                try {
                    $newName = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
                    $image = $manager->read($img);
                    $image->encodeByPath($newName, ...['quality' => 65])->save($newName);
                    return $newName;
                } catch (\Exception $e) {
                    // Fail gracefully
                }
            }
        }
        return false;
    }

    public static function thumbFly($img, $type, $size, $align)
    {
        $manager = new ImageManager(
            Driver::class
        );
        $img = is_array($img) ? ($img[0] ?? '') : $img;
        $img = urldecode($img);
        $img = str_replace('thumb/', '', $img);

        $noimage = 'assets/images/noimage.png';
        $imgExts = explode(".", $img);
        $imgExt = strtolower(end($imgExts));
        $ighash = ($noimage == $img) ? sha1('noimage' . $img) : sha1($img);
        // $cacheFile = ($noimage == $img) ? $img : GX_ASSET.'cache/thumb'.$type.$size.$align.'-'.$ighash.'.'.$imgExt;
        $cacheFile = GX_ASSET . 'cache/thumbs/thumb' . $type . $size . $align . '-' . $ighash . '.' . $imgExt;

        $square = ($size != '') ? Typo::int($size) : 150;
        $large = ($size != '') ? Typo::int($size) : 200;
        $small = ($size != '') ? Typo::int($size) : 100;

        if (file_exists($cacheFile)) {
            $src = $cacheFile;
            ob_clean();
            readfile($src);
            exit;
            // $image = $manager->read($src);

            // if (Options::v('is_logourl') == 'on' && Options::v('logourl') != '') {
            //     $logo = GX_PATH.'/'.Options::v('logourl');
            // } elseif (Options::v('is_logourl') == 'off' && Options::v('logo') != '') {
            //     $logo = GX_PATH.'/'.Options::v('logo');
            // }

            // $image->place(
            //     $logo,
            //     'bottom-right', 
            //     10, 
            //     10,
            //     30
            // );

            // if ($imgExt == 'jpg' || $imgExt == 'jpeg') {
            //     $encoded = $image->toJpeg();
            // }
            // if ($imgExt == 'gif') {
            //     $encoded = $image->toGif();
            // }
            // if ($imgExt == 'png') {
            //     $encoded = $image->toPng();
            // }
            // if ($imgExt == 'webp') {
            //     $encoded = $image->toWebp();
            // }



        } else {


            // check whether files exist on remote or local
            if (Files::isRemote($img)) {
                $imgSrc = $img;
                $exist = Files::remoteExist($imgSrc);
            } else {
                $imgSrc = GX_PATH . '/' . $img;
                $exist = file_exists($imgSrc);
            }

            if (!$exist) {
                $imgSrc = GX_PATH . '/' . $noimage;
                $imgExt = 'png';
            }

            $getsize = @getimagesize($imgSrc);
            if (!$getsize) {
                $imgSrc = GX_PATH . '/' . $noimage;
                $getsize = getimagesize($imgSrc);
            }
            list($width) = $getsize;

            try {
                $image = $manager->read($imgSrc);
            } catch (\Exception $e) {
                // If read fails, fallback to noimage
                $imgSrc = GX_PATH . '/' . $noimage;
                $image = $manager->read($imgSrc);
            }

            // type : square, large, small
            if ($type == 'square') {
                $image->cover($square, $square);
            }
            if ($type == 'large') {
                $image->scale(width: $large);
            }
            if ($type == 'small') {
                $image->scale(width: $small);
            }

            $use_watermark = Options::v('media_use_watermark');
            if ($use_watermark == "on") {
                $watermark_image = Options::v('media_watermark_image');
                $watermark_position = Options::v('media_watermark_position');
                $watermark_opacity = Options::v('media_watermark_opacity');
                if (
                    (isset($size) && $size > 200) ||
                    (!isset($size) || $size == '' && $width > 200)
                ) {
                    $image->place(
                        GX_PATH . '/' . $watermark_image,
                        $watermark_position,
                        0,
                        0,
                        $watermark_opacity
                    );
                }
            }



            $options = [];
            if (in_array(strtolower($imgExt), ['jpg', 'jpeg'])) {
                $options['progressive'] = true;
                $options['quality'] = 65;
            } elseif (in_array(strtolower($imgExt), ['webp', 'avif'])) {
                $options['quality'] = 65;
            }

            $encoded = $image->encodeByPath($cacheFile, ...$options);
            $encoded->save($cacheFile);

            ob_clean();
            echo (string) $encoded;
            exit;
        }


    }

    /**
     * @link http://1stwebmagazine.com/generate-thumbnail-on-the-fly-with-php
     */
    public static function thumbFly2($img, $type = 'square', $size = '', $align = '')
    {
        $manager = new ImageManager(
            Driver::class
        );

        $img = is_array($img) ? ($img[0] ?? '') : $img;
        $img = urldecode($img);

        $noimage = 'assets/images/noimage.png';
        $imgExt = pathinfo($img, PATHINFO_EXTENSION); //substr($img, -3);
        $ighash = ($noimage == $img) ? sha1('noimage' . $img) : sha1($img);
        // $cacheFile = ($noimage == $img) ? $img : GX_ASSET.'cache/thumb'.$type.$size.$align.'-'.$ighash.'.'.$imgExt;
        $cacheFile = GX_ASSET . 'cache/thumbs/thumb' . $type . $size . $align . '-' . $ighash . '.' . $imgExt;

        if (file_exists($cacheFile)) {
            $imgSrc = $cacheFile;
            list($width_orig, $height_orig) = getimagesize($imgSrc);
            if ($imgExt == 'jpg' || $imgExt == 'jpeg' || $imgExt == 'webp') {
                $myImage = imagecreatefromjpeg($imgSrc);
            }
            if ($imgExt == 'gif') {
                $myImage = imagecreatefromgif($imgSrc);
            }
            if ($imgExt == 'png') {
                $myImage = imagecreatefrompng($imgSrc);
            }
            if ($imgExt == 'webp') {
                $myImage = imagecreatefromwebp($imgSrc);
            }
            if ($imgExt == 'jpg' || $imgExt == 'jpeg' || $imgExt == 'webp') {
                imagejpeg($myImage, null, 100);
            }
            if ($imgExt == 'gif') {
                imagegif($myImage);
            }
            if ($imgExt == 'png') {
                imagepng($myImage, null, 9);
            }

        } else {
            $square = ($size != '') ? Typo::int($size) : 150;
            $large = ($size != '') ? Typo::int($size) : 200;
            $small = ($size != '') ? Typo::int($size) : 100;

            // check whether files exist on remote or local
            if (Files::isRemote($img)) {
                $imgSrc = $img;
                if (Files::remoteExist($imgSrc)) {
                    $exist = true;
                } else {
                    $exist = false;
                }
            } else {
                $imgSrc = GX_PATH . '/' . $img;
                if (file_exists($imgSrc)) {
                    $exist = true;
                } else {
                    $exist = false;
                }
            }

            if (!$exist) {
                $imgSrc = GX_PATH . '/' . $noimage;
                $imgExt = 'png';
            }

            // echo $imgSrc;
            ////////////////////////////////////////////////////////////////////////////////// square
            if (isset($type) && ($type == 'square' || $type == '')) {
                // thumb size
                $thumb_width = $square;
                $thumb_height = $square;

                // align
                $align = isset($align) ? $align : '';

                // image source
                // $imgSrc = GX_PATH.'/'.$img;

                if (true) {
                    // image extension
                    if ($imgExt == 'jpg' || $imgExt == 'jpeg') {
                        $myImage = imagecreatefromjpeg($imgSrc);
                    }
                    if ($imgExt == 'gif') {
                        $myImage = imagecreatefromgif($imgSrc);
                    }
                    if ($imgExt == 'png') {
                        $myImage = imagecreatefrompng($imgSrc);
                    }
                    if ($imgExt == 'webp') {
                        $myImage = imagecreatefromwebp($imgSrc);
                    }

                    $getsize = @getimagesize($imgSrc);
                    if (!$getsize) {
                        $imgSrc = GX_PATH . '/' . $noimage;
                        $getsize = getimagesize($imgSrc);
                    }
                    list($width_orig, $height_orig) = $getsize;

                    // ratio
                    $ratio_orig = $width_orig / $height_orig;

                    // landscape or portrait?
                    if ($thumb_width / $thumb_height > $ratio_orig) {
                        $new_height = $thumb_width / $ratio_orig;
                        $new_width = $thumb_width;
                    } else {
                        $new_width = $thumb_height * $ratio_orig;
                        $new_height = $thumb_height;
                    }

                    // middle
                    $x_mid = round($new_width / 2);
                    $y_mid = round($new_height / 2);

                    // create new image
                    $process = imagecreatetruecolor(round($new_width), round($new_height));
                    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                    $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

                    // alignment
                    if ($align == '') {
                        imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumb_width / 2)), ($y_mid - ($thumb_height / 2)), $thumb_width, $thumb_height, $thumb_width, $thumb_height);
                    }
                    if ($align == 'top') {
                        imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumb_width / 2)), 0, $thumb_width, $thumb_height, $thumb_width, $thumb_height);
                    }
                    if ($align == 'bottom') {
                        imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumb_width / 2)), ($new_height - $thumb_height), $thumb_width, $thumb_height, $thumb_width, $thumb_height);
                    }
                    if ($align == 'left') {
                        imagecopyresampled($thumb, $process, 0, 0, 0, ($y_mid - ($thumb_height / 2)), $thumb_width, $thumb_height, $thumb_width, $thumb_height);
                    }
                    if ($align == 'right') {
                        imagecopyresampled($thumb, $process, 0, 0, ($new_width - $thumb_width), ($y_mid - ($thumb_height / 2)), $thumb_width, $thumb_height, $thumb_width, $thumb_height);
                    }

                    imagedestroy($process);
                    imagedestroy($myImage);
                    $image = $manager->read($imgSrc);
                    $use_watermark = Options::v('media_use_watermark');
                    if ($use_watermark == "on") {
                        $watermark_image = Options::v('media_watermark_image');
                        $watermark_position = Options::v('media_watermark_position');
                        $watermark_opacity = Options::v('media_watermark_opacity');
                        if (
                            (isset($size) && $size > 200) ||
                            (!isset($size) || $size == '' && $width_orig > 200)
                        ) {
                            $image->place(
                                GX_PATH . '/' . $watermark_image,
                                $watermark_position,
                                0,
                                0,
                                $watermark_opacity
                            );
                        }
                    }


                    if ($imgExt == 'jpg' || $imgExt == 'jpeg' || $imgExt == 'webp') {
                        if (!file_exists($cacheFile)) {
                            $loc = $cacheFile;
                            imagejpeg($thumb, $loc, 100);
                            self::thumbFly($img, $type, $size, $align);
                        }
                    }
                    if ($imgExt == 'gif') {
                        imagegif($thumb);
                    }
                    if ($imgExt == 'png') {
                        if (!file_exists($cacheFile)) {
                            $loc = $cacheFile;
                            imagepng($thumb, $loc, 9);
                            self::thumbFly($img, $type, $size, $align);
                        }
                    }
                } else {
                    self::thumbFly($noimage, '', $size, $align);
                }
            }

            if (isset($type) && ($type == 'large' || $type == 'small')) {
                if ($type == 'large') {
                    $thumb_width = $large;
                }
                if ($type == 'small') {
                    $thumb_width = $small;
                }

                // image source
                // $imgSrc = GX_PATH.'/'.$img;
                // $imgSrc = $img;

                if (true) {
                    // image extension
                    if ($imgExt == 'jpg' || $imgExt == 'jpeg') {
                        $myImage = imagecreatefromjpeg($imgSrc);
                    }
                    if ($imgExt == 'gif') {
                        $myImage = imagecreatefromgif($imgSrc);
                    }
                    if ($imgExt == 'png') {
                        $myImage = imagecreatefrompng($imgSrc);
                    }
                    if ($imgExt == 'webp') {
                        $myImage = imagecreatefromwebp($imgSrc);
                    }

                    //getting the image dimensions
                    list($width_orig, $height_orig) = getimagesize($imgSrc);

                    // ratio
                    $ratio_orig = $width_orig / $height_orig;
                    $thumb_height = $thumb_width / $ratio_orig;

                    // new dimensions
                    $new_width = round($thumb_width);
                    $new_height = round($thumb_height);

                    // middle
                    $x_mid = $new_width / 2;
                    $y_mid = $new_height / 2;

                    // create new image
                    $process = imagecreatetruecolor(round($new_width), round($new_height));

                    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                    $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
                    imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumb_width / 2)), ($y_mid - ($thumb_height / 2)), $thumb_width, $thumb_height, $thumb_width, $thumb_height);

                    $image = $manager->read($imgSrc);
                    $use_watermark = Options::v('media_use_watermark');
                    if ($use_watermark == "on") {
                        $watermark_image = Options::v('media_watermark_image');
                        $watermark_position = Options::v('media_watermark_position');
                        $watermark_opacity = Options::v('media_watermark_opacity');
                        if (
                            (isset($size) && $size > 200) ||
                            (!isset($size) || $size == '' && $width_orig > 200)
                        ) {
                            $image->place(
                                GX_PATH . '/' . $watermark_image,
                                $watermark_position,
                                0,
                                0,
                                $watermark_opacity
                            );
                        }
                    }

                    if ($imgExt == 'jpg' || $imgExt == 'jpeg' || $imgExt == 'webp') {
                        if (!file_exists($cacheFile)) {
                            $loc = $cacheFile;
                            imagejpeg($thumb, $loc, 100);
                            self::thumbFly($img, $type, $size, $align);
                        }
                    }
                    if ($imgExt == 'gif') {
                        imagegif($thumb);
                    }
                    if ($imgExt == 'png') {
                        if (!file_exists($cacheFile)) {
                            $loc = $cacheFile;
                            imagepng($thumb, $loc, 9);
                            self::thumbFly($img, $type, $size, $align);
                        }
                    }
                } else {
                    self::thumbFly($noimage, 'large', $size, $align);
                }
            }
        }

        ////////////////////////////////////////////////////////////////////////////////// normal
    }
}
