<?php
/**
* GeniXCMS - Content Management System
* 
* PHP Based Content Management System and Framework
*
* @package GeniXCMS
* @since 0.0.1 build date 20140928
* @version 0.0.6
* @link https://github.com/semplon/GeniXCMS
* @link http://genixcms.org
* @author Puguh Wijayanto (www.metalgenix.com)
* @copyright 2014-2015 Puguh Wijayanto
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

define('GX_PATH', realpath(__DIR__ . '/'));
define('GX_LIB', GX_PATH.'/inc/lib/');
define('GX_MOD', GX_PATH.'/inc/mod/');
define('GX_THEME', GX_PATH.'/inc/themes/');
define('GX_ASSET', GX_PATH.'/assets/');

require("autoload.php");


try {
    new System();
    
} catch (Exception $e) {
    echo $e->getMessage();
}

if (isset($_POST['004-patch'])) {
    $sql = "INSERT INTO `options` (`id`, `name`, `value`) VALUES
            (null, 'google_captcha_sitekey', ''),
            (null, 'google_captcha_secret', ''),
            (null, 'google_captcha_lang', 'en'),
            (null, 'google_captcha_enable', 'off')";
    $q = Db::query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `cat_param` (
            `id` int(11) NOT NULL,
              `cat_id` int(11) NOT NULL,
              `param` text NOT NULL,
              `value` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $q = Db::query($sql);
    $sql = "ALTER TABLE `cat_param`
            ADD PRIMARY KEY (`id`)";
    $q = Db::query($sql);       
    $sql = "ALTER TABLE `cat_param`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
    $q = Db::query($sql);

    $sql = "ALTER TABLE `posts` CHANGE `cat` `cat` VARCHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
    $q = Db::query($sql);

    $sql = "ALTER TABLE `posts` ADD `views` int(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0'";
    $q = Db::query($sql);

    $sql = "ALTER TABLE `user_detail` CHANGE `fname` `fname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
    $q = Db::query($sql);

    $sql = "UPDATE `menus` SET `parent` = '0' WHERE `parent` = '' ";
    $q = Db::query($sql);

    $sql = "UPDATE `cat` SET `type` = 'post' WHERE `type` = '' ";
    $q = Db::query($sql);
    if ($q) {
        $alertgreen = 'Upgrade Success!';
    }else{
        $alertred[] = 'Upgrade Failed';
    }
}elseif(isset($_POST['005'])){
    $sql = "ALTER TABLE `posts` ADD `views` int(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0'";
    $q = Db::query($sql);
    if ($q) {
        $alertgreen = 'Upgrade Success!';
    }else{
        $alertred[] = 'Upgrade Failed';
    }
}
Theme::theme('header');
echo "<div class=\"container\">";
if(isset($alertred)) {
    echo "
    <div class=\"alert alert-danger\">
        ";
        foreach($alertred as $alert)
        {
            echo $alert;
        }
    echo"
    </div>";
}
if(isset($alertgreen)) {
    echo "
    <div class=\"alert alert-success\">
        {$alertgreen}
    </div>";
}
echo "
<h1>Upgrade v".System::$version."</h1>
<hr />
<h3>Upgrade from Version 0.0.4-patch</h3>
<form method=\"post\">
<div class=\"form-group\">
<button name=\"004-patch\" class=\"btn btn-success\"><i class=\"fa fa-upload\"></i> Upgrade from v0.0.4-patch</button>
</div>
</form>

<h3>Upgrade from Version 0.0.5</h3>
<form method=\"post\">
<div class=\"form-group\">
<button name=\"005\" class=\"btn btn-success\"><i class=\"fa fa-upload\"></i> Upgrade from v0.0.5</button>
</div>
</form>
";

echo "</div>";
Theme::theme('footer');
System::Zipped();