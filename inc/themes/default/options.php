<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

if (!User::access(0)) {
    Control::error('noaccess');
    return;
}

// ── 1. Initialization ──────────────────────────────────────────────
$saveKey = 'default_theme_options_update';
$optionKey = 'default_theme_options_v2'; // New unified key for OptionsBuilder

// Handle installation of initial options if not exists
if (!Options::isExist($optionKey)) {
    $initial = [
        'link_color' => '#0085A1',
        'navbar_bg' => '#ffffff',
        'footer_bg' => '#f8f9fa',
        'hero_bg' => '',
        'social_fb' => '',
        'social_tw' => '',
        'social_gh' => '',
        'mdo_adsense' => '',
        'mdo_analytics' => '',
        'custom_css' => '',
        'typo_body_font' => '"Inter", sans-serif',
        'typo_h1_font' => '"Gloock", serif'
    ];
    Options::insert([$optionKey => json_encode($initial)]);
}

// ── 2. Schema Definition ───────────────────────────────────────────
$optJson = Options::get($optionKey);
$opt = json_decode($optJson, true) ?: [];

$builder = new OptionsBuilder($opt, [], [], [
    'brandName' => 'Default Latte',
    'brandVer' => 'v2.0',
    'brandAbbr' => 'DL',
    'brandColor' => '#0085A1',
    'saveKey' => $saveKey
]);

// Handle Saving
if (isset($_POST[$saveKey])) {
    $builder->renderCSS(); // Ensure toast style is loaded
    $data = [];
    unset($_POST[$saveKey], $_POST['token']);
    $updates = [];
    foreach ($_POST as $k => $v) {
        $updates[$k] = $v;
    }
    if (Options::update($optionKey, json_encode($updates))) {
        echo '<div id="gx-toast"><i class="fa fa-check-circle"></i> Options updated!</div>';
        echo '<script>setTimeout(() => document.getElementById("gx-toast")?.remove(), 4000);</script>';
    } else {
        echo '<div id="gx-toast" style="background:#ef4444;"><i class="fa fa-exclamation-triangle"></i> Failed to save options.</div>';
        echo '<script>setTimeout(() => document.getElementById("gx-toast")?.remove(), 4000);</script>';
    }
}

$schema = [
    [
        'id' => 'general',
        'label' => 'General Settings',
        'icon' => 'fa fa-home',
        'title' => 'Core Branding & Layout',
        'subtitle' => 'Manage your website identity and visual foundation.',
        'cards' => [
            [
                'title' => 'Site Assets',
                'cols' => 1,
                'fields' => [
                    ['type' => 'text', 'name' => 'hero_bg', 'label' => 'Hero Background Image URL', 'placeholder' => 'https://example.com/hero.jpg', 'hint' => 'Full URL to the image used in the site header background.'],
                    ['type' => 'number', 'name' => 'logo_height', 'label' => 'Logo Height (px)', 'min' => 20, 'max' => 200, 'default' => 40, 'hint' => 'Set the logo height in the navbar. Default is 40.'],
                ]
            ],
            [
                'title' => 'Social Connections',
                'cols' => 3,
                'fields' => [
                    ['type' => 'text', 'name' => 'social_fb', 'label' => 'Facebook URL', 'placeholder' => 'https://facebook.com/...'],
                    ['type' => 'text', 'name' => 'social_tw', 'label' => 'Twitter URL', 'placeholder' => 'https://twitter.com/...'],
                    ['type' => 'text', 'name' => 'social_gh', 'label' => 'GitHub URL', 'placeholder' => 'https://github.com/...'],
                ]
            ]
        ]
    ],
    [
        'id' => 'colors',
        'label' => 'Design & Colors',
        'icon' => 'fa fa-palette',
        'group' => 'Appearance',
        'title' => 'Theme Color System',
        'subtitle' => 'Define the primary palette and regional colors of the theme.',
        'cards' => [
            [
                'title' => 'Primary Accents',
                'cols' => 2,
                'fields' => [
                    ['type' => 'color', 'name' => 'link_color', 'label' => 'Primary Brand Color', 'hint' => 'Used for links, buttons, and decorative icons.'],
                    ['type' => 'color', 'name' => 'link_color_hover', 'label' => 'Hover/Active Color'],
                ]
            ],
            [
                'title' => 'Component Backgrounds',
                'cols' => 3,
                'fields' => [
                    ['type' => 'color', 'name' => 'background_color_navbar', 'label' => 'Navbar Background'],
                    ['type' => 'color', 'name' => 'body_background_color', 'label' => 'Body Background', 'default' => '#f8f9fa'],
                    ['type' => 'color', 'name' => 'background_color_footer', 'label' => 'Footer Background'],
                ]
            ]
        ]
    ],
    [
        'id' => 'typography',
        'label' => 'Typography',
        'icon' => 'fa fa-font',
        'group' => 'Appearance',
        'title' => 'Font Orchestration',
        'subtitle' => 'Customize the typefaces for various parts of your website.',
        'cards' => [
            ['type' => 'typo_row', 'label' => 'Main Body Text', 'prefix' => 'typo_body'],
            ['type' => 'typo_row', 'label' => 'Headline H1', 'prefix' => 'typo_h1'],
            ['type' => 'typo_row', 'label' => 'Blog Post Titles', 'prefix' => 'typo_post_title'],
        ]
    ],
    [
        'id' => 'advertising',
        'label' => 'Monetization',
        'icon' => 'fa fa-dollar-sign',
        'group' => 'Integration',
        'title' => 'Ads & Analytics',
        'subtitle' => 'Paste your tracking and ad scripts here.',
        'cards' => [
            [
                'title' => 'Scripts',
                'fields' => [
                    ['type' => 'textarea', 'name' => 'mdo_analytics', 'label' => 'Analytics Snippet', 'rows' => 6, 'hint' => 'Paste your Google Analytics or other tracking code.'],
                    ['type' => 'textarea', 'name' => 'mdo_adsense', 'label' => 'AdSense / Global Ads', 'rows' => 6, 'hint' => 'Ad units placed in designated theme areas.'],
                ]
            ]
        ]
    ],
    [
        'id' => 'advanced',
        'label' => 'Advanced CSS',
        'icon' => 'fa fa-code',
        'group' => 'Integration',
        'title' => 'Custom CSS Overrides',
        'subtitle' => 'Add custom CSS rules to fine-tune the theme without editing files.',
        'cards' => [
            [
                'fields' => [
                    ['type' => 'textarea', 'name' => 'custom_css', 'label' => 'Custom Styles', 'rows' => 12, 'placeholder' => '.my-class { color: red; }'],
                ]
            ]
        ]
    ]
];

// ── 3. Internal Render ─────────────────────────────────────────────
$builder->render($schema);
?>
