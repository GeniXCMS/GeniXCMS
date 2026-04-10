<?php
/**
 * The Musk Theme Options
 */

// OptionsBuilder is now default in core.


if (isset($_POST['installdb'])) {
    $arr = Themusk::getDefaults();
    Options::insert(['themusk_options' => json_encode($arr)]);
}

// Handle Saving
if (isset($_POST['themusk_options_update'])) {
    // Handled below after builder init
}

if (Themusk::checkDB()) {
    $opt_raw  = Options::get('themusk_options');
    $o_db     = json_decode($opt_raw, true);
    $defaults = Themusk::getDefaults();
    $o = $defaults;
    if (is_array($o_db)) {
        foreach ($o_db as $k => $v) { $o[$k] = $v; }
    }

    $presets = [
        'default' => ['name'=>'Default Green', 'emoji'=>'🌿', 'primary_color'=>'#45655b', 'on_primary_color'=>'#defff3', 'bg_surface'=>'#f9f9fc', 'on_surface'=>'#2d3339', 'on_surface_variant'=>'#596066'],
        'ocean' => ['name'=>'Ocean Blue', 'emoji'=>'🌊', 'primary_color'=>'#2563eb', 'on_primary_color'=>'#eff6ff', 'bg_surface'=>'#f0f9ff', 'on_surface'=>'#1e3a8a', 'on_surface_variant'=>'#3b82f6'],
    ];

    $panel_palettes = [
        ['#3b82f6','#10b981','#f59e0b','#ef4444','#6366f1'],
    ];

    $schema = [
        [
            'id'       => 'tab-general',
            'label'    => 'General Settings',
            'icon'     => 'fa fa-cog',
            'group'    => 'General',
            'title'    => 'General Settings',
            'subtitle' => 'Configure the site appearance.',
            'cards'    => [
                [
                    'title'  => 'Hero Content & Buttons',
                    'fields' => [
                        ['type'=>'text',     'name'=>'intro_title', 'label'=>'Hero Title'],
                        ['type'=>'textarea', 'name'=>'intro_text',  'label'=>'Hero Subtitle / Description'],
                        ['type'=>'text',     'name'=>'default_image', 'label'=>'Default Post Image (URL)', 'hint'=>'Shown when a post has no featured image.'],
                        ['type'=>'text',     'name'=>'featured_posts','label'=>'Featured Post IDs', 'hint'=>'Comma separated IDs, e.g. 1,2,5. Will be displayed as a slider on the homepage.'],
                    ],
                ],
                [
                    'title'  => 'Signature Quote',
                    'fields' => [
                        ['type'=>'textarea', 'name'=>'quote_text',   'label'=>'Quote Text'],
                        ['type'=>'text',     'name'=>'quote_author', 'label'=>'Quote Author'],
                    ],
                ],
                [
                    'title'  => 'Colors',
                    'cols'   => 2,
                    'fields' => [
                        ['type'=>'color', 'name'=>'primary_color', 'label'=>'Primary Color'],
                        ['type'=>'color', 'name'=>'on_primary_color', 'label'=>'On Primary Color'],
                        ['type'=>'color', 'name'=>'bg_surface', 'label'=>'Surface Background Color'],
                        ['type'=>'color', 'name'=>'on_surface', 'label'=>'On Surface Color'],
                        ['type'=>'color', 'name'=>'on_surface_variant', 'label'=>'On Surface Variant Color'],
                    ],
                ],
            ],
        ],
        [
            'id'       => 'tab-typography',
            'label'    => 'Typography',
            'icon'     => 'fa fa-font',
            'group'    => 'General',
            'title'    => 'Typography',
            'subtitle' => 'Control font family, size, weight, and color for every element.',
            'sections' => [
                [
                    'cards' => [
                        ['type'=>'typo_row', 'prefix'=>'typo_p',          'label'=>'Body Text (P)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_li',         'label'=>'List Items (LI)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_h1',         'label'=>'Heading 1 (H1)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_h2',         'label'=>'Heading 2 (H2)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_h3',         'label'=>'Heading 3 (H3)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_h4',         'label'=>'Heading 4 (H4)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_h5',         'label'=>'Heading 5 (H5)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_h6',         'label'=>'Heading 6 (H6)'],
                        ['type'=>'typo_row', 'prefix'=>'typo_code',       'label'=>'Code / Monospace'],
                        ['type'=>'typo_row', 'prefix'=>'typo_blockquote', 'label'=>'Blockquote'],
                    ],
                ],
            ],
        ],
        [
            'id'       => 'tab-layout',
            'label'    => 'Layout',
            'icon'     => 'fa fa-th-large',
            'group'    => 'General',
            'title'    => 'Layout Settings',
            'subtitle' => 'Manage the structural dimensions of your site.',
            'cards'    => [
                [
                    'title'  => 'Main Container',
                    'fields' => [
                        ['type'=>'number', 'name'=>'layout_site_width', 'label'=>'Max Site Width (px)', 'hint'=>'Max width for the main content container.'],
                        ['type'=>'number', 'name'=>'layout_container_padding', 'label'=>'Container Padding (px)', 'hint'=>'Left & right padding for the site container.'],
                    ],
                ],
                [
                    'title'  => 'Section Spacing',
                    'fields' => [
                        ['type'=>'number', 'name'=>'layout_header_py', 'label'=>'Header Vertical Padding (px)'],
                        ['type'=>'number', 'name'=>'layout_footer_py', 'label'=>'Footer Vertical Padding (px)'],
                    ],
                ],
                [
                    'title'  => 'Content Width',
                    'fields' => [
                        ['type'=>'number', 'name'=>'layout_post_content_width', 'label'=>'Post Content Width (px)', 'hint'=>'The width of the article body on single post pages.'],
                    ],
                ],
            ],
        ],
        [
            'id'       => 'tab-advanced',
            'label'    => 'Advanced',
            'icon'     => 'fa fa-code',
            'group'    => 'Extra',
            'title'    => 'Advanced',
            'subtitle' => 'Ads, analytics, and custom CSS. For developers.',
            'cards'    => [
                [
                    'title'  => 'Advertising & Analytics',
                    'fields' => [
                        ['type'=>'textarea', 'name'=>'adsense', 'label'=>'Google Adsense Unit'],
                        ['type'=>'textarea', 'name'=>'analytics', 'label'=>'Analytics Tracking Script',
                         'hint'=>'Paste your full Google Analytics or similar script tag here.'],
                    ],
                ],
                [
                    'title'  => '',
                    'fields' => [
                        ['type'=>'raw', 'html'=>'<div class="gx-card" style="border-color:#fbbf24;">
                            <div class="gx-card-title" style="color:#92400e;">Developer: Custom CSS Override</div>
                            <div class="gx-field">
                                <label>Custom CSS</label>
                                <textarea name="custom_css" class="gx-input" rows="10" style="font-family:\'JetBrains Mono\',monospace;font-size:13px;" placeholder="/* Write your custom CSS here */">'
                                . htmlspecialchars($o['custom_css'] ?? '') . '</textarea>
                                <span class="hint">⚠️ CSS written here is injected directly into the &lt;head&gt;. Use with care.</span>
                            </div>
                        </div>'],
                    ],
                ],
            ],
        ],
    ];

    $config = [
        'brandName'  => 'The Musk Theme',
        'brandVer'   => 'v1.0.2',
        'brandAbbr'  => 'TM',
        'brandIcon'  => 'fa fa-book',
        'brandColor' => '#45655b',
        'saveKey'    => 'themusk_options_update',
    ];
    $builder = new OptionsBuilder($o, $presets, $panel_palettes, $config);

    // Save Logic
    if (isset($_POST['themusk_options_update'])) {
        unset($_POST['themusk_options_update']);
        $builder->renderCSS();
        $data = [];
        foreach ($_POST as $k => $v) { $data[$k] = $v; }
        Options::update('themusk_options', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS));
        echo '<div id="gx-toast"><i class="fa fa-check-circle"></i> The Musk Settings saved!</div>';
        echo '<script>setTimeout(() => document.getElementById("gx-toast")?.remove(), 4000);</script>';
    }

    $builder->render($schema);

} else {
    // DB not initialized yet
    echo '<div style="text-align:center;padding:80px;background:#fff;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,.06);">
        <div style="font-size:48px;margin-bottom:16px;">🚀</div>
        <h3 style="font-weight:800;color:#0f172a;">Database Initialization Required</h3>
        <p style="color:#64748b;">The theme options table is not yet set up for The Musk Theme.</p>
        <form method="post"><button type="submit" name="installdb" class="gx-btn-save" style="margin:20px auto;display:inline-flex;">Initialize Theme Engine</button></form>
    </div>';
}
