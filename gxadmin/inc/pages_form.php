<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

if (isset($_GET['token']) && Token::validate($_GET['token'])) {
    $token = TOKEN;
} else {
    $token = '';
}

$isEdit = ($_GET['act'] == 'edit');
$pagetitle = $isEdit ? _("Modify Page Structure") : _("Initialize New Page");
$act = $isEdit ? "edit&id=" . Typo::int($_GET['id']) . "&token=" . $token : 'add';

$id = $isEdit ? Typo::int($_GET['id']) : 0;
$title = $content = $date = $status = $tags = '';
$pub = $unpub = '';

if (isset($data['post'])) {
    if (!isset($data['post']['error'])) {
        foreach ($data['post'] as $p) {
            if (!is_object($p))
                continue;
            $title = $p->title;
            $content = $p->content;
            $date = $p->date;
            $status = $p->status;
            $tags = @$p->tags;
            $post_image = $p->post_image ?? "";
        }
        $pub = ($status == 1) ? 'SELECTED' : '';
        $unpub = ($status == 0) ? 'SELECTED' : '';
    } else {
        $data['alertDanger'][] = $data['post']['error'];
        $title = $content = $date = $status = $tags = $post_image = '';
    }
} else {
    $title = $content = $date = $status = $tags = $post_image = '';
}
?>

<?php
$ui = new UiBuilder([
    'header' => [
        'title' => $pagetitle,
        'subtitle' => _('Drafting and structured architecture for the digital interface.'),
        'icon' => 'bi bi-files',
        'buttons' => [
            [
                'label' => _('Commit Changes'),
                'type' => 'button',
                'icon' => 'bi bi-cloud-upload',
                'class' => 'btn btn-primary rounded-pill px-4 shadow-sm'
            ],
            [
                'label' => _('Back'),
                'url' => 'index.php?page=pages',
                'icon' => 'bi bi-arrow-left',
                'class' => 'btn btn-light border bg-white rounded-pill px-4'
            ]
        ]
    ]
]);
?>
<form action="index.php?page=pages&act=<?= $act ?>&token=<?= TOKEN; ?>" method="post" role="form">
    <div class="col-md-12">
        <?= Hooks::run('admin_page_notif_action', $data); ?>
    </div>

    <?php $ui->renderHeader(); ?>

    <div class="container-fluid px-0">
        <?php $ui->renderElement([
            'type' => 'breadcrumb',
            'items' => [
                ['label' => _('Pages Library'), 'url' => 'index.php?page=pages'],
                ['label' => _('Drafting'), 'active' => true]
            ]
        ]); ?>
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
                                        <button class="nav-link <?= $isActive; ?> rounded-pill px-4" id="tab-<?= $key; ?>"
                                            data-bs-toggle="pill" data-bs-target="#pane-<?= $key; ?>" type="button" role="tab">
                                            <span class="flag-icon flag-icon-<?= $flag; ?> me-2 small"></span>
                                            <?= $value['country']; ?>
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
                                    <div class="tab-pane fade <?= $isActive; ?>" id="pane-<?= $key; ?>" role="tabpanel">
                                        <div class="mb-2">
                                            <label
                                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Page Heading"); ?>
                                                (<?= $key; ?>)</label>
                                            <input type="text" name="title[<?= $key; ?>]"
                                                class="form-control form-control-lg border-0 bg-light rounded-3 px-4 py-3 fw-bold"
                                                placeholder="<?= _("Enter a descriptive title..."); ?>"
                                                value="<?= $langData['title']; ?>">
                                        </div>
                                        <div class="mb-0">
                                            <label
                                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Body Composition"); ?></label>
                                            <textarea name="content[<?= $key; ?>]" class="form-control editor rounded-4"
                                                id="editor_<?= $key; ?>" rows="22"><?= $langData['content']; ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; else: ?>
                                <div class="mb-2">
                                    <label
                                        class="form-label small text-muted text-uppercase fw-bold"><?= _("Page Heading"); ?></label>
                                    <input type="text" name="title"
                                        class="form-control form-control-lg border-0 bg-light rounded-3 px-4 py-3 fw-bold"
                                        placeholder="<?= _("Enter a descriptive title..."); ?>" value="<?= $title; ?>">
                                </div>
                                <div class="mb-0">
                                    <label
                                        class="form-label small text-muted text-uppercase fw-bold"><?= _("Body Composition"); ?></label>
                                    <textarea name="content" class="form-control editor rounded-4" id="primary_editor"
                                        rows="22"><?= $content; ?></textarea>
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
                        <h6 class="m-0 fw-bold"><i
                                class="bi bi-gear-wide-connected me-2"></i><?= _("Publication Settings"); ?></h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label
                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Page Layout"); ?></label>
                            <?php
                            $layout = $isEdit ? Posts::getParam('layout', $id) : 'default';
                            $layouts = Theme::getLayouts();
                            ?>
                            <div class="input-group rounded-3 border-0 bg-light px-3 py-1">
                                <span class="input-group-text bg-transparent border-0"><i
                                        class="bi bi-layout-sidebar text-success"></i></span>
                                <select name="param[layout]" class="form-select border-0 bg-transparent fw-medium ps-1">
                                    <option value="default" <?= ($layout == 'default' ? 'selected' : ''); ?>>
                                        <?= _("Default Theme Layout"); ?>
                                    </option>
                                    <?php foreach ($layouts as $slug => $name): ?>
                                        <option value="<?= $slug; ?>" <?= ($layout == $slug ? 'selected' : ''); ?>>
                                            <?= $name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label
                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Visibility Status"); ?></label>
                            <div class="input-group rounded-3 border-0 bg-light px-3 py-1">
                                <span class="input-group-text bg-transparent border-0"><i
                                        class="bi bi-eye text-primary"></i></span>
                                <select name="status" class="form-select border-0 bg-transparent fw-medium ps-1">
                                    <option value="1" <?= $pub; ?>><?= _("Public / Live"); ?></option>
                                    <option value="0" <?= $unpub; ?>><?= _("Private / Draft"); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label
                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Internal Timestamp"); ?></label>
                            <div class="input-group rounded-3 border-0 bg-light px-3 py-1">
                                <span class="input-group-text bg-transparent border-0"><i
                                        class="bi bi-calendar-event text-danger"></i></span>
                                <input type="text" name="date"
                                    class="form-control border-0 bg-transparent fw-medium ps-1" id="dateTime"
                                    value="<?= $date; ?>" placeholder="<?= _("Now"); ?>">
                            </div>
                            <div class="form-text extra-small opacity-75 mt-2 ms-1">
                                <?= _("Leave empty for immediate publication."); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="fw-bold m-0 text-dark small"><i
                                class="bi bi-image me-2 text-success"></i><?= _("Hero Asset"); ?></h6>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0 text-center">
                        <div class="media-drop-zone rounded-4 border-2 border-dashed bg-light p-3 position-relative"
                            style="cursor: pointer;" onclick="elfinderDialog2()">
                            <?php if ($post_image): ?>
                                <img id="post_image_preview" class="img-fluid rounded-3 shadow-sm" src="<?= $post_image; ?>"
                                    style="max-height: 250px; width: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="py-5" id="post_image_placeholder">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                                    <p class="text-muted small mt-2 mb-0"><?= _("Click to select hero image"); ?></p>
                                </div>
                                <img id="post_image_preview" class="img-fluid rounded-3 shadow-sm d-none"
                                    style="max-height: 250px; width: 100%; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                        <input name="post_image" id="post_image" type="hidden" value="<?= $post_image; ?>">
                    </div>
                </div>

                <?php Hooks::run('page_param_form_sidebar', $data); ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?= $token; ?>">
</form>

</style>