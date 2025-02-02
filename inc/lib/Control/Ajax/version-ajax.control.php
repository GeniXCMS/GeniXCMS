<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 1.0.0 build date 20160804
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
        $v = trim(System::latestVersion());

        // print_r($v);
        $v2 = str_replace('.', '', $v);
        $selfv = str_replace('.', '', System::$version);
        if ($v2 < $selfv || $v2 == $selfv) {
            echo '{"status": "true"}';
        } else {
            echo '{"status": "false", "version":"'.$v.'"}';
        }
    } else {
        echo '{"status":"error"}';
    }
} else {
    echo '{"status":"Token not exist"}';
}
