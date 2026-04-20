<?php
/**
 * Artisan Atelier - Modern Theme Engine
 */
defined('GX_LIB') or die('Direct Access Not Allowed!');

class ArtisanAtelier
{
    public static $opt;

    public function __construct()
    {
        if (self::checkDB()) {
            self::$opt = self::parseDB();

            // Register Widget Locations (Overwriting core IDs for clean integration)
            Widget::addLocation('footer_1', 'Footer: Brand & About');
            Widget::addLocation('footer_2', 'Footer: Column 1');
            Widget::addLocation('footer_3', 'Footer: Column 2');
            Widget::addLocation('footer_4', 'Footer: Column 3');

            // Register Theme CSS via Asset class
            Asset::register('artisan-style', 'css', Url::theme() . 'css/style.css', 'header', [], 20, 'frontend');

            // Register External Dependencies
            Asset::register('tailwind-cdn', 'js', 'https://cdn.tailwindcss.com?plugins=forms,container-queries', 'header', [], 10, 'frontend');
            Asset::register('google-fonts', 'css', 'https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap', 'header', [], 10, 'frontend');
            Asset::register('material-symbols', 'css', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap', 'header', [], 10, 'frontend');

            if (!defined('GX_ADMIN') || !GX_ADMIN) {
                Asset::enqueue(['google-fonts', 'material-symbols', 'tailwind-cdn', 'artisan-style']);
            }
        }

        // Register "Atelier Options" sub-item under the Themes admin menu.
        Hooks::attach('init', function () {
            AdminMenu::addChild('themes', [
                'label' => _('Atelier Options'),
                'url' => 'index.php?page=themes&view=options',
                'icon' => 'bi bi-palette',
                'access' => 0,
            ]);
        });
    }

    public static function checkDB()
    {
        return Options::validate('artisan_options');
    }

    public static function getDefaults()
    {
        return [
            'primary_color' => '#a74632',
            'secondary_color' => '#4f6c43',
            'surface_color' => '#fffbff',
            'on_surface_color' => '#393832',
            'hero_title' => 'Objects of Quiet Intention',
            'hero_subtitle' => "Curating a collection of handmade ceramics, linens, and art that celebrate the slow rhythm of the maker's hand.",
            'hero_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAnqrKkno9gZRdt0fYy2dnSelztird_BPZvV3NRPJTtKL5EvPeFix4qEOmxGKSCoQD9t6IpiNsi1Fz8Zrb9T05I3mfsvGAQ9Pa5d1nAQXJgosEYqdyWZQs1Diapxb-wnu4p-GTxV9B-jAPcU4UGhIfSivzLuQzKkAHqzElsKJdns1uHLjNoGOUc8A_GBa1yQvT67jwLjeaFZ3j9rknYPK3Ft21sjvVG2VcZpXkfMPXnBHTysEF9kx_lWVqJ2m_f-s1i_NWRGmA86Xo',
            'show_newsletter' => 'on',
            'footer_text' => '© 2024 The Artisanal Atelier. Handcrafted with soul and intention.',
            'custom_css' => '',
            'mod_layout_type' => 'standard',
            'mod_show_title' => 'on',
            'catalog_hero_tag' => 'EST. 2024',
            'catalog_hero_title' => 'Curated for the Mindful Home',
            'catalog_hero_subtitle' => 'Discover objects that carry the soul of their maker. Our ceramics, linens, and prints are crafted for those who cherish intentional living.',
            'catalog_hero_btn1' => 'Explore Ceramics',
            'catalog_hero_url1' => '#',
            'catalog_hero_btn2' => 'New Arrivals',
            'catalog_hero_url2' => '#',
            'catalog_hero_img1' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDNdQFfJCptn_KZDLCZKuFgcKRkfnc2-JT1pmi0YvpTTyJaGsHxjvNZaSip7wvtmAhuu1svikQP6ElOpu7-POmRjNxSDB6DhixSAH6-YEcBJrZYJSo-KA1J90yNoQyS-neePCuxx7Fgt44X8qsB-KnKwOEzpNHoYRHdqPzQuFmPaz4O1kd38tZs1wLFOu0OkqeLkJJBM_vmx-CdPq7cMyLocPZ9S7kqBc4TAQ523txGMv3J6kKTWVC3BMh9ytsFba7piZ9N2_CiErM',
            'catalog_hero_img2' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCHnf3KHNHp4MYyM_TcuFdiT_DQqHGAXbrmiPmUSkGREMJa7006GimQd0vYv6lFzJcDEcaUIl-acWIXyQ1KI6jONbSZtsOUw7-RcvdXJJCnhfkTjPCN5p62BxOH6ICpR-4E3zPRZQfzjTU0UHcuY-Gb4zGwCySyX8ZcRxYqy9H2F38K_RoVwjh2iRLc1hW9dTRj_UMNwiLVJlKdVS4bOBxoZQkpHUwuYGc626oc0pSTnx4s9DO-w_aNKjkXk3znJfV5oIkjBnNRkXg',
            'catalog_work_show' => 'on',
            'catalog_work_tag' => 'Hands-on Learning',
            'catalog_work_title' => 'Learn the Craft at our studio',
            'catalog_work_desc' => 'Join us for weekend workshops in hand-building and glazing. Connect with the material and create your own artisanal heirlooms.',
            'catalog_work_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCkxwwpWutZQl_JC28Yfd0CEDS9RDfJkGuxy65NCPC-neMejSgYV8lnQBfBYqtjbzPlv2afDLllYj2uSsiec_qhuiaJix0uRJ7qKICglTQDhaR6zZLkjhZ-Bjb7v0hhZw2ZmHmBxyv3OoYmVEKcqMr5yPCqelAreFM6fnSiO37T3hAYcmJbm9NLrBEuOGPs0H-sWQdutVb7LKJkdHmHgb5cuNMsuKDUPKFTd1foQ7cxyxAfSo8VUaFvRlpC1AkuRIGdCeTJdH1jse8',
            'catalog_work_btn' => 'View Workshop Schedule',
            'catalog_work_url' => '#',
            'catalog_work_feat1' => 'Weekend Immersion',
            'catalog_work_icon1' => 'event',
            'catalog_work_feat2' => 'Professional Kilns',
            'catalog_work_icon2' => 'oven_gen',
            'catalog_work_feat3' => 'Take Home Your Art',
            'catalog_work_icon3' => 'cardboard_box',
            'process_title' => 'Follow the <br><span class="italic">Process</span>',
            'process_desc' => 'Transparency is the core of our craft. Witness the journey from raw clay to the finished masterpiece through our digital journal.',
            'process_video_label' => 'Watch The Film',
            'process_img1' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCrrswF8-N8vT-BHoAOgsOxeiEq5_xyTkAGQTX6fan86vGscNUq0tmWojXGNdTTyWVsj7TOLuEDIUStgwPGB66T0qTzkaFis9wDv0dYz9mQDcPWsaf_VaIpG2zVRW_sI2kmWAE7Dy8HrNCoCdWA3w2gpMVbPzLjd9FVuHTBrva9n_KZGftw_gsSXWL7AJaXJfyRGAHTKDU_mXLXuHldnojf9lAtQ5J1iNkl146yBix75gwlEVJuqCRKeS94OCzvogOBsyVrZwqCQzA',
            'process_step1_title' => '01. The Firing Stage',
            'process_step1_desc' => 'Where the earth transforms. Temperatures reaching 1200°C solidify the soul of the piece.',
            'process_img2' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDOmMptxEf2CzRPxhiRyqYgTxOm56Rw8WT_FcMC_ful29xmgZpx0O3SnwA6aS_mjK1yhQC7T9Yc_SCtvWPQZKhnQwYwxbRgE-wtzlCaWeA2Nop4jPFiz3YkdPPNOo3fT8k0IIAjf7tznd9hrOq-9n4sx3s0duwBZLXzTYmyVXdWMF3TdW6e_R7_x1oIlhuv64jud959seke9qrsqp43GvtYXnx4xvQaRYIFHb2lnQNrkrs7UJt9dYkJu2_iZdO1RXfr179Gg_BukLs',
            'process_step2_title' => '02. Hand-Applied Glaze',
            'process_step2_desc' => 'No two drips are the same. Our custom-mixed glazes respond to the heat in unpredictable, beautiful ways.',
            'story_gallery_title' => 'Fragments of Creation',
            'story_gallery_subtitle' => 'A glimpse inside the walls of our atelier, where time slows down and art takes shape.',
            'story_gallery_card_title' => 'The Earth',
            'story_gallery_card_desc' => 'Our ceramics are fired in small batches, ensuring each glaze reaction is unique to that specific kiln cycle.',
            'story_gallery_img1' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCT11Jd5Xfo2R7Soxe0uVfCQJaUfcnna3u9fbPSdePqMHmV4QSJfd_79OptZE4anPbjyhPdufkM0jFyRWe6ZR-4YcDOYwXteXU_J_SVNIU_XikAHnYPjLSkj_GPNodlMT6G7zi1RGOEMgeK-Ep8HX4kzdJEDPJNIX_J-SAMVUOp3TpOeeYRnQOT9c0VaIwR8PKpK2DDCXL8sJI_ScXxsHL5NQxxX6ucSPoFms9aGyv7_-Ty8AntNx1hM4lPtD7oaFd0YBW7i3szmjo',
            'story_gallery_img2' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAofzZLb0ATJlPw0jDYv2lWmaIa0CRCC5Mltqg1obG9cyiYRTaC3SZFJUnIyh5tfB04MrK6ULyy-finBBj-0ow3AylZ4y4k-WuSXITVVa6FsGzH_9xHPHdYDPl6k3zdn_u6A-ematvCzket0vvQWVTuIYgXu_36AJ2LDZWitBI-ZU_1iFLmkzz8MQULF_XQG1feTZA3JZsThxfEmyIweME7C_rh4gvhyRSz_X2yctouDh8BzaSjYR_SDvle5D3iidF7YUfGzIES-ls',
            'story_gallery_img3' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDCU4po7EsVbpriFmfxbk8Xg11Dq2wR4N4sWGVAICQ55ZWisWukl7kky2bC3nAtdn3mfKo95pA0-S3i0QHI-mHPhNyYAK9Tc2e6EdBPpBXyzEM0HPhHRBz2UOtY4ePyBuodHLcIVzSvPd72M33eImLaPODIgb1r5fD3Z4eE1XvQndep5nQUKxbvIJGApGAq75q6eJafha5RozjWneiBxdCagrfMvBPj98f0Q5OMwDjeAk6TriOp5qusM45DBf6euC9bcVWOC2imhFA',
            'story_gallery_img4' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBlKhsDEVcpx-pzMMBoT6zBxSVoFT9Gn2Pbq3tgY6aQNDAZ-MUcF_gI6H-Af4D0wixBkNjtfe0fLhRFmfiaoPYGQpE6WYpFqDyDfyMFn6ee3hktg3Y-Hws632Xexe17CUM0ukyo9RSM38e-In4k8l2x2vj7L3I5ItJptupHDFmNXv2rnMSfRLHp5SGTX20gPmnvEhw26WpiMWwCezsFApoCq38zJg9InhD471O-jCg95tath-6OE7Lk6KsPtt6LOSV_feUMLRwvZGU',
            'pillars_title' => 'The Four Pillars',
            'pillars_subtitle' => 'How your chosen object comes to life, from the first spark of inspiration to the final touch.',
            'pillar1_icon' => 'eco',
            'pillar1_title' => 'Pure Sourcing',
            'pillar1_desc' => 'We select only organic, raw materials that respect the ecosystem from which they are taken.',
            'pillar2_icon' => 'draw',
            'pillar2_title' => 'Intention Draft',
            'pillar2_desc' => 'Months are spent in the sketching phase, ensuring the ergonomics match the aesthetic vision.',
            'pillar3_icon' => 'precision_manufacturing',
            'pillar3_title' => 'Slow Craft',
            'pillar3_desc' => 'No machines. No assembly lines. Just steady hands and focused patience over several days.',
            'pillar4_icon' => 'auto_awesome',
            'pillar4_title' => 'Final Soul',
            'pillar4_desc' => 'Each item is hand-finished with a personal stamp, signifying it is ready for its new home.',
            'founder_img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuD2F3GVvk7QWFWNVkQE7c1fjBH1ZXrABDDiAyBrDeWTgnksul58eC9yPKAaMc5EhaKDOYgojVY5haJ2RF5f3XQ1-zX6MDwgbCLWPttLQIsT74REf593tDoZgUfTa_-Rydi0UhDn4qBWlIh1B1RcpJldmLivHNw__-kcuNGsea9KW-VyPSPuFxJprGRKLUJHGYCBuWAgKfbKu_qp4Rs5neFOITx5puS5-g-xWC5Ebq0tDyr5pByYDFK3y7Kk5DO-tg3mBXwkEhnQuLk',
            'founder_note_title' => 'A Note from Elena',
            'founder_quote' => "Thank you for being part of this story. When you bring one of our pieces into your home, you aren't just buying a product; you are supporting a slow movement, a local artisan, and a belief that the things we touch every day should be beautiful and kind to the earth. I hope these objects bring as much peace to your space as they brought joy to ours during their creation.",
            'founder_signature' => 'With gratitude, Elena Rivers',
            'founder_job' => 'Founder & Creative Lead',
        ];
    }

    public static function parseDB()
    {
        $opt = Options::get('artisan_options', false);
        $opt = json_decode((string) $opt, true);
        $defaults = self::getDefaults();
        self::$opt = is_array($opt) ? array_merge($defaults, $opt) : $defaults;
        return self::$opt;
    }

    /**
     * Helper to get theme options
     */
    public static function opt($var)
    {
        $opt = self::$opt ?? self::parseDB();
        return $opt[$var] ?? (self::getDefaults()[$var] ?? '');
    }

    /**
     * Injects custom CSS into the head
     */
    public static function loadCustomCSS()
    {
        $css = self::opt('custom_css');
        if ($css) {
            echo "<style>{$css}</style>";
        }
    }
}

new ArtisanAtelier();

