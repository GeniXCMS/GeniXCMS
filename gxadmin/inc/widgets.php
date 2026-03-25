<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
?>
<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data);?>
</div>

<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0"><?=_("Widgets & Blocks");?></h3>
            <p class="text-muted small mb-0"><?=_("Manage dynamic components and layout structures.");?></p>
        </div>
        <div class="col-md-6 text-md-end">
            <?php if (isset($_GET['act']) && $_GET['act'] == 'edit'): ?>
                <a href="index.php?page=widgets" class="btn btn-light rounded-pill px-4 shadow-sm border">
                    <i class="bi bi-arrow-left me-1"></i> <?=_("Back to List");?>
                </a>
            <?php else: ?>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addWidget">
                    <i class="bi bi-plus-lg me-1"></i> <?=_("New Widget");?>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_GET['act']) && $_GET['act'] == 'edit' && isset($data['widget'][0])): 
        $w = $data['widget'][0];
    ?>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="index.php?page=widgets" method="post">
                    <h5 class="fw-bold mb-4">Edit Widget: <?=$w->name;?></h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Widget Name</label>
                            <input type="text" name="name" class="form-control border-0 bg-light rounded-3 py-2 px-3" value="<?=$w->name;?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Display Title</label>
                            <input type="text" name="title" class="form-control border-0 bg-light rounded-3 py-2 px-3" value="<?=$w->title;?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Location</label>
                            <select name="location" class="form-select border-0 bg-light rounded-3 py-2 px-3">
                                <option value="sidebar" <?=$w->location == 'sidebar' ? 'selected' : '';?>>Sidebar</option>
                                <option value="footer_1" <?=$w->location == 'footer_1' ? 'selected' : '';?>>Footer 1</option>
                                <option value="footer_2" <?=$w->location == 'footer_2' ? 'selected' : '';?>>Footer 2</option>
                                <option value="footer_3" <?=$w->location == 'footer_3' ? 'selected' : '';?>>Footer 3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Type</label>
                            <select name="type" class="form-select border-0 bg-light rounded-3 py-2 px-3">
                                <option value="html" <?=$w->type == 'html' ? 'selected' : '';?>>Custom HTML</option>
                                <option value="module" <?=$w->type == 'module' ? 'selected' : '';?>>Module Content</option>
                                <option value="recent_posts" <?=$w->type == 'recent_posts' ? 'selected' : '';?>>Recent Posts</option>
                                <option value="recent_comments" <?=$w->type == 'recent_comments' ? 'selected' : '';?>>Recent Comments</option>
                                <option value="tag_cloud" <?=$w->type == 'tag_cloud' ? 'selected' : '';?>>Tag Cloud</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ordering</label>
                            <input type="number" name="sorting" class="form-control border-0 bg-light rounded-3 py-2 px-3" value="<?=$w->sorting;?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Content / Hook Name</label>
                            <textarea name="content" class="form-control border-0 bg-light rounded-3 py-2 px-3" rows="8"><?=$w->content;?></textarea>
                            <p class="small text-muted mt-2">Jika tipe konten adalah <strong>Module Content</strong>, masukkan nama Hook yang disediakan oleh module tersebut (contoh: <code>sample_widget_render</code>).</p>
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                            <input type="hidden" name="id" value="<?=$w->id;?>">
                            <input type="hidden" name="token" value="<?=TOKEN;?>">
                            <button type="submit" name="edit_widget" class="btn btn-primary rounded-pill px-5 shadow-sm">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted extra-small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3"><?=_("Name");?></th>
                        <th><?=_("Location");?></th>
                        <th><?=_("Type");?></th>
                        <th class="text-center"><?=_("Order");?></th>
                        <th class="text-center"><?=_("Status");?></th>
                        <th class="text-end pe-4"><?=_("Action");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data['num'] > 0): ?>
                        <?php foreach ($data['widgets'] as $w): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?=$w->name;?></div>
                                <div class="text-muted extra-small"><?=$w->title;?></div>
                            </td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?=$w->location;?></span></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info"><?=$w->type;?></span></td>
                            <td class="text-center"><?=$w->sorting;?></td>
                            <td class="text-center">
                                <?php if ($w->status == 1): ?>
                                    <a href="index.php?page=widgets&act=deactivate&id=<?=$w->id;?>&token=<?=TOKEN;?>" class="badge bg-success bg-opacity-10 text-success text-decoration-none">Active</a>
                                <?php else: ?>
                                    <a href="index.php?page=widgets&act=activate&id=<?=$w->id;?>&token=<?=TOKEN;?>" class="badge bg-danger bg-opacity-10 text-danger text-decoration-none">Disabled</a>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="index.php?page=widgets&act=edit&id=<?=$w->id;?>&token=<?=TOKEN;?>" class="btn btn-light btn-sm rounded-circle border me-2" title="Edit">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </a>
                                    <a href="index.php?page=widgets&act=del&id=<?=$w->id;?>&token=<?=TOKEN;?>" class="btn btn-light btn-sm rounded-circle border" onclick="return confirm('Delete widget?');" title="Delete">
                                        <i class="bi bi-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No widgets found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add -->
    <div class="modal fade" id="addWidget" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="index.php?page=widgets" method="post">
                    <div class="modal-header border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold m-0">Add New Widget</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Widget Name</label>
                                <input type="text" name="name" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Display Title</label>
                                <input type="text" name="title" class="form-control border-0 bg-light rounded-3 py-2 px-3">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Location</label>
                                <select name="location" class="form-select border-0 bg-light rounded-3 py-2 px-3">
                                    <option value="sidebar">Sidebar</option>
                                    <option value="footer_1">Footer 1 (Left)</option>
                                    <option value="footer_2">Footer 2 (Center)</option>
                                    <option value="footer_3">Footer 3 (Right)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Type</label>
                                <select name="type" class="form-select border-0 bg-light rounded-3 py-2 px-3">
                                    <option value="html">Custom HTML</option>
                                    <option value="module">Module Content</option>
                                    <option value="recent_posts">Recent Posts</option>
                                    <option value="recent_comments">Recent Comments</option>
                                    <option value="tag_cloud">Tag Cloud</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Ordering</label>
                                <input type="number" name="sorting" class="form-control border-0 bg-light rounded-3 py-2 px-3" value="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Content / Hook Name</label>
                                <textarea name="content" class="form-control border-0 bg-light rounded-3 py-2 px-3" rows="5"></textarea>
                                <p class="small text-muted mt-2">Gunakan <strong>Module Content</strong> sebagai tipe konten jika ingin memuat widget dari sebuah module.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" name="add_widget" class="btn btn-primary rounded-pill px-4 shadow-sm">Create Widget</button>
                        <input type="hidden" name="token" value="<?=TOKEN;?>">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>
