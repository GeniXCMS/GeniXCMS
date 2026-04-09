<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150219
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
$lang = Options::v('system_lang');
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
$data['site_logo'] = Site::logo(width: '200px', class: "img-fluid");
$data['theme_url'] = Url::theme();
$data['token'] = TOKEN;
$data['tag_cloud'] = Tags::cloud();
$data['archives_list'] = Archives::getList(10);
$data['platform_name'] = "GeniXCMS";
$data['platform_version'] = System::v();
$data['platform_fullname'] = $data['platform_name'] . " " . $data['platform_version'];

$data['site_meta'] = Site::meta($data);

header('HTTP/1.0 403 Forbidden');
$h_file = file_exists(GX_THEME . Theme::$active . '/header.latte') ? '/header.latte' : '/header.php';
$f_file = file_exists(GX_THEME . Theme::$active . '/footer.latte') ? '/footer.latte' : '/footer.php';

if (Theme::exist('403')) {
    $latte->render(GX_THEME . Theme::$active . $h_file, $data);
    $v_file = file_exists(GX_THEME . Theme::$active . '/403.latte') ? '/403.latte' : '/403.php';
    $latte->render(GX_THEME . Theme::$active . $v_file, $data);
    $latte->render(GX_THEME . Theme::$active . $f_file, $data);
} else {
    $latte->render(GX_THEME . Theme::$active . $h_file, $data);
    echo '<center class="mb-5 mt-5">
        <h1>Ooops!!</h1>
        <h2 style="font-size: 20em">403</h2>
        <h3>Forbidden!!</h3>
        Back to <a href="' . Options::v('siteurl') . '">' . Options::v('sitename') . '</a>
        </center>
        ';
    $latte->render(GX_THEME . Theme::$active . $f_file, $data);
}
