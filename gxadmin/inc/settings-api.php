<?php
/**
 * GeniXCMS - Content Management System
 *
 * @since 1.4.0
 * @version 2.3.0
 */

// ── PREPARE SCHEMA ────────────────────────────────────────────────
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
    'card_wrapper' => false, // We'll manage rows manually for better layout
    'content' => [
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
                                ['type' => 'select', 'label' => _('Active Driver'), 'name' => 'api_backend', 'selected' => Options::v('api_backend') ?: 'php', 'options' => ['php' => _('Standard PHP Engine'), 'go' => _('External Go Service')]],
                                ['type' => 'raw', 'html' => '<div class="form-check form-switch mt-3"><input class="form-check-input" type="checkbox" name="go_service_fallback" ' . (Options::v('go_service_fallback') !== 'off' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("Auto PHP Fallback") . '</label></div>'],
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
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$';
        let result = '';
        const array = new Uint32Array(32);
        window.crypto.getRandomValues(array);
        for (let i = 0; i < 32; i++) {
            result += characters.charAt(array[i] % characters.length);
        }
        document.getElementById(fieldId).value = result;
    }
</script>
