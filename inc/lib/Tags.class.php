<?php

if (defined('GX_LIB') === false) {
    die('Direct Access Not Allowed!');
}
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.8 build date 20160317
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2016 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Categories Class.
 *
 * This class will process the categories function. Including Create, Edit,
 * Delete the categories.
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
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
     * @author Puguh Wijayanto <psw@metalgenix.com>
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
     * @author Puguh Wijayanto <psw@metalgenix.com>
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
     * @author Puguh Wijayanto <psw@metalgenix.com>
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
     * @author Puguh Wijayanto <psw@metalgenix.com>
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
    public static function add($tags)
    {
        $tag = explode(',', $tags);

        for ($i = 0; $i < count($tag); ++$i) {
            // echo($i);
            $tag_i = Typo::cleanX($tag[$i]);
            // echo $tag[$i];
            $exist = self::exist($tag_i);
            if (!$exist) {
                if ($tag_i != '') {
                    $slug = Typo::slugify($tag_i);
                    $cat = $tag_i;
                    Db::insert(
                        sprintf(
                            "INSERT INTO `cat` VALUES (null, '%s', '%s', '%d', '', 'tag' )",
                            $cat,
                            $slug,
                            0
                        )
                    );
                }
            }
        }
    }

    public static function exist($tag)
    {
        $tag = Typo::cleanX($tag);
        $sql = "SELECT `name` FROM `cat` WHERE `name` = '{$tag}' AND `type` = 'tag'";
        $q = Db::result($sql);
        // echo Db::$num_rows;
        if (Db::$num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function id($name)
    {
        return Categories::id($name);
    }
}

/* End of file Categories.class.php */
/* Location: ./inc/lib/Categories.class.php */
