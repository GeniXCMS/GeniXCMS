<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/*
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 0.0.8 build date 20160317
 * @version 1.0.0
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
*/
$data = Router::scrap($param);
$token = (SMART_URL) ? $data['token'] : Typo::cleanX($_GET['token']);
if (isset($token) && Token::isExist($token) ) {
    if (User::access(2)) {
        $term = Typo::cleanX($_GET['term']);
        $tags = Db::result(
            "SELECT * FROM `cat` WHERE `type` = 'tag' AND `name` LIKE '".$term."%' ORDER BY `name` ASC"
        );
        $tag = array();
        foreach ($tags as $t) {
            $tag2[] = array(
                'label' => $t->name,
            );
            $tag = array_merge($tag, $tag2);
        }
        echo json_encode($tag2);
    } else {
        echo '{"status":"error"}';
    }
} else {
    echo '{"status":"Token not exist"}';
}
