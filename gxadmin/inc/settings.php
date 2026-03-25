<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>
<form action="" method="POST" enctype="multipart/form-data">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?=_("Global Settings");?></h3>
                <p class="text-muted small mb-0"><?=_("Control the core identity, localization, and functional parameters of your ecosystem.");?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button type="submit" name="change" class="btn btn-primary rounded-pill px-4 shadow-sm" value="Change">
                        <i class="bi bi-save me-1"></i> <?=_("Save Architecture");?>
                    </button>
                    <button type="reset" class="btn btn-light border rounded-pill px-4">
                        <?=_("Discard Changes");?>
                    </button>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" id="settingsContainer">
            <div class="card-header bg-light border-0 p-2">
                <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto scrollbar-hide px-2 pt-2 pb-2" id="settingsTab" role="tablist" style="white-space: nowrap;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab"><?=_("General");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="localization-tab" data-bs-toggle="tab" data-bs-target="#localization" type="button" role="tab"><?=_("Localization");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab"><?=_("E-Mail");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab"><?=_("Social");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="logo-tab" data-bs-toggle="tab" data-bs-target="#logo" type="button" role="tab"><?=_("Identity");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="library-tab" data-bs-toggle="tab" data-bs-target="#library" type="button" role="tab"><?=_("Assets");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab"><?=_("Posts");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab"><?=_("Payment");?></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill small fw-bold py-2 px-4 shadow-none m-1" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab"><?=_("Security");?></button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4 pt-5">
                <div class="tab-content" id="settingsTabContent">
                    <!-- General -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-primary ps-3 h6"><?=_("Platform Identity");?></h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Website Name");?></label>
                                <input type="text" name="sitename" value="<?=Options::v('sitename');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Website Slogan");?></label>
                                <input type="text" name="siteslogan" value="<?=Options::v('siteslogan');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Domain Authority");?></label>
                                <input type="text" name="sitedomain" value="<?=Options::v('sitedomain');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" placeholder="<?=_("example.org");?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Base URL Path");?></label>
                                <input type="text" name="siteurl" value="<?=Options::v('siteurl');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" placeholder="<?=_("http://www.example.org/");?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("SEO Metadata Keywords");?></label>
                                <input type="text" name="sitekeywords" value="<?=Options::v('sitekeywords');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("SEO Meta Description");?></label>
                                <textarea name="sitedesc" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" rows="3"><?=Options::v('sitedesc');?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Administrative E-mail");?></label>
                                <input type="email" name="siteemail" value="<?=Options::v('siteemail');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                            </div>
                        </div>
                    </div>

                    <!-- Localization -->
                    <div class="tab-pane fade" id="localization" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-info ps-3 h6"><?=_("Regional Settings");?></h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Country Origin");?></label>
                                <select name="country_id" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                    <?=Date::optCountry(Options::v('country_id'));?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Chronological Timezone");?></label>
                                <select name="timezone" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                    <?=Date::optTimeZone(Options::v('timezone'));?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Default System Language");?></label>
                                <select name="system_lang" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                    <?=Language::optDropdown(Options::v('system_lang'));?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Character Encoding");?></label>
                                <input name="charset" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" value="<?=Options::v('charset');?>" placeholder="UTF-8">
                            </div>
                        </div>
                    </div>

                    <!-- E-Mail -->
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-success ps-3 h6"><?=_("Mail Delivery Pipeline");?></h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Transport Layer");?></label>
                                <select name="mailtype" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                    <option value="0" <?= (Options::v('mailtype') == 0) ? 'selected' : ''; ?>><?=_("Native PHP Mail");?></option>
                                    <option value="1" <?= (Options::v('mailtype') == 1) ? 'selected' : ''; ?>><?=_("SMTP Protocol");?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("SMTP Gateway Port");?></label>
                                <input type="text" name="smtpport" value="<?=Options::v('smtpport');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" placeholder="<?=_("587");?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("SMTP Server Hostname");?></label>
                                <input type="text" name="smtphost" value="<?=Options::v('smtphost');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" placeholder="<?=_("smtp.example.org");?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("SMTP Authentication Username");?></label>
                                <input type="text" name="smtpuser" value="<?=Options::v('smtpuser');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("SMTP Encryption Password");?></label>
                                <input type="password" name="smtppass" value="<?=Options::v('smtppass');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                            </div>
                        </div>
                    </div>

                    <!-- Social -->
                    <div class="tab-pane fade" id="social" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-warning ps-3 h6"><?=_("Connected Networks");?></h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Facebook Profile Link");?></label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light ps-3 pe-0 text-muted"><i class="bi bi-facebook"></i></span>
                                    <input type="text" name="fbacc" value="<?=Options::v('fbacc');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Facebook Business Page");?></label>
                                <input type="text" name="fbpage" value="<?=Options::v('fbpage');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("X / Twitter Handle");?></label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light ps-3 pe-0 text-muted">@</span>
                                    <input type="text" name="twitter" value="<?=Options::v('twitter');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("LinkedIn Professional Profile");?></label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light ps-3 pe-0 text-muted"><i class="bi bi-linkedin"></i></span>
                                    <input type="text" name="linkedin" value="<?=Options::v('linkedin');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Identity -->
                    <div class="tab-pane fade" id="logo" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-danger ps-3 h6"><?=_("Graphic Identity");?></h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Primary Brand Logo");?></label>
                                <div class="bg-light rounded-4 p-4 text-center mb-3">
                                    <?php
                                    $is_logourl = Options::v('is_logourl');
                                    $logourl = Options::v('logourl');
                                    $logo = Options::v('logo');
                                    $logoimg = ($is_logourl == 'on' && $logourl != '') ? $logourl : (($logo != '') ? Site::$url.$logo : '');
                                    ?>
                                    <div class="logo_preview p-3 mb-3 cursor-pointer d-inline-block shadow-none rounded bg-white" id="fileBrowse" onclick="uploadLogo()">
                                        <?php if($logoimg): ?>
                                            <img src="<?=$logoimg;?>" class="img-fluid" id="logo_preview" style="max-height: 120px;">
                                        <?php else: ?>
                                            <div class="py-5 px-5"><i class="bi bi-image fs-1 text-muted opacity-25"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" id="ImageBrowse" name="file" hidden>
                                    <input type="hidden" name="logo" id="logo_image" value="<?=$logo;?>">
                                    <div class="extra-small text-muted"><?=_("Recommended height: 80-120px. PNG or SVG preferred.");?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light rounded-4 p-4 h-100">
                                    <div class="mb-4">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_logourl" id="useLogoUrl" <?= (Options::v('is_logourl') == 'on') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold" for="useLogoUrl"><?=_("External Logo Storage");?></label>
                                        </div>
                                        <input type="text" name="logourl" value="<?=Options::v('logourl');?>" class="form-control border-0 bg-white rounded-3 shadow-none mt-2" placeholder="<?=_("https://cdn.example.com/logo.png");?>">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Browser Favicon Path");?></label>
                                        <input type="text" name="siteicon" value="<?=Options::v('siteicon');?>" class="form-control border-0 bg-white rounded-3 shadow-none" placeholder="favicon.ico">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Library Assets -->
                    <div class="tab-pane fade" id="library" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-dark ps-3 h6"><?=_("External Libraries & CDNs");?></h6>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Public CDN Repository");?></label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light px-3 text-muted"><i class="bi bi-hdd-network"></i></span>
                                    <input type="text" name="cdn_url" class="form-control border-0 bg-light rounded-end-3 py-2 shadow-none" value="<?=Options::v('cdn_url');?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-0 bg-light rounded-4 p-3 h-100">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="use_jquery" id="useJquery" <?= (Options::v('use_jquery') == 'on') ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="useJquery"><?=_("jQuery Framework");?></label>
                                    </div>
                                    <input type="text" name="jquery_v" class="form-control border-0 bg-white rounded-3 py-1 shadow-none" value="<?=Options::v('jquery_v');?>" placeholder="<?=_("Version (e.g. 1.12.0)");?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light rounded-4 p-3 h-100 opacity-75">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="use_bootstrap" id="useBS" checked disabled>
                                        <label class="form-check-label fw-bold" for="useBS"><?=_("Bootstrap UI Toolkit");?></label>
                                    </div>
                                    <input type="text" name="bs_v" class="form-control border-0 bg-white rounded-3 py-1 shadow-none" value="<?=Options::v('bs_v');?>" readonly>
                                    <div class="extra-small text-muted mt-1 fw-bold"><?=_("Core System Dependency.");?></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light rounded-4 p-3 h-100">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="use_fontawesome" id="useFA" <?= (Options::v('use_fontawesome') == 'on') ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="useFA"><?=_("FontAwesome Icons");?></label>
                                    </div>
                                    <input type="text" name="fontawesome_v" class="form-control border-0 bg-white rounded-3 py-1 shadow-none" value="<?=Options::v('fontawesome_v');?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light rounded-4 p-3 h-100">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="use_editor" id="useEditor" <?= (Options::v('use_editor') == 'on') ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="useEditor"><?=_("Visual Content Editor");?></label>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <input type="text" name="editor_v" class="form-control border-0 bg-white rounded-3 py-1 shadow-none" value="<?=Options::v('editor_v');?>">
                                        <select name="editor_type" class="form-select border-0 bg-white rounded-3 py-1 shadow-none fs-8 fw-bold">
                                            <option value="summernote" selected><?=_("Summernote");?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Posts -->
                    <div class="tab-pane fade" id="posts" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-info ps-3 h6"><?=_("Content Architectural Controls");?></h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Article Pagination Density");?></label>
                                <input type="number" name="post_perpage" value="<?=Options::v('post_perpage');?>" class="form-control border-0 bg-light rounded-3 py-2 shadow-none" min='1'>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Pagination Styling");?></label>
                                <select name="pagination" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                    <option value="number" <?= (Options::v('pagination') == 'number') ? 'selected' : ''; ?>><?=_("Numeric [1, 2, 3]");?></option>
                                    <option value="pager" <?= (Options::v('pagination') == 'pager') ? 'selected' : ''; ?>><?=_("Classic [Prev / Next]");?></option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <div class="card border-0 bg-light rounded-4 p-4">
                                    <div class="row g-4">
                                        <div class="col-md-5">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="pinger_enable" id="enablePing" <?= (Options::v('pinger_enable') == 'on') ? 'checked' : ''; ?>>
                                                <label class="form-check-label fw-bold" for="enablePing"><?=_("Search Engine Pinger");?></label>
                                            </div>
                                            <div class="extra-small text-muted lh-sm"><?=_("Automatically notify global indexers when fresh content is published. Uses standard XML-RPC protocols.");?></div>
                                        </div>
                                        <div class="col-md-7">
                                             <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2"><?=_("Pinger Registry (URLs)");?></label>
                                             <textarea name="pinger" class="form-control border-0 bg-white rounded-3 shadow-none p-3 fs-8" rows="6" style="font-family: monospace;"><?=Options::v('pinger');?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="tab-pane fade" id="payment" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-success ps-3 h6"><?=_("Transaction Gateways");?></h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Platform Currency");?></label>
                                <?php $curr = Options::v('currency'); ?>
                                <select class="form-select border-0 bg-light rounded-3 py-2 shadow-none" name="currency">
                                    <option value="USD" <?= ($curr == 'USD') ? 'selected' : ''; ?>>$ USD (<?=_("Dollar");?>)</option>
                                    <option value="EUR" <?= ($curr == 'EUR') ? 'selected' : ''; ?>>&euro; EUR (<?=_("Euro");?>)</option>
                                    <option value="GBP" <?= ($curr == 'GBP') ? 'selected' : ''; ?>>&pound; GBP (<?=_("Pound");?>)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="card border-start border-4 border-primary rounded-3 bg-light shadow-none p-3 border-top-0 border-end-0 border-bottom-0">
                                    <h6 class="fw-bold fs-7 mb-2 text-dark"><i class="bi bi-paypal me-2 text-primary"></i><?=_("PayPal Merchant Configuration");?></h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="ppsandbox" id="ppSand" <?= (Options::v('ppsandbox') == 'on') ? 'checked' : ''; ?>>
                                                <label class="form-check-label small fw-bold" for="ppSand"><?=_("Sandbox Mode");?></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase"><?=_("API Username / Merchant Email");?></label>
                                            <input type="text" name="ppuser" value="<?=Options::v('ppuser');?>" class="form-control form-control-sm border-0 bg-white shadow-none rounded-2">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase"><?=_("API Password");?></label>
                                            <input type="password" name="pppass" value="<?=Options::v('pppass');?>" class="form-control form-control-sm border-0 bg-white shadow-none rounded-2">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase"><?=_("API Signature");?></label>
                                            <input type="text" name="ppsign" value="<?=Options::v('ppsign');?>" class="form-control form-control-sm border-0 bg-white shadow-none rounded-2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <h6 class="fw-bold text-dark mb-4 border-start border-4 border-danger ps-3 h6"><?=_("Platform Fortification");?></h6>
                        <div class="card border-0 bg-light rounded-4 p-4">
                            <div class="row align-items-center g-4">
                                <div class="col-md-5 border-end border-2 border-white pe-md-4">
                                    <h6 class="fw-bold fs-7 text-dark mb-3"><i class="bi bi-shield-lock-fill me-2 text-danger"></i><?=_("Anti-Bot Defense (reCaptcha)");?></h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="google_captcha_enable" id="enableCap" <?= (Options::v('google_captcha_enable') == 'on') ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold h6 mb-0" for="enableCap"><?=_("Active Barrier");?></label>
                                    </div>
                                    <div class="extra-small text-muted lh-sm mb-3"><?=_("Prevents automated scripts from exploiting forms and registration pipelines.");?></div>
                                    <div class="alert alert-warning border-0 rounded-3 p-3 extra-small mb-0">
                                        <i class="bi bi-info-circle me-1"></i><?=_("Keys must matching the domain configured in General tab.");?>
                                    </div>
                                </div>
                                <div class="col-md-7 ps-md-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Component Locale");?></label>
                                            <input type="text" name="google_captcha_lang" value="<?=Options::v('google_captcha_lang');?>" class="form-control form-control-sm border-0 bg-white shadow-none rounded-2">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("System Site Key");?></label>
                                            <input type="text" name="google_captcha_sitekey" value="<?=Options::v('google_captcha_sitekey');?>" class="form-control form-control-sm border-0 bg-white shadow-none rounded-2">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Encryption Secret");?></label>
                                            <input type="password" name="google_captcha_secret" value="<?=Options::v('google_captcha_secret');?>" class="form-control form-control-sm border-0 bg-white shadow-none rounded-2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?=TOKEN;?>">
</form>

<script>
    $(document).ready(function (e) {
        $("#ImageBrowse").on("change", function() {
            var reader,
            input = document.getElementById('ImageBrowse'),
            preview = document.getElementById('logo_preview');

            if (input.files && input.files[0]) {
                reader = new FileReader();
                reader.onload = function(e) {
                    if(preview) {
                        preview.setAttribute('src', e.target.result);
                    } else {
                        $('#fileBrowse').html('<img src="'+e.target.result+'" class="img-fluid" id="logo_preview" style="max-height: 120px;">');
                    }
                    $.ajax({
                        type:'POST',
                        url: '<?=Url::ajax("saveimage");?>',
                        data: {file: e.target.result, file_name: input.files[0]['name']},
                        success:function(data){
                            data = JSON.parse(data);
                            $('#logo_image').val(data.path);
                        }
                    });
                }
                reader.readAsDataURL(input.files[0]);
            } 
        });
    });

    function uploadLogo() {
        var input = document.getElementById('ImageBrowse');
        input.click();    
    }
</script>
