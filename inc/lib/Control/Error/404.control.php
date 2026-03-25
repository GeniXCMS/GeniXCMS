<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
 *
 * @version 2.0.0
 *
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @author GenixCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2024 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
$lang = Options::v('system_lang');

// Send 404 header BEFORE any output
if (!headers_sent()) {
    header('HTTP/1.0 404 Not Found');
}

$latte = new Latte\Engine;
$latte->addExtension(new Latte\Essential\RawPhpExtension);
$latte->addExtension(new Latte\Essential\TranslatorExtension(
	Typo::translate(...),
	$lang,
));
// Set the temporary directory for compiled templates
$latte->setTempDirectory(GX_CACHE . '/temp');

// Enable auto-refresh for development mode
$latte->setautoRefresh();

$data['site_name'] = Site::$name;
$data['site_footer'] = Site::footer();
$data['site_url'] = Site::$url;
$data['site_cdn'] = Site::$cdn;
$data['site_logo'] = Site::logo(width:'200px', class: "img-fluid");
$data['theme_url'] = Url::theme();
$data['token'] = TOKEN;
$data['tag_cloud'] = Tags::cloud();
$data['archives_list'] = Archives::list(10);
$data['platform_name'] = "GeniXCMS";
$data['platform_version'] = System::v();
$data['platform_fullname'] = $data['platform_name'] . " " . $data['platform_version'];

$data['site_meta'] = Site::meta($data);


if (Theme::exist('404')) {
    $latte->render(GX_THEME . Theme::$active . '/header.php', $data );
    $latte->render(GX_THEME . Theme::$active . '/404.php', $data );
    $latte->render(GX_THEME . Theme::$active . '/footer.php', $data );
} else {
    
    $latte->render(GX_THEME . Theme::$active . '/header.php', $data );

    echo '<center class="mb-5 mt-5">
        <h1>Ooops!!</h1>
        <h2 style="font-size: 20em">404</h2>
        <h3>Page Not Found</h3>
        Back to <a href="'.Options::v('siteurl').'">'.Options::v('sitename').'</a>
        </center>
        ';
        if (isset($val) && $val != '') {
?>
    <div class="container">
        <div class="alert alert-danger">
        <?=$val; ?>
        </div>
    </div>
<?php
        }
        
    $latte->render(GX_THEME . Theme::$active . '/footer.php', $data );
}
