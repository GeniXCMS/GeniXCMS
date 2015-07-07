<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.6 build date 20150706
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/



class Hooks
{
    public static $hooks;

    public function __construct() {
        self::$hooks = self::load();
        
    }

    public static function add() {
        
    }

    public static function load() {
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
                'admin_page_dashboard_action' => array(),
                'admin_page_dashboard_statslist_action' => array(),
                'module_install_action' => array(),
                'theme_install_action' => array(),

            );

        return $hooks;
    }

    public static function attach($hooks_name, $func) {
        $hooks = self::$hooks;
        $hooks[$hooks_name][] = $func;
        self::$hooks = $hooks;
        return self::$hooks;
    }

    /**
    * Run the hooks
    *
    * @link http://stackoverflow.com/questions/42/best-way-to-allow-plugins-for-a-php-application/77#77
    */
    public static function run() {
        //print_r(self::$hooks[$var]);
        $hooks = self::$hooks;
        $num_args = func_num_args();
        $args = func_get_args();
        // print_r($args);
        // if($num_args < 2)
        //     trigger_error("Insufficient arguments", E_USER_ERROR);

        // Hook name should always be first argument
        $hook_name = array_shift($args);

        if(!isset($hooks[$hook_name]))
            return; // No plugins have registered this hook
        // print_r($args[0]);
        // $args = (is_array($args))?$args[0]: $args;
        if (is_array($hooks[$hook_name])) {
            $val = '';
            foreach($hooks[$hook_name] as $func){
                if ($func != '') {
                    // $args = call_user_func_array($func, $args); //
                    $val .= $func((array)$args); 
                }else{
                    $val .= $args;
                }

            }
            return $val;
        }
        
    }

    public static function filter() {
        //print_r(self::$hooks[$var]);
        $hooks = self::$hooks;
        $num_args = func_num_args();
        $args = func_get_args();
        // print_r($args);
        // if($num_args < 2)
        //     trigger_error("Insufficient arguments", E_USER_ERROR);

        // Hook name should always be first argument
        $hook_name = array_shift($args);

        if(!isset($hooks[$hook_name]))
            return; // No plugins have registered this hook
        // print_r($args[0]);
        // $args = (is_array($args))?$args[0]: $args;
        if (is_array($hooks[$hook_name])) {
            
            foreach($hooks[$hook_name] as $func){
                if ($func != '') {
                    // $args = call_user_func_array($func, $args); //
                    $args = $func((array)$args); 
                }else{
                    $args = $args;
                }
                
            }

            $args = $args;
        }else{
            $args = $args;
        }

        $args = is_array($args)?  $args[0]:  $args;
        return $args;
    }



    public static function getKey ($var) {
        return self::$hooks[$var];
    }
}