<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20141001
 *
 * @version 2.0.1
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
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
                $opt = Query::table('options')->insert([
                    'name' => Typo::cleanX($name),
                    'value' => Typo::cleanX($value)
                ]);
            }
        } else {
            Control::error('unknown', _('Format not Found, please in array'));
        }

        return $opt;
    }

    public static function update($key, $val = '')
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $k = Typo::cleanX($k);
                $v = Typo::cleanX($v);
                $exist = self::get($k);
                if ($exist === false) {
                    Query::table('options')->insert([
                        'name' => $k,
                        'value' => $v
                    ]);
                } else {
                    Query::table('options')->where('name', $k)->update(['value' => $v]);
                }
            }
        } else {
            $key = Typo::cleanX($key);
            $val = Typo::cleanX($val);
            $exist = self::get($key);
            if ($exist === false) {
                Query::table('options')->insert([
                    'name' => $key,
                    'value' => $val
                ]);
            } else {
                Query::table('options')->where('name', $key)->update(['value' => $val]);
            }
        }

        return true;
    }

    public static function get($vars, $decode = true)
    {
        $vars = Typo::cleanX($vars);
        $op = Query::table('options')->where('name', $vars)->first();

        if ($op) {
            return ($decode == true) ? Typo::Xclean($op->value) : $op->value;
        } else {
            return false;
        }
    }

    public static function load()
    {
        return Query::table('options')->orderBy('id', 'ASC')->get();
    }

    public static function v($vars)
    {
        $opt = self::$_data;
        if (is_array($opt)) {
            foreach ($opt as $k => $v) {
                if ($v->name == $vars) {
                    if ($v->value != "" && $v->value != NULL) {
                        return Typo::Xclean($v->value);
                    } else {
                        return '';
                    }

                }
            }
        }
        return '';
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

    public static function isExist($var)
    {
        return self::validate($var);
    }


}

/* End of file Options.class.php */
/* Location: ./inc/lib/Options.class.php */
