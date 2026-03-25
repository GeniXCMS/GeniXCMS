<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
if (isset($_GET['token']) && Token::validate($_GET['token'])) {
    $token = TOKEN;
} else {
    $token = '';
}

$isEdit = ($_GET['act'] == 'edit');
$pagetitle = $isEdit ? _("Modify Page Structure") : _("Initialize New Page");
$act = $isEdit ? "edit&id=".Typo::int($_GET['id'])."&token=".$token : 'add';

$id = $isEdit ? Typo::int($_GET['id']) : 0;
$title = $content = $date = $status = $tags = '';
$pub = $unpub = '';

if (isset($data['post'])) {
    if (!isset($data['post']['error'])) {
        foreach ($data['post'] as $p) {
            $title = $p->title;
            $content = $p->content;
            $date = $p->date;
            $status = $p->status;
            $tags = @$p->tags;
        }
        $pub = ($status == 1) ? 'SELECTED' : '';
        $unpub = ($status == 0) ? 'SELECTED' : '';
    } else {
        $data['alertDanger'][] = $data['post']['error'];
    }
}
?>

<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data);?>
</div>

<form action="index.php?page=pages&act=<?=$act?>&token=<?=TOKEN;?>" method="post" role="form">
    <div class="container-fluid py-4">
        <!-- Editor Header -->
        <div class="row align-items-center mb-4">
            <div class="col-md-7">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item small"><a href="index.php?page=pages" class="text-decoration-none text-muted"><?=_("Pages Library");?></a></li>
                        <li class="breadcrumb-item small active" aria-current="page"><?=_("Drafting");?></li>
                    </ol>
                </nav>
                <h3 class="fw-bold text-dark mb-0"><?=$pagetitle;?></h3>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="index.php?page=pages" class="btn btn-light rounded-pill px-4 me-2 border">
                    <i class="bi bi-arrow-left me-1"></i> <?=_("Back");?>
                </a>
                <button type="submit" name="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-cloud-upload me-1"></i> <?=_("Commit Changes");?>
                </button>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <?php if (Options::v('multilang_enable') === 'on'): ?>
                        <ul class="nav nav-pills nav-pills-custom mb-3" id="langTab" role="tablist">
                            <?php
                            $def = Options::v('multilang_default');
                            $listlang = json_decode(Options::v('multilang_country'), true);
                            foreach ($listlang as $key => $value):
                                $flag = strtolower($value['flag']);
                                $isActive = ($key == $def) ? 'active' : '';
                            ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?=$isActive;?> rounded-pill px-4" id="tab-<?=$key;?>" data-bs-toggle="pill" data-bs-target="#pane-<?=$key;?>" type="button" role="tab">
                                    <span class="flag-icon flag-icon-<?=$flag;?> me-2 small"></span> <?=$value['country'];?>
                                </button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <div class="tab-content" id="langTabContent">
                            <?php if (Options::v('multilang_enable') === 'on'): 
                                foreach ($listlang as $key => $value):
                                    $isActive = ($key == $def) ? 'show active' : '';
                                    if ($isEdit) {
                                        $langData = Language::getLangParam($key, $id);
                                        if ($langData == '' || !Posts::existParam('multilang', $id)) {
                                            $langData['title'] = $title;
                                            $langData['content'] = $content;
                                        }
                                    } else {
                                        $langData['title'] = '';
                                        $langData['content'] = '';
                                    }
                            ?>
                            <div class="tab-pane fade <?=$isActive;?>" id="pane-<?=$key;?>" role="tabpanel">
                                <div class="mb-4">
                                    <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Page Heading");?> (<?=$key;?>)</label>
                                    <input type="text" name="title[<?=$key;?>]" class="form-control form-control-lg border-0 bg-light rounded-3 px-4 py-3 fw-bold" 
                                           placeholder="<?=_("Enter a descriptive title...");?>" value="<?=$langData['title'];?>">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Body Composition");?></label>
                                    <textarea name="content[<?=$key;?>]" class="form-control editor rounded-4" id="editor_<?=$key;?>" rows="22"><?=$langData['content'];?></textarea>
                                </div>
                            </div>
                            <?php endforeach; else: ?>
                            <div class="mb-4">
                                <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Page Heading");?></label>
                                <input type="text" name="title" class="form-control form-control-lg border-0 bg-light rounded-3 px-4 py-3 fw-bold" 
                                       placeholder="<?=_("Enter a descriptive title...");?>" value="<?=$title;?>">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Body Composition");?></label>
                                <textarea name="content" class="form-control editor rounded-4" id="primary_editor" rows="22"><?=$content;?></textarea>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php Hooks::run('page_param_form_bottom', $data); ?>
            </div>

            <!-- Sidebar Controls -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-dark text-white py-3 px-4 border-0">
                        <h6 class="m-0 fw-bold"><i class="bi bi-gear-wide-connected me-2"></i><?=_("Publication Settings");?></h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Visibility Status");?></label>
                            <div class="input-group rounded-3 border-0 bg-light px-3 py-1">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-eye text-primary"></i></span>
                                <select name="status" class="form-select border-0 bg-transparent fw-medium ps-1">
                                    <option value="1" <?=$pub;?>><?=_("Public / Live");?></option>
                                    <option value="0" <?=$unpub;?>><?=_("Private / Draft");?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small text-muted text-uppercase fw-bold"><?=_("Internal Timestamp");?></label>
                            <div class="input-group rounded-3 border-0 bg-light px-3 py-1">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-calendar-event text-danger"></i></span>
                                <input type="text" name="date" class="form-control border-0 bg-transparent fw-medium ps-1" id="dateTime" value="<?=$date;?>" placeholder="<?=_("Now");?>">
                            </div>
                            <div class="form-text extra-small opacity-75 mt-2 ms-1"><?=_("Leave empty for immediate publication.");?></div>
                        </div>
                    </div>
                </div>

                <?php Hooks::run('page_param_form_sidebar', $data); ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?=$token;?>">
</form>

<style>
    .nav-pills-custom .nav-link { color: #64748b; font-weight: 600; font-size: 0.85rem; border: 1px solid #f1f5f9; margin-right: 8px; transition: all 0.3s ease; }
    .nav-pills-custom .nav-link:hover { background-color: #f8fafc; border-color: #e2e8f0; }
    .nav-pills-custom .nav-link.active { background-color: var(--gx-primary); color: #fff; border-color: var(--gx-primary); box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); }
    .form-control-lg:focus { background-color: #fff !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    .extra-small { font-size: 0.75rem; }
    .card-header h6 { letter-spacing: 0.5px; text-transform: uppercase; font-size: 0.8rem; }
</style>
