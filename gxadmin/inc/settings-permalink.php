<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>
<form action="index.php?page=settings-permalink" method="post">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?=_("Clean URLs");?></h3>
                <p class="text-muted small mb-0"><?=_("Configure permalink structures and internal routing protocols.");?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button type="submit" name="change" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> <?=_("Apply Routes");?>
                    </button>
                    <button type="reset" class="btn btn-light border rounded-pill px-4">
                        <?=_("Discard");?>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h6 class="fw-bold text-danger text-uppercase mb-4"><?=_("Routing Configuration");?></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch bg-light rounded-4 p-3 ps-5 border-start border-4 border-danger shadow-none mb-3">
                                    <input class="form-check-input" type="checkbox" name="permalink_use_index_php" id="useIndex" <?= ($data['permalink_use_index_php'] === 'on') ? 'checked' : ''; ?>>
                                    <label class="form-check-label ps-2" for="useIndex">
                                        <div class="fw-bold text-dark"><?=_("Legacy Compatibility (index.php)");?></div>
                                        <div class="extra-small text-muted"><?=_("Adds index.php to the URL if the server doesn't support clean rewrites natively.");?></div>
                                    </label>
                                </div>
                                <div class="alert alert-info border-0 rounded-4 py-3 px-4 shadow-none">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-info-circle fs-4 text-primary"></i>
                                        <div class="extra-small lh-sm">
                                            <?=_("Standard Clean URLs (Off) are recommended for better SEO and aesthetics. Ensure your .htaccess or Nginx config is correctly set up.");?>
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
