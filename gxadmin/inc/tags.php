<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>

<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data);?>
</div>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0"><?=_("Content Classification");?></h3>
            <p class="text-muted small"><?=_("Manage and discover tags used across your posts.");?></p>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addTagModal">
                <i class="bi bi-tag-fill me-1"></i> <?=_("New Tag");?>
            </button>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-pill px-4 py-2 bg-white d-inline-flex flex-row align-items-center">
                <span class="badge bg-primary bg-opacity-10 text-primary me-2"><?=Stats::totalCat('tag');?></span>
                <span class="text-muted small fw-bold text-uppercase"><?=_("Distinct Tags");?></span>
            </div>
        </div>
    </div>

    <!-- Tags Grid -->
    <div class="row g-3">
        <?php
        if ($data['num'] > 0):
            foreach ($data['cat'] as $c):
                if ($c->parent == '' || $c->parent == 0):
        ?>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 tag-card overflow-hidden">
                <div class="card-body p-3">
                    <form action="index.php?page=tags" method="POST" class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm rounded-pill overflow-hidden bg-light border-0">
                            <span class="input-group-text bg-transparent border-0 pe-0">
                                <i class="bi bi-hash text-muted"></i>
                            </span>
                            <input type="text" name="cat" class="form-control border-0 bg-transparent ps-2 font-monospace fw-bold" value="<?=$c->name;?>">
                            <input type="hidden" name="id" value="<?=$c->id;?>">
                            <input type="hidden" name="token" value="<?=TOKEN;?>">
                            <button class="btn btn-light border-0 text-primary px-3" type="submit" name="updatecat">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                        <a href="?page=tags&act=del&id=<?=$c->id;?>&token=<?=TOKEN;?>" 
                           class="btn btn-light btn-sm rounded-circle text-danger border-0"
                           onclick="return confirm('<?=_("Are you sure you want to delete this tag?");?>');">
                            <i class="bi bi-trash"></i>
                        </a>
                    </form>
                </div>
            </div>
        </div>
        <?php
                endif;
            endforeach;
        else:
        ?>
        <div class="col-12 text-center py-5">
            <div class="bg-white shadow-sm rounded-4 p-5 d-inline-block">
                <i class="bi bi-tags fs-1 text-muted opacity-25 d-block mb-3"></i>
                <h5 class="text-dark fw-bold"><?=_("No Tags Indexed");?></h5>
                <p class="text-muted mb-0"><?=_("Start classifying your content with tags.");?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <form action="index.php?page=tags" method="post">
                <div class="modal-header border-0 bg-light py-4 px-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-tag-fill me-2 text-primary"></i><?=_("Register New Classification");?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Label Name");?></label>
                        <div class="input-group rounded-3 overflow-hidden bg-light">
                            <span class="input-group-text bg-transparent border-0 text-muted ps-3">#</span>
                            <input type="text" name="cat" class="form-control border-0 bg-light ps-0" placeholder="e.g. artificial-intelligence, gaming, coffee" required>
                        </div>
                        <div class="form-text small opacity-75 mt-2">
                            <?=_("Tags help users find related content through a flat hierarchy.");?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 text-md-end">
                    <input type="hidden" name="token" value="<?=TOKEN;?>">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal"><?=_("Cancel");?></button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" name="addcat"><?=_("Confirm Label");?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tag-card { transition: all 0.2s ease; border: 1px solid transparent !important; }
    .tag-card:hover { transform: translateY(-2px); border-color: rgba(59, 130, 246, 0.2) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important; }
    .tag-card input:focus { caret-color: var(--gx-primary); }
    .font-monospace { letter-spacing: -0.5px; }
</style>
