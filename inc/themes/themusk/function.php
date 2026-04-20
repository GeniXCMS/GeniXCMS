<?php

class Themusk
{
    public static $opt;

    public function __construct()
    {
        if (self::checkDB()) {
            self::$opt = self::parseDB();
            if (isset(self::$opt['adsense']) && self::$opt['adsense'] != '') {
                Hooks::attach('footer_load_lib', array('Themusk', 'loadAdsenseJs'));
            }
            if (isset(self::$opt['analytics']) && self::$opt['analytics'] != '') {
                Hooks::attach('footer_load_lib', array('Themusk', 'loadAnalytics'));
            }

            // Hooks::attach('header_load_meta', array('Themusk', 'loadCSS'));
            Hooks::attach('admin_footer_action', array('Themusk', 'loadAdminAsset'));
        }

        // Register "Themusk Options" sub-item under the Themes admin menu.
        Hooks::attach('init', function () {
            AdminMenu::addChild('themes', [
                'label'  => _('Themusk Options'),
                'url'    => 'index.php?page=themes&view=options',
                'icon'   => 'bi bi-sliders',
                'access' => 0,
            ]);
        });
    }

    public static function checkDB()
    {
        if (Options::validate('themusk_options')) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDefaults()
    {
        return [
            'intro_title' => 'The Thoughtful Reader',
            'intro_text' => 'Exploring the intersection of slow living, deep focus, and the art of curation in a digital age.',
            'quote_text' => 'A room without books is like a body without a soul.',
            'quote_author' => 'Marcus Tullius Cicero',
            'default_image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?q=80&w=1000',
            'adsense' => '',
            'analytics' => '',
            'featured_posts' => '',
            'hero_btn_primary_text' => 'Read Essay',
            'hero_btn_primary_link' => '#',
            'font_headline' => 'Manrope',
            'font_body' => 'Newsreader',
            'custom_css' => '',
            'primary_color' => '#45655b',
            'on_primary_color' => '#defff3',
            'bg_surface' => '#f9f9fc',
            'on_surface' => '#2d3339',
            'on_surface_variant' => '#596066',
            'layout_site_width' => '1280',
            'layout_container_padding' => '24',
            'layout_header_py' => '32',
            'layout_footer_py' => '80',
            'layout_post_content_width' => '896',
        ];
    }

    public static function getFontsLink()
    {
        $h = self::opt('font_headline');
        $b = self::opt('font_body');
        $fonts = [$h ? $h : 'Manrope', $b ? $b : 'Newsreader'];
        $tags = ['p','h1','h2','h3','h4','h5','h6','li','code','blockquote'];
        foreach ($tags as $tag) {
            $f = self::opt("typo_{$tag}_font");
            if ($f && $f !== 'inherit') $fonts[] = trim(str_replace(['"', "'", 'sans-serif', 'serif', 'monospace', ','], '', $f));
        }
        $fonts = array_unique($fonts);
        
        $query = [];
        foreach($fonts as $f) {
            if (!$f) continue;
            $f_url = str_replace(' ', '+', trim($f));
            if (in_array(trim($f), ['Newsreader', 'Source Serif Pro', 'Lora'])) {
                $query[] = "family={$f_url}:ital,wght@0,400;0,700;1,400;1,700";
            } else {
                $query[] = "family={$f_url}:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400;1,700";
            }
        }
        return "https://fonts.googleapis.com/css2?".implode('&', $query)."&display=swap";
    }

    public static function getTypoCSS()
    {
        $tags = [
            'p' => 'typo_p',
            'h1' => 'typo_h1',
            'h2' => 'typo_h2',
            'h3' => 'typo_h3',
            'h4' => 'typo_h4',
            'h5' => 'typo_h5',
            'h6' => 'typo_h6',
            'li' => 'typo_li',
            'code' => 'typo_code',
            'blockquote' => 'typo_blockquote'
        ];
        
        $css = '<style type="text/tailwindcss">' . "\n";
        foreach ($tags as $tag => $prefix) {
            $font    = self::opt($prefix . '_font');
            $size    = self::opt($prefix . '_size');
            $weight  = self::opt($prefix . '_weight');
            $style   = self::opt($prefix . '_style');
            $color   = self::opt($prefix . '_color');
            $lh      = self::opt($prefix . '_lh');
            $ls      = self::opt($prefix . '_ls');
            $ta      = self::opt($prefix . '_align');
            
            $rules = [];
            if ($font && $font !== 'inherit') $rules[] = "font-family: $font;";
            if ($size)   $rules[] = "font-size: {$size}px;";
            if ($weight) $rules[] = "font-weight: {$weight};";
            if ($style)  $rules[] = "font-style: {$style};";
            if ($color)  $rules[] = "color: {$color};";
            if ($lh)     $rules[] = "line-height: {$lh};";
            if ($ls !== null && $ls !== '')      $rules[] = "letter-spacing: {$ls}px;";
            if ($ta && $ta !== 'inherit')      $rules[] = "text-align: {$ta};";
            
            if (!empty($rules)) {
                $css .= "  .prose-slate {$tag}, {$tag} { " . implode(' ', $rules) . " }\n";
            }
        }
        $css .= "</style>\n";
        return $css;
    }

    public static function getLayoutCSS()
    {
        $site_w = self::opt('layout_site_width') ?: '1280';
        $px = self::opt('layout_container_padding') ?: '24';
        $h_py = self::opt('layout_header_py') ?: '32';
        $f_py = self::opt('layout_footer_py') ?: '80';
        $post_w = self::opt('layout_post_content_width') ?: '896';

        $css = '<style>' . "\n";
        $css .= "  .max-site-w { max-width: {$site_w}px !important; margin-left: auto; margin-right: auto; }\n";
        $css .= "  .px-site { padding-left: {$px}px !important; padding-right: {$px}px !important; }\n";
        $css .= "  .header-py { padding-top: {$h_py}px !important; padding-bottom: {$h_py}px !important; }\n";
        $css .= "  .footer-py { padding-top: {$f_py}px !important; padding-bottom: {$f_py}px !important; }\n";
        $css .= "  .post-content-max-w { max-width: {$post_w}px !important; }\n";
        $css .= "  @media (max-width: 768px) { .px-site { padding-left: 16px !important; padding-right: 16px !important; } }\n";
        $css .= "</style>\n";
        return $css;
    }

    public static function parseDB()
    {
        $opt = Options::get('themusk_options');
        $opt = json_decode($opt, true);
        $defaults = self::getDefaults();
        $o = $defaults;
        if (is_array($opt)) {
            foreach ($opt as $k => $v) {
                $deformatted = is_string($v) ? Typo::jsonDeFormat($v) : $v;
                if ($deformatted === '' && isset($defaults[$k]) && $defaults[$k] !== '') {
                    // keep default
                } else {
                    $o[$k] = $deformatted;
                }
            }
        }
        return $o;
    }
    
    public static function getImage($post)
    {
        if (!$post) return '';
        preg_match_all('/<img .*?src=[\'"]([^\'"]+)[\'"].*?>/Ui', $post, $im);
        return isset($im[1][0]) ? $im[1][0] : '';
    }

    public static function isAdsense($adc)
    {
        if ($adc != '') {
            return str_replace('<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>', '', urldecode($adc));
        }
    }

    public static function loadAdsenseJs()
    {
        return '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
    }

    public static function opt($var)
    {
        $opt = self::$opt;
        if (isset($opt[$var])) {
            if ($var == 'adsense') {
                return self::isAdsense(Typo::Xclean($opt[$var]));
            } else {
                return is_string($opt[$var]) ? Typo::Xclean($opt[$var]) : $opt[$var];
            }
        } else {
            $defaults = self::getDefaults();
            return isset($defaults[$var]) ? $defaults[$var] : '';
        }
    }

    public static function loadAnalytics()
    {
        return self::opt('analytics');
    }

    // public static function loadCSS()
    // {
    //     if (!class_exists('GxOptionsBuilder')) {
    //         require_once __DIR__ . '/GxOptionsBuilder.php';
    //     }
    //     return GxOptionsBuilder::generateFrontendCSS(self::$opt, [
    //         'themeUrl'  => Url::theme(),
    //         'minify'    => true,
    //     ]);
    // }

    public static function loadAdminAsset()
    {
        $js = '
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            #gneexForm, .gx-wrap { box-sizing: border-box; }
            #gneexForm *, .gx-wrap * { box-sizing: inherit; }
        </style>';
        System::adminAsset($js);
    }

    public static function getCategoryDesc($id)
    {
        $id = sprintf('%d', $id);
        $cat = Db::result('SELECT `desc` FROM `cat` WHERE `id` = ? LIMIT 1', [$id]);
        return (!isset($cat['error']) && isset($cat[0]->desc)) ? $cat[0]->desc : '';
    }
}
new Themusk();
