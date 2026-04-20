<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class SaveimageAjax
{
    /**
     * GeniXCMS - Content Management System
     */
    public function index($param = null)
    {
        if (!$this->_auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        // A list of permitted file extensions
        $allowed = array('png', 'jpg', 'jpeg', 'gif');
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), $allowed)) {
                return Ajax::error(400, 'Invalid file extension');
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

                return Ajax::response($output);
            }
        } elseif (isset($_POST['file'])) {
            $data = $_POST['file'];

            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    return Ajax::error(400, 'Invalid image type');
                }
                $data = str_replace(' ', '+', $data);
                $data = base64_decode($data);

                if ($data === false) {
                    return Ajax::error(400, 'Base64 decode failed');
                }
            } else {
                return Ajax::error(400, 'Invalid image data');
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

            return Ajax::response($output);
        }

        return Ajax::error(400, 'No file provided');
    }

    /**
     * Internal auth check
     */
    private function _auth($param = null)
    {
        $data = Router::scrap($param);
        $gettoken = (SMART_URL) ? ($data['token'] ?? '') : (isset($_GET['token']) ? Typo::cleanX($_GET['token']) : '');
        $token = (true === Token::validate($gettoken, true)) ? $gettoken : '';
        $url = Site::canonical();
        
        return ($token != '' && Http::validateUrl($url) && User::access(2));
    }
}
