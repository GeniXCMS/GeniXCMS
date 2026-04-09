<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20140925
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class User
{
    public static $group = array(
        '0' => "Administrator",
        '1' => "Supervisor",
        '2' => "Editor",
        '3' => "Author",
        '4' => "Contributor",
        '5' => "VIP Member",
        '6' => "General Member"
    );

    /**
     * User constructor.
     */
    public function __construct()
    {
    }

    /**
     * Secures the current page by ensuring the user is logged in.
     * Redirects to the login page if the session is invalid.
     *
     * @return bool True if authorized.
     */
    public static function secure()
    {
        if (!isset($_SESSION['gxsess']['val']['loggedin']) && !isset($_SESSION['gxsess']['val']['username'])) {
            $url = Url::login("backto=" . urlencode(Site::canonical()));
            header("Location: $url");
            exit;
        } else {

            return true;
        }
    }

    /**
     * Checks if the currently logged-in user has a specific minimum access level.
     *
     * @param string $grp Minimum required group level (0-6).
     * @return bool       True if authorized.
     */
    public static function access($grp = '6')
    {
        if (isset($_SESSION['gxsess']['val']['group'])) {
            if ($_SESSION['gxsess']['val']['group'] <= $grp) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Verifies if a user session is active and logged in.
     *
     * @return bool True if logged in.
     */
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
            $vars = Hooks::filter('user_pre_insert_filter', $vars);
            //print_r($vars['user']);
            $ip = ['ipaddress' => $_SERVER['REMOTE_ADDR']];
            $ipCountry = (!Http::isLocal($_SERVER['REMOTE_ADDR'])) ? Http::getIpCountry($_SERVER['REMOTE_ADDR']) : '';
            $u = $vars['user'];
            $u = array_merge($ip, $u);

            $db = Query::table('user')->insert($u);

            if (!isset($vars['detail']) || $vars['detail'] == '') {
                Query::table('user_detail')->insert([
                    'userid' => $vars['user']['userid'],
                    'country' => $ipCountry
                ]);
            } else {
                $ud = $vars['detail'];
                $ud = array_merge($ud, ['country' => $ipCountry]);
                Query::table('user_detail')->insert($ud);
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
            $vars = Hooks::filter('user_pre_update_filter', $vars);
            //print_r($vars);
            $u = $vars['user'];

            Query::table('user')->where('id', $vars['id'])->update($u);
            if (isset($vars['detail']) && $vars['detail'] != '') {
                $ud = $vars['detail'];
                Query::table('user_detail')->where('id', $vars['id'])->update($ud);
            }
            Hooks::run('user_sqledit_action', $vars);
        }
    }

    /**
     * Deletes a user account and their associated profile details.
     *
     * @param int $id User ID to delete.
     */
    public static function delete($id)
    {
        $id = Typo::int($id);
        Query::table('user')->where('id', $id)->delete();
        Query::table('user_detail')->where('id', $id)->delete();
        Hooks::run('user_sqldel_action', ['id' => $id]);
    }

    // $vars = array(
    //                 'userid' => '',
    //                 'passwd' => ''
    //             );
    /**
     * Generates a truncated MD5 hash for password randomization or legacy verification.
     *
     * @param array|string $vars Dictionary containing 'userid' and 'passwd' or plain password.
     * @return string             Modified password hash.
     */
    public static function randpass($vars)
    {
        if (is_array($vars)) {
            $hash = sha1($vars['passwd'] . SECURITY_KEY . $vars['userid']);
        } else {
            $hash = sha1($vars . SECURITY_KEY);
        }

        $hash = substr($hash, 5, 16);
        $pass = md5($hash);

        return $pass;
    }

    /**
     * Generates a random secure password string.
     *
     * @return string Random 8-character string.
     */
    public static function generatePass()
    {
        $vars = microtime() . Site::$name . rand();
        $hash = sha1($vars . SECURITY_KEY);
        $pass = substr($hash, 5, 8);

        return $pass;
    }

    /**
     * Validates if a username exists in the system.
     *
     * @param string $user   Username to check.
     * @param string $except Username to exclude from the check (for updates).
     * @return bool           True if user exists.
     */
    public static function validate($user, $except = '')
    {
        $q = Query::table('user')->where('userid', Typo::cleanX(Typo::strip($user)));
        if ($except != '') {
            $q->where('userid', '!=', Typo::cleanX(Typo::strip($except)));
        }
        $usr = $q->first();
        return ($usr) ? true : false;
    }

    /**
     * Compares two strings (e.g., password verification).
     *
     * @param string $p1 First string.
     * @param string $p2 Second string.
     * @return bool       True if identical.
     */
    public static function isSame($p1, $p2)
    {
        if ($p1 == $p2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates if an email address is already registered.
     *
     * @param string $vars Email address.
     * @param string $id   Exclude user ID (for updates).
     * @return bool         True if available (not found).
     */
    public static function isEmail($vars, $id = '')
    {
        $q = Query::table('user')->where('email', Typo::cleanX($vars));
        if ($id != '') {
            $q->where('id', '!=', Typo::int($id));
        }
        $e = $q->first();
        return ($e) ? false : true;
    }

    /**
     * Retrieves the numeric ID for a given username.
     *
     * @param string $userid Username.
     * @return string|int    User ID.
     */
    public static function id($userid)
    {
        $usr = Query::table('user')->where('userid', $userid)->first();
        return (isset($usr->id)) ? $usr->id : '';
    }

    /**
     * Retrieves the detail record ID for a given username.
     *
     * @param string $userid Username.
     * @return string|int    Detail record ID.
     */
    public static function idDetail($userid)
    {
        $usr = Query::table('user_detail')->where('userid', $userid)->first();
        return (isset($usr->id)) ? $usr->id : '';
    }

    /**
     * Fetches core user data by ID.
     *
     * @param int $id User ID.
     * @return object  User database record.
     */
    public static function v($id)
    {
        return Query::table('user')->where('id', Typo::int($id))->first();
    }

    /**
     * Fetches comprehensive user data including profile details.
     *
     * @param int $id User ID.
     * @return object  Full user profile record.
     */
    public static function userdata($id)
    {
        return Query::table('user')
            ->select('user.*, ud.fname, ud.lname, ud.sex, ud.addr, ud.city, ud.state, ud.country, ud.postcode')
            ->join('user_detail as ud', 'user.userid', '=', 'ud.userid')
            ->where('user.id', Typo::int($id))
            ->first();
    }

    /**
     * Retrieves the Username string for a given ID.
     *
     * @param int $id User ID.
     * @return string  Username.
     */
    public static function userid($id)
    {
        $usr = self::v($id);
        return (isset($usr->userid)) ? $usr->userid : '';
    }

    /**
     * Retrieves the Email address for a given ID or Username.
     *
     * @param int|string $id User ID or Username.
     * @return string         Email address.
     */
    public static function email($id)
    {
        $usr = Query::table('user')->where('id', $id)->orWhere('userid', $id)->first();
        return (isset($usr->email)) ? $usr->email : '';
    }

    /**
     * Retrieves the Access Group ID for a given ID or Username.
     *
     * @param int|string $id User ID or Username.
     * @return string         Group ID.
     */
    public static function group($id)
    {
        $usr = Query::table('user')->where('id', $id)->orWhere('userid', $id)->first();
        return (isset($usr->group)) ? $usr->group : '';
    }

    /**
     * Retrieves the registration date for a given ID or Username.
     *
     * @param int|string $id User ID or Username.
     * @return string         Registration date string.
     */
    public static function regdate($id)
    {
        $usr = Query::table('user')->where('id', $id)->orWhere('userid', $id)->first();
        return (isset($usr->join_date)) ? $usr->join_date : '';
    }

    /**
     * Retrieves the avatar URL/path for a given ID or Username.
     *
     * @param int|string $id User ID or Username.
     * @return string         Avatar content.
     */
    public static function avatar($id)
    {
        $usr = Query::table('user_detail')->where('id', $id)->orWhere('userid', $id)->first();
        return (isset($usr->avatar)) ? $usr->avatar : '';
    }

    /**
     * Activates a user account (set status to 1).
     *
     * @param int $id User ID.
     * @return bool    Result.
     */
    public static function activate($id)
    {
        return Query::table('user')->where('id', $id)->update(['status' => '1']);
    }

    /**
     * Deactivates a user account (set status to 0).
     *
     * @param int $id User ID.
     * @return bool    Result.
     */
    public static function deactivate($id)
    {
        return Query::table('user')->where('id', $id)->update(['status' => '0']);
    }

    // $vars = array(
    //         'name' => '',
    //         'selected' => '',
    //         'update' => true
    //     );
    /**
     * Generates an HTML dropdown (<select>) for user group selection.
     *
     * @param array $vars {
     *     @type string $name     Select element name attribute.
     *     @type string $selected Currently selected group ID.
     *     @type string $class    CSS classes.
     *     @type string $attr     Additional element attributes.
     * }
     * @return string              Generated HTML markup.
     */
    public static function dropdown($vars)
    {
        $class = (isset($vars['class'])) ? $vars['class'] : 'form-control';
        $attr = (isset($vars['attr'])) ? $vars['attr'] : '';
        $html = '<select name="' . $vars['name'] . '" class="' . $class . '" ' . $attr . '>';
        $html .= (!isset($vars['selected']) && !isset($vars['update'])) ? '<option value="">' . _('All Group') . '</option>' : '';
        foreach (self::$group as $key => $value) {
            $selected = (isset($vars['selected']) && $vars['selected'] == $key) ? 'selected' : '';
            $ugroup = Session::val('group');
            if ($ugroup <= $key && isset($vars['update']) && $vars['update'] == true) {
                $html .= '<option value="' . $key . '" ' . $selected . '>' . _($value) . '</option>';
            } else {
                $html .= '<option value="' . $key . '" ' . $selected . '>' . _($value) . '</option>';
            }
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Renders a grid box of recent users (for dashboard display).
     *
     * @param int $max Maximum number of users to show.
     */
    public static function listRecentBox($max = 10)
    {
        $q = Query::table('user')->orderBy('join_date', 'DESC')->limit($max)->get();
        if ($q) {
            foreach ($q as $k => $v) {
                echo "
                <div class=\"col-3 p-2\">
                    <img class=\"img-fluid rounded-circle\" src=\"" . Image::getGravatar($v->email) . "\" alt=\"User Image\">
                    <a class=\"btn fw-bold fs-7 text-secondary text-truncate w-100 p-0\" href=\"#\">
                        {$v->userid}
                    </a>
                    <div class=\"fs-8\">" . Date::format($v->join_date) . "</div>
                </div>
                ";
            }
        }
    }

    /**
     * Generates a JSON map of user counts by country.
     *
     * @return string JSON encoded array.
     */
    public static function jsonUserLocation()
    {
        $q = Db::result("SELECT DISTINCT `country` FROM `user_detail` ");
        $ctr = array();
        if ($q) {
            foreach ($q as $k => $v) {
                if ($v->country != '') {
                    $count = Query::table('user_detail')->where('country', $v->country)->count();
                    $ctr[$v->country] = $count;
                }
            }
        }
        return json_encode($ctr);
    }

    /**
     * Checks the timestamp of the last password reset request.
     *
     * @return int Unix timestamp.
     */
    public static function checkLastRequestPassword()
    {
        $reqPass = Session::val('reqPass');
        $lastReq = !empty($reqPass) ? $reqPass['time'] : 0;

        return $lastReq;
    }

    /**
     * Records the current timestamp and IP for a password reset request.
     */
    public static function setLastRequestPassword()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $time = time();

        $vars = array(
            'reqPass' => array(
                'time' => $time,
                'ip' => $ip
            )
        );
        Session::set($vars);
    }

    /**
     * Validates if enough time has passed since the last password request.
     * Limits requests to once per 20 minutes (1200 seconds).
     *
     * @return bool True if the request is allowed.
     */
    public static function lastRequestPassword()
    {
        $limit = 1200;

        $lastReq = self::checkLastRequestPassword();
        $reqTime = time() - $lastReq;

        if ($lastReq == 0 || $reqTime > $limit) {
            self::setLastRequestPassword();

            return true;
        } else {

            return false;
        }
    }
}
