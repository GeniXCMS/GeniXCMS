<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150126
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

/**
 * Installation Class.
 *
 * This class will process the Installation Process.
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
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
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function makeConfig($file)
    {
        $config = "<?php if (defined('GX_LIB') === false) die(\"Direct Access Not Allowed!\");
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @package GeniXCMS
 * @since 0.0.1 build date 20140925
 * @version 1.1.11
 * @link https://github.com/semplon/GeniXCMS
 * 
 * @author Puguh Wijayanto (www.metalgenix.com)
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 *
*/error_reporting(0);

// DB CONFIG
define('DB_HOST', '".Session::val('dbhost')."');
define('DB_NAME', '".Session::val('dbname')."');
define('DB_PASS', '".Session::val('dbpass')."');
define('DB_USER', '".Session::val('dbuser')."');
define('DB_DRIVER', 'mysqli');

define('SMART_URL', false); //set 'true' if you want use SMART URL (SEO Friendly URL)
define('GX_URL_PREFIX', '.html');

define('ADMIN_DIR', 'gxadmin');
define('USE_MEMCACHED', false);
define('SITE_ID', '".Typo::getToken(20)."');











##################################// 
# DON't REMOVE or EDIT THIS. 
# ==================================
# YOU WON'T BE ABLE TO LOG IN 
# IF IT CHANGED. PLEASE BE AWARE
##################################//
define('SECURITY_KEY', '".Typo::getToken(200)."'); // for security purpose, will be used for creating password

        ";
        try {
            $f = fopen($file, 'w');
            $c = fwrite($f, $config);
            fclose($f);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $config;
    }

    /**
     * Create Initial SQL Table Function.
     *
     * This will create the SQL table on the
     * installation proccess.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function createTable()
    {
        require_once GX_PATH.'/inc/config/config.php';
        $db = new Db();
        $cat = 'CREATE TABLE IF NOT EXISTS `cat` (
                `id` int(11) NOT NULL,
                  `name` text NOT NULL,
                  `slug` text NOT NULL,
                  `parent` text DEFAULT NULL,
                  `desc` text DEFAULT  NULL,
                  `type` text NOT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ';
        $db->query($cat);

        $pr = 'ALTER TABLE `cat` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `cat` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        $cat_param = 'CREATE TABLE IF NOT EXISTS `cat_param` (
                    `id` int(11) NOT NULL,
                      `cat_id` int(11) NOT NULL,
                      `param` text CHARACTER SET utf8 NOT NULL,
                      `value` text CHARACTER SET utf8 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8';
        $db->query($cat_param);

        $pr = 'ALTER TABLE `cat_param` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `cat_param` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
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

        $pr = 'ALTER TABLE `menus` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `menus` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        $options = 'CREATE TABLE IF NOT EXISTS `options` (
                    `id` int(11) NOT NULL,
                      `name` text CHARACTER SET utf8 NOT NULL,
                      `value` longtext CHARACTER SET utf8 NOT NULL
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8';
        $db->query($options);

        $pr = 'ALTER TABLE `options` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `options` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        $posts = "CREATE TABLE IF NOT EXISTS `posts` (
                `id` bigint(32) NOT NULL,
                  `date` datetime NOT NULL,
                  `title` text NOT NULL,
                  `slug` text NOT NULL,
                  `content` longtext NOT NULL,
                  `author` text NOT NULL,
                  `type` text NOT NULL,
                  `cat` varchar(11) DEFAULT NULL,
                  `modified` datetime DEFAULT NULL,
                  `status` enum('0','1','2') NOT NULL,
                  `views` int(11) NOT NULL DEFAULT '0'
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        $db->query($posts);

        $pr = 'ALTER TABLE `posts` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `posts` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        $post_param = 'CREATE TABLE IF NOT EXISTS `posts_param` (
                `id` bigint(32) NOT NULL,
                  `post_id` BIGINT(32) NOT NULL,
                  `param` TEXT NOT NULL,
                  `value` LONGTEXT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $db->query($post_param);

        $pr = 'ALTER TABLE `posts_param` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `posts_param` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        $user = "CREATE TABLE IF NOT EXISTS `user` (
                `id` bigint(32) NOT NULL,
                  `userid` varchar(32) NOT NULL,
                  `pass` varchar(255) NOT NULL,
                  `confirm` varchar(255) DEFAULT NULL,
                  `group` enum('0','1','2','3','4','5','6') NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `join_date` datetime NOT NULL,
                  `status` enum('0','1') NOT NULL,
                  `activation` text,
                  `ipaddress` text
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        $db->query($user);

        $pr = 'ALTER TABLE `user` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `user` MODIFY `id` bigint(32) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        $user_detail = 'CREATE TABLE IF NOT EXISTS `user_detail` (
                  `id` bigint(20) NOT NULL,
                  `userid` varchar(32)  NOT NULL,
                  `fname` varchar(32)  DEFAULT NULL,
                  `lname` varchar(255)  DEFAULT NULL,
                  `sex` varchar(2)  DEFAULT NULL,
                  `birthplace` varchar(32)  DEFAULT NULL,
                  `birthdate` date DEFAULT NULL,
                  `addr` varchar(255)  DEFAULT NULL,
                  `city` varchar(255)  DEFAULT NULL,
                  `state` varchar(255)  DEFAULT NULL,
                  `country` varchar(255)  DEFAULT NULL,
                  `postcode` varchar(32)  DEFAULT NULL,
                  `avatar` text,
                  `balance` float DEFAULT 0
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8';
        $db->query($user_detail);

        $pr = 'ALTER TABLE `user_detail` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `user_detail` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        $comments = "CREATE TABLE IF NOT EXISTS `comments` (
                  `id` bigint(22) NOT NULL,
                  `date` datetime NOT NULL,
                  `userid` text NOT NULL,
                  `name` text NOT NULL,
                  `email` text NOT NULL,
                  `url` text NOT NULL,
                  `comment` longtext NOT NULL,
                  `post_id` int(11) NOT NULL,
                  `parent` int(11) NOT NULL,
                  `status` enum('0','1','2') NOT NULL,
                  `type` text NOT NULL,
                  `ipaddress` text NOT NULL
                ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8";
        $db->query($comments);

        $pr = 'ALTER TABLE `comments` ADD PRIMARY KEY (`id`)';
        $db->query($pr);

        $pr = 'ALTER TABLE `comments` MODIFY `id` bigint(22) NOT NULL AUTO_INCREMENT';
        $db->query($pr);

        

    }

    /**
     * Database Insertion Function.
     *
     * This will insert value on the SQL Table during the installation.
     *
     * @author Puguh Wijayanto <metalgenix@gmail.com>
     *
     * @since 0.0.1
     */
    public static function insertData()
    {
        require_once GX_PATH.'/inc/config/config.php';
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
            (null, 'jquery_v', '1.12.0'),
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
            (null, 'modules', '[]'),
            (null, 'themes', 'gneex'),
            (null, 'system_lang', 'english'),
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
            (null, 'cache_timeout', '300'),
            (null, 'gneex_options', '{&quot;intro_title&quot;:&quot;Welcome to GenixCMS 1.1.11&quot;,&quot;intro_text&quot;:&quot;Create magnificent Website with GenixCMS&quot;,&quot;intro_image&quot;:&quot;https:\\/\\/www.youtube.com\\/watch?v=M_oeub8YhXA&quot;,&quot;featured_posts&quot;:&quot;1,1,1,1,1,1,1,1&quot;,&quot;background_featured&quot;:&quot;http:\\/\\/localhost\\/genixcms\\/inc\\/themes\\/gneex\\/images\\/pattern-13.jpg&quot;,&quot;background_color_featured&quot;:&quot;#050505&quot;,&quot;front_layout&quot;:&quot;magazine&quot;,&quot;fullwidth_page&quot;:&quot;&quot;,&quot;panel_1&quot;:&quot;1&quot;,&quot;panel_1_color&quot;:&quot;&quot;,&quot;panel_1_font_color&quot;:&quot;&quot;,&quot;panel_2&quot;:&quot;1&quot;,&quot;panel_2_color&quot;:&quot;&quot;,&quot;panel_2_font_color&quot;:&quot;&quot;,&quot;panel_3&quot;:&quot;1&quot;,&quot;panel_3_color&quot;:&quot;&quot;,&quot;panel_3_font_color&quot;:&quot;&quot;,&quot;panel_4&quot;:&quot;1&quot;,&quot;panel_5&quot;:&quot;1&quot;,&quot;panel_5_color&quot;:&quot;&quot;,&quot;panel_5_font_color&quot;:&quot;&quot;,&quot;background_header&quot;:&quot;&quot;,&quot;background_color_header&quot;:&quot;#3d3c3f&quot;,&quot;font_color_header&quot;:&quot;#ffffff&quot;,&quot;background_footer&quot;:&quot;&quot;,&quot;background_color_footer&quot;:&quot;#2465b0&quot;,&quot;font_color_footer&quot;:&quot;#ffffff&quot;,&quot;link_color_footer&quot;:&quot;&quot;,&quot;body_background_color&quot;:&quot;&quot;,&quot;link_color&quot;:&quot;&quot;,&quot;link_color_hover&quot;:&quot;&quot;,&quot;sidebar_background_color_header&quot;:&quot;#d13d7b&quot;,&quot;sidebar_font_color_header&quot;:&quot;#ffffff&quot;,&quot;sidebar_background_color_body&quot;:&quot;#d13d7b&quot;,&quot;sidebar_font_color_body&quot;:&quot;#ffffff&quot;,&quot;sidebar_border_width&quot;:&quot;0&quot;,&quot;sidebar_border_color&quot;:&quot;&quot;,&quot;content_border_width&quot;:&quot;0&quot;,&quot;content_border_color&quot;:&quot;&quot;,&quot;content_background_color_body&quot;:&quot;#f5f5f5&quot;,&quot;content_font_color_body&quot;:&quot;#757575&quot;,&quot;content_title_size&quot;:&quot;33&quot;,&quot;content_title_cat_size&quot;:&quot;30&quot;,&quot;content_title_color&quot;:&quot;#212121&quot;,&quot;content_title_color_hover&quot;:&quot;#2b2b2b&quot;,&quot;list_title_color&quot;:&quot;#1569cc&quot;,&quot;list_title_size&quot;:&quot;23&quot;,&quot;container_width&quot;:&quot;1280&quot;,&quot;category_layout&quot;:&quot;blog&quot;,&quot;adsense&quot;:&quot;&quot;,&quot;analytics&quot;:&quot;&quot;}')
            ";
        $db->query($options);

        $cat = "INSERT INTO `cat` (`id`, `name`, `slug`, `parent`, `desc`, `type`) VALUES
        (null, 'Category', 'category', '0', '', 'post');";
        $db->query($cat);

        $menu = "INSERT INTO `menus` (`id`, `name`, `menuid`, `parent`, `sub`, `type`, `value`, `class`, `order`) VALUES
        (null, 'About Us', 'mainmenu', '0', '0', 'page', '2', '', NULL),
        (null, 'Sub Menu', 'mainmenu', '1', '0', 'custom', '#', '', NULL),
        (null, 'About Us', 'footer', '0', '0', 'page', '2', '', NULL),
        (null, 'Privacy Policy', 'footer', '0', '0', 'page', '5', '', NULL),
        (null, 'Contact Us', 'footer', '0', '0', 'mod', 'contactPage', '', NULL);";
        $db->query($menu);

        $post = "INSERT INTO `posts` (`id`, `date`, `title`, `slug`, `content`, `author`, `type`, `cat`, `modified`, `status`, `views`) VALUES
        (null, '2019-12-15 02:20:36', 'Lorem Ipsum Dolor sit Amet', 'lorem-ipsum-dolor-sit-amet', '&lt;p&gt;&lt;img src=&quot;".$url."assets/media/images/786523-depth-of-field-backgrounds.jpg&quot; style=&quot;width: 100%;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis et massa fermentum risus ultrices tristique ultricies at ante. Nullam venenatis lorem at justo condimentum, id varius felis consequat. Morbi nibh ante, viverra sed enim sit amet, porttitor tincidunt metus. Fusce elementum elit sed odio congue vulputate. Nullam tempor tincidunt justo, eu elementum velit pellentesque non. Praesent sit amet odio efficitur, ultricies nibh ac, vehicula nisl. Sed at nibh vitae ligula accumsan tristique. Aliquam blandit tellus nec nibh fringilla euismod. Nulla vitae ex sed quam volutpat placerat nec a ex. Phasellus eu viverra magna. Cras non libero egestas, sagittis lorem non, dictum enim. Mauris eleifend vitae ex ut sagittis. Maecenas commodo, libero sed posuere finibus, diam risus porttitor sapien, et suscipit nunc massa et enim. Praesent id dui maximus, pellentesque enim eget, sollicitudin elit.[[--readmore--]]\r\n&lt;/p&gt;&lt;p&gt;\r\nSed a egestas mi, sed hendrerit elit. In pharetra, felis vitae blandit auctor, quam neque imperdiet risus, accumsan imperdiet dui enim non libero. In vel mi pretium, consectetur magna vitae, finibus diam. Fusce egestas tortor aliquet, tempor neque ut, cursus ligula. Sed ac nisi nec purus porttitor molestie. Donec faucibus massa id porta malesuada. Morbi vel purus sed sem tristique pulvinar venenatis in quam. Maecenas eu nisi quis ipsum euismod imperdiet in et sapien. Quisque iaculis eleifend ligula, sit amet posuere velit efficitur non. Fusce pharetra iaculis purus, vitae ultricies enim consectetur nec. Vivamus ac iaculis nisl, at congue ante.&amp;nbsp;&lt;/p&gt;&lt;p&gt;\r\nSed fermentum ipsum lorem, ut pulvinar est ornare vel. Sed rhoncus vel est vel dignissim. Donec sit amet leo felis. Nullam porta ante enim. Donec non porttitor lacus. Aliquam malesuada, lacus non pellentesque vulputate, augue neque vehicula lorem, vitae auctor nisi mauris id elit. Maecenas volutpat enim magna. Curabitur aliquet lobortis augue et sodales. Mauris eget magna lacus. Nullam tristique sapien vitae mi malesuada, quis efficitur tortor egestas.&lt;/p&gt;', '{$admin}', 'post', '1', '2019-12-17 09:20:03', '1', 242),
        (null, '2019-12-15 08:56:04', 'About Us', 'about-us', '&lt;p&gt;About Us&lt;/p&gt;', '{$admin}', 'page', NULL, NULL, '1', 8),
        (null, '2019-12-15 10:33:20', 'Post With Images Left Align', 'post-with-images-left-align', '&lt;p&gt;&lt;img src=&quot;".$url."assets/media/images/786523-depth-of-field-backgrounds.jpg&quot; style=&quot;width: 50%;&quot; class=&quot;pull-left&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis et massa fermentum risus ultrices tristique ultricies at ante. Nullam venenatis lorem at justo condimentum, id varius felis consequat. Morbi nibh ante, viverra sed enim sit amet, porttitor tincidunt metus. Fusce elementum elit sed odio congue vulputate. Nullam tempor tincidunt justo, eu elementum velit pellentesque non. Praesent sit amet odio efficitur, ultricies nibh ac, vehicula nisl. Sed at nibh vitae ligula accumsan tristique. Aliquam blandit tellus nec nibh fringilla euismod. Nulla vitae ex sed quam volutpat placerat nec a ex. Phasellus eu viverra magna. Cras non libero egestas, sagittis lorem non, dictum enim. Mauris eleifend vitae ex ut sagittis. Maecenas commodo, libero sed posuere finibus, diam risus porttitor sapien, et suscipit nunc massa et enim. Praesent id dui maximus, pellentesque enim eget, sollicitudin elit.\r\n&lt;/p&gt;&lt;p&gt;\r\nSed a egestas mi, sed hendrerit elit. In pharetra, felis vitae blandit auctor, quam neque imperdiet risus, accumsan imperdiet dui enim non libero. In vel mi pretium, consectetur magna vitae, finibus diam. Fusce egestas tortor aliquet, tempor neque ut, cursus ligula. Sed ac nisi nec purus porttitor molestie. Donec faucibus massa id porta malesuada. Morbi vel purus sed sem tristique pulvinar venenatis in quam. Maecenas eu nisi quis ipsum euismod imperdiet in et sapien. Quisque iaculis eleifend ligula, sit amet posuere velit efficitur non. Fusce pharetra iaculis purus, vitae ultricies enim consectetur nec. Vivamus ac iaculis nisl, at congue ante.&amp;nbsp;&amp;nbsp;&lt;/p&gt;&lt;p&gt;\r\nSed fermentum ipsum lorem, ut pulvinar est ornare vel. Sed rhoncus vel est vel dignissim. Donec sit amet leo felis. Nullam porta ante enim. Donec non porttitor lacus. Aliquam malesuada, lacus non pellentesque vulputate, augue neque vehicula lorem, vitae auctor nisi mauris id elit. Maecenas volutpat enim magna. Curabitur aliquet lobortis augue et sodales. Mauris eget magna lacus. Nullam tristique sapien vitae mi malesuada, quis efficitur tortor egestas.&lt;/p&gt;', '{$admin}', 'post', '1', NULL, '1', 96),
        (null, '2019-12-17 10:45:50', 'Post With a Very Long Long Title, This Title is for Sample About How Themes Will Handle the Long Title', 'post-with-a-very-long-long-title-this-title-is-for-sample-about-how-themes-will-handle-the-long-title', '&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis et massa fermentum risus ultrices tristique ultricies at ante. Nullam venenatis lorem at justo condimentum, id varius felis consequat. Morbi nibh ante, viverra sed enim sit amet, porttitor tincidunt metus. Fusce elementum elit sed odio congue vulputate. Nullam tempor tincidunt justo, eu elementum velit pellentesque non. Praesent sit amet odio efficitur, ultricies nibh ac, vehicula nisl. Sed at nibh vitae ligula accumsan tristique. Aliquam blandit tellus nec nibh fringilla euismod. Nulla vitae ex sed quam volutpat placerat nec a ex. Phasellus eu viverra magna. Cras non libero egestas, sagittis lorem non, dictum enim. Mauris eleifend vitae ex ut sagittis. Maecenas commodo, libero sed posuere finibus, diam risus porttitor sapien, et suscipit nunc massa et enim. Praesent id dui maximus, pellentesque enim eget, sollicitudin elit.\r\n&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;".$url."assets/media/images/landscape-1.jpg&quot; style=&quot;width: 100%;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;Sed a egestas mi, sed hendrerit elit. In pharetra, felis vitae blandit auctor, quam neque imperdiet risus, accumsan imperdiet dui enim non libero. In vel mi pretium, consectetur magna vitae, finibus diam. Fusce egestas tortor aliquet, tempor neque ut, cursus ligula. Sed ac nisi nec purus porttitor molestie. Donec faucibus massa id porta malesuada. Morbi vel purus sed sem tristique pulvinar venenatis in quam. Maecenas eu nisi quis ipsum euismod imperdiet in et sapien. Quisque iaculis eleifend ligula, sit amet posuere velit efficitur non. Fusce pharetra iaculis purus, vitae ultricies enim consectetur nec. Vivamus ac iaculis nisl, at congue ante. \r\n&lt;/p&gt;&lt;p&gt;Sed fermentum ipsum lorem, ut pulvinar est ornare vel. Sed rhoncus vel est vel dignissim. Donec sit amet leo felis. Nullam porta ante enim. Donec non porttitor lacus. Aliquam malesuada, lacus non pellentesque vulputate, augue neque vehicula lorem, vitae auctor nisi mauris id elit. Maecenas volutpat enim magna. Curabitur aliquet lobortis augue et sodales. Mauris eget magna lacus. Nullam tristique sapien vitae mi malesuada, quis efficitur tortor egestas.&lt;/p&gt;', '{$admin}', 'post', '1', NULL, '1', 14),
        (null, '2019-12-17 11:08:07', 'Privacy Policy', 'privacy-policy', '&lt;h2&gt;Cookies\r\n&lt;/h2&gt;&lt;p&gt;We gather information about visitors whom visit our site and register into our site. For visitor whom not registered to our site, we only track information about location, browser, visiting behavior and some information which needed by the tracker. We are using &lt;b&gt;Google Analytics&lt;/b&gt; and &lt;b&gt;Facebook Pixel&lt;/b&gt; for track our visitors. \r\n&lt;/p&gt;&lt;p&gt;It is used to identify returning users and to identify subscribers and registrants (a registrant – and subscriber – will have the cookie linked to their email address as a way of identifying them). We also use a cookie to track a user’s sessions. We use this information to find out what site features are most popular so that we can develop Going Concern in the light of our analysis of people’s usage. We also use cookies and the information we collect to show\r\n you relevant content and advertising.&amp;nbsp;&lt;br&gt;&lt;/p&gt;&lt;p&gt;&lt;b&gt;&lt;i&gt;\r\nYou can delete cookies from your hard drive at any time.&amp;nbsp;&lt;/i&gt;&lt;/b&gt;&lt;/p&gt;&lt;p&gt;\r\n&lt;/p&gt;&lt;h3&gt;Personal Information\r\n&lt;/h3&gt;&lt;p&gt;We only gather personal information for certain visitor whom register to our site or as a one of our clients. These personal information such as, Name, Address, Email, Phone. Those information including when visitor contacting us via our contact us page.\r\n&lt;/p&gt;&lt;p&gt;We didn&apos;t share those information to another third parties. We kept it save into our database and used as communication purpose.&amp;nbsp;&lt;/p&gt;&lt;p&gt;\r\n&lt;/p&gt;&lt;p&gt;This Privacy Policy can changed without notice, we only inform the privacy policy into our website.&lt;/p&gt;', '{$admin}', 'page', NULL, NULL, '1', 2),
        (null, '2019-12-17 11:10:43', 'Any Image with Multiple Align', 'any-image-with-multiple-align', '&lt;p&gt;&lt;img src=&quot;".$url."assets/media/images/402321.jpg&quot; style=&quot;width: 50%;&quot; class=&quot;pull-left&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis et massa fermentum risus ultrices tristique ultricies at ante. Nullam venenatis lorem at justo condimentum, id varius felis consequat. Morbi nibh ante, viverra sed enim sit amet, porttitor tincidunt metus. Fusce elementum elit sed odio congue vulputate. Nullam tempor tincidunt justo, eu elementum velit pellentesque non. Praesent sit amet odio efficitur, ultricies nibh ac, vehicula nisl. Sed at nibh vitae ligula accumsan tristique. Aliquam blandit tellus nec nibh fringilla euismod. Nulla vitae ex sed quam volutpat placerat nec a ex. Phasellus eu viverra magna. Cras non libero egestas, sagittis lorem non, dictum enim. Mauris eleifend vitae ex ut sagittis. Maecenas commodo, libero sed posuere finibus, diam risus porttitor sapien, et suscipit nunc massa et enim. Praesent id dui maximus, pellentesque enim eget, sollicitudin elit.&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;".$url."assets/media/images/786523-depth-of-field-backgrounds.jpg&quot; style=&quot;width: 50%;&quot; class=&quot;pull-right&quot;&gt;Sed a egestas mi, sed hendrerit elit. In pharetra, felis vitae blandit auctor, quam neque imperdiet risus, accumsan imperdiet dui enim non libero. In vel mi pretium, consectetur magna vitae, finibus diam. Fusce egestas tortor aliquet, tempor neque ut, cursus ligula. Sed ac nisi nec purus porttitor molestie. Donec faucibus massa id porta malesuada. Morbi vel purus sed sem tristique pulvinar venenatis in quam. Maecenas eu nisi quis ipsum euismod imperdiet in et sapien. Quisque iaculis eleifend ligula, sit amet posuere velit efficitur non. Fusce pharetra iaculis purus, vitae ultricies enim consectetur nec. Vivamus ac iaculis nisl, at congue ante.&amp;nbsp;&lt;/p&gt;&lt;p&gt;Sed fermentum ipsum lorem, ut pulvinar est ornare vel. Sed rhoncus vel est vel dignissim. Donec sit amet leo felis. Nullam porta ante enim. Donec non porttitor lacus. Aliquam malesuada, lacus non pellentesque vulputate, augue neque vehicula lorem, vitae auctor nisi mauris id elit. Maecenas volutpat enim magna. Curabitur aliquet lobortis augue et sodales. Mauris eget magna lacus. Nullam tristique sapien vitae mi malesuada, quis efficitur tortor egestas.&lt;/p&gt;', '{$admin}', 'post', '1', NULL, '1', 26);
        ";
        $db->query($post);

        $comment = "INSERT INTO `comments` (`id`, `date`, `userid`, `name`, `email`, `url`, `comment`, `post_id`, `parent`, `status`, `type`, `ipaddress`) VALUES
        (null, '2019-12-17 11:11:24', '{$admin}', '{$admin}', '{$admin}@{$domain}' , '', 'This is sample of comment<br />', 6, 0, '1', 'post', '::1');
        ";

        $db->query($comment);
    }
}
