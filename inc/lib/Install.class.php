<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20150126
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
* Installation Class
*
* This class will process the Installation Process.
* 
* 
* @author Puguh Wijayanto (www.metalgenix.com)
* @since 0.0.1
*/
class Install
{
    function __construct () {

    }

    /**
    * Config File Creation Function.
    * This will create config file at inc/config/config.php during the installation
    * process. Data is gathered from the session.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function makeConfig ($file) {
        $config = "<?php if(!defined('GX_LIB')) die(\"Direct Access Not Allowed!\");
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140925
* @version 0.0.3
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/error_reporting(0);

// DB CONFIG
define('DB_HOST', '".Session::val('dbhost')."');
define('DB_NAME', '".Session::val('dbname')."');
define('DB_PASS', '".Session::val('dbpass')."');
define('DB_USER', '".Session::val('dbuser')."');
define('DB_DRIVER', 'mysqli');


define('GX_LANG', 'english');
define('SMART_URL', false); //set 'true' if you want use SMART URL (SEO Friendly URL)
define('GX_URL_PREFIX', '.html');








// DON't REMOVE or EDIT THIS.
define('SECURITY', '".Typo::getToken(200)."'); // for security purpose, will be used for creating password

        ";
        try{
            $f = fopen($file, "w");
            $c = fwrite($f, $config);
            fclose($f);
        }catch (Exception $e) {
            echo $e->getMessage();
        }
        
        return $config;
    }

    /**
    * Create Initial SQL Table Function.
    * This will create the SQL table on the installation proccess.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function createTable () {
        require_once(GX_PATH.'/inc/config/config.php');
        $db = new Db();
        $cat = "CREATE TABLE IF NOT EXISTS `cat` (
                `id` int(11) NOT NULL,
                  `name` text NOT NULL,
                  `slug` text NOT NULL,
                  `parent` text DEFAULT NULL,
                  `desc` text DEFAULT  NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ";
        $db->query($cat);

        $pr = "ALTER TABLE `cat` ADD PRIMARY KEY (`id`)";
        $db->query($pr);

        $pr = "ALTER TABLE `cat` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
        $db->query($pr);

        $menu = "CREATE TABLE IF NOT EXISTS `menus` (
                `id` int(11) NOT NULL,
                  `name` varchar(64) NOT NULL,
                  `menuid` varchar(32) NOT NULL,
                  `parent` varchar(11) DEFAULT  NULL,
                  `sub` enum('0','1') NOT NULL,
                  `type` varchar(8) NOT NULL,
                  `value` text NOT NULL,
                  `class` varchar(64) DEFAULT NULL,
                  `order` varchar(4) DEFAULT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";

        $db->query($menu);

        $pr = "ALTER TABLE `menus` ADD PRIMARY KEY (`id`)";
        $db->query($pr);

        $pr = "ALTER TABLE `menus` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
        $db->query($pr);

        $options = "CREATE TABLE IF NOT EXISTS `options` (
                    `id` int(11) NOT NULL,
                      `name` text CHARACTER SET utf8 NOT NULL,
                      `value` text CHARACTER SET utf8 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        $db->query($options);

        $pr = "ALTER TABLE `options` ADD PRIMARY KEY (`id`)";
        $db->query($pr);

        $pr = "ALTER TABLE `options` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
        $db->query($pr);

        $posts = "CREATE TABLE IF NOT EXISTS `posts` (
                `id` bigint(32) NOT NULL,
                  `date` datetime NOT NULL,
                  `title` text NOT NULL,
                  `slug` text NOT NULL,
                  `content` mediumtext NOT NULL,
                  `author` text NOT NULL,
                  `type` text NOT NULL,
                  `cat` varchar(11) NOT NULL,
                  `modified` datetime DEFAULT NULL,
                  `status` enum('0','1','2') NOT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        $db->query($posts);

        $pr = "ALTER TABLE `posts` ADD PRIMARY KEY (`id`)";
        $db->query($pr);

        $pr = "ALTER TABLE `posts` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT";
        $db->query($pr);

        $post_param = "CREATE TABLE IF NOT EXISTS `posts_param` (
                `id` bigint(32) NOT NULL,
                  `post_id` bigint(32) NOT NULL,
                  `param` text NOT NULL,
                  `value` text NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $db->query($post_param);

        $pr = "ALTER TABLE `posts_param` ADD PRIMARY KEY (`id`)";
        $db->query($pr);

        $pr = "ALTER TABLE `posts_param` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT";
        $db->query($pr);

        $user = "CREATE TABLE IF NOT EXISTS `user` (
                `id` bigint(32) NOT NULL,
                  `userid` varchar(16) NOT NULL,
                  `pass` varchar(255) NOT NULL,
                  `confirm` varchar(255) DEFAULT NULL,
                  `group` enum('0','1','2','3','4','5') NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `join_date` datetime NOT NULL,
                  `status` enum('0','1') NOT NULL,
                  `activation` text
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        $db->query($user);

        $pr = "ALTER TABLE `user` ADD PRIMARY KEY (`id`)";
        $db->query($pr);

        $pr = "ALTER TABLE `user` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT";
        $db->query($pr);

        $user_detail = "CREATE TABLE IF NOT EXISTS `user_detail` (
                `id` bigint(20) NOT NULL,
                  `userid` varchar(32) COLLATE latin1_general_ci NOT NULL,
                  `fname` varchar(32) COLLATE latin1_general_ci NULL,
                  `lname` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
                  `sex` varchar(2) COLLATE latin1_general_ci DEFAULT NULL,
                  `birthplace` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
                  `birthdate` date DEFAULT NULL,
                  `addr` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
                  `city` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
                  `state` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
                  `country` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
                  `postcode` varchar(32) COLLATE latin1_general_ci DEFAULT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        $db->query($user_detail);

        $pr = "ALTER TABLE `user_detail` ADD PRIMARY KEY (`id`)";
        $db->query($pr);

        $pr = "ALTER TABLE `user_detail` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT";
        $db->query($pr);
    }

    /**
    * Database Insertion Function.
    * This will insert value on the SQL Table during the installation.
    * 
    * @author Puguh Wijayanto (www.metalgenix.com)
    * @since 0.0.1
    */
    public static function insertData () {
        require_once(GX_PATH.'/inc/config/config.php');
        $db = new Db();
        $url = Session::val('siteurl');
        $domain = Session::val('sitedomain');
        $sitename = Session::val('sitename');
        $slogan = Session::val('siteslogan');

        $options = "INSERT INTO `options` (`id`, `name`, `value`) VALUES
            (null, 'sitename', '{$sitename}'),
            (null, 'siteurl', '{$url}'),
            (null, 'sitedomain', '{$domain}'),
            (null, 'siteslogan', '{$slogan}'),
            (null, 'sitedesc', 'Descriptions'),
            (null, 'sitekeywords', 'keywords'),
            (null, 'siteicon', 'favicon.ico'),
            (null, 'siteaddress', ''),
            (null, 'siteemail', ''),
            (null, 'fbacc', ''),
            (null, 'fbpage', ''),
            (null, 'twitter', ''),
            (null, 'linkedin', ''),
            (null, 'gplus', ''),
            (null, 'logo', '/assets/images/genixcms-logo-small.png'),
            (null, 'logourl', ''),
            (null, 'is_logourl', 'off'),
            (null, 'currency', 'USD'),
            (null, 'country_id', 'ID'),
            (null, 'mailtype', '0'),
            (null, 'smtphost', ''),
            (null, 'smtpuser', ''),
            (null, 'smtppass', ''),
            (null, 'smtpport', '25'),
            (null, 'timezone', 'Asia/Jakarta'),
            (null, 'paypalemail', ''),
            (null, 'robots', 'index, follow'),
            (null, 'use_jquery', 'on'),
            (null, 'use_bootstrap', 'on'),
            (null, 'use_fontawesome', 'on'),
            (null, 'use_bsvalidator', 'on'),
            (null, 'jquery_v', '1.11.1'),
            (null, 'bs_v', ''),
            (null, 'fontawesome_v', ''),
            (null, 'use_editor', 'on'),
            (null, 'editor_type', 'summernote'),
            (null, 'editor_v', ''),
            (null, 'menus', '{\"mainmenu\":{\"name\":\"Main Menu\",\"class\":\"\",\"menu\":[]},\"footer\":{\"name\":\"Footer Menu\",\"class\":\"\",\"menu\":[{\"parent\":\"\",\"menuid\":\"footer\",\"type\":\"custom\",\"value\":\"{$url}\"},{\"parent\":\"\",\"menuid\":\"footer\",\"type\":\"cat\",\"value\":\"1\"}]}}'),
            (null, 'post_perpage', '3'),
            (null, 'pagination', 'pager'),
            (null, 'pinger', 'rpc.pingomatic.com\r\nblogsearch.google.com/ping/RPC2\r\nhttp://feedburner.google.com/fb/a/pingSubmit?bloglink=http%3A%2F%2F{{domain}}'),
            (null, 'bsvalidator_v', ''),
            (null, 'ppsandbox', 'off'),
            (null, 'ppuser', ''),
            (null, 'pppass', ''),
            (null, 'ppsign', ''),
            (null, 'tokens', ''),
            (null, 'modules', ''),
            (null, 'themes', 'default')";
        $db->query($options);
    }
}