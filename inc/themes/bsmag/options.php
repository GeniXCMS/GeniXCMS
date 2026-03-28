<?php
if (User::access(0)) {
    // 1. Initial State & Logic
    $bsmag_theme_options = Options::isExist('bsmag_theme_options');
    $data = [];

    // Trigger installation if missing
    if (!$bsmag_theme_options && isset($_POST['install_bsmag_options'])) {
        $var = array('bsmag_theme_options' => '{"bsmag_intro_img":"","bsmag_intro_post_id":"","bsmag_featured_posts":"","bsmag_about_site":"","bsmag_analytics":""}');
        Options::insert($var);
        $data['alertSuccess'][] = "BS-Mag Theme Options Installed Successfully!";
        $bsmag_theme_options = true; // Update state after install
    }

    // 2. Handle Save Request
    if (isset($_POST['bsmag_save_options'])) {
        unset($_POST['bsmag_save_options']);
        $opt = array();
        foreach ($_POST as $k => $v) {
            $opt[$k] = Typo::cleanX($v);
        }
        $opt_json = json_encode($opt);
        if (Options::update('bsmag_theme_options', $opt_json)) {
            // Render AJAX Response with Toast
            if (isset($_GET['ajax'])) {
                OptionsBuilder::renderCSS();
                echo '<div class="gx-toast-container">
                    <div class="gx-toast active" role="alert">
                        <i class="fa fa-check-circle"></i> 
                        <span>' . _("Settings Saved Successfully!") . '</span>
                    </div>
                </div>';
                exit;
            }
        } else {
            $data['alertDanger'][] = "Error: Configuration Not Saved";
        }
    }

    // 3. Prepare Options Builder
    if ($bsmag_theme_options) {
        $opt_raw = Options::get('bsmag_theme_options');
        $opt = json_decode($opt_raw, true) ?: [];

        $builder = new OptionsBuilder($opt, [], [], [
            'brandName'  => 'BS-Mag Premium',
            'brandVer'   => 'v3.0.0',
            'brandAbbr'  => 'BM',
            'brandColor' => '#dc3545', // Editorial Red
            'saveKey'    => 'bsmag_save_options'
        ]);

        $schema = [
            [
                'id' => 'home_settings',
                'label' => 'Home Content',
                'icon' => 'fa fa-home',
                'title' => 'Homepage & Hero Experience',
                'subtitle' => 'Manage the editorial focus of your homepage hero and featured sections.',
                'cards' => [
                    [
                        'title' => 'Hero Spotlight',
                        'cols' => 2,
                        'fields' => [
                            [
                                'type' => 'text',
                                'name' => 'bsmag_intro_img',
                                'label' => 'Hero Background Image',
                                'placeholder' => 'https://example.com/banner.jpg',
                                'hint' => 'Full-width image for the top intro section.'
                            ],
                            [
                                'type' => 'text',
                                'name' => 'bsmag_intro_post_id',
                                'label' => 'Spotlight Post ID',
                                'placeholder' => 'e.g. 101',
                                'hint' => 'Database ID of the post to highlight.'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Editorial Grid',
                        'fields' => [
                            [
                                'type' => 'text',
                                'name' => 'bsmag_featured_posts',
                                'label' => 'Featured Post IDs (Comma Separated)',
                                'placeholder' => '1,12,45',
                                'hint' => 'IDs of articles to display in the grid below hero.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 'footer_identity',
                'label' => 'Branding & Footer',
                'icon' => 'fa fa-id-card',
                'title' => 'Brand & Footer Configuration',
                'subtitle' => 'Customize your site about section and footer details.',
                'cards' => [
                    [
                        'title' => 'About Section',
                        'fields' => [
                            [
                                'type' => 'textarea',
                                'name' => 'bsmag_about_site',
                                'label' => 'About Site Text',
                                'rows' => 4,
                                'hint' => 'Appears in the sidebar or footer blocks.'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 'typography',
                'label' => 'Typography',
                'icon' => 'fa fa-font',
                'title' => 'Complete Editorial Typography',
                'subtitle' => 'Fine-tune every text element of your magazine theme from headings to lists.',
                'sections' => [
                    [
                        'title' => 'Global Text Styles',
                        'cards' => [
                            [
                                'type' => 'typo_row',
                                'label' => 'Body & Paragraphs',
                                'prefix' => 'typo_body'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Lists (UL/OL)',
                                'prefix' => 'typo_list'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Post Meta Info',
                                'prefix' => 'typo_meta'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Blockquotes',
                                'prefix' => 'typo_blockquote'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Pagination Buttons',
                                'prefix' => 'typo_pagination'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Headings Hierarchy',
                        'cards' => [
                            [
                                'type' => 'typo_row',
                                'label' => 'Heading 1 (H1)',
                                'prefix' => 'typo_h1'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Heading 2 (H2)',
                                'prefix' => 'typo_h2'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Heading 3 (H3)',
                                'prefix' => 'typo_h3'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Heading 4 (H4)',
                                'prefix' => 'typo_h4'
                            ]
                        ]
                    ],
                    [
                        'title' => 'Brand & Navigation',
                        'cards' => [
                            [
                                'type' => 'color',
                                'name' => 'link_color',
                                'label' => 'Theme Accent Color',
                                'hint' => 'Used for links and critical UI accents.'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Site Brand / Logo Text',
                                'prefix' => 'typo_brand'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Main Navigation',
                                'prefix' => 'typo_nav'
                            ],
                            [
                                'type' => 'typo_row',
                                'label' => 'Hero Spotlight Title',
                                'prefix' => 'typo_hero_title'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 'advanced_scripts',
                'label' => 'Advanced Integration',
                'icon' => 'fa fa-code',
                'title' => 'Custom Scripts & Analytics',
                'subtitle' => 'Add external tracking codes or custom JavaScript globally.',
                'cards' => [
                    [
                        'title' => 'Header & Footer Scripts',
                        'fields' => [
                            [
                                'type' => 'textarea',
                                'name' => 'bsmag_analytics',
                                'label' => 'Custom Scripts (JS/Gtag)',
                                'rows' => 8,
                                'hint' => 'Will be loaded in the theme footer block.'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // 4. Render Panel
        $builder->render($schema);

    } else {
        // Installation Required
        echo '<div class="container-fluid mt-4"><div class="alert alert-warning shadow-sm border-0 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <strong>' . _("Settings Missing!") . '</strong><br>' . 
                _("BS-Mag Theme Options are not installed yet.") . '
                <form action="" method="post" class="mt-3">
                    <button class="btn btn-primary btn-sm px-4" name="install_bsmag_options">
                        <i class="bi bi-tools me-1"></i> ' . _("Install Configuration") . '
                    </button>
                </form>
            </div>
        </div></div>';
    }

    System::alert($data);
} else {
    Control::error('noaccess');
}
?>