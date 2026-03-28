<?php

class Gneex
{
    public static $opt;

    public function __construct()
    {
        if (self::checkDB()) {
            self::$opt = self::parseDB();
            if (isset(self::$opt['adsense']) && self::$opt['adsense'] != '') {
                Hooks::attach('footer_load_lib', array('Gneex', 'loadAdsenseJs'));
            }
            if (isset(self::$opt['analytics']) && self::$opt['analytics'] != '') {
                Hooks::attach('footer_load_lib', array('Gneex', 'loadAnalytics'));
            }

            Hooks::attach('header_load_meta', array('Gneex', 'loadCSS'));
            Hooks::attach('admin_footer_action', array('Gneex', 'loadAdminAsset'));

            Hooks::attach('post_param_form_bottom', array('Gneex', 'postParam'));
            Hooks::attach('page_param_form_bottom', array('Gneex', 'postParam'));

            // Theme Customizer on Frontend
            Hooks::attach('footer_load_lib', array('Gneex', 'themeCustomizerUI'));
        }
    }

    /**
     * Generate Google Fonts <link> tags for panel-selected fonts.
     * Called from Latte template as {Gneex::panelFontsLink()|noescape}
     */
    public static function panelFontsLink()
    {
        $gfMap = [
            "'Inter', sans-serif"         => 'Inter:wght@400;600;700',
            "'Roboto', sans-serif"        => 'Roboto:wght@400;500;700',
            "'Merriweather', serif"       => 'Merriweather:wght@400;700',
            "'Playfair Display', serif"   => 'Playfair+Display:wght@400;700',
            "'JetBrains Mono', monospace" => 'JetBrains+Mono:wght@400;700',
        ];
        $fontsToLoad = [];
        for ($pi = 1; $pi <= 5; $pi++) {
            $ff = self::$opt["panel_{$pi}_font_family"] ?? 'inherit';
            if ($ff !== 'inherit' && isset($gfMap[$ff])) {
                $fontsToLoad[] = $gfMap[$ff];
            }
        }
        if (!$fontsToLoad) return '';
        $fontsToLoad = array_unique($fontsToLoad);
        $q = implode('&family=', $fontsToLoad);
        return '<link rel="preconnect" href="https://fonts.googleapis.com">'
             . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=' . $q . '&display=swap">';
    }

    public static function checkDB()
    {
        if (Options::validate('gneex_options')) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDefaults()
    {
        return [
            'intro_title' => 'Welcome to ' . Site::$name,
            'intro_text' => 'We create amazing digital experiences that help brands grow and thrive in the modern world.',
            'intro_image' => '',
            'featured_posts' => '',
            'adsense' => '',
            'analytics' => '',
            'front_layout' => 'blog',
            'panel_1' => '',
            'panel_1_color' => '',
            'panel_1_font_color' => '',
            'panel_1_bg' => '',
            'panel_1_text_color' => '',
            'panel_1_font_family' => 'inherit',
            'panel_1_font_size' => '1',
            'panel_2' => '',
            'panel_2_color' => '',
            'panel_2_font_color' => '',
            'panel_2_bg' => '',
            'panel_2_text_color' => '',
            'panel_2_font_family' => 'inherit',
            'panel_2_font_size' => '1',
            'panel_3' => '',
            'panel_3_color' => '',
            'panel_3_font_color' => '',
            'panel_3_bg' => '',
            'panel_3_text_color' => '',
            'panel_3_font_family' => 'inherit',
            'panel_3_font_size' => '1',
            'panel_4' => '',
            'panel_4_color' => '',
            'panel_4_font_color' => '',
            'panel_4_bg' => '',
            'panel_4_text_color' => '',
            'panel_4_font_family' => 'inherit',
            'panel_4_font_size' => '1',
            'panel_5' => '',
            'panel_5_color' => '',
            'panel_5_font_color' => '',
            'panel_5_bg' => '',
            'panel_5_text_color' => '',
            'panel_5_font_family' => 'inherit',
            'panel_5_font_size' => '1',
            'background_header' => Url::theme() . 'images/header-bg.jpg',
            'background_color_header' => '#0f172a',
            'background_featured' => '',
            'background_color_featured' => '#050505',
            'background_color_footer' => '#0f172a',
            'font_color_footer' => '#ffffff',
            'font_color_header' => '#ffffff',
            'container_width' => '1280',
            'category_layout' => 'blog',
            'body_background_color' => '#f8fafc',
            'link_color' => '#3b82f6',
            'link_color_hover' => '#2563eb',
            'background_footer' => '',
            'link_color_footer' => '#3b82f6',
            'sidebar_background_color_header' => '',
            'sidebar_font_color_header' => '',
            'sidebar_border_width' => '0',
            'sidebar_border_color' => '',
            'sidebar_background_color_body' => '',
            'sidebar_font_color_body' => '',
            'content_border_width' => '0',
            'content_border_color' => '',
            'content_background_color_body' => '',
            'fullwidth_page' => '',
            'content_font_color_body' => '#334155',
            'content_title_size' => '32',
            'content_title_cat_size' => '24',
            'content_title_color' => '#0f172a',
            'content_title_color_hover' => '#3b82f6',
            'list_title_color' => '#0f172a',
            'list_title_size' => '28',
            'social_fb' => '#',
            'social_tw' => '#',
            'social_ig' => '#',
            'social_yt' => '#',
            'show_breadcrumb' => 'yes',
            'logo_width' => '',
            'logo_height' => '50px',
            'custom_css' => '',
            // Typography defaults
            'typo_body_font'        => '"Inter", sans-serif',
            'typo_body_size'        => '16',
            'typo_body_weight'      => '400',
            'typo_body_color'       => '#334155',
            'typo_h1_font'          => 'inherit',
            'typo_h1_size'          => '36',
            'typo_h1_weight'        => '800',
            'typo_h1_color'         => '#0f172a',
            'typo_h2_font'          => 'inherit',
            'typo_h2_size'          => '28',
            'typo_h2_weight'        => '700',
            'typo_h2_color'         => '#0f172a',
            'typo_h3_font'          => 'inherit',
            'typo_h3_size'          => '22',
            'typo_h3_weight'        => '700',
            'typo_h3_color'         => '#1e293b',
            'typo_h4_font'          => 'inherit',
            'typo_h4_size'          => '18',
            'typo_h4_weight'        => '600',
            'typo_h4_color'         => '#1e293b',
            'typo_nav_font'         => 'inherit',
            'typo_nav_size'         => '14',
            'typo_nav_weight'       => '600',
            'typo_nav_color'        => '#ffffff',
            'typo_post_title_font'  => 'inherit',
            'typo_post_title_size'  => '20',
            'typo_post_title_weight'=> '700',
            'typo_post_title_color' => '#0f172a',
            'typo_meta_font'        => 'inherit',
            'typo_meta_size'        => '13',
            'typo_meta_weight'      => '400',
            'typo_meta_color'       => '#64748b',
            // Blog Post Typography
            'typo_single_title_font'   => 'inherit',
            'typo_single_title_size'   => '36',
            'typo_single_title_weight' => '800',
            'typo_single_title_color'  => '#0f172a',
            'typo_content_font'        => 'inherit',
            'typo_content_size'        => '17',
            'typo_content_weight'      => '400',
            'typo_content_color'       => '#334155',
            'typo_post_meta_font'      => 'inherit',
            'typo_post_meta_size'      => '14',
            'typo_post_meta_weight'    => '400',
            'typo_post_meta_color'     => '#64748b',
            // Blockquote
            'typo_blockquote_font'     => 'inherit',
            'typo_blockquote_size'     => '18',
            'typo_blockquote_weight'   => '400',
            'typo_blockquote_color'    => '#475569',
            // Breadcrumb
            'typo_breadcrumb_font'     => 'inherit',
            'typo_breadcrumb_size'     => '13',
            'typo_breadcrumb_weight'   => '600',
            'typo_breadcrumb_color'    => '#ffffff',
            // Comments
            'typo_comment_title_font'  => 'inherit',
            'typo_comment_title_size'  => '22',
            'typo_comment_title_weight'=> '700',
            'typo_comment_title_color' => '#0f172a',
            'typo_comment_body_font'   => 'inherit',
            'typo_comment_body_size'   => '15',
            'typo_comment_body_weight' => '400',
            'typo_comment_body_color'  => '#334155'
        ];
    }

    public static function parseDB()
    {
        $opt = Options::get('gneex_options');
        $opt = json_decode($opt, true);
        $defaults = self::getDefaults();
        $o = $defaults;
        if (is_array($opt)) {
            foreach ($opt as $k => $v) {
                $deformatted = is_string($v) ? Typo::jsonDeFormat($v) : $v;
                // If stored value is empty string but default is non-empty, keep default
                if ($deformatted === '' && isset($defaults[$k]) && $defaults[$k] !== '') {
                    // keep $o[$k] = default
                } else {
                    $o[$k] = $deformatted;
                }
            }
        }
        return $o;
    }

    public static function getImage($post, $id = null)
    {
        if ($id) {
            $image = Posts::getPostImage($id);
            if ($image != '') {
                return $image;
            }
        }
        if (!$post) return '';
        preg_match_all('/<img .*?src=[\'"]([^\'"]+)[\'"].*?>/Ui', $post, $im);
        return isset($im[1][0]) ? $im[1][0] : '';
    }

    public static function getPost($id)
    {
        if (empty($id)) return '';
        $id = Typo::int($id);
        $sql = "SELECT `content` FROM `posts` WHERE `id` = '{$id}' LIMIT 1";
        $q = Db::result($sql);
        if (isset($q[0]->content)) {
            return Posts::content($q[0]->content);
        }
        return '';
    }

    public static function optionPost($type, $post='')
    {
        $sql = "SELECT * FROM `posts` WHERE `type` = '{$type}' ORDER BY `title` ASC";
        $q = Db::result($sql);
        $opt = '<option></option>';
        foreach ($q as $k => $v) {
            $sel = ($post != '' && $post == $v->id) ? 'selected': '';
            $opt .= "<option value='{$v->id}' {$sel}>{$v->title}</option>";
        }
        return $opt;
    }

    public static function featuredExist()
    {
        $feat = isset(self::$opt['featured_posts']) ? self::$opt['featured_posts'] : '';
        return ($feat != '' && $feat !== null);
    }

    public static function isAdsense($adc)
    {
        if ($adc != '') {
            return str_replace('<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>', '', urldecode($adc));
        }
    }

    public static function loadAdsenseJs()
    {
        echo '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
    }

    public static function opt($var)
    {
        $opt = self::$opt;
        if (isset($opt[$var])) {
            if ($var == 'adsense') {
                return self::isAdsense(Typo::Xclean($opt[$var]));
            } else {
                return is_string($opt[$var]) ? Typo::Xclean($opt[$var]) : $opt[$var];
            }
        } else {
            $defaults = self::getDefaults();
            return isset($defaults[$var]) ? $defaults[$var] : '';
        }
    }

    public static function introIg($url)
    {
        if (empty($url)) return '';
        if (strpos($url, 'youtube') || strpos($url, 'youtu.be')) {
            if (strpos($url, 'youtube')) {
                parse_str( parse_url( $url, PHP_URL_QUERY ), $dom );
            } else {
                $dom = explode('/', $url);
            }
            $hash = (strpos($url, 'youtu.be')) ? $dom[3] : (isset($dom['v']) ? $dom['v'] : '');
            $html = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'.$hash.'?rel=0&amp;controls=0&amp;showinfo=0" class="center-block" frameborder="0" allowfullscreen></iframe>';
        } elseif(strpos($url, 'vimeo')) {
            $dom = explode('/', $url);
            $html = '<iframe src="https://player.vimeo.com/video/'.$dom[3].'?byline=0&portrait=0" width="640" height="267" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        } elseif(strpos($url, 'dailymotion') || strpos($url, 'dai.ly')) {
            $dom = explode('/', $url);
            $hash = (strpos($url, 'dai.ly')) ? $dom[3]: (isset($dom[4]) ? $dom[4] : '');
            $html = '<iframe frameborder="0" width="480" height="270" src="//www.dailymotion.com/embed/video/'.$hash.'" allowfullscreen></iframe>';
        } else {
            $html = '<img src="'.$url.'" class="img-fluid rounded-4 shadow-sm">';
        }
        return $html;
    }

    public static function loadAnalytics()
    {
        echo self::opt('analytics');
    }

    public static function loadCSS()
    {
        // Global safeguard: never inject the theme's premium frontend styles into the administrative portal
        if (strpos($_SERVER['PHP_SELF'], 'gxadmin') !== false) {
            return '';
        }

        return OptionsBuilder::generateFrontendCSS(self::$opt, [
            'themeUrl'  => Url::theme(),
            'minify'    => true,
        ]);
    }

    public static function loadAdminAsset()
    {
        $js = '
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
            /* GneeX admin base reset */
            #gneexForm, .gx-wrap { box-sizing: border-box; }
            #gneexForm *, .gx-wrap * { box-sizing: inherit; }
        </style>';
        System::adminAsset($js);
    }

    public static function postParam($data)
    {
        $bar = isset($data[0]['post'][0]->id) ? Posts::getParam('sidebar', $data[0]['post'][0]->id): 'yes';
        $yes = ($bar == 'yes') ? 'selected': '';
        $no = ($bar == 'no') ? 'selected': '';
        echo '<div class="row"><div class="col-sm-4"><label>Show/Hide Sidebar</label>
              <select name="param[sidebar]" class="form-control">
                <option value="yes" '.$yes.'>Show Sidebar</option>
                <option value="no" '.$no.'>Hide Sidebar</option>
              </select></div></div>';
    }
    public static function themeCustomizerUI()
    {
        $html = '
        <div id="gx-customizer" style="position:fixed;right:-280px;top:20%;width:280px;background:#fff;z-index:9999;box-shadow:-5px 0 20px rgba(0,0,0,0.1);padding:20px;border-radius:10px 0 0 10px;transition:right 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
            <button id="gx-customizer-toggle" style="position:absolute;left:-45px;top:20px;width:45px;height:45px;background:#2d6efb;color:#fff;border:none;border-radius:10px 0 0 10px;cursor:pointer;font-size:20px;display:flex;align-items:center;justify-content:center;box-shadow:-2px 0 10px rgba(0,0,0,0.1);">
                <i class="fa fa-paint-brush"></i>
            </button>
            <h5 style="margin-top:0;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:10px;margin-bottom:20px;font-size:16px;">
               <i class="fa fa-cog fa-spin"></i> Customizer
            </h5>
            
            <p style="font-size:12px;color:#64748b;margin-bottom:15px;line-height:1.5;">Pilih preset warna untuk mengubah tampilan tema secara instan.</p>
            
            <div style="display:flex;flex-wrap:wrap;gap:10px;" id="gx-preset-container">
            </div>
            
            <hr style="margin:20px 0;border-top:1px solid #e2e8f0;">
            <div style="font-size:12px;color:#64748b;margin-bottom:15px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Kustomisasi Warna:</div>
            
            <div style="margin-bottom:15px;">
                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;color:#334155;">Primary / Link Color</label>
                <div style="display:flex;align-items:center;gap:10px;padding:5px;border:1px solid #e2e8f0;border-radius:8px;">
                    <input type="color" id="gx-color-primary" style="width:30px;height:30px;border:none;border-radius:5px;cursor:pointer;padding:0;background:transparent;">
                    <span id="gx-color-primary-val" style="font-size:12px;font-family:monospace;color:#64748b;font-weight:600;">#000000</span>
                </div>
            </div>
            <div style="margin-bottom:15px;">
                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;color:#334155;">Background Color</label>
                <div style="display:flex;align-items:center;gap:10px;padding:5px;border:1px solid #e2e8f0;border-radius:8px;">
                    <input type="color" id="gx-color-bg" style="width:30px;height:30px;border:none;border-radius:5px;cursor:pointer;padding:0;background:transparent;">
                    <span id="gx-color-bg-val" style="font-size:12px;font-family:monospace;color:#64748b;font-weight:600;">#000000</span>
                </div>
            </div>
            
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toggle = document.getElementById("gx-customizer-toggle");
            var panel = document.getElementById("gx-customizer");
            var isOpen = false;
            
            toggle.addEventListener("click", function() {
                isOpen = !isOpen;
                panel.style.right = isOpen ? "0" : "-280px";
                toggle.innerHTML = isOpen ? \'<i class="fa fa-times" style="transform:rotate(90deg);transition:all 0.3s;"></i>\' : \'<i class="fa fa-paint-brush" style="transition:all 0.3s;"></i>\';
            });
            
            var presets = [
                {name:"Ocean Blue", emoji:"🌊", p:"#2563eb", h:"#1d4ed8", bg:"#f0f9ff", t:"#1e3a5f"},
                {name:"Forest Green", emoji:"🌿", p:"#16a34a", h:"#15803d", bg:"#f0fdf4", t:"#14532d"},
                {name:"Sunset Red", emoji:"🌅", p:"#dc2626", h:"#b91c1c", bg:"#fff7ed", t:"#7c2d12"},
                {name:"Royal Violet", emoji:"💜", p:"#7c3aed", h:"#6d28d9", bg:"#faf5ff", t:"#3b0764"},
                {name:"Midnight Dark", emoji:"🌙", p:"#f59e0b", h:"#d97706", bg:"#0f172a", t:"#f8fafc"},
                {name:"Rose Gold", emoji:"🌸", p:"#e11d48", h:"#be123c", bg:"#fff1f2", t:"#881337"}
            ];
            
            var container = document.getElementById("gx-preset-container");
            var root = document.documentElement;
            
            presets.forEach(function(preset) {
                var btn = document.createElement("button");
                btn.style.cssText = "display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;cursor:pointer;font-size:12.5px;font-weight:600;color:#475569;width:100%;transition:all 0.2s;text-align:left;";
                btn.innerHTML = \'<div style="width:16px;height:16px;border-radius:50%;background:\'+preset.p+\';flex-shrink:0;"></div> <span style="flex:1;">\' + preset.emoji + " " + preset.name + \'</span>\';
                
                btn.onmouseover = function() { btn.style.borderColor = preset.p; btn.style.backgroundColor = preset.bg; btn.style.color = preset.t; };
                btn.onmouseout = function() { btn.style.borderColor = "#e2e8f0"; btn.style.backgroundColor = "#fff"; btn.style.color = "#475569"; };
                
                btn.onclick = function() {
                    root.style.setProperty("--primary-color", preset.p);
                    root.style.setProperty("--primary-hover", preset.h);
                    root.style.setProperty("--bg-body", preset.bg);
                    root.style.setProperty("--text-heading", preset.t);
                    
                    document.getElementById("gx-color-primary").value = preset.p;
                    document.getElementById("gx-color-primary-val").textContent = preset.p;
                    
                    document.getElementById("gx-color-bg").value = preset.bg;
                    document.getElementById("gx-color-bg-val").textContent = preset.bg;
                    
                    // Save to localstorage for demo persistence
                    localStorage.setItem("gx_demo_primary", preset.p);
                    localStorage.setItem("gx_demo_primary_hover", preset.h);
                    localStorage.setItem("gx_demo_text_heading", preset.t);
                    localStorage.setItem("gx_demo_bg", preset.bg);
                };
                container.appendChild(btn);
            });
            
            // Initiate color pickers
            var cp = document.getElementById("gx-color-primary");
            var cb = document.getElementById("gx-color-bg");
            var cpVal = document.getElementById("gx-color-primary-val");
            var cbVal = document.getElementById("gx-color-bg-val");
            
            var rs = getComputedStyle(root);
            
            // load from ls if exist
            var ls_p = localStorage.getItem("gx_demo_primary");
            var ls_ph = localStorage.getItem("gx_demo_primary_hover");
            var ls_t = localStorage.getItem("gx_demo_text_heading");
            var ls_bg = localStorage.getItem("gx_demo_bg");
            
            if (ls_p) { 
                root.style.setProperty("--primary-color", ls_p); 
                if (ls_ph) root.style.setProperty("--primary-hover", ls_ph); 
                if (ls_t) root.style.setProperty("--text-heading", ls_t); 
            }
            if (ls_bg) { root.style.setProperty("--bg-body", ls_bg); }
            
            cp.value = rgb2hex(ls_p || rs.getPropertyValue("--primary-color").trim()) || "#2563eb";
            cb.value = rgb2hex(ls_bg || rs.getPropertyValue("--bg-body").trim()) || "#f8fafc";
            
            cpVal.textContent = cp.value;
            cbVal.textContent = cb.value;
            
            cp.addEventListener("input", function() { 
                root.style.setProperty("--primary-color", this.value); 
                cpVal.textContent = this.value;
                localStorage.setItem("gx_demo_primary", this.value);
            });
            cb.addEventListener("input", function() { 
                root.style.setProperty("--bg-body", this.value); 
                cbVal.textContent = this.value;
                localStorage.setItem("gx_demo_bg", this.value);
            });
            
            function rgb2hex(rgb) {
                if (!rgb) return null;
                if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;
                var match = rgb.match(/^rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)$/i);
                if (!match) return rgb;
                function hex(x) { return ("0" + parseInt(x).toString(16)).slice(-2); }
                return "#" + hex(match[1]) + hex(match[2]) + hex(match[3]);
            }
        });
        </script>
        ';
        return $html;
    }
}
new Gneex();
