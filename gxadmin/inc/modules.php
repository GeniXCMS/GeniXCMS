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
            <h3 class="fw-bold text-dark mb-0"><?=_("Plugin Ecosystem");?></h3>
            <p class="text-muted small mb-0"><?=_("Extend core features and add new capabilities with modular extensions.");?></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                <i class="bi bi-plus-circle-fill me-1"></i> <?=_("Install New Module");?>
            </button>
        </div>
    </div>

    <!-- Modules Controller Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-muted extra-small text-uppercase tracking-wider">
                <i class="bi bi-cpu me-2 text-primary"></i><?=_("Installed Extensions");?>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted extra-small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3"><?=_("Extension Identity");?></th>
                            <th><?=_("Capability / Description");?></th>
                            <th class="text-end pe-4" style="width: 250px;"><?=_("Operational Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($data['mods']) > 0):
                            foreach ($data['mods'] as $mod):
                                $m = Mod::data($mod);
                                $isActive = Mod::isActive($mod);
                                $statusClass = $isActive ? 'success' : 'secondary';
                                $btnClass = $isActive ? 'warning' : 'success';
                                $actLabel = $isActive ? _("Deactivate") : _("Activate");
                                $actIcon = $isActive ? 'bi-toggle-on' : 'bi-toggle-off';
                                $actUri = $isActive ? 'deactivate' : 'activate';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-<?=$statusClass;?> bg-opacity-10 p-3 rounded-3 text-<?=$statusClass;?> me-3">
                                        <i class="<?=(isset($m['icon'])?$m['icon']:'bi bi-box-seam');?> fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark h6 mb-1"><?=$m['name'];?></div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge bg-light text-muted border extra-small px-2 py-1 rounded-pill">v<?=$m['version'];?></span>
                                            <small class="text-muted extra-small"><?=_("License");?>: <?=$m['license'];?></small>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-muted small mb-1" title="<?=$m['desc'];?>">
                                    <?=strlen($m['desc']) > 120 ? substr($m['desc'], 0, 120).'...' : $m['desc'];?>
                                </p>
                                <div class="extra-small text-muted">
                                    <i class="bi bi-person-circle me-1"></i> <?=_("Author");?>: <a href="<?=$m['url'];?>" target="_blank" class="text-decoration-none text-primary"><?=$m['developer'];?></a>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group gap-2">
                                    <a href="index.php?page=modules&act=<?=$actUri;?>&modules=<?=$mod;?>&token=<?=TOKEN;?>" 
                                       class="btn btn-<?=$btnClass;?> btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                                        <i class="bi <?=$actIcon;?> me-2"></i> <?=$actLabel;?>
                                    </a>
                                    
                                    <?php if (!$isActive): ?>
                                    <a href="index.php?page=modules&act=remove&modules=<?=$mod;?>&token=<?=TOKEN;?>" 
                                       class="btn btn-light btn-sm rounded-circle border p-2" 
                                       onclick="return confirm('<?=_("Permanent removal of this module?");?>');" title="Remove Module">
                                        <i class="bi bi-trash text-danger"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="opacity-25 mb-3"><i class="bi bi-puzzle fs-1"></i></div>
                                <p class="text-muted mb-0"><?=_("No modular extensions found in your library.");?></p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3 px-4">
            <div class="extra-small text-muted text-uppercase tracking-widest fw-bold">
                <i class="bi bi-info-circle-fill me-2 text-primary"></i><?=_("Activated modules may add new menu items to your sidebar.");?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="index.php?page=modules" method="post" enctype="multipart/form-data">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark m-0"><?=_("Deploy New Module");?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="upload-zone border-2 border-dashed rounded-4 p-5 mb-3 bg-light cursor-pointer">
                        <i class="bi bi-plugin text-success fs-1 mb-3 d-block"></i>
                        <h6 class="fw-bold text-dark"><?=_("Upload Package");?></h6>
                        <p class="extra-small text-muted mb-4"><?=_("Select the module .zip archive to install");?></p>
                        <input type="file" name="module" class="form-control border-0 bg-white rounded-pill px-4 py-2 border shadow-sm">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?=_("Cancel");?></button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" name="upload"><?=_("Install Module");?></button>
                </div>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
            </form>
        </div>
    </div>
</div>

<style>
    .upload-zone { border-style: dashed !important; border-color: #dee2e6 !important; transition: all 0.2s; }
    .upload-zone:hover { background-color: #f1f3f5 !important; border-color: #0d6efd !important; }
</style>
