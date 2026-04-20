<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.6 build date 20150706
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Hooks
{
    public static $hooks;

    /**
     * Hooks Constructor.
     * Loads the initial hooks registry.
     */
    public function __construct()
    {
        self::$hooks = self::load();
    }

    /**
     * @deprecated 2.0.0 Use attach() to register your functions to specific hooks.
     */
    public static function add()
    {
    }

    /**
     * Returns the comprehensive list of registered hook points in the system.
     * Hooks are divided into actions (execution points) and filters (data modification points).
     *
     * @return array Initialized hooks collection.
     */
    public static function load()
    {
        $hooks = array(
            'init' => array(), // init the system
            'header_pre_action' => array(), // action before load header
            'site_title_filter' => array(), // filter website title
            'site_desc_filter' => array(),
            'site_key_filter' => array(),
            'header_load_meta' => array(),
            'footer_load_lib' => array(),
            'post_title_filter' => array(),
            'post_meta_filter' => array(),
            'post_content_filter' => array(),
            'post_content_before_action' => array(),
            'post_content_after_action' => array(),
            'post_author_filter' => array(),
            'post_date_filter' => array(),
            'post_category_filter' => array(),
            'post_submit_add_action' => array(), // on submit
            'post_sqladd_action' => array(), // on sql add
            'post_submit_edit_action' => array(), // on submit
            'post_sqledit_action' => array(), // on sql add
            'post_submit_title_filter' => array(),
            'post_submit_content_filter' => array(),
            'post_submit_category_filter' => array(),
            'post_delete_action' => array(), // on delete
            'post_sqldel_action' => array(), // on sql delete
            'post_param_form_bottom' => array(),
            'post_param_form_sidebar' => array(),
            'page_param_form_bottom' => array(),
            'page_param_form_sidebar' => array(),
            'user_submit_add_action' => array(),
            'user_sqladd_action' => array(),
            'user_submit_edit_action' => array(),
            'user_sqledit_action' => array(),
            'user_delete_action' => array(),
            'user_sqldel_action' => array(),
            'user_reg_action' => array(),
            'user_login_action' => array(),
            'user_logout_action' => array(),
            'user_activation_action' => array(),
            'admin_page_notif_action' => array(),
            'admin_page_top_action' => array(),
            'admin_page_bottom_action' => array(),
            'admin_header_top_right_action' => array(),
            'admin_page_dashboard_action' => array(),
            'admin_page_dashboard_statslist_action' => array(),
            'admin_footer_action' => array(),
            'dynamic_builder_blocks' => array(),
            'dynamic_builder_layout' => array(),
            'dynamic_builder_css' => array(),
            'dynamic_builder_js' => array(),
            'module_install_action' => array(),
            'theme_install_action' => array(),
            'mod_control' => array(),
            'login_form_header' => array(),
            'login_form_footer' => array(),
            'admin_dashboard_schema' => array(), // dashboard schema filter
            'search_type_filter' => array(), // search filter
            'breadcrumbs_filter' => array(), // breadcrumbs filter
            'post_url' => array(), // post url filter
        );

        return $hooks;
    }

    /**
     * Attaches a callback function or method to a specific hook name.
     *
     * @param string   $hooks_name The name of the hook to attach to.
     * @param callable $func       The callback function or method array.
     * @return array               The updated hooks registry.
     */
    public static function attach($hooks_name, $func)
    {
        global $data;
        $hooks = self::$hooks;
        $hooks[$hooks_name][] = $func;
        self::$hooks = $hooks;

        return self::$hooks;
    }

    /**
     * Executes all functions attached to an action hook.
     * Multiple arguments can be passed; the first one must always be the hook name.
     *
     * @param string $hook_name The name of the hook to run.
     * @param mixed  ...$args   Additional arguments passed to the hooked functions.
     * @return string           Combined output from all hooked functions.
     */
    public static function run($hook_name, ...$args)
    {
        $hooks = self::$hooks;

        if (!isset($hooks[$hook_name])) {
            return '';
        }

        if (is_array($hooks[$hook_name])) {
            $val = '';
            foreach ($hooks[$hook_name] as $func) {
                if ($func != '') {
                    $ret = $func($args);
                    if (!is_array($ret)) {
                        $val .= $ret;
                    }
                }
            }
            return $val;
        }
        return '';
    }

    /**
     * Passes data through all filters attached to a specific hook name.
     * The data is modified progressively by each attached function.
     *
     * @param string $hook_name The name of the filter hook.
     * @param mixed  ...$args   The data to be filtered and additional context.
     * @return mixed            The final filtered data.
     */
    public static function filter($hook_name, ...$args)
    {
        $hooks = self::$hooks;

        if (!isset($hooks[$hook_name])) {
            return (isset($args[0])) ? $args[0] : '';
        }

        if (is_array($hooks[$hook_name])) {
            foreach ($hooks[$hook_name] as $func) {
                if ($func != '') {
                    $args = $func((array) $args);
                    if (!is_array($args)) {
                        $args = [$args];
                    }
                }
            }
        }

        if (is_array($args) && count($args) === 1 && isset($args[0])) {
            $args = $args[0];
        }

        return $args;
    }

    /**
     * Retrieves all callbacks registered for a specific hook.
     *
     * @param string $var Hook name.
     * @return array      List of attached callbacks.
     */
    public static function getKey($var)
    {
        return self::$hooks[$var];
    }

    /**
     * Checks if a specific callback already exists for a given hook.
     *
     * @param string|array $val   The callback to look for.
     * @param string       $hooks The hook name to check in.
     * @return bool               True if the callback is already attached.
     */
    public static function exist($val, $hooks)
    {
        //         print_r(self::getKey($hooks));
        $hooked = self::getKey($hooks);
        $n = 0;
        for ($i = 0; $i < count($hooked); ++$i) {
            if (in_array($val, $hooked[$i])) {
                $n = $n + 1;
            }
        }

        if ($n > 0) {
            return true;
        } else {
            return false;
        }
    }
}
