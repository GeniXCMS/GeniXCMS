<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141003
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

/**
 * Upload Class.
 *
 * This class will run the upload files.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 *
 * @since 0.0.1
 */
class Upload
{
    public function __construct()
    {
    }

    /**
     * Upload Proccess Function. This will do the upload proccess. This function
     * need some variables, eg:.
     *
     * @param string $input   This is the input field name.
     * @param string $path    This is the path the file will be stored.
     * @param array  $allowed This is the array of the allowed file extension.
     * @param false  $uniq    Set to true if want to use a unique name.
     * @param int    $size    File size maximum allowed.
     * @param int    $width   The width of the dimension.
     * @param int    $height  The height of the dimension.
     *
     * @return array
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function go($input, $path, $allowed = '', $uniq = false, $size = '', $width = '', $height = '')
    {
        $filename = Typo::cleanX($_FILES[$input]['name']);
        $filename = str_replace(' ', '_', $filename);
        if (isset($_FILES[$input]) && $_FILES[$input]['error'] == 0) {
            if ($uniq == true) {
                $site = Typo::slugify(Options::v('sitename'));
                $uniqfile = $site.'-'.sha1(microtime().$filename).'-';
            } else {
                $uniqfile = '';
            }

            $extension = pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION);
            $filetmp = $_FILES[$input]['tmp_name'];
            $filepath = GX_PATH.$path.$uniqfile.$filename;

            if (!in_array(strtolower($extension), $allowed)) {
                $result['error'] = 'File not allowed';
            } else {
                // if(file_exists($filepath)) {
                //     @chmod($filepath,0755); //Change the file permissions if allowed
                //     @unlink($filepath); //remove the file
                // }
                if (move_uploaded_file(
                    $filetmp,
                    $filepath
                )
                ) {
                    $result['filesize'] = filesize($filepath);
                    $result['filename'] = $uniqfile.$filename;
                    $result['path'] = $path.$uniqfile.$filename;
                    $result['filepath'] = $filepath;
                    $result['fileurl'] = Site::$url.$path.$uniqfile.$filename;
                } else {
                    $result['error'] = 'Cannot upload to directory, please check 
                    if directory is exist or You had permission to write it.';
                }
            }
        } else {
            //$result['error'] = $_FILES[$input]['error'];
             $result['error'] = '';
        }

        return $result;
    }
}

/* End of file Upload.class.php */
/* Location: ./inc/lib/Upload.class.php */
