<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 0.0.8 build date 20160317
 * @version 2.0.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
$data = Router::scrap($param);
$gettoken = (SMART_URL) ? ($data['token'] ?? '') : (Typo::cleanX($_GET['token'] ?? ''));
$tokenValid = Token::validate($gettoken, true);

if ($tokenValid) {
    if (User::access(2)) {
        $term = isset($_GET['term']) ? Typo::cleanX($_GET['term']) : '';
        $postType = Typo::cleanX($_GET['type'] ?? 'post');
        $tagType = ($postType === 'post') ? 'tag' : "{$postType}_tag";
        $tags = Query::table('cat')
            ->select('name')
            ->where('type', $tagType)
            ->where('name', 'LIKE', "%{$term}%")
            ->orderBy('name', 'ASC')
            ->get();
        $tag2 = [];
        if (!empty($tags)) {
            foreach ($tags as $t) {
                if (is_object($t))
                    $tag2[] = $t->name;
            }
        }

        echo json_encode($tag2);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No access']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Token invalid']);
}
