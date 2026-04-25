<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 2.0.0
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$isEdit = (isset($_GET['act']) && $_GET['act'] == 'edit' && isset($data['widget']));
$rows = [];
if (!$isEdit && $data['num'] > 0) {
    foreach ($data['widgets'] as $w) {
        $statusBadge = ($w->status == 1)
            ? '<a href="index.php?page=widgets&act=deactivate&id=' . $w->id . '&token=' . TOKEN . '" class="badge bg-success bg-opacity-10 text-success text-decoration-none px-3 py-2 rounded-pill fw-bold">' . _("Active") . '</a>'
            : '<a href="index.php?page=widgets&act=activate&id=' . $w->id . '&token=' . TOKEN . '" class="badge bg-danger bg-opacity-10 text-danger text-decoration-none px-3 py-2 rounded-pill fw-bold">' . _("Disabled") . '</a>';

        $rows[] = [
            ['content' => "<div class='fw-bold text-dark'>{$w->name}</div><div class='text-muted extra-small'>{$w->title}</div>", 'class' => 'ps-4 py-3'],
            "<div><span class='badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded-pill extra-small fw-bold text-uppercase'>{$w->location}</span></div>",
            "<div><span class='badge bg-info bg-opacity-10 text-info border px-2 py-1 rounded-pill extra-small fw-bold text-uppercase'>{$w->type}</span></div>",
            ['content' => "<span class='fw-bold text-zinc-400'>#{$w->sorting}</span>", 'class' => 'text-center'],
            ['content' => $statusBadge, 'class' => 'text-center'],
            [
                'content' => "
                <div class='btn-group'>
                    <a href='index.php?page=widgets&act=edit&id={$w->id}&token=" . TOKEN . "' class='btn btn-light btn-sm rounded-circle border me-2' title='Edit'><i class='bi bi-pencil-square text-primary'></i></a>
                    <a href='index.php?page=widgets&act=del&id={$w->id}&token=" . TOKEN . "' class='btn btn-light btn-sm rounded-circle border' onclick=\"return confirm('" . _("Delete widget?") . "');\" title='Delete'><i class='bi bi-trash text-danger'></i></a>
                </div>",
                'class' => 'text-end pe-4'
            ]
        ];
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
if ($isEdit) {
    /** @var stdClass $w */
    $w = $data['widget'];
    $schema = [
        'header' => [
            'title' => _('Modify Widget Instance'),
            'subtitle' => _('Adjusting global block parameters for improved layout density.'),
            'icon' => 'bi bi-pencil-square',
            'button' => [
                'type' => 'link',
                'url' => 'index.php?page=widgets',
                'label' => _('Back to Widgets'),
                'icon' => 'bi bi-arrow-left',
                'class' => 'btn btn-light border rounded-pill px-4 fw-bold'
            ],
        ],
        'content' => [
            [
                'type' => 'card',
                'title' => _('Block Configuration'),
                'subtitle' => _('Widget Identity: ') . $w->name,
                'body_elements' => [
                    [
                        'type' => 'form',
                        'action' => 'index.php?page=widgets',
                        'fields' => [
                            [
                                'type' => 'row',
                                'items' => [
                                    ['width' => 6, 'content' => ['type' => 'input', 'name' => 'name', 'label' => _("Internal Name"), 'value' => $w->name, 'required' => true]],
                                    ['width' => 6, 'content' => ['type' => 'input', 'name' => 'title', 'label' => _("Display Label"), 'value' => $w->title]]
                                ]
                            ],
                            [
                                'type' => 'row',
                                'items' => [
                                    ['width' => 4, 'content' => ['type' => 'select', 'name' => 'location', 'label' => _("Slot Location"), 'options' => Widget::getLocations(), 'selected' => $w->location]],
                                    [
                                        'width' => 4,
                                        'content' => [
                                            'type' => 'select',
                                            'name' => 'type',
                                            'label' => _("Payload Type"),
                                            'options' => Widget::types(),
                                            'selected' => $w->type
                                        ]
                                    ],
                                    ['width' => 4, 'content' => ['type' => 'input', 'name' => 'sorting', 'label' => _("Sequence Order"), 'input_type' => 'number', 'value' => $w->sorting]]
                                ]
                            ],
                            [
                                'type' => 'raw',
                                'html' => '
                                <div id="widget-content-container-edit" ' . ((Hooks::run('widget_param_form', $w->type) != '') ? 'style="display:none;"' : '') . '>
                                    <label class="form-label fw-bold text-dark small">' . _("Content Data / Callback Hook") . '</label>
                                    <textarea name="content" class="form-control bg-light shadow-none border py-2 px-3 fs-8 fw-bold rounded-4 mb-3" rows="10">' . htmlspecialchars((string)$w->content) . '</textarea>
                                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 extra-small rounded-3 mb-4">
                                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                        ' . _("If type is <strong>Module Hook</strong>, enter the system hook name provided by the developer (e.g., <code>sample_widget_render</code>).") . '
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="' . $w->id . '">
                                <input type="hidden" name="token" value="' . TOKEN . '">
                                <div id="widget-params-container-edit" class="mb-4">' . Hooks::run('widget_param_form', $w->type) . '</div>'
                            ],
                            ['type' => 'button', 'name' => 'edit_widget', 'label' => _("Commit Modifications"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold']
                        ]
                    ]
                ]
            ]
        ]
    ];
} else {
    $schema = [
        'header' => [
            'title' => _('Widgets & Dynamic Blocks'),
            'subtitle' => _('Manage strategic component placement and dynamic content streams.'),
            'icon' => 'bi bi-grid-1x2',
            'button' => [
                'url' => '#',
                'label' => _('New Widget'),
                'icon' => 'bi bi-plus-lg',
                'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
                'attr' => 'data-bs-toggle="modal" data-bs-target="#addWidget"'
            ],
        ],
        'content' => [
            [
                'type' => 'card',
                'no_padding' => true,
                'body_elements' => [
                    [
                        'type' => 'table',
                        'headers' => [
                            ['content' => _('Block Identity'), 'class' => 'ps-4 py-3'],
                            _('Slot Location'),
                            _('Payload Type'),
                            ['content' => _('Sequence'), 'class' => 'text-center'],
                            ['content' => _('Status'), 'class' => 'text-center'],
                            ['content' => _('Actions'), 'class' => 'text-end pe-4']
                        ],
                        'rows' => $rows,
                        'empty_message' => _('Your block registry is currently empty.')
                    ]
                ]
            ],
            // Modal
            [
                'type' => 'modal',
                'id' => 'addWidget',
                'header' => _("Initialize New Widget Block"),
                'size' => 'lg',
                'body_elements' => [
                    [
                        'type' => 'form',
                        'action' => 'index.php?page=widgets',
                        'fields' => [
                            [
                                'type' => 'row',
                                'items' => [
                                    ['width' => 6, 'content' => ['type' => 'input', 'name' => 'name', 'label' => _("Internal Reference"), 'required' => true]],
                                    ['width' => 6, 'content' => ['type' => 'input', 'name' => 'title', 'label' => _("User-Facing Title")]]
                                ]
                            ],
                            [
                                'type' => 'row',
                                'items' => [
                                    ['width' => 4, 'content' => ['type' => 'select', 'name' => 'location', 'label' => _("Destination Slot"), 'options' => Widget::getLocations()]],
                                    [
                                        'width' => 4,
                                        'content' => [
                                            'type' => 'select',
                                            'name' => 'type',
                                            'label' => _("Block Type"),
                                            'options' => Widget::types()
                                        ]
                                    ],
                                    ['width' => 4, 'content' => ['type' => 'input', 'name' => 'sorting', 'label' => _("Ordering"), 'input_type' => 'number', 'value' => '0']]
                                ]
                            ],
                            [
                                'type' => 'raw',
                                'html' => '
                                <div id="widget-content-container-add">
                                    <label class="form-label fw-bold text-dark small">' . _("Content Data / System Hook") . '</label>
                                    <textarea name="content" class="form-control bg-light shadow-none border py-2 px-3 fs-8 fw-bold rounded-4 mb-3" rows="5"></textarea>
                                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 extra-small rounded-3 mb-4">
                                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                        ' . _("If type is <strong>Module Hook</strong>, enter the system hook name provided by the developer (e.g., <code>sample_widget_render</code>).") . '
                                    </div>
                                </div>
                                <input type="hidden" name="token" value="' . TOKEN . '">
                                <div id="widget-params-container-add" class="mb-3"></div>'
                            ],
                            ['type' => 'button', 'name' => 'add_widget', 'label' => _("Deploy Widget"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100 mt-3']
                        ]
                    ]
                ]
            ]
        ]
    ];
}

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

$builder = new UiBuilder($schema);
$builder->render();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const handleTypeChange = function(selectEl, containerId, widgetId = 0) {
        if (!selectEl) return;
        selectEl.addEventListener('change', function() {
            const type = this.value;
            const container = document.getElementById(containerId);
            const form = this.closest('form');
            const contentContainer = form.querySelector('[id^="widget-content-container"]');

            if (!type) {
                container.innerHTML = '';
                if (contentContainer) contentContainer.style.display = 'block';
                return;
            }
            
            // Use official Url::ajax() procedures
            const baseUrl = '<?= Url::ajax("widget", "get_params") ?>';
            const url = `${baseUrl}&type=${type}&id=${widgetId}`;
            
            // Show loading state
            container.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div><span class="text-muted extra-small">Loading modular parameters...</span></div>';
            
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.html.trim() !== '') {
                        container.innerHTML = data.html;
                        if (contentContainer) contentContainer.style.display = 'none';
                    } else {
                        container.innerHTML = '';
                        if (contentContainer) contentContainer.style.display = 'block';
                    }
                })
                .catch(err => {
                    console.error('AJAX Error:', err);
                    container.innerHTML = '';
                    if (contentContainer) contentContainer.style.display = 'block';
                });
        });
    };

    // For Edit Page
    const editTypeSelect = document.querySelector('form[action*="widgets"] select[name="type"]:not(#addWidget *)');
    const editWidgetId = document.querySelector('input[name="id"]')?.value || 0;
    handleTypeChange(editTypeSelect, 'widget-params-container-edit', editWidgetId);

    // For Add Modal
    const addTypeSelect = document.querySelector('#addWidget select[name="type"]');
    handleTypeChange(addTypeSelect, 'widget-params-container-add', 0);
});
</script>
