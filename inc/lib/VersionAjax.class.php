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

class VersionAjax
{
    /**
     * GeniXCMS - Content Management System
     */
    public function index($param = null)
    {
        if (!$this->_auth($param)) {
            return Ajax::response(['status' => 'Token not exist']);
        }

        $apiUrl = 'https://genixcms.web.id/api/v1/download/latest';
        $response = Http::fetch([
            'url' => $apiUrl,
            'curl' => true,
            'curl_options' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'GeniXCMS/' . System::$version
            ]
        ]);
        $apiData = json_decode($response, true);
        $latestCore = $apiData['data'] ?? null;
        $v = $latestCore['version'] ?? System::$version;

        if (version_compare(System::$version, $v, '<')) {
            return Ajax::response(['status' => 'true', 'version' => $v]);
        } else {
            return Ajax::response(['status' => 'false', 'version' => $v]);
        }
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
