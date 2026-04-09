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
$tagCards = [];
if ($data['num'] > 0) {
    foreach ($data['cat'] as $c) {
        if ($c->parent == '' || $c->parent == 0) {
            $tagCards[] = "
            <div class='card border-0 shadow-sm rounded-4 tag-card overflow-hidden'>
                <div class='card-body p-3'>
                    <form action='index.php?page=tags' method='POST' class='d-flex align-items-center gap-2'>
                        <div class='input-group input-group-sm rounded-pill overflow-hidden bg-light border-0'>
                            <span class='input-group-text bg-transparent border-0 pe-0'><i class='bi bi-hash text-muted'></i></span>
                            <input type='text' name='cat' class='form-control border-0 bg-light ps-2 font-monospace fw-bold' value='{$c->name}'>
                            <input type='hidden' name='id' value='{$c->id}'>
                            <input type='hidden' name='token' value='" . TOKEN . "'>
                            <button class='btn btn-light border-0 text-primary px-3' type='submit' name='updatecat'><i class='bi bi-check-lg'></i></button>
                        </div>
                        <a href='?page=tags&act=del&id={$c->id}&token=" . TOKEN . "' class='btn btn-light btn-sm rounded-circle text-danger border-0' onclick=\"return confirm('" . _("Are you sure?") . "');\"><i class='bi bi-trash'></i></a>
                    </form>
                </div>
            </div>";
        }
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Content Classification'),
        'subtitle' => _('Manage and discover tags used across your posts.'),
        'icon' => 'bi bi-tags',
        'button' => [
            'url' => '#',
            'label' => _('New Tag'),
            'icon' => 'bi bi-tag-fill',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#addTagModal"'
        ],
    ],
    'content' => [
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 12,
                    'content' => [
                        'type' => 'raw',
                        'html' => '
                <div class="card border-0 shadow-sm rounded-pill px-4 py-2 bg-white d-inline-flex flex-row align-items-center mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary me-2">' . Stats::totalCat('tag') . '</span>
                    <span class="text-muted small fw-bold text-uppercase">' . _("Distinct Tags") . '</span>
                </div>'
                    ]
                ]
            ]
        ],
        [
            'type' => 'row',
            'items' => (function () use ($tagCards) {
                $items = [];
                if (empty($tagCards)) {
                    $items[] = ['width' => 12, 'content' => ['type' => 'raw', 'html' => '<div class="text-center py-5"><i class="bi bi-tags fs-1 text-muted opacity-25 d-block mb-3"></i><h5 class="text-dark fw-bold">' . _("No Tags Indexed") . '</h5></div>']];
                } else {
                    foreach ($tagCards as $card) {
                        $items[] = ['width' => 3, 'content' => ['type' => 'raw', 'html' => $card]];
                    }
                }
                return $items;
            })()
        ],

        // Modal
        [
            'type' => 'modal',
            'id' => 'addTagModal',
            'header' => '<i class="bi bi-tag-fill me-2 text-primary"></i>' . _("Register New Classification"),
            'size' => 'md',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php?page=tags',
                    'fields' => [
                        [
                            'type' => 'raw',
                            'html' => '
                            <div class="mb-0">
                                <label class="form-label small text-muted text-uppercase fw-bold">' . _("Label Name") . '</label>
                                <div class="input-group rounded-3 overflow-hidden bg-light">
                                    <span class="input-group-text bg-transparent border-0 text-muted ps-3">#</span>
                                    <input type="text" name="cat" class="form-control border-0 bg-light ps-0" placeholder="' . _("e.g. tech, lifestyle") . '" required>
                                </div>
                                <div class="form-text small opacity-75 mt-2">' . _("Tags help users find related content through a flat hierarchy.") . '</div>
                            </div>
                            <input type="hidden" name="token" value="' . TOKEN . '">'
                        ],
                        ['type' => 'button', 'name' => 'addcat', 'label' => _("Confirm Label"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100 mt-4']
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

<style>
    .tag-card {
        transition: all 0.2s ease;
        border: 1px solid transparent !important;
    }

    .tag-card:hover {
        transform: translateY(-2px);
        border-color: rgba(59, 130, 246, 0.2) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
    }

    .font-monospace {
        letter-spacing: -0.5px;
    }
</style>