<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 1.0.0 build date 20160804
 * @version 1.0.0
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
*/

if (isset($_GET['token']) && Token::isExist($_GET['token'])) {
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
