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
        <div class="col-md-6 text-start">
            <h3 class="fw-bold text-dark mb-0"><?=_("Navigation Architect");?></h3>
            <p class="text-muted small mb-0"><?=_("Define and organize your site's structural navigation menus.");?></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                <i class="bi bi-node-plus-fill me-1"></i> <?=_("Create New Menu");?>
            </button>
        </div>
    </div>

    <!-- Menus Accordion -->
    <div class="row">
        <div class="col-12">
            <div class="accordion custom-accordion shadow-sm rounded-4 overflow-hidden" id="menuArchitect">
                <?php
                if (isset($data['menus']) && $data['menus'] != '') {
                    $menus = json_decode(Typo::Xclean($data['menus']), true);
                    $first = true;
                    foreach ($menus as $k => $m) {
                        $collapseId = "collapse-" . $k;
                        $headerId = "heading-" . $k;
                        $show = $first ? 'show' : '';
                        $btnCollapsed = $first ? '' : 'collapsed';
                ?>
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="<?=$headerId;?>">
                        <button class="accordion-button <?=$btnCollapsed;?> bg-white py-4 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#<?=$collapseId;?>" aria-expanded="<?=($first?'true':'false');?>" aria-controls="<?=$collapseId;?>">
                            <div class="d-flex align-items-center w-100">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-3">
                                    <i class="bi bi-list fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark h5 mb-0"><?=$m['name'];?></div>
                                    <div class="extra-small text-muted fw-bold text-uppercase tracking-wider">ID: <?=$k;?></div>
                                </div>
                                <div class="me-3">
                                    <a href="index.php?page=menus&act=remove&menuid=<?=$k;?>&token=<?=TOKEN;?>" 
                                       class="btn btn-light btn-sm rounded-pill px-3 border" 
                                       onclick="return confirm('<?=_("Permanent removal of this entire menu?");?>');">
                                        <i class="bi bi-trash text-danger me-1"></i> <span class="extra-small fw-bold text-uppercase"><?=_("Delete");?></span>
                                    </a>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="<?=$collapseId;?>" class="accordion-collapse collapse <?=$show;?>" aria-labelledby="<?=$headerId;?>" data-bs-parent="#menuArchitect">
                        <div class="accordion-body bg-light bg-opacity-50 p-4">
                            <!-- Inner Tabs -->
                            <ul class="nav nav-pills custom-pills mb-4" id="<?=$k;?>-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active rounded-pill px-4 me-2" id="<?=$k;?>-menuitem-tab" data-bs-toggle="pill" href="#<?=$k;?>-menuitem" role="tab"><?=_("Menu Structure");?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded-pill px-4" id="<?=$k;?>-additem-tab" data-bs-toggle="pill" href="#<?=$k;?>-additem" role="tab"><?=_("Add Link / Item");?></a>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="<?=$k;?>-tabContent">
                                <div class="tab-pane fade show active" id="<?=$k;?>-menuitem" role="tabpanel">
                                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                                        <?php echo Menus::getMenuAdmin($k, ''); ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="<?=$k;?>-additem" role="tabpanel">
                                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                                        <?php
                                            $data['parent'] = Menus::isHadParent('', $k);
                                            $data['menuid'] = $k;
                                            System::inc('menus_form', $data);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    $first = false;
                    } 
                } else { ?>
                <div class="p-5 text-center bg-white">
                    <div class="opacity-25 mb-3"><i class="bi bi-node-minus fs-1"></i></div>
                    <p class="text-muted mb-0"><?=_("No navigation menus created yet.");?></p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="index.php?page=menus" method="post">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark m-0"><?=_("Architect New Menu");?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Menu Registry ID");?></label>
                        <input type="text" name="id" class="form-control border-0 bg-light rounded-3 py-2 px-3" placeholder="e.g. main-nav" required>
                        <div class="extra-small text-muted mt-1 ps-1"><?=_("Technical identifier used in theme templates.");?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Friendly Name");?></label>
                        <input type="text" name="name" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Custom CSS Class");?></label>
                        <input type="text" name="class" class="form-control border-0 bg-light rounded-3 py-2 px-3" placeholder="e.g. navbar-nav">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?=_("Cancel");?></button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" name="submit"><?=_("Initialize Menu");?></button>
                </div>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
            </form>
        </div>
    </div>
</div>

<style>
    .custom-accordion .accordion-button:not(.collapsed) { background-color: #fff; box-shadow: none; color: inherit; }
    .custom-accordion .accordion-button:focus { box-shadow: none; }
    .custom-accordion .accordion-button::after { background-size: 1rem; }
    
    .custom-pills .nav-link { color: #6c757d; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid transparent; }
    .custom-pills .nav-link.active { background-color: #0d6efd; color: #fff; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    .custom-pills .nav-link:not(.active):hover { background-color: #f8f9fa; border-color: #dee2e6; }
</style>

