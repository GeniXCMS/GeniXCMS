<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * OptionsBuilder Class
 *
 * A self-contained, premium UI engine for theme and module configuration.
 * It provides a standardized, industrial ERP-style administrative panel
 * with an integrated CSS generation engine for the frontend.
 *
 * Architecture:
 * - Admin UI: Tabbed sidebar, sticky topbar, glassmorphic headers.
 * - CSS Engine: Reactive typography and color mapping using standard prefixes.
 * - Notifications: Integrated global AJAX toast system.
 * @since 2.0.0
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class OptionsBuilder
{
    // ── Instance properties ────────────────────────────────────
    private array $opt;
    private array $presets;
    private array $panel_palettes;

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
        'inherit' => 'Inherit (Default)',
        '"Inter", sans-serif' => 'Inter (Sans-Serif)',
        '"Roboto", sans-serif' => 'Roboto',
        '"Open Sans", sans-serif' => 'Open Sans',
        '"Lato", sans-serif' => 'Lato',
        '"Poppins", sans-serif' => 'Poppins',
        '"Nunito", sans-serif' => 'Nunito',
        '"Material Symbols Outlined", sans-serif' => 'Material Icons',
        '"Merriweather", serif' => 'Merriweather (Serif)',
        '"Playfair Display", serif' => 'Playfair Display (Serif)',
        '"Georgia", serif' => 'Georgia (Serif)',
        '"JetBrains Mono", monospace' => 'JetBrains Mono (Monospace)',
    ];

    /** Font-family value → Google Fonts API parameter string */
    public static array $GF_MAP = [
        '"Inter", sans-serif' => 'Inter:wght@300;400;500;600;700;800;900',
        '"Roboto", sans-serif' => 'Roboto:wght@300;400;500;700;900',
        '"Open Sans", sans-serif' => 'Open+Sans:wght@300;400;500;600;700;800',
        '"Lato", sans-serif' => 'Lato:wght@300;400;700;900',
        '"Poppins", sans-serif' => 'Poppins:wght@300;400;500;600;700;800;900',
        '"Nunito", sans-serif' => 'Nunito:wght@300;400;500;600;700;800;900',
        '"Merriweather", serif' => 'Merriweather:wght@300;400;700;900',
        '"Playfair Display", serif' => 'Playfair+Display:wght@400;500;600;700;800;900',
        '"Georgia", serif' => '', // web-safe, no import needed
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
     * OptionsBuilder Constructor.
     * Initializes the UI configuration for the theme/module options panel.
     *
     * @param array $opt            Currently saved options (key-value pairs).
     * @param array $presets        Optional color scheme presets for quick application.
     * @param array $panel_palettes Optional color palettes for specific panel blocks.
     * @param array $config         Identity configuration (brand name, version, icons, etc).
     */
    public function __construct(
        array $opt,
        array $presets = [],
        array $panel_palettes = [],
        array $config = []
    ) {
        $this->opt = $opt;
        $this->presets = $presets;
        $this->panel_palettes = $panel_palettes;
        $this->brandName = $config['brandName'] ?? 'GeniXCMS Theme';
        $this->brandVer = $config['brandVer'] ?? 'v2.1';
        $this->brandAbbr = $config['brandAbbr'] ?? 'GX';
        $this->brandIcon = $config['brandIcon'] ?? '';
        $this->brandColor = $config['brandColor'] ?? '#3b82f6';
        $this->saveKey = $config['saveKey'] ?? 'theme_options_update';
    }

    /**
     * Safely retrieves a value from the current options array.
     *
     * @param string $key     The option key.
     * @param mixed  $default Default value if key is not found.
     * @return string         The decoded option value.
     */
    private function getValue(string $key, $default = ''): string
    {
        $v = (string) ($this->opt[$key] ?? $default);
        return htmlspecialchars_decode($v, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * Generates the dynamic CSS and Google Fonts assets for the frontend.
     * Automatically maps typography prefixes to CSS variables and selectors.
     *
     * @param array $opt    Combined options array.
     * @param array $config Styling configuration (minify, themeUrl, etc).
     * @return string       Generated <link> and <style> HTML tags.
     */
    public static function generateFrontendCSS(array $opt, array $config = []): string
    {
        $themeUrl = $config['themeUrl'] ?? '';
        $minify = $config['minify'] ?? false;
        $extraCSS = $config['extraCssRules'] ?? ($opt['custom_css'] ?? '');

        // ── Google Fonts ──────────────────────────────────────────────────
        $typoKeys = $config['typoKeys'] ?? [
            'typo_body_font',
            'typo_nav_font',
            'typo_h1_font',
            'typo_h2_font',
            'typo_h3_font',
            'typo_h4_font',
            'typo_post_title_font',
            'typo_meta_font',
            'typo_single_title_font',
            'typo_content_font',
            'typo_post_meta_font',
            'typo_blockquote_font',
            'typo_breadcrumb_font',
            'typo_comment_title_font',
            'typo_comment_body_font',
            'typo_hero_title_font',
            'typo_hero_text_font',
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
            . (($themeUrl && ($config['loadStyleCss'] ?? true)) ? '<link href="' . $themeUrl . 'css/style.css" rel="stylesheet">' . "\n" : '');

        // ── CSS Variables ──────────────────────────────────────────────────
        $root = ":root {\n";
        $root .= "  --primary-color: " . ($opt['link_color'] ?? '#3b82f6') . ";\n";
        $root .= "  --primary-hover: " . ($opt['link_hover_color'] ?? '#1d4ed8') . ";\n";
        $root .= "  --bg-body: " . ($opt['body_background_color'] ?? '#f8fafc') . ";\n";
        $root .= "  --bg-card: " . ($opt['content_background_color_body'] ?? '#ffffff') . ";\n";
        $root .= "  --container-width: " . ($opt['container_width'] ?? '1140') . "px;\n";
        $root .= "  --transition: all 0.3s ease;\n";
        $root .= "}\n";

        $typo = function ($prefix, $selector) use ($opt) {
            $f = $opt[$prefix . '_font'] ?? '';
            $s = $opt[$prefix . '_size'] ?? '';
            $w = $opt[$prefix . '_weight'] ?? '';
            $c = $opt[$prefix . '_color'] ?? '';
            $out = "{$selector} {\n";
            if ($f && $f !== 'inherit')
                $out .= "  font-family: {$f} !important;\n";
            if ($s)
                $out .= "  font-size: {$s}px !important;\n";
            if ($w)
                $out .= "  font-weight: {$w} !important;\n";
            if ($c)
                $out .= "  color: {$c} !important;\n";
            $out .= "}\n";
            return $out;
        };

        $parts = [$root];

        // Global Layout & Sizes - Strictly Conditional
        $layoutCss = "";
        if (isset($opt['typo_body_color']))
            $layoutCss .= "body { color: " . $opt['typo_body_color'] . "; }\n";
        if (isset($opt['body_background_color']))
            $layoutCss .= "body { background-color: var(--bg-body) !important; }\n";
        if (isset($opt['container_width']))
            $layoutCss .= ".container { max-width: var(--container-width) !important; }\n";
        if (isset($opt['background_color_navbar']))
            $layoutCss .= ".navbar { background-color: " . $opt['background_color_navbar'] . " !important; }\n";

        if (isset($opt['background_color_header']) || isset($opt['background_header'])) {
            $layoutCss .= ".hero-bg { ";
            if (isset($opt['background_color_header']))
                $layoutCss .= "background-color: " . $opt['background_color_header'] . " !important; ";
            if (isset($opt['background_header']))
                $layoutCss .= "background-image: url('" . $opt['background_header'] . "'); background-size: cover; background-position: center; ";
            $layoutCss .= "}\n";
        }

        if (isset($opt['content_background_color_body']) || isset($opt['content_border_width']) || isset($opt['content_border_color'])) {
            $layoutCss .= ".card, .post-card { ";
            if (isset($opt['content_background_color_body']))
                $layoutCss .= "background-color: var(--bg-card) !important; ";
            if (isset($opt['content_border_width']))
                $layoutCss .= "border-width: " . $opt['content_border_width'] . "px !important; border-style: solid !important; ";
            if (isset($opt['content_border_color']))
                $layoutCss .= "border-color: " . $opt['content_border_color'] . " !important; ";
            $layoutCss .= "}\n";
        }

        $parts[] = $layoutCss;

        // Typography orchestration
        $parts[] = $typo('typo_body', 'body, p, .entry-content');
        $parts[] = $typo('typo_nav', '.nav-link, .navbar-brand, .blog-nav-item');
        $parts[] = $typo('typo_brand', '.blog-header-logo, .site-logo-text');
        $parts[] = $typo('typo_h1', 'h1, .display-1, .blog-post-title, .post-title');
        $parts[] = $typo('typo_h2', 'h2, .display-2');
        $parts[] = $typo('typo_h3', 'h3, .display-3');
        $parts[] = $typo('typo_h4', 'h4, .display-4');
        $parts[] = $typo('typo_h5', 'h5, .display-5');
        $parts[] = $typo('typo_h6', 'h6, .display-6');
        $parts[] = $typo('typo_list', 'ul, ol, .entry-content ul, .entry-content ol');
        $parts[] = $typo('typo_blockquote', 'blockquote, .blockquote');
        $parts[] = $typo('typo_pagination', '.pagination, .page-link, .page-item a');
        $parts[] = $typo('typo_pagination', '.pagination, .page-link, .page-item a');
        $parts[] = $typo('typo_meta', '.post-meta, .text-muted, .entry-meta');
        $parts[] = $typo('typo_hero_title', '.hero-section h1, .inner-hero-title, .display-4.fst-italic');
        $parts[] = $typo('typo_hero_text', '.hero-description, .lead.my-3');

        // ── Component Base Styles ──────────────────────────────
        $parts[] = "
        .btn-primary { 
            " . (isset($opt['link_color']) ? "background-color: var(--primary-color) !important; border-color: var(--primary-color) !important;" : "") . "
            font-weight: 700 !important; border-radius: 8px !important; 
        }
        .pagination, .blog-pagination { display: flex; gap: 4px; list-style: none; padding: 0; }
        .page-item .page-link, .blog-pagination a, .blog-pagination span { 
            border: 1px solid #dee2e6; background: #fff; color: var(--primary-color); 
            padding: 8px 16px; text-decoration: none !important;
        }
        .page-item.active .page-link, .blog-pagination span { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
        ";

        if ($extraCSS) {
            $parts[] = $extraCSS;
        }

        $css = implode("\n", $parts);
        if ($minify && class_exists('Site') && method_exists('Site', 'minifyCSS')) {
            $css = Site::minifyCSS($css);
        }
        return $assets . "<style id=\"gx-dynamic-css\">\n" . trim($css) . "\n</style>";
    }

    /**
     * Renders the complete options panel UI within an HTML form.
     * Hooks into global CSS/JS assets and iterates through the provided schema.
     *
     * @param array $schema Multi-dimensional array defining tabs, sections, and fields.
     */
    public function render(array $schema): void
    {
        $this->renderCSS();
        $this->renderJS(); // Define function globally BEFORE use
        echo '<form method="post" id="gxOptionsForm">';
        echo '<input type="hidden" name="token" value="' . TOKEN . '">';
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

    /**
     * Renders the administrative topbar containing brand identity and save button.
     */
    private function renderTopbar(): void
    {
        $name = htmlspecialchars($this->brandName);
        $ver = htmlspecialchars($this->brandVer);
        $key = htmlspecialchars($this->saveKey);
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

    /**
     * Shifts a HEX color by a given number of degrees on the color wheel.
     * Used for generating gradients from a single brand color.
     *
     * @param string $hex     Original HEX color.
     * @param int    $degrees Degrees to shift (default 30).
     * @return string         The shifted HEX color.
     */
    private function shiftColor(string $hex, int $degrees = 30): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6)
            return '#6366f1';
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;
        $h = 0;
        $s = 0;
        $l = ($max + $min) / 2;
        if ($delta > 0) {
            $s = $delta / (1 - abs(2 * $l - 1));
            if ($max === $r)
                $h = 60 * fmod(($g - $b) / $delta, 6);
            elseif ($max === $g)
                $h = 60 * (($b - $r) / $delta + 2);
            else
                $h = 60 * (($r - $g) / $delta + 4);
        }
        $h = fmod($h + $degrees + 360, 360);
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;
        if ($h < 60) {
            $r2 = $c;
            $g2 = $x;
            $b2 = 0;
        } elseif ($h < 120) {
            $r2 = $x;
            $g2 = $c;
            $b2 = 0;
        } elseif ($h < 180) {
            $r2 = 0;
            $g2 = $c;
            $b2 = $x;
        } elseif ($h < 240) {
            $r2 = 0;
            $g2 = $x;
            $b2 = $c;
        } elseif ($h < 300) {
            $r2 = $x;
            $g2 = 0;
            $b2 = $c;
        } else {
            $r2 = $c;
            $g2 = 0;
            $b2 = $x;
        }
        return sprintf(
            '#%02x%02x%02x',
            round(($r2 + $m) * 255),
            round(($g2 + $m) * 255),
            round(($b2 + $m) * 255)
        );
    }

    /**
     * Renders the sidebar navigation containing tab groups and items.
     *
     * @param array $schema The builder schema definition.
     */
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
                $id = htmlspecialchars($tab['id']);
                $label = htmlspecialchars($tab['label']);
                $icon = htmlspecialchars($tab['icon'] ?? 'fa fa-circle');
                $active = ($first || !empty($tab['active'])) ? ' active' : '';
                $first = false;
                echo "<button type=\"button\" class=\"gx-nav-item{$active}\" data-tab=\"{$id}\"><i class=\"{$icon}\"></i> {$label}</button>";
            }
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Iterates through the schema to render each individual panel container.
     *
     * @param array $schema The builder schema definition.
     */
    private function renderAllPanels(array $schema): void
    {
        $first = true;
        foreach ($schema as $tab) {
            $id = htmlspecialchars($tab['id']);
            $active = ($first || !empty($tab['active'])) ? ' active' : '';
            $first = false;

            echo "<div class=\"gx-panel{$active}\" id=\"{$id}\">";
            if (!empty($tab['title']))
                echo '<div class="gx-section-title">' . $tab['title'] . '</div>';
            if (!empty($tab['subtitle']))
                echo '<div class="gx-section-sub">' . htmlspecialchars($tab['subtitle']) . '</div>';

            $type = $tab['type'] ?? 'standard';
            if ($type === 'presets') {
                $this->renderPresets();
            } elseif ($type === 'panels') {
                $this->renderPanels();
            } else {
                $this->renderStandardPanel($tab);
            }

            if (!empty($tab['raw']))
                echo $tab['raw'];
            echo '</div>';
        }
    }

    /**
     * Renders a standard panel containing sections and cards.
     *
     * @param array $tab Configuration for the individual tab.
     */
    private function renderStandardPanel(array $tab): void
    {
        foreach ($tab['sections'] ?? [] as $section) {
            if (!empty($section['title']))
                echo '<div class="gx-section-title" style="margin-top:1.5rem;font-size:1rem;border-top:1px solid #e2e8f0;padding-top:1.5rem;">' . $section['title'] . '</div>';
            if (!empty($section['subtitle']))
                echo '<div class="gx-section-sub text-muted extra-small mb-4">' . $section['subtitle'] . '</div>';
            foreach ($section['cards'] ?? [] as $card)
                $this->renderCard($card);
        }
        foreach ($tab['cards'] ?? [] as $card)
            $this->renderCard($card);
    }

    /**
     * Renders the presets panel showing quick color scheme options.
     */
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

    /**
     * Renders the dynamic panels builder for category-based content blocks.
     */
    private function renderPanels(): void
    {
        echo '<div class="gx-grid-1">';
        for ($i = 1; $i <= 5; $i++) {
            $catVal = $this->opt["panel_{$i}"] ?? '';
            $colorVal = $this->opt["panel_{$i}_color"] ?? '#3b82f6';
            $fontVal = $this->opt["panel_{$i}_font_color"] ?? '#ffffff';
            $bgVal = $this->opt["panel_{$i}_bg"] ?? '';
            $txtColVal = $this->opt["panel_{$i}_text_color"] ?? '';
            $ffVal = $this->opt["panel_{$i}_font_family"] ?? 'inherit';
            $fsVal = $this->opt["panel_{$i}_font_size"] ?? '1';

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

    /**
     * Renders a layout card which acts as a container for multiple input fields.
     * Supports multi-column grid layouts and row-based typography groups.
     *
     * @param array $card Configuration array for the card and its children.
     */
    private function renderCard(array $card): void
    {
        if (isset($card['type']) && $card['type'] === 'typo_row') {
            echo $this->fieldTypoRow($card['label'] ?? '', $card['prefix'] ?? '');
            return;
        }
        if (isset($card['type']) && $card['type'] === 'raw') {
            echo $card['html'] ?? '';
            return;
        }
        $title = $card['title'] ?? '';
        $cols = (int) ($card['cols'] ?? 1);
        $grid = $cols > 1 ? "gx-grid-{$cols}" : '';
        $wrap = $cols > 1;
        echo '<div class="gx-card">';
        if ($title)
            echo '<div class="gx-card-title">' . htmlspecialchars($title) . '</div>';
        if ($wrap)
            echo "<div class=\"{$grid}\">";
        foreach ($card['fields'] ?? [] as $field)
            $this->renderField($field, $wrap);
        if ($wrap)
            echo '</div>';
        echo '</div>';
    }

    /**
     * Entry point for rendering individual configuration fields based on their type.
     *
     * @param array $field  Field configuration (type, name, label, etc).
     * @param bool  $inGrid Whether the field is currently nested inside a grid card.
     */
    private function renderField(array $field, bool $inGrid = false): void
    {
        $type = $field['type'] ?? 'text';
        if ($type === 'raw') {
            echo $field['html'] ?? '';
            return;
        }
        if ($type === 'divider') {
            echo '<hr style="border:none;border-top:1px solid #e2e8f0;margin:12px 0;">';
            return;
        }
        if ($type === 'heading') {
            echo '<h6 style="font-size:11px;font-weight:800;text-transform:uppercase;color:#94a3b8;margin:12px 0 8px;">' . htmlspecialchars($field['text'] ?? '') . '</h6>';
            return;
        }
        if ($type === 'typo_row') {
            echo $this->fieldTypoRow($field['label'] ?? '', $field['prefix'] ?? '');
            return;
        }
        $wrapStyle = !$inGrid && !empty($field['style']) ? ' style="' . htmlspecialchars($field['style']) . '"' : '';
        echo "<div class=\"gx-field\"{$wrapStyle}>";
        if (!empty($field['label']))
            echo '<label>' . $field['label'] . '</label>';
        switch ($type) {
            case 'text':
                $this->fieldText($field);
                break;
            case 'password':
                $this->fieldPassword($field);
                break;
            case 'textarea':
                $this->fieldTextarea($field);
                break;
            case 'color':
                $this->fieldColor($field);
                break;
            case 'number':
                $this->fieldNumber($field);
                break;
            case 'range':
                $this->fieldRange($field);
                break;
            case 'select':
                $this->fieldSelect($field);
                break;
            case 'toggle':
                $this->fieldToggle($field);
                break;
            case 'checkbox':
                $this->fieldCheckbox($field);
                break;
        }
        if (!empty($field['hint']))
            echo '<span class="hint">' . htmlspecialchars($field['hint']) . '</span>';
        echo '</div>';
    }

    /**
     * Renders a standard single-line text input.
     */
    private function fieldText(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $val = htmlspecialchars($this->getValue($f['name'] ?? ''));
        $ph = htmlspecialchars($f['placeholder'] ?? '');
        $id = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<input type=\"text\" name=\"{$name}\"{$id} class=\"gx-input\" value=\"{$val}\" placeholder=\"{$ph}\">";
    }

    /**
     * Renders a password input field.
     */
    private function fieldPassword(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $val = htmlspecialchars($this->getValue($f['name'] ?? ''));
        $ph = htmlspecialchars($f['placeholder'] ?? '');
        $id = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<input type=\"password\" name=\"{$name}\"{$id} class=\"gx-input\" value=\"{$val}\" placeholder=\"{$ph}\">";
    }

    /**
     * Renders a multi-line auto-expanding textarea.
     */
    private function fieldTextarea(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $val = htmlspecialchars($this->getValue($f['name'] ?? ''));
        $rows = (int) ($f['rows'] ?? 4);
        echo "<textarea name=\"{$name}\" class=\"gx-input\" rows=\"{$rows}\">{$val}</textarea>";
    }

    /**
     * Renders a premium color picker with integrated HEX text support.
     */
    private function fieldColor(array $f): void
    {
        $name = $f['name'] ?? '';
        $val = (string) ($this->opt[$name] ?? '');
        $id = $f['id'] ?? '';
        echo self::colorField($name, $val, $id);
    }

    /**
     * Renders a numeric input field with min/max validation.
     */
    private function fieldNumber(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $val = htmlspecialchars($this->getValue($f['name'] ?? '', '0'));
        $min = $f['min'] ?? 0;
        $max = $f['max'] ?? 99999999;
        $step = $f['step'] ?? 'any';
        echo "<input type=\"number\" name=\"{$name}\" class=\"gx-input\" value=\"{$val}\" min=\"{$min}\" max=\"{$max}\" step=\"{$step}\">";
    }

    /**
     * Renders a range slider with a dynamic value badge unit.
     */
    private function fieldRange(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $val = (int) ($this->opt[$f['name'] ?? ''] ?? ($f['default'] ?? 0));
        $min = $f['min'] ?? 0;
        $max = $f['max'] ?? 100;
        $step = $f['step'] ?? 1;
        $unit = $f['unit'] ?? 'px';
        $id = !empty($f['id']) ? htmlspecialchars($f['id']) : "range_" . rand(100, 999);
        echo "<div class=\"gx-range-wrap\">";
        echo "<input type=\"range\" name=\"{$name}\" id=\"{$id}\" class=\"gx-range\" value=\"{$val}\" min=\"{$min}\" max=\"{$max}\" step=\"{$step}\" oninput=\"document.getElementById('{$id}_val').innerText=this.value\">";
        echo "<span class=\"gx-range-badge\"><span id=\"{$id}_val\">{$val}</span>{$unit}</span>";
        echo "</div>";
    }

    /**
     * Renders a single checkbox switch.
     */
    private function fieldCheckbox(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $value = $this->opt[$f['name'] ?? ''] ?? 'off';
        $checked = ($value === 'on') ? ' checked' : '';
        echo "<input type=\"hidden\" name=\"{$name}\" value=\"off\">";
        echo "<input type=\"checkbox\" name=\"{$name}\" value=\"on\"{$checked} class=\"gx-checkbox-input\">";
    }

    /**
     * Renders a dropdown selection menu.
     */
    private function fieldSelect(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $selected = $this->getValue($f['name'] ?? '');
        $id = !empty($f['id']) ? ' id="' . htmlspecialchars($f['id']) . '"' : '';
        echo "<select name=\"{$name}\"{$id} class=\"gx-input\">";
        foreach ($f['options'] ?? [] as $v => $label) {
            $val_attr = htmlspecialchars((string) $v);
            $sel = (string) $v === $selected ? ' selected' : '';
            echo "<option value=\"{$val_attr}\"{$sel}>" . htmlspecialchars($label) . "</option>";
        }
        echo "</select>";
    }

    /**
     * Renders a segmented toggle switch (On/Off).
     */
    private function fieldToggle(array $f): void
    {
        $name = htmlspecialchars($f['name'] ?? '');
        $val = $this->opt[$f['name'] ?? ''] ?? 'off';
        $yes = htmlspecialchars($f['yes'] ?? 'On');
        $no = htmlspecialchars($f['no'] ?? 'Off');
        $isYes = $val === 'on';
        echo "<div class=\"gx-toggle\">";
        echo "<input type=\"radio\" name=\"{$name}\" id=\"{$name}_off\" value=\"off\" " . (!$isYes ? 'checked' : '') . ">";
        echo "<label for=\"{$name}_off\">{$no}</label>";
        echo "<input type=\"radio\" name=\"{$name}\" id=\"{$name}_on\" value=\"on\" " . ($isYes ? 'checked' : '') . ">";
        echo "<label for=\"{$name}_on\">{$yes}</label>";
        echo "</div>";
    }

    /**
     * Renders a specialized typography row group (Family, Size, Weight, Color).
     *
     * @param string $label  Human label for the group.
     * @param string $prefix Prefix used for the specific typography option keys.
     * @return string        Empty string (echoes output directly).
     */
    private function fieldTypoRow(string $label, string $prefix): string
    {
        $font = $prefix . '_font';
        $size = $prefix . '_size';
        $weight = $prefix . '_weight';
        $color = $prefix . '_color';
        echo '<div class="gx-card gx-typo-card">';
        echo "<div class=\"gx-typo-label\">{$label}</div>";
        echo '<div class="gx-typo-grid">';
        $this->renderField(['type' => 'select', 'name' => $font, 'label' => 'Family', 'options' => self::$FONTS], true);
        $this->renderField(['type' => 'number', 'name' => $size, 'label' => 'Size (px)'], true);
        $this->renderField(['type' => 'select', 'name' => $weight, 'label' => 'Weight', 'options' => self::$WEIGHTS], true);
        $this->renderField(['type' => 'color', 'name' => $color, 'label' => 'Color'], true);
        echo '</div></div>';
        return '';
    }

    /**
     * static utility to generate a premium color field with text input sync.
     *
     * @param string $name  Option key name.
     * @param string $value Current setting value.
     * @param string $id    Optional DOM ID for synchronization.
     * @return string        Generated HTML string.
     */
    public static function colorField(string $name, string $value, string $id = ''): string
    {
        $id = $id ?: "color_" . rand(100, 999);
        $value = htmlspecialchars($value);
        return "<div class=\"gx-color-field\"><input type=\"color\" id=\"{$id}\" value=\"{$value}\" oninput=\"document.getElementById('{$id}_txt').value=this.value\"><input type=\"text\" name=\"{$name}\" id=\"{$id}_txt\" value=\"{$value}\" placeholder=\"#000000\" oninput=\"document.getElementById('{$id}').value=this.value\"></div>";
    }

    /**
     * Renders the base CSS styles for the OptionsBuilder administrative interface.
     * Includes layouts for topbar, sidebar, glassmorphism effects, and the toast system.
     */
    public static function renderCSS(): void
    {
        echo '<style>
        @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

        .gx-wrap { 
            background: #f1f5f9; 
            font-family: "Plus Jakarta Sans", sans-serif; 
            color: #334155; 
            margin: -30px -30px 0 -30px; 
            min-height: 100vh; 
            position: relative; 
            display: flex; 
            flex-direction: column;
        }

        /* Topbar - Glass Effect */
        .gx-topbar { 
            position: sticky; 
            top: var(--gx-header-height, 0) !important; 
            z-index: 1040; 
            height: 64px; 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 1); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 2rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .brand { display: flex; align-items: center; gap: 16px; }
        .brand-logo { 
            width: 44px; height: 44px; border-radius: 12px; color: #fff; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: 800; font-size: 20px; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .brand-name { font-weight: 800; color: #0f172a; font-size: 18px; letter-spacing: -0.02em; }
        .brand-ver { background: #e2e8f0; color: #475569; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 50px; }

        .gx-btn-save { 
            background: #0f172a; color: #fff; border: none; padding: 12px 28px; 
            border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); 
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
            display: flex; align-items: center; gap: 8px;
        }
        .gx-btn-save:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.3); }
        .gx-btn-save:active { transform: translateY(0); }

        .gx-layout { display: flex; flex: 1; padding: 1.5rem 2rem; gap: 2rem; }

        /* Sidebar - Floating Card Style */
        .gx-sidebar { 
            width: 260px; 
            background: #fff; 
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            height: fit-content;
            padding: 1.25rem 0.5rem; 
            position: sticky; 
            top: calc(var(--gx-header-height, 0px) + 74px); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .gx-nav-group { margin-bottom: 2rem; }
        .gx-nav-label { 
            display: block; padding: 0 1.25rem; font-size: 11px; font-weight: 800; 
            text-transform: uppercase; color: #94a3b8; letter-spacing: 0.1em; margin-bottom: 1rem; 
        }

        .gx-nav-item { 
            display: flex; align-items: center; gap: 12px; width: 100%; 
            padding: 0.85rem 1.25rem; border: none; background: transparent; 
            text-align: left; font-size: 14px; font-weight: 600; color: #64748b; 
            cursor: pointer; transition: all 0.2s ease; border-radius: 14px;
            margin-bottom: 4px;
        }
        .gx-nav-item i { width: 20px; text-align: center; font-size: 18px; opacity: 0.8; }
        .gx-nav-item:hover { background: #f8fafc; color: #0f172a; }
        .gx-nav-item.active { background: #0f172a; color: #fff; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        .gx-main { flex: 1; max-width: 960px; }

        .gx-panel { display: none; transform-origin: top; }
        .gx-panel.active { display: block; animation: panelEnter 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes panelEnter { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .gx-section-title { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem; letter-spacing: -0.03em; }
        .gx-section-sub { font-size: 14px; color: #64748b; margin-bottom: 1.5rem; line-height: 1.5; }

        /* Cards - Modern & Stacked */
        .gx-card { 
            background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; 
            padding: 2.25rem; margin-bottom: 2rem; 
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .gx-card:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }
        .gx-card-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 1.75rem; display: flex; align-items: center; gap: 10px; }

        .gx-grid-1 { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        .gx-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        .gx-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; }

        .gx-field { margin-bottom: 1.75rem; }
        .gx-field label { display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 0.75rem; }

        .gx-input { 
            width: 100%; background: #f8fafc; border: 2px solid #f1f5f9; border-radius: 14px; 
            padding: 0.9rem 1.1rem; font-size: 14px; font-weight: 500; color: #1e293b; 
            transition: all 0.3s ease; 
        }
        .gx-input:focus { background: #fff; border-color: #0f172a; outline: none; box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05); }

        .gx-field input[type="checkbox"] { margin-top: 0.25rem; transform: scale(1.1); }
        .gx-field label + .gx-checkbox-input { margin-left: 0; }

        .gx-color-field { display: flex; background: #f8fafc; border: 2px solid #f1f5f9; border-radius: 14px; overflow: hidden; height: 52px; }
        .gx-color-field input[type="color"] { width: 52px; height: 100%; border: none; background: none; cursor: pointer; padding: 4px; }
        .gx-color-field input[type="text"] { flex: 1; border: none; background: none; padding: 0 15px; font-family: "JetBrains Mono", monospace; font-size: 13px; font-weight: 600; }

        .gx-range-wrap { display: flex; align-items: center; gap: 20px; }
        .gx-range { flex: 1; height: 8px; background: #e2e8f0; border-radius: 10px; -webkit-appearance: none; cursor: pointer; }
        .gx-range::-webkit-slider-thumb { -webkit-appearance: none; width: 22px; height: 22px; background: #0f172a; border: 4px solid #fff; border-radius: 50%; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); transition: transform 0.2s; }
        .gx-range::-webkit-slider-thumb:hover { transform: scale(1.1); }
        .gx-range-badge { background: #0f172a; color: #fff; font-size: 12px; font-weight: 800; padding: 5px 12px; border-radius: 10px; min-width: 55px; text-align: center; }

        .gx-toggle { display: inline-flex; background: #f1f5f9; padding: 5px; border-radius: 12px; }
        .gx-toggle input { display: none; }
        .gx-toggle label { margin: 0; padding: 8px 20px; font-size: 13px; font-weight: 700; color: #64748b; cursor: pointer; transition: all 0.3s; border-radius: 9px; }
        .gx-toggle input:checked + label { background: #fff; color: #0f172a; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }

        .gx-typo-card { padding: 2.25rem; border-left: 6px solid #0f172a; }
        .gx-typo-label { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 1.5rem; background: #f8fafc; padding: 8px 16px; border-radius: 12px; width: fit-content; border: 1px solid #f1f5f9; }
        .gx-typo-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1.5rem; }

        .hint { display: block; font-size: 12px; color: #94a3b8; margin-top: 8px; line-height: 1.5; font-weight: 500; }

        /* Presets & Panels Premium Overlay */
        .gx-preset-card { 
            background: #fff; border: 1px solid #f1f5f9; border-radius: 20px; 
            padding: 1.5rem; cursor: pointer; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            text-align: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .gx-preset-card:hover { transform: translateY(-8px) scale(1.02); border-color: #0f172a; box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.08); }
        .gx-preset-preview { 
            width: 70px; height: 70px; border-radius: 24px; margin: 0 auto 1.25rem; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 32px; color: #fff; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
        }
        .gx-preset-name { font-weight: 800; font-size: 14px; color: #0f172a; }

        .gx-panel-builder { border-left: 6px solid #3b82f6; }
        .gx-panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; border-bottom: 1px solid #f8fafc; padding-bottom: 1.25rem; }

        /* Universal Toast System */
        #gx-toast-container { position: fixed; top: 20px; right: 20px; z-index: 10000; }
        .gx-toast { 
            background: #0f172a; color: #fff; padding: 16px 28px; border-radius: 16px; 
            font-weight: 700; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); 
            display: flex; align-items: center; gap: 12px; min-width: 300px;
            border: 1px solid rgba(255,255,255,0.1);
            animation: gxToastIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes gxToastIn { from { transform: translateX(120%); } to { transform: translateX(0); } }
        .gx-toast.out { animation: gxToastOut 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes gxToastOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(120%); opacity: 0; } }
        .gx-toast i { color: #22c55e; font-size: 20px; }
        </style>';
    }

    /**
     * Renders the administrative JavaScript logic for OptionsBuilder.
     * Includes tab switching, preset application, and AJAX form persistence.
     */
    private function renderJS(): void
    {
        echo <<<JS
        <script>
        window.showGxToast = function(msg, icon = 'fa-check-circle') {
            let container = document.getElementById('gx-toast-container');
            if(!container) {
                container = document.createElement('div');
                container.id = 'gx-toast-container';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.className = 'gx-toast';
            toast.innerHTML = `<i class="fa \${icon}"></i> <span>\${msg}</span>`;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('out');
                setTimeout(() => toast.remove(), 600);
            }, 4000);
        };

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
            const firstTab = document.querySelector(".gx-nav-item:not([data-tab='tab-presets'])");
            if(firstTab) firstTab.click();
            window.showGxToast(p.name + " applied! Click Save Changes to store.");
        };

        window.initSidebar = function() {
            document.querySelectorAll(".gx-nav-item").forEach(btn => {
                btn.onclick = () => {
                    document.querySelectorAll(".gx-nav-item, .gx-panel").forEach(el => el.classList.remove("active"));
                    btn.classList.add("active");
                    const target = document.getElementById(btn.dataset.tab);
                    if(target) target.classList.add("active");
                    history.replaceState(null, null, "#" + btn.dataset.tab);
                }
            });
            if(window.location.hash) {
                const h = window.location.hash.substring(1);
                const target = document.querySelector(`.gx-nav-item[data-tab="\${h}"]`);
                if(target) target.click();
            }
            
            // AJAX Save Global Logic
            const form = document.getElementById("gxOptionsForm");
            if(form) {
                form.onsubmit = (e) => {
                    e.preventDefault();
                    const btn = document.querySelector(".gx-btn-save");
                    const oldInner = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
                    
                    const formData = new FormData(form);
                    formData.append(btn.name, "y");

                    fetch(window.location.href + '&ajax=1', {
                        method: "POST",
                        body: formData
                    })
                    .then(r => r.text())
                    .then(txt => {
                        btn.disabled = false;
                        btn.innerHTML = oldInner;
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(txt, "text/html");
                        const toastEl = doc.querySelector('.gx-toast span') || doc.getElementById('gx-toast');
                        if(toastEl) {
                            window.showGxToast(toastEl.innerText || toastEl.textContent);
                        } else {
                            window.showGxToast("Settings Saved Successfully!");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        btn.disabled = false;
                        btn.innerHTML = oldInner;
                        window.showGxToast("Error connecting to server.", "fa-exclamation-triangle");
                    });
                };
            }
        };
        
        document.addEventListener("DOMContentLoaded", window.initSidebar);
        </script>
JS;
    }
}
