<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

// --- Handling Save Action ---
if (isset($_POST['seonix_save'])) {
    if (!Token::validate($_POST['token'])) {
        $data['alertDanger'][] = _("Invalid or expired token. Please try again.");
    } else {
        $seonix_data = [
            'title_format'     => Typo::cleanX($_POST['title_format'] ?? '%title% | %sitename%'),
            'meta_keywords'    => Typo::cleanX($_POST['meta_keywords'] ?? ''),
            'meta_description' => Typo::cleanX($_POST['meta_description'] ?? ''),
            'og_enable'        => isset($_POST['og_enable']) ? 'yes' : 'no',
            'twitter_enable'   => isset($_POST['twitter_enable']) ? 'yes' : 'no',
            'twitter_site'     => Typo::cleanX($_POST['twitter_site'] ?? ''),
            'ga_id'            => Typo::cleanX($_POST['ga_id'] ?? ''),
            'fb_pixel_id'      => Typo::cleanX($_POST['fb_pixel_id'] ?? ''),
            'indexnow_enable'  => isset($_POST['indexnow_enable']) ? 'yes' : 'no',
            'indexnow_key'     => Typo::cleanX($_POST['indexnow_key'] ?? ''),
            'index_categories' => isset($_POST['index_categories']) ? 'yes' : 'no',
            'index_tags'       => isset($_POST['index_tags']) ? 'yes' : 'no',
        ];
        
        // Remove 'seonix_options_raw' if it exists and save as JSON
        if (Options::update('seonix_options', json_encode($seonix_data, JSON_UNESCAPED_UNICODE))) {
            $data['alertSuccess'][] = _("SeoNix configuration has been successfully updated.");
        } else {
            // First time setup: insert if update fails
            Options::insert(['seonix_options' => json_encode($seonix_data, JSON_UNESCAPED_UNICODE)]);
            $data['alertSuccess'][] = _("SeoNix configuration initialized and saved.");
        }
        
        // Handle IndexNow key file creation
        if (!empty($seonix_data['indexnow_key'])) {
            $key = $seonix_data['indexnow_key'];
            $file_path = GX_PATH . '/' . $key . '.txt';
            if (!file_exists($file_path)) {
                @file_put_contents($file_path, $key);
            }
        }
    }
}

// --- Fetch Current Data ---
$raw_opt = Options::get('seonix_options');
$o = $raw_opt ? json_decode($raw_opt, true) : [];

// Safety defaults
$o['title_format']     = $o['title_format'] ?? '%title% | %sitename%';
$o['meta_keywords']    = $o['meta_keywords'] ?? '';
$o['meta_description'] = $o['meta_description'] ?? '';
$o['og_enable']        = $o['og_enable'] ?? 'yes';
$o['twitter_enable']   = $o['twitter_enable'] ?? 'yes';
$o['twitter_site']     = $o['twitter_site'] ?? '@username';
$o['ga_id']            = $o['ga_id'] ?? '';
$o['fb_pixel_id']      = $o['fb_pixel_id'] ?? '';
$o['indexnow_enable']  = $o['indexnow_enable'] ?? 'no';
$o['indexnow_key']     = $o['indexnow_key'] ?? '';
$o['index_categories'] = $o['index_categories'] ?? 'yes';
$o['index_tags']       = $o['index_tags'] ?? 'yes';
?>

<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data ?? []);?>
    </div>
</div>

<div class="container-fluid py-4 mb-5">
    
    <!-- Module Header -->
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-graph-up-arrow text-primary me-2"></i> SeoNix
            </h3>
            <p class="text-muted mb-0">The Official Search Engine Optimization Engine for GeniXCMS.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary border-opacity-25">
                <i class="bi bi-robot me-1"></i> Core Module Ready
            </span>
        </div>
    </div>

    <form action="" method="post">
        
        <div class="row g-4">
            
            <!-- Left Column: Main Settings -->
            <div class="col-lg-8">
                
                <!-- Section 1: Title & Meta -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-window fw-bold fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark text-uppercase mb-1"><?=_("Metadata & Titles");?></h6>
                                <p class="small text-muted mb-0">Configure how your site appears in search engine results.</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1"><?=_("Title Format");?></label>
                            <input type="text" name="title_format" class="form-control form-control-lg bg-light border-0" value="<?=htmlspecialchars($o['title_format']);?>" required>
                            <div class="form-text fs-8 mt-2">
                                Available variables: <code>%title%</code> (Page/Post Title), <code>%sitename%</code> (Site Name), <code>%slogan%</code> (Site Slogan).
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1"><?=_("Global Meta Description");?></label>
                            <textarea name="meta_description" class="form-control bg-light border-0" rows="3" placeholder="Enter a brief, compelling description..."><?=htmlspecialchars($o['meta_description']);?></textarea>
                            <div class="form-text fs-8 mt-2">This is used as the fallback description if a specific page or post lacks its own meta description. Recommended length: 150-160 characters.</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1"><?=_("Global Meta Keywords");?></label>
                            <input type="text" name="meta_keywords" class="form-control bg-light border-0" value="<?=htmlspecialchars($o['meta_keywords']);?>" placeholder="keyword 1, keyword 2, ...">
                            <div class="form-text fs-8 mt-2">Comma separated keywords. (Note: Most modern search engines ignore this field, but it can be useful for internal tagging).</div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Social Graph -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-share-fill fw-bold fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark text-uppercase mb-1"><?=_("Social Graph Integration");?></h6>
                                <p class="small text-muted mb-0">Optimize how links look when shared on platforms like Facebook and Twitter.</p>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="bg-light rounded-4 p-3 border border-light shadow-none">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="og_enable" id="ogEnable" <?=$o['og_enable'] == 'yes' ? 'checked' : '';?>>
                                        <label class="form-check-label fw-bold ms-2" for="ogEnable">
                                            Enable OpenGraph Data
                                            <span class="d-block text-muted fw-normal" style="font-size:11px;">(Facebook, LinkedIn, WhatsApp)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light rounded-4 p-3 border border-light shadow-none">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="twitter_enable" id="twEnable" <?=$o['twitter_enable'] == 'yes' ? 'checked' : '';?>>
                                        <label class="form-check-label fw-bold ms-2" for="twEnable">
                                            Enable Twitter Cards
                                            <span class="d-block text-muted fw-normal" style="font-size:11px;">(X / Twitter specific graph)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1"><?=_("Twitter Site Account");?></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-twitter-x"></i></span>
                                <input type="text" name="twitter_site" class="form-control bg-light border-0" value="<?=htmlspecialchars($o['twitter_site']);?>" placeholder="@youraccount">
                            </div>
                            <div class="form-text fs-8 mt-2">Required if Twitter Cards are enabled. Example: <code>@genixcms</code></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Analytics & Indexing -->
            <div class="col-lg-4">
                
                <!-- Section 3: Indexing Control -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark text-uppercase mb-4"><i class="bi bi-robot text-danger me-2"></i><?=_("Crawler Indexing");?></h6>
                        
                        <div class="mb-4">
                            <label class="fw-bold text-dark fs-7 mb-2 d-block">Categories Archieve</label>
                            <select name="index_categories" class="form-select bg-light border-0">
                                <option value="yes" <?=$o['index_categories'] == 'yes' ? 'selected' : '';?>>Index (Allow Search Engines)</option>
                                <option value="no" <?=$o['index_categories'] == 'no' ? 'selected' : '';?>>NoIndex (Hide from Search Engines)</option>
                            </select>
                        </div>

                        <div>
                            <label class="fw-bold text-dark fs-7 mb-2 d-block">Tags Archive</label>
                            <select name="index_tags" class="form-select bg-light border-0">
                                <option value="yes" <?=$o['index_tags'] == 'yes' ? 'selected' : '';?>>Index (Allow Search Engines)</option>
                                <option value="no" <?=$o['index_tags'] == 'no' ? 'selected' : '';?>>NoIndex (Hide from Search Engines)</option>
                            </select>
                            <div class="form-text fs-8 mt-2 text-muted">Setting tags to NoIndex can prevent duplicate content penalties.</div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Analytics -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark text-uppercase mb-4"><i class="bi bi-bar-chart-fill text-warning me-2"></i><?=_("Google Analytics");?></h6>
                        
                        <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1"><?=_("Measurement ID");?></label>
                        <input type="text" name="ga_id" class="form-control form-control-lg text-center bg-light border-0 font-monospace" value="<?=htmlspecialchars($o['ga_id']);?>" placeholder="G-XXXXXXXXXX">
                        <div class="form-text fs-8 mt-3 text-center">
                            Enter your GA4 Measurement ID to automatically inject the tracking script into your site's header. SeoNix handles the code injection for you.
                        </div>
                    </div>
                </div>

                <!-- Section 5: Facebook Pixel -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark text-uppercase mb-4"><i class="bi bi-meta text-primary me-2"></i><?=_("Facebook Pixel");?></h6>
                        
                        <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1"><?=_("Pixel ID");?></label>
                        <input type="text" name="fb_pixel_id" class="form-control form-control-lg text-center bg-light border-0 font-monospace" value="<?=htmlspecialchars($o['fb_pixel_id']);?>" placeholder="XXXXXXXXXXXXXXX">
                        <div class="form-text fs-8 mt-3 text-center">
                            Enter your Meta (Facebook) Pixel ID to track conversions and build targeted audiences. SeoNix handles the base snippet injection for you.
                        </div>
                    </div>
                </div>

                <!-- Section 6: IndexNow Protocol -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-lightning-charge-fill text-warning fs-3 me-2"></i>
                            <h6 class="fw-bold text-dark text-uppercase mb-0"><?=_("IndexNow Pinger");?></h6>
                        </div>
                        
                        <div class="bg-light rounded-4 p-3 border border-light shadow-none mb-3">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="indexnow_enable" id="inEnable" <?=$o['indexnow_enable'] == 'yes' ? 'checked' : '';?>>
                                <label class="form-check-label fw-bold ms-2" for="inEnable">
                                    Enable Instant Auto-Indexing
                                </label>
                            </div>
                        </div>

                        <?php 
                        $in_key = current(explode('-', current(explode('{', current(explode('(', sprintf('%04x%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535))))))));
                        $in_key = $o['indexnow_key'] ?: $in_key; 
                        ?>
                        <label class="form-label fw-bold small text-muted text-uppercase letter-spacing-1"><?=_("IndexNow Key");?></label>
                        <input type="text" name="indexnow_key" class="form-control bg-light border-0 font-monospace text-center" value="<?=htmlspecialchars($in_key);?>" placeholder="Enter or generate key">
                        <div class="form-text fs-8 mt-2">
                            When enabled, GeniXCMS will instantly ping search engines whenever a post is published, updated, or deleted. 
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-shield-check display-4 mb-3 d-block" style="color: #3b82f6;"></i>
                        <h6 class="fw-bold text-white mb-2">Ready to Boost?</h6>
                        <p class="small text-white-50 mb-4">Save changes to apply new SEO parameters globally.</p>
                        
                        <input type="hidden" name="token" value="<?=TOKEN;?>">
                        <button type="submit" name="seonix_save" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill text-white shadow-sm border-0">
                            <i class="bi bi-save2 me-2"></i> Update Settings
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>
