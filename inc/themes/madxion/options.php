<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

if (!User::access(0)) {
    Control::error('noaccess');
    return;
}

// ── 1. Initialization ──────────────────────────────────────────────
$saveKey = 'madxion_theme_options_update';
$optionKey = 'madxion_theme_options_v1';

// Handle installation of initial options if not exists
if (!Options::isExist($optionKey)) {
    $initial = [
        // Brand & General
        'site_logo' => '',
        'hero_section_label' => '01 // THE DIGITAL FRONTIER',
        'hero_headline' => 'Revolutionizing IT Solutions',
        'hero_headline_accent' => 'for the Digital Age.',
        'hero_description' => 'We architect elite digital infrastructures that transform complex challenges into competitive advantages. Powering the next generation of global enterprises.',
        'hero_cta_primary_label' => 'Get Started',
        'hero_cta_primary_url' => '#contact',
        'hero_cta_secondary_label' => 'View Ecosystem',
        'hero_cta_secondary_url' => '#ecosystem',
        'hero_section_image' => '',
        'hero_bg_image' => '',
        'hero_bg_enable' => 'on',
        'expertise_label' => '02 // CORE DOMAINS',
        'expertise_title' => 'Our Expertise',
        'expertise_description' => 'Deep technical mastery across the full stack of modern enterprise infrastructure.',
        'expertise_card_1_icon' => 'terminal',
        'expertise_card_1_title' => 'Intelligent Software',
        'expertise_card_1_text' => 'Custom-built applications designed for high performance, scalability, and seamless user experiences across platforms.',
        'expertise_card_1_image' => '',
        'expertise_card_2_icon' => 'cloud_done',
        'expertise_card_2_title' => 'Cloud Infrastructure',
        'expertise_card_2_text' => 'Cloud-native solutions that provide elastic scalability and global distribution for massive datasets.',
        'expertise_card_3_icon' => 'shield_lock',
        'expertise_card_3_title' => 'Cyber Sentinel',
        'expertise_card_3_text' => 'Military-grade encryption and proactive threat hunting to protect your organization\'s most vital assets.',
        'expertise_card_4_icon' => 'insights',
        'expertise_card_4_title' => 'Predictive Analytics',
        'expertise_card_4_text' => 'Turning raw data into actionable intelligence through advanced AI modeling and real-time processing.',
        'expertise_card_4_image' => '',
        'advantage_label' => '03 // THE MADXION ADVANTAGE',
        'advantage_title' => 'Why Madxion?',
        'advantage_item_1_title' => 'The Kinetic Monolith Architecture',
        'advantage_item_1_text' => 'Our proprietary design framework ensures every system is stable as a monolith but fluid as kinetic energy.',
        'advantage_item_2_title' => 'Radical Transparency',
        'advantage_item_2_text' => 'Full visibility into every line of code, every cloud node, and every security protocol we implement.',
        'advantage_item_3_title' => 'Human-Centric Reliability',
        'advantage_item_3_text' => 'Technology is built by humans for humans. We prioritize intuitive operation for complex systems.',
        'validation_label' => '04 // VALIDATION',
        'validation_title' => 'Success Stories',
        'validation_description' => 'Real results from real clients.',
        'validation_testimonial_1_stat' => '300%',
        'validation_testimonial_1_stat_label' => 'Scale Acceleration',
        'validation_testimonial_1_quote' => 'Madxion didn\'t just fix our IT; they rebuilt our digital DNA. We scaled from regional to global in 18 months.',
        'validation_testimonial_1_name' => 'Jameson Dovrak',
        'validation_testimonial_1_position' => 'CTO, Nexus Dynamics',
        'validation_testimonial_1_initials' => 'JD',
        'validation_testimonial_1_color' => 'primary',
        'validation_testimonial_2_stat' => 'Zero',
        'validation_testimonial_2_stat_label' => 'Security Breaches',
        'validation_testimonial_2_quote' => 'Their security protocols are unmatched. We feel fortified for the first time in our company\'s history.',
        'validation_testimonial_2_name' => 'Elena Laurent',
        'validation_testimonial_2_position' => 'Head of Security, FinSafe',
        'validation_testimonial_2_initials' => 'EL',
        'validation_testimonial_2_color' => 'secondary',
        'validation_testimonial_3_stat' => '40ms',
        'validation_testimonial_3_stat_label' => 'Global Latency',
        'validation_testimonial_3_quote' => 'The edge computing solution provided by Madxion revolutionized our user retention rates overnight.',
        'validation_testimonial_3_name' => 'Marcus Kane',
        'validation_testimonial_3_position' => 'CEO, StreamVault',
        'validation_testimonial_3_initials' => 'MK',
        'validation_testimonial_3_color' => 'white',
        'posts_section_title' => 'Latest Insights',
        'posts_section_subtitle' => 'Stay updated with our latest thoughts and industry insights.',
        'posts_limit' => '6',
        'expertise_card_border_opacity' => '5',
        'expertise_card_1_border_color' => 'white',
        'expertise_card_1_border_opacity' => '5',
        'expertise_card_1_border_hover_opacity' => '30',
        'expertise_card_1_bg_color' => 'surface',
        'expertise_card_2_border_color' => 'white',
        'expertise_card_2_border_opacity' => '5',
        'expertise_card_2_border_hover_opacity' => '0',
        'expertise_card_2_bg_color' => 'surface-container-high',
        'expertise_card_3_border_color' => 'white',
        'expertise_card_3_border_opacity' => '5',
        'expertise_card_3_border_hover_opacity' => '0',
        'expertise_card_3_bg_color' => 'surface-container-high',
        'expertise_card_4_border_color' => 'white',
        'expertise_card_4_border_opacity' => '5',
        'expertise_card_4_border_hover_opacity' => '30',
        'expertise_card_4_bg_color' => 'surface',
        'validation_card_border_opacity' => '5',
        'validation_card_1_border_color' => 'white',
        'validation_card_1_border_opacity' => '5',
        'validation_card_1_border_hover_opacity' => '0',
        'validation_card_1_bg_color' => 'surface-container-low',
        'validation_card_2_border_color' => 'white',
        'validation_card_2_border_opacity' => '5',
        'validation_card_2_border_hover_opacity' => '0',
        'validation_card_2_bg_color' => 'surface-container-low',
        'validation_card_3_border_color' => 'white',
        'validation_card_3_border_opacity' => '5',
        'validation_card_3_border_hover_opacity' => '0',
        'validation_card_3_bg_color' => 'surface-container-low',
        'posts_card_border_color' => 'primary-container',
        'posts_card_border_opacity' => '20',
        'posts_card_border_hover_opacity' => '100',
        'posts_card_bg_color' => 'surface-container-low',
        'hero_image_border_enable' => 'off',
        'hero_image_border_color' => 'white',
        'hero_image_border_opacity' => '5',
        'header_cta_label' => 'Get in Touch',
        'header_cta_url' => '#contact',
        
        // Primary Colors
        'primary_color' => '#ffb4a8',
        'primary_container' => '#d20000',
        'secondary_color' => '#ffb956',
        'secondary_container' => '#ca8500',
        
        // Surface & Background
        'background_color' => '#131313',
        'surface_color' => '#131313',
        'surface_container_low' => '#1c1b1b',
        'surface_container' => '#201f1f',
        'surface_container_high' => '#2a2a2a',
        
        // Text Colors
        'text_color' => '#e5e2e1',
        'text_variant' => '#e8bcb5',
        
        // Typography
        'typo_headline_font' => '"Space Grotesk", sans-serif',
        'typo_body_font' => '"Manrope", sans-serif',
        'typo_label_font' => '"Manrope", sans-serif',
        'typo_h1_size' => '48px',
        'typo_h2_size' => '36px',
        'typo_h3_size' => '28px',
        
        // Social Links
        'social_fb' => '',
        'social_tw' => '',
        'social_gh' => '',
        'social_li' => '',
        'social_ig' => '',
        
        // Features Toggle
        'show_hero_pattern' => 'on',
        'enable_blur_effect' => 'on',
        'enable_animations' => 'on',
        'navbar_transparent' => 'on',
        
        // Advanced
        'mdo_analytics' => '',
        'mdo_adsense' => '',
        'custom_css' => ''
    ];
    Options::insert([$optionKey => json_encode($initial)]);
}

// ── 2. Schema Definition ───────────────────────────────────────────
$optJson = Options::get($optionKey);
$opt = json_decode($optJson, true) ?: [];

$builder = new OptionsBuilder($opt, [], [], [
    'brandName' => 'Madxion',
    'brandVer' => 'v1.0',
    'brandAbbr' => 'MDX',
    'brandColor' => '#d20000',
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
        echo '<div id="gx-toast"><i class="fa fa-check-circle"></i> Theme options updated successfully!</div>';
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
        'icon' => 'fa fa-cog',
        'title' => 'Brand & Layout',
        'subtitle' => 'Configure your site branding and main layout options.',
        'cards' => [
            [
                'title' => 'Branding',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'site_logo', 'label' => 'Logo/Brand Image URL', 'placeholder' => 'https://example.com/logo.png', 'hint' => 'Full URL to your logo or brand image'],
                    ['type' => 'text', 'name' => 'header_cta_label', 'label' => 'Header CTA Label', 'placeholder' => 'Get in Touch', 'hint' => 'Text for the header call-to-action button'],
                    ['type' => 'text', 'name' => 'header_cta_url', 'label' => 'Header CTA URL', 'placeholder' => '#contact', 'hint' => 'Link URL for the header CTA button'],
                ]
            ],
            [
                'title' => 'Navbar & Visual Effects',
                'cols' => 3,
                'fields' => [
                    ['type' => 'toggle', 'name' => 'navbar_transparent', 'label' => 'Transparent Navbar', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Make navbar translucent on scroll.'],
                ]
            ],
            [
                'title' => 'Features',
                'cols' => 2,
                'fields' => [
                    ['type' => 'toggle', 'name' => 'enable_animations', 'label' => 'Enable Animations', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Enable page animation effects.'],
                    ['type' => 'toggle', 'name' => 'navbar_transparent', 'label' => 'Transparent Navbar', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Make navbar translucent on scroll.'],
                ]
            ]
        ]
    ],
    [
        'id' => 'hero',
        'label' => 'Hero Settings',
        'icon' => 'fa fa-rocket',
        'group' => 'Appearance',
        'title' => 'Hero Section',
        'subtitle' => 'Configure the home page hero layout and copy to match the Madxion homepage.',
        'cards' => [
            [
                'title' => 'Hero Content',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'hero_section_label', 'label' => 'Hero Label', 'placeholder' => '01 // THE DIGITAL FRONTIER', 'hint' => 'Upper label text shown above the main headline.'],
                    ['type' => 'text', 'name' => 'hero_headline', 'label' => 'Hero Headline', 'placeholder' => 'Revolutionizing IT Solutions', 'hint' => 'Main hero headline text.'],
                    ['type' => 'text', 'name' => 'hero_headline_accent', 'label' => 'Headline Accent', 'placeholder' => 'for the Digital Age.', 'hint' => 'Accent text shown on the second line of the hero headline.'],
                    ['type' => 'textarea', 'name' => 'hero_description', 'label' => 'Hero Description', 'rows' => 4, 'placeholder' => 'We architect elite digital infrastructures that transform complex challenges into competitive advantages. Powering the next generation of global enterprises.', 'hint' => 'Paragraph text below the hero headline.'],
                ]
            ],
            [
                'title' => 'Hero Actions',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'hero_cta_primary_label', 'label' => 'Primary CTA Label', 'placeholder' => 'Get Started', 'hint' => 'Text for the primary hero button.'],
                    ['type' => 'text', 'name' => 'hero_cta_primary_url', 'label' => 'Primary CTA URL', 'placeholder' => '#contact', 'hint' => 'Link for the primary hero button.'],
                    ['type' => 'text', 'name' => 'hero_cta_secondary_label', 'label' => 'Secondary CTA Label', 'placeholder' => 'View Ecosystem', 'hint' => 'Text for the secondary hero button.'],
                    ['type' => 'text', 'name' => 'hero_cta_secondary_url', 'label' => 'Secondary CTA URL', 'placeholder' => '#ecosystem', 'hint' => 'Link for the secondary hero button.'],
                ]
            ],
            [
                'title' => 'Hero Media',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'hero_section_image', 'label' => 'Hero Section Image', 'placeholder' => 'https://example.com/hero-image.jpg', 'hint' => 'Right-side hero illustration or screenshot image URL.'],
                    ['type' => 'text', 'name' => 'hero_bg_image', 'label' => 'Hero Background Image', 'placeholder' => 'https://example.com/hero-bg.jpg', 'hint' => 'Optional background image for the hero section.'],
                    ['type' => 'toggle', 'name' => 'hero_bg_enable', 'label' => 'Show Hero Background', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Toggle hero section background image.'],
                    ['type' => 'toggle', 'name' => 'show_hero_pattern', 'label' => 'Show Grid Pattern', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Show subtle hero pattern overlay.'],
                    ['type' => 'toggle', 'name' => 'enable_blur_effect', 'label' => 'Enable Blur Effects', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Enable soft blur lighting and overlays.'],
                    ['type' => 'toggle', 'name' => 'enable_animations', 'label' => 'Enable Animations', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Enable hero entrance animations.'],
                ]
            ],
            [
                'title' => 'Hero Image Styling',
                'cols' => 2,
                'fields' => [
                    ['type' => 'toggle', 'name' => 'hero_image_border_enable', 'label' => 'Enable Border', 'yes' => 'On', 'no' => 'Off', 'hint' => 'Enable border for hero image.'],
                    ['type' => 'select', 'name' => 'hero_image_border_color', 'label' => 'Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for hero image.'],
                    ['type' => 'number', 'name' => 'hero_image_border_opacity', 'label' => 'Border Opacity', 'placeholder' => '5', 'hint' => 'Border opacity (0-100).'],
                ]
            ]
        ]
    ],
    [
        'id' => 'expertise',
        'label' => 'Expertise Section',
        'icon' => 'fa fa-lightbulb',
        'group' => 'Appearance',
        'title' => 'Our Expertise',
        'subtitle' => 'Configure the section under hero with expertise cards.',
        'cards' => [
            [
                'title' => 'Section Content',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'expertise_label', 'label' => 'Section Label', 'placeholder' => '02 // CORE DOMAINS', 'hint' => 'Label text displayed above the section title.'],
                    ['type' => 'text', 'name' => 'expertise_title', 'label' => 'Section Title', 'placeholder' => 'Our Expertise', 'hint' => 'Main section title.'],
                    ['type' => 'textarea', 'name' => 'expertise_description', 'label' => 'Section Description', 'rows' => 3, 'placeholder' => 'Deep technical mastery across the full stack of modern enterprise infrastructure.', 'hint' => 'Description text below the section title.'],
                ]
            ],
            [
                'title' => 'Card 1',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'expertise_card_1_icon', 'label' => 'Card 1 Icon', 'placeholder' => 'terminal', 'hint' => 'Material symbol icon name for card 1.'],
                    ['type' => 'text', 'name' => 'expertise_card_1_title', 'label' => 'Card 1 Title', 'placeholder' => 'Intelligent Software', 'hint' => 'Title for the first expertise card.'],
                    ['type' => 'textarea', 'name' => 'expertise_card_1_text', 'label' => 'Card 1 Text', 'rows' => 3, 'placeholder' => 'Custom-built applications designed for high performance, scalability, and seamless user experiences across platforms.', 'hint' => 'Description for the first card.'],
                    ['type' => 'text', 'name' => 'expertise_card_1_image', 'label' => 'Card 1 Image URL', 'placeholder' => 'https://example.com/card1.jpg', 'hint' => 'Optional background image for card 1.'],
                ]
            ],
            [
                'title' => 'Card 2 & 3',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'expertise_card_2_icon', 'label' => 'Card 2 Icon', 'placeholder' => 'cloud_done', 'hint' => 'Material symbol icon name for card 2.'],
                    ['type' => 'text', 'name' => 'expertise_card_2_title', 'label' => 'Card 2 Title', 'placeholder' => 'Cloud Infrastructure', 'hint' => 'Title for the second expertise card.'],
                    ['type' => 'textarea', 'name' => 'expertise_card_2_text', 'label' => 'Card 2 Text', 'rows' => 3, 'placeholder' => 'Cloud-native solutions that provide elastic scalability and global distribution for massive datasets.', 'hint' => 'Description for the second card.'],
                    ['type' => 'text', 'name' => 'expertise_card_3_icon', 'label' => 'Card 3 Icon', 'placeholder' => 'shield_lock', 'hint' => 'Material symbol icon name for card 3.'],
                    ['type' => 'text', 'name' => 'expertise_card_3_title', 'label' => 'Card 3 Title', 'placeholder' => 'Cyber Sentinel', 'hint' => 'Title for the third expertise card.'],
                    ['type' => 'textarea', 'name' => 'expertise_card_3_text', 'label' => 'Card 3 Text', 'rows' => 3, 'placeholder' => 'Military-grade encryption and proactive threat hunting to protect your organization\'s most vital assets.', 'hint' => 'Description for the third card.'],
                ]
            ],
            [
                'title' => 'Card 4',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'expertise_card_4_icon', 'label' => 'Card 4 Icon', 'placeholder' => 'insights', 'hint' => 'Material symbol icon name for card 4.'],
                    ['type' => 'text', 'name' => 'expertise_card_4_title', 'label' => 'Card 4 Title', 'placeholder' => 'Predictive Analytics', 'hint' => 'Title for the fourth expertise card.'],
                    ['type' => 'textarea', 'name' => 'expertise_card_4_text', 'label' => 'Card 4 Text', 'rows' => 3, 'placeholder' => 'Turning raw data into actionable intelligence through advanced AI modeling and real-time processing.', 'hint' => 'Description for the fourth card.'],
                    ['type' => 'text', 'name' => 'expertise_card_4_image', 'label' => 'Card 4 Image URL', 'placeholder' => 'https://example.com/card4.jpg', 'hint' => 'Optional background image for card 4.'],
                ]
            ],
            [
                'title' => 'Card 1 Styling',
                'cols' => 2,
                'fields' => [
                    ['type' => 'select', 'name' => 'expertise_card_1_border_color', 'label' => 'Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for card 1.'],
                    ['type' => 'number', 'name' => 'expertise_card_1_border_opacity', 'label' => 'Border Opacity', 'placeholder' => '5', 'hint' => 'Normal border opacity (0-100).'],
                    ['type' => 'number', 'name' => 'expertise_card_1_border_hover_opacity', 'label' => 'Hover Border Opacity', 'placeholder' => '30', 'hint' => 'Border opacity on hover (0-100).'],
                    ['type' => 'select', 'name' => 'expertise_card_1_bg_color', 'label' => 'Background Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background color for card 1.'],
                ]
            ],
            [
                'title' => 'Card 2 & 3 Styling',
                'cols' => 2,
                'fields' => [
                    ['type' => 'select', 'name' => 'expertise_card_2_border_color', 'label' => 'Card 2 Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for card 2.'],
                    ['type' => 'number', 'name' => 'expertise_card_2_border_opacity', 'label' => 'Card 2 Border Opacity', 'placeholder' => '5', 'hint' => 'Normal border opacity for card 2.'],
                    ['type' => 'number', 'name' => 'expertise_card_2_border_hover_opacity', 'label' => 'Card 2 Hover Opacity', 'placeholder' => '0', 'hint' => 'Hover border opacity for card 2.'],
                    ['type' => 'select', 'name' => 'expertise_card_2_bg_color', 'label' => 'Card 2 BG Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background for card 2.'],
                    ['type' => 'select', 'name' => 'expertise_card_3_border_color', 'label' => 'Card 3 Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for card 3.'],
                    ['type' => 'number', 'name' => 'expertise_card_3_border_opacity', 'label' => 'Card 3 Border Opacity', 'placeholder' => '5', 'hint' => 'Normal border opacity for card 3.'],
                    ['type' => 'number', 'name' => 'expertise_card_3_border_hover_opacity', 'label' => 'Card 3 Hover Opacity', 'placeholder' => '0', 'hint' => 'Hover border opacity for card 3.'],
                    ['type' => 'select', 'name' => 'expertise_card_3_bg_color', 'label' => 'Card 3 BG Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background for card 3.'],
                ]
            ],
            [
                'title' => 'Card 4 Styling',
                'cols' => 2,
                'fields' => [
                    ['type' => 'select', 'name' => 'expertise_card_4_border_color', 'label' => 'Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for card 4.'],
                    ['type' => 'number', 'name' => 'expertise_card_4_border_opacity', 'label' => 'Border Opacity', 'placeholder' => '5', 'hint' => 'Normal border opacity (0-100).'],
                    ['type' => 'number', 'name' => 'expertise_card_4_border_hover_opacity', 'label' => 'Hover Border Opacity', 'placeholder' => '30', 'hint' => 'Border opacity on hover (0-100).'],
                    ['type' => 'select', 'name' => 'expertise_card_4_bg_color', 'label' => 'Background Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background color for card 4.'],
                ]
            ]
        ]
    ],
    [
        'id' => 'advantage',
        'label' => 'Advantage Section',
        'icon' => 'fa fa-award',
        'group' => 'Appearance',
        'title' => 'Why Madxion',
        'subtitle' => 'Configure the section under the expertise grid.',
        'cards' => [
            [
                'title' => 'Section Header',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'advantage_label', 'label' => 'Section Label', 'placeholder' => '03 // THE MADXION ADVANTAGE', 'hint' => 'Text displayed above the section title.'],
                    ['type' => 'text', 'name' => 'advantage_title', 'label' => 'Section Title', 'placeholder' => 'Why Madxion?', 'hint' => 'Main title for this section.'],
                ]
            ],
            [
                'title' => 'Feature Cards',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'advantage_item_1_title', 'label' => 'Item 1 Title', 'placeholder' => 'The Kinetic Monolith Architecture', 'hint' => 'Title for the first feature item.'],
                    ['type' => 'textarea', 'name' => 'advantage_item_1_text', 'label' => 'Item 1 Text', 'rows' => 3, 'placeholder' => 'Our proprietary design framework ensures every system is stable as a monolith but fluid as kinetic energy.', 'hint' => 'Description for the first feature item.'],
                    ['type' => 'text', 'name' => 'advantage_item_2_title', 'label' => 'Item 2 Title', 'placeholder' => 'Radical Transparency', 'hint' => 'Title for the second feature item.'],
                    ['type' => 'textarea', 'name' => 'advantage_item_2_text', 'label' => 'Item 2 Text', 'rows' => 3, 'placeholder' => 'Full visibility into every line of code, every cloud node, and every security protocol we implement.', 'hint' => 'Description for the second feature item.'],
                    ['type' => 'text', 'name' => 'advantage_item_3_title', 'label' => 'Item 3 Title', 'placeholder' => 'Human-Centric Reliability', 'hint' => 'Title for the third feature item.'],
                    ['type' => 'textarea', 'name' => 'advantage_item_3_text', 'label' => 'Item 3 Text', 'rows' => 3, 'placeholder' => 'Technology is built by humans for humans. We prioritize intuitive operation for complex systems.', 'hint' => 'Description for the third feature item.'],
                ]
            ]
        ]
    ],
    [
        'id' => 'validation',
        'label' => 'Validation Section',
        'icon' => 'fa fa-check-circle',
        'group' => 'Appearance',
        'title' => 'Validation',
        'subtitle' => 'Configure the validation section with client testimonials and achievements.',
        'cards' => [
            [
                'title' => 'Section Header',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'validation_label', 'label' => 'Section Label', 'placeholder' => '04 // VALIDATION', 'hint' => 'Text displayed above the section title.'],
                    ['type' => 'text', 'name' => 'validation_title', 'label' => 'Section Title', 'placeholder' => 'Success Stories', 'hint' => 'Main title for this section.'],
                    ['type' => 'textarea', 'name' => 'validation_description', 'label' => 'Section Description', 'rows' => 3, 'placeholder' => 'Real results from real clients.', 'hint' => 'Description text below the section title.'],
                ]
            ],
            [
                'title' => 'Testimonial 1',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'validation_testimonial_1_stat', 'label' => 'Statistic', 'placeholder' => '300%', 'hint' => 'The key metric or statistic.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_1_stat_label', 'label' => 'Statistic Label', 'placeholder' => 'Scale Acceleration', 'hint' => 'Label for the statistic.'],
                    ['type' => 'textarea', 'name' => 'validation_testimonial_1_quote', 'label' => 'Quote Text', 'rows' => 3, 'placeholder' => 'Madxion didn\'t just fix our IT; they rebuilt our digital DNA...', 'hint' => 'The testimonial quote.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_1_name', 'label' => 'Client Name', 'placeholder' => 'Jameson Dovrak', 'hint' => 'Name of the person giving the testimonial.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_1_position', 'label' => 'Position & Company', 'placeholder' => 'CTO, Nexus Dynamics', 'hint' => 'Job title and company name.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_1_initials', 'label' => 'Avatar Initials', 'placeholder' => 'JD', 'hint' => 'Initials for the avatar circle.'],
                    ['type' => 'select', 'name' => 'validation_testimonial_1_color', 'label' => 'Avatar Color', 'options' => ['primary' => 'Primary', 'secondary' => 'Secondary', 'white' => 'White'], 'hint' => 'Color for the avatar initials.'],
                ]
            ],
            [
                'title' => 'Testimonial 2 & 3',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'validation_testimonial_2_stat', 'label' => 'Testimonial 2 Statistic', 'placeholder' => 'Zero', 'hint' => 'The key metric for testimonial 2.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_2_stat_label', 'label' => 'Testimonial 2 Label', 'placeholder' => 'Security Breaches', 'hint' => 'Label for testimonial 2 statistic.'],
                    ['type' => 'textarea', 'name' => 'validation_testimonial_2_quote', 'label' => 'Testimonial 2 Quote', 'rows' => 3, 'placeholder' => 'Their security protocols are unmatched...', 'hint' => 'Quote for testimonial 2.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_2_name', 'label' => 'Testimonial 2 Name', 'placeholder' => 'Elena Laurent', 'hint' => 'Name for testimonial 2.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_2_position', 'label' => 'Testimonial 2 Position', 'placeholder' => 'Head of Security, FinSafe', 'hint' => 'Position for testimonial 2.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_2_initials', 'label' => 'Testimonial 2 Initials', 'placeholder' => 'EL', 'hint' => 'Initials for testimonial 2.'],
                    ['type' => 'select', 'name' => 'validation_testimonial_2_color', 'label' => 'Testimonial 2 Color', 'options' => ['primary' => 'Primary', 'secondary' => 'Secondary', 'white' => 'White'], 'hint' => 'Color for testimonial 2 avatar.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_3_stat', 'label' => 'Testimonial 3 Statistic', 'placeholder' => '40ms', 'hint' => 'The key metric for testimonial 3.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_3_stat_label', 'label' => 'Testimonial 3 Label', 'placeholder' => 'Global Latency', 'hint' => 'Label for testimonial 3 statistic.'],
                    ['type' => 'textarea', 'name' => 'validation_testimonial_3_quote', 'label' => 'Testimonial 3 Quote', 'rows' => 3, 'placeholder' => 'The edge computing solution provided by Madxion...', 'hint' => 'Quote for testimonial 3.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_3_name', 'label' => 'Testimonial 3 Name', 'placeholder' => 'Marcus Kane', 'hint' => 'Name for testimonial 3.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_3_position', 'label' => 'Testimonial 3 Position', 'placeholder' => 'CEO, StreamVault', 'hint' => 'Position for testimonial 3.'],
                    ['type' => 'text', 'name' => 'validation_testimonial_3_initials', 'label' => 'Testimonial 3 Initials', 'placeholder' => 'MK', 'hint' => 'Initials for testimonial 3.'],
                    ['type' => 'select', 'name' => 'validation_testimonial_3_color', 'label' => 'Testimonial 3 Color', 'options' => ['primary' => 'Primary', 'secondary' => 'Secondary', 'white' => 'White'], 'hint' => 'Color for testimonial 3 avatar.'],
                ]
            ],
            [
                'title' => 'Testimonial 1 Styling',
                'cols' => 2,
                'fields' => [
                    ['type' => 'select', 'name' => 'validation_card_1_border_color', 'label' => 'Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for testimonial 1.'],
                    ['type' => 'number', 'name' => 'validation_card_1_border_opacity', 'label' => 'Border Opacity', 'placeholder' => '5', 'hint' => 'Normal border opacity (0-100).'],
                    ['type' => 'number', 'name' => 'validation_card_1_border_hover_opacity', 'label' => 'Hover Opacity', 'placeholder' => '0', 'hint' => 'Border opacity on hover (0-100).'],
                    ['type' => 'select', 'name' => 'validation_card_1_bg_color', 'label' => 'Background Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background color for testimonial 1.'],
                ]
            ],
            [
                'title' => 'Testimonial 2 & 3 Styling',
                'cols' => 2,
                'fields' => [
                    ['type' => 'select', 'name' => 'validation_card_2_border_color', 'label' => 'Testimonial 2 Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for testimonial 2.'],
                    ['type' => 'number', 'name' => 'validation_card_2_border_opacity', 'label' => 'Testimonial 2 Border Opacity', 'placeholder' => '5', 'hint' => 'Normal border opacity for testimonial 2.'],
                    ['type' => 'number', 'name' => 'validation_card_2_border_hover_opacity', 'label' => 'Testimonial 2 Hover Opacity', 'placeholder' => '0', 'hint' => 'Hover border opacity for testimonial 2.'],
                    ['type' => 'select', 'name' => 'validation_card_2_bg_color', 'label' => 'Testimonial 2 BG Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background for testimonial 2.'],
                    ['type' => 'select', 'name' => 'validation_card_3_border_color', 'label' => 'Testimonial 3 Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for testimonial 3.'],
                    ['type' => 'number', 'name' => 'validation_card_3_border_opacity', 'label' => 'Testimonial 3 Border Opacity', 'placeholder' => '5', 'hint' => 'Normal border opacity for testimonial 3.'],
                    ['type' => 'number', 'name' => 'validation_card_3_border_hover_opacity', 'label' => 'Testimonial 3 Hover Opacity', 'placeholder' => '0', 'hint' => 'Hover border opacity for testimonial 3.'],
                    ['type' => 'select', 'name' => 'validation_card_3_bg_color', 'label' => 'Testimonial 3 BG Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background for testimonial 3.'],
                ]
            ]
        ]
    ],
    [
        'id' => 'posts',
        'label' => 'Posts Section',
        'icon' => 'fa fa-newspaper',
        'group' => 'Content',
        'title' => 'Recent Posts Display',
        'subtitle' => 'Configure how recent posts are displayed on the homepage.',
        'cards' => [
            [
                'title' => 'Section Header',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'posts_section_title', 'label' => 'Section Title', 'placeholder' => 'Latest Insights', 'hint' => 'Title for the posts section.'],
                    ['type' => 'textarea', 'name' => 'posts_section_subtitle', 'label' => 'Section Subtitle', 'rows' => 3, 'placeholder' => 'Stay updated with our latest thoughts and industry insights.', 'hint' => 'Subtitle text below the section title.'],
                ]
            ],
            [
                'title' => 'Display Settings',
                'cols' => 2,
                'fields' => [
                    ['type' => 'number', 'name' => 'posts_limit', 'label' => 'Number of Posts to Display', 'placeholder' => '6', 'hint' => 'Maximum number of recent posts to show on the homepage.'],
                ]
            ],
            [
                'title' => 'Post Card Styling',
                'cols' => 2,
                'fields' => [
                    ['type' => 'select', 'name' => 'posts_card_border_color', 'label' => 'Border Color', 'options' => ['white' => 'White', 'primary' => 'Primary', 'primary-container' => 'Primary Container', 'secondary' => 'Secondary', 'none' => 'None'], 'hint' => 'Border color for post cards.'],
                    ['type' => 'number', 'name' => 'posts_card_border_opacity', 'label' => 'Border Opacity', 'placeholder' => '20', 'hint' => 'Normal border opacity (0-100).'],
                    ['type' => 'number', 'name' => 'posts_card_border_hover_opacity', 'label' => 'Hover Border Opacity', 'placeholder' => '100', 'hint' => 'Border opacity on hover (0-100).'],
                    ['type' => 'select', 'name' => 'posts_card_bg_color', 'label' => 'Background Color', 'options' => ['surface' => 'Surface', 'surface-container-low' => 'Surface Container Low', 'surface-container-high' => 'Surface Container High', 'transparent' => 'Transparent'], 'hint' => 'Background color for post cards.'],
                ]
            ]
        ]
    ],
    [
        'id' => 'colors',
        'label' => 'Design & Colors',
        'label' => 'Design & Colors',
        'label' => 'Design & Colors',
        'icon' => 'fa fa-palette',
        'group' => 'Appearance',
        'title' => 'Color System',
        'subtitle' => 'Customize the Kinetic theme color palette.',
        'cards' => [
            [
                'title' => 'Primary Palette',
                'cols' => 2,
                'fields' => [
                    ['type' => 'color', 'name' => 'primary_color', 'label' => 'Primary Color', 'hint' => 'Main accent color (default: #ffb4a8)'],
                    ['type' => 'color', 'name' => 'primary_container', 'label' => 'Primary Container', 'hint' => 'Darker primary for containers (default: #d20000)'],
                ]
            ],
            [
                'title' => 'Secondary Palette',
                'cols' => 2,
                'fields' => [
                    ['type' => 'color', 'name' => 'secondary_color', 'label' => 'Secondary Color', 'hint' => 'Secondary accent (default: #ffb956)'],
                    ['type' => 'color', 'name' => 'secondary_container', 'label' => 'Secondary Container', 'hint' => 'Darker secondary (default: #ca8500)'],
                ]
            ],
            [
                'title' => 'Background Colors',
                'cols' => 3,
                'fields' => [
                    ['type' => 'color', 'name' => 'background_color', 'label' => 'Main Background', 'hint' => 'Primary background color'],
                    ['type' => 'color', 'name' => 'surface_color', 'label' => 'Surface Color', 'hint' => 'Default surface color'],
                    ['type' => 'color', 'name' => 'surface_container_low', 'label' => 'Surface Container Low'],
                ]
            ],
            [
                'title' => 'Surface Variants',
                'cols' => 3,
                'fields' => [
                    ['type' => 'color', 'name' => 'surface_container', 'label' => 'Surface Container', 'hint' => 'Standard container background'],
                    ['type' => 'color', 'name' => 'surface_container_high', 'label' => 'Surface Container High', 'hint' => 'Elevated surfaces'],
                ]
            ],
            [
                'title' => 'Text Colors',
                'cols' => 2,
                'fields' => [
                    ['type' => 'color', 'name' => 'text_color', 'label' => 'Main Text Color', 'hint' => 'Primary text color'],
                    ['type' => 'color', 'name' => 'text_variant', 'label' => 'Secondary Text Color', 'hint' => 'For muted/secondary text'],
                ]
            ]
        ]
    ],
    [
        'id' => 'typography',
        'label' => 'Typography',
        'icon' => 'fa fa-font',
        'group' => 'Appearance',
        'title' => 'Font Configuration',
        'subtitle' => 'Customize typefaces and sizing throughout the theme.',
        'cards' => [
            [
                'title' => 'Font Families',
                'cols' => 3,
                'fields' => [
                    ['type' => 'text', 'name' => 'typo_headline_font', 'label' => 'Headline Font', 'placeholder' => '"Space Grotesk", sans-serif', 'hint' => 'Font for headings (H1, H2, H3)'],
                    ['type' => 'text', 'name' => 'typo_body_font', 'label' => 'Body Font', 'placeholder' => '"Manrope", sans-serif', 'hint' => 'Font for body text'],
                    ['type' => 'text', 'name' => 'typo_label_font', 'label' => 'Label Font', 'placeholder' => '"Manrope", sans-serif', 'hint' => 'Font for labels and captions'],
                ]
            ],
            [
                'title' => 'Font Sizes',
                'cols' => 3,
                'fields' => [
                    ['type' => 'text', 'name' => 'typo_h1_size', 'label' => 'H1 Size', 'placeholder' => '48px', 'hint' => 'Main heading size'],
                    ['type' => 'text', 'name' => 'typo_h2_size', 'label' => 'H2 Size', 'placeholder' => '36px', 'hint' => 'Subheading size'],
                    ['type' => 'text', 'name' => 'typo_h3_size', 'label' => 'H3 Size', 'placeholder' => '28px', 'hint' => 'Section heading size'],
                ]
            ]
        ]
    ],
    [
        'id' => 'social',
        'label' => 'Social & Links',
        'icon' => 'fa fa-share-alt',
        'group' => 'Integration',
        'title' => 'Social Media',
        'subtitle' => 'Connect your social media profiles.',
        'cards' => [
            [
                'title' => 'Social Links',
                'cols' => 2,
                'fields' => [
                    ['type' => 'text', 'name' => 'social_fb', 'label' => 'Facebook URL', 'placeholder' => 'https://facebook.com/yourpage'],
                    ['type' => 'text', 'name' => 'social_tw', 'label' => 'Twitter URL', 'placeholder' => 'https://twitter.com/yourhandle'],
                    ['type' => 'text', 'name' => 'social_gh', 'label' => 'GitHub URL', 'placeholder' => 'https://github.com/yourprofile'],
                    ['type' => 'text', 'name' => 'social_li', 'label' => 'LinkedIn URL', 'placeholder' => 'https://linkedin.com/company/yourcompany'],
                    ['type' => 'text', 'name' => 'social_ig', 'label' => 'Instagram URL', 'placeholder' => 'https://instagram.com/yourprofile'],
                ]
            ]
        ]
    ],
    [
        'id' => 'advanced',
        'label' => 'Advanced',
        'icon' => 'fa fa-code',
        'group' => 'Integration',
        'title' => 'Custom Code & Scripts',
        'subtitle' => 'Add tracking, ads, and custom CSS.',
        'cards' => [
            [
                'title' => 'Analytics & Tracking',
                'fields' => [
                    ['type' => 'textarea', 'name' => 'mdo_analytics', 'label' => 'Analytics Code', 'rows' => 6, 'placeholder' => '<!-- Google Analytics or similar -->
<script>
// Your tracking code here
</script>', 'hint' => 'Paste your Google Analytics or tracking code'],
                ]
            ],
            [
                'title' => 'Advertising',
                'fields' => [
                    ['type' => 'textarea', 'name' => 'mdo_adsense', 'label' => 'Ad Scripts', 'rows' => 6, 'placeholder' => '<!-- Global ad scripts -->
<script>
// Your ad code here
</script>', 'hint' => 'AdSense or other ad network scripts'],
                ]
            ],
            [
                'title' => 'Custom Styles',
                'fields' => [
                    ['type' => 'textarea', 'name' => 'custom_css', 'label' => 'Custom CSS', 'rows' => 12, 'placeholder' => '/* Add custom styles here */
.my-custom-class {
  color: #ffffff;
  font-size: 16px;
}', 'hint' => 'Write custom CSS to override or extend theme styles'],
                ]
            ]
        ]
    ]
];

// ── 3. Internal Render ─────────────────────────────────────────────
$builder->render($schema);
?>
