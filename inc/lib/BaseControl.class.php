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
        $this->latte->setautoRefresh();
        
        // Add common filters
        $this->latte->addFilter('nl2br', fn($s) => is_string($s) ? nl2br($s) : $s);
        $this->latte->addFilter('stripHtml', fn($s) => is_string($s) ? strip_tags($s) : $s);
    }

    protected function initCommonData()
    {
        $lang = Options::v('system_lang');
        $this->data['website_lang'] = substr($lang, 0, 2);
        $this->data['site_name'] = Site::$name;
        $this->data['site_footer'] = Site::footer();
        $this->data['site_url'] = Site::$url;
        $this->data['site_cdn'] = Site::$cdn;
        $this->data['site_logo'] = Site::logo(width:'200px', class: "img-fluid");
        $this->data['theme_url'] = Url::theme();
        $this->data['p_type'] = '';
        $this->data['token'] = TOKEN;
        $this->data['tag_cloud'] = Tags::cloud();
        $this->data['archives_list'] = Archives::list(10);
        $this->data['platform_name'] = "GeniXCMS";
        $this->data['platform_version'] = System::v();
        $this->data['platform_fullname'] = $this->data['platform_name'] . " " . $this->data['platform_version'];
    }

    protected function render($view, $data = [])
    {
        $this->data = array_merge($this->data, $data);
        $this->data['site_meta'] = Site::meta($this->data);
        
        Cache::start();
        $this->latte->render(GX_THEME . Theme::$active . '/header.php', $this->data);
        $this->latte->render(GX_THEME . Theme::$active . '/' . $view . '.php', $this->data);
        $this->latte->render(GX_THEME . Theme::$active . '/footer.php', $this->data);
        Cache::end();
    }
}
