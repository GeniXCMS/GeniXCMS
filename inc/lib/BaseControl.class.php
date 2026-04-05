<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

abstract class BaseControl
{
    protected $latte;
    protected $data = [];
    protected $db;
    protected $user;
    protected $system;

    public function __construct($db = null, $user = null, $system = null)
    {
        $this->db = $db ?? Container::get('db');
        $this->user = $user ?? Container::get('user');
        $this->system = $system ?? Container::get('system');

        $this->initLatte();
        $this->initCommonData();
    }

    protected function initLatte()
    {
        $lang = Options::v('system_lang');
        $this->latte = new Latte\Engine;
        $this->latte->addExtension(new Latte\Essential\RawPhpExtension);
        $this->latte->addExtension(new Latte\Essential\TranslatorExtension(
            Typo::translate(...),
            $lang,
        ));
        $this->latte->setTempDirectory(GX_CACHE . '/temp');
        $this->latte->setAutoRefresh(true);

        // Add common filters
        $this->latte->addFilter('nl2br', fn($s) => is_string($s) ? nl2br($s) : $s);
        $this->latte->addFilter('stripHtml', fn($s) => is_string($s) ? Shortcode::strip(strip_tags($s)) : $s);
    }

    protected function initCommonData()
    {
        $lang = Options::v('system_lang');
        $this->data['website_lang'] = substr($lang, 0, 2);
        $this->data['site_name'] = Site::$name;
        $this->data['site_footer'] = Site::footer();
        $this->data['site_url'] = Site::$url;
        $this->data['site_cdn'] = Site::$cdn;
        $mdo_opt = json_decode(Options::get('default_theme_options_v2'), true) ?: [];
        $logo_h = !empty($mdo_opt['logo_height']) ? $mdo_opt['logo_height'] . 'px' : '40px';
        $this->data['site_logo'] = Site::logo(height: $logo_h, class: "img-fluid");
        $this->data['theme_url'] = Url::theme();
        $this->data['p_type'] = '';
        $this->data['token'] = TOKEN;
        $this->data['tag_cloud'] = Tags::cloud();
        $this->data['archives_list'] = Archives::getList(10);
        $this->data['platform_name'] = "GeniXCMS";
        $this->data['platform_version'] = System::v();
        $this->data['platform_fullname'] = $this->data['platform_name'] . " " . $this->data['platform_version'];
    }

    protected function render($view, $viewData = [])
    {
        global $data;
        $this->data = array_merge($this->data, $viewData);
        $data = $this->data;
        $this->data['data'] = $this->data;
        $this->data['site_meta'] = Site::meta($this->data);

        $theme_dir = rtrim(GX_THEME, '/\\') . DIRECTORY_SEPARATOR . Theme::$active . DIRECTORY_SEPARATOR;

        $v_file = file_exists($theme_dir . $view . '.latte') ? $view . '.latte' : $view . '.php';
        $h_file = file_exists($theme_dir . 'header.latte') ? 'header.latte' : 'header.php';
        $f_file = file_exists($theme_dir . 'footer.latte') ? 'footer.latte' : 'footer.php';

        $h_out = $this->latte->renderToString($theme_dir . $h_file, $this->data);
        $v_out = $this->latte->renderToString($theme_dir . $v_file, $this->data);
        $f_out = $this->latte->renderToString($theme_dir . $f_file, $this->data);

        echo $h_out;
        echo $v_out;
        echo $f_out;

        flush();
    }
}
