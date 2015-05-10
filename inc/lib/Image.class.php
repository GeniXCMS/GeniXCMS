<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150214
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* Image Processor Class
*
* This class will run the image modifier.
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class Image
{
    public function __construct () {

    }


    /**
    * Image Resize function.
    * This function will resize and crop images.
    *
    * @param string $src The source of the images.
    * @param string $dst The destination of the output image.
    * @param int $width The width dimension of the output images.
    * @param int $height The height dimension of the output images.
    * @param bool $crop 0 or 1, 0 for not cropped. and 1 for cropped.
    *
    * @return true
    *
    * @author promaty@gmail.com
    * @link http://php.net/manual/en/function.imagecopyresampled.php#104028
    */
    static public function resize($src, $dst, $width, $height, $crop=0){

        if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

        $type = strtolower(substr(strrchr($src,"."),1));
        if($type == 'jpeg') $type = 'jpg';
        switch($type){
            case 'bmp': $img = imagecreatefromwbmp($src); break;
            case 'gif': $img = imagecreatefromgif($src); break;
            case 'jpg': $img = imagecreatefromjpeg($src); break;
            case 'png': $img = imagecreatefrompng($src); break;
            default : return "Unsupported picture type!";
        }

        // resize
        if($crop){
            if($w < $width or $h < $height) return "Picture is too small!";
            $ratio = max($width/$w, $height/$h);
            $h = $height / $ratio;
            $x = ($w - $width / $ratio) / 2;
            $w = $width / $ratio;
        }
        else{
            if($w < $width and $h < $height) return "Picture is too small!";
            $ratio = min($width/$w, $height/$h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
        }

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if($type == "gif" or $type == "png"){
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

        switch($type){
            case 'bmp': imagewbmp($new, $dst); break;
            case 'gif': imagegif($new, $dst); break;
            case 'jpg': imagejpeg($new, $dst); break;
            case 'png': imagepng($new, $dst); break;
        }
        return true;
    }

    static public function compress_png($path, $max_quality = 85) {

        $check = shell_exec("pngquant --version");
        if(!$check) {
            return false;
        }else{
            // guarantee that quality won't be worse than that.
            $min_quality = 60;

            // '-' makes it use stdout, required to save to $compressed_png_content variable
            // '<' makes it read from the given file path
            // escapeshellarg() makes this safe to use with any path
            $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg($path));

            if (!$compressed_png_content) {
                throw new Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
            }else{
                file_put_contents($path, $compressed_png_content);
                return true;
            }

            
        }
    }

}