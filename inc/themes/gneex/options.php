<?php
/**
 * GneeX Latte Theme Options
 * 
 * This file only contains:
 *   1. Default values
 *   2. DB install / save logic
 *   3. The panel schema (what tabs/cards/fields to show)
 * 
 * All rendering is delegated to GxOptionsBuilder.
 */

// OptionsBuilder is now default in core.


// ─────────────────────────────────────────────────────────
// 1. INSTALL DB (first run)
// ─────────────────────────────────────────────────────────
if (isset($_POST['installdb'])) {
    $arr = array(
        'intro_title' => 'Welcome to GneeX Latte',
        'intro_text' => 'A modern magazine theme.',
        'intro_image' => '',
        'background_header' => '',
        'background_featured' => '',
        'background_color_header' => '#0f172a',
        'background_color_footer' => '#0f172a',
        'background_color_featured' => '#1e293b',
        'background_footer' => '',
        'font_color_header' => '#ffffff',
        'font_color_footer' => '#94a3b8',
        'link_color_footer' => '#3b82f6',
        'link_color' => '#3b82f6',
        'link_color_hover' => '#2563eb',
        'body_background_color' => '#f8fafc',
        'content_background_color_body' => '#ffffff',
        'content_border_width' => '1',
        'content_border_color' => '#e2e8f0',
        'content_title_color' => '#0f172a',
        'content_title_color_hover' => '#3b82f6',
        'content_title_size' => '32',
        'list_title_size' => '20',
        'list_title_color' => '#0f172a',
        'content_title_cat_size' => '24',
        'content_font_color_body' => '#334155',
        'sidebar_background_color_header' => '#f8fafc',
        'sidebar_font_color_header' => '#1e293b',
        'sidebar_background_color_body' => '#ffffff',
        'sidebar_font_color_body' => '#334155',
        'sidebar_border_width' => '1',
        'sidebar_border_color' => '#e2e8f0',
        'front_layout' => 'magazine',
        'category_layout' => 'magazine',
        'fullwidth_page' => '',
        'featured_posts' => '',
        'panel_1' => '',
        'panel_1_color' => '#3b82f6',
        'panel_1_font_color' => '#ffffff',
        'panel_1_bg' => '',
        'panel_1_text_color' => '',
        'panel_1_font_family' => 'inherit',
        'panel_1_font_size' => '1',
        'panel_2' => '',
        'panel_2_color' => '#10b981',
        'panel_2_font_color' => '#ffffff',
        'panel_2_bg' => '',
        'panel_2_text_color' => '',
        'panel_2_font_family' => 'inherit',
        'panel_2_font_size' => '1',
        'panel_3' => '',
        'panel_3_color' => '#f59e0b',
        'panel_3_font_color' => '#ffffff',
        'panel_3_bg' => '',
        'panel_3_text_color' => '',
        'panel_3_font_family' => 'inherit',
        'panel_3_font_size' => '1',
        'panel_4' => '',
        'panel_4_color' => '#ef4444',
        'panel_4_font_color' => '#ffffff',
        'panel_4_bg' => '',
        'panel_4_text_color' => '',
        'panel_4_font_family' => 'inherit',
        'panel_4_font_size' => '1',
        'panel_5' => '',
        'panel_5_color' => '#6366f1',
        'panel_5_font_color' => '#ffffff',
        'panel_5_bg' => '',
        'panel_5_text_color' => '',
        'panel_5_font_family' => 'inherit',
        'panel_5_font_size' => '1',
        'social_fb' => '#',
        'social_tw' => '#',
        'social_ig' => '#',
        'social_yt' => '#',
        'show_breadcrumb' => 'yes',
        'logo_width' => '',
        'logo_height' => '50px',
        'custom_css' => '',
        'container_width' => '1280',
        'adsense' => '',
        'analytics' => '',
        // Typography — Global
        'typo_body_font' => '"Inter", sans-serif',
        'typo_body_size' => '16',
        'typo_body_weight' => '400',
        'typo_body_color' => '#334155',
        'typo_h1_font' => 'inherit',
        'typo_h1_size' => '36',
        'typo_h1_weight' => '800',
        'typo_h1_color' => '#0f172a',
        'typo_h2_font' => 'inherit',
        'typo_h2_size' => '28',
        'typo_h2_weight' => '700',
        'typo_h2_color' => '#1e293b',
        'typo_h3_font' => 'inherit',
        'typo_h3_size' => '22',
        'typo_h3_weight' => '700',
        'typo_h3_color' => '#1e293b',
        'typo_h4_font' => 'inherit',
        'typo_h4_size' => '18',
        'typo_h4_weight' => '600',
        'typo_h4_color' => '#1e293b',
        'typo_nav_font' => 'inherit',
        'typo_nav_size' => '14',
        'typo_nav_weight' => '600',
        'typo_nav_color' => '#ffffff',
        'typo_post_title_font' => 'inherit',
        'typo_post_title_size' => '20',
        'typo_post_title_weight' => '700',
        'typo_post_title_color' => '#0f172a',
        'typo_meta_font' => 'inherit',
        'typo_meta_size' => '13',
        'typo_meta_weight' => '400',
        'typo_meta_color' => '#64748b',
        // Typography — Blog Post
        'typo_single_title_font' => 'inherit',
        'typo_single_title_size' => '36',
        'typo_single_title_weight' => '800',
        'typo_single_title_color' => '#ffffff',
        'typo_content_font' => 'inherit',
        'typo_content_size' => '17',
        'typo_content_weight' => '400',
        'typo_content_color' => '#334155',
        'typo_post_meta_font' => 'inherit',
        'typo_post_meta_size' => '14',
        'typo_post_meta_weight' => '400',
        'typo_post_meta_color' => '#64748b',
        // Typography — Extra elements
        'typo_blockquote_font' => 'inherit',
        'typo_blockquote_size' => '18',
        'typo_blockquote_weight' => '400',
        'typo_blockquote_color' => '#475569',
        'typo_breadcrumb_font' => 'inherit',
        'typo_breadcrumb_size' => '13',
        'typo_breadcrumb_weight' => '600',
        'typo_breadcrumb_color' => '#ffffff',
        'typo_comment_title_font' => 'inherit',
        'typo_comment_title_size' => '22',
        'typo_comment_title_weight' => '700',
        'typo_comment_title_color' => '#0f172a',
        'typo_comment_body_font' => 'inherit',
        'typo_comment_body_size' => '15',
        'typo_comment_body_weight' => '400',
        'typo_comment_body_color' => '#334155',
    );
    Options::insert(['gneex_options' => json_encode($arr)]);
}

// ─────────────────────────────────────────────────────────
// 2. SAVE (Handled later after builder init)
// ─────────────────────────────────────────────────────────


// ─────────────────────────────────────────────────────────
// 3. LOAD OPTIONS
// ─────────────────────────────────────────────────────────
if (Gneex::checkDB()) {
    $opt_raw = Options::get('gneex_options');
    $o_db = json_decode($opt_raw, true);
    $defaults = Gneex::getDefaults();
    $o = $defaults;
    if (is_array($o_db)) {
        foreach ($o_db as $k => $v) {
            $o[$k] = $v;
        }
    }

    // ─────────────────────────────────────────────────────
    // 4. PRESETS & PALETTES DATA
    // ─────────────────────────────────────────────────────
    $presets = [
        'ocean' => ['name' => 'Ocean Blue', 'emoji' => '🌊', 'link_color' => '#3b82f6', 'link_color_hover' => '#2563eb', 'body_background_color' => '#f0f9ff', 'content_title_color' => '#0c4a6e', 'background_color_header' => '#0f172a', 'background_color_footer' => '#0f172a', 'content_font_color_body' => '#1e3a5f'],
        'forest' => ['name' => 'Forest Green', 'emoji' => '🌿', 'link_color' => '#10b981', 'link_color_hover' => '#059669', 'body_background_color' => '#f0fdf4', 'content_title_color' => '#064e3b', 'background_color_header' => '#064e3b', 'background_color_footer' => '#022c22', 'content_font_color_body' => '#1d3a2a'],
        'sunset' => ['name' => 'Sunset Orange', 'emoji' => '🌅', 'link_color' => '#f59e0b', 'link_color_hover' => '#d97706', 'body_background_color' => '#fffbeb', 'content_title_color' => '#78350f', 'background_color_header' => '#1c1917', 'background_color_footer' => '#1c1917', 'content_font_color_body' => '#44322b'],
        'cherry' => ['name' => 'Cherry Red', 'emoji' => '🍒', 'link_color' => '#ef4444', 'link_color_hover' => '#dc2626', 'body_background_color' => '#fff1f2', 'content_title_color' => '#881337', 'background_color_header' => '#1c0a0d', 'background_color_footer' => '#1c0a0d', 'content_font_color_body' => '#4a1221'],
        'midnight' => ['name' => 'Midnight', 'emoji' => '🌙', 'link_color' => '#818cf8', 'link_color_hover' => '#6366f1', 'body_background_color' => '#0f172a', 'content_title_color' => '#e2e8f0', 'background_color_header' => '#020617', 'background_color_footer' => '#020617', 'content_font_color_body' => '#94a3b8'],
        'rose' => ['name' => 'Rose Gold', 'emoji' => '🌸', 'link_color' => '#f43f5e', 'link_color_hover' => '#e11d48', 'body_background_color' => '#fff1f2', 'content_title_color' => '#881337', 'background_color_header' => '#4c0519', 'background_color_footer' => '#4c0519', 'content_font_color_body' => '#4a1221'],
    ];

    $panel_palettes = [
        ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
        ['#0ea5e9', '#22c55e', '#eab308', '#f97316', '#a855f7'],
        ['#06b6d4', '#84cc16', '#f59e0b', '#ec4899', '#8b5cf6'],
        ['#14b8a6', '#4ade80', '#fb923c', '#f87171', '#c084fc'],
        ['#1d4ed8', '#15803d', '#b45309', '#b91c1c', '#4338ca'],
    ];

    // ─────────────────────────────────────────────────────
    // 5. PANEL SCHEMA
    // ─────────────────────────────────────────────────────

    $social_fields = [];
    $socials = [
        'social_fb' => ['fa fa-facebook', '#1877f2', 'Facebook'],
        'social_tw' => ['fa fa-twitter', '#1da1f2', 'X / Twitter'],
        'social_ig' => ['fa fa-instagram', '#e1306c', 'Instagram'],
        'social_yt' => ['fa fa-youtube-play', '#ff0000', 'YouTube'],
    ];
    foreach ($socials as $field => [$icon, $color, $label]) {
        $social_fields[] = [
            'type' => 'text',
            'name' => $field,
            'label' => '<span style="background:' . $color . ';width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;margin-right:8px;font-size:13px;vertical-align:middle;"><i class="' . $icon . '"></i></span>' . $label . ' URL',
            'placeholder' => 'https://',
        ];
    }

    $schema = [

        // ── HEADER & NAVIGATION ────────────────────────────────────────────
        [
            'id' => 'tab-header',
            'label' => 'Header & Nav',
            'icon' => 'fa fa-bars',
            'group' => 'General',
            'active' => true,
            'title' => 'Header & Navigation',
            'subtitle' => 'Configure the site header, logo sizing, and breadcrumbs.',
            'cards' => [
                [
                    'title' => 'Site Logo Sizing',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'logo_width', 'label' => 'Logo Width', 'placeholder' => 'e.g. 150px', 'hint' => 'Leave empty to rely on height.'],
                        ['type' => 'text', 'name' => 'logo_height', 'label' => 'Logo Height', 'placeholder' => 'e.g. 50px', 'hint' => 'Recommended: 50px.'],
                    ],
                ],
                [
                    'title' => 'Header Colors',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'background_color_navbar', 'label' => 'Header / Navbar Background', 'hint' => 'Background color for the sticky top menu.'],
                        ['type' => 'color', 'name' => 'typo_nav_color', 'label' => 'Header Menu Link Color', 'hint' => 'Color for the links in the top navigation.'],
                    ],
                ],
                [
                    'title' => 'Navigation & Layout',
                    'cols' => 2,
                    'fields' => [
                        [
                            'type' => 'toggle',
                            'name' => 'show_breadcrumb',
                            'label' => 'Show Breadcrumbs',
                            'yes' => 'Yes, show breadcrumbs',
                            'no' => 'No, hide them'
                        ],
                        [
                            'type' => 'range',
                            'name' => 'container_width',
                            'label' => 'Container Width (px)',
                            'min' => 1000,
                            'max' => 1600,
                            'step' => 10,
                            'unit' => 'px',
                            'id' => 'container_width_val'
                        ],
                    ],
                ],
            ],
        ],

        // ── HERO SECTION ───────────────────────────────────────────────────
        [
            'id' => 'tab-hero',
            'label' => 'Hero Section',
            'icon' => 'fa fa-image',
            'group' => 'General',
            'title' => 'Hero Section',
            'subtitle' => 'Configure the large hero banner on the homepage and inner pages.',
            'cards' => [
                [
                    'title' => 'Hero Content & Buttons',
                    'fields' => [
                        ['type' => 'text', 'name' => 'intro_title', 'label' => 'Hero Title'],
                        ['type' => 'textarea', 'name' => 'intro_text', 'label' => 'Hero Subtitle / Description'],
                        [
                            'type' => 'text',
                            'name' => 'intro_image',
                            'label' => 'Featured Image or YouTube URL',
                            'placeholder' => 'https://www.youtube.com/watch?v=...',
                            'hint' => 'Supports YouTube, Vimeo, Dailymotion, or direct image URL.'
                        ],
                        ['type' => 'raw', 'html' => '<hr>'],
                        ['type' => 'text', 'name' => 'hero_btn_primary_text', 'label' => 'Primary Button Text', 'placeholder' => 'e.g. Start Reading'],
                        ['type' => 'text', 'name' => 'hero_btn_primary_link', 'label' => 'Primary Button Link', 'placeholder' => 'e.g. #blog or https://...'],
                        ['type' => 'text', 'name' => 'hero_btn_secondary_text', 'label' => 'Secondary Button Text', 'placeholder' => 'e.g. Our Story'],
                        ['type' => 'text', 'name' => 'hero_btn_secondary_link', 'label' => 'Secondary Button Link', 'placeholder' => 'e.g. /about or https://...'],
                    ],
                ],
                [
                    'title' => 'Hero Typography',
                    'subtitle' => 'Customize the fonts specifically for the hero banner sections.',
                    'fields' => [
                        ['type' => 'typo_row', 'prefix' => 'typo_hero_title', 'label' => 'Hero Title Typography'],
                        ['type' => 'typo_row', 'prefix' => 'typo_hero_text', 'label' => 'Hero Subtitle/Description Typography'],
                    ],
                ],
                [
                    'title' => 'Hero Background & Colors',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'background_header', 'label' => 'Background Image URL', 'placeholder' => 'Leave empty for solid color'],
                        ['type' => 'color', 'name' => 'background_color_header', 'label' => 'Hero Overlay Color', 'hint' => 'Color overlay on top of the hero image.'],
                        ['type' => 'color', 'name' => 'font_color_header', 'label' => 'Hero Text Color'],
                    ],
                ],
            ],
        ],

        // ── FRONT PAGE ─────────────────────────────────────────────────────
        [
            'id' => 'tab-frontpage',
            'label' => 'Front Page',
            'icon' => 'fa fa-home',
            'group' => 'General',
            'title' => 'Front Page Settings',
            'subtitle' => 'Control how the homepage and category pages look.',
            'cards' => [
                [
                    'title' => 'Layout Strategy',
                    'cols' => 2,
                    'fields' => [
                        [
                            'type' => 'select',
                            'name' => 'front_layout',
                            'label' => 'Homepage Layout',
                            'id' => 'frontpageSelector',
                            'options' => ['blog' => '📰 Blog List', 'magazine' => '🗞️ Magazine Grid', 'fullwidth' => '📄 Full Width Page']
                        ],
                        [
                            'type' => 'select',
                            'name' => 'category_layout',
                            'label' => 'Category / Archive Style',
                            'options' => ['blog' => 'Blog List', 'magazine' => 'Magazine Grid']
                        ],
                        [
                            'type' => 'raw',
                            'html' => '<div id="fullwidth-opt" class="gx-field" style="display:none;grid-column:1/-1;">
                            <label>Select Full Width Page</label>
                            <select name="fullwidth_page" class="gx-input gx-select">' . Gneex::optionPost('page', $o['fullwidth_page'] ?? '') . '</select>
                        </div>'
                        ],
                    ],
                ],
                [
                    'title' => 'Featured Posts Slider',
                    'cols' => 2,
                    'fields' => [
                        [
                            'type' => 'text',
                            'name' => 'featured_posts',
                            'label' => 'Featured Post IDs',
                            'placeholder' => 'e.g. 12, 15, 20',
                            'hint' => 'Comma-separated post IDs. These appear in the top slider above content.'
                        ],
                        ['type' => 'text', 'name' => 'background_featured', 'label' => 'Featured Section BG Image', 'placeholder' => 'Image URL or leave empty'],
                        ['type' => 'color', 'name' => 'background_color_featured', 'label' => 'Featured Section BG Color'],
                    ],
                ],
            ],
        ],

        // ── MAGAZINE PANELS ────────────────────────────────────────────────
        [
            'id' => 'tab-panels',
            'label' => 'Magazine Panels',
            'icon' => 'fa fa-th-large',
            'group' => 'General',
            'type' => 'panels',
            'title' => 'Magazine Panels',
            'subtitle' => 'Assign categories and customize the accent color for each panel block. Applies only in Magazine layout.',
        ],

        // ── COLORS & BODY ──────────────────────────────────────────────────
        [
            'id' => 'tab-colors',
            'label' => 'Colors & Body',
            'icon' => 'fa fa-paint-brush',
            'group' => 'Appearance',
            'title' => 'Colors & Body',
            'subtitle' => 'Control the main site palette, background, and link colors.',
            'cards' => [
                [
                    'title' => 'Primary Color',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'link_color', 'label' => 'Primary / Link Color', 'hint' => 'Used for buttons, links, accents.'],
                        ['type' => 'color', 'name' => 'link_color_hover', 'label' => 'Link Hover Color'],
                    ],
                ],
                [
                    'title' => 'Site Background',
                    'fields' => [
                        ['type' => 'color', 'name' => 'body_background_color', 'label' => 'Body Background Color', 'hint' => 'Background color of the entire page.'],
                    ],
                ],
                [
                    'title' => 'Card / Post Card',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'content_background_color_body', 'label' => 'Card Background Color'],
                        ['type' => 'number', 'name' => 'content_border_width', 'label' => 'Card Border Width (px)', 'min' => 0, 'max' => 10],
                        ['type' => 'color', 'name' => 'content_border_color', 'label' => 'Card Border Color'],
                    ],
                ],
            ],
        ],

        // ── TYPOGRAPHY ─────────────────────────────────────────────────────
        [
            'id' => 'tab-typography',
            'label' => 'Typography',
            'icon' => 'fa fa-font',
            'group' => 'Appearance',
            'title' => 'Typography',
            'subtitle' => 'Control font family, size, weight, and color for every element globally across the site.',
            'sections' => [
                [
                    'cards' => [
                        ['type' => 'typo_row', 'prefix' => 'typo_body', 'label' => 'Body Text'],
                        ['type' => 'typo_row', 'prefix' => 'typo_nav', 'label' => 'Navigation / Menu'],
                        ['type' => 'typo_row', 'prefix' => 'typo_h1', 'label' => 'Heading 1 (H1)'],
                        ['type' => 'typo_row', 'prefix' => 'typo_h2', 'label' => 'Heading 2 (H2)'],
                        ['type' => 'typo_row', 'prefix' => 'typo_h3', 'label' => 'Heading 3 (H3)'],
                        ['type' => 'typo_row', 'prefix' => 'typo_h4', 'label' => 'Heading 4 (H4)'],
                        ['type' => 'typo_row', 'prefix' => 'typo_post_title', 'label' => 'Post / Article Title (List)'],
                        ['type' => 'typo_row', 'prefix' => 'typo_meta', 'label' => 'Meta Text (date, author, etc.)'],
                    ],
                ],
                [
                    'title' => '📄 Blog Post (Single Article)',
                    'subtitle' => 'Typography for the full article page view.',
                    'cards' => [
                        ['type' => 'typo_row', 'prefix' => 'typo_single_title', 'label' => 'Page / Article Title (Hero)'],
                        ['type' => 'typo_row', 'prefix' => 'typo_content', 'label' => 'Article Body Content'],
                        ['type' => 'typo_row', 'prefix' => 'typo_post_meta', 'label' => 'Article Meta (date, category, author)'],
                    ],
                ],
                [
                    'title' => '🧩 Extra Elements',
                    'subtitle' => 'Blockquote, breadcrumb navigation, and comment area styles.',
                    'cards' => [
                        ['type' => 'typo_row', 'prefix' => 'typo_blockquote', 'label' => 'Blockquote'],
                        ['type' => 'typo_row', 'prefix' => 'typo_breadcrumb', 'label' => 'Breadcrumb Navigation'],
                        ['type' => 'typo_row', 'prefix' => 'typo_comment_title', 'label' => 'Comment Section Title'],
                        ['type' => 'typo_row', 'prefix' => 'typo_comment_body', 'label' => 'Comment Body Text'],
                    ],
                ],
            ],
        ],

        // ── FOOTER ─────────────────────────────────────────────────────────
        [
            'id' => 'tab-footer',
            'label' => 'Footer',
            'icon' => 'fa fa-map',
            'group' => 'Appearance',
            'title' => 'Footer',
            'subtitle' => 'Style the footer background, text, and links.',
            'cards' => [
                [
                    'title' => 'Footer Background',
                    'fields' => [
                        ['type' => 'text', 'name' => 'background_footer', 'label' => 'Background Image URL (optional)', 'placeholder' => 'Leave empty for solid color'],
                        ['type' => 'color', 'name' => 'background_color_footer', 'label' => 'Footer Background Color'],
                    ],
                ],
                [
                    'title' => 'Footer Text & Links',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'font_color_footer', 'label' => 'Footer Text Color'],
                        ['type' => 'color', 'name' => 'link_color_footer', 'label' => 'Footer Link Color'],
                    ],
                ],
                [
                    'title' => '',
                    'fields' => [
                        [
                            'type' => 'raw',
                            'html' =>
                                '<div class="gx-card" style="background:' . $o['background_color_footer'] . ';border-color:transparent;" id="footerPreview">
                                <div style="font-size:12px;font-weight:700;opacity:.5;color:' . $o['font_color_footer'] . ';text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Footer Preview</div>
                                <div style="color:' . $o['font_color_footer'] . ';font-size:14px;">Footer text looks like this.</div>
                                <div style="color:' . $o['link_color_footer'] . ';font-size:13px;margin-top:6px;font-weight:600;">Footer link color →</div>
                            </div>'
                        ],
                    ],
                ],
            ],
        ],

        // ── SIDEBAR & WIDGETS ──────────────────────────────────────────────
        [
            'id' => 'tab-sidebar',
            'label' => 'Sidebar',
            'icon' => 'fa fa-indent',
            'group' => 'Appearance',
            'title' => 'Sidebar & Widgets',
            'subtitle' => 'Customize sidebar widget header and body colors.',
            'cards' => [
                [
                    'title' => 'Widget Header',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'sidebar_background_color_header', 'label' => 'Header Background'],
                        ['type' => 'color', 'name' => 'sidebar_font_color_header', 'label' => 'Header Font Color'],
                    ],
                ],
                [
                    'title' => 'Widget Body',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'sidebar_background_color_body', 'label' => 'Body Background'],
                        ['type' => 'color', 'name' => 'sidebar_font_color_body', 'label' => 'Body Font Color'],
                    ],
                ],
                [
                    'title' => 'Widget Border',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'number', 'name' => 'sidebar_border_width', 'label' => 'Border Width (px)', 'min' => 0, 'max' => 10],
                        ['type' => 'color', 'name' => 'sidebar_border_color', 'label' => 'Border Color'],
                    ],
                ],
            ],
        ],

        // ── CONTENT STYLE ──────────────────────────────────────────────────
        [
            'id' => 'tab-content',
            'label' => 'Content Style',
            'icon' => 'fa fa-file-text-o',
            'group' => 'Appearance',
            'title' => 'Content Style',
            'subtitle' => 'Typography sizes and colors for posts, titles, and body text.',
            'cards' => [
                [
                    'title' => 'Headings & Titles',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'content_title_color', 'label' => 'Global Title Color'],
                        ['type' => 'color', 'name' => 'content_title_color_hover', 'label' => 'Title Hover Color'],
                        [
                            'type' => 'range',
                            'name' => 'content_title_size',
                            'label' => 'Post Title Size (single page)',
                            'min' => 20,
                            'max' => 56,
                            'step' => 1,
                            'unit' => 'px',
                            'id' => 'ct_size_val'
                        ],
                        [
                            'type' => 'range',
                            'name' => 'list_title_size',
                            'label' => 'List / Archive Title Size',
                            'min' => 16,
                            'max' => 44,
                            'step' => 1,
                            'unit' => 'px',
                            'id' => 'lt_size_val'
                        ],
                        ['type' => 'color', 'name' => 'list_title_color', 'label' => 'Index / List Title Color'],
                        [
                            'type' => 'range',
                            'name' => 'content_title_cat_size',
                            'label' => 'Category Title Size',
                            'min' => 14,
                            'max' => 40,
                            'step' => 1,
                            'unit' => 'px',
                            'id' => 'cat_size_val',
                            'default' => 24
                        ],
                    ],
                ],
                [
                    'title' => 'Body Text',
                    'fields' => [
                        [
                            'type' => 'color',
                            'name' => 'content_font_color_body',
                            'label' => 'Body Text Color',
                            'hint' => 'Color for article body text and descriptions.'
                        ],
                    ],
                ],
            ],
        ],

        // ── SOCIAL LINKS ───────────────────────────────────────────────────
        [
            'id' => 'tab-social',
            'label' => 'Social Links',
            'icon' => 'fa fa-share-alt',
            'group' => 'Extra',
            'title' => 'Social Links',
            'subtitle' => 'Set your social media URLs. These appear in the footer and hero sections.',
            'cards' => [
                ['title' => '', 'fields' => $social_fields],
            ],
        ],

        // ── ADVANCED ───────────────────────────────────────────────────────
        [
            'id' => 'tab-advanced',
            'label' => 'Advanced',
            'icon' => 'fa fa-code',
            'group' => 'Extra',
            'title' => 'Advanced',
            'subtitle' => 'Ads, analytics, and custom CSS. For developers.',
            'cards' => [
                [
                    'title' => 'Advertising',
                    'fields' => [
                        ['type' => 'textarea', 'name' => 'adsense', 'label' => 'Google Adsense Unit'],
                    ],
                ],
                [
                    'title' => 'Analytics',
                    'fields' => [
                        [
                            'type' => 'textarea',
                            'name' => 'analytics',
                            'label' => 'Analytics Tracking Script',
                            'hint' => 'Paste your full Google Analytics or similar script tag here.'
                        ],
                    ],
                ],
                [
                    'title' => '',
                    'fields' => [
                        [
                            'type' => 'raw',
                            'html' => '<div class="gx-card" style="border-color:#fbbf24;">
                            <div class="gx-card-title" style="color:#92400e;">Developer: Custom CSS Override</div>
                            <div class="gx-field">
                                <label>Custom CSS</label>
                                <textarea name="custom_css" class="gx-input" rows="10" style="font-family:\'JetBrains Mono\',monospace;font-size:13px;" placeholder="/* Write your custom CSS here */">'
                                . htmlspecialchars($o['custom_css'] ?? '') . '</textarea>
                                <span class="hint">⚠️ CSS written here is injected directly into the &lt;head&gt;. Use with care.</span>
                            </div>
                        </div>'
                        ],
                    ],
                ],
            ],
        ],

    ]; // end $schema

    // ─────────────────────────────────────────────────────
    // 6. RENDER
    // ─────────────────────────────────────────────────────
    $config = [
        'brandName' => 'GneeX',
        'brandVer' => 'v2.1.1',
        'brandAbbr' => 'GL',
        'brandIcon' => 'fa fa-leaf',      // or any Font Awesome icon
        'brandColor' => '#10b981',         // custom brand color (emerald green)
        'saveKey' => 'gneex_options_update',
    ];
    $builder = new OptionsBuilder($o, $presets, $panel_palettes, $config);

    // Save Logic
    if (isset($_POST['gneex_options_update'])) {
        unset($_POST['gneex_options_update']);
        $builder->renderCSS();
        $data = [];
        foreach ($_POST as $k => $v) {
            $data[$k] = $v;
        }
        Options::update('gneex_options', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS));
        echo '<div id="gx-toast"><i class="fa fa-check-circle"></i> GneeX Settings saved!</div>';
        echo '<script>setTimeout(() => document.getElementById("gx-toast")?.remove(), 4000);</script>';
    }

    $builder->render($schema);

} else {
    // DB not initialized yet
    echo '<div style="text-align:center;padding:80px;background:#fff;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,.06);">
        <div style="font-size:48px;margin-bottom:16px;">🚀</div>
        <h3 style="font-weight:800;color:#0f172a;">Database Initialization Required</h3>
        <p style="color:#64748b;">The theme options table is not yet set up for GneeX Latte.</p>
        <form method="post"><button type="submit" name="installdb" class="gx-btn-save" style="margin:20px auto;display:inline-flex;">Initialize Theme Engine</button></form>
    </div>';
}
