<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.4.0
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
        $allowed = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

        // Ensure upload directory exists
        $uploadDir = GX_PATH . '/assets/media/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Map PHP upload error codes to readable messages
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => _('File exceeds the server upload_max_filesize limit.'),
            UPLOAD_ERR_FORM_SIZE  => _('File exceeds the MAX_FILE_SIZE limit.'),
            UPLOAD_ERR_PARTIAL    => _('File was only partially uploaded.'),
            UPLOAD_ERR_NO_FILE    => _('No file was uploaded.'),
            UPLOAD_ERR_NO_TMP_DIR => _('Missing temporary folder.'),
            UPLOAD_ERR_CANT_WRITE => _('Failed to write file to disk.'),
            UPLOAD_ERR_EXTENSION  => _('Upload blocked by a PHP extension.'),
        ];

        // Accept any file field name (EditorJS uses 'image', others use 'file')
        $fileKey = null;
        foreach (['image', 'file', 'upload'] as $k) {
            if (isset($_FILES[$k])) {
                // Report PHP-level upload errors immediately
                if ($_FILES[$k]['error'] !== UPLOAD_ERR_OK) {
                    $msg = $uploadErrors[$_FILES[$k]['error']] ?? _('Unknown upload error: ') . $_FILES[$k]['error'];
                    return Ajax::error(400, $msg);
                }
                $fileKey = $k;
                break;
            }
        }
        // Fallback: first available file
        if (!$fileKey && !empty($_FILES)) {
            $first = array_key_first($_FILES);
            if ($_FILES[$first]['error'] !== UPLOAD_ERR_OK) {
                $msg = $uploadErrors[$_FILES[$first]['error']] ?? _('Upload error: ') . $_FILES[$first]['error'];
                return Ajax::error(400, $msg);
            }
            $fileKey = $first;
        }

        if ($fileKey && $_FILES[$fileKey]['error'] == 0) {
            $extension = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), $allowed)) {
                return Ajax::error(400, 'Invalid file extension');
            }
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES[$fileKey]['name']);
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], GX_PATH . '/assets/media/images/' . $filename)) {
                $tmp = GX_PATH . '/assets/media/images/' . $filename;
                if (Image::isPng($tmp)) {
                    Image::compressPng($tmp);
                } elseif (Image::isJpg($tmp)) {
                    Image::compressJpg($tmp);
                }

                $output = [
                    'success' => 1,
                    'status'  => 'success',
                    'url'     => rtrim(Site::$url, '/') . '/assets/media/images/' . $filename,
                    'path'    => 'assets/media/images/' . $filename,
                    'file'    => [
                        'url' => rtrim(Site::$url, '/') . '/assets/media/images/' . $filename,
                    ]
                ];

                return Ajax::response($output);
            }
            return Ajax::error(500, 'Failed to move uploaded file');
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

        return Ajax::error(400, _('No file provided.'));
    }

    /**
     * Internal auth check
     */
    private function _auth($param = null)
    {
        $gettoken = '';
        if (SMART_URL && $param) {
            $data = Router::scrap($param);
            $gettoken = $data['token'] ?? '';
        }
        if (empty($gettoken)) {
            $gettoken = $_GET['token'] ?? $_REQUEST['token'] ?? '';
            $gettoken = Typo::cleanX($gettoken);
        }

        return ($gettoken !== '' && Token::validate($gettoken, true) && User::access(2));
    }
}
