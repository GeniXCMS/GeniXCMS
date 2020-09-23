<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141001
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Options
{
    /**
     * GeniXCMS Options variable.
     *
     * @return object
     */
    public static $_data;

    public function __construct()
    {
        self::$_data = self::load();
    }

    // $vars = array(
    //             'name' => 'value',
    //         );
    public static function insert($vars)
    {
        if (is_array($vars)) {
            foreach ($vars as $name => $value) {
                $ins = array(
                        'table' => 'options',
                        'key' => array(
                            'name' => Typo::cleanX($name),
                            'value' => Typo::cleanX($value),
                            ),
                    );
                $opt = Db::insert($ins);
            }
        } else {
            Control::error('unknown', 'Format not Found, please in array');
        }

        return $opt;
    }

    public static function update($key, $val = '')
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $k = Typo::cleanX($k);
                $v = Typo::cleanX($v);
                $post = Db::query("UPDATE `options` SET `value`='{$v}' WHERE `name` = '{$k}' LIMIT 1");
            }
        } else {
            $key = Typo::cleanX($key);
            $val = Typo::cleanX($val);
            $post = Db::query("UPDATE `options` SET `value`='{$val}' WHERE `name` = '{$key}' LIMIT 1");
        }

        return $post;
    }

    public static function get($vars, $decode = true)
    {
        $vars = Typo::cleanX($vars);
        $op = Db::result("SELECT `value` FROM `options` WHERE `name` = '{$vars}' LIMIT 1");
        if (Db::$num_rows > 0) {
            return ($decode == true ) ? Typo::Xclean($op[0]->value): $op[0]->value;
        } else {
            return false;
        }
    }

    public static function load()
    {
        $op = Db::result('SELECT * FROM `options` ORDER BY `id` ASC ');
        if (Db::$num_rows > 0) {
            return $op;
        } else {
            return false;
        }
    }

    public static function v($vars)
    {
        $opt = self::$_data;
        // echo "<pre>";
        foreach ($opt as $k => $v) {
            // echo $v->name;
            if ($v->name == $vars) {
                return Typo::Xclean($v->value);
            }
        }
        // echo "</pre>";
    }

    public static function validate($vars)
    {
        $vars = Typo::cleanX($vars);
        $opt = self::get($vars);

        if (false !== $opt) {
            return true;
        } else {
            return false;
        }
    }


}

/* End of file Options.class.php */
/* Location: ./inc/lib/Options.class.php */
