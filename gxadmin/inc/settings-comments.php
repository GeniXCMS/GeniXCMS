<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>
<form action="index.php?page=settings-comments" method="post">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?=_("Engagement Settings");?></h3>
                <p class="text-muted small mb-0"><?=_("Manage user interactions, comment moderation, and anti-spam protocols.");?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button type="submit" name="change" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> <?=_("Save Engagement");?>
                    </button>
                    <button type="reset" class="btn btn-light border rounded-pill px-4">
                        <?=_("Discard");?>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h6 class="fw-bold text-success text-uppercase mb-4"><?=_("Core Controls");?></h6>
                        <div class="row g-4">
                            <div class="col-md-7">
                                <div class="form-check form-switch bg-light rounded-4 p-3 ps-5 border-start border-4 border-success shadow-none mb-4">
                                    <input class="form-check-input" type="checkbox" name="comments_enable" id="enableComm" <?= ($data['comments_enable'] === 'on') ? 'checked' : ''; ?>>
                                    <label class="form-check-label ps-2" for="enableComm">
                                        <div class="fw-bold text-dark"><?=_("Active Comment Ecosystem");?></div>
                                        <div class="extra-small text-muted"><?=_("Users will be able to leave feedback on your published posts.");?></div>
                                    </label>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Pagination Threshold");?></label>
                                    <div class="input-group">
                                        <input type="number" name="comments_perpage" class="form-control border-0 bg-light rounded-start-3 py-2 shadow-none" value="<?=$data['comments_perpage'];?>">
                                        <span class="input-group-text border-0 bg-light rounded-end-3 py-1 opacity-50 px-3 fs-7"><?=_("comments/page");?></span>
                                    </div>
                                    <div class="extra-small text-muted mt-1 ps-1"><?=_("Defines how many entries load before splitting into pages.");?></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="bg-light border-0 rounded-4 p-4 h-100">
                                    <h6 class="fw-bold fs-7 text-dark mb-3"><i class="bi bi-shield-check me-2 text-primary"></i><?=_("Anti-Spam Filter");?></h6>
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2"><?=_("Blacklist Registry");?></label>
                                    <textarea class="form-control border-0 bg-white rounded-3 shadow-none p-3" name="spamwords" rows="5" style="font-family: monospace; font-size: 0.85rem;" placeholder="bad-word-1&#10;bad-word-2"><?= $data['spamwords']; ?></textarea>
                                    <div class="extra-small text-muted mt-2 lh-sm ps-1">
                                        <?=_("Place one restricted term per line. Comments containing these will be automatically flagged.");?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="alert alert-primary border-0 rounded-4 shadow-sm p-4 h-100">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i><?=_("Moderation Tip");?></h6>
                    <ul class="extra-small text-dark opacity-75 d-flex flex-column gap-3 ps-3">
                        <li><?=_("Lower the threshold for intense discussions to keep the page load speed high.");?></li>
                        <li><?=_("Update your blacklist regularly to maintain a healthy community environment.");?></li>
                        <li><?=_("Consider integrating third-party tools for advanced spam detection if volume scales.");?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?=TOKEN;?>">
</form>
