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
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0"><?=_("Content Taxonomy");?></h3>
            <p class="text-muted small"><?=_("Organize your posts into hierarchical categories.");?></p>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="bi bi-folder-plus me-1"></i> <?=_("New Category");?>
            </button>
        </div>
    </div>

    <!-- Quick Stats Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-pill px-4 py-2 bg-white d-inline-flex flex-row align-items-center">
                <span class="badge bg-primary bg-opacity-10 text-primary me-2"><?=Stats::totalCat('post');?></span>
                <span class="text-muted small fw-bold text-uppercase"><?=_("Registered Categories");?></span>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row g-4">
        <?php
        if ($data['num'] > 0):
            foreach ($data['cat'] as $c):
                if ($c->parent == '' || $c->parent == 0):
        ?>
        <div class="col-12 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden category-card">
                <div class="card-header bg-light border-0 py-3 px-4">
                    <form action="index.php?page=categories" method="POST" class="d-flex gap-2">
                        <div class="input-group input-group-sm rounded-pill overflow-hidden bg-white border">
                            <a href="?page=categories&act=del&id=<?=$c->id;?>&token=<?=TOKEN;?>" 
                               class="btn btn-white border-0 text-danger px-2"
                               onclick="return confirm('<?=_("Are you sure you want to delete this category and all its children?");?>');">
                                <i class="bi bi-trash"></i>
                            </a>
                            <input type="text" name="cat" class="form-control border-0 ps-1" value="<?=$c->name;?>">
                            <input type="hidden" name="id" value="<?=$c->id;?>">
                            <input type="hidden" name="token" value="<?=TOKEN;?>">
                            <button class="btn btn-dark border-0 px-3" type="submit" name="updatecat"><?=_("Save");?></button>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php 
                        $hasChildren = false;
                        foreach ($data['cat'] as $c2):
                            if ($c2->parent == $c->id):
                                $hasChildren = true;
                        ?>
                        <li class="list-group-item border-0 py-2 px-4 bg-transparent border-bottom">
                            <form action="index.php?page=categories" method="POST" class="d-flex align-items-center">
                                <div class="input-group input-group-sm rounded-3 overflow-hidden bg-light border-0">
                                    <a href="?page=categories&act=del&id=<?=$c2->id;?>&token=<?=TOKEN;?>" 
                                       class="btn btn-light border-0 text-muted px-2"
                                       onclick="return confirm('<?=_("Delete this sub-category?");?>');">
                                        <i class="bi bi-dash-circle"></i>
                                    </a>
                                    <input type="text" name="cat" class="form-control border-0 bg-transparent ps-1 small" value="<?=$c2->name;?>">
                                    <input type="hidden" name="id" value="<?=$c2->id;?>">
                                    <input type="hidden" name="token" value="<?=TOKEN;?>">
                                    <button class="btn btn-light border-0 text-primary px-3" type="submit" name="updatecat"><i class="bi bi-check2"></i></button>
                                </div>
                            </form>
                        </li>
                        <?php 
                            endif;
                        endforeach; 
                        if(!$hasChildren):
                        ?>
                        <li class="list-group-item border-0 py-4 px-4 text-center text-muted opacity-50 small">
                            <i class="bi bi-collection-fill d-block mb-1 opacity-25"></i>
                            <?=_("No sub-categories");?>
                        </li>
                        <?php endif; ?>
                    </ul>
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
                <i class="bi bi-diagram-3 fs-1 text-muted opacity-25 d-block mb-3"></i>
                <h5 class="text-dark fw-bold"><?=_("Taxonomy Empty");?></h5>
                <p class="text-muted mb-0"><?=_("Start by creating your first primary category.");?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <form action="index.php?page=categories" method="post">
                <div class="modal-header border-0 bg-light py-4 px-4">
                    <h5 class="modal-title fw-bold" id="myModalLabel"><i class="bi bi-plus-circle me-2 text-primary"></i><?=_("Create Taxonomy Entry");?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Hierarchical Position");?></label>
                        <?php
                        $vars = [
                            'parent' => '0',
                            'name' => 'parent',
                            'sort' => 'ASC',
                            'order_by' => 'name',
                            'type' => 'post',
                            'class' => 'form-select border-0 bg-light rounded-3'
                        ];
                        echo Categories::dropdown($vars);
                        ?>
                        <div class="form-text small opacity-75 mt-1"><?=_("Select 'None' to create a root category.");?></div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Official Name");?></label>
                        <input type="text" name="cat" class="form-control border-0 bg-light rounded-3" placeholder="e.g. Technology, Lifestyle, News" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <input type="hidden" name="token" value="<?=TOKEN;?>">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal"><?=_("Cancel");?></button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" name="addcat"><?=_("Initialize Category");?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .category-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .category-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .input-group:focus-within { border-color: var(--gx-primary) !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .form-control:focus { box-shadow: none !important; }
    .list-group-item:last-child { border-bottom: 0 !important; }
</style>
