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
            <h3 class="fw-bold text-dark mb-0"><?=_("Content Management");?></h3>
            <p class="text-muted small"><?=_("Manage and organize your website posts.");?></p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="index.php?page=posts&act=add&token=<?=TOKEN;?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> <?=_("Create New Post");?>
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary me-3">
                    <i class="bi bi-files fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase"><?=_("Total");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::totalPost('post');?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase"><?=_("Published");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::activePost('post');?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3 d-flex flex-row align-items-center">
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger me-3">
                    <i class="bi bi-dash-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase"><?=_("Drafts");?></div>
                    <div class="h4 fw-bold m-0"><?=Stats::inactivePost('post');?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-4 px-4">
            <form action="index.php?page=posts" method="get" class="row g-3">
                <input type="hidden" name="page" value="posts">
                <input type="hidden" name="token" value="<?=TOKEN;?>">
                
                <div class="col-lg-3 col-md-6">
                    <div class="input-group border rounded-pill overflow-hidden bg-light">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control bg-transparent border-0" placeholder="<?=_("Filter by title...");?>">
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <?php
                    $vars = array(
                        'name' => 'cat',
                        'type' => 'post',
                        'class' => 'form-select border rounded-pill bg-light ps-3'
                    );
                    echo Categories::dropdown($vars);
                    ?>
                </div>

                <div class="col-lg-2 col-md-6">
                    <select name="status" class="form-select border rounded-pill bg-light ps-3">
                        <option value="1"><?=_("Published");?></option>
                        <option value="0"><?=_("Draft");?></option>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="input-group">
                        <input type="date" name="from" class="form-control border rounded-start-pill bg-light ps-3" placeholder="<?=_("From");?>">
                        <input type="date" name="to" class="form-control border rounded-end-pill bg-light ps-3" placeholder="<?=_("To");?>">
                    </div>
                </div>

                <div class="col-lg-2">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 px-3">
                        <i class="bi bi-funnel me-1"></i> <?=_("Apply Filter");?>
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <form action="" method="post">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 50px;"><?=_("ID");?></th>
                                <th><?=_("Core Post Details");?></th>
                                <th class="text-center"><?=_("Taxonomy");?></th>
                                <th class="text-center"><?=_("Timeline");?></th>
                                <th class="text-center"><?=_("Accountability");?></th>
                                <th class="text-center pe-4" style="width: 120px;"><?=_("Interaction");?></th>
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
                                        $statusLabel = '<span class="badge bg-warning bg-opacity-10 text-warning px-3 rounded-pill">'._("Draft").'</span>';
                                        $rowStyle = 'background-color: rgba(255, 193, 7, 0.02);';
                                    } else {
                                        $statusLabel = '<span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">'._("Published").'</span>';
                                        $rowStyle = '';
                                    }
                            ?>
                            <tr style="<?=$rowStyle;?>">
                                <td class="ps-4 text-muted small"><?=$p->id;?></td>
                                <td>
                                    <a href="<?=Url::post($p->id);?>" target="_blank" class="fw-bold text-dark text-decoration-none d-block mb-1">
                                        <?=(strlen($p->title) > 50) ? substr($p->title, 0, 48).'...' : $p->title;?>
                                    </a>
                                    <?=$statusLabel;?>
                                </td>
                                <td class="text-center">
                                    <span class="badge border text-muted px-2 py-1"><?=Categories::name($p->cat);?></span>
                                </td>
                                <td class="text-center">
                                    <div class="small fw-semibold"><?=Date::format($p->date, 'd M Y');?></div>
                                    <div class="text-muted extra-small"><?=Date::format($p->date, 'H:i A');?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <img src="<?=Site::$url;?>assets/images/user1-256x256.png" class="rounded-circle me-2" width="24">
                                        <span class="small text-dark fw-medium"><?=$p->author;?></span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <?php if($accessEdit): ?>
                                        <a href="index.php?page=posts&act=edit&id=<?=$p->id;?>&token=<?=TOKEN;?>" class="btn btn-light btn-sm rounded-circle me-1 border" title="<?=_("Edit");?>">
                                            <i class="bi bi-pencil-square text-success"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if($accessDelete): ?>
                                        <a href="index.php?page=posts&act=del&id=<?=$p->id;?>&token=<?=TOKEN;?>" 
                                           class="btn btn-light btn-sm rounded-circle border" 
                                           onclick="return confirm('<?=_("Are you sure you want to delete this?");?>');" title="<?=_("Delete");?>">
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
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                    <p class="text-muted"><?=_("No posts found in your library.");?></p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top py-4 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <?php if(User::access(2)): ?>
                        <select name="action" class="form-select form-select-sm rounded-pill bg-light" style="width: 150px;">
                            <option value="publish"><?=_("Bulk Publish");?></option>
                            <option value="unpublish"><?=_("Bulk Draft");?></option>
                            <option value="delete"><?=_("Bulk Delete");?></option>
                        </select>
                        <button type="submit" name="doaction" class="btn btn-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-lightning-fill"></i> <?=_("Execute");?>
                        </button>
                        <?php endif; ?>
                        <input type="hidden" name="token" value="<?=TOKEN;?>">
                    </div>
                    <div class="pagination-wrapper">
                        <?=$data['paging'];?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .btn-group .btn:hover { background-color: #f8fafc; border-color: #cbd5e1 !important; transform: translateY(-1px); }
    .table-hover tbody tr:hover { background-color: rgba(59, 130, 246, 0.03) !important; }
    .pagination-wrapper .pagination { margin-bottom: 0; }
    .pagination-wrapper .page-link { border-radius: 50% !important; margin: 0 2px; border: 0; background: #f1f5f9; color: #475569; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; }
    .pagination-wrapper .page-item.active .page-link { background: var(--gx-primary); color: #fff; }
</style>

