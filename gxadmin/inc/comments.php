<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
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
            <h3 class="fw-bold text-dark mb-0"><?=_("Comment Moderation");?></h3>
            <p class="text-muted small"><?=_("Review and manage user engagement across your content.");?></p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="card border-0 shadow-sm rounded-pill px-4 py-2 bg-white d-inline-flex flex-row align-items-center">
                <span class="badge bg-warning bg-opacity-10 text-warning me-2 fw-bold"><?=Stats::pendingComments();?></span>
                <span class="text-muted extra-small fw-bold text-uppercase"><?=_("Pending Approval");?></span>
            </div>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-2"><i class="bi bi-chat-dots fs-5"></i></div>
                    <span class="text-muted extra-small fw-bold text-uppercase"><?=_("Total Feed");?></span>
                </div>
                <div class="h4 fw-bold m-0"><?=Stats::totalComments();?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success me-2"><i class="bi bi-patch-check fs-5"></i></div>
                    <span class="text-muted extra-small fw-bold text-uppercase"><?=_("Active");?></span>
                </div>
                <div class="h4 fw-bold m-0"><?=Stats::activeComments();?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning me-2"><i class="bi bi-hourglass-split fs-5"></i></div>
                    <span class="text-muted extra-small fw-bold text-uppercase"><?=_("Pending");?></span>
                </div>
                <div class="h4 fw-bold m-0"><?=Stats::pendingComments();?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-white p-3 rounded-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger me-2"><i class="bi bi-shield-exclamation fs-5"></i></div>
                    <span class="text-muted extra-small fw-bold text-uppercase"><?=_("Spam/Blocked");?></span>
                </div>
                <div class="h4 fw-bold m-0"><?=Stats::inactiveComments();?></div>
            </div>
        </div>
    </div>

    <!-- Management Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-4 px-4">
            <form action="index.php?page=comments" method="get" class="row g-3">
                <input type="hidden" name="page" value="comments">
                <input type="hidden" name="token" value="<?=TOKEN;?>">
                
                <div class="col-lg-4">
                    <div class="input-group border rounded-pill overflow-hidden bg-light">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control bg-transparent border-0 ps-2" placeholder="<?=_("Keyword, email, or IP...");?>">
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select border rounded-pill bg-light ps-3">
                        <option value="1"><?=_("Published");?></option>
                        <option value="2"><?=_("Pending");?></option>
                        <option value="0"><?=_("Unpublished");?></option>
                    </select>
                </div>

                <div class="col-lg-4">
                    <div class="input-group">
                        <input type="date" name="from" class="form-control border rounded-start-pill bg-light ps-3">
                        <input type="date" name="to" class="form-control border rounded-end-pill bg-light ps-3">
                    </div>
                </div>

                <div class="col-lg-2">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 px-3">
                        <i class="bi bi-funnel me-1"></i> <?=_("Filter");?>
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
                                <th class="ps-4 py-3" style="width: 50px;"><input type="checkbox" id="selectall" class="form-check-input all"></th>
                                <th style="min-width: 300px;"><?=_("Comment Insight");?></th>
                                <th class="text-center"><?=_("Identity");?></th>
                                <th class="text-center"><?=_("Timeline");?></th>
                                <th class="text-center"><?=_("Status");?></th>
                                <th class="text-end pe-4"><?=_("Actions");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($data['num'] > 0):
                                foreach ($data['posts'] as $p):
                                    $statusBadge = '';
                                    $rowClass = '';
                                    if ($p->status == '0') {
                                        $statusBadge = '<span class="badge bg-secondary bg-opacity-10 text-secondary px-3 rounded-pill">'._("Hidden").'</span>';
                                        $rowClass = 'opacity-75';
                                    } elseif ($p->status == '1') {
                                        $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">'._("Approved").'</span>';
                                    } elseif ($p->status == '2') {
                                        $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning px-3 rounded-pill">'._("Pending").'</span>';
                                        $rowClass = 'bg-warning bg-opacity-10';
                                    }
                                    $commentText = Typo::strip($p->comment);
                                    $commentShort = (strlen($commentText) > 120) ? substr($commentText, 0, 117).'...' : $commentText;
                            ?>
                            <tr class="<?=$rowClass;?>">
                                <td class="ps-4">
                                    <input type="checkbox" name="post_id[]" value="<?=$p->id;?>" class="form-check-input all select">
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <a href="<?=Url::post($p->post_id);?>" target="_blank" class="text-dark fw-bold text-decoration-none h6 mb-0 d-inline-block">
                                            <?=$commentShort;?>
                                        </a>
                                    </div>
                                    <div class="extra-small text-muted d-flex align-items-center">
                                        <i class="bi bi-geo-alt me-1"></i> IP: <?=$p->ipaddress;?>
                                        <span class="mx-2 text-opacity-25 opacity-25">|</span>
                                        <i class="bi bi-link-45deg me-1"></i> ID: <?=$p->id;?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark small"><?=$p->name;?></div>
                                    <div class="extra-small text-muted"><?=$p->email;?></div>
                                </td>
                                <td class="text-center">
                                    <div class="small fw-semibold text-dark"><?=Date::format($p->date, 'd M Y');?></div>
                                    <div class="text-muted extra-small"><?=Date::format($p->date, 'H:i A');?></div>
                                </td>
                                <td class="text-center">
                                    <?=$statusBadge;?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="index.php?page=comments&act=del&id=<?=$p->id;?>&token=<?=TOKEN;?>" 
                                           class="btn btn-light btn-sm rounded-circle border overflow-hidden" 
                                           onclick="return confirm('<?=_("Permanent removal of this comment?");?>');" title="Remove Permanently">
                                            <i class="bi bi-trash text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="opacity-25 mb-3"><i class="bi bi-chat-left-dots fs-1"></i></div>
                                    <p class="text-muted mb-0"><?=_("Your feedback history is clean.");?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <select name="action" class="form-select form-select-sm rounded-pill bg-light" style="width: 160px;">
                            <option value="publish"><?=_("Approve Selected");?></option>
                            <option value="unpublish"><?=_("Hide Selected");?></option>
                            <option value="delete"><?=_("Purge Selected");?></option>
                        </select>
                        <button type="submit" name="doaction" class="btn btn-danger btn-sm rounded-pill px-4">
                            <i class="bi bi-lightning-charge-fill me-1"></i> <?=_("Process");?>
                        </button>
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
    .pagination-container .pagination { margin: 0; }
    .pagination-container .page-link { border: 0; background: #f8fafc; color: #64748b; border-radius: 8px !important; margin: 0 3px; font-weight: 500; font-size: 0.85rem; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; }
    .pagination-container .page-item.active .page-link { background: var(--gx-primary); color: #fff; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); }
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
</style>