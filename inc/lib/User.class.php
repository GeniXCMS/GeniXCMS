<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140925
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
class User
{
    public static $group = array(
        '0' => ADMINISTRATOR,
        '1' => SUPERVISOR,
        '2' => EDITOR,
        '3' => AUTHOR,
        '4' => CONTRIBUTOR,
        '5' => VIP_MEMBER,
        '6' => GENERAL_MEMBER, );

    public function __construct()
    {
    }

    public static function secure()
    {
        if (!isset($_SESSION['gxsess']['val']['loggedin']) && !isset($_SESSION['gxsess']['val']['username'])) {
            header('Location: '.Site::$url.'login.php');
            // print_r($_SESSION);
            exit;
        } else {

            return true;
        }
    }

    public static function access($grp = '6')
    {
        if (isset($_SESSION['gxsess']['val']['group'])) {
            if ($_SESSION['gxsess']['val']['group'] <= $grp) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function isLoggedin()
    {
        if (isset($_SESSION['gxsess']['val']['loggedin']) && $_SESSION['gxsess']['val']['loggedin'] == 1) {
            $v = true;
        } else {
            $v = false;
        }

        return $v;
    }

    /**
     * Create User Function
     * This will insert certain value of user into the database.
     * <code>
     *    $vars = array(
     *                'user' => array(
     *                                'userid' => '',
     *                                'passwd' => '',
     *                                'email' => '',
     *                                'group' => ''
     *                            ),
     *                'detail' => array(
     *                                'userid' => '',
     *                                'fname' => '',
     *                                'lname' => '',
     *                                'sex' => '',
     *                                'birthplace' => '',
     *                                'birthdate' => '',
     *                                'addr' => '',
     *                                'city' => '',
     *                                'state' => '',
     *                                'country' => '',
     *                                'postcode' => ''
     *                            )
     *            );
     * </code>.
     */
    public static function create($vars)
    {
        if (is_array($vars)) {
            //print_r($vars['user']);
            $ip = [ 'ipaddress' => $_SERVER['REMOTE_ADDR'] ];
            $ipCountry = (!Http::isLocal($ip)) ? Http::getIpCountry($ip): '';
            $u = $vars['user'];
            $u = array_merge($ip, $u);
            $sql = array(
                'table' => 'user',
                'key' => $u,
            );
            $db = Db::insert($sql);

            if (!isset($vars['detail']) || $vars['detail'] == '') {
                Db::insert("INSERT INTO `user_detail` (`userid`, `country`) VALUES ('{$vars['user']['userid']}', '{$ipCountry}')");
            } else {

                $u = $vars['detail'];
                $u = array_merge($u, ['country' => $ipCountry]);
                $sql = array(
                    'table' => 'user_detail',
                    'key' => $u,
                );
                Db::insert($sql);
            }
            Hooks::run('user_sqladd_action', $vars);
        }

        return $db;
    }

    /**
     * Update User Function.
     * This will insert certain value of user into the database.
     * <code>
     *    $vars = array(
     *                'id' => '',
     *                'user' => array(
     *                                'userid' => '',
     *                                'passwd' => '',
     *                                'email' => '',
     *                                'group' => ''
     *                            ),
     *                'detail' => array(
     *                                'userid' => '',
     *                                'fname' => '',
     *                                'lname' => '',
     *                                'sex' => '',
     *                                'birthplace' => '',
     *                                'birthdate' => '',
     *                                'addr' => '',
     *                                'city' => '',
     *                                'state' => '',
     *                                'country' => '',
     *                                'postcode' => ''
     *                            )
     *            );
     * </code>.
     */
    public static function update($vars)
    {
        if (is_array($vars)) {
            //print_r($vars);
            $u = $vars['user'];

            $sql = array(
                'table' => 'user',
                'id' => $vars['id'],
                'key' => $u,
            );
            Db::update($sql);
            if (isset($vars['detail']) && $vars['detail'] != '') {
                $u = $vars['detail'];
                $sql = array(
                    'table' => 'user_detail',
                    'id' => $vars['id'],
                    'key' => $u,
                );
                Db::update($sql);
            }
            Hooks::run('user_sqledit_action', $vars);
        }
    }

    public static function delete($id)
    {
        $id = Typo::int($id);
        $vars = array(
            'table' => 'user',
            'where' => array(
                'id' => $id,
            ),
        );
        Db::delete($vars);

        $vars = array(
            'table' => 'user_detail',
            'where' => array(
                'id' => $id,
            ),
        );
        Db::delete($vars);
        Hooks::run('user_sqldel_action', $vars);
    }

    // $vars = array(
    //                 'userid' => '',
    //                 'passwd' => ''
    //             );
    public static function randpass($vars)
    {
        if (is_array($vars)) {
            $hash = sha1($vars['passwd'].SECURITY_KEY.$vars['userid']);
        } else {
            $hash = sha1($vars.SECURITY_KEY);
        }

        $hash = substr($hash, 5, 16);
        $pass = md5($hash);

        return $pass;
    }

    public static function generatePass()
    {
        $vars = microtime().Site::$name.rand();
        $hash = sha1($vars.SECURITY_KEY);
        $pass = substr($hash, 5, 8);

        return $pass;
    }

    public static function validate($user, $except='')
    {
        if ($except != '') {
            $id = Typo::cleanX(Typo::strip($except));
            $where = "AND `userid` != '{$id}' ";
        } else {
            $where = '';
        }
        $user = Typo::cleanX(Typo::strip($user));
        $sql = sprintf("SELECT * FROM `user` WHERE `userid` = '%s' %s ", $user, $where);
        $usr = Db::result($sql);
        $n = Db::$num_rows;
        if ($n > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isSame($p1, $p2)
    {
        if ($p1 == $p2) {
            return true;
        } else {
            return false;
        }
    }

    public static function isEmail($vars, $id='')
    {
        if (isset($id)) {
            $id = Typo::int($id);
            $where = "AND `id` != '{$id}' ";
        } else {
            $where = '';
        }
        $vars = Typo::cleanX($vars);
        $sql = sprintf("SELECT * FROM `user` WHERE `email` = '%s' %s", $vars, $where);
        $e = Db::result($sql);
        if (Db::$num_rows > 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function id($userid)
    {
        $usr = Db::result(
            sprintf(
                "SELECT `id` FROM `user` WHERE `userid` = '%s' LIMIT 1",
                Typo::cleanX($userid)
            )
        );

        return $usr[0]->id;
    }

    public static function idDetail($userid)
    {
        $usr = Db::result(
            sprintf(
                "SELECT `id` FROM `user_detail` WHERE `userid` = '%s' LIMIT 1",
                Typo::cleanX($userid)
            )
        );

        return $usr[0]->id;
    }

    public static function userid($id)
    {
        $usr = Db::result(
            sprintf(
                "SELECT `userid` FROM `user` WHERE `id` = '%d' LIMIT 1",
                Typo::int($id)
            )
        );

        return $usr[0]->userid;
    }

    public static function email($id)
    {
        $usr = Db::result(
            sprintf(
                "SELECT `email` FROM `user` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1",
                Typo::int($id),
                Typo::cleanX($id)
            )
        );

        return $usr[0]->email;
    }

    public static function group($id)
    {
        $usr = Db::result(
            sprintf(
                "SELECT `group` FROM `user` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1",
                Typo::int($id),
                Typo::cleanX($id)
            )
        );

        return $usr[0]->group;
    }

    public static function regdate($id)
    {
        $usr = Db::result(
            sprintf(
                "SELECT `join_date` FROM `user` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1",
                Typo::int($id),
                Typo::cleanX($id)
            )
        );

        return $usr[0]->join_date;
    }

    public static function avatar($id)
    {
        $usr = Db::result(
            sprintf(
                "SELECT `avatar` FROM `user_detail` WHERE `id` = '%d' OR `userid` = '%s' LIMIT 1",
                Typo::int($id),
                Typo::cleanX($id)
            )
        );

        return $usr[0]->avatar;
    }

    public static function activate($id)
    {
        $act = Db::query(
            sprintf(
                "UPDATE `user` SET `status` = '1' WHERE `id` = '%d'",
                Typo::int($id)
            )
        );
        if ($act) {
            return true;
        } else {
            return false;
        }
    }

    public static function deactivate($id)
    {
        $act = Db::query(
            sprintf(
                "UPDATE `user` SET `status` = '0' WHERE `id` = '%d'",
                Typo::int($id)
            )
        );
        if ($act) {
            return true;
        } else {
            return false;
        }
    }

    // $vars = array(
    //         'name' => '',
    //         'selected' => '',
    //         'update' => true
    //     );
    public static function dropdown($vars)
    {
        $html = '<select name="'.$vars['name'].'" class="form-control">';
        $html .= (!isset($vars['selected']) && !isset($vars['update'])) ? '<option value="">All Group</option>' : '';
        foreach (self::$group as $key => $value) {
            $selected = (isset($vars['selected']) && $vars['selected'] == $key) ? 'selected' : '';
            $ugroup = Session::val('group');
            if ($ugroup <= $key && isset($vars['update']) && $vars['update'] == true) {
                $html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            } else {
                $html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            }
        }
        $html .= '</select>';

        return $html;
    }

    public static function listRecentBox($max=10)
    {
        $sql = "SELECT * FROM `user` ORDER BY `join_date` DESC LIMIT {$max}";
        $q = Db::result($sql);
        echo "<ul  class=\"users-list clearfix\">";
        foreach ($q as $k => $v) {
            echo "<li>
                <img src='".Image::getGravatar($v->email)."'>
                <a class=\"users-list-name\" href=\"#\">{$v->userid}</a>
                <span class=\"users-list-date\">".Date::format($v->join_date)."</span>
            </li>";
        }
        echo "</ul>";
    }

    public static function jsonUserLocation()
    {
        $sql = "SELECT DISTINCT `country` FROM `user_detail`";
        $q = Db::result($sql);
        $ctr = array();
        foreach ($q as $k => $v) {
            if ($v->country != '') {
                $sql2 = "SELECT * FROM `user_detail` WHERE `country` = '{$v->country}'";
                $q2 = Db::result($sql2);
                $ctr[$v->country] = Db::$num_rows;
            }
        }
//        print_r($ctr);
        return json_encode($ctr);
    }

    public static function checkLastRequestPassword() 
    {
        $reqPass = Session::val('reqPass');
        $lastReq = !empty($reqPass) ? $reqPass['time']: 0;

        return $lastReq;
    }

    public static function setLastRequestPassword() 
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = time();
        
        $vars = array(
            'reqPass' => array(
                    'time' => $time,
                    'ip'    => $ip
                )
            );
        Session::set($vars);
    }

    public static function lastRequestPassword()
    {
        $limit = 1200;

        $lastReq = self::checkLastRequestPassword();
        $reqTime = time() - $lastReq;

        if ($lastReq == 0 || $reqTime > $limit ) {
            self::setLastRequestPassword();

            return true;
        } else {

            return false;
        }
    }
}

/* End of file user.class.php */
/* Location: ./inc/lib/user.class.php */
