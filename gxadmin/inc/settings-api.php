<?php
/**
 * GeniXCMS - Content Management System
 *
 * @since 1.4.0
 * @version 2.4.0
 */

// ── DETECT GO SERVICE ─────────────────────────────────────────────
$goServicePath    = GX_PATH . '/go-service';
$goServiceExists  = is_dir($goServicePath);

// If go-service folder is missing, force driver back to php
if (!$goServiceExists && Options::v('api_backend') === 'go') {
    Options::update('api_backend', 'php');
}

// ── GO SERVICE STATUS BANNER ──────────────────────────────────────
$goStatusBanner = '';
if (!$goServiceExists) {
    $goStatusBanner = '
    <div class="alert border-0 rounded-4 mb-4 d-flex align-items-start gap-3"
         style="background:rgba(245,158,11,.08);border-left:4px solid #f59e0b !important;border-left-width:4px !important">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 flex-shrink-0 mt-1"></i>
        <div class="flex-grow-1">
            <div class="fw-bold text-dark mb-1">' . _('Go Microservice Not Installed') . '</div>
            <p class="extra-small text-muted mb-3 lh-base">' . _('The <code>go-service/</code> folder was not found in your installation root. The Go backend is disabled. Download and extract the Go service package to enable high-performance mode.') . '</p>
            <button type="button" id="btn-download-go" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold shadow-sm"
                    onclick="downloadGoService()">
                <i class="bi bi-cloud-download me-2"></i>' . _('Download Go Service') . '
            </button>
            <span id="go-dl-status" class="ms-3 extra-small text-muted"></span>
        </div>
    </div>';
} else {
    // Check for executable or binary
    $goBinary = file_exists($goServicePath . '/gxservice') || file_exists($goServicePath . '/gxservice.exe')
             || file_exists($goServicePath . '/main.go') || file_exists($goServicePath . '/main');
    $goStatusBanner = '
    <div class="alert border-0 rounded-4 mb-4 d-flex align-items-center gap-3"
         style="background:rgba(16,185,129,.08);border-left:4px solid #10b981 !important">
        <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
        <div>
            <span class="fw-bold text-dark">' . _('Go Service Installed') . '</span>
            <span class="extra-small text-muted ms-2">' . htmlspecialchars($goServicePath) . '</span>
        </div>
    </div>';
}

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$driverOptions = ['php' => _('Standard PHP Engine')];
if ($goServiceExists) {
    $driverOptions['go'] = _('External Go Service');
}

$schema = [
    'header' => [
        'title' => _('API Service Controller'),
        'subtitle' => _('Manage your RESTful ecosystem and high-performance Go backend connectivity.'),
        'icon' => 'bi bi-cpu',
        'button' => [
            'type' => 'button',
            'name' => 'change',
            'label' => _('Save API Config'),
            'icon' => 'bi bi-check2-all',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'value="Change"'
        ],
    ],
    'card_wrapper' => false,
    'content' => [
        [
            'type' => 'raw',
            'html' => $goStatusBanner,
        ],
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 4,
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => _('Primary Backend'),
                            'icon' => 'bi bi-toggle-on',
                            'body_elements' => [
                                [
                                    'type' => 'raw',
                                    'html' => '
                                    <label class="form-label fw-bold text-dark small">' . _('Active Driver') . '</label>
                                    <select name="api_backend" class="form-select rounded-4 bg-light shadow-none border py-2 px-3 fs-8 fw-bold"'
                                        . (!$goServiceExists ? ' disabled title="' . _('Install Go Service to enable this option') . '"' : '') . '>
                                        ' . implode('', array_map(
                                            fn($v, $l) => '<option value="' . $v . '"' . ((Options::v('api_backend') ?: 'php') === $v ? ' selected' : '') . '>' . $l . '</option>',
                                            array_keys($driverOptions), $driverOptions
                                        )) . '
                                    </select>'
                                    . (!$goServiceExists ? '<p class="extra-small text-muted mt-2 mb-0"><i class="bi bi-lock me-1"></i>' . _('Locked — Go service not installed.') . '</p>' : ''),
                                ],
                                ['type' => 'raw', 'html' => '<div class="form-check form-switch mt-3"><input class="form-check-input" type="checkbox" name="go_service_fallback" ' . (Options::v('go_service_fallback') !== 'off' ? 'checked' : '') . (!$goServiceExists ? ' disabled' : '') . '><label class="form-check-label fw-bold">' . _("Auto PHP Fallback") . '</label></div>'],
                            ],
                            'footer' => '<p class="extra-small text-muted mb-0">' . _("Fallback automatically reverts to PHP if Go service is unreachable.") . '</p>'
                        ]
                    ]
                ],
                [
                    'width' => 8,
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => _('Go Microservice Configuration'),
                            'icon' => 'bi bi-diagram-3',
                            'body_elements' => [
                                ['type' => 'input', 'label' => _('Service Endpoint URL'), 'name' => 'go_service_url', 'value' => Options::v('go_service_url'), 'placeholder' => 'http://localhost:8080'],
                                [
                                    'type' => 'raw',
                                    'html' => '
                                    <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("Shared Authorization Secret") . '</label>
                                    <div class="input-group">
                                        <input type="text" name="go_service_secret" id="go_secret_field" value="' . Options::v('go_service_secret') . '" class="form-control border bg-light shadow-none rounded-start-4 py-2 px-3 fs-7 fw-bold font-monospace">
                                        <button class="btn btn-white border px-4 rounded-end-4 hover-shadow" type="button" onclick="generateSecret(\'go_secret_field\')" title="' . _("Regenerate Secret") . '"><i class="bi bi-shield-shaded text-primary h4 mb-0"></i></button>
                                    </div>
                                    <div class="mt-3 p-3 bg-light rounded-4 border-start border-4 border-primary">
                                        <p class="extra-small text-muted mb-0 lh-base">' . _("The secret key must match the GX_SECRET value in your Go service .env file.") . '</p>
                                    </div>'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 12,
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => _('Public RESTful Access Control'),
                            'icon' => 'bi bi-file-earmark-lock',
                            'body_elements' => [
                                [
                                    'type' => 'raw',
                                    'html' => '
                        <div class="p-4 bg-danger bg-opacity-10 rounded-5 border-start border-5 border-danger mb-4">
                            <h6 class="fw-black text-danger text-uppercase mb-2">' . _("Security Clearance Required") . '</h6>
                            <p class="extra-small text-danger text-opacity-75 mb-0 lh-base fw-bold">' . _("This token allows programmatic access to your entire data ecosystem. Handle with care.") . '</p>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("Active RESTful Token") . '</label>
                                <div class="input-group">
                                    <input type="text" name="api_key" id="api_key_field" value="' . Options::v('api_key') . '" class="form-control border bg-light shadow-none rounded-start-4 py-2 px-3 fs-7 fw-bold font-monospace">
                                    <button class="btn btn-white border px-4 rounded-end-4 hover-shadow" type="button" onclick="generateSecret(\'api_key_field\')" title="' . _("Regenerate Token") . '"><i class="bi bi-arrow-repeat text-primary h4 mb-0"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("API Throughput Limit") . '</label>
                                <div class="input-group">
                                    <span class="input-group-text border bg-white rounded-start-4"><i class="bi bi-speedometer2 text-muted"></i></span>
                                    <input type="number" name="api_rate_limit" value="' . (Options::v('api_rate_limit') ?: '100') . '" class="form-control border bg-light shadow-none rounded-end-4 py-2 fs-7 fw-bold">
                                </div>
                            </div>
                        </div>'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 12,
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => _('Go Backend Security (Whitelist)'),
                            'icon' => 'bi bi-shield-lock',
                            'body_elements' => [
                                [
                                    'type' => 'textarea',
                                    'label' => _('Allowed Resources Whitelist'),
                                    'name' => 'go_service_whitelist',
                                    'placeholder' => 'posts, categories, tags, widgets, nix_products',
                                    'help' => _('List the database tables (comma separated) that are allowed to be accessed via the Go Service. Core tables like posts, categories, and tags are recommended for performance. Leave empty to allow standard core tables only.'),
                                    'value' => Options::v('go_service_whitelist')
                                ],
                            ]
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

echo '<form action="" method="POST" enctype="multipart/form-data">';
$builder = new UiBuilder($schema);
$builder->render();
echo '<input type="hidden" name="token" value="' . TOKEN . '">';
echo '</form>';
?>

<script>
function generateSecret(fieldId) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let result = '';
    const arr = new Uint32Array(32);
    window.crypto.getRandomValues(arr);
    for (let i = 0; i < 32; i++) result += chars.charAt(arr[i] % chars.length);
    document.getElementById(fieldId).value = result;
}

async function downloadGoService() {
    const btn    = document.getElementById('btn-download-go');
    const status = document.getElementById('go-dl-status');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span><?= _('Fetching info...') ?>';
    status.textContent = '';

    try {
        // 1. Fetch download info from GeniXCMS marketplace API
        const infoRes = await fetch('https://genixcms.web.id/api/v1/download/go-service', {
            headers: { 'X-GeniXCMS-Version': '<?= System::$version ?>' }
        });
        if (!infoRes.ok) throw new Error('<?= _('API unreachable') ?> (' + infoRes.status + ')');
        const info = await infoRes.json();

        if (!info.download_url) throw new Error('<?= _('No download URL returned') ?>');

        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span><?= _('Downloading & installing...') ?>';
        status.textContent = 'v' + (info.version || '?');

        // 2. Trigger server-side download + extract via AJAX
        const installRes = await fetch('<?= Url::ajax('updates') ?>&action=install_go_service&url=' + encodeURIComponent(info.download_url) + '&token=<?= TOKEN ?>');
        const result = await installRes.json();

        if (result.status === 'success') {
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i><?= _('Installed!') ?>';
            btn.className = btn.className.replace('btn-warning', 'btn-success');
            status.textContent = '<?= _('Reloading...') ?>';
            setTimeout(() => location.reload(), 1500);
        } else {
            throw new Error(result.message || '<?= _('Installation failed') ?>');
        }
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-cloud-download me-2"></i><?= _('Retry Download') ?>';
        status.textContent = '⚠ ' + err.message;
        status.style.color = '#ef4444';
    }
}
</script>
