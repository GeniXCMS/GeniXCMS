<?php
/**
 * Name: GxEditor
 * Desc: GeniXCMS Official Block Editor — Block-based editor inspired by Gutenberg & EditorJS.
 * Version: 1.0.0
 * Build: 2.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-pencil-square
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class Gxeditor
{
    public static function init()
    {
        // Register GxEditor to the new modular Editor system
        Editor::register('gxeditor', 'GxEditor (Official Block Editor)', [self::class, 'setupGxEditor']);

        // Core GxEditor backend logic
        Hooks::attach('post_content_filter', array('Gxeditor', 'parseShortcodes'));
        Hooks::attach('comment_allowed_blocks', array('Gxeditor', 'filterCommentBlocks'));

        AdminMenu::addChild('settings', [
            'label' => _('GxEditor Settings'),
            'url' => 'index.php?page=mods&mod=gxeditor',
            'access' => 0
        ]);
    }



    public static function filterCommentBlocks($blocks)
    {
        $opt = json_decode(Options::v('gxeditor_mini_blocks') ?? Options::v('gxeditor_comment_blocks') ?? '', true);
        return !empty($opt) ? $opt : $blocks;
    }

    public static function parseShortcodes($content)
    {
        if (is_array($content)) {
            $content = $content[0];
        }
        if (empty($content))
            return $content;

        // Parse [image src="..." width="..." align="..." style="..." alt="..." caption="..."]
        $content = Shortcode::parse('image', $content, function ($attrs) {
            $src = $attrs['src'] ?? '';
            $width = $attrs['width'] ?? '';
            $align = $attrs['align'] ?? '';
            $style = !empty($attrs['style']) ? $attrs['style'] : 'rounded';
            $alt = $attrs['alt'] ?? '';
            $caption = $attrs['caption'] ?? '';

            if (!$src)
                return '';

            $cls = "img-fluid $style $width $align";
            $wrapCls = "gx-image-rendered mb-4";
            if ($align === 'mx-auto d-block')
                $wrapCls .= ' text-center';
            else if ($align === 'float-start' || $align === 'float-end')
                $wrapCls .= ' clearfix';

            $html = '<div class="' . $wrapCls . '">';
            $html .= '<img src="' . $src . '" class="' . $cls . '" alt="' . htmlspecialchars($alt) . '">';
            if (!empty($caption)) {
                $html .= '<div class="text-muted small mt-2">' . htmlspecialchars($caption) . '</div>';
            }
            $html .= '</div>';
            return $html;
        });

        // Parse [recent_posts count="5"]
        $content = Shortcode::parse('recent_posts', $content, function ($attrs) {
            $count = (int) ($attrs['count'] ?? 5);
            return Posts::lists([
                'num' => $count,
                'image' => true,
                'image_size' => 80,
                'title' => true,
                'date' => true,
                'type' => 'post',
                'class' => [
                    'row' => 'd-flex align-items-center mb-3 mb-border-bottom pb-2',
                    'img' => 'rounded flex-shrink-0 object-fit-cover',
                    'list' => 'flex-grow-1 ms-3',
                    'h4' => 'fs-6 fw-semibold mb-0',
                    'date' => 'text-body-secondary mt-0'
                ]
            ]);
        });

        // Parse [random_posts count="5"]
        $content = Shortcode::parse('random_posts', $content, function ($attrs) {
            $count = (int) ($attrs['count'] ?? 5);
            $randFn = (defined('DB_DRIVER') && DB_DRIVER === 'mysql') ? 'RAND()' : 'RANDOM()';
            $posts = Query::table('posts')->where('type', 'post')->where('status', '1')
                ->orderByRaw($randFn)->limit($count)->get();

            if (empty($posts))
                return '';
            $posts = Posts::prepare($posts);
            $html = '';
            foreach ($posts as $p) {
                if (!is_object($p))
                    continue;
                $post_image = Posts::getPostImage($p->id);
                $img = ($post_image != "") ? $post_image : Posts::getImage(Typo::Xclean($p->content), 1);
                $imgurl = ($img == "") ? Url::thumb(Site::$url . "assets/images/noimage.png", 'square', 80) : Url::thumb($img, 'square', 80);

                $html .= '<div class="d-flex align-items-center mb-3 mb-border-bottom pb-2">';
                $html .= '<div class="flex-shrink-0"><a href="' . Url::post($p->id) . '"><img class="rounded flex-shrink-0 object-fit-cover" src="' . $imgurl . '" alt="' . $p->title . '" width="80" height="80"></a></div>';
                $html .= '<div class="flex-grow-1 ms-3">';
                $html .= '<h4 class="fs-6 fw-semibold mb-0"><a class="text-dark text-decoration-none" href="' . Url::post($p->id) . '">' . $p->title . '</a></h4>';
                $html .= '<small class="text-body-secondary mt-0">' . Date::local($p->date) . '</small>';
                $html .= '</div></div>';
            }
            return $html;
        });

        // Parse [post id="11"]
        $content = Shortcode::parse('post', $content, function ($attrs) {
            $id = (int) ($attrs['id'] ?? 0);
            $p = Query::table('posts')->where('id', $id)->first();
            if (empty($p))
                return '';

            $p = Posts::prepare([$p])[0];
            $post_image = Posts::getPostImage($p->id);
            $img = ($post_image != "") ? $post_image : Posts::getImage(Typo::Xclean($p->content), 1);
            $imgurl = ($img == "") ? Url::thumb(Site::$url . "assets/images/noimage.png", 'square', 120) : Url::thumb($img, 'square', 120);

            $html = '<div class="card mb-3"><div class="row g-0 align-items-center">';
            $html .= '<div class="col-4 col-md-3"><a href="' . Url::post($p->id) . '"><img src="' . $imgurl . '" class="img-fluid rounded-start object-fit-cover w-100" style="height:120px;" alt="..."></a></div>';
            $html .= '<div class="col-8 col-md-9"><div class="card-body">';
            $html .= '<h5 class="card-title fw-bold fs-6 mb-1"><a class="text-dark text-decoration-none" href="' . Url::post($p->id) . '">' . $p->title . '</a></h5>';
            $html .= '<p class="card-text mb-0"><small class="text-muted">' . Date::local($p->date) . '</small></p>';
            $html .= '</div></div></div></div>';
            return $html;
        });

        // Parse [icon class="..." size="..." color="..."]
        $content = Shortcode::parse('icon', $content, function ($attrs) {
            $class = !empty($attrs['class']) ? $attrs['class'] : 'bi bi-star';
            $size = !empty($attrs['size']) ? $attrs['size'] : '2.5rem';
            $color = !empty($attrs['color']) ? $attrs['color'] : 'inherit';

            return sprintf(
                '<div class="gx-icon-rendered mb-4 text-center"><i class="%s" style="font-size:%s; color:%s; display:inline-block; vertical-align:middle;"></i></div>',
                htmlspecialchars($class),
                htmlspecialchars($size),
                htmlspecialchars($color)
            );
        });

        if (strpos($content, '[toc') !== false) {
            // Unpack [toc] from any outer .gx-toc wrapper BEFORE parsing so its float works!
            $content = preg_replace('/<div[^>]*class="[^"]*gx-toc[^"]*"[^>]*>\s*(\[toc\b[^\]]*\])\s*<\/div>/is', '$1', $content);
            $headings = [];
            // First, scan all headings to build the list and add IDs
            $content = preg_replace_callback('/<(h[1-4])(.*?)>(.*?)<\/\1>/i', function ($m) use (&$headings) {
                $tag = $m[1];
                $attrs = $m[2];
                $title = trim(strip_tags($m[3]));
                if (empty($title))
                    return $m[0];

                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                static $usedIds = [];
                $finalId = $slug;
                $count = 1;
                while (in_array($finalId, $usedIds)) {
                    $finalId = $slug . '-' . $count++;
                }
                $usedIds[] = $finalId;

                $headings[] = [
                    'tag' => $tag,
                    'title' => $title,
                    'id' => $finalId,
                    'level' => (int) substr($tag, 1)
                ];

                if (strpos($attrs, 'id=') !== false)
                    return $m[0];
                return "<$tag id=\"$finalId\"$attrs>{$m[3]}</$tag>";
            }, $content);

            // Now replace the [toc] shortcode with a styled version
            $content = Shortcode::parse('toc', $content, function ($attrs) use ($headings) {
                $title = $attrs['title'] ?? 'Daftar Isi';
                $float = $attrs['float'] ?? 'none';
                $width = $attrs['width'] ?? '450px';
                $doCollapse = (!empty($attrs['collapse']) && $attrs['collapse'] === 'yes');

                if (empty($headings))
                    return '';

                $id = 'gx-toc-' . rand(100, 999);
                $collapseClass = $doCollapse ? 'collapse' : 'show';
                $floatCls = ($float !== 'none') ? $float : '';
                if (strpos($floatCls, 'float-start') !== false && strpos($floatCls, 'me-') === false)
                    $floatCls .= ' me-2';
                if (strpos($floatCls, 'float-end') !== false && strpos($floatCls, 'ms-') === false)
                    $floatCls .= ' ms-2';

                $html = '<div class="gx-toc-widget card mb-4 ' . $floatCls . '" style="width:' . $width . '; max-width:100%; border:1px solid #e5e7eb;">';
                $html .= '<div class="card-header d-flex justify-content-between align-items-center py-2 border-0">';
                $html .= '<span class="fw-bold text-primary small"><i class="bi bi-list-nested me-2"></i>' . $title . '</span>';
                if ($doCollapse) {
                    $html .= '<button class="btn btn-sm p-0 border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#' . $id . '"><i class="bi bi-chevron-expand"></i></button>';
                }
                $html .= '</div>';
                $html .= '<div id="' . $id . '" class="card-body ' . $collapseClass . ' p-0">';
                $html .= '<div class="p-3">'; // Outer padding wrapper for smooth height calc
                $html .= '<ul class="list-unstyled mb-0" style="font-size: 0.9rem; line-height: 1.6;">';
                foreach ($headings as $h) {
                    $indent = ($h['level'] - 1) * 12;
                    $fw = ($h['tag'] == 'h1' || $h['tag'] == 'h2') ? 'fw-bold' : '';
                    $html .= '<li class="gx-toc-list-item" style="padding-left: ' . $indent . 'px; margin-bottom: 4px;">';
                    $html .= '<a href="#' . $h['id'] . '" class="text-decoration-none text-dark hover-primary ' . $fw . '">' . $h['title'] . '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul></div></div></div>';
                return $html;
            });

        }

        // Parse [icon_list icon="..." color="..."] ... [/icon_list]
        $content = Shortcode::parse('icon_list', $content, function ($attrs, $inner) {
            $icon = $attrs['icon'] ?? 'bi bi-check2-circle';
            $color = $attrs['color'] ?? '#6366f1';
            $items = explode("\n", trim(strip_tags($inner, '<li>')));
            $html = '<ul class="list-unstyled gx-icon-list mb-4">';
            foreach ($items as $item) {
                if (empty(trim($item)))
                    continue;
                $html .= '<li class="d-flex align-items-start gap-2 mb-2">';
                $html .= '<i class="' . $icon . '" style="color:' . $color . '; font-size:1.1rem; margin-top:0.2rem;"></i>';
                $html .= '<span>' . trim($item) . '</span>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            return $html;
        });

        // Parse [pricing_table]
        $content = Shortcode::parse('pricing_table', $content, function ($attrs) {
            $currency = $attrs['currency'] ?? '$';
            $p1_m = $attrs['p1_monthly'] ?? '19';
            $p1_y = $attrs['p1_yearly'] ?? '190';
            $p2_m = $attrs['p2_monthly'] ?? '49';
            $p2_y = $attrs['p2_yearly'] ?? '490';
            $p3_m = $attrs['p3_monthly'] ?? '99';
            $p3_y = $attrs['p3_yearly'] ?? '990';

            $id = 'gx-pricing-' . rand(100, 999);

            $html = '
            <div class="gx-pricing-wrapper py-4" id="' . $id . '">
                <div class="gx-pricing-switcher d-flex justify-content-center mb-5 align-items-center gap-3">
                    <span class="fw-bold small text-muted">MONTHLY</span>
                    <div class="form-check form-switch p-0 m-0" style="min-height: auto;">
                        <input class="form-check-input gx-pricing-toggle" type="checkbox" role="switch" style="width: 3.2em; height: 1.6em; cursor: pointer; margin-left: 0;">
                    </div>
                    <span class="fw-bold small text-muted">YEARLY <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 ms-1" style="font-size: 0.65rem;">SAVE 20%</span></span>
                </div>

                <div class="row g-4 gx-pricing-cards">
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                            <div class="mb-3"><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">BASIC</span></div>
                            <div class="h1 fw-bold mb-1">
                                <span class="gx-price-monthly">' . $currency . $p1_m . '</span>
                                <span class="gx-price-yearly d-none">' . $currency . $p1_y . '</span>
                            </div>
                            <div class="text-muted small mb-4">per user / month</div>
                            <ul class="list-unstyled mb-4 text-start small">
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> 1 Active Project</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Community Support</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Basic Analytics</li>
                            </ul>
                            <div class="mt-auto">
                                <a href="#" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold py-2">Select Plan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-4 text-center position-relative overflow-hidden" style="border: 2px solid #6366f1 !important;">
                            <div class="position-absolute top-0 end-0 bg-primary text-white px-3 py-1 small fw-bold" style="border-bottom-left-radius: 12px;">POPULAR</div>
                            <div class="mb-3"><span class="badge bg-primary px-3 py-2 rounded-pill fw-bold">PROFESSIONAL</span></div>
                            <div class="h1 fw-bold mb-1">
                                <span class="gx-price-monthly">' . $currency . $p2_m . '</span>
                                <span class="gx-price-yearly d-none">' . $currency . $p2_y . '</span>
                            </div>
                            <div class="text-muted small mb-4">per user / month</div>
                            <ul class="list-unstyled mb-4 text-start small">
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> 10 Active Projects</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Priority Support</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Advanced Reports</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Custom Branding</li>
                            </ul>
                            <div class="mt-auto">
                                <a href="#" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold py-2 shadow-sm text-white">Get Started</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                            <div class="mb-3"><span class="badge bg-dark bg-opacity-10 text-dark px-3 py-2 rounded-pill fw-bold">ENTERPRISE</span></div>
                            <div class="h1 fw-bold mb-1">
                                <span class="gx-price-monthly">' . $currency . $p3_m . '</span>
                                <span class="gx-price-yearly d-none">' . $currency . $p3_y . '</span>
                            </div>
                            <div class="text-muted small mb-4">per user / month</div>
                            <ul class="list-unstyled mb-4 text-start small">
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Unlimited Projects</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Dedicated Manager</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Custom Integration</li>
                                <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> SLA Guarantee</li>
                            </ul>
                            <div class="mt-auto">
                                <a href="#" class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-bold py-2">Contact Sales</a>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    (function() {
                        var wrap = document.getElementById("' . $id . '");
                        var toggle = wrap.querySelector(".gx-pricing-toggle");
                        toggle.addEventListener("change", function() {
                            var monthly = wrap.querySelectorAll(".gx-price-monthly");
                            var yearly = wrap.querySelectorAll(".gx-price-yearly");
                            if (this.checked) {
                                monthly.forEach(function(el) { el.classList.add("d-none"); });
                                yearly.forEach(function(el) { el.classList.remove("d-none"); });
                            } else {
                                monthly.forEach(function(el) { el.classList.remove("d-none"); });
                                yearly.forEach(function(el) { el.classList.add("d-none"); });
                            }
                        });
                    })();
                </script>
            </div>';
            return $html;
        });

        // Parse [table border="..." striped="..." hover="..." head="..."] ... [/table]
        $content = Shortcode::parse('table', $content, function ($attrs, $inner) {
            $border = ($attrs['border'] ?? 'yes') === 'yes' ? 'table-bordered' : '';
            $striped = ($attrs['striped'] ?? 'no') === 'yes' ? 'table-striped' : '';
            $hover = ($attrs['hover'] ?? 'no') === 'yes' ? 'table-hover' : '';
            $head = $attrs['head'] ?? 'light';

            $html = '<div class="table-responsive mb-4"><table class="table ' . $border . ' ' . $striped . ' ' . $hover . '">';
            // Assume $inner contains <tr>...</tr>
            $html .= $inner;
            $html .= '</table></div>';
            return $html;
        });
        // Parse [raw_html] ... [/raw_html]
        $content = Shortcode::parse('raw_html', $content, function ($attrs, $inner) {
            $inner = trim($inner);
            if (strpos($inner, 'base64:') === 0) {
                // Remove prefix and any potential whitespace/characters injected by filters
                $b64 = substr($inner, 7);
                $b64 = preg_replace('/[^A-Za-z0-9\+\/=]/', '', $b64);
                return base64_decode($b64);
            }
            return $inner;
        });

        return $content;
    }

    public static function registerEditorOption($options)
    {
        $options['gxeditor'] = 'GxEditor (Official Block Editor)';
        return $options;
    }

    public static function setupGxEditor()
    {
        $siteUrl = rtrim(Site::$url, "/");
        $elfinderAjaxUrl = Url::ajax("elfinder");
        $elfinderAjaxUrlJson = json_encode($elfinderAjaxUrl);
        $editorStyle = Options::v("gxeditor_style") ?: "block";

        $blockMeta = [
            'paragraph' => ['label' => 'Text', 'icon' => 'bi bi-justify-left', 'desc' => 'Pure text content', 'cat' => 'Basic'],
            'h1' => ['label' => 'Heading 1', 'icon' => 'bi bi-type-h1', 'desc' => 'Main section title', 'cat' => 'Basic'],
            'h2' => ['label' => 'Heading 2', 'icon' => 'bi bi-type-h2', 'desc' => 'Secondary title', 'cat' => 'Basic'],
            'h3' => ['label' => 'Heading 3', 'icon' => 'bi bi-type-h3', 'desc' => 'Small section title', 'cat' => 'Basic'],
            'image' => ['label' => 'Image', 'icon' => 'bi bi-image', 'desc' => 'Photo or illustration', 'cat' => 'Basic'],
            'button' => ['label' => 'Button', 'icon' => 'bi bi-hand-index-thumb', 'desc' => 'Call to action button', 'cat' => 'Basic'],
            'grid2' => ['label' => '2 Columns', 'icon' => 'bi bi-layout-split', 'desc' => 'Side-by-side content', 'cat' => 'Layout'],
            'grid2x2' => ['label' => 'Grid 2x2', 'icon' => 'bi bi-grid-3x3-gap', 'desc' => 'Four cells layout', 'cat' => 'Layout'],
            'card' => ['label' => 'Card', 'icon' => 'bi bi-card-text', 'desc' => 'Boxed content with header/footer', 'cat' => 'Layout'],
            'quote' => ['label' => 'Quote', 'icon' => 'bi bi-quote', 'desc' => 'Blockquote styling', 'cat' => 'Basic'],
            'ul' => ['label' => 'Bulleted List', 'icon' => 'bi bi-list-ul', 'desc' => 'Unordered list', 'cat' => 'Basic'],
            'ol' => ['label' => 'Numbered List', 'icon' => 'bi bi-list-ol', 'desc' => 'Ordered list', 'cat' => 'Basic'],
            'table' => ['label' => 'Table', 'icon' => 'bi bi-table', 'desc' => 'Data grid', 'cat' => 'Basic'],
            'icon' => ['label' => 'Icon Block', 'icon' => 'bi bi-stars', 'desc' => 'Stylized bootstrap icon', 'cat' => 'Basic'],
            'icon_list' => ['label' => 'Icon List', 'icon' => 'bi bi-check2-square', 'desc' => 'List with custom icon markers', 'cat' => 'Basic'],
            'single_post' => ['label' => 'Post Embed', 'icon' => 'bi bi-file-earmark-richtext', 'desc' => 'Embed post by ID', 'cat' => 'Standard Sections'],
            'toc' => ['label' => 'Table of Contents', 'icon' => 'bi bi-list-nested', 'desc' => 'Auto-generated page links', 'cat' => 'Standard Sections'],
            'divider' => ['label' => 'Divider', 'icon' => 'bi bi-hr', 'desc' => 'Horizontal separator', 'cat' => 'Basic'],
            'code' => ['label' => 'Code Block', 'icon' => 'bi bi-code-slash', 'desc' => 'Syntax highlighted code', 'cat' => 'Basic'],
            'pricing' => ['label' => 'Price Comparison', 'icon' => 'bi bi-tags', 'desc' => 'Pricing table with monthly/yearly switch', 'cat' => 'Standard Sections'],
            'recent_posts' => ['label' => 'Recent Posts', 'icon' => 'bi bi-clock-history', 'desc' => 'List of latest posts', 'cat' => 'Standard Sections'],
            'random_posts' => ['label' => 'Random Posts', 'icon' => 'bi bi-shuffle', 'desc' => 'List of random posts', 'cat' => 'Standard Sections']
        ];

        $coreBlocks = array_keys($blockMeta);
        $allowedTypes = $GLOBALS["editor_blocks"] ?? [];
        if (!empty($allowedTypes)) {
            $allowedTypes = array_unique(array_merge($allowedTypes, ["icon_list", "table"]));
        } else {
            $allowedTypes = $coreBlocks;
        }

        $allowedBlocks = [];
        foreach ($allowedTypes as $type) {
            if (isset($blockMeta[$type])) {
                $allowedBlocks[] = array_merge(['type' => $type], $blockMeta[$type]);
            }
        }

        // 1. CSS (Header)
        Asset::register("gxeditor-css", "css", $siteUrl . "/inc/mod/gxeditor/assets/css/gxeditor.css", "header", [], 20);
        Asset::enqueue("gxeditor-css");

        // 2. Globals & HTML UI (Footer Raw)
        $uiHtml = '
            <style>
                .gxb-context-menu { 
                    position: fixed; z-index: 9999999; background: #fff !important; 
                    border: 1px solid #e2e8f0; border-radius: 12px; 
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); 
                    overflow: hidden; font-family: "Inter", sans-serif;
                }
                .gxb-ctx-header { 
                    background: #f8fafc; padding: 10px 14px; border-bottom: 1px solid #f1f5f9; 
                    font-weight: 700; font-size: 0.85rem; color: #334155; display: flex; align-items: center;
                }
                .gxb-ctx-body { padding: 14px; }
                .btn-context-item { 
                    width: 100%; text-align: left; background: none; border: none; 
                    padding: 8px 14px; font-size: 0.82rem; transition: background 0.2s; 
                    border-radius: 0; display: flex; align-items: center; color: #475569;
                }
                .btn-context-item:hover { background: #f1f5f9; color: #6366f1; }
                .form-label.small { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; color: #94a3b8; }
            </style>
            <!-- Inline Toolbar -->
            <div id="gxb-inline-toolbar" style="display:none; align-items:center;">
                <button type="button" data-cmd="paragraph">P</button>
                <button type="button" data-cmd="bold"><i class="bi bi-type-bold"></i></button>
                <button type="button" data-cmd="italic"><i class="bi bi-type-italic"></i></button>
                <button type="button" data-cmd="underline"><i class="bi bi-type-underline"></i></button>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="h1">H1</button>
                <button type="button" data-cmd="h2">H2</button>
                <button type="button" data-cmd="h3">H3</button>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="justifyLeft"><i class="bi bi-text-left"></i></button>
                <button type="button" data-cmd="justifyCenter"><i class="bi bi-text-center"></i></button>
                <button type="button" data-cmd="justifyRight"><i class="bi bi-text-right"></i></button>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="insertImageGX"><i class="bi bi-image"></i></button>
                <button type="button" data-cmd="createLink"><i class="bi bi-link-45deg"></i></button>
            </div>

            <!-- Block Picker -->
            <div id="gxb-picker">
                <div class="gxb-picker-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="gxb-picker-search" placeholder="Search blocks... (or type /)">
                </div>
                <div id="gxb-picker-list" class="gxb-picker-grid"></div>
            </div>

            <!-- Image Context Menu -->
            <div id="gxb-img-context" class="gxb-context-menu" style="display:none; width:220px;">
                <div class="gxb-ctx-header"><i class="bi bi-image me-2"></i>Image Properties</div>
                <div class="gxb-ctx-body">
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Dimensions</label>
                        <select id="gxb-prop-width" class="form-select form-select-sm">
                            <option value="">Responsive (Default)</option>
                            <option value="w-25">25% Width</option>
                            <option value="w-50">50% Width</option>
                            <option value="w-75">75% Width</option>
                            <option value="w-100">100% Width</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Alignment</label>
                        <select id="gxb-prop-align" class="form-select form-select-sm">
                            <option value="">Inline (None)</option>
                            <option value="float-start">Float Left</option>
                            <option value="mx-auto d-block">Center Block</option>
                            <option value="float-end">Float Right</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1 small text-muted">Visual Style</label>
                        <select id="gxb-prop-style" class="form-select form-select-sm">
                            <option value="rounded">Rounded Corners</option>
                            <option value="img-thumbnail">Thumbnail Border</option>
                            <option value="rounded-circle">Circle / Oval</option>
                            <option value="shadow">Shadow Only</option>
                        </select>
                    </div>
                    <div class="border-top pt-2">
                        <button id="gxb-img-replace" class="btn btn-sm btn-light w-100 mb-1 text-primary shadow-none"><i class="bi bi-arrow-repeat me-2"></i>Replace Image</button>
                        <button id="gxb-img-delete" class="btn btn-sm btn-light w-100 text-danger shadow-none"><i class="bi bi-trash3 me-2"></i>Delete block</button>
                    </div>
                </div>
            </div>

            <!-- Button Context Menu -->
            <div id="gxb-btn-context" class="gxb-context-menu" style="display:none; width:200px;">
                <div class="gxb-ctx-header"><i class="bi bi-hand-index-thumb me-2"></i>Button Settings</div>
                <div class="gxb-ctx-body">
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Action Link</label>
                        <input type="text" id="gxb-prop-btn-url" class="form-control form-control-sm" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1 small text-muted">Color Palette</label>
                        <select id="gxb-prop-btn-style" class="form-select form-select-sm">
                            <option value="btn-primary">Solid Primary</option>
                            <option value="btn-outline-primary">Outline Primary</option>
                            <option value="btn-success">Solid Success</option>
                            <option value="btn-danger">Solid Danger</option>
                            <option value="btn-dark">Dark Theme</option>
                            <option value="btn-light">Light Theme</option>
                            <option value="btn-link">Simple Link</option>
                        </select>
                    </div>
                    <button id="gxb-btn-delete" class="btn btn-sm btn-light w-100 text-danger border-top pt-2 mt-1 shadow-none"><i class="bi bi-trash3 me-2"></i>Delete</button>
                </div>
            </div>

            <!-- Card Context Menu -->
            <div id="gxb-card-context" class="gxb-context-menu" style="display:none; width:180px;">
                <div class="gxb-ctx-header"><i class="bi bi-card-text me-2"></i>Card Layers</div>
                <div class="gxb-ctx-body">
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input" type="checkbox" id="gxb-prop-card-header">
                        <label class="form-check-label" for="gxb-prop-card-header">Header Layer</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="gxb-prop-card-footer">
                        <label class="form-check-label" for="gxb-prop-card-footer">Footer Layer</label>
                    </div>
                    <button id="gxb-card-delete" class="btn btn-sm btn-light w-100 text-danger border-top pt-2 shadow-none"><i class="bi bi-trash3 me-2"></i>Delete Card</button>
                </div>
            </div>

            <!-- Grid Context Menu -->
            <div id="gxb-grid-context" class="gxb-context-menu" style="display:none; width:220px;">
                <div class="gxb-ctx-header"><i class="bi bi-grid-1x2 me-2"></i>Grid Layout</div>
                <div class="gxb-ctx-body">
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Column Ratio</label>
                        <select id="gxb-prop-ratio" class="form-select form-select-sm">
                            <option value="6:6">50% | 50% (Default)</option>
                            <option value="4:8">33% | 66%</option>
                            <option value="8:4">66% | 33%</option>
                            <option value="3:9">25% | 75%</option>
                            <option value="9:3">75% | 25%</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 mt-3 pt-2 border-top">
                        <button id="gxb-grid-save" class="btn btn-sm btn-primary flex-grow-1 shadow-none">Apply</button>
                        <button id="gxb-grid-delete" class="btn btn-sm btn-light text-danger shadow-none"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>

            <!-- Icon Context Menu -->
            <div id="gxb-icon-context" class="gxb-context-menu" style="display:none; width:200px;">
                <div class="gxb-ctx-header"><i class="bi bi-stars me-2"></i>Icon Styling</div>
                <div class="gxb-ctx-body">
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Icon Class</label>
                        <input type="text" id="gxb-prop-icon-class" class="form-control form-control-sm" placeholder="bi bi-star">
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Font Size</label>
                        <input type="text" id="gxb-prop-icon-size" class="form-control form-control-sm" placeholder="2.5rem">
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1 small text-muted">Icon Color</label>
                        <input type="color" id="gxb-prop-icon-color" class="form-control form-control-sm h-auto p-1 border-0" style="min-height:32px;">
                    </div>
                    <div class="d-flex gap-2 border-top pt-2">
                        <button id="gxb-icon-save" class="btn btn-sm btn-primary flex-grow-1 shadow-none text-white">Save</button>
                        <button id="gxb-icon-delete" class="btn btn-sm btn-light text-danger shadow-none"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>

            <!-- Table Context Menu -->
            <div id="gxb-table-context" class="gxb-context-menu" style="display:none; width:180px;">
                <div class="gxb-ctx-header"><i class="bi bi-table me-2"></i>Table Tools</div>
                <div class="gxb-ctx-body p-0">
                    <button id="gxb-table-add-row" class="btn btn-context-item"><i class="bi bi-plus-square me-2"></i>Add Row</button>
                    <button id="gxb-table-add-col" class="btn btn-context-item"><i class="bi bi-plus-square-dotted me-2"></i>Add Column</button>
                    <div class="border-top my-1"></div>
                    <button id="gxb-table-del-row" class="btn btn-context-item text-danger"><i class="bi bi-dash-square me-2"></i>Delete Row</button>
                    <button id="gxb-table-del-col" class="btn btn-context-item text-danger"><i class="bi bi-dash-square-dotted me-2"></i>Delete Column</button>
                    <div class="border-top mt-2 pt-2 px-2 pb-2">
                        <button id="gxb-table-delete" class="btn btn-sm btn-light w-100 text-danger shadow-none"><i class="bi bi-trash3 me-2"></i>Remove Table</button>
                    </div>
                </div>
            </div>

            <!-- Post Context Menu -->
            <div id="gxb-post-context" class="gxb-context-menu" style="display:none; width:180px;">
                <div class="gxb-ctx-header"><i class="bi bi-card-checklist me-2"></i>Post Embed</div>
                <div class="gxb-ctx-body">
                    <div class="mb-3">
                        <label class="form-label mb-1 small text-muted">Numeric ID</label>
                        <input type="text" id="gxb-prop-post-id" class="form-control form-control-sm" placeholder="Post ID">
                    </div>
                    <div class="d-flex gap-2">
                        <button id="gxb-post-save" class="btn btn-sm btn-primary flex-grow-1 shadow-none">Update</button>
                        <button id="gxb-post-delete" class="btn btn-sm btn-light text-danger shadow-none"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>

            <!-- TOC Context Menu -->
            <div id="gxb-toc-context" class="gxb-context-menu" style="display:none; width:220px;">
                <div class="gxb-ctx-header"><i class="bi bi-list-nested me-2"></i>TOC Settings</div>
                <div class="gxb-ctx-body">
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Title</label>
                        <input type="text" id="gxb-prop-toc-title" class="form-control form-control-sm" placeholder="Daftar Isi">
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Float</label>
                        <select id="gxb-prop-toc-float" class="form-select form-select-sm">
                            <option value="none">Full Width</option>
                            <option value="float-start">Float Left</option>
                            <option value="float-end">Float Right</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">Width</label>
                        <input type="text" id="gxb-prop-toc-width" class="form-control form-control-sm" placeholder="450px">
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1 small text-muted">Collapsible</label>
                        <select id="gxb-prop-toc-collapse" class="form-select form-select-sm">
                            <option value="no">Start Expanded</option>
                            <option value="yes">Start Collapsed</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button id="gxb-toc-save" class="btn btn-sm btn-primary flex-grow-1 shadow-none">Save</button>
                        <button id="gxb-toc-delete" class="btn btn-sm btn-light text-danger shadow-none"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>

            <!-- Text/Paragraph Context Menu -->
            <div id="gxb-text-context" class="gxb-context-menu" style="display:none; width:180px;">
                <div class="gxb-ctx-header"><i class="bi bi-textarea-t me-2"></i>Text Formatting</div>
                <div class="gxb-ctx-body">
                    <div class="mb-3">
                        <label class="form-label mb-1 small text-muted">Alignment</label>
                        <select id="gxb-prop-text-align" class="form-select form-select-sm">
                            <option value="">Default Alignment</option>
                            <option value="left">Left Aligned</option>
                            <option value="center">Centered</option>
                            <option value="right">Right Aligned</option>
                            <option value="justify">Justified</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button id="gxb-text-save" class="btn btn-sm btn-primary flex-grow-1 shadow-none font-weight-bold">Apply</button>
                        <button id="gxb-text-delete" class="btn btn-sm btn-light text-danger shadow-none"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>

            <!-- Icon List Context Menu -->
            <div id="gxb-iconlist-context" class="gxb-context-menu" style="display:none; width:200px;">
                <div class="gxb-ctx-header"><i class="bi bi-check2-square me-2"></i>Icon List Props</div>
                <div class="gxb-ctx-body">
                    <div class="mb-2">
                        <label class="form-label mb-1 small text-muted">List Icon Class</label>
                        <input type="text" id="gxb-prop-iconlist-class" class="form-control form-control-sm" placeholder="bi bi-check">
                    </div>
                    <div class="mb-3">
                        <label class="form-label mb-1 small text-muted">Marker Color</label>
                        <input type="color" id="gxb-prop-iconlist-color" class="form-control form-control-sm h-auto p-1 border-0" style="min-height:32px;">
                    </div>
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button id="gxb-iconlist-save" class="btn btn-sm btn-primary flex-grow-1 shadow-none">Save Changes</button>
                        <button id="gxb-iconlist-delete" class="btn btn-sm btn-light text-danger shadow-none"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>

            <!-- Classic Toolbar Template -->
            <div id="gxb-classic-toolbar-template" style="display:none;">
                <div class="gxb-classic-toolbar border-bottom shadow-sm">
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-sm btn-light p-1 border-0 shadow-none d-flex align-items-center" data-bs-toggle="dropdown" style="font-size:0.8rem; font-weight:700;">H <i class="bi bi-chevron-down ms-1" style="font-size:0.6rem;"></i></button>
                            <ul class="dropdown-menu shadow-sm">
                                <li><a class="dropdown-item" href="#" data-cmd="h1">Heading 1</a></li>
                                <li><a class="dropdown-item" href="#" data-cmd="h2">Heading 2</a></li>
                                <li><a class="dropdown-item" href="#" data-cmd="h3">Heading 3</a></li>
                                <li><a class="dropdown-item" href="#" data-cmd="paragraph">Paragraph</a></li>
                            </ul>
                        </div>
                        <div class="gxb-tb-sep"></div>
                        <button type="button" data-cmd="bold"><i class="bi bi-type-bold"></i></button>
                        <button type="button" data-cmd="italic"><i class="bi bi-type-italic"></i></button>
                        <button type="button" data-cmd="underline"><i class="bi bi-type-underline"></i></button>
                        <button type="button" data-cmd="createLink"><i class="bi bi-link-45deg"></i></button>
                        <div class="ms-auto">
                            <button type="button" id="gxb-classic-add-btn" title="Add Block"><i class="bi bi-plus-circle-fill" style="color:#6366f1; font-size:1.1rem;"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                var GX_ELFINDER_URL = ' . $elfinderAjaxUrlJson . ';
                var GX_EDITOR_BLOCKS = ' . json_encode($allowedBlocks) . ';
                var GX_EDITOR_STYLE = ' . json_encode($editorStyle) . ';
            </script>
        ';
        Asset::register("gxeditor-ui", "raw", $uiHtml, "footer", [], 21);
        Asset::enqueue("gxeditor-ui");

        // 3. JS Modules (Footer) - Critical: utils must load before builder-js (v21)
        Asset::register("gxeditor-utils", "js", $siteUrl . "/inc/mod/gxeditor/assets/js/gxeditor-utils.js", "footer", ["jquery"], 15);
        Asset::register("gxeditor-ui-js", "js", $siteUrl . "/inc/mod/gxeditor/assets/js/gxeditor-ui.js", "footer", ["gxeditor-utils"], 23);
        Asset::register("gxeditor-parser", "js", $siteUrl . "/inc/mod/gxeditor/assets/js/gxeditor-parser.js", "footer", ["gxeditor-ui-js"], 24);
        Asset::register("gxeditor-renderer", "js", $siteUrl . "/inc/mod/gxeditor/assets/js/gxeditor-renderer.js", "footer", ["gxeditor-parser"], 25);
        Asset::register("gxeditor-main", "js", $siteUrl . "/inc/mod/gxeditor/assets/js/gxeditor.js", "footer", ["gxeditor-renderer"], 26);
        Asset::enqueue("gxeditor-main");
    }
}

// Hooks::filter('editor_type_options', array('Gxeditor', 'registerEditorOption')); // No longer needed as we use Editor::register
Gxeditor::init();
