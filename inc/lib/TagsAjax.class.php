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

class TagsAjax
{
    /**
     * GeniXCMS - Content Management System
     */
    public function index($param = null)
    {
        if (!$this->_auth($param)) {
            return Ajax::response(['status' => 'error', 'message' => 'Token invalid']);
        }

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

        return Ajax::response($tag2);
    }

    /**
     * Internal auth check
     */
    private function _auth($param = null)
    {
        if (!User::access(2)) {
            return false;
        }

        $data = Router::scrap($param);
        $gettoken = (SMART_URL) ? ($data['token'] ?? '') : (Typo::cleanX($_GET['token'] ?? ''));
        return Token::validate($gettoken, true);
    }
}
