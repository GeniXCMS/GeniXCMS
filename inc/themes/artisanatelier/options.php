<?php
/**
 * Artisan Atelier - Theme Options
 * Powered by GxOptionsBuilder
 */

// 1. Initial Installation
if (isset($_POST['installdb'])) {
    $arr = array(
        'primary_color' => '#a74632',
        'secondary_color' => '#4f6c43',
        'surface_color' => '#fdf9f4',
        'on_surface_color' => '#393832',
        'hero_title' => 'Objects of Quiet Intention',
        'hero_subtitle' => "Curating a collection of handmade ceramics, linens, and art that celebrate the slow rhythm of the maker's hand.",
        'hero_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAnqrKkno9gZRdt0fYy2dnSelztird_BPZvV3NRPJTtKL5EvPeFix4qEOmxGKSCoQD9t6IpiNsi1Fz8Zrb9T05I3mfsvGAQ9Pa5d1nAQXJgosEYqdyWZQs1Diapxb-wnu4p-GTxV9B-jAPcU4UGhIfSivzLuQzKkAHqzElsKJdns1uHLjNoGOUc8A_GBa1yQvT67jwLjeaFZ3j9rknYPK3Ft21sjvVG2VcZpXkfMPXnBHTysEF9kx_lWVqJ2m_f-s1i_NWRGmA86Xo',
        'show_newsletter' => 'yes',
        'footer_text' => '© 2024 The Artisanal Atelier. Handcrafted with soul and intention.',
        'custom_css' => '',
    );
    Options::insert(['artisan_options' => json_encode($arr)]);
}

// 2. Load Options
if (ArtisanAtelier::checkDB()) {
    $opt_raw = Options::get('artisan_options');
    $o_db = json_decode($opt_raw, true);
    $defaults = ArtisanAtelier::getDefaults();
    $o = array_merge($defaults, (is_array($o_db) ? $o_db : []));

    $presets = [
        'default' => ['name' => 'Original Earth', 'emoji' => '🏺', 'primary_color' => '#a74632', 'secondary_color' => '#4f6c43', 'surface_color' => '#fdf9f4'],
        'monochrome' => ['name' => 'Studio Grey', 'emoji' => '🎨', 'primary_color' => '#393832', 'secondary_color' => '#66645e', 'surface_color' => '#f5f5f5'],
        'ocean' => ['name' => 'Coastal Clay', 'emoji' => '🌊', 'primary_color' => '#2a4d69', 'secondary_color' => '#4b86b4', 'surface_color' => '#f0f8ff'],
    ];

    $schema = [
        [
            'id' => 'tab-general',
            'label' => 'Design System',
            'icon' => 'bi bi-palette',
            'group' => 'Core',
            'active' => true,
            'title' => 'Colors & Aesthetics',
            'subtitle' => 'Define the artisanal palette of your atelier.',
            'cards' => [
                [
                    'title' => 'Primary Palette',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'color', 'name' => 'primary_color', 'label' => 'Primary Accent', 'hint' => 'Used for buttons, highlights, and italics.'],
                        ['type' => 'color', 'name' => 'secondary_color', 'label' => 'Secondary Accent'],
                        ['type' => 'color', 'name' => 'surface_color', 'label' => 'Background / Surface'],
                        ['type' => 'color', 'name' => 'on_surface_color', 'label' => 'Main Text Color'],
                    ],
                ],
                [
                    'title' => 'Structural Layout',
                    'fields' => [
                        [
                            'type' => 'select',
                            'name' => 'mod_layout_type',
                            'label' => 'Module Page Layout',
                            'options' => [
                                'standard' => 'Standard (Framed)',
                                'fullwidth' => 'Modern (Borderless Full Width)'
                            ],
                            'hint' => 'Framed wraps content in a card, Full Width lets module occupy entire container space.'
                        ],
                        [
                            'type' => 'toggle',
                            'name' => 'mod_show_title',
                            'label' => 'Show Module Title',
                            'yes' => 'Show',
                            'no' => 'Hide',
                            'hint' => 'Toggle the visibility of the module name/title at the top of the page.'
                        ],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-hero',
            'label' => 'Homepage Hero',
            'icon' => 'bi bi-image',
            'group' => 'General',
            'title' => 'Hero Section',
            'subtitle' => 'The first impression for your visitors.',
            'cards' => [
                [
                    'title' => 'Hero Content',
                    'fields' => [
                        ['type' => 'text', 'name' => 'hero_title', 'label' => 'Hero Title'],
                        ['type' => 'textarea', 'name' => 'hero_subtitle', 'label' => 'Hero Subtitle'],
                        ['type' => 'text', 'name' => 'hero_image', 'label' => 'Background Image URL'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-catalog',
            'label' => 'Store Catalog',
            'icon' => 'bi bi-shop',
            'group' => 'General',
            'title' => 'Catalog Hero Settings',
            'subtitle' => 'Customize the banner section of your store catalog page.',
            'cards' => [
                [
                    'title' => 'Hero Text & CTA',
                    'fields' => [
                        ['type' => 'text', 'name' => 'catalog_hero_tag', 'label' => 'Status Tag', 'hint' => 'Small rounded badge at the top.'],
                        ['type' => 'text', 'name' => 'catalog_hero_title', 'label' => 'Hero Headline'],
                        ['type' => 'textarea', 'name' => 'catalog_hero_subtitle', 'label' => 'Sub-headline Description'],
                        ['type' => 'text', 'name' => 'catalog_hero_btn1', 'label' => 'Primary Button Text'],
                        ['type' => 'text', 'name' => 'catalog_hero_url1', 'label' => 'Primary Button URL'],
                        ['type' => 'text', 'name' => 'catalog_hero_btn2', 'label' => 'Secondary Button Text'],
                        ['type' => 'text', 'name' => 'catalog_hero_url2', 'label' => 'Secondary Button URL'],
                    ],
                ],
                [
                    'title' => 'Hero Visuals',
                    'fields' => [
                        ['type' => 'text', 'name' => 'catalog_hero_img1', 'label' => 'Primary Showcase Image'],
                        ['type' => 'text', 'name' => 'catalog_hero_img2', 'label' => 'Secondary Accent Image'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-workshop',
            'label' => 'Workshop Studio',
            'icon' => 'bi bi-tools',
            'group' => 'General',
            'title' => 'Workshop Teaser Settings',
            'subtitle' => 'Promote your hands-on studio experiences on the catalog page.',
            'cards' => [
                [
                    'title' => 'Main Content',
                    'fields' => [
                        ['type' => 'toggle', 'name' => 'catalog_work_show', 'label' => 'Show Workshop Section', 'yes' => 'Enable', 'no' => 'Disable'],
                        ['type' => 'text', 'name' => 'catalog_work_tag', 'label' => 'Status Tag', 'hint' => 'Small badge above the title.'],
                        ['type' => 'text', 'name' => 'catalog_work_title', 'label' => 'Teaser Headline'],
                        ['type' => 'textarea', 'name' => 'catalog_work_desc', 'label' => 'Teaser Description'],
                        ['type' => 'text', 'name' => 'catalog_work_img', 'label' => 'Showcase Image URL'],
                    ],
                ],
                [
                    'title' => 'Call to Action',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'catalog_work_btn', 'label' => 'Button Label'],
                        ['type' => 'text', 'name' => 'catalog_work_url', 'label' => 'Destination URL'],
                    ],
                ],
                [
                    'title' => 'Feature Highlights',
                    'cols' => 3,
                    'fields' => [
                        ['type' => 'text', 'name' => 'catalog_work_icon1', 'label' => 'Icon 01'],
                        ['type' => 'text', 'name' => 'catalog_work_feat1', 'label' => 'Feature 01'],
                        ['type' => 'text', 'name' => 'catalog_work_icon2', 'label' => 'Icon 02'],
                        ['type' => 'text', 'name' => 'catalog_work_feat2', 'label' => 'Feature 02'],
                        ['type' => 'text', 'name' => 'catalog_work_icon3', 'label' => 'Icon 03'],
                        ['type' => 'text', 'name' => 'catalog_work_feat3', 'label' => 'Feature 03'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-process',
            'label' => 'Process Journal',
            'icon' => 'bi bi-journal-text',
            'group' => 'General',
            'title' => 'Process & Craftsmanship',
            'subtitle' => 'Share the journey from raw materials to finished art.',
            'cards' => [
                [
                    'title' => 'Main Section Content',
                    'fields' => [
                        ['type' => 'text', 'name' => 'process_title', 'label' => 'Section Title'],
                        ['type' => 'textarea', 'name' => 'process_desc', 'label' => 'Section Description'],
                        ['type' => 'text', 'name' => 'process_video_label', 'label' => 'Action Label', 'hint' => 'e.g. Watch The Film'],
                    ],
                ],
                [
                    'title' => 'Process Step 01',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'process_img1', 'label' => 'Step 01 Image URL', 'col' => 12],
                        ['type' => 'text', 'name' => 'process_step1_title', 'label' => 'Step 01 Title'],
                        ['type' => 'textarea', 'name' => 'process_step1_desc', 'label' => 'Step 01 Description'],
                    ],
                ],
                [
                    'title' => 'Process Step 02',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'process_img2', 'label' => 'Step 02 Image URL', 'col' => 12],
                        ['type' => 'text', 'name' => 'process_step2_title', 'label' => 'Step 02 Title'],
                        ['type' => 'textarea', 'name' => 'process_step2_desc', 'label' => 'Step 02 Description'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-gallery',
            'label' => 'Story Gallery',
            'icon' => 'bi bi-grid-3x3',
            'group' => 'General',
            'title' => 'Creation Gallery',
            'subtitle' => 'Curate the visual narrative of your artisanal process.',
            'cards' => [
                [
                    'title' => 'Gallery Header',
                    'fields' => [
                        ['type' => 'text', 'name' => 'story_gallery_title', 'label' => 'Section Title'],
                        ['type' => 'textarea', 'name' => 'story_gallery_subtitle', 'label' => 'Section Subtitle'],
                    ],
                ],
                [
                    'title' => 'Gallery Content',
                    'fields' => [
                        ['type' => 'text', 'name' => 'story_gallery_img1', 'label' => 'Left Top Image (Square)'],
                        ['type' => 'text', 'name' => 'story_gallery_card_title', 'label' => 'Infocard Title'],
                        ['type' => 'textarea', 'name' => 'story_gallery_card_desc', 'label' => 'Infocard Description'],
                        ['type' => 'text', 'name' => 'story_gallery_img2', 'label' => 'Middle Large Image (4:5)'],
                        ['type' => 'text', 'name' => 'story_gallery_img3', 'label' => 'Right Top Image (3:4)'],
                        ['type' => 'text', 'name' => 'story_gallery_img4', 'label' => 'Right Bottom Image (Square)'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-pillars',
            'label' => 'Brand Pillars',
            'icon' => 'bi bi-columns-gap',
            'group' => 'General',
            'title' => 'Core Brand Pillars',
            'subtitle' => 'Define the foundational values of your atelier.',
            'cards' => [
                [
                    'title' => 'Pillars Header',
                    'fields' => [
                        ['type' => 'text', 'name' => 'pillars_title', 'label' => 'Section Title'],
                        ['type' => 'textarea', 'name' => 'pillars_subtitle', 'label' => 'Section Subtitle'],
                    ],
                ],
                [
                    'title' => 'Pillar 01',
                    'fields' => [
                        ['type' => 'text', 'name' => 'pillar1_icon', 'label' => 'Google Icon Name (e.g. eco)'],
                        ['type' => 'text', 'name' => 'pillar1_title', 'label' => 'Title'],
                        ['type' => 'textarea', 'name' => 'pillar1_desc', 'label' => 'Description'],
                    ],
                ],
                [
                    'title' => 'Pillar 02',
                    'fields' => [
                        ['type' => 'text', 'name' => 'pillar2_icon', 'label' => 'Google Icon Name (e.g. draw)'],
                        ['type' => 'text', 'name' => 'pillar2_title', 'label' => 'Title'],
                        ['type' => 'textarea', 'name' => 'pillar2_desc', 'label' => 'Description'],
                    ],
                ],
                [
                    'title' => 'Pillar 03',
                    'fields' => [
                        ['type' => 'text', 'name' => 'pillar3_icon', 'label' => 'Google Icon Name (e.g. precision_manufacturing)'],
                        ['type' => 'text', 'name' => 'pillar3_title', 'label' => 'Title'],
                        ['type' => 'textarea', 'name' => 'pillar3_desc', 'label' => 'Description'],
                    ],
                ],
                [
                    'title' => 'Pillar 04',
                    'fields' => [
                        ['type' => 'text', 'name' => 'pillar4_icon', 'label' => 'Google Icon Name (e.g. auto_awesome)'],
                        ['type' => 'text', 'name' => 'pillar4_title', 'label' => 'Title'],
                        ['type' => 'textarea', 'name' => 'pillar4_desc', 'label' => 'Description'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-founder',
            'label' => "Founder's Note",
            'icon' => 'bi bi-chat-quote',
            'group' => 'General',
            'title' => 'Personal Message',
            'subtitle' => 'Add a personal touch to your brand narrative.',
            'cards' => [
                [
                    'title' => 'Founder Identity',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'founder_img', 'label' => 'Portrait Image URL'],
                        ['type' => 'text', 'name' => 'founder_note_title', 'label' => 'Note Title'],
                    ],
                ],
                [
                    'title' => 'The Message',
                    'fields' => [
                        ['type' => 'textarea', 'name' => 'founder_quote', 'label' => 'Founder Quote'],
                        ['type' => 'text', 'name' => 'founder_signature', 'label' => 'Signature Name'],
                        ['type' => 'text', 'name' => 'founder_job', 'label' => 'Job Title / Role'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-footer',
            'label' => 'Footer & Extra',
            'icon' => 'bi bi-layout-sidebar-inset',
            'group' => 'General',
            'title' => 'Footer Details',
            'cards' => [
                [
                    'title' => 'Site Footer',
                    'fields' => [
                        ['type' => 'textarea', 'name' => 'footer_text', 'label' => 'Copyright Text'],
                        ['type' => 'toggle', 'name' => 'show_newsletter', 'label' => 'Show Newsletter Section', 'yes' => 'Enable', 'no' => 'Disable'],
                    ],
                ],
                [
                    'title' => 'Advanced Styling',
                    'fields' => [
                        ['type' => 'textarea', 'name' => 'custom_css', 'label' => 'Custom CSS Overlay', 'hint' => 'Inject raw CSS into the header.'],
                    ],
                ],
            ],
        ],
    ];

    $config = [
        'brandName' => 'Artisan',
        'brandVer' => 'v1.0.0',
        'brandAbbr' => 'AA',
        'brandIcon' => 'bi bi-brush',
        'brandColor' => '#a74632',
        'saveKey' => 'artisan_options_update',
    ];

    $builder = new OptionsBuilder($o, $presets, [], $config);

    if (isset($_POST['artisan_options_update'])) {
        unset($_POST['artisan_options_update']);
        $builder->renderCSS();
        $json = json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);
        if (Options::validate('artisan_options')) {
            Query::table('options')->where('name', 'artisan_options')->update(['value' => $json]);
        } else {
            // Use direct query to bypass cleanX on insertion too
            Query::table('options')->insert([
                'name' => 'artisan_options',
                'value' => $json
            ]);
        }
        echo '<div id="gx-toast"><i class="bi bi-check-circle"></i> Artisan Settings saved!</div>';
        echo '<script>setTimeout(() => window.location.reload(), 1000);</script>';
    }

    $builder->render($schema);

} else {
    echo '<div class="card p-5 text-center shadow-sm border-0 rounded-4">
        <div class="display-1 mb-4">🏺</div>
        <h3 class="fw-bold">Artisan Atelier Theme Engine</h3>
        <p class="text-muted">Initialize the database to begin personalizing your artisanal experience.</p>
        <form method="post">
            <button type="submit" name="installdb" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow">
                Initialize Atelier
            </button>
        </form>
    </div>';
}
