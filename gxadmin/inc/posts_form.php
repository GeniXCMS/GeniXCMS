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
if (isset($_GET['token']) && Token::validate(Typo::cleanX($_GET['token']))) {
    $token = TOKEN;
} else {
    $token = '';
}
$isEdit = ($_GET['act'] == 'edit');
$postType = $data['postType'] ?? Typo::cleanX($_GET['type'] ?? 'post');
$pagetitle = $isEdit ? _('Edit') : _('Create');
$act = $isEdit ? "edit&id=" . Typo::int($_GET['id']) . "&token=" . $token : 'add';
$act .= "&type=" . $postType;

if (isset($data['post'])) {
    if (!isset($data['post']['error'])) {
        foreach ($data['post'] as $p) {
            $title = $p->title;
            $content = $p->content;
            $date = $p->date;
            $status = $p->status;
            $cat = $p->cat;
            $tags = $p->tags ?? "";
            $post_image = $p->post_image ?? "";
        }
        $id = Typo::int($_GET['id']);
    } else {
        $data['alertDanger'][] = $data['post']['error'];
        $title = $content = $date = $status = $cat = $tags = $post_image = '';
    }
} else {
    $title = $content = $date = $status = $cat = $tags = $post_image = '';
}
?>
<?php
$ui = new UiBuilder([
    'header' => [
        'title' => $pagetitle . ' ' . _('Post'),
        'subtitle' => _('Compose and distribute high-fidelity content across the digital enterprise.'),
        'icon' => 'bi bi-pencil-square',
        'buttons' => [
            [
                'label' => _('Publish Changes'),
                'type' => 'button',
                'icon' => 'bi bi-send',
                'class' => 'btn btn-primary rounded-pill px-4 shadow-sm'
            ],
            [
                'label' => _('Discard'),
                'url' => 'index.php?page=posts',
                'icon' => 'bi bi-x-circle',
                'class' => 'btn btn-light border bg-white rounded-pill px-4'
            ]
        ]
    ]
]);
?>
<form action="index.php?page=posts&act=<?= $act ?>&token=<?= TOKEN; ?>" method="post" role="form">
    <div class="col-md-12">
        <?= Hooks::run('admin_page_notif_action', $data); ?>
    </div>

    <?php $ui->renderHeader(); ?>

    <div class="container-fluid px-0">
        <div class="row g-4">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <?php if (Options::v('multilang_enable') === 'on'): ?>
                            <ul class="nav nav-pills mb-4 bg-light p-1 rounded-pill" role="tablist"
                                style="width: fit-content;">
                                <?php
                                $def = Options::v('multilang_default');
                                $deflang = Language::getDefaultLang();
                                $listlang = json_decode(Options::v('multilang_country'), true);
                                ?>
                                <li class="nav-item">
                                    <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill"
                                        data-bs-target="#lang-<?= $def; ?>" type="button">
                                        <i class="bi bi-translate me-1"></i> <?= $deflang['country']; ?>
                                    </button>
                                </li>
                                <?php
                                $clonedList = $listlang;
                                unset($clonedList[$def]);
                                foreach ($clonedList as $key => $value):
                                    ?>
                                    <li class="nav-item">
                                        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill"
                                            data-bs-target="#lang-<?= $key; ?>" type="button">
                                            <?= $value['country']; ?>
                                        </button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="lang-<?= $def; ?>">
                                    <div class="mb-4">
                                        <label
                                            class="form-label fw-bold text-dark small text-uppercase"><?= _("Primary Heading"); ?></label>
                                        <input type="text" name="title[<?= $def; ?>]"
                                            class="form-control form-control-lg border-0 bg-light rounded-3"
                                            placeholder="<?= _("Write a captivating title..."); ?>" value="<?= $title; ?>">
                                    </div>
                                    <div class="mb-4">
                                        <textarea name="content[<?= $def; ?>]" class="form-control editor"
                                            id="content_<?= $def; ?>"><?= $content; ?></textarea>
                                    </div>
                                </div>
                                <?php
                                foreach ($clonedList as $key => $value):
                                    $lang = $isEdit ? Language::getLangParam($key, $id) : ['title' => '', 'content' => ''];
                                    if (empty($lang) || !Posts::existParam('multilang', $id)) {
                                        $lang = ['title' => $title, 'content' => $content];
                                    }
                                    ?>
                                    <div class="tab-pane fade" id="lang-<?= $key; ?>">
                                        <div class="mb-4">
                                            <label
                                                class="form-label fw-bold text-dark small text-uppercase"><?= _("Translation Info"); ?></label>
                                            <input type="text" name="title[<?= $key; ?>]"
                                                class="form-control border-0 bg-light rounded-3"
                                                placeholder="<?= _("Title for"); ?> <?= $value['country']; ?>"
                                                value="<?= $lang['title']; ?>">
                                        </div>
                                        <div class="mb-4">
                                            <textarea name="content[<?= $key; ?>]" class="form-control editor"
                                                id="content_<?= $key; ?>"><?= $lang['content']; ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <label
                                    class="form-label fw-bold text-dark small text-uppercase"><?= _("Post Architecture"); ?></label>
                                <input type="text" name="title"
                                    class="form-control form-control-lg border-0 bg-light rounded-3 shadow-none"
                                    placeholder="<?= _("Enter post title here..."); ?>" value="<?= $title; ?>">
                            </div>
                            <div class="mb-4">
                                <textarea name="content" class="form-control editor"
                                    id="editor_main"><?= $content; ?></textarea>
                            </div>
                        <?php endif; ?>

                        <?php Hooks::run('post_param_form_bottom', $data); ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar Controls -->
            <div class="col-lg-4">
                <!-- Publishing Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="fw-bold m-0"><i
                                class="bi bi-eye me-2 text-primary"></i><?= _("Visibility & Distribution"); ?></h6>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0">
                        <div class="mb-3">
                            <label
                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Primary Category"); ?></label>
                            <?php
                            $catVars = [
                                'order_by' => 'name',
                                'name' => 'cat',
                                'sort' => 'ASC',
                                'type' => $postType,
                                'class' => 'form-select border-0 bg-light rounded-3'
                            ];
                            if (isset($cat))
                                $catVars['selected'] = $cat;
                            echo Categories::dropdown($catVars);
                            ?>
                        </div>
                        <div class="mb-3">
                            <label
                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Life-Cycle State"); ?></label>
                            <select name="status" class="form-select border-0 bg-light rounded-3">
                                <option value="1" <?= $status == 1 ? 'selected' : ''; ?>><?= _("Release (Live)"); ?>
                                </option>
                                <option value="0" <?= $status == 0 ? 'selected' : ''; ?>><?= _("Draft (Private)"); ?>
                                </option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label
                                class="form-label small text-muted text-uppercase fw-bold"><?= _("Chronology (Manual Override)"); ?></label>
                            <div class="input-group bg-light rounded-3 overflow-hidden">
                                <input type="text" class="form-control border-0 bg-transparent" name="date"
                                    value="<?= $date; ?>" placeholder="<?= _("YYYY-MM-DD HH:MM:SS"); ?>">
                                <span class="input-group-text bg-transparent border-0 text-muted px-3"><i
                                        class="bi bi-clock-history"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="fw-bold m-0"><i class="bi bi-image me-2 text-success"></i><?= _("Hero Asset"); ?>
                        </h6>
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

                <!-- Taxonomy Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h6 class="fw-bold m-0"><i
                                class="bi bi-tag me-2 text-warning"></i><?= _("Classification Tags"); ?></h6>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0">
                        <textarea name="tags" id="tags"
                            class="form-control border-0 bg-light rounded-3"><?= $tags; ?></textarea>
                        <p class="text-muted extra-small mt-2 mb-0">
                            <?= _("Connect concepts using comma separated terms."); ?>
                        </p>
                    </div>
                </div>

                <?php Hooks::run('post_param_form_sidebar', $data); ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?= $token; ?>">
</form>

<style>
    .media-drop-zone {
        transition: all 0.3s ease;
        border: 2px dashed #e2e8f0;
    }

    .media-drop-zone:hover {
        border-color: var(--gx-primary);
        background-color: rgba(59, 130, 246, 0.05) !important;
    }

    .form-control-lg {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        border: 1px solid var(--gx-primary) !important;
    }

    .nav-pills .nav-link {
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .nav-pills .nav-link.active {
        background: #fff !important;
        color: var(--gx-primary) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .extra-small {
        font-size: 0.72rem;
    }
</style>

<script>
    $(document).ready(function () {
        if ($('#tags').length > 0) {
            $('#tags').tagsInput({
                width: 'auto',
                height: 'auto',
                defaultText: '<?= _("add a tag"); ?>'
            });

            // Target the input created by tagsInput (id+tag)
            var ajaxUrl = '<?= Url::ajax("tags"); ?>&type=<?= $postType; ?>';
            $('#tags_tag').autocomplete({
                source: function (request, response) {
                    $.getJSON(ajaxUrl, { term: request.term }, response);
                },
                minLength: 1,
                select: function (event, ui) {
                    $('#tags').addTag(ui.item.value);
                    return false;
                }
            });
        }
    });
</script>