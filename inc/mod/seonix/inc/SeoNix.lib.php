<?php
class SeoNix
{
    public function __construct()
    {
        Hooks::attach('full_site_title_filter', array('SeoNix', 'titleFilter'));
        Hooks::attach('site_key_filter', array('SeoNix', 'keyFilter'));
        Hooks::attach('site_desc_filter', array('SeoNix', 'descFilter'));
        Hooks::attach('header_load_meta', array('SeoNix', 'headerMeta'));
        Hooks::attach('header_load_admin_meta', array('SeoNix', 'headerMetaAdmin')); // if needed later

        // IndexNow Hooks
        Hooks::attach('post_submit_add_action', array('SeoNix', 'indexNowAdd'));
        Hooks::attach('post_submit_edit_action', array('SeoNix', 'indexNowEdit'));
        Hooks::attach('post_delete_action', array('SeoNix', 'indexNowDel'));

        // Admin Menu Registration
        AdminMenu::add([
            'id'       => 'seonix',
            'label'    => _('SeoNix'),
            'icon'     => 'bi bi-graph-up-arrow',
            'url'      => 'index.php?page=mods&mod=seonix',
            'access'   => 1,
            'position' => 'settings',
            'order'    => 90,
        ]);
    }

    public static function getOpt()
    {
        $raw = Options::get('seonix_options');
        return $raw ? json_decode($raw, true) : [];
    }

    /**
     * Filters the full title string using SeoNix title_format option.
     */
    public static function titleFilter($title)
    {
        $title = is_array($title) ? $title[0] : $title;
        $opt = self::getOpt();
        $format = $opt['title_format'] ?? '%title% | %sitename%';

        // $title originally has "Page Title - Site Name"
        // Let's strip the site name out to get the raw page title
        $delimiter = " - ";
        if (strpos($title, $delimiter) !== false) {
            $parts = explode($delimiter, $title);
            // Drop the last part because it's the sitename
            array_pop($parts);
            $clean_title = implode($delimiter, $parts);
        } else {
            // If there's no delimiter (like on homepage), the title IS just the site name.
            // But we should use the sitename in place of %title% if empty, or just leave it.
            $clean_title = Site::$name;
            if ($title == Site::$name) {
                // If it's homepage, usually title is just the site name.
                // Replace %title% with %sitename%.
                $clean_title = Site::$name;
            }
        }

        $new_title = str_replace('%title%', $clean_title, $format);
        $new_title = str_replace('%sitename%', Site::$name, $new_title);
        $new_title = str_replace('%slogan%', Site::$desc, $new_title);
        
        return $new_title;
    }

    /**
     * Filters the meta keywords.
     */
    public static function keyFilter($keys)
    {
        $keys = is_array($keys) ? $keys[0] : $keys;
        $opt = self::getOpt();
        if (empty($keys) && !empty($opt['meta_keywords'])) {
            return $opt['meta_keywords'];
        }
        return $keys;
    }

    /**
     * Filters the meta description.
     */
    public static function descFilter($desc)
    {
        $desc = is_array($desc) ? $desc[0] : $desc;
        $opt = self::getOpt();
        // If the description matches the default Site::$desc exactly, it means
        // no specific content description was provided. Override with global SeoNix desc if available.
        if (!empty($opt['meta_description'])) {
            $default_desc = substr(Site::$desc, 0, 150);
            if (trim($desc) == trim($default_desc)) {
                return $opt['meta_description'];
            }
        }
        return $desc;
    }

    /**
     * Injects tags into headers (GA, Twitter tags).
     */
    public static function headerMeta($data)
    {
        $opt = self::getOpt();
        $out = "";

        // Google Analytics 4
        if (!empty($opt['ga_id'])) {
            $ga = $opt['ga_id'];
            $out .= "\n    <!-- Global site tag (gtag.js) - Google Analytics -->\n";
            $out .= "    <script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga}\"></script>\n";
            $out .= "    <script>\n";
            $out .= "      window.dataLayer = window.dataLayer || [];\n";
            $out .= "      function gtag(){dataLayer.push(arguments);}\n";
            $out .= "      gtag('js', new Date());\n";
            $out .= "      gtag('config', '{$ga}');\n";
            $out .= "    </script>\n";
        }

        // Facebook Pixel
        if (!empty($opt['fb_pixel_id'])) {
            $fbp = $opt['fb_pixel_id'];
            $out .= "\n    <!-- Meta Pixel Code -->\n";
            $out .= "    <script>\n";
            $out .= "    !function(f,b,e,v,n,t,s)\n";
            $out .= "    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?\n";
            $out .= "    n.callMethod.apply(n,arguments):n.queue.push(arguments)};\n";
            $out .= "    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';\n";
            $out .= "    n.queue=[];t=b.createElement(e);t.async=!0;\n";
            $out .= "    t.src=v;s=b.getElementsByTagName(e)[0];\n";
            $out .= "    s.parentNode.insertBefore(t,s)}(window, document,'script',\n";
            $out .= "    'https://connect.facebook.net/en_US/fbevents.js');\n";
            $out .= "    fbq('init', '{$fbp}');\n";
            $out .= "    fbq('track', 'PageView');\n";
            $out .= "    </script>\n";
            $out .= "    <noscript><img height=\"1\" width=\"1\" style=\"display:none\"\n";
            $out .= "    src=\"https://www.facebook.com/tr?id={$fbp}&ev=PageView&noscript=1\"\n";
            $out .= "    /></noscript>\n";
            $out .= "    <!-- End Meta Pixel Code -->\n";
        }

        // Twitter Site Meta
        if (!empty($opt['twitter_site']) && ($opt['twitter_enable'] ?? 'yes') == 'yes') {
            $out .= "    <meta name=\"twitter:site\" content=\"{$opt['twitter_site']}\">\n\n";
        }

        // If Indexing is disabled for categories/tags, SeoNix handles it by injecting a meta robots noindex manually IF NEEDED.
        // Wait, Site::indexing() handles this, but since we want SeoNix to override, 
        // we can conditionally output <meta name="robots" content="noindex"> to override the original one (search engines respect the strictest rule: noindex > index).
        if (isset($data['p_type'])) {
            if ($data['p_type'] == 'categories' && ($opt['index_categories'] ?? 'yes') == 'no') {
                $out .= "    <meta name=\"robots\" content=\"noindex, follow\" data-seonix=\"override\">\n";
            }
            if ($data['p_type'] == 'tags' && ($opt['index_tags'] ?? 'yes') == 'no') {
                $out .= "    <meta name=\"robots\" content=\"noindex, follow\" data-seonix=\"override\">\n";
            }
        }

        return $out;
    }

    public static function headerMetaAdmin($data) {}

    /**
     * IndexNow Integration
     */
    public static function indexNowAdd($post)
    {
        $id = Posts::$last_id;
        if ($id) self::indexNowPing($id);
    }
    
    public static function indexNowEdit($post)
    {
        if (isset($_GET['id'])) {
            self::indexNowPing($_GET['id']);
        }
    }
    
    public static function indexNowDel($arg)
    {
        $id = is_array($arg) && isset($arg['id']) ? $arg['id'] : (is_scalar($arg) ? $arg : null);
        if ($id) self::indexNowPing($id);
    }

    public static function indexNowPing($id)
    {
        $opt = self::getOpt();
        if (($opt['indexnow_enable'] ?? 'no') == 'no' || empty($opt['indexnow_key'])) {
            return;
        }

        $url = Url::post($id);
        
        // If the URL couldn't properly construct (e.g., during delete), abort
        if (empty($url) || strpos($url, '.html') === false && strpos($url, '?post=') === false) {
            return;
        }

        $key = $opt['indexnow_key'];
        
        // Bing IndexNow API endpoint
        $api_url = "https://api.indexnow.org/indexnow?url=" . urlencode($url) . "&key=" . urlencode($key);
        
        // Fire and forget using cURL
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
            curl_exec($ch);
            curl_close($ch);
        }
    }
}
