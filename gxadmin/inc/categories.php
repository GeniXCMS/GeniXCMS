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

// ── PREPARE DATA ──────────────────────────────────────────────────
$catType = isset($data['type']) ? $data['type'] : 'post';
$typeParam = ($catType !== 'post') ? '&type=' . $catType : '';
$baseAction = '?page=categories' . $typeParam;

$categoryCards = [];
if ($data['num'] > 0 && !isset($data['cat']['error'])) {
    foreach ($data['cat'] as $c) {
        $c = (object) $c;
        if (!is_object($c) || isset($c->error))
            continue;

        // Only process root categories for the main cards
        if ($c->parent == '' || $c->parent == 0) {
            $icon = $c->image ? $c->image : 'bi bi-folder2-open';
            $iconHtml = (filter_var($icon, FILTER_VALIDATE_URL))
                ? "<img src='{$icon}' class='img-fluid rounded-3' style='width:32px; height:32px; object-fit:cover;'>"
                : "<i class='{$icon}'></i>";

            // Build Sub-categories HTML
            $subHtml = "";
            $hasChildren = false;
            foreach ($data['cat'] as $c2) {
                $c2 = (object) $c2;
                if ($c2->parent == $c->id) {
                    $hasChildren = true;
                    $subHtml .= "
                        <div class='badge bg-light text-dark fw-medium rounded-pill px-3 py-2 border d-flex align-items-center gap-2 group-badge'>
                            <span>{$c2->name}</span>
                            <div class='badge-actions'>
                                <a href='#' onclick='editCategory(" . json_encode($c2) . ")' data-bs-toggle='modal' data-bs-target='#editCategoryModal' class='text-primary'><i class='bi bi-pencil'></i></a>
                                <a href='{$baseAction}&act=del&id={$c2->id}&token=" . TOKEN . "' class='text-danger ps-1' onclick=\"return confirm('" . _("Delete this sub-category?") . "');\"><i class='bi bi-dash-circle'></i></a>
                            </div>
                        </div>";
                }
            }
            if (!$hasChildren) {
                $subHtml = "<span class='text-muted small opacity-50 italic'>" . _("No children defined") . "</span>";
            }

            $categoryCards[] = "
            <div class='card border-0 shadow-sm rounded-4 h-100 category-card'>
                <div class='card-body p-4'>
                    <div class='d-flex align-items-start gap-4'>
                        <div class='category-icon-box flex-shrink-0 rounded-4 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary'>
                            {$iconHtml}
                        </div>
                        <div class='flex-grow-1 min-w-0'>
                            <h5 class='fw-bold text-dark mb-1 text-truncate'>{$c->name}</h5>
                            <p class='text-muted small mb-3 line-clamp-2' style='min-height: 2.4rem;'>" . ($c->desc ?: _("No description provided.")) . "</p>
                            <div class='d-flex align-items-center gap-2'>
                                <button class='btn btn-sm btn-light border rounded-pill px-3 py-1 fw-bold fs-xs text-muted' onclick='editCategory(" . json_encode($c) . ")' data-bs-toggle='modal' data-bs-target='#editCategoryModal'>
                                    <i class='bi bi-pencil me-1'></i> Edit
                                </button>
                                <a href='{$baseAction}&act=del&id={$c->id}&token=" . TOKEN . "' class='btn btn-sm btn-white text-danger border rounded-circle p-1' onclick=\"return confirm('" . _("Are you sure?") . "');\"><i class='bi bi-trash'></i></a>
                            </div>
                        </div>
                    </div>
                    <div class='mt-4 pt-3 border-top'>
                        <label class='d-block small text-muted text-uppercase fw-bold ls-1 mb-2 fs-xs'>" . _("Sub-Taxonomy") . "</label>
                        <div class='d-flex flex-wrap gap-2'>{$subHtml}</div>
                    </div>
                </div>
            </div>";
        }
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Content Taxonomy'),
        'subtitle' => _('Organize your site content with powerful hierarchical categories.'),
        'icon' => 'bi bi-diagram-3',
        'button' => [
            'url' => '#',
            'label' => _('New Category'),
            'icon' => 'bi bi-folder-plus',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#addCategoryModal"'
        ],
    ],
    'content' => [
        [
            'type' => 'raw',
            'html' => '
            <div class="row mb-5">
                <div class="col-12 text-start">
                    <div class="card border-0 shadow-sm rounded-pill px-4 py-2 bg-white d-inline-flex flex-row align-items-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary me-2">' . Stats::totalCat($catType) . '</span>
                        <span class="text-muted small fw-bold text-uppercase fs-xs">' . _("Registered Categories") . '</span>
                        <span class="mx-3 text-zinc-200">|</span>
                        <span class="text-muted small opacity-75">' . _("Adding context to your topics makes them easier to navigate.") . '</span>
                    </div>
                </div>
            </div>'
        ],
        [
            'type' => 'row',
            'items' => (function () use ($categoryCards) {
                $items = [];
                if (empty($categoryCards)) {
                    $items[] = ['width' => 12, 'content' => ['type' => 'raw', 'html' => '<div class="text-center py-5"><i class="bi bi-diagram-3 fs-1 text-muted opacity-25 d-block mb-3"></i><h5 class="text-dark fw-bold">' . _("Taxonomy Empty") . '</h5></div>']];
                } else {
                    foreach ($categoryCards as $card) {
                        $items[] = ['width' => 4, 'content' => ['type' => 'raw', 'html' => $card]];
                    }
                }
                return $items;
            })()
        ],

        // Modals
        [
            'type' => 'modal',
            'id' => 'addCategoryModal',
            'header' => '<i class="bi bi-plus-circle me-2"></i>' . _("Create New Category"),
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php' . $baseAction,
                    'fields' => [
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 7,
                                    'content' => [
                                        'type' => 'raw',
                                        'html' => '
                                <div class="mb-4">
                                    <label class="form-label text-muted text-uppercase fw-bold ls-1 fs-xs">' . _("Category Name") . '</label>
                                    <input type="text" name="cat" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted text-uppercase fw-bold ls-1 fs-xs">' . _("Hierarchical Parent") . '</label>
                                    ' . Categories::dropdown(['parent' => '0', 'name' => 'parent', 'sort' => 'ASC', 'order_by' => 'name', 'type' => $catType, 'class' => 'form-select border-0 bg-light rounded-3 py-2 px-3']) . '
                                </div>'
                                    ]
                                ],
                                [
                                    'width' => 5,
                                    'content' => [
                                        'type' => 'raw',
                                        'html' => '
                                <div class="mb-4">
                                    <label class="form-label text-muted text-uppercase fw-bold ls-1 fs-xs">' . _("Icon / Image URL") . '</label>
                                    <input type="text" name="image" class="form-control border-0 bg-light rounded-3 py-2 px-3" placeholder="bi bi-heart">
                                </div>'
                                    ]
                                ]
                            ]
                        ],
                        ['type' => 'textarea', 'name' => 'desc', 'label' => _("Context Description"), 'rows' => 3],
                        ['type' => 'raw', 'html' => '<input type="hidden" name="token" value="' . TOKEN . '">'],
                        ['type' => 'button', 'name' => 'addcat', 'label' => _("Add Category"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold']
                    ]
                ]
            ]
        ],
        [
            'type' => 'modal',
            'id' => 'editCategoryModal',
            'header' => '<i class="bi bi-pencil-square me-2 text-warning"></i>' . _("Modify Taxonomy Entry"),
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php' . $baseAction,
                    'fields' => [
                        [
                            'type' => 'row',
                            'items' => [
                                [
                                    'width' => 7,
                                    'content' => [
                                        'type' => 'raw',
                                        'html' => '
                                <div class="mb-4">
                                    <label class="form-label text-muted text-uppercase fw-bold ls-1 fs-xs">' . _("Official Name") . '</label>
                                    <input type="text" name="cat" id="edit_cat_name" class="form-control border-0 bg-light rounded-3 py-2 px-3" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted text-uppercase fw-bold ls-1 fs-xs">' . _("Parent Category") . '</label>
                                    ' . Categories::dropdown(['parent' => '0', 'name' => 'parent', 'id' => 'edit_cat_parent', 'sort' => 'ASC', 'order_by' => 'name', 'type' => $catType, 'class' => 'form-select border-0 bg-light rounded-3 py-2 px-3']) . '
                                </div>'
                                    ]
                                ],
                                [
                                    'width' => 5,
                                    'content' => [
                                        'type' => 'raw',
                                        'html' => '
                                <div class="mb-4 text-center">
                                    <div id="edit_icon_preview" class="mx-auto mb-3 rounded-4 d-flex align-items-center justify-content-center bg-light text-primary fs-1" style="width:80px; height:80px; border: 2px dashed #e2e8f0;">
                                        <i class="bi bi-image text-muted opacity-25"></i>
                                    </div>
                                    <input type="text" name="image" id="edit_cat_image" class="form-control border-0 bg-light rounded-3 py-2 px-3 text-center" oninput="updateIconPreview(this.value)">
                                </div>'
                                    ]
                                ]
                            ]
                        ],
                        ['type' => 'textarea', 'name' => 'desc', 'id' => 'edit_cat_desc', 'label' => _("Taxonomy Description"), 'rows' => 3],
                        ['type' => 'raw', 'html' => '<input type="hidden" name="id" id="edit_cat_id"><input type="hidden" name="token" value="' . TOKEN . '">'],
                        ['type' => 'button', 'name' => 'updatecat', 'label' => _("Commit Changes"), 'class' => 'btn btn-dark rounded-pill px-5 fw-bold']
                    ]
                ]
            ]
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

$builder = new UiBuilder($schema);
$builder->render();
?>

<script>
    function editCategory(data) {
        document.getElementById('edit_cat_id').value = data.id;
        document.getElementById('edit_cat_name').value = data.name;
        document.getElementById('edit_cat_parent').value = data.parent;
        document.getElementById('edit_cat_image').value = data.image || '';
        document.getElementById('edit_cat_desc').value = data.desc || '';
        updateIconPreview(data.image || '');
    }
    function updateIconPreview(val) {
        const preview = document.getElementById('edit_icon_preview');
        if (!val) { preview.innerHTML = '<i class="bi bi-image text-muted opacity-25"></i>'; return; }
        if (val.match(/^https?:\/\//i) || val.match(/^\//i)) {
            preview.innerHTML = `<img src="${val}" class="img-fluid rounded-4 h-100 w-100" style="object-fit:cover;">`;
        } else { preview.innerHTML = `<i class="${val}"></i>`; }
    }
</script>

<style>
    .category-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9 !important;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08) !important;
        border-color: rgba(59, 130, 246, 0.2) !important;
    }

    .category-icon-box {
        width: 64px;
        height: 64px;
        font-size: 24px;
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    .fs-xs {
        font-size: 0.75rem;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .group-badge {
        transition: all 0.2s;
        position: relative;
        overflow: hidden;
    }

    .badge-actions {
        display: none;
        margin-left: 8px;
        font-size: 11px;
    }

    .group-badge:hover .badge-actions {
        display: inline-flex;
        align-items: center;
    }
</style>