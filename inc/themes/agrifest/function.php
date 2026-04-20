<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Agrifest Theme Functionality
 *
 * @since 1.0.0
 */

class Agrifest
{
    /**
     * Define default theme settings
     */
    public static function getDefaults()
    {
        return [
            'logo_text' => 'Harvest Estate',
            'primary_color' => '#0d631b',
            'secondary_color' => '#7a5649',
            'background_color' => '#f5fced',
            'footer_copyright' => '© 2024 The Curated Estate. All rights reserved.',
            'footer_desc' => 'Cultivating a sustainable future through meticulous farming and innovative ag-tech solutions.',
            'hero_tagline' => 'ESTATE GROWN & CURATED',
            'hero_title' => "Nature’s Finest, <br/><span class='text-primary italic'>Delivered.</span>",
            'hero_desc' => 'Experience the precision of modern ag-tech blended with the heritage of fertile soil. Direct from our estate to your kitchen.',
            'hero_img' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1600',
            'about_hero_tagline' => 'ESTABLISHED 1924',
            'about_hero_title' => 'The Legacy of the Soil',
            'about_hero_desc' => 'Cultivating excellence through four generations of sustainable farming and a deep-rooted commitment to the earth.',
            'about_hero_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ3CafK1ji1CEbFp77xc1QjGB34d5AyD_z8ULkqv_EO0aywx4xhktB3WH4hEjfWfSWVQvNu4mOLPgF6YUWT48KplxC04bHKvTO-oqqazEz5MG-MxYXVz4ta5ZHsK7X1acjc_AO8hHx7-XGmGwGUxvN0_5el6pauVNrXVp3Rw1f4G92Z3Zq-xLvxwCEimDI7BWl3zCy1i1SNXOFqPxb99bcLpPNsCVRi_uSE98-gdJnG4qUBKS9cZ7mJ4-aL5WoP6qT2hUENROOSAQ',
            'about_story_title' => 'Rooted in Tradition, Growing with Precision.',
            'about_story_desc1' => 'What began as a small patch of fertile land in the heart of the valley has blossomed into a curated estate. Our founders believed that if you treat the soil with respect, it returns the favor with unparalleled flavor and vitality.',
            'about_story_desc2' => 'Today, Harvest Estate remains family-owned. We merge traditional regenerative practices with modern precision agriculture to ensure that every leaf, fruit, and grain that leaves our gates is a masterpiece of nature.',
            'about_story_img1' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAZo4NU5ikKfhxXQaZYEwWnTlo_rLGrTpB1KzlQ8zU-S8f5_XFCGpzWIHkT-uu3v0TwZZaR0ximB2JH_Bn2Bcxy1mLqLk-KZFTaBR8gqthKK3v1uwGkXuBz-mrv5MGYE3fDudgQmmDzRHB2iiNe5lVRuTatibgvyrf2-bEzCdE_icOK7rDk5q52BHvxUky1Lbsi42-UhCbTy0JkugexVAM7ioV-n3ejcsXer4CIEzIXIHG7gja6jizfKeV3u2XOE1uXN9pxXs6o6jU',
            'about_story_img2' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC_lpl-ILERQE9n1VxfbafTClrCsK7Q492Y0tSb4EYOnedfs2cDrRuPClk3M12ROVwrV0vDFwalhV6PrQmKuHkPjBLEChanrwTbiHCg0YrYaAbProR0Kspvz6HFeRZz-VsTUO5NOt74Tf260iPW2lfbGsh0o8ZkWxdXxqonkILZ7D3VP3EX7M0yxDO-cLUyCap1jIYVXU4Knacf2AD-59HoaW32P7S9oGR126jExrw3zsIMn5-owMNC-LmXBDFMCjLNPj2oMhqwstA',
            'team1_name' => 'Arthur Thorne',
            'team1_role' => 'Head of Viticulture',
            'team1_quote' => 'Nature is the best teacher we have.',
            'team1_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDEWbjkBwkbX9VFzagUKdgi0KCjHNVd0U0gZjFIhPm2nOBqaD8Rzsh7sSBV-hTcAD1C852umHZIB46fEHoF5a7vknNo0AwiokR63teciPOGz0pnLd2KMnMLBX99vkNiZaDu-ZQ6Eo3qCHthhW4vF3u57ovEYHrTpwMrGOMvSRivIGE2-ic4O05_-FYtlf0YK6gAfvaEUtnRm2PGXrivA44zckI2bJFxmg4lqkrJTaN2NbXvIYqPHe-KbvSVOGjKSOsON-L3QBmmsZY',
            'team2_name' => 'Elena Rossi',
            'team2_role' => 'Operations Director',
            'team2_quote' => 'Efficiency is the key to sustainability.',
            'team2_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDFGM31BVvtnjhNVdvFF-GD15yNuoTZiRWGEqObcW0RpClWznrmFEdGzodSrDQalZyOt6mFCLmcehHcNhvAldLDmzXYXmmYZ_En8DVbiVBFJWuGKDlhnSAreXLkD36J3T_XqIZ_iq3dPn5nxcgS8UMGw-Kp4KzW6j3_FxrhKTHJE1Tbc0jRMM3Shdge-GqgOYhBqiGIJvku8ZOVKYXsIR3FkH6Bnu_w9oUNgk_BCWXJbfXUkQ0HazhGKZhltUvEHl4QRAIx3SqvbXQ',
            'team3_name' => 'Maya Chen',
            'team3_role' => 'Quality Assurance',
            'team3_quote' => 'The details are what create the magic.',
            'team3_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB-y-lgxz8OgKq3o9flhVk10hQso6YV3vEfATg7x19TPR5toKc90x7H8h3bNfahQHvHZIOlUbZU6tCY-nVPfwPWkl_pe6kVTifYyoSsErPSPk57fztIkia7AN2nSgpEd0evZPxwsrhygSaJ6LR1vHgy9xZVGgt4A98XUII4zMIi-DQbIXw3g9sHofOpWWub39pUSG9wCuOboinkf2tQniMOnlZKJWWVD7I6wGgFr2AvMsvsCCgl3yNs0BONBGMXNs8atlXYaG7eiAk',
            'team4_name' => 'Julian Vance',
            'team4_role' => 'Estate Manager',
            'team4_quote' => 'Protecting the legacy for the next generation.',
            'team4_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA7zYguBBMexWqBGdST3bo4WnVUizmmRARyTMoVw9GpfZPeYvdVYJhMbpEMoTul32g5-YXZy93frvaBhf8QEz2GQuxatrJw4Lw56OSWlYGH30kEL-u6AXRfCd6OGW9MPlB-DYNr8tNhpNr0NxMRVsDG8Y-xljpd5_AUv-HuzR9ku6GAkPqpGyjqRhkBYmnFxaoPlFtccXedimBzTXVrx5psymCqNxjBEde_BnVlNbriKbVLeDZUzNBJtcYgSegWnfyiyj3fxefEC-4',
            'profile_hero_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDZ3CafK1ji1CEbFp77xc1QjGB34d5AyD_z8ULkqv_EO0aywx4xhktB3WH4hEjfWfSWVQvNu4mOLPgF6YUWT48KplxC04bHKvTO-oqqazEz5MG-MxYXVz4ta5ZHsK7X1acjc_AO8hHx7-XGmGwGUxvN0_5el6pauVNrXVp3Rw1f4G92Z3Zq-xLvxwCEimDI7BWl3zCy1i1SNXOFqPxb99bcLpPNsCVRi_uSE98-gdJnG4qUBKS9cZ7mJ4-aL5WoP6qT2hUENROOSAQ',
            'profile_show_stats' => 'on',
            'blog_hero_title' => 'The Future of Regenerative Agriculture',
            'blog_hero_desc' => 'Healing the soil from the ground up. Discover ancient wisdom for modern times.',
            'blog_hero_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBWkwfNDg8_zUQfr6hQZAxSdbwdbR2iT1l0lOJ6hio_OksqpHEE-5c9d14svGrDSnWlub7n1qvRqkKg-LVh_VJduR5fQaaKBb8o2BknBZXEvDtV_HYBgU4aZZZvCCsjYNFy_ogMeVLJ8DdjbyZOa-li3GiPea4905PF_MeDtj_CoeqZf_793Xqi4b5ovb9RDQ5gJjEoc9M_4sPX26YdN31EZIUN6kbc5-via1YfSVw3e13_wiuzbQrQVKOHftlEWfzn7HHInOkZKoc',
            'blog_featured_post' => '0',
            'blog_bento_post1' => '0',
            'blog_newsletter_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8vYedfmRop68vIi5g2_zKM1Ktl2A6_CzyA0RAoaVBFieD0arl_Pm23G6Rt2nFmbVspmfUxgHZsT1Z4Qs_ukymUVFUufr7ZrVxSpTkCwnxmdoZ5HgqyweK4BnrXImvU2yTyHzS-UZ9n-h9Tse2gi17DJod_Wv9mMzc5dvOvgtltkApjNXkS5U-8UbFz4TeGZ6C3CMIHSZmaOzZIuSyREqOiIVHNwWl-jz8B3WJ1SOspFLbEtGqhTSdUUWSb6XtjA6yG0GhcQL2mQE',
            'blog_accent_title' => 'Precision Irrigation in 2024',
            'blog_accent_desc' => 'How satellite imagery and IoT sensors are saving millions of gallons across our estates.',
            'blog_accent_label' => 'SUSTAINABILITY',
            'blog_accent_icon' => 'water_drop',
            'cat_hero_img' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1200',
            'mod_layout' => 'framed',
            'mod_show_title' => 'on',
            'catalog_hero_tagline' => 'Seasonally Curated',
            'catalog_hero_title' => "The Earth's finest, <br/><span class='text-primary italic'>delivered to your estate.</span>",
            'catalog_badge_label' => 'Peak Season',
            'catalog_badge_text' => 'Heirloom Apples',
            'catalog_badge_icon' => 'potted_plant',
            'catalog_sidebar_title' => 'Estate Harvest',
            'catalog_promise_title' => 'Harvest Estate Promise',
            'catalog_promise_desc' => 'Every item is harvested within 24 hours of dispatch from our partner estates, guaranteeing peak vitality and flavor.',
            'catalog_inventory_label' => 'Artisan Items Found'
        ];
    }

    private static $_overrides = [];

    /**
     * Retrieve theme options with defaults and runtime overrides merged
     */
    public static function options()
    {
        static $_opts = null;
        if ($_opts === null) {
            $opt_raw = Options::get('agrifest_options');
            $o_db = json_decode($opt_raw ?? '', true);
            $defaults = self::getDefaults();
            $_opts = array_merge($defaults, (is_array($o_db) ? $o_db : []));
        }
        return array_merge($_opts, self::$_overrides);
    }

    /**
     * Set a dynamic override for a theme option (runtime only)
     */
    public static function setOption($key, $val)
    {
        self::$_overrides[$key] = $val;
    }

    /**
     * Helper to get a specific theme option value with fallback
     */
    public static function get($key, $default = '')
    {
        $opts = self::options();
        return isset($opts[$key]) && $opts[$key] !== '' ? $opts[$key] : $default;
    }

    public function __construct()
    {
        Hooks::attach('init', function() {
            AdminMenu::addChild('themes', [
                'label' => _('Agrifest Options'),
                'icon' => 'bi bi-palette2',
                'url' => 'index.php?page=themes&view=options',
                'access' => 0
            ]);
        });
    }

    /**
     * Check if theme options exist in database
     */
    public static function checkDB()
    {
        return Options::validate('agrifest_options');
    }
}
new Agrifest();

/**
 * Enqueue theme assets
 */
Hooks::attach('init', function () {
    // Register custom parameters for Nixomers products
    if (class_exists('Params')) {
        Params::register([
            'bottom' => [
                [
                    'groupname' => 'harvest_tracking',
                    'grouptitle' => 'Harvest Tracking & Quality',
                    'post_type' => 'nixomers',
                    'fields' => [
                        [
                            'title' => 'Batch Code',
                            'name' => 'batch_code',
                            'type' => 'text',
                            'placeholder' => 'e.g. BATCH-042',
                            'boxclass' => 'col-md-6'
                        ],
                        [
                            'title' => 'Harvest Date',
                            'name' => 'harvest_date',
                            'type' => 'date',
                            'boxclass' => 'col-md-6'
                        ],
                        [
                            'title' => 'Energy (kcal)',
                            'name' => 'nutri_energy',
                            'type' => 'text',
                            'placeholder' => '304 kcal',
                            'boxclass' => 'col-md-3'
                        ],
                        [
                            'title' => 'Carbohydrates',
                            'name' => 'nutri_carbs',
                            'type' => 'text',
                            'placeholder' => '82g',
                            'boxclass' => 'col-md-3'
                        ],
                        [
                            'title' => 'Sugars',
                            'name' => 'nutri_sugars',
                            'type' => 'text',
                            'placeholder' => '80g',
                            'boxclass' => 'col-md-3'
                        ],
                        [
                            'title' => 'Protein',
                            'name' => 'nutri_protein',
                            'type' => 'text',
                            'placeholder' => '0.3g',
                            'boxclass' => 'col-md-3'
                        ]
                    ]
                ],
                [
                    'groupname' => 'the_process',
                    'grouptitle' => 'The Process (Heritage Story)',
                    'post_type' => 'nixomers',
                    'fields' => [
                        [
                            'title' => 'Process Headline',
                            'name' => 'proc_title',
                            'type' => 'text',
                            'placeholder' => 'e.g. From Bloom to Bottle',
                            'boxclass' => 'col-md-12'
                        ],
                        [
                            'title' => 'Process Description',
                            'name' => 'proc_desc',
                            'type' => 'textarea',
                            'rows' => 3,
                            'boxclass' => 'col-md-12'
                        ],
                        [
                            'title' => 'Process Hero Image',
                            'name' => 'proc_image',
                            'type' => 'media',
                            'boxclass' => 'col-md-12'
                        ],
                        [
                            'title' => 'Key Process Points',
                            'name' => 'proc_points',
                            'type' => 'repeater',
                            'fields' => [
                                [
                                    'title' => 'Point Detail',
                                    'name' => 'point',
                                    'type' => 'text'
                                ]
                            ],
                            'boxclass' => 'col-md-12'
                        ]
                    ]
                ]
            ],
            'sidebar' => [
                [
                    'groupname' => 'extra_images',
                    'grouptitle' => 'Additional Assets',
                    'post_type' => 'nixomers',
                    'fields' => [
                        [
                            'title' => 'Custom Image 1',
                            'name' => 'custom_image_1',
                            'type' => 'media'
                        ],
                        [
                            'title' => 'Custom Image 2',
                            'name' => 'custom_image_2',
                            'type' => 'media'
                        ]
                    ]
                ]
            ]
        ]);
    }

    // Agrifest theme stylesheet
    Asset::register(
        'agrifest-css',
        'css',
        Url::theme() . 'assets/css/agrifest.css',
        'header',
        [],
        30,
        'frontend'
    );
    Asset::enqueue('agrifest-css');
});
