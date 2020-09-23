<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20140928
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
date_default_timezone_set('UTC');

define('GX_PATH', realpath(__DIR__.'/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require 'autoload.php';

try {
    new System();
} catch (Exception $e) {
    echo $e->getMessage();
}

$url = Site::$url.'/';

if (isset($_POST['004-patch'])) {
    $sql = "INSERT INTO `options` (`id`, `name`, `value`) VALUES
            (null, 'google_captcha_sitekey', ''),
            (null, 'google_captcha_secret', ''),
            (null, 'google_captcha_lang', 'en'),
            (null, 'google_captcha_enable', 'off'),
            (null, 'multilang_enable', 'off'),
            (null, 'multilang_default', ''),
            (null, 'multilang_country', ''),
            (null, 'system_check', '{}'),
            (null, 'permalink_use_index_php', 'off'),
            (null, 'pinger_enable', 'off'),
            (null, 'cdn_url', '{$url}'),
            (null, 'spamwords', ''),
            (null, 'comments_perpage', '5'),
            (null, 'comments_enable', 'on')";
    $q = Db::query($sql);

    $sql = 'CREATE TABLE IF NOT EXISTS `cat_param` (
            `id` int(11) NOT NULL,
              `cat_id` int(11) NOT NULL,
              `param` text NOT NULL,
              `value` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `cat_param`
            ADD PRIMARY KEY (`id`)';
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `cat_param`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT';
    $q = Db::query($sql);

    $sql = 'ALTER TABLE `posts` CHANGE `cat` `cat` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
    $q = Db::query($sql);

    $sql = "ALTER TABLE `posts` ADD `views` int(11) NOT NULL DEFAULT '0'";
    $q = Db::query($sql);

    $sql = 'ALTER TABLE `user_detail` CHANGE `fname` `fname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL';
    $q = Db::query($sql);

    $sql = "UPDATE `menus` SET `parent` = '0' WHERE `parent` = '' ";
    $q = Db::query($sql);

    $sql = "UPDATE `cat` SET `type` = 'post' WHERE `type` = '' ";
    $q = Db::query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `comments` (
              `id` bigint(22) NOT NULL,
              `date` datetime NOT NULL,
              `userid` text NOT NULL,
              `name` text NOT NULL,
              `email` text NOT NULL,
              `url` text NOT NULL,
              `comment` text NOT NULL,
              `post_id` int(11) NOT NULL,
              `parent` int(11) NOT NULL,
              `status` enum('0','1','2') NOT NULL,
              `type` text NOT NULL,
              `ipaddress` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ";
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            ADD PRIMARY KEY (`id`)
            ';
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            MODIFY `id` bigint(22) NOT NULL AUTO_INCREMENT
            ';
    $q = Db::query($sql);

    new Options();
    $options = Options::$_data;
    $q = Options::update($options);

    $q = Options::update('siteurl', $url);
    $q = addCacheOptions();
    $q = alterUserGroup();

    if ($q) {
        $alertSuccess = 'Upgrade Success!';
    } else {
        $alertDanger[] = 'Upgrade Failed';
    }
} elseif (isset($_POST['005'])) {
    $sql = "ALTER TABLE `posts` ADD `views` int(11) NOT NULL DEFAULT '0'";
    $q = Db::query($sql);
    $sql = "INSERT INTO `options` VALUES
            (null, 'multilang_enable', 'off'),
            (null, 'multilang_default', ''),
            (null, 'multilang_country', ''),
            (null, 'system_check', '{}'),
            (null, 'permalink_use_index_php', 'off'),
            (null, 'pinger_enable', 'off'),
            (null, 'cdn_url', '{$url}'),
            (null, 'spamwords', ''),
            (null, 'comments_perpage', '5'),
            (null, 'comments_enable', 'on')
            ";
    $q = Db::query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `comments` (
              `id` bigint(22) NOT NULL,
              `date` datetime NOT NULL,
              `userid` text NOT NULL,
              `name` text NOT NULL,
              `email` text NOT NULL,
              `url` text NOT NULL,
              `comment` text NOT NULL,
              `post_id` int(11) NOT NULL,
              `parent` int(11) NOT NULL,
              `status` enum('0','1','2') NOT NULL,
              `type` text NOT NULL,
              `ipaddress` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ";
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            ADD PRIMARY KEY (`id`)
            ';
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            MODIFY `id` bigint(22) NOT NULL AUTO_INCREMENT
            ';
    $q = Db::query($sql);

    $options = Options::$_data;
    $opt = [];
    foreach ($options as $k => $v){
        $opt[$k] = Typo::cleanX($v);
    }
    $q = Options::update($opt);

    $q = Options::update('siteurl', $url);
    $q = addCacheOptions();
    $q = alterUserGroup();

    if ($q) {
        $alertSuccess = 'Upgrade Success!';
    } else {
        $alertDanger[] = 'Upgrade Failed';
    }
} elseif (isset($_POST['006'])) {
    $sql = "INSERT INTO `options` VALUES
            (null, 'multilang_enable', 'off'),
            (null, 'multilang_default', ''),
            (null, 'multilang_country', ''),
            (null, 'system_check', '{}'),
            (null, 'permalink_use_index_php', 'off'),
            (null, 'pinger_enable', 'off'),
            (null, 'cdn_url', '{$url}'),
            (null, 'spamwords', ''),
            (null, 'comments_perpage', '5'),
            (null, 'comments_enable', 'on')
            ";
    $q = Db::query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `comments` (
              `id` bigint(22) NOT NULL,
              `date` datetime NOT NULL,
              `userid` text NOT NULL,
              `name` text NOT NULL,
              `email` text NOT NULL,
              `url` text NOT NULL,
              `comment` text NOT NULL,
              `post_id` int(11) NOT NULL,
              `parent` int(11) NOT NULL,
              `status` enum('0','1','2') NOT NULL,
              `type` text NOT NULL,
              `ipaddress` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ";
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            ADD PRIMARY KEY (`id`)
            ';
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            MODIFY `id` bigint(22) NOT NULL AUTO_INCREMENT
            ';
    $q = Db::query($sql);

    $options = Options::$_data;
    $opt = [];
    foreach ($options as $k => $v){
        $opt[$k] = Typo::cleanX($v);
    }
    $q = Options::update($opt);

    $q = Options::update('siteurl', $url);

    $q = alterUserGroup();
    $q = addCacheOptions();
    if ($q) {
        $alertSuccess = 'Upgrade Success!';
    } else {
        $alertDanger[] = 'Upgrade Failed';
    }
} elseif (isset($_POST['007'])) {
    $sql = "INSERT INTO `options` VALUES
            (null, 'permalink_use_index_php', 'off'),
            (null, 'pinger_enable', 'off'),
            (null, 'cdn_url', '{$url}'),
            (null, 'spamwords', ''),
            (null, 'comments_perpage', '5'),
            (null, 'comments_enable', 'on')
            ";
    $q = Db::query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `comments` (
              `id` bigint(22) NOT NULL,
              `date` datetime NOT NULL,
              `userid` text NOT NULL,
              `name` text NOT NULL,
              `email` text NOT NULL,
              `url` text NOT NULL,
              `comment` text NOT NULL,
              `post_id` int(11) NOT NULL,
              `parent` int(11) NOT NULL,
              `status` enum('0','1','2') NOT NULL,
              `type` text NOT NULL,
              `ipaddress` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ";
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            ADD PRIMARY KEY (`id`)
            ';
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            MODIFY `id` bigint(22) NOT NULL AUTO_INCREMENT
            ';
    $q = Db::query($sql);

    $options = Options::$_data;
    $opt = [];
    foreach ($options as $k => $v){
        $opt[$k] = Typo::cleanX($v);
    }
    $q = Options::update($opt);

    $q = Options::update('siteurl', $url);
    $q = addCacheOptions();
    $q = alterUserGroup();
    if ($q) {
        $alertSuccess = 'Upgrade Success!';
    } else {
        $alertDanger[] = 'Upgrade Failed';
    }
} elseif (isset($_POST['008'])) {
    $sql = "INSERT INTO `options` VALUES
            (null, 'pinger_enable', 'off'),
            (null, 'cdn_url', '{$url}'),
            (null, 'spamwords', ''),
            (null, 'comments_perpage', '5'),
            (null, 'comments_enable', 'on')
            ";
    $q = Db::query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `comments` (
              `id` bigint(22) NOT NULL,
              `date` datetime NOT NULL,
              `userid` text NOT NULL,
              `name` text NOT NULL,
              `email` text NOT NULL,
              `url` text NOT NULL,
              `comment` text NOT NULL,
              `post_id` int(11) NOT NULL,
              `parent` int(11) NOT NULL,
              `status` enum('0','1','2') NOT NULL,
              `type` text NOT NULL,
              `ipaddress` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ";
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            ADD PRIMARY KEY (`id`)
            ';
    $q = Db::query($sql);
    $sql = 'ALTER TABLE `comments`
            MODIFY `id` bigint(22) NOT NULL AUTO_INCREMENT
            ';
    $q = Db::query($sql);

    $options = Options::$_data;
    $opt = [];
    foreach ($options as $k => $v){
        $opt[$k] = Typo::cleanX($v);
    }
    $q = Options::update($opt);

    $q = Options::update('siteurl', $url);
    $q = addCacheOptions();
    $q = alterUserGroup();
    if ($q) {
        $alertSuccess = 'Upgrade Success!';
    } else {
        $alertDanger[] = 'Upgrade Failed';
    }
} elseif (isset($_POST['100'])) {
    $q = addCacheOptions();
    $q = alterUserGroup();
    if ($q) {
        $alertSuccess = 'Upgrade Success!';
    } else {
        $alertDanger[] = 'Upgrade Failed';
    }
} elseif (isset($_POST['111'])) {
    $q = addCacheOptions();
    if ($q) {
        $alertSuccess = 'Upgrade Success!';
    } else {
        $alertDanger[] = 'Upgrade Failed';
    }
}

function alterUserGroup(){
    $sql = "ALTER TABLE `user` CHANGE `group` `group` ENUM('0','1','2','3','4','5','6') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
    $q = Db::query($sql);
    return $q;
}

function addCacheOptions(){
    $vars = array(
        'cache_path' => '/assets/cache/pages/',
        'cache_timeout' => '300',
        'cache_enabled' => 'off'
      );
    $q = Options::insert($vars);
    return $q;
}

Theme::theme('header');
echo '<div class="container">';
if (isset($alertDanger)) {
    echo '
    <div class="alert alert-danger">
        ';
    foreach ($alertDanger as $alert) {
        echo Typo::cleanX($alert);
    }
    echo'
    </div>';
}
if (isset($alertSuccess)) {
    echo "
    <div class=\"alert alert-success\">";
        echo Typo::cleanX($alertSuccess);
    echo"</div>";
}
echo '
<h1>Upgrade v'.System::$version.'</h1>
<hr />
<h3>Upgrade from Version 1.0.1 - 1.1.1</h3>
<form method="post">
<div class="form-group">
<button name="111" class="btn btn-success"><i class="fa fa-upload"></i> Upgrade from v1.0.0-v1.1.1</button>
</div>
</form>

<h3>Upgrade from Version 1.0.0</h3>
<form method="post">
<div class="form-group">
<button name="100" class="btn btn-success"><i class="fa fa-upload"></i> Upgrade from v1.0.0</button>
</div>
</form>

<h3>Upgrade from Version 0.0.8</h3>
<form method="post">
<div class="form-group">
<button name="008" class="btn btn-success"><i class="fa fa-upload"></i> Upgrade from v0.0.8</button>
</div>
</form>

<h3>Upgrade from Version 0.0.7</h3>
<form method="post">
<div class="form-group">
<button name="007" class="btn btn-success"><i class="fa fa-upload"></i> Upgrade from v0.0.7</button>
</div>
</form>

<h3>Upgrade from Version 0.0.6</h3>
<form method="post">
<div class="form-group">
<button name="006" class="btn btn-success"><i class="fa fa-upload"></i> Upgrade from v0.0.6</button>
</div>
</form>

<h3>Upgrade from Version 0.0.5</h3>
<form method="post">
<div class="form-group">
<button name="005" class="btn btn-success"><i class="fa fa-upload"></i> Upgrade from v0.0.5</button>
</div>
</form>

<h3>Upgrade from Version 0.0.4-patch</h3>
<form method="post">
<div class="form-group">
<button name="004-patch" class="btn btn-success"><i class="fa fa-upload"></i> Upgrade from v0.0.4-patch</button>
</div>
</form>
';

echo '</div>';
Theme::theme('footer');
System::Zipped();