<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework

 *
 * @since 0.0.1 build date 20141003
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

$data = Router::scrap($param);
$gettoken = (SMART_URL) ? $data['token'] : Typo::cleanX($_GET['token']);
$token = (true === Token::validate($gettoken, true)) ? $gettoken : '';
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
            if (move_uploaded_file($_FILES['file']['tmp_name'], GX_PATH . '/assets/media/images/' . $_FILES['file']['name'])) {
                $tmp = GX_PATH . '/assets/media/images/' . $_FILES['file']['name'];
                if (Image::isPng($tmp)) {
                    Image::compressPng($tmp);
                } elseif (Image::isJpg($tmp)) {
                    Image::compressJpg($tmp);
                }

                $output = [
                    'success' => 1,
                    'status' => 'success',
                    'url' => Site::$url . '/assets/media/images/' . $_FILES['file']['name'],
                    'path' => 'assets/media/images/' . $_FILES['file']['name'],
                    'file' => [
                        'url' => Site::$url . '/assets/media/images/' . $_FILES['file']['name']
                    ]
                ];

                echo json_encode($output);
                //echo '{"status":"success"}';
                exit;
            }
        } else {
            $data = $_POST['file'];

            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('invalid image type');
                }
                $data = str_replace(' ', '+', $data);
                $data = base64_decode($data);

                if ($data === false) {
                    throw new \Exception('base64_decode failed');
                }
            } else {
                throw new \Exception('did not match data URI with image data');
            }

            file_put_contents(GX_PATH . '/assets/media/images/' . $_POST['file_name'], $data);

            $tmp = GX_PATH . '/assets/media/images/' . $_POST['file_name'];
            if (Image::isPng($tmp)) {
                Image::compressPng($tmp);
            } elseif (Image::isJpg($tmp)) {
                Image::compressJpg($tmp);
            }

            $output = [
                'success' => 1,
                'status' => 'success',
                'url' => Site::$url . 'assets/media/images/' . $_POST['file_name'],
                'path' => 'assets/media/images/' . $_POST['file_name'],
                'file' => [
                    'url' => Site::$url . 'assets/media/images/' . $_POST['file_name']
                ]
            ];

            echo json_encode($output);
            //echo '{"status":"success"}';
            exit;
        }
    } else {
        echo '{"status":"error"}';
    }
} else {
    echo '{"status":"Token not exist"}';
}
