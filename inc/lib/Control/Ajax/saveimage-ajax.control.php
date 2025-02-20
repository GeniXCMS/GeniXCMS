<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 0.0.1 build date 20141003
 * @version 1.1.12
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
$data = Router::scrap($param);
$gettoken = (SMART_URL) ? $data['token'] : Typo::cleanX($_GET['token']);
$token = (true === Token::validate($gettoken, true)) ? $gettoken: '';
$url = Site::canonical();
if ($token != '' && Http::validateUrl($url)) {
    if (User::access(2)) {
        // A list of permitted file extensions
        $allowed = array('png', 'jpg', 'jpeg', 'gif');
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), $allowed)) {
                echo '{"status":"error"}';
                exit;
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'], GX_PATH.'/assets/media/images/'.$_FILES['file']['name'])) {
                $tmp = GX_PATH.'/assets/media/images/'.$_FILES['file']['name'];
                if (Image::isPng($tmp)) {
                    Image::compressPng($tmp);
                } elseif (Image::isJpg($tmp)) {
                    Image::compressJpg($tmp);
                }
                echo Site::$url.'/assets/media/images/'.$_FILES['file']['name'];
                //echo '{"status":"success"}';
                exit;
            }
        }
    } else {
        echo '{"status":"error"}';
    }
} else {
    echo '{"status":"Token not exist"}';
}
