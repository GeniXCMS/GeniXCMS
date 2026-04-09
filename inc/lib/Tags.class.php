<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.8 build date 20160317
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Tags Class.
 *
 * This class will process the categories function. Including Create, Edit,
 * Delete the categories.
 *
 * @since 0.0.1
 */
class Tags
{
    public function __construct()
    {
    }

    /**
     * Tags Dropdown Function.
     *
     * This will list the categories into the HTML Dropdown Below are how to use
     * it :
     * <code>
     * $vars = array(
     *             'name' => 'catname',
     *             'parent' => 'parent',
     *             'order_by' => '',
     *             'sort' => 'ASC',
     *             'type' => ''
     *             );
     * Tags::dropdown($vars);
     * </code>
     *
     * @param array $vars the delivered data must be in array with above format
     *
     * @uses Db::result();
     *
     * @since 0.0.1
     */
    public static function dropdown($vars)
    {
        return Categories::dropdown($vars);
    }

    public static function lists($vars)
    {
        return Categories::lists($vars);
    }

    /**
     * Tag Name function.
     *
     * This will get the specified ID Tag name
     *
     * @param int $id
     *
     * @uses Db::result();
     *
     * @since 0.0.1
     */
    public static function name($id)
    {
        return Categories::name($id);
    }

    /**
     * Tag Get Parent function.
     *
     * This will get the specified ID Tag parent data
     *
     * @param int $id
     *
     * @uses Db::result();
     *
     * @since 0.0.1
     */
    public static function getParent($id = '')
    {
        return Categories::getParent($id);
    }

    /**
     * Tag Delete function.
     *
     * This will delete the specified ID Tag data
     *
     * @param int   $id
     * @param array $sql
     *
     * @uses self::getParent();
     * @uses Db::delete();
     * @uses Db::result();
     * @uses Db::$num_rows;
     *
     * @since 0.0.1
     */
    public static function delete($id)
    {
        return Categories::delete($id);
    }

    public static function type($id)
    {
        return Categories::type($id);
    }

    // $tags = "tag1, tag2, tag3";
    public static function add($tags, $postType = 'post')
    {
        $tagType = ($postType === 'post') ? 'tag' : "{$postType}_tag";
        $tag = explode(',', $tags);

        for ($i = 0; $i < count($tag); ++$i) {
            // echo($i);
            $tag_i = Typo::cleanX($tag[$i]);
            // echo $tag[$i];
            $exist = self::exist($tag_i, $postType);
            if (!$exist) {
                if ($tag_i != '') {
                    $slug = Typo::slugify($tag_i);
                    $cat = Typo::cleanX($tag_i);
                    Query::table('cat')->insert([
                        'name' => $cat,
                        'slug' => $slug,
                        'parent' => 0,
                        'desc' => '',
                        'type' => $tagType
                    ]);
                }
            }
        }
    }

    public static function exist($tag, $postType = 'post')
    {
        $tagType = ($postType === 'post') ? 'tag' : "{$postType}_tag";
        $tag = Typo::cleanX($tag);
        $cat = Query::table('cat')
            ->where('name', $tag)
            ->orWhere('slug', $tag)
            ->where('type', $tagType)
            ->first();

        return ($cat) ? true : false;
    }

    public static function id($name)
    {
        return Categories::id($name);
    }

    public static function slug($id)
    {
        return Categories::slug($id);
    }

    public static function count($tag)
    {
        return Query::table('posts_param')
            ->where('param', 'tags')
            ->where('value', 'LIKE', "%{$tag}%")
            ->count();
    }

    public static function cloud()
    {
        // get all tags first
        $q = Query::table('cat')->where('type', 'tag')->get();
        if ($q) {
            $tags = [];
            foreach ($q as $key => $value) {
                $tags[$value->name] = self::count($value->name);
            }
            arsort($tags);
            $cloud = "";
            foreach ($tags as $key => $value) {
                $cloud .= "<a class='tag-item' href='" . Url::tag($key) . "'>{$key} <span class='tag-count'>{$value}</span></a> ";
            }

        } else {
            $cloud = '';
        }
        return $cloud;
    }
    public static function search($q)
    {
        $q = Typo::cleanX($q);
        return Query::table('cat')
            ->where('type', 'tag')
            ->where('name', 'LIKE', "%{$q}%")
            ->limit(10)
            ->get() ?: [];
    }
}
