<?php
/**
 * GeniXCMS - Content Management Systemhouse.
 *
 * PHP Based Content Management System and Framework
 */
defined('GX_LIB') or die('Direct Access Not Allowed!');

// ── DATA HANDLING ──────────────────────────────────────────────────
if (isset($_POST['seonix_save'])) {
    $seonix_data = [
        'og_enable'        => isset($_POST['og_enable']) ? 'yes' : 'no',
        'twitter_enable'   => isset($_POST['twitter_enable']) ? 'yes' : 'no',
        'twitter_site'     => Typo::cleanX($_POST['twitter_site'] ?? ''),
        'ga_id'            => Typo::cleanX($_POST['ga_id'] ?? ''),
        'fb_pixel_id'      => Typo::cleanX($_POST['fb_pixel_id'] ?? ''),
        'indexnow_enable'  => isset($_POST['indexnow_enable']) ? 'yes' : 'no',
        'indexnow_key'     => Typo::cleanX($_POST['indexnow_key'] ?? ''),
        
        'index_posts'      => $_POST['index_posts'] ?? 'yes',
        'follow_posts'     => $_POST['follow_posts'] ?? 'yes',
        'index_pages'      => $_POST['index_pages'] ?? 'yes',
        'follow_pages'     => $_POST['follow_pages'] ?? 'yes',
        'index_categories' => $_POST['index_categories'] ?? 'yes',
        'follow_categories'=> $_POST['follow_categories'] ?? 'yes',
        'index_tags'       => $_POST['index_tags'] ?? 'yes',
        'follow_tags'      => $_POST['follow_tags'] ?? 'yes',

        // Expanded SERP Identity
        'title_home'       => Typo::cleanX($_POST['title_home'] ?? '%sitename% | %slogan%'),
        'desc_home'        => Typo::cleanX($_POST['desc_home'] ?? '%site_desc%'),
        'kw_home'          => Typo::cleanX($_POST['kw_home'] ?? ''),
        
        'title_posts'      => Typo::cleanX($_POST['title_posts'] ?? '%title% | %sitename%'),
        'desc_posts'       => Typo::cleanX($_POST['desc_posts'] ?? '%post_desc%'),
        'kw_posts'         => Typo::cleanX($_POST['kw_posts'] ?? '%tags_list%'),
        
        'title_pages'      => Typo::cleanX($_POST['title_pages'] ?? '%title% | %sitename%'),
        'desc_pages'       => Typo::cleanX($_POST['desc_pages'] ?? '%post_desc%'),
        'kw_pages'         => Typo::cleanX($_POST['kw_pages'] ?? ''),
        
        'title_categories' => Typo::cleanX($_POST['title_categories'] ?? '%title% | %sitename%'),
        'desc_categories'  => Typo::cleanX($_POST['desc_categories']  ?? 'Archive for category %title%'),
        'kw_categories'    => Typo::cleanX($_POST['kw_categories']    ?? ''),
        
        'title_tags'       => Typo::cleanX($_POST['title_tags']       ?? '%title% | %sitename%'),
        'desc_tags'        => Typo::cleanX($_POST['desc_tags']        ?? 'Archive for tag %title%'),
        'kw_tags'          => Typo::cleanX($_POST['kw_tags']          ?? ''),
        
        'title_authors'    => Typo::cleanX($_POST['title_authors']    ?? 'Posts by %author% | %sitename%'),
        'desc_authors'     => Typo::cleanX($_POST['desc_authors']     ?? 'Explore all posts authored by %author% at %sitename%'),
        'kw_authors'       => Typo::cleanX($_POST['kw_authors']       ?? ''),
        
        'title_search'     => Typo::cleanX($_POST['title_search']     ?? 'Search results for %query% | %sitename%'),
        'desc_search'      => Typo::cleanX($_POST['desc_search']      ?? 'Search our platform for %query% and find relevant content.'),
        'kw_search'        => Typo::cleanX($_POST['kw_search']        ?? ''),
        
        'meta_keywords_global' => Typo::cleanX($_POST['meta_keywords_global'] ?? ''),
    ];

    $json_data = json_encode($seonix_data, JSON_UNESCAPED_UNICODE);
    Options::update('seonix_options', $json_data);
    $data['alertSuccess'][] = _("SeoNix orchestration suite has been synchronized.");

    if (!empty($seonix_data['indexnow_key'])) {
        $key = $seonix_data['indexnow_key'];
        $file_path = GX_PATH . '/' . $key . '.txt';
        if (!file_exists($file_path)) {
            @file_put_contents($file_path, $key);
        }
    }
}

$raw_opt = Options::get('seonix_options');
$o = $raw_opt ? json_decode($raw_opt, true) : [];

// Identity Defaults
$o['title_home']       = $o['title_home'] ?? '%sitename% | %slogan%';
$o['desc_home']        = $o['desc_home'] ?? '%site_desc%';
$o['kw_home']          = $o['kw_home'] ?? '';

$o['title_posts']      = $o['title_posts'] ?? '%title% | %sitename%';
$o['desc_posts']       = $o['desc_posts'] ?? '%post_desc%';
$o['kw_posts']         = $o['kw_posts'] ?? '%tags_list%';

$o['title_pages']      = $o['title_pages'] ?? '%title% | %sitename%';
$o['desc_pages']       = $o['desc_pages'] ?? '%post_desc%';
$o['kw_pages']         = $o['kw_pages'] ?? '';

$o['title_categories'] = $o['title_categories'] ?? '%title% | %sitename%';
$o['desc_categories']  = $o['desc_categories'] ?? 'Archive for category %title%';
$o['kw_categories']    = $o['kw_categories'] ?? '';

$o['title_tags']       = $o['title_tags'] ?? '%title% | %sitename%';
$o['desc_tags']        = $o['desc_tags'] ?? 'Archive for tag %title%';
$o['kw_tags']          = $o['kw_tags'] ?? '';

$o['title_authors']    = $o['title_authors'] ?? 'Posts by %author% | %sitename%';
$o['desc_authors']     = $o['desc_authors'] ?? 'Explore posts authored by %author% at %sitename%';
$o['kw_authors']       = $o['kw_authors'] ?? '';

$o['title_search']     = $o['title_search'] ?? 'Search results for %query% | %sitename%';
$o['desc_search']      = $o['desc_search'] ?? 'Search for %query% at %sitename%';
$o['kw_search']        = $o['kw_search'] ?? '';

$o['meta_keywords_global'] = $o['meta_keywords_global'] ?? '';

// Analytics & Social Defaults
$o['og_enable']        = $o['og_enable'] ?? 'yes';
$o['twitter_enable']   = $o['twitter_enable'] ?? 'yes';
$o['twitter_site']     = $o['twitter_site'] ?? '@username';
$o['ga_id']            = $o['ga_id'] ?? '';
$o['fb_pixel_id']      = $o['fb_pixel_id'] ?? '';
$o['indexnow_enable']  = $o['indexnow_enable'] ?? 'no';
$o['indexnow_key']     = $o['indexnow_key'] ?? '';

// Indexing Defaults
$o['index_posts']      = $o['index_posts'] ?? 'yes';
$o['follow_posts']     = $o['follow_posts'] ?? 'yes';
$o['index_pages']      = $o['index_pages'] ?? 'yes';
$o['follow_pages']     = $o['follow_pages'] ?? 'yes';
$o['index_categories'] = $o['index_categories'] ?? 'yes';
$o['follow_categories']= $o['follow_categories'] ?? 'yes';
$o['index_tags']       = $o['index_tags'] ?? 'yes';
$o['follow_tags']      = $o['follow_tags'] ?? 'yes';

$new_key = sprintf('%04x%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
$in_key = $o['indexnow_key'] ?: $new_key;

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('SeoNix Engine'),
        'subtitle' => _('Search Engine Optimization orchestrator for GeniXCMS.'),
        'icon' => 'bi bi-graph-up-arrow',
        'button' => [
            'type' => 'button', 'label' => _('Sync Protocols'), 'icon' => 'bi bi-shield-check',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold', 'name' => 'seonix_save', 'attr' => 'value="Save"'
        ],
    ],
    'card_wrapper' => false,
    'content' => [
        ['type' => 'row', 'items' => [
            ['width' => 9, 'content' => [
                ['type' => 'tabs_nav', 'id' => 'seoTabs', 'tabs' => [
                    'identity' => ['label' => _('SERP Identity'), 'icon' => 'bi bi-window-sidebar'],
                    'indexing' => ['label' => _('Crawler Hub'), 'icon' => 'bi bi-robot'],
                    'social'   => ['label' => _('Social Graph'), 'icon' => 'bi bi-share-fill'],
                    'analytics'=> ['label' => _('Insight Hub'), 'icon' => 'bi bi-bar-chart-fill'],
                ]],
                ['type' => 'raw', 'html' => '<div class="tab-content" id="seoTabsContent">'],
                ['type' => 'tab_content', 'id' => 'identity', 'active' => true, 'body_elements' => [
                    ['type' => 'card', 'body_elements' => [
                        ['type' => 'heading', 'text' => _('Universal Identity Architecture'), 'icon' => 'bi bi-fingerprint', 'subtitle' => _('Detailed presentation parameters for every site architecture segment.')],
                        ['type' => 'alert', 'style' => 'primary', 'content' => '
                            <div class="extra-small fw-bold">
                                <i class="bi bi-info-circle-fill me-2"></i> '._("Supported Variables:").' 
                                <code class="ms-2">%title%</code>, <code>%sitename%</code>, <code>%slogan%</code>, <code>%post_desc%</code>, <code>%site_desc%</code>, <code>%keywords%</code>, <code>%tags_list%</code>, <code>%author%</code>, <code>%query%</code>
                            </div>'],
                        
                        ['type' => 'row', 'items' => [
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-4">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-house-door me-1"></i> '._("Home Landing Page").'</label>
                                        <input type="text" name="title_home" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" value="'.htmlspecialchars($o['title_home']).'" placeholder="'._("Title Format").'">
                                        <textarea name="desc_home" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" rows="2" placeholder="'._("Meta Description").'">'.htmlspecialchars($o['desc_home']).'</textarea>
                                        <input type="text" name="kw_home" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none" value="'.htmlspecialchars($o['kw_home']).'" placeholder="'._("Meta Keywords").'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-4">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-file-post me-1"></i> '._("Single Post Protocol").'</label>
                                        <input type="text" name="title_posts" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" value="'.htmlspecialchars($o['title_posts']).'" placeholder="'._("Title Format").'">
                                        <textarea name="desc_posts" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" rows="2" placeholder="'._("Meta Description").'">'.htmlspecialchars($o['desc_posts']).'</textarea>
                                        <input type="text" name="kw_posts" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none" value="'.htmlspecialchars($o['kw_posts']).'" placeholder="'._("Meta Keywords (Try %tags_list%)").'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-4 h-100">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-file-earmark-code me-1"></i> '._("Static Pages Architecture").'</label>
                                        <input type="text" name="title_pages" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" value="'.htmlspecialchars($o['title_pages']).'" placeholder="'._("Title Format").'">
                                        <textarea name="desc_pages" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" rows="2" placeholder="'._("Meta Description").'">'.htmlspecialchars($o['desc_pages']).'</textarea>
                                        <input type="text" name="kw_pages" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none" value="'.htmlspecialchars($o['kw_pages']).'" placeholder="'._("Meta Keywords").'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-4 h-100">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-folder2-open me-1"></i> '._("Taxonomy Category Archive").'</label>
                                        <input type="text" name="title_categories" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" value="'.htmlspecialchars($o['title_categories']).'" placeholder="'._("Title Format").'">
                                        <textarea name="desc_categories" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" rows="2" placeholder="'._("Meta Description").'">'.htmlspecialchars($o['desc_categories']).'</textarea>
                                        <input type="text" name="kw_categories" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none" value="'.htmlspecialchars($o['kw_categories']).'" placeholder="'._("Meta Keywords").'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-4 h-100">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-tags me-1"></i> '._("Taxonomy Tag Archive").'</label>
                                        <input type="text" name="title_tags" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" value="'.htmlspecialchars($o['title_tags']).'" placeholder="'._("Title Format").'">
                                        <textarea name="desc_tags" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" rows="2" placeholder="'._("Meta Description").'">'.htmlspecialchars($o['desc_tags']).'</textarea>
                                        <input type="text" name="kw_tags" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none" value="'.htmlspecialchars($o['kw_tags']).'" placeholder="'._("Meta Keywords").'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-4 h-100">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-person-badge me-1"></i> '._("Author Intelligence Hub").'</label>
                                        <input type="text" name="title_authors" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" value="'.htmlspecialchars($o['title_authors']).'" placeholder="'._("Title Format").'">
                                        <textarea name="desc_authors" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" rows="2" placeholder="'._("Meta Description").'">'.htmlspecialchars($o['desc_authors']).'</textarea>
                                        <input type="text" name="kw_authors" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none" value="'.htmlspecialchars($o['kw_authors']).'" placeholder="'._("Meta Keywords").'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-0 h-100">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-search me-1"></i> '._("Search Discovery Engine").'</label>
                                        <input type="text" name="title_search" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" value="'.htmlspecialchars($o['title_search']).'" placeholder="'._("Title Format").'">
                                        <textarea name="desc_search" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold mb-2 shadow-none" rows="2" placeholder="'._("Meta Description").'">'.htmlspecialchars($o['desc_search']).'</textarea>
                                        <input type="text" name="kw_search" class="form-control border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none" value="'.htmlspecialchars($o['kw_search']).'" placeholder="'._("Meta Keywords").'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-20 h-100">
                                        <label class="form-label fw-black text-primary extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-key-fill me-1"></i> '._("Global Seed Metadata").'</label>
                                        <textarea name="meta_keywords_global" class="form-control border-0 bg-white rounded-3 py-2 fs-9 fw-bold shadow-none" rows="3" placeholder="'._("Enter primary seed keywords...").'">'.htmlspecialchars($o['meta_keywords_global']).'</textarea>
                                    </div>']
                            ]]
                        ]]
                    ]]
                ]],
                ['type' => 'tab_content', 'id' => 'indexing', 'body_elements' => [
                    ['type' => 'card', 'full_height' => true, 'body_elements' => [
                        ['type' => 'heading', 'text' => _('Bot Navigation Terminal'), 'icon' => 'bi bi-signpost-2-fill', 'subtitle' => _('Manage crawler indexing and following directives for site taxonomies.')],
                        ['type' => 'row', 'items' => [
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-file-post me-1"></i> '._("Single Posts").'</label>
                                        <div class="d-flex gap-2">
                                            <select name="index_posts" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['index_posts'] == 'yes' ? 'selected' : '').'>INDEX</option>
                                                <option value="no" '.($o['index_posts'] == 'no' ? 'selected' : '').'>NOINDEX</option>
                                            </select>
                                            <select name="follow_posts" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['follow_posts'] == 'yes' ? 'selected' : '').'>FOLLOW</option>
                                                <option value="no" '.($o['follow_posts'] == 'no' ? 'selected' : '').'>NOFOLLOW</option>
                                            </select>
                                        </div>
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-file-earmark-code me-1"></i> '._("Static Pages").'</label>
                                        <div class="d-flex gap-2">
                                            <select name="index_pages" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['index_pages'] == 'yes' ? 'selected' : '').'>INDEX</option>
                                                <option value="no" '.($o['index_pages'] == 'no' ? 'selected' : '').'>NOINDEX</option>
                                            </select>
                                            <select name="follow_pages" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['follow_pages'] == 'yes' ? 'selected' : '').'>FOLLOW</option>
                                                <option value="no" '.($o['follow_pages'] == 'no' ? 'selected' : '').'>NOFOLLOW</option>
                                            </select>
                                        </div>
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-folder2-open me-1"></i> '._("Categories").'</label>
                                        <div class="d-flex gap-2">
                                            <select name="index_categories" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['index_categories'] == 'yes' ? 'selected' : '').'>INDEX</option>
                                                <option value="no" '.($o['index_categories'] == 'no' ? 'selected' : '').'>NOINDEX</option>
                                            </select>
                                            <select name="follow_categories" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['follow_categories'] == 'yes' ? 'selected' : '').'>FOLLOW</option>
                                                <option value="no" '.($o['follow_categories'] == 'no' ? 'selected' : '').'>NOFOLLOW</option>
                                            </select>
                                        </div>
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-3 bg-light rounded-4 border mb-3">
                                        <label class="form-label fw-black text-dark extra-small text-uppercase tracking-widest mb-2" style="font-size:0.6rem;"><i class="bi bi-tags me-1"></i> '._("Tags").'</label>
                                        <div class="d-flex gap-2">
                                            <select name="index_tags" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['index_tags'] == 'yes' ? 'selected' : '').'>INDEX</option>
                                                <option value="no" '.($o['index_tags'] == 'no' ? 'selected' : '').'>NOINDEX</option>
                                            </select>
                                            <select name="follow_tags" class="form-select border-0 bg-white rounded-3 py-1 fs-9 fw-bold shadow-none">
                                                <option value="yes" '.($o['follow_tags'] == 'yes' ? 'selected' : '').'>FOLLOW</option>
                                                <option value="no" '.($o['follow_tags'] == 'no' ? 'selected' : '').'>NOFOLLOW</option>
                                            </select>
                                        </div>
                                    </div>']
                            ]]
                        ]],
                        ['type' => 'raw', 'html' => '
                            <div class="bg-primary bg-opacity-10 rounded-4 p-4 border border-primary border-opacity-25 mt-2">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="indexnow_enable" id="inEnable" '.($o['indexnow_enable'] == 'yes' ? 'checked' : '').'>
                                    <label class="form-check-label ms-2 fw-black text-primary text-uppercase fs-9 tracking-widest" for="inEnable">'._("IndexNow Instant Pinger").'</label>
                                </div>
                                <input type="text" name="indexnow_key" class="form-control border bg-white rounded-3 py-1 fs-8 fw-bold px-3 shadow-none font-monospace" value="'.htmlspecialchars($in_key).'">
                            </div>']
                    ]]
                ]],
                ['type' => 'tab_content', 'id' => 'social', 'body_elements' => [
                    ['type' => 'card', 'full_height' => true, 'body_elements' => [
                        ['type' => 'heading', 'text' => _('Social Graph Optimization'), 'icon' => 'bi bi-lightning-charge', 'subtitle' => _('Control the visual fidelity of shared links across global social ecosystems.')],
                        ['type' => 'row', 'items' => [
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-4 bg-light rounded-4 border border-start border-4 border-success h-100">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="og_enable" id="ogEnable" '.($o['og_enable'] == 'yes' ? 'checked' : '').'>
                                            <label class="form-check-label ps-2" for="ogEnable">
                                                <div class="fw-black text-dark text-uppercase tracking-wider fs-9 mb-0">'._("OpenGraph Protocol").'</div>
                                                <div class="extra-small text-muted fw-bold">'._("Optimize Metadata for FB/LinkedIn").'</div>
                                            </label>
                                        </div>
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-4 bg-light rounded-4 border border-start border-4 border-info h-100">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="twitter_enable" id="twEnable" '.($o['twitter_enable'] == 'yes' ? 'checked' : '').'>
                                            <label class="form-check-label ps-2" for="twEnable">
                                                <div class="fw-black text-dark text-uppercase tracking-wider fs-9 mb-0">'._("X (Twitter) Cards").'</div>
                                                <div class="extra-small text-muted fw-bold">'._("Optimize Feeds for Microblogging").'</div>
                                            </label>
                                        </div>
                                    </div>']
                            ]]
                        ]],
                        ['type' => 'raw', 'html' => '
                            <div class="mt-4 p-4 bg-light rounded-4 border">
                                <label class="form-label fw-black text-muted extra-small text-uppercase tracking-widest mb-1" style="font-size:0.6rem;">'._("X (Twitter) Handle Identity").'</label>
                                <input type="text" name="twitter_site" class="form-control border bg-white rounded-3 py-1 fs-8 fw-bold px-3 shadow-none" value="'.htmlspecialchars($o['twitter_site']).'">
                            </div>']
                    ]]
                ]],
                ['type' => 'tab_content', 'id' => 'analytics', 'body_elements' => [
                    ['type' => 'card', 'full_height' => true, 'body_elements' => [
                        ['type' => 'heading', 'text' => _('Insight Telemetry Hub'), 'icon' => 'bi bi-terminal-dash', 'subtitle' => _('Deploy advanced tracking payloads for precise user behavior telemetry.')],
                        ['type' => 'row', 'items' => [
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-4 bg-light rounded-4 border h-100 border-start border-4 border-warning">
                                        <label class="form-label fw-black text-muted extra-small text-uppercase tracking-widest mb-1" style="font-size:0.7rem;">'._("GA4 Measurement ID").'</label>
                                        <input type="text" name="ga_id" class="form-control border bg-white rounded-3 py-1 fs-8 fw-bold px-3 shadow-none font-monospace" value="'.htmlspecialchars($o['ga_id']).'">
                                    </div>']
                            ]],
                            ['width' => 6, 'content' => [
                                ['type' => 'raw', 'html' => '
                                    <div class="p-4 bg-light rounded-4 border h-100 border-start border-4 border-primary">
                                        <label class="form-label fw-black text-muted extra-small text-uppercase tracking-widest mb-1" style="font-size:0.7rem;">'._("Meta Pixel ID").'</label>
                                        <input type="text" name="fb_pixel_id" class="form-control border bg-white rounded-3 py-1 fs-8 fw-bold px-3 shadow-none font-monospace" value="'.htmlspecialchars($o['fb_pixel_id']).'">
                                    </div>']
                            ]]
                        ]]
                    ]]
                ]],
                ['type' => 'raw', 'html' => '</div>'],
            ]],
            ['width' => 3, 'content' => [
                ['type' => 'card', 'body_elements' => [
                    ['type' => 'raw', 'html' => '
                        <div class="text-center py-2">
                             <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                                <i class="bi bi-shield-check text-success fs-1"></i>
                            </div>
                            <h6 class="fw-black text-dark text-uppercase tracking-widest fs-8 mb-2">'._("Apply SEONIX Protocols").'</h6>
                            <p class="extra-small text-muted fw-bold mb-4">'._("Synchronize entire SEO orchestration engine.").'</p>
                            <div class="d-grid shadow-none">
                                <button type="submit" name="seonix_save" class="btn btn-primary rounded-pill py-2 fw-black text-uppercase fs-9 shadow-sm border-0">
                                    <i class="bi bi-cpu-fill me-2"></i> '._("Update Engine").'
                                </button>
                            </div>
                        </div>']
                ]],
                 ['type' => 'card', 'title' => _('Strategy Advisor'), 'icon' => 'bi bi-lightbulb', 'body_elements' => [
                    ['type' => 'raw', 'html' => '
                        <div class="d-flex flex-column gap-3 mt-1">
                            <div class="extra-small text-muted fw-bold lh-base py-2 border-bottom border-light">
                                <i class="bi bi-info-circle text-primary me-2"></i>'._("Use %tags_list% for single post keywords.").'
                            </div>
                            <div class="extra-small text-muted fw-bold lh-base">
                                <i class="bi bi-info-circle text-primary me-2"></i>'._("Global seed keywords provide a solid SEO baseline.").'
                            </div>
                        </div>']
                ]]
            ]]
        ]]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<form action="" method="post">';
echo '<input type="hidden" name="token" value="'.TOKEN.'">';
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data ?? []);
echo '</div>';

$builder = new UiBuilder($schema);
$builder->render();
echo '</form>';
?>
