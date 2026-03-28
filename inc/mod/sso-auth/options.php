<?php
if (isset($_POST['save_sso'])) {
    if (!Token::validate($_POST['token'])) {
        $alertDanger[] = _("Invalid token.");
    } else {
        Options::update('sso_google_client_id', $_POST['sso_google_client_id']);
        Options::update('sso_fb_app_id', $_POST['sso_fb_app_id']);
        Options::update('sso_github_client_id', $_POST['sso_github_client_id']);
        Options::update('sso_github_secret', $_POST['sso_github_secret']);
        Options::update('sso_x_client_id', $_POST['sso_x_client_id']);
        Options::update('sso_x_secret', $_POST['sso_x_secret']);
        Options::update('sso_apple_client_id', $_POST['sso_apple_client_id']);
        Options::update('sso_apple_team_id', $_POST['sso_apple_team_id']);
        Options::update('sso_apple_key_id', $_POST['sso_apple_key_id']);
        Options::update('sso_apple_private_key', $_POST['sso_apple_private_key']);
        
        $alertSuccess[] = _("Multi-Provider SSO configuration securely updated.");
    }
}

$schema = [
    'header' => [
        'title' => _('Unified SSO Hub'),
        'subtitle' => _('Secure Multi-Provider Authentication Matrix'),
        'icon' => 'bi bi-shield-lock'
    ],
    'content' => [
        [
            'type' => 'card',
            'title' => _('Authentication Providers'),
            'subtitle' => _('Define your OAuth 2.0 metrics for global SSO. Leave any client ID blank to disable that specific provider.'),
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => '',
                    'fields' => [
                        // Google
                        [
                            'type' => 'raw',
                            'html' => '<h6 class="fw-bold mt-2 text-primary border-bottom pb-2"><i class="bi bi-google me-2"></i>Google Identity Services</h6>'
                        ],
                        [
                            'type' => 'input',
                            'name' => 'sso_google_client_id',
                            'label' => _("Google Client ID"),
                            'value' => Options::v('sso_google_client_id')
                        ],

                        // Facebook
                        [
                            'type' => 'raw',
                            'html' => '<h6 class="fw-bold mt-4 text-primary border-bottom pb-2"><i class="bi bi-facebook me-2"></i>Facebook Login</h6>'
                        ],
                        [
                            'type' => 'input',
                            'name' => 'sso_fb_app_id',
                            'label' => _("Facebook App ID"),
                            'value' => Options::v('sso_fb_app_id')
                        ],

                        // GitHub
                        [
                            'type' => 'raw',
                            'html' => '<h6 class="fw-bold mt-4 text-primary border-bottom pb-2"><i class="bi bi-github me-2"></i>GitHub Auth</h6>'
                        ],
                        ['type' => 'row', 'items' => [
                            ['width' => 6, 'content' => ['type' => 'input', 'name' => 'sso_github_client_id', 'label' => _("GitHub Client ID"), 'value' => Options::v('sso_github_client_id')]],
                            ['width' => 6, 'content' => ['type' => 'input', 'name' => 'sso_github_secret', 'label' => _("GitHub Client Secret"), 'value' => Options::v('sso_github_secret'), 'input_type' => 'password']]
                        ]],

                        // X (Twitter)
                        [
                            'type' => 'raw',
                            'html' => '<h6 class="fw-bold mt-4 text-primary border-bottom pb-2"><i class="bi bi-twitter-x me-2"></i>X (Twitter) OAuth 2.0</h6>'
                        ],
                        ['type' => 'row', 'items' => [
                            ['width' => 6, 'content' => ['type' => 'input', 'name' => 'sso_x_client_id', 'label' => _("X Client ID"), 'value' => Options::v('sso_x_client_id')]],
                            ['width' => 6, 'content' => ['type' => 'input', 'name' => 'sso_x_secret', 'label' => _("X Client Secret"), 'value' => Options::v('sso_x_secret'), 'input_type' => 'password']]
                        ]],

                        // Apple
                        [
                            'type' => 'raw',
                            'html' => '<h6 class="fw-bold mt-4 text-primary border-bottom pb-2"><i class="bi bi-apple me-2"></i>Sign in with Apple</h6>'
                        ],
                        ['type' => 'row', 'items' => [
                            ['width' => 4, 'content' => ['type' => 'input', 'name' => 'sso_apple_client_id', 'label' => _("Apple Client ID (Services ID)"), 'value' => Options::v('sso_apple_client_id')]],
                            ['width' => 4, 'content' => ['type' => 'input', 'name' => 'sso_apple_team_id', 'label' => _("Apple Team ID"), 'value' => Options::v('sso_apple_team_id')]],
                            ['width' => 4, 'content' => ['type' => 'input', 'name' => 'sso_apple_key_id', 'label' => _("Key ID"), 'value' => Options::v('sso_apple_key_id')]]
                        ]],
                        [
                            'type' => 'textarea',
                            'name' => 'sso_apple_private_key',
                            'label' => _("Apple Private Key (.p8 contents)"),
                            'value' => Options::v('sso_apple_private_key'),
                            'rows' => 4
                        ],

                        // Actions
                        [
                            'type' => 'raw',
                            'html' => '<div class="alert alert-info border-0 bg-info bg-opacity-10 extra-small rounded-3 mb-4 mt-4"><i class="bi bi-info-circle-fill me-2 fs-5"></i>'._("The unified callback URI for all severe-side implementations (GitHub, X, Apple) is: <strong class=\'text-dark\'>" . Site::$url . "index.php?sso_handler=callback</strong>").'</div><input type="hidden" name="token" value="'.TOKEN.'">'
                        ],
                        [
                            'type' => 'button',
                            'name' => 'save_sso',
                            'label' => _("Deploy Architecture"),
                            'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100'
                        ]
                    ]
                ]
            ]
        ]
    ]
];

if (isset($alertSuccess) || isset($alertDanger)) {
    System::alert(['alertSuccess' => $alertSuccess ?? null, 'alertDanger' => $alertDanger ?? null]);
}
$builder = new UiBuilder($schema);
$builder->render();
?>
