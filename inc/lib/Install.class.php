<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 0.0.1 build date 20150126
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

/**
 * Installation Class.
 *
 * This class will process the Installation Process.
 *
 * @since 0.0.1
 */
class Install
{
    public function __construct()
    {
    }

    /**
     * Config File Creation Function.
     *
     * This will create config file at inc/config/config.php
     * during the installation process. Data is gathered
     * from the session.
     *
     * @since 0.0.1
     */
    public static function makeConfig($file)
    {
        // Generate SECURITY_KEY once so we can use it both in the
        // written file and define it in the current request scope.
        $securityKey = Typo::getToken(200);
        $siteId = Typo::getToken(20);

        $config = "<?php if (defined('GX_LIB') === false) die(\"Direct Access Not Allowed!\");
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @package GeniXCMS
 * @since 0.0.1 build date 20140925
 * @version 2.4.0
 * 
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
*/
error_reporting(E_ALL);

// DB CONFIG
!defined('DB_HOST')        ? define('DB_HOST',        '" . Session::val('dbhost') . "') : null;
!defined('DB_NAME')        ? define('DB_NAME',        '" . Session::val('dbname') . "') : null;
!defined('DB_PASS')        ? define('DB_PASS',        '" . Session::val('dbpass') . "') : null;
!defined('DB_USER')        ? define('DB_USER',        '" . Session::val('dbuser') . "') : null;
!defined('DB_DRIVER')      ? define('DB_DRIVER',      '" . Session::val('dbdriver') . "') : null;

!defined('SMART_URL')      ? define('SMART_URL',      false) : null;
!defined('GX_URL_PREFIX')  ? define('GX_URL_PREFIX',  '.html') : null;

!defined('ADMIN_DIR')      ? define('ADMIN_DIR',      'gxadmin') : null;
!defined('ADMIN_THEME')    ? define('ADMIN_THEME',    'default') : null;
!defined('USE_MEMCACHED')  ? define('USE_MEMCACHED',  false) : null;
!defined('SITE_ID')        ? define('SITE_ID',        '{$siteId}') : null;

!defined('DEBUG')          ? define('DEBUG',          false) : null;

!defined('OFFLINE_MODE')   ? define('OFFLINE_MODE',   false) : null; // true = local assets, false = CDN
!defined('DEVELOPER_MODE') ? define('DEVELOPER_MODE', false) : null; // true = show Assets & Hooks inspector

!defined('SESSION_EXPIRES') ? define('SESSION_EXPIRES', 720) : null;
!defined('SESSION_DB')      ? define('SESSION_DB',      false) : null;

##################################// 
# DON't REMOVE or EDIT THIS. 
# ==================================
# YOU WON'T BE ABLE TO LOG IN 
# IF IT CHANGED. PLEASE BE AWARE
##################################//
!defined('SECURITY_KEY') ? define('SECURITY_KEY', '{$securityKey}') : null;

        ";
        try {
            $f = fopen($file, 'w');
            fwrite($f, $config);
            fclose($f);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Return both the config string and the generated security key
        // so the installer can define it in the current request scope.
        return [
            'config' => $config,
            'security_key' => $securityKey,
        ];
    }

    /**
     * Create Initial SQL Table Function.
     *
     * This will create the SQL table on the
     * installation proccess.
     *
     * @since 0.0.1
     */
    public static function createTable()
    {
        // Reconnect explicitly using session credentials.
        // We cannot use require_once here because config.php may already be
        // loaded (or the constants already defined by aaaconfig.php), so
        // Db::$pdo would still be null.
        Db::$pdo = null; // force a fresh connection
        Db::connect(
            Session::val('dbhost'),
            Session::val('dbuser'),
            Session::val('dbpass'),
            Session::val('dbname'),
            Session::val('dbdriver')
        );
        $db = new Db();
        $driver = Session::val('dbdriver') ?: (defined('DB_DRIVER') ? DB_DRIVER : 'mysql');

        $tables = [];

        if ($driver == 'pgsql') {
            $tables[] = "CREATE TABLE IF NOT EXISTS cat (id SERIAL PRIMARY KEY, name TEXT NOT NULL, slug TEXT NOT NULL, parent TEXT, image TEXT, \"desc\" TEXT, type TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS cat_param (id SERIAL PRIMARY KEY, cat_id INTEGER NOT NULL, param TEXT NOT NULL, value TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS menus (id SERIAL PRIMARY KEY, name VARCHAR(64) NOT NULL, menuid VARCHAR(32) NOT NULL, parent VARCHAR(11), sub CHAR(1) CHECK (sub IN ('0','1')) NOT NULL, type VARCHAR(8) NOT NULL, value TEXT NOT NULL, class VARCHAR(64), \"order\" VARCHAR(4))";
            $tables[] = "CREATE TABLE IF NOT EXISTS options (id SERIAL PRIMARY KEY, name TEXT NOT NULL, value TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS posts (id BIGSERIAL PRIMARY KEY, date TIMESTAMP NOT NULL, title TEXT NOT NULL, slug TEXT NOT NULL, content TEXT NOT NULL, author TEXT NOT NULL, type TEXT NOT NULL, cat VARCHAR(11), modified TIMESTAMP, status CHAR(1) CHECK (status IN ('0','1','2')) NOT NULL, views INTEGER DEFAULT 0)";
            $tables[] = "CREATE TABLE IF NOT EXISTS posts_param (id BIGSERIAL PRIMARY KEY, post_id BIGINT NOT NULL, param TEXT NOT NULL, value TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS \"user\" (id BIGSERIAL PRIMARY KEY, userid VARCHAR(32) NOT NULL, pass VARCHAR(255) NOT NULL, confirm VARCHAR(255), \"group\" VARCHAR(1) CHECK (\"group\" IN ('0','1','2','3','4','5','6')) NOT NULL, email VARCHAR(255) NOT NULL, join_date TIMESTAMP NOT NULL, status CHAR(1) CHECK (status IN ('0','1')) NOT NULL, activation TEXT, ipaddress TEXT)";
            $tables[] = "CREATE TABLE IF NOT EXISTS user_detail (id BIGSERIAL PRIMARY KEY, userid VARCHAR(32) NOT NULL, fname VARCHAR(32), lname VARCHAR(255), sex VARCHAR(2), birthplace VARCHAR(32), birthdate DATE, addr VARCHAR(255), city VARCHAR(255), state VARCHAR(255), country VARCHAR(255), postcode VARCHAR(32), avatar TEXT, balance FLOAT DEFAULT 0)";
            $tables[] = "CREATE TABLE IF NOT EXISTS comments (id BIGSERIAL PRIMARY KEY, date TIMESTAMP NOT NULL, userid TEXT NOT NULL, name TEXT NOT NULL, email TEXT NOT NULL, url TEXT NOT NULL, comment TEXT NOT NULL, post_id INTEGER NOT NULL, parent INTEGER NOT NULL, status CHAR(1) CHECK (status IN ('0','1','2')) NOT NULL, type TEXT NOT NULL, ipaddress TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS widgets (id SERIAL PRIMARY KEY, name TEXT NOT NULL, title TEXT NOT NULL, content TEXT NOT NULL, type TEXT NOT NULL, location TEXT NOT NULL, sorting INTEGER NOT NULL, status CHAR(1) CHECK (status IN ('0','1')) NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS widgets_param (id SERIAL PRIMARY KEY, widget_id INTEGER NOT NULL, param TEXT NOT NULL, value TEXT NOT NULL DEFAULT '')";
            $tables[] = "CREATE TABLE IF NOT EXISTS permissions (id SERIAL PRIMARY KEY, group_id INTEGER NOT NULL, permission VARCHAR(100) NOT NULL, status SMALLINT NOT NULL DEFAULT 0)";
        } elseif ($driver == 'sqlite') {
            $tables[] = "CREATE TABLE IF NOT EXISTS cat (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, slug TEXT NOT NULL, parent TEXT, image TEXT, [desc] TEXT, type TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS cat_param (id INTEGER PRIMARY KEY AUTOINCREMENT, cat_id INTEGER NOT NULL, param TEXT NOT NULL, value TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS menus (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(64) NOT NULL, menuid VARCHAR(32) NOT NULL, parent VARCHAR(11), sub TEXT CHECK (sub IN ('0','1')) NOT NULL, type VARCHAR(8) NOT NULL, value TEXT NOT NULL, class VARCHAR(64), [order] VARCHAR(4))";
            $tables[] = "CREATE TABLE IF NOT EXISTS options (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, value TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS posts (id INTEGER PRIMARY KEY AUTOINCREMENT, date TEXT NOT NULL, title TEXT NOT NULL, slug TEXT NOT NULL, content TEXT NOT NULL, author TEXT NOT NULL, type TEXT NOT NULL, cat VARCHAR(11), modified TEXT, status TEXT CHECK (status IN ('0','1','2')) NOT NULL, views INTEGER DEFAULT 0)";
            $tables[] = "CREATE TABLE IF NOT EXISTS posts_param (id INTEGER PRIMARY KEY AUTOINCREMENT, post_id INTEGER NOT NULL, param TEXT NOT NULL, value TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS user (id INTEGER PRIMARY KEY AUTOINCREMENT, userid VARCHAR(32) NOT NULL, pass VARCHAR(255) NOT NULL, confirm VARCHAR(255), [group] TEXT CHECK ([group] IN ('0','1','2','3','4','5','6')) NOT NULL, email VARCHAR(255) NOT NULL, join_date TEXT NOT NULL, status TEXT CHECK (status IN ('0','1')) NOT NULL, activation TEXT, ipaddress TEXT)";
            $tables[] = "CREATE TABLE IF NOT EXISTS user_detail (id INTEGER PRIMARY KEY AUTOINCREMENT, userid VARCHAR(32) NOT NULL, fname VARCHAR(32), lname VARCHAR(255), sex VARCHAR(2), birthplace VARCHAR(32), birthdate TEXT, addr VARCHAR(255), city VARCHAR(255), state VARCHAR(255), country VARCHAR(255), postcode VARCHAR(32), avatar TEXT, balance FLOAT DEFAULT 0)";
            $tables[] = "CREATE TABLE IF NOT EXISTS comments (id INTEGER PRIMARY KEY AUTOINCREMENT, date TEXT NOT NULL, userid TEXT NOT NULL, name TEXT NOT NULL, email TEXT NOT NULL, url TEXT NOT NULL, comment TEXT NOT NULL, post_id INTEGER NOT NULL, parent INTEGER NOT NULL, status TEXT CHECK (status IN ('0','1','2')) NOT NULL, type TEXT NOT NULL, ipaddress TEXT NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS widgets (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, title TEXT NOT NULL, content TEXT NOT NULL, type TEXT NOT NULL, location TEXT NOT NULL, sorting INTEGER NOT NULL, status TEXT CHECK (status IN ('0','1')) NOT NULL)";
            $tables[] = "CREATE TABLE IF NOT EXISTS widgets_param (id INTEGER PRIMARY KEY AUTOINCREMENT, widget_id INTEGER NOT NULL, param TEXT NOT NULL, value TEXT NOT NULL DEFAULT '')";
            $tables[] = "CREATE TABLE IF NOT EXISTS permissions (id INTEGER PRIMARY KEY AUTOINCREMENT, group_id INTEGER NOT NULL, permission TEXT NOT NULL, status INTEGER NOT NULL DEFAULT 0)";
        } else {
            // Default to MySQL
            $tables[] = "CREATE TABLE IF NOT EXISTS `cat` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` text NOT NULL, `slug` text NOT NULL, `parent` text DEFAULT NULL, `image` text DEFAULT NULL, `desc` text DEFAULT NULL, `type` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `cat_param` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `cat_id` int(11) NOT NULL, `param` text NOT NULL, `value` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `menus` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` varchar(64) NOT NULL, `menuid` varchar(32) NOT NULL, `parent` varchar(11) DEFAULT NULL, `sub` enum('0','1') NOT NULL, `type` varchar(8) NOT NULL, `value` text NOT NULL, `class` varchar(64) DEFAULT NULL, `order` varchar(4) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `options` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` text NOT NULL, `value` longtext NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `posts` (`id` bigint(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, `date` datetime NOT NULL, `title` text NOT NULL, `slug` text NOT NULL, `content` longtext NOT NULL, `author` text NOT NULL, `type` text NOT NULL, `cat` varchar(11) DEFAULT NULL, `modified` datetime DEFAULT NULL, `status` enum('0','1','2') NOT NULL, `views` int(11) NOT NULL DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `posts_param` (`id` bigint(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, `post_id` bigint(32) NOT NULL, `param` text NOT NULL, `value` longtext NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `user` (`id` bigint(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, `userid` varchar(32) NOT NULL, `pass` varchar(255) NOT NULL, `confirm` varchar(255) DEFAULT NULL, `group` enum('0','1','2','3','4','5','6') NOT NULL, `email` varchar(255) NOT NULL, `join_date` datetime NOT NULL, `status` enum('0','1') NOT NULL, `activation` text, `ipaddress` text) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `user_detail` (`id` bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT, `userid` varchar(32) NOT NULL, `fname` varchar(32) DEFAULT NULL, `lname` varchar(255) DEFAULT NULL, `sex` varchar(2) DEFAULT NULL, `birthplace` varchar(32) DEFAULT NULL, `birthdate` date DEFAULT NULL, `addr` varchar(255) DEFAULT NULL, `city` varchar(255) DEFAULT NULL, `state` varchar(255) DEFAULT NULL, `country` varchar(255) DEFAULT NULL, `postcode` varchar(32) DEFAULT NULL, `avatar` text, `balance` float DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `comments` (`id` bigint(22) NOT NULL PRIMARY KEY AUTO_INCREMENT, `date` datetime NOT NULL, `userid` text NOT NULL, `name` text NOT NULL, `email` text NOT NULL, `url` text NOT NULL, `comment` longtext NOT NULL, `post_id` int(11) NOT NULL, `parent` int(11) NOT NULL, `status` enum('0','1','2') NOT NULL, `type` text NOT NULL, `ipaddress` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `widgets` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `name` text NOT NULL, `title` text NOT NULL, `content` longtext NOT NULL, `type` text NOT NULL, `location` text NOT NULL, `sorting` int(11) NOT NULL, `status` enum('0','1') NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $tables[] = "CREATE TABLE IF NOT EXISTS `widgets_param` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `widget_id` int(11) NOT NULL, `param` varchar(191) NOT NULL, `value` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $tables[] = "CREATE TABLE IF NOT EXISTS `permissions` (`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, `group_id` int(11) NOT NULL, `permission` varchar(100) NOT NULL, `status` tinyint(1) NOT NULL DEFAULT '0') ENGINE=InnoDB DEFAULT CHARSET=utf8";
        }

        foreach ($tables as $sql) {
            $db->query($sql);
        }
    }

    /**
     * Database Insertion Function.
     *
     * This will insert value on the SQL Table during the installation.
     *
     * @since 0.0.1
     */
    public static function insertData()
    {
        // Same reconnect approach as createTable().
        Db::$pdo = null;
        Db::connect(
            Session::val('dbhost'),
            Session::val('dbuser'),
            Session::val('dbpass'),
            Session::val('dbname'),
            Session::val('dbdriver')
        );
        $db = new Db();
        $url = Session::val('siteurl');
        $domain = Session::val('sitedomain');
        $sitename = Session::val('sitename');
        $slogan = Session::val('siteslogan');
        $logo = 'assets/images/genixcms-logo-sign-small.png';
        $admin = Session::val('adminuser');

        $options = "INSERT INTO `options` (`id`, `name`, `value`) VALUES
            (null, 'sitename', '{$sitename}'),
            (null, 'siteurl', '{$url}'),
            (null, 'sitedomain', '{$domain}'),
            (null, 'siteslogan', '{$slogan}'),
            (null, 'sitedesc', ''),
            (null, 'sitekeywords', ''),
            (null, 'siteicon', '{$url}favicon.ico'),
            (null, 'siteaddress', ''),
            (null, 'siteemail', ''),
            (null, 'fbacc', ''),
            (null, 'fbpage', ''),
            (null, 'twitter', ''),
            (null, 'linkedin', ''),
            (null, 'gplus', ''),
            (null, 'logo', '{$logo}'),
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
            (null, 'jquery_v', '3.7.1'),
            (null, 'bs_v', ''),
            (null, 'fontawesome_v', ''),
            (null, 'use_editor', 'on'),
            (null, 'editor_type', 'summernote'),
            (null, 'editor_v', ''),
            (null, 'menus', '{&quot;mainmenu&quot;:{&quot;name&quot;:&quot;Main Menu&quot;,&quot;class&quot;:&quot;&quot;,&quot;menu&quot;:[]},&quot;footer&quot;:{&quot;name&quot;:&quot;Footer Menu&quot;,&quot;class&quot;:&quot;&quot;,&quot;menu&quot;:[]}}'),
            (null, 'post_perpage', '3'),
            (null, 'pagination', 'pager'),
            (null, 'pinger', 'rpc.pingomatic.com\r\nblogsearch.google.com/ping/RPC2\r\nfeedburner.google.com/fb/a/pingSubmit?bloglink=http%3A%2F%2F{{domain}}'),
            (null, 'bsvalidator_v', ''),
            (null, 'ppsandbox', 'off'),
            (null, 'ppuser', ''),
            (null, 'pppass', ''),
            (null, 'ppsign', ''),
            (null, 'tokens', ''),
            (null, 'modules', '[&quot;gxeditor&quot;]'),
            (null, 'themes', 'default'),
            (null, 'system_lang', 'en_US'),
            (null, 'charset', 'utf-8'),
            (null, 'google_captcha_sitekey', ''),
            (null, 'google_captcha_secret', ''),
            (null, 'google_captcha_lang', 'en'),
            (null, 'google_captcha_enable', 'off'),
            (null, 'multilang_enable', 'off'),
            (null, 'multilang_default', ''),
            (null, 'multilang_country', ''),
            (null, 'system_check', '{}'),
            (null, 'permalink_use_index_php', 'off'),
            (null, 'pinger_enable', 'on'),
            (null, 'cdn_url', '{$url}'),
            (null, 'spamwords', ''),
            (null, 'comments_perpage', '5'),
            (null, 'comments_enable', 'on'),
            (null, 'db_version', '1.1.4'),
            (null, 'cache_enabled', 'off'),
            (null, 'cache_path', '/assets/cache/pages/'),
            (null, 'cache_timeout', '300')
            ";
        $db->query($options);

        $cat = "INSERT INTO `cat` (`id`, `name`, `slug`, `parent`, `desc`, `type`) VALUES
        (null, 'News', 'news', '0', 'Latest announcements and news.', 'post'),
        (null, 'Tutorials', 'tutorials', '0', 'Guides and how-to articles.', 'post');";
        $db->query($cat);

        $menu = "INSERT INTO `menus` (`id`, `name`, `menuid`, `parent`, `sub`, `type`, `value`, `class`, `order`) VALUES
        (null, 'Home', 'mainmenu', '0', '0', 'custom', '{$url}', '', NULL),
        (null, 'About Us', 'mainmenu', '0', '0', 'page', '4', '', NULL),
        (null, 'Privacy Policy', 'footer', '0', '0', 'page', '5', '', NULL),
        (null, 'Contact Us', 'footer', '0', '0', 'mod', 'contactPage', '', NULL);";
        $db->query($menu);

        $date = date('Y-m-d H:i:s');

        $post = "INSERT INTO `posts` (`id`, `date`, `title`, `slug`, `content`, `author`, `type`, `cat`, `modified`, `status`, `views`) VALUES
        (null, '{$date}', 'Welcome to GeniXCMS 2.3.0', 'welcome-to-genixcms', '&lt;p&gt;Congratulations and welcome to the future of modular web management! &lt;strong&gt;GeniXCMS 2.3.0&lt;/strong&gt; is our most powerful release yet, featuring a refined e-commerce ecosystem and advanced financial intelligence.&lt;/p&gt;&lt;p&gt;New highlights in this version:&lt;/p&gt;&lt;ul&gt;&lt;li&gt;Nixomers Ecosystem: Ultra-Premium checkout experiences with Glassmorphism.&lt;/li&gt;&lt;li&gt;Financial Recalculate Engine: Deep data synchronization between orders and transactions.&lt;/li&gt;&lt;li&gt;Optimized Granular Tracking: Selective unit-level monitoring for performance.&lt;/li&gt;&lt;li&gt;Next-Gen UiBuilder: Programmatic logic loops and schema-driven dashboards.&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;We hope you enjoy building with GeniXCMS. Explore the dashboard and start creating!&lt;/p&gt;', '{$admin}', 'post', '1', '{$date}', '1', 42),
        (null, '{$date}', 'Professional Publishing: Math &amp; Tables', 'professional-publishing-math-tables', '&lt;p&gt;GeniXCMS 2.1.1 introduces professional publishing tools: &lt;strong&gt;Math Equations&lt;/strong&gt; and the &lt;strong&gt;Table Wizard&lt;/strong&gt;.&lt;/p&gt;&lt;p&gt;Write complex LaTeX formulas with real-time previews using the Sigma-Root icon, or build structured tables via our new Bootstrap-based modal interface. These tools make GeniXCMS perfect for technical and academic content.&lt;/p&gt;', '{$admin}', 'post', '2', '{$date}', '1', 25),
        (null, '{$date}', 'Dynamic Layouts and Deep Dark Mode', 'dynamic-layouts-dark-mode', '&lt;p&gt;Themes now support &lt;strong&gt;Dynamic Page Layouts&lt;/strong&gt;—simply create a &lt;code&gt;layout-*.latte&lt;/code&gt; file and select it in the admin panel. Combined with our expanded &lt;strong&gt;Dark Mode&lt;/strong&gt; support, your site will look professional in any light.&lt;/p&gt;', '{$admin}', 'post', '2', '{$date}', '1', 18),
        (null, '{$date}', 'About Us', 'about-us', '&lt;p&gt;Hello and welcome to our website! We are a passionate team dedicated to delivering high-quality content and services to our incredible community.&lt;/p&gt;&lt;p&gt;This page is fully customizable. You can edit this text from the &lt;strong&gt;Pages&lt;/strong&gt; menu in your administration panel.&lt;/p&gt;', '{$admin}', 'page', NULL, NULL, '1', 12),
        (null, '{$date}', 'Privacy Policy', 'privacy-policy', '&lt;h2&gt;Cookies&lt;/h2&gt;&lt;p&gt;We gather information about visitors who visit our site. For anonymous visitors, we track non-identifying data for analytics to improve user experience. You can manage or delete cookies directly from your browser settings.&lt;/p&gt;&lt;h3&gt;Personal Information&lt;/h3&gt;&lt;p&gt;If you register on our site, we securely store information such as your Name and Email address. We do not sell or share this information to untrusted third parties. It is strictly used for platform communication.&lt;/p&gt;', '{$admin}', 'page', NULL, NULL, '1', 4);
        ";
        $db->query($post);

        $comment = "INSERT INTO `comments` (`id`, `date`, `userid`, `name`, `email`, `url`, `comment`, `post_id`, `parent`, `status`, `type`, `ipaddress`) VALUES
        (null, '{$date}', '{$admin}', '{$admin}', '{$admin}@{$domain}' , '', 'This is sample of comment<br />', 6, 0, '1', 'post', '::1');
        ";

        $db->query($comment);
    }
}
