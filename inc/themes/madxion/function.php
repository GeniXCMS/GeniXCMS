<?php
/**
 * Madxion Theme Functions
 * Handles theme loading and options management
 */

class MadxionTheme
{
    public static $opt;
    public static $optionKey = 'madxion_theme_options_v1';

    public function __construct()
    {
        self::$opt = self::parseDB();

        $isAdmin = defined('GX_ADMIN') || strpos($_SERVER['SCRIPT_FILENAME'] ?? '', 'gxadmin') !== false;
        if (!$isAdmin) {
            if (!empty(self::opt('mdo_adsense'))) {
                Hooks::attach('footer_load_lib', array(__CLASS__, 'loadAdsenseJs'));
            }
            if (!empty(self::opt('mdo_analytics'))) {
                Hooks::attach('footer_load_lib', array(__CLASS__, 'loadAnalytics'));
            }
            Hooks::attach('header_load_lib', array(__CLASS__, 'loadThemeCSS'));
        }

        AdminMenu::addChild('themes', [
            'label'  => _('Madxion Theme Options'),
            'url'    => 'index.php?page=themes&view=options',
            'icon'   => 'bi bi-palette',
            'access' => 0,
        ]);
    }

    public static function getDefaults()
    {
        return [
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
            'primary_color' => '#ffb4a8',
            'primary_container' => '#d20000',
            'secondary_color' => '#ffb956',
            'secondary_container' => '#ca8500',
            'background_color' => '#131313',
            'surface_color' => '#131313',
            'surface_container_low' => '#1c1b1b',
            'surface_container' => '#201f1f',
            'surface_container_high' => '#2a2a2a',
            'text_color' => '#e5e2e1',
            'text_variant' => '#e8bcb5',
            'typo_headline_font' => '"Space Grotesk", sans-serif',
            'typo_body_font' => '"Manrope", sans-serif',
            'typo_label_font' => '"Manrope", sans-serif',
            'typo_h1_size' => '48px',
            'typo_h2_size' => '36px',
            'typo_h3_size' => '28px',
            'show_hero_pattern' => 'on',
            'enable_blur_effect' => 'on',
            'enable_animations' => 'on',
            'navbar_transparent' => 'on',
            'header_cta_label' => 'Get in Touch',
            'header_cta_url' => '#contact',
            'social_fb' => '',
            'social_tw' => '',
            'social_gh' => '',
            'social_li' => '',
            'social_ig' => '',
            'mdo_analytics' => '',
            'mdo_adsense' => '',
            'custom_css' => ''
        ];
    }

    public static function parseDB()
    {
        $optRaw = Options::get(self::$optionKey);
        $opt = json_decode((string) $optRaw, true);
        $defaults = self::getDefaults();
        $o = $defaults;
        if (is_array($opt)) {
            foreach ($opt as $k => $v) {
                $deformatted = is_string($v) ? Typo::jsonDeFormat($v) : $v;
                if ($deformatted === '' && isset($defaults[$k]) && $defaults[$k] !== '') {
                    continue;
                }
                $o[$k] = $deformatted;
            }
        }
        return $o;
    }

    public static function opt($key, $default = '')
    {
        if (isset(self::$opt[$key])) {
            $value = self::$opt[$key];
            if (in_array($key, ['mdo_adsense', 'mdo_analytics', 'custom_css'])) {
                return $value;
            }
            return is_string($value) ? htmlspecialchars_decode($value) : $value;
        }
        return $default;
    }

    public static function get($key, $default = '')
    {
        return self::opt($key, $default);
    }

    public static function loadThemeCSS()
    {
        $primary = self::opt('primary_color', '#ffb4a8');
        $primaryContainer = self::opt('primary_container', '#d20000');
        $secondary = self::opt('secondary_color', '#ffb956');
        $secondaryContainer = self::opt('secondary_container', '#ca8500');
        $bg = self::opt('background_color', '#131313');
        $surface = self::opt('surface_color', '#131313');
        $surfaceLow = self::opt('surface_container_low', '#1c1b1b');
        $surfaceContainer = self::opt('surface_container', '#201f1f');
        $surfaceHigh = self::opt('surface_container_high', '#2a2a2a');
        $text = self::opt('text_color', '#e5e2e1');
        $textVar = self::opt('text_variant', '#e8bcb5');
        $headlineFont = self::opt('typo_headline_font', '"Space Grotesk", sans-serif');
        $bodyFont = self::opt('typo_body_font', '"Manrope", sans-serif');
        $customCss = self::opt('custom_css', '');

        // Hover opacity values
        $expertiseCard1BorderHoverOpacity = self::opt('expertise_card_1_border_hover_opacity', '30');
        $expertiseCard2BorderHoverOpacity = self::opt('expertise_card_2_border_hover_opacity', '0');
        $expertiseCard3BorderHoverOpacity = self::opt('expertise_card_3_border_hover_opacity', '0');
        $expertiseCard4BorderHoverOpacity = self::opt('expertise_card_4_border_hover_opacity', '30');
        $validationCard1BorderHoverOpacity = self::opt('validation_card_1_border_hover_opacity', '0');
        $validationCard2BorderHoverOpacity = self::opt('validation_card_2_border_hover_opacity', '0');
        $validationCard3BorderHoverOpacity = self::opt('validation_card_3_border_hover_opacity', '0');
        $postsCardBorderHoverOpacity = self::opt('posts_card_border_hover_opacity', '100');
        $postsCardBorderOpacity = self::opt('posts_card_border_opacity', '20');

        ob_start();
        echo "<style>\n";
        echo ":root {\n";
        echo "  --primary-color: {$primary};\n";
        echo "  --primary-container: {$primaryContainer};\n";
        echo "  --secondary-color: {$secondary};\n";
        echo "  --secondary-container: {$secondaryContainer};\n";
        echo "  --background-color: {$bg};\n";
        echo "  --surface-color: {$surface};\n";
        echo "  --surface-container-low: {$surfaceLow};\n";
        echo "  --surface-container: {$surfaceContainer};\n";
        echo "  --surface-container-high: {$surfaceHigh};\n";
        echo "  --text-color: {$text};\n";
        echo "  --text-variant: {$textVar};\n";
        echo "  --headline-font: {$headlineFont};\n";
        echo "  --body-font: {$bodyFont};\n";
        // Hover opacity CSS variables
        echo "  --expertise-card-1-border-hover-opacity: {$expertiseCard1BorderHoverOpacity}%;\n";
        echo "  --expertise-card-2-border-hover-opacity: {$expertiseCard2BorderHoverOpacity}%;\n";
        echo "  --expertise-card-3-border-hover-opacity: {$expertiseCard3BorderHoverOpacity}%;\n";
        echo "  --expertise-card-4-border-hover-opacity: {$expertiseCard4BorderHoverOpacity}%;\n";
        echo "  --validation-card-1-border-hover-opacity: {$validationCard1BorderHoverOpacity}%;\n";
        echo "  --validation-card-2-border-hover-opacity: {$validationCard2BorderHoverOpacity}%;\n";
        echo "  --validation-card-3-border-hover-opacity: {$validationCard3BorderHoverOpacity}%;\n";
        echo "  --posts-card-border-hover-opacity: {$postsCardBorderHoverOpacity}%;\n";
        echo "  --posts-card-border-opacity: {$postsCardBorderOpacity}%;\n";
        echo "}\n";
        echo "body { background-color: {$bg} !important; color: {$text} !important; font-family: {$bodyFont} !important; }\n";
        echo "h1, h2, h3, .font-headline { font-family: {$headlineFont} !important; }\n";
        echo " .bg-primary { background-color: var(--primary-color) !important; }\n";
        echo " .bg-primary-container { background-color: var(--primary-container) !important; }\n";
        echo " .bg-secondary { background-color: var(--secondary-color) !important; }\n";
        echo " .bg-secondary-container { background-color: var(--secondary-container) !important; }\n";
        echo " .bg-background { background-color: var(--background-color) !important; }\n";
        echo " .bg-surface { background-color: var(--surface-color) !important; }\n";
        echo " .bg-surface-container { background-color: var(--surface-container) !important; }\n";
        echo " .bg-surface-container-low { background-color: var(--surface-container-low) !important; }\n";
        echo " .bg-surface-container-high { background-color: var(--surface-container-high) !important; }\n";
        echo " .text-primary { color: var(--primary-color) !important; }\n";
        echo " .text-primary-container { color: var(--primary-container) !important; }\n";
        echo " .text-secondary { color: var(--secondary-color) !important; }\n";
        echo " .text-on-surface { color: var(--text-color) !important; }\n";
        echo " .text-on-surface-variant { color: var(--text-variant) !important; }\n";
        echo " .border-primary { border-color: var(--primary-color) !important; }\n";
        echo " .border-primary-container { border-color: var(--primary-container) !important; }\n";
        echo " .border-secondary { border-color: var(--secondary-color) !important; }\n";
        echo " .border-secondary-container { border-color: var(--secondary-container) !important; }\n";
        echo " .border-white { border-color: #ffffff !important; }\n";
        echo " .border-none { border-color: transparent !important; }\n";

        // Hover effect styles
        echo " .expertise-card-1:hover { border-color: var(--primary-container) !important; border-opacity: var(--expertise-card-1-border-hover-opacity) !important; }\n";
        echo " .expertise-card-2:hover { background-color: var(--surface-container-high) !important; }\n";
        echo " .expertise-card-3:hover { background-color: var(--surface-container-high) !important; }\n";
        echo " .expertise-card-4:hover { border-color: var(--secondary-container) !important; border-opacity: var(--expertise-card-4-border-hover-opacity) !important; }\n";
        echo " .validation-card-1:hover { border-opacity: var(--validation-card-1-border-hover-opacity) !important; }\n";
        echo " .validation-card-2:hover { border-opacity: var(--validation-card-2-border-hover-opacity) !important; }\n";
        echo " .validation-card-3:hover { border-opacity: var(--validation-card-3-border-hover-opacity) !important; }\n";
        echo " .posts-card { border: 1px solid rgba(210, 0, 0, calc(var(--posts-card-border-opacity) / 100)) !important; }\n";
        echo " .posts-card:hover { border: 1px solid rgba(210, 0, 0, calc(var(--posts-card-border-hover-opacity) / 100)) !important; background-color: var(--surface-container-high) !important; }\n";

        echo "</style>\n";

        if ($customCss) {
            echo "<style>{$customCss}</style>\n";
        }

        return ob_get_clean();
    }

    public static function loadAnalytics()
    {
        return self::opt('mdo_analytics');
    }

    public static function loadAdsenseJs()
    {
        return self::opt('mdo_adsense');
    }
}

new MadxionTheme();

