<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150214
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

/**
 * Image Processor Class.
 *
 * This class will run the image modifier.
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
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
            return 'Unsupported picture type!';
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
                return 'Unsupported picture type!';
        }

        // resize
        if ($crop) {
            if ($w < $width or $h < $height) {
                return 'Picture is too small!';
            }
            $ratio = max($width / $w, $height / $h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        } else {
            if ($w < $width and $h < $height) {
                return 'Picture is too small!';
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

    public static function compressPng($path, $max_quality = 80)
    {
        $check = shell_exec('pngquant --version');
        if (!$check) {
            return false;
        } else {
            // guarantee that quality won't be worse than that.
            $min_quality = 70;

            // '-' makes it use stdout, required to save to $compressed_png_content variable
            // '<' makes it read from the given file path
            // escapeshellarg() makes this safe to use with any path
            $compressed_png_content = exec("pngquant -f --quality $min_quality-$max_quality -o ".escapeshellarg($path).' -- '.escapeshellarg($path));

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
        $img = new Imagick();
        $img->readImage($path);
        $img->setImageCompression(imagick::COMPRESSION_JPEG);
        $img->setImageCompressionQuality($quality);
        $img->stripImage();
        $img->writeImage($path);
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

    public function getGravatar($email, $s = 60, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="'.$url.'"';
            foreach ($atts as $key => $val) {
                $url .= ' '.$key.'="'.$val.'"';
            }
            $url .= ' />';
        }

        return $url;
    }

    /**
     * @link http://1stwebmagazine.com/generate-thumbnail-on-the-fly-with-php
     */
    public static function thumbFly($img, $type = 'square', $size = '', $align = '')
    {
        $square = ($size != '') ? Typo::int($size) : 150;
        $large = ($size != '') ? Typo::int($size) : 200;
        $small = ($size != '') ? Typo::int($size) : 100;

        ////////////////////////////////////////////////////////////////////////////////// square
        if (isset($type) && ($type == 'square' || $type == '')) {
            // thumb size
            $thumb_width = $square;
            $thumb_height = $square;

        // align
            $align = isset($align) ? $align : '';

        // image source
            $imgSrc = GX_PATH.'/'.$img;
            $imgExt = substr($imgSrc, -3);

            if (file_exists($imgSrc)) {
                // image extension
                if ($imgExt == 'jpg') {
                    $myImage = imagecreatefromjpeg($imgSrc);
                }
                if ($imgExt == 'gif') {
                    $myImage = imagecreatefromgif($imgSrc);
                }
                if ($imgExt == 'png') {
                    $myImage = imagecreatefrompng($imgSrc);
                }

            // getting the image dimensions
                list($width_orig, $height_orig) = getimagesize($imgSrc);

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
                $x_mid = $new_width / 2;
                $y_mid = $new_height / 2;

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

                if ($imgExt == 'jpg') {
                    imagejpeg($thumb, null, 100);
                }
                if ($imgExt == 'gif') {
                    imagegif($thumb);
                }
                if ($imgExt == 'png') {
                    imagepng($thumb, null, 9);
                }
            }
        }

        ////////////////////////////////////////////////////////////////////////////////// normal
        if (isset($type) && ($type == 'large' || $type == 'small')) {
            if ($type == 'large') {
                $thumb_width = $large;
            }
            if ($type == 'small') {
                $thumb_width = $small;
            }

        // image source
            $imgSrc = GX_PATH.'/'.$img;
            $imgExt = substr($imgSrc, -3);
            if (file_exists($imgSrc)) {
                // image extension
                if ($imgExt == 'jpg') {
                    $myImage = imagecreatefromjpeg($imgSrc);
                }
                if ($imgExt == 'gif') {
                    $myImage = imagecreatefromgif($imgSrc);
                }
                if ($imgExt == 'png') {
                    $myImage = imagecreatefrompng($imgSrc);
                }

            //getting the image dimensions
                list($width_orig, $height_orig) = getimagesize($imgSrc);

            // ratio
                $ratio_orig = $width_orig / $height_orig;
                $thumb_height = $thumb_width / $ratio_orig;

            // new dimensions
                $new_width = $thumb_width;
                $new_height = $thumb_height;

            // middle
                $x_mid = $new_width / 2;
                $y_mid = $new_height / 2;

            // create new image
                $process = imagecreatetruecolor(round($new_width), round($new_height));

                imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
                imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumb_width / 2)), ($y_mid - ($thumb_height / 2)), $thumb_width, $thumb_height, $thumb_width, $thumb_height);

                if ($imgExt == 'jpg') {
                    imagejpeg($thumb, null, 100);
                }
                if ($imgExt == 'gif') {
                    imagegif($thumb);
                }
                if ($imgExt == 'png') {
                    imagepng($thumb, null, 9);
                }
            }
        }
    }
}
