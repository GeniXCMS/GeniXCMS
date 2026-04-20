<?php
/**
 * Bakeshop - Theme Options
 */

// 1. Initial Installation
if (isset($_POST['installdb'])) {
    $arr = Bakeshop::getDefaults();
    Options::insert(['bakeshop_options' => json_encode($arr)]);
}

// 2. Load Options
if (Bakeshop::checkDB()) {
    $o = Bakeshop::options();

    $schema = [
        [
            'id' => 'tab-style',
            'label' => 'Branding',
            'icon' => 'bi bi-palette',
            'group' => 'Core',
            'active' => true,
            'title' => 'Visual Identity',
            'subtitle' => 'Define your bakery s brand colors and logo.',
            'cards' => [
                [
                    'title' => 'Logo & Text',
                    'fields' => [
                        ['type' => 'text', 'name' => 'logo_text', 'label' => 'Brand Name'],
                    ],
                ],
                [
                    'title' => 'Color Palette',
                    'cols' => 3,
                    'fields' => [
                        ['type' => 'color', 'name' => 'primary_color', 'label' => 'Primary Accent'],
                        ['type' => 'color', 'name' => 'secondary_color', 'label' => 'Secondary Accent'],
                        ['type' => 'color', 'name' => 'background_color', 'label' => 'Background Surface'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-hero',
            'label' => 'Homepage Hero',
            'icon' => 'bi bi-image',
            'group' => 'General',
            'title' => 'Hero Banner Configuration',
            'subtitle' => 'The main top section of your home page.',
            'cards' => [
                [
                    'title' => 'Text Content',
                    'fields' => [
                        ['type' => 'text', 'name' => 'hero_tagline', 'label' => 'Tagline (Small Top)'],
                        ['type' => 'text', 'name' => 'hero_title', 'label' => 'Main Headline'],
                        ['type' => 'textarea', 'name' => 'hero_desc', 'label' => 'Sub-headline Description'],
                    ],
                ],
                [
                    'title' => 'Call to Action',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'hero_btn_text', 'label' => 'Button Text'],
                        ['type' => 'text', 'name' => 'hero_btn_url', 'label' => 'Button Link'],
                    ],
                ],
                [
                    'title' => 'Visual Imagery (Grid)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'hero_img1', 'label' => 'Left Image URL'],
                        ['type' => 'text', 'name' => 'hero_img2', 'label' => 'Right Top Image URL'],
                        ['type' => 'text', 'name' => 'hero_img3', 'label' => 'Right Bottom Image URL'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-values',
            'label' => 'Core Values',
            'icon' => 'bi bi-heart',
            'group' => 'General',
            'title' => 'Our Secret Sauce',
            'subtitle' => 'List three points that make your bakery special.',
            'cards' => [
                [
                    'title' => 'Header',
                    'fields' => [
                        ['type' => 'text', 'name' => 'value_title', 'label' => 'Section Title'],
                        ['type' => 'text', 'name' => 'value_subtitle', 'label' => 'Section Subtitle'],
                    ],
                ],
                [
                    'title' => 'Value 01',
                    'fields' => [
                        ['type' => 'text', 'name' => 'value1_icon', 'label' => 'Material Icon Name'],
                        ['type' => 'text', 'name' => 'value1_title', 'label' => 'Title'],
                        ['type' => 'textarea', 'name' => 'value1_desc', 'label' => 'Description'],
                    ],
                ],
                [
                    'title' => 'Value 02',
                    'fields' => [
                        ['type' => 'text', 'name' => 'value2_icon', 'label' => 'Material Icon Name'],
                        ['type' => 'text', 'name' => 'value2_title', 'label' => 'Title'],
                        ['type' => 'textarea', 'name' => 'value2_desc', 'label' => 'Description'],
                    ],
                ],
                [
                    'title' => 'Value 03',
                    'fields' => [
                        ['type' => 'text', 'name' => 'value3_icon', 'label' => 'Material Icon Name'],
                        ['type' => 'text', 'name' => 'value3_title', 'label' => 'Title'],
                        ['type' => 'textarea', 'name' => 'value3_desc', 'label' => 'Description'],
                    ],
                ],
                [
                    'title' => 'Featured Collection Settings',
                    'fields' => [
                        ['type' => 'text', 'name' => 'featured_post_type', 'label' => 'Post Type Slug', 'hint' => 'Default is "post". If you have a store, use "nixomers" or your custom type.'],
                        ['type' => 'number', 'name' => 'featured_limit', 'label' => 'Total Items to Show'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-testimonials',
            'label' => 'Testimonials',
            'icon' => 'bi bi-chat-heart',
            'group' => 'General',
            'title' => 'Customer Love',
            'subtitle' => 'Manage the social proof on your homepage.',
            'cards' => [
                [
                    'title' => 'Global Section Visuals',
                    'fields' => [
                        ['type' => 'text', 'name' => 'testi_main_img', 'label' => 'Main Showcase Image'],
                        ['type' => 'text', 'name' => 'testi_stat_num', 'label' => 'Highlight Number', 'hint' => 'e.g. 5,000+'],
                        ['type' => 'text', 'name' => 'testi_stat_text', 'label' => 'Highlight Description'],
                    ],
                ],
                [
                    'title' => 'Testimonial 01',
                    'fields' => [
                        ['type' => 'textarea', 'name' => 'testi1_quote', 'label' => 'The Quote'],
                        ['type' => 'text', 'name' => 'testi1_name', 'label' => 'Customer Name'],
                        ['type' => 'text', 'name' => 'testi1_job', 'label' => 'Customer Role'],
                        ['type' => 'text', 'name' => 'testi1_img', 'label' => 'Customer Portrait URL'],
                    ],
                ],
                [
                    'title' => 'Testimonial 02',
                    'fields' => [
                        ['type' => 'textarea', 'name' => 'testi2_quote', 'label' => 'The Quote'],
                        ['type' => 'text', 'name' => 'testi2_name', 'label' => 'Customer Name'],
                        ['type' => 'text', 'name' => 'testi2_job', 'label' => 'Customer Role'],
                        ['type' => 'text', 'name' => 'testi2_img', 'label' => 'Customer Portrait URL'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-about',
            'label' => 'About Us',
            'icon' => 'bi bi-info-circle',
            'group' => 'Pages',
            'title' => 'Our Story',
            'subtitle' => 'Manage the content for your about page.',
            'cards' => [
                [
                    'title' => 'About Hero',
                    'fields' => [
                        ['type' => 'text', 'name' => 'about_hero_subtitle', 'label' => 'Small Subtitle', 'hint' => 'e.g. Our Story'],
                        ['type' => 'text', 'name' => 'about_hero_title', 'label' => 'Main Heading'],
                        ['type' => 'textarea', 'name' => 'about_hero_desc', 'label' => 'Description'],
                        ['type' => 'text', 'name' => 'about_hero_img', 'label' => 'Hero Image URL'],
                        ['type' => 'text', 'name' => 'about_badge_num', 'label' => 'Badge Number', 'hint' => 'e.g. 100%'],
                        ['type' => 'text', 'name' => 'about_badge_text', 'label' => 'Badge Description'],
                    ],
                ],
                [
                    'title' => 'Philosophy & Mission',
                    'fields' => [
                        ['type' => 'text', 'name' => 'about_phil_title', 'label' => 'Philosophy Section Title'],
                        ['type' => 'textarea', 'name' => 'about_phil_desc', 'label' => 'Philosophy Subtext'],
                        ['type' => 'textarea', 'name' => 'about_mission', 'label' => 'Mission Quote'],
                    ],
                ],
                [
                    'title' => 'The Team (Artisan 01)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'team1_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team1_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team1_img', 'label' => 'Portrait Image URL'],
                        ['type' => 'textarea', 'name' => 'team1_bio', 'label' => 'Short Bio'],
                    ],
                ],
                [
                    'title' => 'The Team (Artisan 02)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'team2_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team2_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team2_img', 'label' => 'Portrait Image URL'],
                        ['type' => 'textarea', 'name' => 'team2_bio', 'label' => 'Short Bio'],
                    ],
                ],
                [
                    'title' => 'The Team (Artisan 03)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'team3_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team3_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team3_img', 'label' => 'Portrait Image URL'],
                        ['type' => 'textarea', 'name' => 'team3_bio', 'label' => 'Short Bio'],
                    ],
                ],
                [
                    'title' => 'The Team (Artisan 04)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'team4_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team4_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team4_img', 'label' => 'Portrait Image URL'],
                        ['type' => 'textarea', 'name' => 'team4_bio', 'label' => 'Short Bio'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-category',
            'label' => 'Category Grid',
            'icon' => 'bi bi-grid-1x2',
            'group' => 'Pages',
            'title' => 'Category Layout Banner',
            'subtitle' => 'Configure the seasonal promotional banner appearing at the bottom of the category directory.',
            'cards' => [
                [
                    'title' => 'Promotional Section',
                    'fields' => [
                        [
                            'type' => 'select',
                            'name' => 'cat_banner_show',
                            'label' => 'Banner Visibility',
                            'options' => [
                                'on' => 'Show Banner',
                                'off' => 'Hide Banner',
                            ]
                        ],
                        ['type' => 'text', 'name' => 'cat_banner_badge', 'label' => 'Small Badge Tag', 'hint' => 'e.g. Limited Edition'],
                        ['type' => 'text', 'name' => 'cat_banner_title', 'label' => 'Main Headline'],
                        ['type' => 'textarea', 'name' => 'cat_banner_desc', 'label' => 'Marketing Description'],
                        ['type' => 'text', 'name' => 'cat_banner_btn_text', 'label' => 'Button Text'],
                        ['type' => 'text', 'name' => 'cat_banner_btn_url', 'label' => 'Button Link / URL'],
                        ['type' => 'text', 'name' => 'cat_banner_img', 'label' => 'Featured Product Image URL'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-modules',
            'label' => 'Modules',
            'icon' => 'bi bi-grid-3x3-gap',
            'group' => 'General',
            'title' => 'General module page settings',
            'subtitle' => 'Configure how your module pages (Contact, Shop, etc.) look.',
            'cards' => [
                [
                    'title' => 'Module Layout',
                    'fields' => [
                        [
                            'type' => 'select',
                            'name' => 'mod_layout',
                            'label' => 'Layout Type',
                            'options' => [
                                'default' => 'Default (Centered & Framed)',
                                'fullwidth' => 'Full Width',
                            ]
                        ],
                        [
                            'type' => 'select',
                            'name' => 'mod_show_title',
                            'label' => 'Show Title',
                            'options' => [
                                'on' => 'Show Title',
                                'off' => 'Hide Title',
                            ]
                        ],
                    ],
                ],
            ],
        ],
    ];

    $config = [
        'brandName' => 'Bakeshop',
        'brandVer' => 'v1.0.0',
        'brandAbbr' => 'BS',
        'brandIcon' => 'bi bi-cake2',
        'brandColor' => '#a7295a',
        'saveKey' => 'bakeshop_options_update',
    ];

    $builder = new OptionsBuilder($o, [], [], $config);

    if (isset($_POST['bakeshop_options_update'])) {
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            // Handle error or just skip
        } else {
            unset($_POST['bakeshop_options_update'], $_POST['token']);
            $json = json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);
            
            // Bypass cleanX for theme options to prevent JSON corruption
            if (Options::validate('bakeshop_options')) {
                Query::table('options')->where('name', 'bakeshop_options')->update(['value' => $json]);
            } else {
                Query::table('options')->insert(['name' => 'bakeshop_options', 'value' => $json]);
            }

            echo '<div id="gx-toast"><i class="bi bi-check-circle"></i> Bakeshop Settings saved!</div>';
            echo '<script>setTimeout(() => window.location.reload(), 1000);</script>';
        }
    }

    $builder->render($schema);

} else {
    echo '<div class="card p-5 text-center shadow-sm border-0 rounded-4">
        <div class="display-1 mb-4">🧁</div>
        <h3 class="fw-bold">Bakeshop Theme Engine</h3>
        <p class="text-muted">Initialize the database to begin personalizing your bakery experience.</p>
        <form method="post">
            <button type="submit" name="installdb" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow">
                Initialize Bakeshop
            </button>
        </form>
    </div>';
}
