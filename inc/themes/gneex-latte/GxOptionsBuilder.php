<?php
/**
 * GxOptionsBuilder
 *
 * A self-contained, reusable Options Panel Builder.
 * Can be used in any GeniXCMS theme or module.
 *
 * USAGE — Admin Panel:
 *   $builder = new GxOptionsBuilder($opt, $presets, $palettes, [
 *       'brandName' => 'My Theme',
 *       'brandVer'  => 'v1.0',
 *       'brandAbbr' => 'MT',
 *       'saveKey'   => 'my_theme_save',
 *   ]);
 *   $builder->render($schema);
 *
 * USAGE — Frontend CSS:
 *   echo GxOptionsBuilder::generateFrontendCSS($opt, [
 *       'themeUrl'     => Url::theme(),
 *       'extraCssRules'=> '.my-class { color: red; }',
 *       'minify'       => true,   // requires Site::minifyCSS()
 *   ]);
 */
class GxOptionsBuilder
{
    // ── Instance properties ────────────────────────────────────
    private array  $opt;
    private array  $presets;
    private array  $panel_palettes;

    // Brand / panel identity
    private string $brandName;
    private string $brandVer;
    private string $brandAbbr;   // Short abbreviation for the logo box, e.g. 'GL'
    private string $brandIcon;   // Optional FA icon class instead of abbr, e.g. 'fa fa-paint-brush'
    private string $brandColor;  // Gradient start color for logo box
    private string $saveKey;

    // ── Shared font registry ───────────────────────────────────

    /** Font-family value → human label (for dropdowns) */
    public static array $FONTS = [
        'inherit'                     => 'Inherit (Default)',
        '"Inter", sans-serif'         => 'Inter (Sans-Serif)',
        '"Roboto", sans-serif'        => 'Roboto',
        '"Open Sans", sans-serif'     => 'Open Sans',
        '"Lato", sans-serif'          => 'Lato',
        '"Poppins", sans-serif'       => 'Poppins',
        '"Nunito", sans-serif'        => 'Nunito',
        '"Merriweather", serif'       => 'Merriweather (Serif)',
        '"Playfair Display", serif'   => 'Playfair Display (Serif)',
        '"Georgia", serif'            => 'Georgia (Serif)',
        '"JetBrains Mono", monospace' => 'JetBrains Mono (Monospace)',
    ];

    /** Font-family value → Google Fonts API parameter string */
    public static array $GF_MAP = [
        '"Inter", sans-serif'         => 'Inter:wght@300;400;500;600;700;800;900',
        '"Roboto", sans-serif'        => 'Roboto:wght@300;400;500;700;900',
        '"Open Sans", sans-serif'     => 'Open+Sans:wght@300;400;500;600;700;800',
        '"Lato", sans-serif'          => 'Lato:wght@300;400;700;900',
        '"Poppins", sans-serif'       => 'Poppins:wght@300;400;500;600;700;800;900',
        '"Nunito", sans-serif'        => 'Nunito:wght@300;400;500;600;700;800;900',
        '"Merriweather", serif'       => 'Merriweather:wght@300;400;700;900',
        '"Playfair Display", serif'   => 'Playfair+Display:wght@400;500;600;700;800;900',
        '"Georgia", serif'            => '', // web-safe, no import needed
        '"JetBrains Mono", monospace' => 'JetBrains+Mono:wght@400;700',
    ];

    public static array $WEIGHTS = [
        '300' => 'Light (300)',
        '400' => 'Regular (400)',
        '500' => 'Medium (500)',
        '600' => 'Semi-Bold (600)',
        '700' => 'Bold (700)',
        '800' => 'Extra Bold (800)',
        '900' => 'Black (900)',
    ];

    /**
     * @param array $opt            Saved options key-value array
     * @param array $presets        Preset color schemes (for Quick Presets tab)
     * @param array $panel_palettes Panel color palettes
     * @param array $config         Builder identity configuration:
     *   - brandName  (string)  Display name shown in topbar, default 'GneeX Latte'
     *   - brandVer   (string)  Version badge text, default 'v2.1'
     *   - brandAbbr  (string)  Two-letter abbreviation for logo box, default 'GL'
     *   - brandIcon  (string)  Font Awesome icon class instead of abbr (e.g. 'fa fa-cog')
     *   - brandColor (string)  CSS gradient color for logo box, default '#3b82f6'
     *   - saveKey    (string)  POST key for save button, default 'gneex_options_update'
     */
    public function __construct(
        array  $opt,
        array  $presets        = [],
        array  $panel_palettes = [],
        array  $config         = []
    ) {
        $this->opt            = $opt;
        $this->presets        = $presets;
        $this->panel_palettes = $panel_palettes;
        $this->brandName      = $config['brandName']  ?? 'GneeX Latte';
        $this->brandVer       = $config['brandVer']   ?? 'v2.1';
        $this->brandAbbr      = $config['brandAbbr']  ?? 'GL';
        $this->brandIcon      = $config['brandIcon']  ?? '';
        $this->brandColor     = $config['brandColor'] ?? '#3b82f6';
        $this->saveKey        = $config['saveKey']    ?? 'gneex_options_update';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STATIC: FRONTEND CSS GENERATOR
    // Moved from Gneex::loadCSS() so any theme/module can call it.
    //
    // Usage:
    //   $html = GxOptionsBuilder::generateFrontendCSS($opt, [
    //       'themeUrl'  => Url::theme(),
    //       'minify'    => true,
    //   ]);
    //   echo $html; // outputs <link> tags + <style> block
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate the full frontend <link>/<style> output driven by saved options.
     *
     * @param array $opt    Saved theme options
     * @param array $config Configuration overrides:
     *   - themeUrl      (string) Base URL for theme assets (used for style.css link)
     *   - extraCssRules (string) Additional CSS appended inside <style> (replaces custom_css if set)
     *   - minify        (bool)   Whether to call Site::minifyCSS() on output (default false)
     *   - typoKeys      (array)  Override which option keys to scan for Google Fonts
     * @return string
     */
    public static function generateFrontendCSS(array $opt, array $config = []): string
    {
        $themeUrl  = $config['themeUrl']  ?? '';
        $minify    = $config['minify']    ?? false;
        $extraCSS  = $config['extraCssRules'] ?? ($opt['custom_css'] ?? '');

        // ── Google Fonts ──────────────────────────────────────────────────
        $typoKeys = $config['typoKeys'] ?? [
            'typo_body_font', 'typo_nav_font',
            'typo_h1_font', 'typo_h2_font', 'typo_h3_font', 'typo_h4_font',
            'typo_post_title_font', 'typo_meta_font',
            'typo_single_title_font', 'typo_content_font', 'typo_post_meta_font',
            'typo_blockquote_font', 'typo_breadcrumb_font',
            'typo_comment_title_font', 'typo_comment_body_font',
        ];
        $fontsToLoad = [];
        foreach ($typoKeys as $k) {
            $ff = $opt[$k] ?? 'inherit';
            if ($ff !== 'inherit' && isset(self::$GF_MAP[$ff]) && self::$GF_MAP[$ff] !== '') {
                $fontsToLoad[] = self::$GF_MAP[$ff];
            }
        }
        $fontHtml = '';
        if ($fontsToLoad) {
            $fontsToLoad = array_unique($fontsToLoad);
            $q = implode('&family=', $fontsToLoad);
            $fontHtml = '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n"
                . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n"
                . '<link href="https://fonts.googleapis.com/css2?family=' . $q . '&display=swap" rel="stylesheet">' . "\n";
        }

        // ── External assets ───────────────────────────────────────────────
        $assets = $fontHtml
            . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">' . "\n"
            . '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">' . "\n"
            . '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/v4-shims.min.css">' . "\n"
            . '<link href="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.6.2/flexslider.min.css" rel="stylesheet">' . "\n"
            . ($themeUrl ? '<link href="' . $themeUrl . 'css/style.css" rel="stylesheet">' . "\n" : '');

        // ── Helper: build a CSS font-family declaration ───────────────────
        $ff = function(string $key, string $fallback = '') use ($opt): string {
            $v = $opt[$key] ?? 'inherit';
            return ($v && $v !== 'inherit') ? "font-family: {$v};" : ($fallback ? "font-family: {$fallback};" : '');
        };
        $fc = function(string $key, string $fallback = '') use ($opt): string {
            $v = $opt[$key] ?? '';
            return $v ?: $fallback;
        };
        $fv = function(string $key, $default) use ($opt) {
            return $opt[$key] ?? $default;
        };

        $overlayColor = $opt['background_color_header'] ?? '#0f172a';


        // ── Dynamic <style> block ─────────────────────────────────────────
        // Using explicit concatenation to avoid quote conflicts inside CSS.
        $borderW   = $opt['content_border_width']  ?? 1;
        $borderC   = $opt['content_border_color']  ?? '#e2e8f0';
        $cardBg    = $opt['content_background_color_body'] ?: '#ffffff';
        $sbarBg    = $fc('sidebar_background_color_header', 'rgba(0,0,0,.03)');
        $sbarFg    = $fc('sidebar_font_color_header', 'var(--text-heading)');
        $sbarBodyBg= $fc('sidebar_background_color_body', '#ffffff');
        $sbarBodyFg= $fc('sidebar_font_color_body', 'var(--text-main)');
        $ftBg      = $fc('background_color_footer', '#0f172a');
        $ftFg      = $fc('font_color_footer', '#94a3b8');
        $ftLink    = $fc('link_color_footer', 'rgba(255,255,255,.6)');
        $brdColor  = $fc('typo_breadcrumb_color', $fc('font_color_header', '#ffffff'));
        $hdrFg     = $fc('font_color_header', '#ffffff');
        $brdSel    = ($borderW > 0 && $borderC) ? $borderC : 'rgba(0,0,0,.06)';

        $parts = [];
        $parts[] = ':root {'
            . '--primary-color:' . $fc('link_color', '#3b82f6') . ';'
            . '--primary-hover:' . $fc('link_color_hover', '#2563eb') . ';'
            . '--bg-body:' . $fc('body_background_color', '#f8fafc') . ';'
            . '--text-main:' . $fc('content_font_color_body', '#334155') . ';'
            . '--text-heading:' . $fc('content_title_color', '#0f172a') . ';'
            . '--card-bg:' . $cardBg . ';'
            . '--card-shadow:0 10px 30px -10px rgba(0,0,0,.08),0 4px 10px -5px rgba(0,0,0,.04);'
            . '--card-shadow-hover:0 25px 50px -12px rgba(0,0,0,.15);'
            . '--transition:all .4s cubic-bezier(.16,1,.3,1);'
            . '--radius-lg:0px;--radius-md:0px;}';
        $parts[] = '::selection{background:var(--primary-color);color:#fff;}';
        $parts[] = 'body{'
            . 'background-color:var(--bg-body);'
            . $ff('typo_body_font', '"Plus Jakarta Sans",sans-serif')
            . 'color:' . $fc('typo_body_color', 'var(--text-main)') . ';'
            . 'font-size:' . $fv('typo_body_size', '16') . 'px;'
            . 'font-weight:' . $fv('typo_body_weight', '400') . ';'
            . 'line-height:1.7;-webkit-font-smoothing:antialiased;}';
        $parts[] = 'body.is-fullwidth{padding:0!important;margin:0!important;}';
        $parts[] = 'body.is-fullwidth #fullwidth-frontpage-content{width:100%;min-height:100vh;}';
        $parts[] = 'h1{' . $ff('typo_h1_font','"Outfit",sans-serif') . 'color:' . $fc('typo_h1_color','var(--text-heading)') . ';font-size:' . $fv('typo_h1_size','36') . 'px;font-weight:' . $fv('typo_h1_weight','800') . ';}';
        $parts[] = 'h2{' . $ff('typo_h2_font','"Outfit",sans-serif') . 'color:' . $fc('typo_h2_color','var(--text-heading)') . ';font-size:' . $fv('typo_h2_size','28') . 'px;font-weight:' . $fv('typo_h2_weight','700') . ';}';
        $parts[] = 'h3{' . $ff('typo_h3_font','"Outfit",sans-serif') . 'color:' . $fc('typo_h3_color','var(--text-heading)') . ';font-size:' . $fv('typo_h3_size','22') . 'px;font-weight:' . $fv('typo_h3_weight','700') . ';}';
        $parts[] = 'h4,h5,h6{' . $ff('typo_h4_font','"Outfit",sans-serif') . 'color:' . $fc('typo_h4_color','var(--text-heading)') . ';font-size:' . $fv('typo_h4_size','18') . 'px;font-weight:' . $fv('typo_h4_weight','600') . ';}';
        $parts[] = '.post-title a{'
            . $ff('typo_post_title_font')
            . 'color:' . $fc('typo_post_title_color','var(--text-heading)') . '!important;'
            . 'font-size:' . $fv('typo_post_title_size','20') . 'px;'
            . 'font-weight:' . $fv('typo_post_title_weight','700') . ';}';
        $parts[] = '.post-meta,.post-meta a,.post-meta span{'
            . $ff('typo_meta_font')
            . 'color:' . $fc('typo_meta_color','#64748b') . '!important;'
            . 'font-size:' . $fv('typo_meta_size','13') . 'px;'
            . 'font-weight:' . $fv('typo_meta_weight','400') . ';}';
        $parts[] = '.container{max-width:' . $fv('container_width','1280') . 'px;}';
        $parts[] = 'a{color:var(--primary-color);text-decoration:none;transition:var(--transition);}';
        $parts[] = 'a:hover{color:var(--primary-hover);}';
        $parts[] = '#header{'
            . 'background:' . $fc('background_color_navbar', 'rgba(255,255,255,.98)') . '!important;'
            . 'border-bottom:2px solid var(--primary-color);'
            . 'min-height:90px;display:flex;align-items:center;'
            . 'position:sticky;top:0;z-index:1100;'
            . 'box-shadow:0 4px 20px rgba(0,0,0,0.05);}';
        $parts[] = '.navbar-nav .nav-item { position: relative; margin: 0 4px; }';
        $parts[] = '.navbar-nav > .nav-item > .nav-link {'
            . $ff('typo_nav_font','"Plus Jakarta Sans",sans-serif')
            . 'font-weight:' . $fv('typo_nav_weight','600') . ';'
            . 'font-size:' . $fv('typo_nav_size','15') . 'px;'
            . 'color:' . $fc('typo_nav_color','#475569') . '!important;'
            . 'padding:.75rem 1rem!important;transition:var(--transition);position:relative;overflow:hidden;}';
        $parts[] = '@media (min-width: 992px) { '
            . '.navbar-nav > .nav-item > .nav-link::after { content: ""; position: absolute; bottom: 8px; left: 1rem; right: 1rem; height: 3px; background-color: var(--primary-color); transform: scaleX(0); transform-origin: center; transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); border-radius: 3px; } '
            . '.navbar-nav > .nav-item:hover > .nav-link::after, .navbar-nav > .nav-item.active > .nav-link::after { transform: scaleX(1); } '
            . '.navbar .navbar-nav .dropdown:hover > .dropdown-menu { display: block; opacity: 1; transform: translateY(0); pointer-events: auto; visibility: visible; } '
            . '.navbar .dropdown-menu { display:block; opacity: 0; transform: translateY(15px); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); margin-top: 0; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.08), 0 5px 15px rgba(0,0,0,0.03); border-radius: 12px; padding: 1rem 0; min-width: 220px; pointer-events: none; visibility: hidden; } '
            . '}';
        $parts[] = '.navbar-nav .nav-link:hover, .navbar-nav .nav-item.active > .nav-link { color: var(--primary-color) !important; }';
        $parts[] = '.dropdown-menu .dropdown-item { padding: 0.6rem 1.5rem; font-weight: 500; font-size: 0.95rem; color: var(--text-main); transition: var(--transition); position: relative; display: flex; align-items: center; }';
        $parts[] = '.dropdown-menu .dropdown-item:hover { background-color: rgba(0,0,0,0.02); color: var(--primary-color); padding-left: 1.8rem; }';
        $parts[] = '.dropdown-menu .dropdown-item::before { content: "\f105"; font-family: "FontAwesome"; position: absolute; left: 10px; opacity: 0; transform: translateX(-10px); transition: var(--transition); color: var(--primary-color); }';
        $parts[] = '.dropdown-menu .dropdown-item:hover::before { opacity: 1; transform: translateX(0); }';
        $parts[] = '.hero-section{padding:5rem 0;background:' . $overlayColor . ';color:' . $hdrFg . ';position:relative;z-index:1;}';
        $parts[] = '.hero-bg{min-height:500px;background-size:cover;background-position:center;overflow:hidden;position:relative;}';
        $parts[] = '.hero-overlay{position:absolute;inset:0;background:' . $overlayColor . 'f2;z-index:1;}';
        $parts[] = '.hero-section h1,.hero-section h2,.hero-section h3,.hero-section h4,.hero-section h5,.hero-section h6{color:' . $hdrFg . '!important;}';
        $parts[] = '.hero-section p,.hero-section .lead,.hero-section .hero-description{color:' . $hdrFg . ';opacity:.85;}';
        $parts[] = '.inner-hero h1,.inner-hero h2{color:' . $hdrFg . '!important;}';
        $parts[] = '.post-inner-card{padding:50px;}';
        $parts[] = '.post-meta-details{'
            . 'display:flex;gap:20px;margin-bottom:30px;'
            . 'font-size:' . $fv('typo_post_meta_size','14') . 'px;'
            . 'color:' . $fc('typo_post_meta_color','#64748b') . ';'
            . $ff('typo_post_meta_font')
            . 'font-weight:' . $fv('typo_post_meta_weight','400') . ';'
            . 'border-bottom:1px solid #f1f5f9;padding-bottom:20px;}';
        $parts[] = '.post-meta-details i{color:var(--primary-color);}';
        $parts[] = '.inner-hero-title{'
            . $ff('typo_single_title_font')
            . 'font-size:' . $fv('typo_single_title_size','36') . 'px!important;'
            . 'font-weight:' . $fv('typo_single_title_weight','800') . '!important;'
            . 'color:' . $fc('typo_single_title_color','#ffffff') . '!important;'
            . 'line-height:1.25;}';
        $parts[] = '.entry-content{'
            . $ff('typo_content_font')
            . 'font-size:' . $fv('typo_content_size','17') . 'px;'
            . 'font-weight:' . $fv('typo_content_weight','400') . ';'
            . 'line-height:1.8;color:' . $fc('typo_content_color','#334155') . ';}';
        $parts[] = '.entry-content p{margin-bottom:1.5rem;}';
        $parts[] = '.entry-content h2,.entry-content h3{margin-top:2.5rem;margin-bottom:1.25rem;}';
        $parts[] = '.blog-post{background:' . $cardBg . ';border:' . $borderW . 'px solid ' . $borderC . ';}';
        $parts[] = '.post-inner-card{padding:40px;border-radius:12px;}';
        $parts[] = '.share-bar{display:flex;align-items:center;flex-wrap:wrap;gap:10px;}';
        $parts[] = '.share-btn{display:inline-flex;align-items:center;gap:10px;padding:10px 20px;border-radius:50px;font-size:.85rem;font-weight:700;text-decoration:none!important;transition:var(--transition);border:1px solid #eee;background:#fff;color:#475569;}';
        $parts[] = '.share-btn i{font-size:1.1rem;}';
        $parts[] = '.share-btn:hover{transform:translateY(-3px);box-shadow:0 10px 15px -3px rgba(0,0,0,.1);color:#fff!important;}';
        $parts[] = '.share-fb:hover{background:#1877f2!important;border-color:#1877f2!important;}';
        $parts[] = '.share-tw:hover{background:#000!important;border-color:#000!important;}';
        $parts[] = '.share-wa:hover{background:#25d366!important;border-color:#25d366!important;}';
        $parts[] = '.hero-content{position:relative;z-index:2;}';
        $parts[] = '.btn-lg{padding:1rem 2.5rem;font-weight:700;letter-spacing:-.01em;transition:var(--transition);}';
        $parts[] = '.btn-primary{background:var(--primary-color);border:none;box-shadow:0 10px 20px -10px var(--primary-color);}';
        $parts[] = '.btn-primary:hover{transform:translateY(-2px);box-shadow:0 15px 30px -10px var(--primary-color);}';
        $parts[] = '#blog{background-color:var(--bg-body)!important;}';
        $parts[] = '.panel,.card,article.blog-post{background:var(--card-bg)!important;border:' . $borderW . 'px solid ' . $brdSel . '!important;border-radius:0!important;box-shadow:var(--card-shadow)!important;transition:var(--transition);overflow:hidden;margin-bottom:2rem;}';
        $parts[] = '.card-header,.panel-heading{background:rgba(0,0,0,.02)!important;border-bottom:1px solid rgba(0,0,0,.06)!important;padding:1.25rem 1.5rem!important;}';
        $parts[] = '.card-body,.panel-body{background:var(--card-bg)!important;color:var(--text-main)!important;}';
        $parts[] = '.card-title,.panel-title{color:var(--text-heading)!important;font-weight:700;}';
        $parts[] = '.panel:hover,.card:hover{transform:translateY(-5px);box-shadow:var(--card-shadow-hover)!important;}';
        $parts[] = '.category-header h2{font-size:2rem;letter-spacing:-.03em;margin-bottom:1.5rem;}';
        $parts[] = '.post-title{font-size:' . $fv('content_title_size','24') . 'px;line-height:1.3;letter-spacing:-.02em;margin-top:1rem;}';
        $parts[] = '.card a,.panel a{color:var(--text-heading);}';
        $parts[] = '.card a:hover,.panel a:hover{color:var(--primary-color)!important;}';
        $parts[] = '.widget-box{background:var(--card-bg);border-radius:var(--card-radius);box-shadow:var(--card-shadow);overflow:hidden;transition:var(--transition);border:1px solid rgba(0,0,0,.03);}';
        $parts[] = '.widget-box:hover{transform:translateY(-5px);box-shadow:var(--card-shadow-hover);}';
        $parts[] = '.widget-header{background:' . $sbarBg . '!important;min-height:55px;padding:0 1.25rem;border-bottom:1px solid rgba(0,0,0,.06);display:flex;align-items:center;margin:0;}';
        $parts[] = '.widget-title{color:' . $sbarFg . '!important;font-weight:700;margin:0!important;font-size:1.05rem;line-height:1.5;display:block;width:100%;}';
        $parts[] = '.widget-body{padding:1.25rem;background:' . $sbarBodyBg . ';color:' . $sbarBodyFg . ';}';
        $parts[] = '.widget-body a{color:inherit;text-decoration:none;transition:var(--transition);}';
        $parts[] = '.widget-body a:hover{color:var(--primary-color);}';
        $parts[] = '.sidebar-cards .card-header{background:' . $sbarBg . '!important;min-height:55px;padding:0 1.25rem!important;border-bottom:1px solid rgba(0,0,0,.05)!important;display:flex;align-items:center;margin:0;}';
        $parts[] = '.sidebar-cards .card-title{font-size:.95rem;text-transform:uppercase;letter-spacing:.08em;color:' . $sbarFg . '!important;margin:0!important;line-height:1.5;}';
        $parts[] = '.tag-item{background:#f1f5f9;color:#475569!important;padding:.6rem 1.2rem;font-size:.85rem;font-weight:600;display:inline-block;margin:0 .5rem .5rem 0;transition:var(--transition);}';
        $parts[] = '.tag-item:hover{background:var(--primary-color);color:#fff!important;transform:scale(1.05);}';
        $parts[] = 'footer{background-color:' . $ftBg . ';color:' . $ftFg . ';padding:8rem 0 0;margin-top:8rem;position:relative;border-top:4px solid var(--primary-color);}';
        $parts[] = 'footer .footer-widget-title{font-size:1.25rem;margin-bottom:2rem;position:relative;padding-bottom:1rem;}';
        $parts[] = 'footer .footer-widget-title::after{content:"";position:absolute;left:0;bottom:0;width:40px;height:3px;background:var(--primary-color);}';
        $parts[] = '.footer-social-box .social-link-item{width:45px;height:45px;display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,.05);color:#fff;margin-right:10px;transition:var(--transition);border:1px solid rgba(255,255,255,.1);border-radius:50%;}';
        $parts[] = '.footer-social-box .social-link-item:hover{background:var(--primary-color);transform:translateY(-5px);border-color:var(--primary-color);}';
        $parts[] = '.footer-links{list-style:none;padding:0;}';
        $parts[] = '.footer-links li{margin-bottom:12px;}';
        $parts[] = '.footer-links a{color:' . $ftLink . ';font-size:.95rem;}';
        $parts[] = '.footer-links a:hover{color:#fff;padding-left:8px;}';
        $parts[] = 'footer a{color:' . $ftLink . ';transition:var(--transition);}';
        $parts[] = 'footer a:hover{color:#fff;}';
        $parts[] = '.footer-bottom{padding:30px 0;border-top:1px solid rgba(255,255,255,.05);margin-top:60px;font-size:.9rem;color:rgba(255,255,255,.4);}';
        // Blockquote
        $parts[] = '.entry-content blockquote,blockquote{'
            . $ff('typo_blockquote_font')
            . 'font-size:' . $fv('typo_blockquote_size','18') . 'px;'
            . 'font-weight:' . $fv('typo_blockquote_weight','400') . ';'
            . 'color:' . $fc('typo_blockquote_color','#475569') . ';'
            . 'font-style:italic;border-left:4px solid var(--primary-color);'
            . 'padding:1rem 1.5rem;margin:1.5rem 0;background:rgba(0,0,0,.02);border-radius:0 8px 8px 0;}';
        // Breadcrumb
        $parts[] = '.breadcrumb-wrapper{background:rgba(0,0,0,.2)!important;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.1)!important;padding:.4rem 1.5rem!important;border-radius:50px;display:inline-flex;align-items:center;}';
        $parts[] = '.breadcrumb-wrapper .breadcrumb{margin:0!important;padding:0!important;background:transparent!important;}';
        $parts[] = '.breadcrumb-wrapper .breadcrumb-item,.breadcrumb-wrapper .breadcrumb-item a,.breadcrumb-wrapper .breadcrumb-item.active{'
            . $ff('typo_breadcrumb_font')
            . 'color:' . $brdColor . '!important;'
            . 'font-size:' . $fv('typo_breadcrumb_size','13') . 'px;'
            . 'font-weight:' . $fv('typo_breadcrumb_weight','600') . ';'
            . 'opacity:.9;line-height:1.5;}';
        $parts[] = '.breadcrumb-wrapper .breadcrumb-item+.breadcrumb-item::before{color:' . $brdColor . '!important;opacity:.5;}';
        // Comments
        $parts[] = '.comments-container h3{'
            . $ff('typo_comment_title_font')
            . 'font-size:' . $fv('typo_comment_title_size','22') . 'px;'
            . 'font-weight:' . $fv('typo_comment_title_weight','700') . ';'
            . 'color:' . $fc('typo_comment_title_color','#0f172a') . ';}';
        $parts[] = '.comments-container .comment-body,.comments-container .comment-text,.comment-list-wrapper p,.comment-list-wrapper .comment-content{'
            . $ff('typo_comment_body_font')
            . 'font-size:' . $fv('typo_comment_body_size','15') . 'px;'
            . 'font-weight:' . $fv('typo_comment_body_weight','400') . ';'
            . 'color:' . $fc('typo_comment_body_color','#334155') . ';}';
        // Magazine panels
        $parts[] = self::generatePanelCSS($opt);
        // Misc
        $parts[] = '.btn-glass{background:rgba(255,255,255,.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2);font-weight:600;}';
        $parts[] = '.btn-glass:hover{background:rgba(255,255,255,.2);transform:translateY(-2px);}';
        $parts[] = '.search-form .form-control:focus{width:250px!important;background:#fff!important;box-shadow:0 10px 20px -10px rgba(0,0,0,.1);}';
        $parts[] = '.search-form .form-control{transition:var(--transition);}';
        $parts[] = '.feat-card-premium{transition:var(--transition);}';
        $parts[] = '.feat-card-premium:hover{transform:translateY(-8px);box-shadow:0 30px 60px -12px rgba(0,0,0,.25)!important;}';
        $parts[] = '.feat-card-img-container img{transition:all .7s cubic-bezier(.16,1,.3,1);}';
        $parts[] = '.feat-card-premium:hover .feat-card-img-container img{transform:scale(1.1);}';
        $parts[] = '.feat-card-overlay{background:linear-gradient(to top,rgba(0,0,0,.95) 0%,rgba(0,0,0,.6) 40%,rgba(0,0,0,0) 100%);transition:var(--transition);}';
        $parts[] = '.feat-card-premium:hover .feat-card-overlay{background:linear-gradient(to top,rgba(0,0,0,1) 0%,rgba(0,0,0,.7) 50%,rgba(0,0,0,.1) 100%);}';
        $parts[] = '.text-shadow{text-shadow:0 2px 10px rgba(0,0,0,.5);}';
        $parts[] = '.extra-small{font-size:.75rem;}';
        $parts[] = '.transition-base{transition:var(--transition);}';
        $parts[] = '.feat-card-premium:hover .feat-card-footer{opacity:1!important;transform:translateY(-10px);}';
        $parts[] = '.feat-card-footer{transform:translateY(0);}';
        $parts[] = '.w-fit-content{width:fit-content;}';
        $parts[] = '@media(max-width:991px){.feat-card-img-container{min-height:300px!important;}}';
        if ($extraCSS) { $parts[] = $extraCSS; }

        $css = '<style>' . implode("\n", $parts) . '</style>';

        if ($minify && class_exists('Site') && method_exists('Site', 'minifyCSS')) {
            return $assets . Site::minifyCSS($css);
        }
        return $assets . $css;
    }


    /**
     * Generate magazine panel CSS rules (used internally in generateFrontendCSS).
     */
    private static function generatePanelCSS(array $opt): string
    {
        $css = '';
        $panelNames = ['one','two','three','four','five'];
        for ($i = 1; $i <= 5; $i++) {
            $color     = $opt["panel_{$i}_color"]      ?? '';
            $fontColor = $opt["panel_{$i}_font_color"] ?? '';
            $bg        = $opt["panel_{$i}_bg"]          ?? '';
            $textColor = $opt["panel_{$i}_text_color"]  ?? '';
            $fontFam   = $opt["panel_{$i}_font_family"] ?? 'inherit';
            $fontSize  = $opt["panel_{$i}_font_size"]   ?? '1';
            $panelClass = '.panel.panel-' . $panelNames[$i - 1];

            if ($i === 4) {
                // Panel 4 uses a different selector pattern
                if ($color) {
                    $css .= ".panel-four-wrapper h3 { color:{$color}!important; }";
                    $css .= ".panel-four-wrapper h3>span { background-color:{$color}!important; }";
                }
            } else {
                if ($color) {
                    $css .= "{$panelClass} { border-top:6px solid {$color}!important; }";
                    $css .= "{$panelClass} .card-header,{$panelClass} .panel-heading { background:{$color}26!important; border-top:none!important; }";
                }
                if ($fontColor) {
                    $css .= "{$panelClass} .panel-title,{$panelClass} .card-title { color:{$fontColor}!important; }";
                }
            }
            if ($bg) {
                $css .= "{$panelClass} .panel-body,{$panelClass} .card-body { background-color:{$bg}!important; }";
            }
            if ($textColor) {
                $css .= "{$panelClass} .panel-body,{$panelClass} .card-body,{$panelClass} p,{$panelClass} .card-text { color:{$textColor}!important; }";
            }
            if ($fontFam && $fontFam !== 'inherit') {
                $css .= "{$panelClass} { font-family:{$fontFam}; }";
            }
            if ($fontSize && $fontSize != '1') {
                $css .= "{$panelClass} { font-size:{$fontSize}rem; }";
            }
        }
        return $css;
    }


    // ─────────────────────────────────────────────────────────
    // PUBLIC: RENDER FULL PANEL
    // ─────────────────────────────────────────────────────────

    /**
     * Render the full options panel.
     *
     * @param array $schema  Array of tab definitions. Each tab:
     *   [
     *     'id'       => 'tab-hero',
     *     'label'    => 'Hero / Header',
     *     'icon'     => 'fa fa-image',
     *     'group'    => 'General',          // sidebar group label
     *     'active'   => true,               // (optional) default active tab
     *     'type'     => 'standard',         // 'standard' | 'presets' | 'typography' | 'panels'
     *     'title'    => 'Hero & Header',    // panel heading
     *     'subtitle' => 'Configure...',     // panel subheading
     *     'sections' => [                   // (standard type only) array of sections
     *       [
     *         'title'  => 'Section Title',  // (optional) sub-section heading
     *         'subtitle' => '...',          // (optional)
     *         'cards'  => [                 // array of cards
     *           [
     *             'title'  => 'Card Title',
     *             'cols'   => 2,            // grid columns: 1|2|3
     *             'fields' => [ ... ]       // array of field definitions
     *           ]
     *         ]
     *       ]
     *     ],
     *     'raw' => '<div>...</div>',        // (optional) raw HTML appended to panel
     *   ]
     *
     * Field definition formats:
     *   ['type'=>'text',     'name'=>'field_name', 'label'=>'...', 'placeholder'=>'...', 'hint'=>'...']
     *   ['type'=>'textarea', 'name'=>'field_name', 'label'=>'...', 'hint'=>'...']
     *   ['type'=>'color',    'name'=>'field_name', 'label'=>'...', 'hint'=>'...']
     *   ['type'=>'number',   'name'=>'field_name', 'label'=>'...', 'min'=>0, 'max'=>10, 'hint'=>'...']
     *   ['type'=>'range',    'name'=>'field_name', 'label'=>'...', 'min'=>10, 'max'=>72, 'step'=>1, 'unit'=>'px', 'id'=>'...']
     *   ['type'=>'select',   'name'=>'field_name', 'label'=>'...', 'options'=>['value'=>'Label',...], 'id'=>'...']
     *   ['type'=>'toggle',   'name'=>'field_name', 'label'=>'...', 'yes'=>'Yes', 'no'=>'No']
     *   ['type'=>'typo_row', 'prefix'=>'typo_body', 'label'=>'Body Text']
     *   ['type'=>'raw',      'html'=>'<div>...</div>']
     *   ['type'=>'divider']
     *   ['type'=>'heading',  'text'=>'Sub-heading']
     */
    public function render(array $schema): void
    {
        $this->renderCSS();
        echo '<form method="post" id="gneexForm">';
        echo '<div class="gx-wrap">';
        $this->renderTopbar();
        echo '<div class="gx-layout">';
        $this->renderSidebar($schema);
        echo '<div class="gx-main">';
        $this->renderAllPanels($schema);
        echo '</div>'; // gx-main
        echo '</div>'; // gx-layout
        echo '</div>'; // gx-wrap
        echo '</form>';
        $this->renderJS();
    }

    // ─────────────────────────────────────────────────────────
    // TOPBAR
    // ─────────────────────────────────────────────────────────
    private function renderTopbar(): void
    {
        $name  = htmlspecialchars($this->brandName);
        $ver   = htmlspecialchars($this->brandVer);
        $key   = htmlspecialchars($this->saveKey);
        $color = htmlspecialchars($this->brandColor);
        $color2 = $this->shiftColor($this->brandColor, 30); // complementary gradient color

        // Logo box: icon takes priority over abbreviation
        $logoInner = $this->brandIcon
            ? '<i class="' . htmlspecialchars($this->brandIcon) . '"></i>'
            : htmlspecialchars($this->brandAbbr);

        $logoStyle = "background:linear-gradient(135deg,{$color},{$color2})";

        echo <<<HTML
    <div class="gx-topbar">
        <div class="brand">
            <div class="brand-logo" style="{$logoStyle}">{$logoInner}</div>
            <div>
                <div class="brand-name">{$name}</div>
            </div>
            <span class="brand-ver">{$ver}</span>
        </div>
        <button type="submit" name="{$key}" class="gx-btn-save">
            <i class="fa fa-save"></i> Save Changes
        </button>
    </div>
HTML;
    }

    /**
     * Shift a hex color by rotating its hue by $degrees (simple HSL shift).
     * Used to generate a 2-stop gradient from a single brand color.
     */
    private function shiftColor(string $hex, int $degrees = 30): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) return '#6366f1';
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $max = max($r,$g,$b); $min = min($r,$g,$b); $delta = $max - $min;
        $h = 0; $s = 0; $l = ($max + $min) / 2;
        if ($delta > 0) {
            $s = $delta / (1 - abs(2 * $l - 1));
            if ($max === $r) $h = 60 * fmod(($g - $b) / $delta, 6);
            elseif ($max === $g) $h = 60 * (($b - $r) / $delta + 2);
            else $h = 60 * (($r - $g) / $delta + 4);
        }
        $h = fmod($h + $degrees + 360, 360);
        // HSL back to RGB
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;
        if ($h < 60)      { $r2=$c; $g2=$x; $b2=0; }
        elseif ($h < 120) { $r2=$x; $g2=$c; $b2=0; }
        elseif ($h < 180) { $r2=0; $g2=$c; $b2=$x; }
        elseif ($h < 240) { $r2=0; $g2=$x; $b2=$c; }
        elseif ($h < 300) { $r2=$x; $g2=0; $b2=$c; }
        else              { $r2=$c; $g2=0; $b2=$x; }
        return sprintf('#%02x%02x%02x',
            round(($r2+$m)*255), round(($g2+$m)*255), round(($b2+$m)*255));
    }

    // ─────────────────────────────────────────────────────────
    // SIDEBAR
    // ─────────────────────────────────────────────────────────
    private function renderSidebar(array $schema): void
    {
        // Group tabs by 'group'
        $groups = [];
        foreach ($schema as $tab) {
            $group = $tab['group'] ?? 'General';
            $groups[$group][] = $tab;
        }

        echo '<div class="gx-sidebar">';
        $first = true;
        foreach ($groups as $groupLabel => $tabs) {
            echo '<div class="gx-nav-group">';
            echo '<span class="gx-nav-label">' . htmlspecialchars($groupLabel) . '</span>';
            foreach ($tabs as $tab) {
                $id     = htmlspecialchars($tab['id']);
                $label  = htmlspecialchars($tab['label']);
                $icon   = htmlspecialchars($tab['icon'] ?? 'fa fa-circle');
                $active = ($first || !empty($tab['active'])) ? ' active' : '';
                $first  = false;
                echo "<button type=\"button\" class=\"gx-nav-item{$active}\" data-tab=\"{$id}\"><i class=\"{$icon}\"></i> {$label}</button>";
            }
            echo '</div>';
        }
        echo '</div>';
    }

    // ─────────────────────────────────────────────────────────
    // PANELS
    // ─────────────────────────────────────────────────────────
    private function renderAllPanels(array $schema): void
    {
        $first = true;
        foreach ($schema as $tab) {
            $id     = htmlspecialchars($tab['id']);
            $active = ($first || !empty($tab['active'])) ? ' active' : '';
            $first  = false;

            echo "<div class=\"gx-panel{$active}\" id=\"{$id}\">";

            if (!empty($tab['title'])) {
                echo '<div class="gx-section-title">' . $tab['title'] . '</div>';
            }
            if (!empty($tab['subtitle'])) {
                echo '<div class="gx-section-sub">' . htmlspecialchars($tab['subtitle']) . '</div>';
            }

            $type = $tab['type'] ?? 'standard';
            switch ($type) {
                case 'presets':
                    $this->renderPresetsPanel();
                    break;
                case 'panels':
                    $this->renderMagazinePanelsPanel();
                    break;
                default:
                    $this->renderStandardPanel($tab);
                    break;
            }

            if (!empty($tab['raw'])) {
                echo $tab['raw'];
            }

            echo '</div>'; // gx-panel
        }
    }

    // ─────────────────────────────────────────────────────────
    // STANDARD PANEL: sections → cards → fields
    // ─────────────────────────────────────────────────────────
    private function renderStandardPanel(array $tab): void
    {
        foreach ($tab['sections'] ?? [] as $section) {
            if (!empty($section['title'])) {
                echo '<div class="gx-section-title" style="margin-top:1.5rem;font-size:1rem;border-top:1px solid #e2e8f0;padding-top:1.5rem;">'
                    . $section['title'] . '</div>';
            }
            if (!empty($section['subtitle'])) {
                echo '<div class="gx-section-sub">' . $section['subtitle'] . '</div>';
            }
            foreach ($section['cards'] ?? [] as $card) {
                $this->renderCard($card);
            }
        }
        // top-level cards (shorthand without sections)
        foreach ($tab['cards'] ?? [] as $card) {
            $this->renderCard($card);
        }
    }

    private function renderCard(array $card): void
    {
        // Shorthand: card is itself a typo_row definition
        if (isset($card['type']) && $card['type'] === 'typo_row') {
            echo $this->fieldTypoRow($card['label'] ?? '', $card['prefix'] ?? '');
            return;
        }

        // Shorthand: single raw HTML block
        if (isset($card['type']) && $card['type'] === 'raw') {
            echo $card['html'] ?? '';
            return;
        }

        $title = $card['title'] ?? '';
        $cols  = (int)($card['cols'] ?? 1);
        $grid  = $cols > 1 ? "gx-grid-{$cols}" : '';
        $wrap  = $cols > 1;

        // Check if all fields are typo_rows or raw — skip gx-card wrapper for them
        $fields = $card['fields'] ?? [];
        $allTypo = !empty($fields) && array_reduce($fields, fn($c, $f) => $c && in_array($f['type'] ?? '', ['typo_row', 'raw', 'divider', 'heading']), true);

        if ($allTypo && !$title) {
            foreach ($fields as $field) {
                $this->renderField($field, false);
            }
            return;
        }

        echo '<div class="gx-card">';
        if ($title) {
            echo '<div class="gx-card-title">' . htmlspecialchars($title) . '</div>';
        }
        if ($wrap) echo "<div class=\"{$grid}\">";
        foreach ($fields as $field) {
            $this->renderField($field, $wrap);
        }
        if ($wrap) echo '</div>';
        echo '</div>';
    }

    // ─────────────────────────────────────────────────────────
    // FIELD RENDERERS
    // ─────────────────────────────────────────────────────────
    private function renderField(array $field, bool $inGrid = false): void
    {
        $type = $field['type'] ?? 'text';

        // Special non-wrapped types
        if ($type === 'raw') {
            echo $field['html'] ?? '';
            return;
        }
        if ($type === 'divider') {
            echo '<hr style="border:none;border-top:1px solid #e2e8f0;margin:12px 0;">';
            return;
        }
        if ($type === 'heading') {
            echo '<h6 style="font-size:11px;font-weight:800;text-transform:uppercase;color:#94a3b8;margin:12px 0 8px;">'
                . htmlspecialchars($field['text'] ?? '') . '</h6>';
            return;
        }
        if ($type === 'typo_row') {
            echo $this->fieldTypoRow($field['label'] ?? '', $field['prefix'] ?? '');
            return;
        }

        // Standard field wrapper
        $wrapStyle = !$inGrid && !empty($field['style']) ? ' style="' . htmlspecialchars($field['style']) . '"' : '';
        echo "<div class=\"gx-field\"{$wrapStyle}>";

        if (!empty($field['label'])) {
            // Allow raw HTML labels (e.g. for social icons with colored badges)
            echo '<label>' . $field['label'] . '</label>';
        }

        switch ($type) {
            case 'text':    $this->fieldText($field);     break;
            case 'textarea':$this->fieldTextarea($field); break;
            case 'color':   $this->fieldColor($field);    break;
            case 'number':  $this->fieldNumber($field);   break;
            case 'range':   $this->fieldRange($field);    break;
            case 'select':  $this->fieldSelect($field);   break;
            case 'toggle':  $this->fieldToggle($field);   break;
        }

        if (!empty($field['hint'])) {
            echo '<span class="hint">' . htmlspecialchars($field['hint']) . '</span>';
        }

        echo '</div>'; // gx-field
    }

    private function fieldText(array $f): void
    {
        $name  = htmlspecialchars($f['name'] ?? '');
        $val   = htmlspecialchars($this->opt[$f['name'] ?? ''] ?? '');
        $ph    = htmlspecialchars($f['placeholder'] ?? '');
        $id    = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<input type=\"text\" name=\"{$name}\"{$id} class=\"gx-input\" value=\"{$val}\" placeholder=\"{$ph}\">";
    }

    private function fieldTextarea(array $f): void
    {
        $name  = htmlspecialchars($f['name'] ?? '');
        $val   = htmlspecialchars($this->opt[$f['name'] ?? ''] ?? '');
        $rows  = (int)($f['rows'] ?? 4);
        echo "<textarea name=\"{$name}\" class=\"gx-input\" rows=\"{$rows}\">{$val}</textarea>";
    }

    private function fieldColor(array $f): void
    {
        $name  = $f['name'] ?? '';
        $val   = $this->opt[$name] ?? '';
        $id    = $f['id'] ?? '';
        echo self::colorField($name, $val, $id);
    }

    private function fieldNumber(array $f): void
    {
        $name  = htmlspecialchars($f['name'] ?? '');
        $val   = htmlspecialchars($this->opt[$f['name'] ?? ''] ?? '0');
        $min   = $f['min'] ?? 0;
        $max   = $f['max'] ?? 100;
        $id    = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<input type=\"number\" name=\"{$name}\"{$id} class=\"gx-input\" min=\"{$min}\" max=\"{$max}\" value=\"{$val}\">";
    }

    private function fieldRange(array $f): void
    {
        $name  = htmlspecialchars($f['name'] ?? '');
        $val   = $this->opt[$f['name'] ?? ''] ?? ($f['default'] ?? 0);
        $min   = $f['min'] ?? 0;
        $max   = $f['max'] ?? 100;
        $step  = $f['step'] ?? 1;
        $unit  = htmlspecialchars($f['unit'] ?? '');
        $uid   = $f['id'] ?? ('range_val_' . $f['name'] ?? uniqid());
        $oninput = "document.getElementById('{$uid}').textContent=this.value+'{$unit}'";
        echo <<<HTML
<div class="gx-range-wrap">
    <input type="range" name="{$name}" id="{$name}_range" min="{$min}" max="{$max}" step="{$step}" value="{$val}" oninput="{$oninput}">
    <span class="gx-range-val"><span id="{$uid}">{$val}</span>{$unit}</span>
</div>
HTML;
    }

    private function fieldSelect(array $f): void
    {
        $name    = htmlspecialchars($f['name'] ?? '');
        $current = $this->opt[$f['name'] ?? ''] ?? '';
        $options = $f['options'] ?? [];
        $id      = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<select name=\"{$name}\"{$id} class=\"gx-input gx-select\">";
        foreach ($options as $val => $label) {
            $sel = ($current == $val) ? ' selected' : '';
            $ve  = htmlspecialchars($val);
            $le  = htmlspecialchars($label);
            echo "<option value=\"{$ve}\"{$sel}>{$le}</option>";
        }
        echo '</select>';
    }

    private function fieldToggle(array $f): void
    {
        $name    = htmlspecialchars($f['name'] ?? '');
        $current = $this->opt[$f['name'] ?? ''] ?? 'yes';
        $yes     = htmlspecialchars($f['yes'] ?? 'Yes');
        $no      = htmlspecialchars($f['no'] ?? 'No');
        $ysel    = ($current == 'yes') ? ' selected' : '';
        $nsel    = ($current == 'no')  ? ' selected' : '';
        echo <<<HTML
<select name="{$name}" class="gx-input gx-select">
    <option value="yes"{$ysel}>{$yes}</option>
    <option value="no"{$nsel}>{$no}</option>
</select>
HTML;
    }

    private function fieldTypoRow(string $label, string $prefix): string
    {
        $opt     = $this->opt;
        $fonts   = self::$FONTS;
        $weights = self::$WEIGHTS;
        $ff = $opt["{$prefix}_font"]   ?? 'inherit';
        $fs = $opt["{$prefix}_size"]   ?? '16';
        $fw = $opt["{$prefix}_weight"] ?? '400';
        $fc = $opt["{$prefix}_color"]  ?? '';

        ob_start();
        ?>
        <div class='gx-card'>
            <div class='gx-card-title'><?= htmlspecialchars($label) ?></div>
            <div style='display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;'>
                <div class='gx-field'>
                    <label>Font Family</label>
                    <select name='<?= $prefix ?>_font' class='gx-input gx-select'>
                        <?php foreach ($fonts as $val => $name): ?>
                        <option value="<?= htmlspecialchars($val) ?>" <?= ($ff === $val) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class='gx-field'>
                    <label>Font Weight</label>
                    <select name='<?= $prefix ?>_weight' class='gx-input gx-select'>
                        <?php foreach ($weights as $val => $name): ?>
                        <option value='<?= $val ?>' <?= ($fw == $val) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style='display:grid;grid-template-columns:1fr 1fr;gap:16px;'>
                <div class='gx-field'>
                    <label>Font Size (px)</label>
                    <div style='display:flex;align-items:center;gap:10px;'>
                        <input type='range' name='<?= $prefix ?>_size' min='10' max='72' step='1' value='<?= (int)$fs ?>' style='flex:1;' oninput='this.nextElementSibling.textContent=this.value+"px"'>
                        <span style='font-size:12px;font-weight:700;color:#3b82f6;width:40px;text-align:right;'><?= (int)$fs ?>px</span>
                    </div>
                </div>
                <div class='gx-field'>
                    <label>Color</label>
                    <?= self::colorField("{$prefix}_color", $fc) ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // ─────────────────────────────────────────────────────────
    // SPECIAL PANELS
    // ─────────────────────────────────────────────────────────
    private function renderPresetsPanel(): void
    {
        $o = $this->opt;
        $presets = $this->presets;
        $panel_palettes = $this->panel_palettes;
        ?>
        <div class="gx-card">
            <div class="gx-card-title">Theme Presets</div>
            <div class="gx-preset-grid">
                <?php foreach ($presets as $key => $p): ?>
                <button type="button" class="gx-preset-btn" onclick="applyPreset(<?= htmlspecialchars(json_encode($p)) ?>)">
                    <div class="gx-preset-dot" style="background:<?= $p['link_color'] ?>"></div>
                    <?= $p['emoji'] ?> <?= $p['name'] ?>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="gx-mini-bar mt-3" id="miniBar">
                <div class="gx-mini-bar-seg" style="background:<?= $o['link_color'] ?>"></div>
                <div class="gx-mini-bar-seg" style="background:<?= $o['body_background_color'] ?>; border: 1px solid #e2e8f0;"></div>
                <div class="gx-mini-bar-seg" style="background:<?= $o['content_title_color'] ?>"></div>
                <div class="gx-mini-bar-seg" style="background:<?= $o['background_color_header'] ?>"></div>
                <div class="gx-mini-bar-seg" style="background:<?= $o['background_color_footer'] ?>"></div>
            </div>
            <p class="hint mt-2" style="font-size:12px;color:#94a3b8;">↑ Preview of current colors: primary · body · heading · header · footer. Click "Save Changes" after applying a preset.</p>
        </div>

        <div class="gx-card">
            <div class="gx-card-title">Current Active Colors</div>
            <div class="gx-grid-3">
                <?php
                $displayColors = [
                    'link_color'              => 'Primary Color',
                    'body_background_color'   => 'Body Background',
                    'content_title_color'     => 'Heading Color',
                    'content_font_color_body' => 'Body Text',
                    'background_color_header' => 'Header BG',
                    'background_color_footer' => 'Footer BG',
                ];
                foreach ($displayColors as $field => $label): ?>
                <div style="display:flex;align-items:center;gap:10px;padding:10px;background:#f8fafc;border-radius:10px;">
                    <div style="width:28px;height:28px;border-radius:8px;background:<?= $o[$field] ?>;border:1px solid rgba(0,0,0,.1);flex-shrink:0;"></div>
                    <div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:600;"><?= $label ?></div>
                        <div style="font-size:12px;font-weight:700;color:#374151;font-family:monospace;"><?= $o[$field] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="gx-card">
            <div class="gx-card-title">Quick Panel Color Palette</div>
            <p style="font-size:13px;color:#64748b;margin:0 0 14px;">Click a palette to auto-assign colors to all 5 panels.</p>
            <?php foreach ($panel_palettes as $pi => $palette): ?>
            <div class="gx-palette" style="margin-bottom:12px;">
                <span style="font-size:11px;font-weight:700;color:#94a3b8;width:60px;display:flex;align-items:center;">Palette <?= $pi + 1 ?></span>
                <?php foreach ($palette as $ci => $color): ?>
                <div class="gx-palette-dot" style="background:<?= $color ?>" title="Panel <?= $ci + 1 ?>: <?= $color ?>" onclick="applyPanelPalette(<?= json_encode($palette) ?>)"></div>
                <?php endforeach; ?>
                <button type="button" style="font-size:11px;color:#3b82f6;background:none;border:none;cursor:pointer;font-weight:700;padding:0;" onclick="applyPanelPalette(<?= json_encode($palette) ?>)">Apply →</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function renderMagazinePanelsPanel(): void
    {
        $o = $this->opt;
        $fontOptions = [
            'inherit'                     => 'Inherit (Default)',
            '"Inter", sans-serif'         => 'Inter',
            '"Roboto", sans-serif'        => 'Roboto',
            '"Merriweather", serif'       => 'Merriweather',
            '"Playfair Display", serif'   => 'Playfair Display',
            '"JetBrains Mono", monospace' => 'Monospace',
        ];
        echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">';
        for ($i = 1; $i <= 5; $i++) {
            $pColor = $o["panel_{$i}_color"] ?: '#3b82f6';
            $pFont  = $o["panel_{$i}_font_color"] ?: '#ffffff';
            $pSize  = $o["panel_{$i}_font_size"] ?: '1';
            $pFF    = $o["panel_{$i}_font_family"] ?: 'inherit';
            ?>
            <div class="gx-panel-card">
                <div class="panel-num">Panel <?= $i ?></div>
                <div class="gx-panel-preview" id="preview-panel-<?= $i ?>" style="background:<?= $pColor ?>;color:<?= $pFont ?>">
                    Panel <?= $i ?> Preview
                </div>
                <div class="gx-field" style="margin-bottom:12px;">
                    <label style="font-size:11.5px;">Category</label>
                    <?= Categories::dropdown(['name' => "panel_{$i}", 'selected' => $o["panel_{$i}"] ?? '', 'class' => 'gx-input gx-select', 'style' => 'font-size:13px;padding:8px 12px;']) ?>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div class="gx-field" style="margin:0;">
                        <label style="font-size:11px;">Accent Color</label>
                        <?= self::colorField("panel_{$i}_color", $o["panel_{$i}_color"], "panel-color-{$i}") ?>
                    </div>
                    <div class="gx-field" style="margin:0;">
                        <label style="font-size:11px;">Title Color</label>
                        <?= self::colorField("panel_{$i}_font_color", $o["panel_{$i}_font_color"], "panel-font-{$i}") ?>
                    </div>
                </div>

                <div style="border-top:1px dashed #e2e8f0; margin:15px -20px; padding:15px 20px 0;">
                    <h6 style="font-size:11px; font-weight:800; text-transform:uppercase; color:#94a3b8; margin-bottom:12px;">Body / Content Styling</h6>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                        <div class="gx-field" style="margin:0;">
                            <label style="font-size:11px;">Background Color</label>
                            <?= self::colorField("panel_{$i}_bg", $o["panel_{$i}_bg"] ?? '', "panel-bg-{$i}") ?>
                        </div>
                        <div class="gx-field" style="margin:0;">
                            <label style="font-size:11px;">Body Text Color</label>
                            <?= self::colorField("panel_{$i}_text_color", $o["panel_{$i}_text_color"] ?? '', "panel-textcolor-{$i}") ?>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="gx-field" style="margin:0;">
                            <label style="font-size:11px;">Font Family</label>
                            <select name="panel_<?= $i ?>_font_family" class="gx-input gx-select" style="font-size:12px;padding:6px 10px;">
                                <?php foreach ($fontOptions as $val => $label): ?>
                                <option value="<?= htmlspecialchars($val) ?>" <?= ($pFF === $val) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="gx-field" style="margin:0;">
                            <label style="font-size:11px;">Font Size / Scale</label>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <input type="range" name="panel_<?= $i ?>_font_size" min="0.8" max="1.5" step="0.05" value="<?= $pSize ?>" style="flex:1;" oninput="this.nextElementSibling.textContent=this.value+'rem'">
                                <span style="font-size:11px; font-weight:700; color:#3b82f6; width:40px; text-align:right;"><?= $pSize ?>rem</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';
    }

    // ─────────────────────────────────────────────────────────
    // STATIC HELPERS
    // ─────────────────────────────────────────────────────────

    /**
     * Render a color picker field (swatch + native color input + hex text input).
     */
    public static function colorField(string $name, string $value, string $id = ''): string
    {
        $value   = htmlspecialchars($value ?: '');
        $idAttr  = $id ? 'id="' . htmlspecialchars($id) . '"' : '';
        $swatchBg = $value ?: '#3b82f6';
        return <<<HTML
<div class="gx-color-field" {$idAttr}>
    <div style="position:relative;flex-shrink:0;">
        <div class="gx-color-swatch" style="background:{$swatchBg}"></div>
        <input type="color" class="gx-color-native" value="{$swatchBg}">
    </div>
    <input type="text" name="{$name}" class="gx-input" value="{$value}" placeholder="#000000">
</div>
HTML;
    }

    // ─────────────────────────────────────────────────────────
    // CSS
    // ─────────────────────────────────────────────────────────
    private function renderCSS(): void
    {
        echo <<<'CSS'
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

.gx-wrap { font-family: 'Inter', sans-serif; background: #f8fafc; min-height: 100vh; }
.gx-topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; height: 64px; position: relative; z-index: 998; box-shadow: 0 1px 3px rgba(0,0,0,.05); }
.gx-topbar.is-sticky { position: fixed; right: 0; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
.gx-topbar .brand { display: flex; align-items: center; gap: 12px; }
.gx-topbar .brand-logo { width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #6366f1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 900; font-size: 14px; }
.gx-topbar .brand-name { font-weight: 800; font-size: 17px; color: #0f172a; }
.gx-topbar .brand-ver { font-size: 11px; background: #eff6ff; color: #3b82f6; padding: 3px 8px; border-radius: 20px; font-weight: 700; }
.gx-btn-save { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all .3s; box-shadow: 0 4px 15px rgba(59,130,246,.4); display: flex; align-items: center; gap: 8px; }
.gx-btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(59,130,246,.5); }

.gx-layout { display: grid; grid-template-columns: 240px 1fr; min-height: calc(100vh - 64px); }
.gx-sidebar { background: #fff; border-right: 1px solid #e2e8f0; padding: 24px 0; position: sticky; top: 64px; height: calc(100vh - 64px); overflow-y: auto; }
.gx-nav-group { padding: 0 16px; margin-bottom: 8px; }
.gx-nav-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; padding: 0 8px; margin-bottom: 6px; margin-top: 16px; display: block; }
.gx-nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; color: #475569; font-weight: 600; font-size: 13.5px; cursor: pointer; transition: all .2s; border: none; background: none; width: 100%; text-align: left; }
.gx-nav-item i { width: 20px; text-align: center; font-size: 14px; opacity: .7; }
.gx-nav-item:hover { background: #f1f5f9; color: #1e293b; }
.gx-nav-item.active { background: #eff6ff; color: #3b82f6; }
.gx-nav-item.active i { opacity: 1; }

.gx-main { padding: 32px; max-width: 900px; }
.gx-panel { display: none; animation: gxFadeIn .3s ease; }
.gx-panel.active { display: block; }
@keyframes gxFadeIn { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform:translateY(0); } }
@keyframes slideDown { from { opacity:0; transform: translateY(-20px); } to { opacity:1; transform:translateY(0); } }

.gx-section-title { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
.gx-section-sub { font-size: 13.5px; color: #64748b; margin-bottom: 28px; }
.gx-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
.gx-card-title { font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 18px; }
.gx-field { margin-bottom: 18px; }
.gx-field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
.gx-input { width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 13.5px; color: #1e293b; background: #f8fafc; transition: all .2s; font-family: inherit; box-sizing: border-box; }
.gx-input:focus { outline: none; border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.gx-select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%2394a3b8' d='M0 0l6 8 6-8z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; cursor: pointer; }
.gx-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.gx-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
.gx-range-wrap { display: flex; align-items: center; gap: 12px; }
.gx-range-wrap input[type=range] { flex: 1; accent-color: #3b82f6; }
.gx-range-val { font-size: 13px; font-weight: 700; color: #3b82f6; min-width: 52px; text-align: right; }
.hint { display: block; font-size: 12px; color: #94a3b8; margin-top: 5px; }

/* Color field */
.gx-color-field { display: flex; align-items: center; gap: 10px; }
.gx-color-swatch { width: 36px; height: 36px; border-radius: 8px; border: 2px solid #e2e8f0; cursor: pointer; transition: all .2s; }
.gx-color-swatch:hover { transform: scale(1.1); }
.gx-color-native { position: absolute; opacity: 0; width: 36px; height: 36px; top: 0; left: 0; cursor: pointer; }

/* Preset & palette */
.gx-preset-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.gx-preset-btn { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 10px; background: #fff; cursor: pointer; font-size: 13px; font-weight: 600; color: #475569; transition: all .2s; text-align: left; }
.gx-preset-btn:hover { border-color: #3b82f6; background: #eff6ff; color: #1e40af; }
.gx-preset-dot { width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0; }
.gx-mini-bar { display: flex; height: 8px; border-radius: 4px; overflow: hidden; }
.gx-mini-bar-seg { flex: 1; }
.gx-palette { display: flex; align-items: center; gap: 8px; }
.gx-palette-dot { width: 28px; height: 28px; border-radius: 50%; cursor: pointer; transition: transform .2s; flex-shrink: 0; border: 2px solid rgba(255,255,255,.4); }
.gx-palette-dot:hover { transform: scale(1.2); }

/* Magazine panel card */
.gx-panel-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; }
.gx-panel-preview { padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; margin-bottom: 14px; }
.panel-num { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; margin-bottom: 10px; }
</style>
CSS;
    }

    // ─────────────────────────────────────────────────────────
    // JAVASCRIPT
    // ─────────────────────────────────────────────────────────
    private function renderJS(): void
    {
        echo <<<'JS'
<script>
$(function() {
    // Tab navigation
    $('.gx-nav-item').on('click', function() {
        var tab = $(this).data('tab');
        $('.gx-nav-item').removeClass('active');
        $(this).addClass('active');
        $('.gx-panel').removeClass('active');
        $('#' + tab).addClass('active');
        localStorage.setItem('gneex_tab_v2', tab);
    });
    var saved = localStorage.getItem('gneex_tab_v2');
    if (saved && $('#' + saved).length) {
        $('[data-tab="' + saved + '"]').trigger('click');
    }

    // Front layout switcher
    function toggleFrontLayout() {
        var v = $('#frontpageSelector').val();
        if (v === 'fullwidth') { $('#fullwidth-opt').fadeIn(); }
        else { $('#fullwidth-opt').hide(); }
    }
    $('#frontpageSelector').on('change', toggleFrontLayout);
    toggleFrontLayout();

    // Color swatches
    function initColorSwatches() {
        $('.gx-color-swatch').each(function() {
            var swatch = $(this);
            var native = swatch.siblings('.gx-color-native');
            var input  = swatch.parent().siblings('.gx-input');
            var val = input.val() || '#3b82f6';
            swatch.css('background', val);
            native.val(val);

            swatch.on('click', function(e) { e.preventDefault(); native[0].click(); });
            native.on('input change', function() {
                var c = this.value;
                swatch.css('background', c);
                input.val(c).trigger('gx:colorchange');
            });
            input.on('input', function() {
                var v = this.value.trim();
                if (/^#[0-9a-fA-F]{3,6}$/.test(v)) { swatch.css('background', v); native.val(v); }
            });
        });

        // Panel live preview
        for (var i = 1; i <= 5; i++) {
            (function(idx) {
                $('[name="panel_' + idx + '_color"]').on('input change', function() {
                    $('#preview-panel-' + idx).css('background', this.value || '#3b82f6');
                });
                $('[name="panel_' + idx + '_font_color"]').on('input change', function() {
                    $('#preview-panel-' + idx).css('color', this.value || '#ffffff');
                });
            })(i);
        }
    }
    initColorSwatches();
});

// Color field helper
function setColorField(name, value) {
    var input = $('[name="' + name + '"]');
    if (!input.length) return;
    input.val(value);
    var field = input.closest('.gx-color-field');
    field.find('.gx-color-swatch').css('background', value);
    field.find('.gx-color-native').val(value);
    input.trigger('input');
}

// Apply full preset
function applyPreset(p) {
    var map = {
        'link_color': 'link_color', 'link_color_hover': 'link_color_hover',
        'body_background_color': 'body_background_color', 'content_title_color': 'content_title_color',
        'background_color_header': 'background_color_header', 'background_color_footer': 'background_color_footer',
        'content_font_color_body': 'content_font_color_body'
    };
    for (var k in map) { if (p[k]) setColorField(map[k], p[k]); }
    updateMiniBar();
    showToast('Preset "' + p.name + '" applied! Click Save to keep.', '#3b82f6');
}

function applyPanelPalette(colors) {
    for (var i = 0; i < Math.min(colors.length, 5); i++) {
        setColorField('panel_' + (i+1) + '_color', colors[i]);
        setColorField('panel_' + (i+1) + '_font_color', '#ffffff');
        $('#preview-panel-' + (i+1)).css('background', colors[i]).css('color', '#ffffff');
    }
    showToast('Panel colors applied!', '#10b981');
}

function updateMiniBar() {
    var fields = ['link_color','body_background_color','content_title_color','background_color_header','background_color_footer'];
    $('#miniBar .gx-mini-bar-seg').each(function(i) {
        var v = $('[name="' + fields[i] + '"]').val();
        if (v) $(this).css('background', v);
    });
}

function showToast(msg, color) {
    var t = $('<div>').text(msg).css({
        position:'fixed',top:'20px',right:'20px',zIndex:9999,
        background:color,color:'#fff',padding:'12px 20px',
        borderRadius:'10px',fontWeight:700,fontSize:'14px',
        boxShadow:'0 8px 24px rgba(0,0,0,.2)',fontFamily:'Inter,sans-serif'
    });
    $('body').append(t);
    setTimeout(()=>t.remove(), 2500);
}
</script>

<script>
// Sticky-on-scroll for gx-topbar
(function() {
    function setup() {
        var gxTopbar       = document.querySelector('.gx-topbar');
        var gxWrap         = document.querySelector('.gx-wrap');
        var gxInnerSidebar = document.querySelector('.gx-sidebar');
        var adminNavbar    = document.querySelector('.top-navbar');
        var adminSidebar   = document.getElementById('sidebar');

        if (!gxTopbar || !gxWrap) return;

        var spacer = document.createElement('div');
        spacer.id = 'gx-topbar-spacer';
        spacer.style.cssText = 'height:0;flex-shrink:0;pointer-events:none;';
        gxWrap.insertBefore(spacer, gxTopbar);

        var naturalOffsetTop = 0;

        function getAdminNavBottom() { return adminNavbar ? adminNavbar.getBoundingClientRect().bottom : 0; }
        function getAdminLeft()      { return adminSidebar ? adminSidebar.getBoundingClientRect().right : 0; }

        function applySticky(sticky) {
            if (sticky) {
                var topPx = getAdminNavBottom(), leftPx = getAdminLeft();
                gxTopbar.classList.add('is-sticky');
                gxTopbar.style.top = topPx + 'px';
                gxTopbar.style.left = leftPx + 'px';
                spacer.style.height = '64px';
                if (gxInnerSidebar) {
                    gxInnerSidebar.style.top    = (topPx + 64) + 'px';
                    gxInnerSidebar.style.height = 'calc(100vh - ' + (topPx + 64) + 'px)';
                }
            } else {
                gxTopbar.classList.remove('is-sticky');
                gxTopbar.style.top = gxTopbar.style.left = '';
                spacer.style.height = '0';
                if (gxInnerSidebar) {
                    gxInnerSidebar.style.top    = '64px';
                    gxInnerSidebar.style.height = 'calc(100vh - 64px)';
                }
            }
        }

        function onScroll() {
            var shouldStick = (window.scrollY + getAdminNavBottom()) >= naturalOffsetTop;
            applySticky(shouldStick);
        }

        function init() {
            gxTopbar.classList.remove('is-sticky');
            gxTopbar.style.top = gxTopbar.style.left = '';
            spacer.style.height = '0';
            naturalOffsetTop = gxTopbar.getBoundingClientRect().top + window.scrollY;
            onScroll();
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', init);
        var st = document.getElementById('sidebarToggle');
        if (st) st.addEventListener('click', function() { setTimeout(init, 350); });
        init();
    }

    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', setup); }
    else { setup(); }
})();
</script>
JS;
    }
}
