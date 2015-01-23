<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : Private
*    ------------------------------------------------------------
* filename : Upload.class.php
* version : 0.0.1 pre
* build : 2014103
*/

/**
* 
*/
class Upload
{
    
    function __construct()
    {
        # code...
    }

    public static function go($input, $path, $allowed='', $uniq=false, $size='', $width = '', $height = ''){
        //$allowed = array('png', 'jpg', 'gif','zip');
        $filename = Typo::cleanX($_FILES[$input]['name']);
        $filename = str_replace(' ', '_', $filename);
        if(isset($_FILES[$input]) && $_FILES[$input]['error'] == 0){
            if($uniq == true){
                $uniqfile = sha1(microtime().$filename);
            }else{
                $uniqfile = '';
            }

            $extension = pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION);
            $filetmp = $_FILES[$input]['tmp_name'];
            $filepath = GX_PATH.$path.$uniqfile.$filename;

            if(!in_array(strtolower($extension), $allowed)){
                $result['error'] = 'File not allowed';
            }else{
                if(move_uploaded_file(
                    $filetmp, 
                    $filepath)
                ){
                    $result['filesize'] = filesize($filepath);
                    $result['filename'] = $uniqfile.$filename;
                    $result['path'] = $path.$uniqfile.$filename;
                    $result['filepath'] = $filepath;
                    $result['fileurl'] = GX_URL.$path.$uniqfile.$filename;

                }else{
                    $result['error'] = 'Cannot upload to directory, please check 
                    if directory is exist or You had permission to write it.';
                }
            }

            
        }else{
            //$result['error'] = $_FILES[$input]['error'];
            $result['error'] = '';
        }

        return $result;

    }
}

/* End of file Upload.class.php */
/* Location: ./inc/lib/Upload.class.php */