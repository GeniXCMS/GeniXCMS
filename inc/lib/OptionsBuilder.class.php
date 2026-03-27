<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * OptionsBuilder Class
 *
 * A self-contained, reusable Options Panel Builder for themes and modules.
 * This class provides a fluent interface to create professional, tabbed
 * administrator settings panels with standard UI components.
 *
 * Built-in components:
 * - Color Pickers
 * - Range Sliders
 * - Typography Groups (Font, Size, Weight, Color)
 * - Toggles & Selects
 * - Grid-based layouts
 * - Preset Color Schemes
 * - Magazine Panel Builders
 * - Frontend CSS Generator
 *
 * @since 1.1.0
 */
class OptionsBuilder
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
        '"Material Symbols Outlined", sans-serif' => 'Material Icons',
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
        $this->brandName      = $config['brandName']  ?? 'GeniXCMS Theme';
        $this->brandVer       = $config['brandVer']   ?? 'v2.1';
        $this->brandAbbr      = $config['brandAbbr']  ?? 'GX';
        $this->brandIcon      = $config['brandIcon']  ?? '';
        $this->brandColor     = $config['brandColor'] ?? '#3b82f6';
        $this->saveKey        = $config['saveKey']    ?? 'theme_options_update';
    }

    /**
     * Generate the full frontend <link>/<style> output driven by saved options.
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
            'typo_hero_title_font', 'typo_hero_text_font',
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
            . ($themeUrl ? '<link href="' . $themeUrl . 'css/style.css" rel="stylesheet">' . "\n" : '');

        // ── CSS Variables ──────────────────────────────────────────────────
        $root = ":root {\n"
            . "  --primary-color: " . ($opt['link_color'] ?? '#3b82f6') . ";\n"
            . "  --primary-hover: " . ($opt['link_color_hover'] ?? '#2563eb') . ";\n"
            . "  --bg-body: " . ($opt['body_background_color'] ?? '#f8fafc') . ";\n"
            . "  --container-width: " . ($opt['container_width'] ?? '1280') . "px;\n"
            . "  --bg-card: " . ($opt['content_background_color_body'] ?? '#ffffff') . ";\n"
            . "  --transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);\n"
            . "  --logo-height: " . ($opt['logo_height'] ?? '50px') . ";\n";

        // Panel accent colors
        for ($i=1; $i<=5; $i++) {
            $root .= "  --panel-{$i}-color: " . ($opt["panel_{$i}_color"] ?? '#3b82f6') . ";\n";
        }
        $root .= "}\n";

        // ── Helper: Typography Block ───────────────────────────────────────
        $typo = function($prefix, $selector) use ($opt): string {
            $f = $opt[$prefix . '_font']   ?? 'inherit';
            $s = $opt[$prefix . '_size']   ?? '';
            $w = $opt[$prefix . '_weight'] ?? '';
            $c = $opt[$prefix . '_color']  ?? '';
            $out = "{$selector} {\n";
            if ($f && $f !== 'inherit') $out .= "  font-family: {$f} !important;\n";
            if ($s) $out .= "  font-size: {$s}px !important;\n";
            if ($w) $out .= "  font-weight: {$w} !important;\n";
            if ($c) $out .= "  color: {$c} !important;\n";
            $out .= "}\n";
            return $out;
        };

        $parts = [$root];

        // Global Layout & Sizes
        $parts[] = "body { background-color: var(--bg-body) !important; color: " . ($opt['typo_body_color'] ?? '#334155') . "; }\n"
                 . ".container { max-width: var(--container-width) !important; }\n"
                 . ".navbar { background-color: " . ($opt['background_color_navbar'] ?? '#0f172a') . " !important; }\n"
                 . ".hero-bg { background-color: " . ($opt['background_color_header'] ?? '#0f172a') . " !important; background-image: url('" . ($opt['background_header'] ?? '') . "'); background-size: cover; background-position: center; }\n"
                 . ".card, .post-card { background-color: var(--bg-card) !important; border: " . ($opt['content_border_width'] ?? '1') . "px solid " . ($opt['content_border_color'] ?? '#e2e8f0') . " !important; }\n"
                 . "footer { background-color: " . ($opt['background_color_footer'] ?? '#0f172a') . " !important; color: " . ($opt['font_color_footer'] ?? '#94a3b8') . " !important; }\n"
                 . "footer a { color: " . ($opt['link_color_footer'] ?? '#3b82f6') . " !important; }\n";

        // Typography orchestration
        $parts[] = $typo('typo_body', 'body, p');
        $parts[] = $typo('typo_nav', '.nav-link, .navbar-brand');
        $parts[] = $typo('typo_h1', 'h1');
        $parts[] = $typo('typo_h2', 'h2');
        $parts[] = $typo('typo_h3', 'h3');
        $parts[] = $typo('typo_h4', 'h4');
        $parts[] = $typo('typo_post_title', '.post-title, .entry-title a');
        $parts[] = $typo('typo_meta', '.post-meta, .text-muted');
        $parts[] = $typo('typo_single_title', '.single-post-title, .hero-title, .inner-hero-title');
        $parts[] = $typo('typo_content', '.entry-content, .post-content');
        $parts[] = $typo('typo_blockquote', 'blockquote');
        $parts[] = $typo('typo_breadcrumb', '.breadcrumb-item, .breadcrumb-item a');
        $parts[] = $typo('typo_comment_title', '#comments-title');
        $parts[] = $typo('typo_comment_body', '.comment-body');
        $parts[] = $typo('typo_hero_title', '.hero-section h1, .inner-hero-title');
        $parts[] = $typo('typo_hero_text', '.hero-description');

        // ── Premium Theme Styles (GneeX Core) ──────────────────────────────
        $primary = ($opt['link_color'] ?? '#3b82f6');
        $primaryHover = ($opt['link_color_hover'] ?? '#2563eb');

        $parts[] = "
        /* Global Button Overrides */
        .btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; font-weight: 700 !important; border-radius: 12px !important; transition: var(--transition) !important; }
        .btn-primary:hover { background-color: var(--primary-hover) !important; border-color: var(--primary-hover) !important; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .btn-glass { background: rgba(255,255,255,0.1) !important; backdrop-filter: blur(10px) !important; border: 1px solid rgba(255,255,255,0.2) !important; color: #fff !important; font-weight: 700 !important; border-radius: 12px !important; }
        .btn-glass:hover { background: rgba(255,255,255,0.2) !important; color: #fff !important; }

        /* Hero Section Orchestration */
        .hero-section { position: relative; overflow: hidden; }
        .hero-bg { min-height: 650px; position: relative; display: flex; align-items: center; }
        .hero-bg-small { min-height: 350px !important; }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.8), rgba(0,0,0,0.3)); z-index: 1; }
        .hero-content { position: relative; z-index: 2; color: #fff; }
        .hero-media-wrapper { border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .hero-media-wrapper iframe, .hero-media-wrapper img { width: 100%; aspect-ratio: 16/9; border: none; }

        /* Single Post & Content Architecture */
        .post-header-img-single { border-radius: 24px; overflow: hidden; margin-bottom: -60px; position: relative; z-index: 1; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .post-inner-card { background: #fff; border-radius: 24px; padding: 85px 50px 50px; margin-top: 0; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .post-meta-details { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 40px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; }
        .post-meta-details span { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .post-meta-details i { color: var(--primary-color); }
        .entry-content { font-size: 1.1rem; line-height: 1.8; color: #334155; }
        .entry-content h2, .entry-content h3 { margin-top: 2rem; font-weight: 800; }
        .entry-content img { max-width: 100%; border-radius: 16px; margin: 2rem 0; }
        
        /* Social Share Bar */
        .share-bar { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
        .share-btn { padding: 10px 20px; border-radius: 12px; font-size: 13px; font-weight: 800; text-decoration: none !important; color: #fff !important; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; }
        .share-fb { background: #1877f2; }
        .share-tw { background: #000000; }
        .share-wa { background: #25d366; }
        .share-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); opacity: 0.9; }

        /* Related Posts & Comments Architecture */
        .related-posts h4, .comments-container h3 { font-size: 1.5rem; font-weight: 900; letter-spacing: -0.5px; position: relative; padding-left: 20px; }
        .related-posts h4::before, .comments-container h3::before { content:\"\"; position: absolute; left: 0; top: 10%; bottom: 10%; width: 5px; background: var(--primary-color); border-radius: 50px; }
        
        .comment-item { background: #fff; border-radius: 20px; padding: 30px; margin-bottom: 25px; border: 1px solid #f1f5f9; transition: var(--transition); display: flex; gap: 20px; align-items: flex-start; }
        .comment-item:hover { border-color: var(--primary-color); box-shadow: 0 15px 40px rgba(0,0,0,0.05); transform: translateY(-3px); }
        .comment-avatar img { width: 64px; height: 64px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .comment-content { flex: 1; }
        .comment-author { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 2px; display: block; }
        .comment-date { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: block; }
        .comment-body { color: #334155; line-height: 1.7; font-size: 1rem; }
        .comment-reply { margin-top: 15px; }
        .comment-reply-link { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--primary-color) !important; text-decoration: none !important; display: inline-flex; align-items: center; gap: 5px; transition: var(--transition); }
        .comment-reply-link:hover { color: var(--primary-hover) !important; letter-spacing: 0.5px; }
        
        /* Comment Form Modernization */
        #comment-form .form-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 8px; }
        #comment-form .form-control { border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 12px 18px; font-size: 1rem; transition: var(--transition); }
        #comment-form .form-control:focus { background: #fff; border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        #comment-form .btn-submit { background: var(--primary-color); color: #fff; border: none; padding: 12px 30px; border-radius: 50px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; transition: var(--transition); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); }
        #comment-form .btn-submit:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3); }

        /* Magazine Panels Orchestration */
        .panel { border-radius: 16px !important; overflow: hidden; border: none !important; }
        .panel-heading { padding: 1rem 1.5rem !important; border: none !important; }
        .panel-title { font-size: 1.1rem !important; text-transform: uppercase; letter-spacing: 0.05em; }
        .panel-body { padding: 1.5rem !important; }
        .horizontal-list img, .vertical-list img { border-radius: 12px; transition: var(--transition); }
        .horizontal-list img:hover, .vertical-list img:hover { transform: scale(1.02); }
        .panel-four-wrapper h3 { padding-left: 0; }

        /* Sidebar & Widget Architecture */
        .sidebar-cards .card, .widget-box {
            background-color: " . ($opt['sidebar_background_color_body'] ?? '#ffffff') . " !important;
            border: " . ($opt['sidebar_border_width'] ?? '1') . "px solid " . ($opt['sidebar_border_color'] ?? '#e2e8f0') . " !important;
            border-radius: 16px !important;
            margin-bottom: 3rem !important;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05) !important;
        }
        .sidebar-cards .card-header, .widget-header {
            background-color: " . ($opt['sidebar_background_color_header'] ?? '#ffffff') . " !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
            padding: 1.25rem 1.5rem !important;
        }
        .sidebar-cards .card-title, .widget-title {
            font-size: 0.9rem !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            color: " . ($opt['sidebar_font_color_header'] ?? '#0f172a') . " !important;
            margin: 0 !important;
        }
        .sidebar-cards .card-body, .widget-body {
            padding: 1.5rem !important;
            color: " . ($opt['sidebar_font_color_body'] ?? 'inherit') . " !important;
        }
        .widget-body a { color: var(--primary-color); text-decoration: none; font-weight: 700; transition: var(--transition); display: inline-block; margin-bottom: 8px; }
        .widget-body a:hover { color: var(--primary-hover); transform: translateX(5px); }
        .tag-cloud-wrapper .tag-link { background: #fff; color: #64748b; padding: 6px 16px; border-radius: 8px; font-size: 11px !important; text-decoration: none; font-weight: 800; transition: var(--transition); border: 1px solid #f1f5f9; display: inline-block; margin: 0 4px 8px 0; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
        .tag-cloud-wrapper .tag-link:hover { background: var(--primary-color); color: #fff; border-color: var(--primary-color); transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        /* Footer Architecture */
        footer { padding: 80px 0 0 !important; border-top: 5px solid var(--primary-color); }
        .footer-logo { font-size: 24px; font-weight: 900; }
        .footer-widget-title { font-size: 1.2rem !important; margin-bottom: 1.5rem !important; position: relative; padding-bottom: 10px; }
        .footer-widget-title::after { content:\"\"; position: absolute; left: 0; bottom: 0; width: 40px; height: 3px; background: var(--primary-color); }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li a { color: rgba(255,255,255,0.7) !important; text-decoration: none; display: block; padding: 4px 0; transition: var(--transition); }
        .footer-links li a:hover { color: #fff !important; padding-left: 5px; }
        .social-link-item { width: 40px; height: 40px; background: rgba(255,255,255,0.1); display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; color: #fff !important; margin-right: 10px; transition: var(--transition); }
        .social-link-item:hover { background: var(--primary-color); transform: translateY(-3px); }
        .footer-bottom { margin-top: 60px; padding: 30px 0; border-top: 1px solid rgba(255,255,255,0.08); font-size: 13px; color: rgba(255,255,255,0.5); }

        /* Featured & Blog Cards */
        .feat-card-premium { transition: var(--transition); }
        .feat-card-premium:hover img { transform: scale(1.05); }
        .feat-card-overlay { background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); }
        .text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .transition-base { transition: var(--transition); }
        .rounded-4 { border-radius: 1rem !important; }

        /* Navigation Overwrites */
        .navbar-nav .nav-link { font-weight: 600; padding: 0.5rem 1rem !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .navbar-brand img { height: var(--logo-height) !important; width: auto; }

        /* Pagination & Misc */
        .pagination .page-link { border-radius: 8px; margin: 0 3px; color: var(--primary-color); border: none; font-weight: 700; }
        .pagination .active .page-link { background-color: var(--primary-color) !important; color: #fff !important; }
        ";

        // Extra Overrides
        if ($extraCSS) { $parts[] = "/* Custom CSS Override */\n" . $extraCSS; }

        $css = '<style>' . implode("\n", $parts) . '</style>';

        if ($minify && class_exists('Site') && method_exists('Site', 'minifyCSS')) {
            return $assets . Site::minifyCSS($css);
        }
        return $assets . $css;
    }

    /**
     * Render the full options panel.
     */
    public function render(array $schema): void
    {
        $this->renderCSS();
        $this->renderJS(); // Define function globally BEFORE use
        echo '<form method="post" id="gxOptionsForm">';
        echo '<input type="hidden" name="token" value="'.TOKEN.'">';
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
    }

    private function renderTopbar(): void
    {
        $name  = htmlspecialchars($this->brandName);
        $ver   = htmlspecialchars($this->brandVer);
        $key   = htmlspecialchars($this->saveKey);
        $color = htmlspecialchars($this->brandColor);
        $color2 = $this->shiftColor($this->brandColor, 30);

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

    private function renderSidebar(array $schema): void
    {
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

    private function renderAllPanels(array $schema): void
    {
        $first = true;
        foreach ($schema as $tab) {
            $id     = htmlspecialchars($tab['id']);
            $active = ($first || !empty($tab['active'])) ? ' active' : '';
            $first  = false;

            echo "<div class=\"gx-panel{$active}\" id=\"{$id}\">";
            if (!empty($tab['title'])) echo '<div class="gx-section-title">' . $tab['title'] . '</div>';
            if (!empty($tab['subtitle'])) echo '<div class="gx-section-sub">' . htmlspecialchars($tab['subtitle']) . '</div>';

            $type = $tab['type'] ?? 'standard';
            if ($type === 'presets') {
                $this->renderPresets();
            } elseif ($type === 'panels') {
                $this->renderPanels();
            } else {
                $this->renderStandardPanel($tab);
            }

            if (!empty($tab['raw'])) echo $tab['raw'];
            echo '</div>';
        }
    }

    private function renderStandardPanel(array $tab): void
    {
        foreach ($tab['sections'] ?? [] as $section) {
            if (!empty($section['title'])) echo '<div class="gx-section-title" style="margin-top:1.5rem;font-size:1rem;border-top:1px solid #e2e8f0;padding-top:1.5rem;">' . $section['title'] . '</div>';
            if (!empty($section['subtitle'])) echo '<div class="gx-section-sub text-muted extra-small mb-4">' . $section['subtitle'] . '</div>';
            foreach ($section['cards'] ?? [] as $card) $this->renderCard($card);
        }
        foreach ($tab['cards'] ?? [] as $card) $this->renderCard($card);
    }

    private function renderPresets(): void
    {
        echo '<div class="gx-grid-3 mt-4">';
        foreach ($this->presets as $id => $p) {
            $json = htmlspecialchars(json_encode($p));
            $c = $p['link_color'] ?? '#3b82f6';
            echo "<div class=\"gx-preset-card\" onclick='applyPreset({$json})'>
                <div class=\"gx-preset-preview\" style=\"background:{$c}\">" . ($p['emoji'] ?? '🎨') . "</div>
                <div class=\"gx-preset-name\">{$p['name']}</div>
            </div>";
        }
        echo '</div>';
    }

    private function renderPanels(): void
    {
        echo '<div class="gx-grid-1">';
        for ($i = 1; $i <= 5; $i++) {
            $catVal    = $this->opt["panel_{$i}"] ?? '';
            $colorVal  = $this->opt["panel_{$i}_color"] ?? '#3b82f6';
            $fontVal   = $this->opt["panel_{$i}_font_color"] ?? '#ffffff';
            $bgVal     = $this->opt["panel_{$i}_bg"] ?? '';
            $txtColVal = $this->opt["panel_{$i}_text_color"] ?? '';
            $ffVal     = $this->opt["panel_{$i}_font_family"] ?? 'inherit';
            $fsVal     = $this->opt["panel_{$i}_font_size"] ?? '1';

            echo "<div class=\"gx-card gx-panel-builder\">
                <div class=\"gx-panel-header\">
                    <span class=\"badge bg-primary rounded-pill mb-2\">Panel Block #{$i}</span>
                </div>
                <div class=\"gx-grid-2\">
                    <div class=\"gx-field\">
                        <label>Target Category</label>
                        " . Categories::dropdown(['name' => "panel_{$i}", 'selected' => $catVal, 'class' => 'gx-input']) . "
                    </div>
                    <div class=\"gx-field\">
                        <label>Accent Color</label>
                        " . self::colorField("panel_{$i}_color", $colorVal) . "
                    </div>
                    <div class=\"gx-field\">
                        <label>Heading Font Color</label>
                         " . self::colorField("panel_{$i}_font_color", $fontVal) . "
                    </div>
                     <div class=\"gx-field\">
                        <label>Font Size Factor</label>
                        <input type=\"number\" name=\"panel_{$i}_font_size\" class=\"gx-input\" value=\"{$fsVal}\" step=\"0.1\" min=\"0.5\" max=\"3\">
                    </div>
                </div>
                <div class=\"mt-3 gx-panel-advanced-toggle\">
                     <button type=\"button\" class=\"btn btn-link btn-sm p-0 text-decoration-none\" onclick=\"this.nextElementSibling.classList.toggle('d-none')\"><i class=\"fa fa-cog me-1\"></i> Advanced Panel Options</button>
                     <div class=\"d-none mt-3\">
                        <div class=\"gx-grid-3\">
                            <div class=\"gx-field\">
                                <label>Panel BG Image</label>
                                <input type=\"text\" name=\"panel_{$i}_bg\" class=\"gx-input\" value=\"{$bgVal}\" placeholder=\"Image URL\">
                            </div>
                            <div class=\"gx-field\">
                                <label>Panel Text Color</label>
                                 " . self::colorField("panel_{$i}_text_color", $txtColVal) . "
                            </div>
                            <div class=\"gx-field\">
                                <label>Font Family</label>
                                <select name=\"panel_{$i}_font_family\" class=\"gx-input\">";
                                foreach (self::$FONTS as $f => $l) {
                                    $sel = ($ffVal == $f) ? 'selected' : '';
                                    echo "<option value='{$f}' {$sel}>{$l}</option>";
                                }
            echo "              </select>
                            </div>
                        </div>
                     </div>
                </div>
            </div>";
        }
        echo '</div>';
    }

    private function renderCard(array $card): void
    {
        if (isset($card['type']) && $card['type'] === 'typo_row') { echo $this->fieldTypoRow($card['label'] ?? '', $card['prefix'] ?? ''); return; }
        if (isset($card['type']) && $card['type'] === 'raw') { echo $card['html'] ?? ''; return; }
        $title = $card['title'] ?? ''; $cols = (int)($card['cols'] ?? 1); $grid = $cols > 1 ? "gx-grid-{$cols}" : ''; $wrap = $cols > 1;
        echo '<div class="gx-card">';
        if ($title) echo '<div class="gx-card-title">' . htmlspecialchars($title) . '</div>';
        if ($wrap) echo "<div class=\"{$grid}\">";
        foreach ($card['fields'] ?? [] as $field) $this->renderField($field, $wrap);
        if ($wrap) echo '</div>';
        echo '</div>';
    }

    private function renderField(array $field, bool $inGrid = false): void
    {
        $type = $field['type'] ?? 'text';
        if ($type === 'raw') { echo $field['html'] ?? ''; return; }
        if ($type === 'divider') { echo '<hr style="border:none;border-top:1px solid #e2e8f0;margin:12px 0;">'; return; }
        if ($type === 'heading') { echo '<h6 style="font-size:11px;font-weight:800;text-transform:uppercase;color:#94a3b8;margin:12px 0 8px;">' . htmlspecialchars($field['text'] ?? '') . '</h6>'; return; }
        if ($type === 'typo_row') { echo $this->fieldTypoRow($field['label'] ?? '', $field['prefix'] ?? ''); return; }
        $wrapStyle = !$inGrid && !empty($field['style']) ? ' style="' . htmlspecialchars($field['style']) . '"' : '';
        echo "<div class=\"gx-field\"{$wrapStyle}>";
        if (!empty($field['label'])) echo '<label>' . $field['label'] . '</label>';
        switch ($type) {
            case 'text':    $this->fieldText($field);     break;
            case 'textarea':$this->fieldTextarea($field); break;
            case 'color':   $this->fieldColor($field);    break;
            case 'number':  $this->fieldNumber($field);   break;
            case 'range':   $this->fieldRange($field);    break;
            case 'select':  $this->fieldSelect($field);   break;
            case 'toggle':  $this->fieldToggle($field);   break;
        }
        if (!empty($field['hint'])) echo '<span class="hint">' . htmlspecialchars($field['hint']) . '</span>';
        echo '</div>';
    }

    private function fieldText(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? ''); $val = htmlspecialchars((string)($this->opt[$f['name'] ?? ''] ?? ''));
        $ph = htmlspecialchars($f['placeholder'] ?? ''); $id = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<input type=\"text\" name=\"{$name}\"{$id} class=\"gx-input\" value=\"{$val}\" placeholder=\"{$ph}\">";
    }

    private function fieldTextarea(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? ''); $val = htmlspecialchars((string)($this->opt[$f['name'] ?? ''] ?? ''));
        $rows = (int)($f['rows'] ?? 4); echo "<textarea name=\"{$name}\" class=\"gx-input\" rows=\"{$rows}\">{$val}</textarea>";
    }

    private function fieldColor(array $f): void
    {
        $name = $f['name'] ?? ''; $val = (string)($this->opt[$name] ?? ''); $id = $f['id'] ?? '';
        echo self::colorField($name, $val, $id);
    }

    private function fieldNumber(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? ''); $val = htmlspecialchars((string)($this->opt[$f['name'] ?? ''] ?? '0'));
        $min = $f['min'] ?? 0; $max = $f['max'] ?? 10000;
        echo "<input type=\"number\" name=\"{$name}\" class=\"gx-input\" value=\"{$val}\" min=\"{$min}\" max=\"{$max}\">";
    }

    private function fieldRange(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? ''); $val = (int)($this->opt[$f['name'] ?? ''] ?? ($f['default'] ?? 0));
        $min = $f['min'] ?? 0; $max = $f['max'] ?? 100; $step = $f['step'] ?? 1; $unit = $f['unit'] ?? 'px';
        $id = !empty($f['id']) ? htmlspecialchars($f['id']) : "range_" . rand(100, 999);
        echo "<div class=\"gx-range-wrap\">";
        echo "<input type=\"range\" name=\"{$name}\" id=\"{$id}\" class=\"gx-range\" value=\"{$val}\" min=\"{$min}\" max=\"{$max}\" step=\"{$step}\" oninput=\"document.getElementById('{$id}_val').innerText=this.value\">";
        echo "<span class=\"gx-range-badge\"><span id=\"{$id}_val\">{$val}</span>{$unit}</span>";
        echo "</div>";
    }

    private function fieldSelect(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? ''); $selected = (string)($this->opt[$f['name'] ?? ''] ?? '');
        $id = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<select name=\"{$name}\"{$id} class=\"gx-input\">";
        foreach ($f['options'] ?? [] as $v => $label) {
            $sel = (string)$v === $selected ? ' selected' : '';
            echo "<option value=\"{$v}\"{$sel}>" . htmlspecialchars($label) . "</option>";
        }
        echo "</select>";
    }

    private function fieldToggle(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? ''); $val = $this->opt[$f['name'] ?? ''] ?? 'off';
        $yes = htmlspecialchars($f['yes'] ?? 'On'); $no = htmlspecialchars($f['no'] ?? 'Off');
        $isYes = $val === 'on';
        echo "<div class=\"gx-toggle\">";
        echo "<input type=\"radio\" name=\"{$name}\" id=\"{$name}_off\" value=\"off\" " . (!$isYes ? 'checked' : '') . ">";
        echo "<label for=\"{$name}_off\">{$no}</label>";
        echo "<input type=\"radio\" name=\"{$name}\" id=\"{$name}_on\" value=\"on\" " . ($isYes ? 'checked' : '') . ">";
        echo "<label for=\"{$name}_on\">{$yes}</label>";
        echo "</div>";
    }

    private function fieldTypoRow(string $label, string $prefix): string
    {
        $font  = $prefix . '_font'; $size  = $prefix . '_size'; $weight = $prefix . '_weight'; $color = $prefix . '_color';
        echo '<div class="gx-card gx-typo-card">';
        echo "<div class=\"gx-typo-label\">{$label}</div>";
        echo '<div class="gx-typo-grid">';
        $this->renderField(['type' => 'select', 'name' => $font, 'label' => 'Family', 'options' => self::$FONTS], true);
        $this->renderField(['type' => 'number', 'name' => $size, 'label' => 'Size (px)'], true);
        $this->renderField(['type' => 'select', 'name' => $weight, 'label' => 'Weight', 'options' => self::$WEIGHTS], true);
        $this->renderField(['type' => 'color',  'name' => $color, 'label' => 'Color'], true);
        echo '</div></div>';
        return '';
    }

    public static function colorField(string $name, string $value, string $id = ''): string
    {
        $id = $id ?: "color_" . rand(100, 999); $value = htmlspecialchars($value);
        return "<div class=\"gx-color-field\"><input type=\"color\" id=\"{$id}\" value=\"{$value}\" oninput=\"document.getElementById('{$id}_txt').value=this.value\"><input type=\"text\" name=\"{$name}\" id=\"{$id}_txt\" value=\"{$value}\" placeholder=\"#000000\" oninput=\"document.getElementById('{$id}').value=this.value\"></div>";
    }

    private function renderCSS(): void
    {
        echo '<style>
        .gx-wrap { background:#f8fafc; font-family:"Plus Jakarta Sans", "Inter", sans-serif; color:#334155; margin:-20px; min-height:100vh; position:relative; }
        .gx-topbar { position:sticky; top:50px; z-index:100; height:70px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; padding:0 2rem; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:0; }
        .brand { display:flex; align-items:center; gap:12px; }
        .brand-logo { width:40px; height:40px; border-radius:10px; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:18px; }
        .brand-name { font-weight:800; color:#0f172a; font-size:16px; letter-spacing:-0.01em; }
        .brand-ver { background:#f1f5f9; color:#64748b; font-size:10px; font-weight:700; padding:2px 8px; border-radius:12px; }
        .gx-btn-save { background:#3b82f6; color:#fff; border:none; padding:10px 24px; border-radius:10px; font-weight:700; font-size:14px; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 12px rgba(59, 130, 246, 0.2); }
        .gx-btn-save:hover { background:#2563eb; transform:translateY(-1px); }
        .gx-layout { display:flex; }
        .gx-sidebar { width:260px; background:#fff; border-right:1px solid #e2e8f0; min-height:calc(100vh - 70px); padding:1.5rem 0; position:sticky; top:120px; }
        .gx-nav-group { margin-bottom:1.5rem; }
        .gx-nav-label { display:block; padding:0 1.5rem; font-size:11px; font-weight:800; text-transform:uppercase; color:#94a3b8; letter-spacing:0.05em; margin-bottom:0.5rem; }
        .gx-nav-item { display:flex; align-items:center; gap:12px; width:100%; padding:0.85rem 1.5rem; border:none; background:none; text-align:left; font-size:14px; font-weight:600; color:#64748b; cursor:pointer; transition:all 0.2s; position:relative; }
        .gx-nav-item i { width:18px; text-align:center; font-size:16px; opacity:0.75; }
        .gx-nav-item:hover { background:#f8fafc; color:#3b82f6; }
        .gx-nav-item.active { background:#eff6ff; color:#3b82f6; }
        .gx-nav-item.active::after { content:""; position:absolute; right:0; top:15%; bottom:15%; width:4px; background:#3b82f6; border-radius:4px 0 0 4px; }
        .gx-main { flex:1; padding:2.5rem; max-width:900px; }
        .gx-panel { display:none; animation:fadeIn 0.3s ease; }
        .gx-panel.active { display:block; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .gx-section-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:0.4rem; letter-spacing:-0.02em; }
        .gx-section-sub { font-size:14px; color:#64748b; margin-bottom:2rem; line-height:1.6; }
        .gx-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:1.75rem; margin-bottom:1.5rem; box-shadow:0 1px 2px rgba(0,0,0,0.03); }
        .gx-card-title { font-size:14px; font-weight:700; color:#0f172a; margin-bottom:1.25rem; }
        .gx-grid-1 { display:grid; grid-template-columns:1fr; gap:1.5rem; }
        .gx-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; }
        .gx-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.2rem; }
        .gx-field { margin-bottom:1.25rem; }
        .gx-field:last-child { margin-bottom:0; }
        .gx-field label { display:block; font-size:13px; font-weight:700; color:#475569; margin-bottom:0.5rem; }
        .gx-input { width:100%; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:0.75rem 0.9rem; font-size:13px; font-weight:500; color:#1e293b; transition:all 0.2s; }
        .gx-input:focus { background:#fff; border-color:#3b82f6; outline:none; box-shadow:0 0 0 4px rgba(59, 130, 246, 0.08); }
        .gx-color-field { display:flex; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; }
        .gx-color-field input[type="color"] { width:48px; height:42px; border:none; background:none; cursor:pointer; padding:2px; }
        .gx-color-field input[type="text"] { flex:1; border:none; background:none; padding:0 12px; font-family:monospace; font-size:13px; }
        .gx-range-wrap { display:flex; align-items:center; gap:15px; }
        .gx-range { flex:1; height:6px; background:#e2e8f0; border-radius:10px; -webkit-appearance:none; cursor:pointer; }
        .gx-range::-webkit-slider-thumb { -webkit-appearance:none; width:18px; height:18px; background:#3b82f6; border:3px solid #fff; border-radius:50%; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
        .gx-range-badge { background:#eff6ff; color:#3b82f6; font-size:11px; font-weight:800; padding:4px 10px; border-radius:8px; min-width:50px; text-align:center; }
        .gx-toggle { display:inline-flex; background:#f1f5f9; padding:4px; border-radius:10px; }
        .gx-toggle input { display:none; }
        .gx-toggle label { margin:0; padding:6px 16px; font-size:12px; font-weight:700; color:#64748b; cursor:pointer; transition:all 0.2s; border-radius:7px; }
        .gx-toggle input:checked + label { background:#fff; color:#3b82f6; box-shadow:0 2px 4px rgba(0,0,0,0.05); }
        .gx-typo-card { padding:1.5rem; }
        .gx-typo-label { font-size:13px; font-weight:800; color:#0f172a; margin-bottom:1.25rem; background:#f1f5f9; padding:6px 12px; border-radius:8px; width:fit-content; }
        .gx-typo-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:1rem; }
        .hint { display:block; font-size:11px; color:#94a3b8; margin-top:6px; line-height:1.4; }

        /* Custom Styles for Presets & Panels */
        .gx-preset-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:1.25rem; cursor:pointer; transition:all 0.3s; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.03); }
        .gx-preset-card:hover { transform:translateY(-4px); border-color:#3b82f6; box-shadow:0 10px 25px rgba(59, 130, 246, 0.1); }
        .gx-preset-preview { width:60px; height:60px; border-radius:50%; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; font-size:24px; color:#fff; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        .gx-preset-name { font-weight:800; font-size:13px; color:#0f172a; }
        .gx-panel-builder { border-left:4px solid #3b82f6; }
        .gx-panel-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; border-bottom:1px solid #f1f5f9; padding-bottom:1rem; }
        </style>';
    }

    private function renderJS(): void
    {
        echo '<script>
        window.applyPreset = function(p) {
            if(!confirm("Apply " + p.name + " style preset? This will overwrite your current color settings.")) return;
            for (const [key, value] of Object.entries(p)) {
                const el = document.getElementsByName(key)[0];
                if(el) {
                    el.value = value;
                    if(el.type === "color") {
                         const txt = document.getElementById(el.id + "_txt");
                         if(txt) txt.value = value;
                    }
                    if(el.type === "range") {
                         const badge = document.getElementById(el.id + "_val");
                         if(badge) badge.innerText = value;
                    }
                }
            }
            const firstTab = document.querySelector(".gx-nav-item:not([data-tab=\'tab-presets\'])");
            if(firstTab) firstTab.click();
            
            const toast = document.createElement("div");
            toast.style.cssText = "position:fixed;bottom:30px;left:50%;transform:translateX(-50%);background:#10b981;color:#fff;padding:12px 30px;border-radius:50px;font-weight:700;box-shadow:0 10px 30px rgba(16,185,129,0.3);z-index:99999;";
            toast.innerHTML = "<i class=\'fa fa-check-circle me-2\'></i> " + p.name + " applied! Click Save Changes to store.";
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        };

        window.initSidebar = function() {
            document.querySelectorAll(".gx-nav-item").forEach(btn => {
                btn.onclick = () => {
                    document.querySelectorAll(".gx-nav-item, .gx-panel").forEach(el => el.classList.remove("active"));
                    btn.classList.add("active");
                    document.getElementById(btn.dataset.tab).classList.add("active");
                    window.location.hash = btn.dataset.tab;
                }
            });
            if(window.location.hash) {
                const h = window.location.hash.substring(1);
                const target = document.querySelector(`.gx-nav-item[data-tab="${h}"]`);
                if(target) target.click();
            }
        };
        
        document.addEventListener("DOMContentLoaded", window.initSidebar);
        </script>';
    }
}
