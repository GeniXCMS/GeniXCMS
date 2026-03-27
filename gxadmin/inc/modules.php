<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$rows = [];
if (count($data['mods']) > 0) {
    foreach ($data['mods'] as $mod) {
        $m = Mod::data($mod);
        $isActive = Mod::isActive($mod);
        $statusClass = $isActive ? 'success' : 'secondary';
        $btnClass = $isActive ? 'warning' : 'success';
        $actLabel = $isActive ? _("Deactivate") : _("Activate");
        $actIcon = $isActive ? 'bi-toggle-on' : 'bi-toggle-off';
        $actUri = $isActive ? 'deactivate' : 'activate';
        $icon = (isset($m['icon']) ? $m['icon'] : 'bi bi-puzzle');

        $rows[] = [
            ['content' => "
                <div class='d-flex align-items-center ps-4 py-3'>
                    <div class='bg-{$statusClass} bg-opacity-10 p-3 rounded-4 text-{$statusClass} me-3 shadow-sm'>
                        <i class='{$icon} fs-4'></i>
                    </div>
                    <div>
                        <div class='fw-bold text-dark h6 mb-1'>{$m['name']}</div>
                        <div class='d-flex gap-2 align-items-center'>
                            <span class='badge bg-light text-muted border extra-small px-3 py-1 rounded-pill fw-bold'>v{$m['version']}</span>
                            <span class='extra-small text-muted opacity-50 fw-bold text-uppercase ls-1'>{$m['license']}</span>
                        </div>
                    </div>
                </div>", 'class' => 'p-0'],
            "<div>
                <p class='text-muted small mb-1 lh-base' style='max-width: 400px;'>".(strlen($m['desc']) > 140 ? substr($m['desc'], 0, 137).'...' : $m['desc'])."</p>
                <div class='extra-small text-muted d-flex align-items-center gap-1'>
                    <i class='bi bi-person-circle'></i> "._("Authored by").": <a href='{$m['url']}' target='_blank' class='text-primary fw-bold text-decoration-none'>{$m['developer']}</a>
                </div>
             </div>",
            ['content' => "
                <div class='btn-group gap-2'>
                    <a href='index.php?page=modules&act={$actUri}&modules={$mod}&token=".TOKEN."' 
                       class='btn btn-{$btnClass} btn-sm rounded-pill px-4 shadow-sm d-inline-flex align-items-center fw-bold'>
                        <i class='bi {$actIcon} me-2'></i> {$actLabel}
                    </a>
                    " . (!$isActive ? "
                    <a href='index.php?page=modules&act=remove&modules={$mod}&token=".TOKEN."' 
                       class='btn btn-light btn-sm rounded-circle border p-2' 
                       onclick=\"return confirm('"._("Permanent removal of this module?")."');\" title='Remove Module'>
                        <i class='bi bi-trash text-danger'></i>
                    </a>" : "") . "
                </div>", 'class' => 'text-end pe-4']
        ];
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Plugin Ecosystem'),
        'subtitle' => _('Extend core features and add new capabilities with modular extensions.'),
        'icon' => 'bi bi-cpu',
        'button' => [
            'url' => '#',
            'label' => _('New Module'),
            'icon' => 'bi bi-plus-circle-fill',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#installModal"'
        ],
    ],
    'content' => [
        [
            'type' => 'card',
            'no_padding' => true,
            'title' => _('Installed Extensions'),
            'subtitle' => _('Manage system extensions and operational status.'),
            'icon' => 'bi bi-cpu',
            'body_elements' => [
                [
                    'type' => 'table',
                    'headers' => [
                        ['content' => _('Extension Identity'), 'class' => 'ps-4 py-3'],
                        _('Capability & Origin'),
                        ['content' => _('Operational Control'), 'class' => 'text-end pe-4']
                    ],
                    'rows' => $rows,
                    'empty_message' => _('No modular extensions found in your library.')
                ]
            ],
            'footer' => '
                <div class="extra-small text-muted text-uppercase tracking-widest fw-bold d-flex align-items-center mx-3 my-1">
                    <i class="bi bi-info-circle-fill me-2 text-primary fs-5"></i>
                    '._("Activated modules may add new operational nodes to your administrative sidebar.").'
                </div>'
        ],
        // Modal
        [
            'type' => 'modal',
            'id' => 'installModal',
            'header' => _("Deploy New Module Package"),
            'size' => 'md',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php?page=modules',
                    'fields' => [
                        ['type' => 'raw', 'html' => '
                            <div class="upload-zone border-2 border-dashed rounded-5 p-5 mb-4 bg-light text-center">
                                <i class="bi bi-plugin text-success fs-1 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">'._("Upload Module Archive").'</h6>
                                <p class="extra-small text-muted mb-4">'._("Select the .zip package to expand and install").'</p>
                                <input type="file" name="module" id="modFile" class="form-control border-0 bg-white rounded-pill px-4 py-2 border shadow-sm">
                            </div>
                            <div class="alert bg-info bg-opacity-10 border-0 rounded-4 p-3 extra-small mb-4 d-flex">
                                <i class="bi bi-shield-check text-info fs-5 me-3"></i>
                                <div class="text-dark opacity-75">
                                    <strong>Installation Note:</strong> Modules are executed with core system privileges. Ensure your package is from a verified developer or the official marketplace.
                                </div>
                            </div>
                            <input type="hidden" name="token" value="'.TOKEN.'">'],
                        ['type' => 'button', 'name' => 'upload', 'label' => _("Initialize Installation"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100 shadow-sm']
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
    .upload-zone { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); border-color: #e2e8f0 !important; }
    .upload-zone:hover { background-color: #fff !important; border-color: var(--gx-primary) !important; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .ls-1 { letter-spacing: 0.5px; }
    .tracking-widest { letter-spacing: 0.1em; }
</style>
