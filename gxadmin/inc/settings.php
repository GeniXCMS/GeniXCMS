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

// ── SYSTEM METADATA PREPARATION ───────────────────────────────────
$sys_v = System::v();
$php_v = phpversion();
$mysql_v = Db::$v ?? '8.0+';

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Infrastructure & Ecosystem'),
        'subtitle' => _('Core architectural control center for high-performance deployment and digital identity.'),
        'icon' => 'bi bi-command',
        'button' => [
            'type' => 'button',
            'name' => 'change',
            'label' => _('Update Framework'),
            'icon' => 'bi bi-magic',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'value="Change"'
        ],
    ],
    'card_wrapper' => true,
    'tab_mode' => 'js',
    'tab_style' => 'modern',
    'tabs' => [
        'general' => [
            'label' => _('General'),
            'icon' => 'bi bi-grid-1x2',
            'content' => [
                ['type' => 'heading', 'text' => _('Platform Signature'), 'icon' => 'bi bi-person-badge', 'subtitle' => _('Defines the primary digital footprint of your ecosystem.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 8,
                            'content' => [
                                [
                                    'type' => 'row',
                                    'items' => [
                                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Platform Name'), 'name' => 'sitename', 'value' => Options::v('sitename')]],
                                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Marketing Slogan'), 'name' => 'siteslogan', 'value' => Options::v('siteslogan')]],
                                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Domain Authority'), 'name' => 'sitedomain', 'value' => Options::v('sitedomain'), 'placeholder' => 'example.org']],
                                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Base URL Path'), 'name' => 'siteurl', 'value' => Options::v('siteurl'), 'placeholder' => 'http://www.example.org/']],
                                    ]
                                ],
                                ['type' => 'input', 'label' => _('Discovery Keywords'), 'name' => 'sitekeywords', 'value' => Options::v('sitekeywords')],
                                ['type' => 'textarea', 'label' => _('Meta Description for Search Engines'), 'name' => 'sitedesc', 'value' => Options::v('sitedesc'), 'rows' => 3],
                                ['type' => 'select', 'label' => _('Admin Navigation Layout'), 'name' => 'admin_layout_type', 'selected' => Options::v('admin_layout_type') ?: 'sidebar', 'options' => ['sidebar' => _('Classic Sidebar'), 'top' => _('Top Navigation')]],
                            ]
                        ],
                        [
                            'width' => 4,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('Performance Meta'),
                                    'icon' => 'bi bi-cpu',
                                    'body_elements' => [
                                        [
                                            'type' => 'raw',
                                            'html' => '
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="extra-small fw-bold text-muted">FRAMEWORK</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill fw-black" style="font-size:0.65rem;">v' . $sys_v . '</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="extra-small fw-bold text-muted">PHP RUNTIME</span>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-black" style="font-size:0.65rem;">' . $php_v . '</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="extra-small fw-bold text-muted">DATABASE</span>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill fw-black" style="font-size:0.65rem;">' . $mysql_v . '</span>
                                </div>
                                <div class="mt-4 p-3 bg-light rounded-4">
                                    <h6 class="extra-small fw-black text-dark text-uppercase mb-2"><i class="bi bi-lightbulb me-1"></i> Pro-Tip</h6>
                                    <p class="extra-small text-muted mb-0 lh-base">' . _("Keep your platform slogan concise (under 60 chars) to ensure perfect visibility in social sharing previews.") . '</p>
                                </div>'
                                        ],
                                        ['type' => 'input', 'name' => 'api_rate_limit', 'label' => _('Rate Limit (Requests / Hour)'), 'value' => Options::v('api_rate_limit') ?: '100', 'input_type' => 'number'],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ],
        'localization' => [
            'label' => _('Localization'),
            'icon' => 'bi bi-translate',
            'content' => [
                ['type' => 'heading', 'text' => _('Regional Authority'), 'icon' => 'bi bi-globe', 'subtitle' => _('Synchronize chronos and language protocols with your primary market.')],
                [
                    'type' => 'row',
                    'items' => [
                        ['width' => 6, 'content' => ['type' => 'raw', 'html' => '<label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("Nexus Country") . '</label><select name="country_id" class="form-select border bg-light rounded-4 py-2 px-3 shadow-none fs-8 fw-bold mb-4">' . Date::optCountry(Options::v('country_id')) . '</select>']],
                        ['width' => 6, 'content' => ['type' => 'raw', 'html' => '<label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("Chronological Timezone") . '</label><select name="timezone" class="form-select border bg-light rounded-4 py-2 px-3 shadow-none fs-8 fw-bold mb-4">' . Date::optTimeZone(Options::v('timezone')) . '</select>']],
                    ]
                ],
                [
                    'type' => 'row',
                    'items' => [
                        ['width' => 6, 'content' => ['type' => 'raw', 'html' => '<label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("System Linguistics") . '</label><select name="system_lang" class="form-select border bg-light rounded-4 py-2 px-3 shadow-none fs-8 fw-bold mb-4">' . Language::optDropdown(Options::v('system_lang')) . '</select>']],
                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Character Encoding'), 'name' => 'charset', 'value' => Options::v('charset'), 'placeholder' => 'UTF-8']],
                    ]
                ],
                [
                    'type' => 'raw',
                    'html' => '
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-5 p-4 d-flex align-items-center mt-3">
                        <div class="rounded-circle bg-primary bg-opacity-25 p-3 me-4"><i class="bi bi-info-square-fill text-primary h4 mb-0"></i></div>
                        <div>
                            <h6 class="fw-black text-primary mb-1">Localization Intelligence</h6>
                            <p class="extra-small text-primary text-opacity-75 mb-0 fw-bold">' . _("Changing the system language will update the entire administration interface instantly. Ensure the chosen locale is fully indexed in the /inc/lang/ registry.") . '</p>
                        </div>
                    </div>'
                ]
            ]
        ],
        'email' => [
            'label' => _('Transmission'),
            'icon' => 'bi bi-send-check',
            'content' => [
                ['type' => 'heading', 'text' => _('Mail Distribution Architecture'), 'icon' => 'bi bi-envelope-check', 'subtitle' => _('Configure how your server handles outbound transactional transmissions.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 5,
                            'content' => [
                                ['type' => 'select', 'label' => _('Transport Layer Protocol'), 'name' => 'mailtype', 'selected' => Options::v('mailtype'), 'options' => ['0' => _('Standard PHP Mailer'), '1' => _('SMTP Distribution Relay')]],
                                [
                                    'type' => 'raw',
                                    'html' => '
                        <div class="p-3 bg-light rounded-4 border-start border-4 border-info mt-3">
                            <h6 class="extra-small fw-black text-info text-uppercase mb-2">Protocol Note</h6>
                            <p class="extra-small text-muted mb-0 lh-base">' . _("SMTP is highly recommended for newsletters to bypass strict SPAM filtering mechanisms applied by mail providers like Gmail or Outlook.") . '</p>
                        </div>'
                                ]
                            ]
                        ],
                        [
                            'width' => 7,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('SMTP Relay Credentials'),
                                    'icon' => 'bi bi-key',
                                    'body_elements' => [
                                        ['type' => 'input', 'label' => _('Relay Hostname'), 'name' => 'smtphost', 'value' => Options::v('smtphost'), 'placeholder' => 'smtp.provider.com'],
                                        [
                                            'type' => 'row',
                                            'items' => [
                                                ['width' => 4, 'content' => ['type' => 'input', 'label' => _('Port'), 'name' => 'smtpport', 'value' => Options::v('smtpport'), 'placeholder' => '587']],
                                                ['width' => 8, 'content' => ['type' => 'input', 'label' => _('Distribution Account'), 'name' => 'siteemail', 'value' => Options::v('siteemail'), 'input_type' => 'email']],
                                            ]
                                        ],
                                        [
                                            'type' => 'row',
                                            'items' => [
                                                ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Username'), 'name' => 'smtpuser', 'value' => Options::v('smtpuser')]],
                                                ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Authentication Secret'), 'name' => 'smtppass', 'value' => Options::v('smtppass'), 'input_type' => 'password']],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'social' => [
            'label' => _('Social'),
            'icon' => 'bi bi-share',
            'content' => [
                ['type' => 'heading', 'text' => _('Connected Networks'), 'icon' => 'bi bi-link-45deg', 'subtitle' => _('Link your official social media handles to boost platform visibility.')],
                [
                    'type' => 'row',
                    'items' => [
                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Facebook Account'), 'name' => 'fbacc', 'value' => Options::v('fbacc')]],
                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Facebook Page'), 'name' => 'fbpage', 'value' => Options::v('fbpage')]],
                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('X / Twitter Handle'), 'name' => 'twitter', 'value' => Options::v('twitter')]],
                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('LinkedIn Profile'), 'name' => 'linkedin', 'value' => Options::v('linkedin')]],
                    ]
                ],
                ['type' => 'alert', 'style' => 'light', 'content' => '<i class="bi bi-info-circle me-1"></i> ' . _("Social links are used by several themes to display your presence in the footer or sidebar areas.")]
            ]
        ],
        'identity' => [
            'label' => _('Identity'),
            'icon' => 'bi bi-gem',
            'content' => [
                ['type' => 'heading', 'text' => _('Vision & Branding'), 'icon' => 'bi bi-image', 'subtitle' => _('Upload logos and favicons to provide a cohesive visual experience.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'raw',
                                    'html' => '
                            <div class="bg-light rounded-4 p-5 text-center mb-0 h-100 d-flex flex-column justify-content-center align-items-center">
                                <div class="logo_preview p-3 mb-4 cursor-pointer d-inline-block shadow-none rounded bg-white border" id="fileBrowse" onclick="uploadLogo()">
                                    ' . (Options::v('logo') ? '<img src="' . Site::$url . Options::v('logo') . '" class="img-fluid" id="logo_preview" style="max-height: 120px;">' : '<div class="py-5 px-5"><i class="bi bi-image fs-1 text-muted opacity-25"></i></div>') . '
                                </div>
                                <input type="file" id="ImageBrowse" name="file" hidden>
                                <input type="hidden" name="logo" id="logo_image" value="' . Options::v('logo') . '">
                                <h6 class="fw-black text-dark text-uppercase tracking-widest fs-8 mb-2">' . _("Core Platform Logo") . '</h6>
                                <p class="extra-small text-muted mb-0 lh-base">' . _("Recommended height: 80-120px. PNG or SVG targets provide the best clarity for high-DPI displays.") . '</p>
                            </div>'
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'body_elements' => [
                                        [
                                            'type' => 'raw',
                                            'html' => '
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_logourl" id="useLogoUrl" ' . (Options::v('is_logourl') == 'on' ? 'checked' : '') . '>
                                    <label class="form-check-label fw-bold" for="useLogoUrl">' . _("External Logo URL Path") . '</label>
                                </div>'
                                        ],
                                        ['type' => 'input', 'name' => 'logourl', 'value' => Options::v('logourl'), 'placeholder' => 'https://cdn.example.com/logo.png'],
                                        ['type' => 'input', 'label' => _('Favicon Icon Path'), 'name' => 'siteicon', 'value' => Options::v('siteicon'), 'placeholder' => 'favicon.ico']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                ['type' => 'textarea', 'label' => _('Admin Custom CSS'), 'name' => 'admin_custom_css', 'value' => Options::v('admin_custom_css'), 'rows' => 6, 'class' => 'form-control bg-dark text-light font-monospace fs-8 border', 'placeholder' => '/* Inject custom CSS for the dashboard here */']
            ]
        ],
        'assets' => [
            'label' => _('Assets'),
            'icon' => 'bi bi-boxes',
            'content' => [
                ['type' => 'heading', 'text' => _('Asset Infrastructure'), 'icon' => 'bi bi-hdd-network', 'subtitle' => _('Control the foundational libraries and build pipeline optimizations.')],
                ['type' => 'input', 'label' => _('Content Distribution URL (CDN)'), 'name' => 'cdn_url', 'value' => Options::v('cdn_url'), 'placeholder' => 'https://cdn.mysite.com/'],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 4,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="use_bootstrap" ' . (Options::v('use_bootstrap') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("Bootstrap Engine") . '</label></div>'],
                                        ['type' => 'input', 'name' => 'bootstrap_v', 'value' => Options::v('bootstrap_v'), 'label' => _('Framework Version')]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 4,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="use_jquery" ' . (Options::v('use_jquery') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("jQuery Module") . '</label></div>'],
                                        ['type' => 'input', 'name' => 'jquery_v', 'value' => Options::v('jquery_v'), 'label' => _('Framework Version')]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 4,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="use_fontawesome" ' . (Options::v('use_fontawesome') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("FontAwesome") . '</label></div>'],
                                        ['type' => 'input', 'name' => 'fontawesome_v', 'value' => Options::v('fontawesome_v'), 'label' => _('Framework Version')]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'card',
                    'title' => _('Modern Build Pipeline (Vite)'),
                    'icon' => 'bi bi-lightning',
                    'body_elements' => [
                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="vite_dev_mode" ' . (Options::v('vite_dev_mode') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("Engineered Hot Reload") . '</label></div>'],
                        ['type' => 'input', 'label' => _('Development Server Endpoint'), 'name' => 'vite_dev_server', 'value' => Options::v('vite_dev_server')],
                        [
                            'type' => 'raw',
                            'html' => '
                        <div class="mt-3 p-3 bg-primary bg-opacity-10 rounded-4 border-start border-4 border-primary">
                            <h6 class="extra-small fw-black text-primary text-uppercase mb-2"><i class="bi bi-gear-fill me-1"></i> Pipeline Intel</h6>
                            <p class="extra-small text-muted mb-0 lh-base">' . _("The Vite pipeline provides instant feedback during theme development via HMR. When enabled, your changes are pushed to the browser in real-time, bypassing full page reloads.") . '</p>
                        </div>'
                        ]
                    ]
                ]
            ]
        ],
        'posts' => [
            'label' => _('Registry'),
            'icon' => 'bi bi-journal-check',
            'content' => [
                ['type' => 'heading', 'text' => _('Content Archiving Protocol'), 'icon' => 'bi bi-pencil-square', 'subtitle' => _('Dictates how the primary content registry interacts with the data center.')],
                [
                    'type' => 'row',
                    'items' => [
                        ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Database Records Per View'), 'name' => 'post_perpage', 'value' => Options::v('post_perpage'), 'input_type' => 'number']],
                        ['width' => 6, 'content' => ['type' => 'select', 'label' => _('Pagination Interface Interface'), 'name' => 'pagination', 'selected' => Options::v('pagination'), 'options' => ['number' => _('Sequential Digits'), 'pager' => _('Minimalist Pager')]]],
                    ]
                ],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('Authoring Pipeline'),
                                    'icon' => 'bi bi-brush',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="use_editor" ' . (Options::v('use_editor') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("Active WYSIWYG Catalyst") . '</label></div>'],
                                        ['type' => 'select', 'label' => _('Architect Preferred Engine'), 'name' => 'editor_type', 'selected' => Options::v('editor_type'), 'options' => Editor::getEditors()]
                                    ],
                                    'footer' => '<p class="extra-small text-muted mb-0"><i class="bi bi-info-circle me-1"></i> ' . _("Switch engines to change the authoring experience.") . '</p>'
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('Discovery Pinger'),
                                    'icon' => 'bi bi-broadcast',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="pinger_enable" ' . (Options::v('pinger_enable') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("Instant Engine Ping") . '</label></div>'],
                                        ['type' => 'textarea', 'name' => 'pinger', 'value' => Options::v('pinger'), 'rows' => 4, 'class' => 'form-control bg-white font-monospace fs-8 border']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'payment' => [
            'label' => _('Payment'),
            'icon' => 'bi bi-credit-card-2-front',
            'content' => [
                ['type' => 'heading', 'text' => _('Transaction Gateways'), 'icon' => 'bi bi-cash-stack', 'subtitle' => _('Configure secure payment processors for monetary interactions.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 4,
                            'content' => [
                                ['type' => 'select', 'label' => _('Ecosystem Currency'), 'name' => 'currency', 'selected' => Options::v('currency'), 'options' => ['USD' => '$ USD', 'EUR' => '€ EUR', 'GBP' => '£ GBP', 'IDR' => 'Rp IDR']],
                                ['type' => 'alert', 'style' => 'info', 'content' => '<i class="bi bi-info-circle me-1"></i> ' . _("Select the primary currency for your store or subscription modules.")],
                            ]
                        ],
                        [
                            'width' => 8,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('PayPal Provider'),
                                    'icon' => 'bi bi-paypal',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="ppsandbox" ' . (Options::v('ppsandbox') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("Debug / Sandbox Protocol") . '</label></div>'],
                                        ['type' => 'input', 'label' => _('Merchant Identification'), 'name' => 'ppuser', 'value' => Options::v('ppuser')],
                                        ['type' => 'input', 'label' => _('API Gateway Password'), 'name' => 'pppass', 'value' => Options::v('pppass'), 'input_type' => 'password'],
                                        ['type' => 'input', 'label' => _('Secure API Signature'), 'name' => 'ppsign', 'value' => Options::v('ppsign')]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'security' => [
            'label' => _('Security'),
            'icon' => 'bi bi-shield-lock',
            'content' => [
                ['type' => 'heading', 'text' => _('Hardening Strategy'), 'icon' => 'bi bi-shield-fill-check', 'subtitle' => _('Critical access management and automated defense parameters.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 7,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('Bot Barrier (reCAPTCHA)'),
                                    'icon' => 'bi bi-robot',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="google_captcha_enable" ' . (Options::v('google_captcha_enable') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold">' . _("Active Barrier") . '</label></div>'],
                                        [
                                            'type' => 'row',
                                            'items' => [
                                                ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Site Identification Key'), 'name' => 'google_captcha_sitekey', 'value' => Options::v('google_captcha_sitekey')]],
                                                ['width' => 6, 'content' => ['type' => 'input', 'label' => _('Privileged Secret Key'), 'name' => 'google_captcha_secret', 'value' => Options::v('google_captcha_secret'), 'input_type' => 'password']],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 5,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('RESTful API KEY'),
                                    'icon' => 'bi bi-file-earmark-lock',
                                    'body_elements' => [
                                        [
                                            'type' => 'raw',
                                            'html' => '
                                <div class="p-3 bg-danger bg-opacity-10 rounded-4 border-start border-4 border-danger">
                                    <h6 class="extra-small fw-black text-danger text-uppercase mb-2">Restricted Area</h6>
                                    <p class="extra-small text-danger text-opacity-75 mb-0 lh-base fw-bold">' . _("Programmatic tokens represent the highest level of system authority. Regenerate immediately if leaked.") . '</p>
                                </div>
                                <div class="input-group mt-3">
                                    <input type="text" name="api_key" id="api_key_field" value="' . Options::v('api_key') . '" class="form-control border bg-light shadow-none rounded-start-4 py-2 fs-8 fw-bold">
                                    <button class="btn btn-white border px-3 rounded-end-4" type="button" onclick="generateApiKey()"><i class="bi bi-arrow-repeat text-primary h5 mb-0"></i></button>
                                </div>'
                                        ],
                                        ['type' => 'input', 'name' => 'api_rate_limit', 'label' => _('Rate Limit (Requests / Hour)'), 'value' => Options::v('api_rate_limit') ?: '100', 'input_type' => 'number'],
                                    ]
                                ]
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
    $(document).ready(function (e) {
        $("#ImageBrowse").on("change", function () {
            var reader, input = this, preview = document.getElementById('logo_preview');
            if (input.files && input.files[0]) {
                reader = new FileReader();
                reader.onload = function (e) {
                    if (preview) preview.setAttribute('src', e.target.result);
                    else $('#fileBrowse').html('<img src="' + e.target.result + '" class="img-fluid" id="logo_preview" style="max-height: 120px;">');
                    $.post('<?= Url::ajax("saveimage"); ?>', { file: e.target.result, file_name: input.files[0]['name'] }, function (data) {
                        $('#logo_image').val(JSON.parse(data).path);
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
    function uploadLogo() { document.getElementById('ImageBrowse').click(); }
    function generateApiKey() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        const array = new Uint32Array(32); window.crypto.getRandomValues(array);
        for (let i = 0; i < 32; i++) result += characters.charAt(array[i] % characters.length);
        document.getElementById('api_key_field').value = result;
    }
</script>