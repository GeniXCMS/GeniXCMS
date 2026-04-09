<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Base Control Class
 *
 * Handles initialization of Latte engine and common data for controllers
 * @since 2.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

abstract class BaseControl
{
    protected $latte;
    protected $data = [];
    protected $db;
    protected $user;
    protected $system;

    /**
     * BaseControl Constructor.
     * Initializes core system objects, the template engine, and common view data.
     *
     * @param object|null $db     Database connection instance.
     * @param object|null $user   User management instance.
     * @param object|null $system System core instance.
     */
    public function __construct($db = null, $user = null, $system = null)
    {
        $this->db = $db ?? Container::get('db');
        $this->user = $user ?? Container::get('user');
        $this->system = $system ?? Container::get('system');

        $this->initLatte();
        $this->initCommonData();
    }

    /**
     * Initializes the Latte template engine.
     * Configures extensions (PHP, Translator), cache directories, and custom filters.
     */
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

    /**
     * Populates the common data array used across all frontend views.
     * Includes site name, URL, CDN, current theme info, and system metadata.
     */
    protected function initCommonData()
    {
        $lang = Options::v('system_lang');
        $this->data['website_lang'] = substr($lang, 0, 2);
        $this->data['site_name'] = Site::$name;
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

    /**
     * Renders a specific view within the active theme.
     * Combines header, main view, and footer into a single response.
     *
     * @param string $view     The name of the view file (without extension).
     * @param array  $viewData Additional data to pass to the view.
     */
    protected function render($view, $viewData = [])
    {
        global $data;
        $this->data = array_merge($this->data, $viewData);
        $data = $this->data;
        $this->data['data'] = $this->data;
        $this->data['site_meta'] = Site::meta($this->data);

        $theme_dir = rtrim(GX_THEME, '/\\') . DIRECTORY_SEPARATOR . Theme::$active . DIRECTORY_SEPARATOR;

        $layout = $this->data['layout'] ?? 'default';
        $h_file_name = 'header';
        $f_file_name = 'footer';
        $v_file_name = $view;

        if ($layout !== 'default') {
            // Check for layout-specific partials
            if (file_exists($theme_dir . 'header-' . $layout . '.latte') || file_exists($theme_dir . 'header-' . $layout . '.php')) {
                $h_file_name = 'header-' . $layout;
            }
            if (file_exists($theme_dir . 'footer-' . $layout . '.latte') || file_exists($theme_dir . 'footer-' . $layout . '.php')) {
                $f_file_name = 'footer-' . $layout;
            }
            // Check for layout-specific main view
            if (file_exists($theme_dir . 'layout-' . $layout . '.latte') || file_exists($theme_dir . 'layout-' . $layout . '.php')) {
                $v_file_name = 'layout-' . $layout;
            }
        }

        $v_file = file_exists($theme_dir . $v_file_name . '.latte') ? $v_file_name . '.latte' : $v_file_name . '.php';
        $h_file = file_exists($theme_dir . $h_file_name . '.latte') ? $h_file_name . '.latte' : $h_file_name . '.php';
        $f_file = file_exists($theme_dir . $f_file_name . '.latte') ? $f_file_name . '.latte' : $f_file_name . '.php';

        $h_out = $this->latte->renderToString($theme_dir . $h_file, $this->data);
        $v_out = $this->latte->renderToString($theme_dir . $v_file, $this->data);
        $f_out = $this->latte->renderToString($theme_dir . $f_file, $this->data);

        echo $h_out;
        echo $v_out;
        echo $f_out;

        flush();
    }
}
