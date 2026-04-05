<?php

// --- Save Settings ---
if (isset($_POST['contact_save'])) {
    if (!Token::validate(Typo::cleanX($_POST['token']))) {
        $alertDanger[] = _('Invalid security token.');
    } else {
        $opt = [
            'notify_email' => Typo::cleanX($_POST['notify_email'] ?? ''),
            'success_msg'  => Typo::cleanX($_POST['success_msg'] ?? ''),
            'show_phone'   => isset($_POST['show_phone']) ? 'on' : 'off',
            'show_address' => isset($_POST['show_address']) ? 'on' : 'off',
            'phone'        => Typo::cleanX($_POST['phone'] ?? ''),
            'address'      => Typo::cleanX($_POST['address'] ?? ''),
        ];
        Options::update('contact_form_options', json_encode($opt));
        $alertSuccess[] = _('Settings saved successfully.');
    }
}

// Uniform Toast System
System::alert(['alertSuccess' => $alertSuccess ?? [], 'alertDanger' => $alertDanger ?? []]);

$opt          = json_decode(Options::get('contact_form_options') ?? '{}', true) ?? [];
$token        = TOKEN;
$site_email   = Options::v('siteemail');
$notify_email = $opt['notify_email'] ?? '';
$success_msg  = $opt['success_msg']  ?? 'Your message was sent, Thank you for contacting Us.';
$show_phone   = ($opt['show_phone']   ?? 'off') === 'on';
$show_address = ($opt['show_address'] ?? 'off') === 'on';
$phone        = $opt['phone']   ?? '';
$address      = $opt['address'] ?? '';
$mod_url      = rtrim(Options::v('siteurl'), '/') . '/mod/contactPage.html';

require_once GX_LIB . '/UiBuilder.class.php';

$schema = [
    'header' => [
        'title'    => 'Contact Form',
        'subtitle' => 'Configure the public-facing contact form and notification preferences.',
        'icon'     => 'bi bi-envelope-at',
    ],
    'default_tab' => 'settings',
    'tabs' => [
        'settings' => ['label' => 'Settings',   'icon' => 'bi bi-gear',  'content' => []],
        'howto'    => ['label' => 'How To Use', 'icon' => 'bi bi-book',  'content' => []],
    ],
];

// --- SETTINGS TAB ---
$schema['tabs']['settings']['content'][] = [
    'type'   => 'form',
    'action' => 'index.php?page=mods&mod=contact-form',
    'hidden' => ['token' => $token],
    'fields' => [
        [
            'type'  => 'row',
            'items' => [
                [
                    'width'   => 8,
                    'content' => [
                        'type'          => 'card',
                        'title'         => 'Email Notification',
                        'icon'          => 'bi bi-bell',
                        'body_elements' => [
                            [
                                'type'       => 'input',
                                'input_type' => 'email',
                                'name'       => 'notify_email',
                                'label'      => 'Recipient Email',
                                'value'      => $notify_email,
                                'placeholder'=> $site_email,
                                'help'       => 'All form submissions will be sent here. Leave blank to use the global site email: <strong>' . htmlspecialchars($site_email) . '</strong>',
                            ],
                            [
                                'type'        => 'input',
                                'name'        => 'success_msg',
                                'label'       => 'Success Message',
                                'value'       => $success_msg,
                                'placeholder' => 'Your message was sent successfully.',
                                'help'        => 'Shown to the visitor after a successful form submission.',
                            ],
                        ],
                    ],
                ],
                [
                    'width'   => 4,
                    'content' => [
                        'type'          => 'card',
                        'title'         => 'Display Options',
                        'icon'          => 'bi bi-layout-text-sidebar',
                        'body_elements' => [
                            [
                                'type'    => 'checkbox',
                                'name'    => 'show_phone',
                                'label'   => 'Show Phone Number',
                                'checked' => $show_phone,
                                'help'    => 'Display a phone number alongside the form.',
                            ],
                            [
                                'type'        => 'input',
                                'name'        => 'phone',
                                'label'       => 'Phone Number',
                                'value'       => $phone,
                                'placeholder' => '+1 (800) 000-0000',
                                'help'        => 'Include country code. Displayed in the contact info sidebar on the public page.',
                            ],
                            [
                                'type'    => 'checkbox',
                                'name'    => 'show_address',
                                'label'   => 'Show Office Address',
                                'checked' => $show_address,
                                'help'    => 'Display a physical address alongside the form.',
                            ],
                            [
                                'type'        => 'input',
                                'name'        => 'address',
                                'label'       => 'Office Address',
                                'value'       => $address,
                                'placeholder' => '123 Main St, City, Country',
                                'help'        => 'Displayed in the contact info sidebar on the public page.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'type'  => 'button',
            'name'  => 'contact_save',
            'label' => 'Save Settings',
            'icon'  => 'bi bi-save',
            'class' => 'btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm mt-2',
        ],
    ],
];

// --- HOW TO USE TAB ---
$schema['tabs']['howto']['content'][] = [
    'type'  => 'row',
    'items' => [
        [
            'width'   => 6,
            'content' => [
                'type'          => 'card',
                'title'         => '1. Add to Menu',
                'icon'          => 'bi bi-list-ul',
                'body_elements' => [
                    ['type' => 'raw', 'html' => '
                    <ol class="ps-3 lh-lg">
                        <li>Go to <strong>Admin &rarr; Menus</strong>.</li>
                        <li>Click <strong>Add Menu Item</strong>.</li>
                        <li>Set <strong>Type</strong> to <kbd>Mod</kbd>.</li>
                        <li>Set <strong>Value</strong> to <kbd>contactPage</kbd>.</li>
                        <li>Choose menu target (e.g. <kbd>mainmenu</kbd> or <kbd>footer</kbd>).</li>
                        <li>Save — the link points to your Contact page automatically.</li>
                    </ol>
                    <div class="alert alert-info mt-3 rounded-3 small">
                        <i class="bi bi-link-45deg me-1"></i> Public URL:<br>
                        <a href="' . $mod_url . '" target="_blank" class="fw-bold">' . $mod_url . '</a>
                    </div>'],
                ],
            ],
        ],
        [
            'width'   => 6,
            'content' => [
                'type'          => 'card',
                'title'         => '2. How It Works',
                'icon'          => 'bi bi-diagram-3',
                'body_elements' => [
                    [
                        'type'    => 'table',
                        'headers' => ['Feature', 'Details'],
                        'rows'    => [
                            ['Email Target',     'Configured recipient, or global site email'],
                            ['CSRF Security',    'Every submission protected by a Token'],
                            ['Field Validation', 'Name, Email, Subject & Message are required'],
                            ['Captcha',          'Auto-enabled if reCAPTCHA is configured'],
                            ['Hook ID',          '<code>contactPage</code>'],
                            ['Folder',           '<code>inc/mod/contact-form/</code>'],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$builder = new UiBuilder($schema);
$builder->render();
