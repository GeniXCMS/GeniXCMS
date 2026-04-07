<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 1.0.0 build date 20160804
 * @version 2.1.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
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
            echo '{"status": "true", "version":"' . $v . '"}';
        } else {
            echo '{"status": "false", "version":"' . $v . '"}';
        }
    } else {
        echo '{"status":"error"}';
    }
} else {
    echo '{"status":"Token not exist"}';
}
