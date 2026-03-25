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
            <h3 class="fw-bold text-dark mb-0"><?=_("Static Pages");?></h3>
            <p class="text-muted small"><?=_("Manage permanent site structure and informational pages.");?></p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="index.php?page=pages&act=add&token=<?=TOKEN;?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-journal-plus me-1"></i> <?=_("Create New Page");?>
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary me-3">
                    <i class="bi bi-file-earmark-text fs-4"></i>
                </div>
                <div>
                    <div class="text-muted extra-small fw-bold text-uppercase"><?=_("Total Library");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::totalPost('page');?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                    <i class="bi bi-cloud-check fs-4"></i>
                </div>
                <div>
                    <div class="text-muted extra-small fw-bold text-uppercase"><?=_("Live Pages");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::activePost('page');?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger me-3">
                    <i class="bi bi-archive fs-4"></i>
                </div>
                <div>
                    <div class="text-muted extra-small fw-bold text-uppercase"><?=_("Archived/Draft");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::inactivePost('page');?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-4 px-4">
            <form action="index.php?page=pages" method="get" class="row g-3">
                <input type="hidden" name="page" value="pages">
                <input type="hidden" name="token" value="<?=TOKEN;?>">
                
                <div class="col-lg-4 col-md-6">
                    <div class="input-group border rounded-pill overflow-hidden bg-light">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control bg-transparent border-0 ps-2" placeholder="<?=_("Locate page by title...");?>">
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <select name="status" class="form-select border rounded-pill bg-light ps-3">
                        <option value="1"><?=_("Published");?></option>
                        <option value="0"><?=_("Draft");?></option>
                    </select>
                </div>

                <div class="col-lg-4 col-md-8">
                    <div class="input-group">
                        <input type="date" name="from" class="form-control border rounded-start-pill bg-light ps-3" placeholder="<?=_("From");?>">
                        <input type="date" name="to" class="form-control border rounded-end-pill bg-light ps-3" placeholder="<?=_("To");?>">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 px-3">
                        <i class="bi bi-filter me-1"></i> <?=_("Filter Results");?>
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <form action="" method="post">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted extra-small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 60px;"><?=_("ID");?></th>
                                <th><?=_("Information Architecture");?></th>
                                <th class="text-center"><?=_("Timeline");?></th>
                                <th class="text-center"><?=_("Accountability");?></th>
                                <th class="text-end pe-4" style="width: 140px;"><?=_("Interaction");?></th>
                                <th class="text-center pe-4" style="width: 50px;">
                                    <input type="checkbox" id="selectall" class="form-check-input">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $username = Session::val('username');
                            $group = Session::val('group');
                            if ($data['num'] > 0):
                                foreach ($data['posts'] as $p):
                                    $accessEdit = $group <= 2 ? 1: ($p->author == $username ? 1 : 0);
                                    $accessDelete = $group < 2 ? 1: 0;
                                    
                                    if ($p->status == '0') {
                                        $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning px-3 rounded-pill fw-medium">'._("Draft").'</span>';
                                        $rowStyle = 'background-color: rgba(255, 193, 7, 0.01);';
                                    } else {
                                        $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill fw-medium">'._("Live").'</span>';
                                        $rowStyle = '';
                                    }
                            ?>
                            <tr style="<?=$rowStyle;?>">
                                <td class="ps-4 text-muted small"><?=$p->id;?></td>
                                <td>
                                    <a href="<?=Url::page($p->id);?>" target="_blank" class="fw-bold text-dark text-decoration-none d-block mb-1">
                                        <?=$p->title;?>
                                    </a>
                                    <?=$statusBadge;?>
                                </td>
                                <td class="text-center">
                                    <div class="small fw-semibold text-dark"><?=Date::format($p->date, 'd M Y');?></div>
                                    <div class="text-muted extra-small"><?=Date::format($p->date, 'H:i A');?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center px-2 py-1 bg-light rounded-pill">
                                        <i class="bi bi-person-circle text-muted me-2"></i>
                                        <span class="small text-dark fw-medium"><?=$p->author;?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <?php if($accessEdit): ?>
                                        <a href="index.php?page=pages&act=edit&id=<?=$p->id;?>&token=<?=TOKEN;?>" class="btn btn-light btn-sm rounded-circle me-1 border" title="<?=_("Edit Metadata");?>">
                                            <i class="bi bi-pencil-square text-success"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if($accessDelete): ?>
                                        <a href="index.php?page=pages&act=del&id=<?=$p->id;?>&token=<?=TOKEN;?>" 
                                           class="btn btn-light btn-sm rounded-circle border" 
                                           onclick="return confirm('<?=_("Are you sure you want to delete this page permanently?");?>');" title="<?=_("Remove Page");?>">
                                            <i class="bi bi-trash text-danger"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <input type="checkbox" name="post_id[]" value="<?=$p->id;?>" class="check form-check-input">
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="opacity-25 mb-3"><i class="bi bi-journal-x fs-1"></i></div>
                                    <p class="text-muted mb-0"><?=_("Your page library is currently empty.");?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <?php if(User::access(2)): ?>
                        <select name="action" class="form-select form-select-sm rounded-pill bg-light" style="width: 160px;">
                            <option value="publish"><?=_("Batch Publish");?></option>
                            <option value="unpublish"><?=_("Batch Unpublish");?></option>
                            <option value="delete"><?=_("Batch Delete");?></option>
                        </select>
                        <button type="submit" name="doaction" class="btn btn-danger btn-sm rounded-pill px-4">
                            <i class="bi bi-lightning-fill me-1"></i> <?=_("Start Action");?>
                        </button>
                        <?php endif; ?>
                        <input type="hidden" name="token" value="<?=TOKEN;?>">
                    </div>
                    <div class="pagination-container">
                        <?=$data['paging'];?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .table-hover tbody tr:hover { background-color: rgba(59, 130, 246, 0.02) !important; cursor: pointer; }
    .btn-group .btn { transition: all 0.2s; }
    .btn-group .btn:hover { background-color: #f1f5f9; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .pagination-container .pagination { margin: 0; }
    .pagination-container .page-link { border: 0; background: #f8fafc; color: #64748b; border-radius: 8px !important; margin: 0 3px; font-weight: 500; font-size: 0.85rem; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; }
    .pagination-container .page-item.active .page-link { background: var(--gx-primary); color: #fff; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); }
</style>