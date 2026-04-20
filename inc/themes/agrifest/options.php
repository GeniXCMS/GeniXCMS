<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Agrifest - Theme Options Admin Panel
 */

// 1. Initial Installation
if (isset($_POST['installdb'])) {
    $arr = Agrifest::getDefaults();
    Options::insert(['agrifest_options' => json_encode($arr)]);
}

// 2. Load Options UI
if (Agrifest::checkDB()) {
    $o = Agrifest::options();

    $schema = [
        [
            'id' => 'tab-branding',
            'label' => 'Identity',
            'icon' => 'bi bi-palette',
            'group' => 'Core',
            'active' => true,
            'title' => 'Visual Identity',
            'subtitle' => 'Define your estate s brand colors and logo.',
            'cards' => [
                [
                    'title' => 'Logo & Text',
                    'fields' => [
                        ['type' => 'text', 'name' => 'logo_text', 'label' => 'Brand Name'],
                    ],
                ],
                [
                    'title' => 'Core Palette',
                    'cols' => 3,
                    'fields' => [
                        ['type' => 'color', 'name' => 'primary_color', 'label' => 'Primary Brand'],
                        ['type' => 'color', 'name' => 'secondary_color', 'label' => 'Secondary Accent'],
                        ['type' => 'color', 'name' => 'background_color', 'label' => 'Background'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-hero',
            'label' => 'Hero Banner',
            'icon' => 'bi bi-image',
            'group' => 'General',
            'title' => 'Hero Section Configuration',
            'subtitle' => 'The main top section of your home page.',
            'cards' => [
                [
                    'title' => 'Hero Content',
                    'fields' => [
                        ['type' => 'text', 'name' => 'hero_tagline', 'label' => 'Upper Tagline'],
                        ['type' => 'text', 'name' => 'hero_title', 'label' => 'Main Headline'],
                        ['type' => 'textarea', 'name' => 'hero_desc', 'label' => 'Introduction Text'],
                    ],
                ],
                [
                    'title' => 'Hero Background',
                    'fields' => [
                        ['type' => 'text', 'name' => 'hero_img', 'label' => 'Hero Image URL', 'hint' => 'Use high-quality landscape imagery.'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-bento',
            'label' => 'Home Bento',
            'icon' => 'bi bi-grid-1x2',
            'group' => 'General',
            'title' => 'Featured Categories (Bento Grid)',
            'subtitle' => 'Set the categories and dynamic assets for the home page bento block.',
            'cards' => [
                [
                    'title' => 'Bento Item 1 (Main/Large)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'bento1_cat', 'label' => 'Category ID / Slug', 'hint' => 'Category identifying slug or ID (e.g. vegetables)'],
                        ['type' => 'text', 'name' => 'bento1_title', 'label' => 'Override Title', 'hint' => 'Leave blank to use category name'],
                        ['type' => 'textarea', 'name' => 'bento1_desc', 'label' => 'Short Description', 'hint' => 'Only for the first large bento card'],
                        ['type' => 'text', 'name' => 'bento1_img', 'label' => 'Image URL', 'hint' => '16:10 aspect ratio recommended'],
                    ]
                ],
                [
                    'title' => 'Bento Item 2 (Top Right)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'bento2_cat', 'label' => 'Category ID / Slug', 'hint' => 'Category identifying slug or ID (e.g. fruits)'],
                        ['type' => 'text', 'name' => 'bento2_title', 'label' => 'Override Title', 'hint' => 'Leave blank to use category name'],
                        ['type' => 'text', 'name' => 'bento2_img', 'label' => 'Image URL', 'hint' => 'Square aspect ratio recommended'],
                    ]
                ],
                [
                    'title' => 'Bento Item 3 (Bottom Right)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'bento3_cat', 'label' => 'Category ID / Slug', 'hint' => 'Category identifying slug or ID (e.g. grains)'],
                        ['type' => 'text', 'name' => 'bento3_title', 'label' => 'Override Title', 'hint' => 'Leave blank to use category name'],
                        ['type' => 'text', 'name' => 'bento3_img', 'label' => 'Image URL', 'hint' => 'Square aspect ratio recommended'],
                    ]
                ]
            ]
        ],
        [
            'id' => 'tab-footer',
            'label' => 'Footer',
            'icon' => 'bi bi-layout-sidebar-inset',
            'group' => 'General',
            'title' => 'Footer Settings',
            'subtitle' => 'Manage branding and copyright info at the bottom.',
            'cards' => [
                [
                    'title' => 'Footer Info',
                    'fields' => [
                        ['type' => 'textarea', 'name' => 'footer_desc', 'label' => 'Quick About'],
                        ['type' => 'text', 'name' => 'footer_copyright', 'label' => 'Copyright Notice'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-about',
            'label' => 'About Us',
            'icon' => 'bi bi-info-circle',
            'group' => 'Pages',
            'title' => 'Our Story & Legacy',
            'subtitle' => 'Configure the content for your legacy storytelling page.',
            'cards' => [
                [
                    'title' => 'About Hero',
                    'fields' => [
                        ['type' => 'text', 'name' => 'about_hero_tagline', 'label' => 'Hero Tagline'],
                        ['type' => 'text', 'name' => 'about_hero_title', 'label' => 'Hero Title'],
                        ['type' => 'textarea', 'name' => 'about_hero_desc', 'label' => 'Hero Description'],
                        ['type' => 'text', 'name' => 'about_hero_img', 'label' => 'Hero Image URL'],
                    ],
                ],
                [
                    'title' => 'Story Content',
                    'fields' => [
                        ['type' => 'text', 'name' => 'about_story_title', 'label' => 'Story Headline'],
                        ['type' => 'textarea', 'name' => 'about_story_desc1', 'label' => 'Story Paragraph 1'],
                        ['type' => 'textarea', 'name' => 'about_story_desc2', 'label' => 'Story Paragraph 2'],
                        ['type' => 'text', 'name' => 'about_story_img1', 'label' => 'Main Story Image'],
                        ['type' => 'text', 'name' => 'about_story_img2', 'label' => 'Small Accent Image'],
                    ],
                ],
                [
                    'title' => 'Team Member 1',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'team1_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team1_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team1_quote', 'label' => 'Personal Quote'],
                        ['type' => 'text', 'name' => 'team1_img', 'label' => 'Portrait URL'],
                    ],
                ],
                [
                    'title' => 'Team Member 2',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'team2_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team2_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team2_quote', 'label' => 'Personal Quote'],
                        ['type' => 'text', 'name' => 'team2_img', 'label' => 'Portrait URL'],
                    ],
                ],
                [
                    'title' => 'Team Member 3',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'team3_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team3_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team3_quote', 'label' => 'Personal Quote'],
                        ['type' => 'text', 'name' => 'team3_img', 'label' => 'Portrait URL'],
                    ],
                ],
                [
                    'title' => 'Team Member 4',
                    'cols' => 2,
                    'fields' => [
                        ['type' => 'text', 'name' => 'team4_name', 'label' => 'Name'],
                        ['type' => 'text', 'name' => 'team4_role', 'label' => 'Role'],
                        ['type' => 'text', 'name' => 'team4_quote', 'label' => 'Personal Quote'],
                        ['type' => 'text', 'name' => 'team4_img', 'label' => 'Portrait URL'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-profiles',
            'label' => 'Stewards',
            'icon' => 'bi bi-person-badge',
            'group' => 'Pages',
            'title' => 'Steward Profile Layout',
            'subtitle' => 'Global settings for author and contributor profile pages.',
            'cards' => [
                [
                    'title' => 'Profile Hero & Visuals',
                    'fields' => [
                        ['type' => 'text', 'name' => 'profile_hero_img', 'label' => 'Default Hero Background URL', 'hint' => 'Background used if individual author hasn\'t set one.'],
                        [
                            'type' => 'select',
                            'name' => 'profile_show_stats',
                            'label' => 'Show Achievement Stats',
                            'options' => [
                                'on' => 'Show Stats (Articles, Readers, Projects)',
                                'off' => 'Hide Stats',
                            ]
                        ],
                    ],
                ],
                [
                    'title' => 'Global Profile Text',
                    'fields' => [
                        ['type' => 'text', 'name' => 'profile_role_label', 'label' => 'Expert Role Label', 'default' => 'Expert Contributor'],
                        ['type' => 'text', 'name' => 'profile_certification_label', 'label' => 'Certification Label', 'default' => 'Estate Certified'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-blog',
            'label' => 'Estate Blog',
            'icon' => 'bi bi-journal-text',
            'group' => 'Pages',
            'title' => 'Blog Landing Configuration',
            'subtitle' => 'Curate the editorial experience for your journal landing page.',
            'cards' => [
                [
                    'title' => 'Editorial Highlight (Hero)',
                    'fields' => [
                        ['type' => 'text', 'name' => 'blog_featured_post', 'label' => 'Featured Post ID', 'hint' => 'Pick a post ID to showcase as the main cover story.'],
                        ['type' => 'text', 'name' => 'blog_hero_title', 'label' => 'Hero Headline (Fallback)', 'hint' => 'Used if no post ID is provided.'],
                        ['type' => 'textarea', 'name' => 'blog_hero_desc', 'label' => 'Hero Description (Fallback)'],
                        ['type' => 'text', 'name' => 'blog_hero_img', 'label' => 'Hero Cover Image (Fallback)'],
                    ],
                ],
                [
                    'title' => 'Bento Spotlight',
                    'fields' => [
                        ['type' => 'text', 'name' => 'blog_bento_post1', 'label' => 'Bento Post 1 ID', 'hint' => 'The large horizontal post in the insights grid.'],
                        ['type' => 'text', 'name' => 'blog_accent_title', 'label' => 'Accent Card Title'],
                        ['type' => 'textarea', 'name' => 'blog_accent_desc', 'label' => 'Accent Card Description'],
                        ['type' => 'text', 'name' => 'blog_accent_label', 'label' => 'Accent Card Label', 'hint' => 'e.g. SUSTAINABILITY'],
                        ['type' => 'text', 'name' => 'blog_accent_icon', 'label' => 'Accent Card Icon', 'hint' => 'Material Symbols icon name (e.g. water_drop, eco, etc.)'],
                    ],
                ],
                [
                    'title' => 'Newsletter Section',
                    'fields' => [
                        ['type' => 'text', 'name' => 'blog_newsletter_img', 'label' => 'Newsletter Backdrop URL'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-catalog',
            'label' => 'Store Catalog',
            'icon' => 'bi bi-shop',
            'group' => 'Pages',
            'title' => 'Product Gallery Layout',
            'subtitle' => 'Curate the visual experience for your estate marketplace.',
            'cards' => [
                [
                    'title' => 'Catalog Hero Section',
                    'fields' => [
                        ['type' => 'text', 'name' => 'catalog_hero_tagline', 'label' => 'Upper Tagline', 'default' => 'Seasonally Curated'],
                        ['type' => 'text', 'name' => 'catalog_hero_title', 'label' => 'Main Headline', 'default' => "The Earth's finest, <br/><span class='text-primary italic'>delivered to your estate.</span>"],
                        ['type' => 'text', 'name' => 'catalog_hero_img', 'label' => 'Hero Background URL'],
                    ],
                ],
                [
                    'title' => 'Sidebar & Content Labels',
                    'fields' => [
                        ['type' => 'text', 'name' => 'catalog_sidebar_title', 'label' => 'Sidebar Category Heading', 'default' => 'Estate Harvest'],
                        ['type' => 'text', 'name' => 'catalog_promise_title', 'label' => 'Estate Promise Title', 'default' => 'Harvest Estate Promise'],
                        ['type' => 'textarea', 'name' => 'catalog_promise_desc', 'label' => 'Estate Promise Text'],
                        ['type' => 'text', 'name' => 'catalog_inventory_label', 'label' => 'Inventory Count Label', 'default' => 'Artisan Items Found'],
                    ],
                ],
                [
                    'title' => 'Featured Highlight Badge',
                    'fields' => [
                        ['type' => 'text', 'name' => 'catalog_badge_label', 'label' => 'Badge Label', 'default' => 'Peak Season'],
                        ['type' => 'text', 'name' => 'catalog_badge_text', 'label' => 'Highlight Product', 'default' => 'Heirloom Apples'],
                        ['type' => 'text', 'name' => 'catalog_badge_icon', 'label' => 'Material Icon', 'default' => 'potted_plant'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'tab-cat',
            'label' => 'Category Layout',
            'icon' => 'bi bi-collection',
            'group' => 'Pages',
            'title' => 'Category Landing Pages',
            'subtitle' => 'Global defaults for your topic-based landing pages.',
            'cards' => [
                [
                    'title' => 'Category Hero Defaults',
                    'fields' => [
                        ['type' => 'text', 'name' => 'cat_hero_img', 'label' => 'Default Hero Backdrop', 'hint' => 'Used if no specific image is set for the category.'],
                    ],
                ],
                [
                    'title' => 'Category Specific Layouts',
                    'subtitle' => 'Customize the hero and featured content for each specific topic.',
                    'fields' => (function() {
                        $fields = [];
                        $cats = Query::table('cat')->where('type', 'post')->orderBy('name', 'ASC')->get();
                        if (!empty($cats)) {
                            foreach ($cats as $c) {
                                $fields[] = [
                                    'type' => 'text',
                                    'name' => 'f_cat_img_' . $c->id,
                                    'label' => '🖼️ ' . $c->name . ': Hero Backdrop',
                                    'hint' => 'URL for the top banner background.'
                                ];
                                $fields[] = [
                                    'type' => 'text',
                                    'name' => 'f_cat_post_' . $c->id,
                                    'label' => '📰 ' . $c->name . ': Featured Posts',
                                    'hint' => 'Comma separated Post IDs (e.g. 1, 2, 3).'
                                ];
                                $fields[] = ['type' => 'divider'];
                            }
                        }
                        return $fields;
                    })()
                ]
            ],
        ],
        [
            'id' => 'tab-cat-nix',
            'label' => 'Store Categories',
            'icon' => 'bi bi-tags',
            'group' => 'Pages',
            'title' => 'Product Category Landing',
            'subtitle' => 'Global defaults and secondary promotional sections for your store categories.',
            'cards' => [
                [
                    'title' => 'Category Defaults',
                    'fields' => [
                        ['type' => 'text', 'name' => 'cat_nix_hero_tagline', 'label' => 'Upper Tagline', 'default' => 'The Curated Selection'],
                        ['type' => 'text', 'name' => 'cat_nix_hero_img', 'label' => 'Default Hero Backdrop'],
                    ]
                ],
                [
                    'title' => 'Secondary Promotion Section',
                    'fields' => [
                        ['type' => 'select', 'name' => 'cat_nix_promo_show', 'label' => 'Show Promotion Section', 'options' => ['on' => 'Show', 'off' => 'Hide']],
                        ['type' => 'text', 'name' => 'cat_nix_promo_tagline', 'label' => 'Promo Tagline', 'default' => 'Coming Next Harvest'],
                        ['type' => 'text', 'name' => 'cat_nix_promo_title', 'label' => 'Promo Title', 'default' => 'Only Vegetables'],
                        ['type' => 'textarea', 'name' => 'cat_nix_promo_desc', 'label' => 'Promo Description', 'default' => 'Our vegetable gardens are currently maturing under the careful watch of our estate agronomists. Expect crisp heirlooms and nutrient-dense greens within the coming weeks.'],
                        ['type' => 'text', 'name' => 'cat_nix_promo_img', 'label' => 'Promo Image URL'],
                        ['type' => 'text', 'name' => 'cat_nix_promo_btn', 'label' => 'Promo Button Text', 'default' => 'Notify Me of Harvest'],
                    ]
                ],
                [
                    'title' => 'Category Specific Backdrops',
                    'subtitle' => 'Set custom hero images for specific store categories.',
                    'fields' => (function() {
                        $fields = [];
                        $cats = Query::table('cat')->where('type', 'nixomers')->orderBy('name', 'ASC')->get();
                        if (empty($cats)) {
                            // Support alternative typo if needed based on older DB structures
                            $cats = Query::table('cat')->where('type', 'nixomer')->orderBy('name', 'ASC')->get();
                        }
                        if (!empty($cats)) {
                            foreach ($cats as $c) {
                                $fields[] = [
                                    'type' => 'text',
                                    'name' => 'f_cat_nix_img_' . $c->id,
                                    'label' => '🖼️ ' . $c->name . ': Hero Backdrop'
                                ];
                                $fields[] = ['type' => 'divider'];
                            }
                        }
                        return $fields;
                    })()
                ]
            ]
        ],
        [
            'id' => 'tab-modules',
            'label' => 'Modules',
            'icon' => 'bi bi-grid-3x3-gap',
            'group' => 'General',
            'title' => 'Module Display Settings',
            'subtitle' => 'Configure how modular content (e.g., Shop, Forum) appears.',
            'cards' => [
                [
                    'title' => 'Framework Layout',
                    'fields' => [
                        [
                            'type' => 'select',
                            'name' => 'mod_layout',
                            'label' => 'Canvas Style',
                            'options' => [
                                'framed' => 'Framed (Centered with Background)',
                                'full' => 'Full Width (Edge-to-Edge Canvas)'
                            ]
                        ],
                        [
                            'type' => 'select',
                            'name' => 'mod_show_title',
                            'label' => 'Display Heading',
                            'options' => [
                                'on' => 'Show Module Title Header',
                                'off' => 'Hide Header (Clean Contained View)'
                            ]
                        ]
                    ]
                ]
            ]
        ],
    ];

    $config = [
        'brandName' => 'Agrifest',
        'brandVer' => 'v1.0.0',
        'brandAbbr' => 'AG',
        'brandIcon' => 'bi bi-leaf',
        'brandColor' => '#0d631b',
        'saveKey' => 'agrifest_options_update',
    ];

    $builder = new OptionsBuilder($o, [], [], $config);

    if (isset($_POST['agrifest_options_update'])) {
        if (!isset($_POST['token']) || !Token::isExist($_POST['token'])) {
            // Handle error or just skip
        } else {
            unset($_POST['agrifest_options_update'], $_POST['token']);
            $json = json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);
            
            if (Options::validate('agrifest_options')) {
                Query::table('options')->where('name', 'agrifest_options')->update(['value' => $json]);
            } else {
                Query::table('options')->insert(['name' => 'agrifest_options', 'value' => $json]);
            }

            echo '<div id="gx-toast"><i class="bi bi-check-circle"></i> Agrifest Settings saved!</div>';
            echo '<script>setTimeout(() => window.location.reload(), 1000);</script>';
        }
    }

    $builder->render($schema);

} else {
    echo '<div class="card p-5 text-center shadow-sm border-0 rounded-4">
        <div class="display-1 mb-4">🌿</div>
        <h3 class="fw-bold">Agrifest Theme Engine</h3>
        <p class="text-muted">Initialize the database to begin personalizing your estate experience.</p>
        <form method="post">
            <button type="submit" name="installdb" class="btn btn-success px-5 py-3 rounded-pill fw-bold shadow">
                Initialize Agrifest
            </button>
        </form>
    </div>';
}
