<?php
Theme::editor();
require_once __DIR__ . '/Newsletter.class.php';

// Install tables on first load
Newsletter::install();

$data  = [];
$tab   = isset($_GET['tab']) ? Typo::cleanX($_GET['tab']) : 'dashboard';
$token = TOKEN;

// ── CAMPAIGN ACTIONS ────────────────────────────────────────────────────────
if (isset($_POST['save_campaign'])) {
    if (!Token::validate(Typo::cleanX($_POST['token']))) {
        $data['alertDanger'][] = _("Invalid token.");
    } else {
        $id    = Typo::int($_POST['campaign_id'] ?? 0);
        $cdata = [
            'subject'   => Typo::cleanX($_POST['subject']),
            'body'      => $_POST['body'],
            'type'      => $_POST['mailtype'] === 'text' ? 'text' : 'html',
            'recipient' => Typo::cleanX($_POST['recipient']),
        ];
        if ($id > 0) {
            Newsletter::campaignUpdate($id, $cdata);
            $data['alertSuccess'][] = _("Campaign updated.");
        } else {
            Newsletter::campaignSave($cdata);
            $data['alertSuccess'][] = _("Campaign saved as draft.");
        }
        $tab = 'campaigns';
    }
}

if (isset($_GET['send_campaign'])) {
    $cid = Typo::int($_GET['send_campaign']);
    if (!Token::validate(Typo::cleanX($_GET['token'] ?? ''))) {
        $data['alertDanger'][] = _("Invalid token.");
    } else {
        $result = Newsletter::sendCampaign($cid);
        $data['alertSuccess'][] = sprintf(_("Sent: %d, Failed: %d"), $result['sent'], $result['failed']);
        if (!empty($result['errors'])) {
            $data['alertDanger'] = $result['errors'];
        }
    }
    $tab = 'campaigns';
}

if (isset($_GET['del_campaign'])) {
    $cid = Typo::int($_GET['del_campaign']);
    if (!Token::validate(Typo::cleanX($_GET['token'] ?? ''))) {
        $data['alertDanger'][] = _("Invalid token.");
    } else {
        Newsletter::campaignDelete($cid);
        $data['alertSuccess'][] = _("Campaign deleted.");
    }
    $tab = 'campaigns';
}

// ── SUBSCRIBER ACTIONS ───────────────────────────────────────────────────────
if (isset($_POST['add_subscriber'])) {
    if (!Token::validate(Typo::cleanX($_POST['token']))) {
        $data['alertDanger'][] = _("Invalid token.");
    } else {
        $email = Typo::cleanX($_POST['sub_email']);
        $name  = Typo::cleanX($_POST['sub_name'] ?? '');
        if (Newsletter::subscriberAdd($email, $name)) {
            $data['alertSuccess'][] = _("Subscriber added.");
        } else {
            $data['alertDanger'][] = _("Invalid email or already subscribed.");
        }
    }
    $tab = 'subscribers';
}

if (isset($_POST['import_csv'])) {
    if (!Token::validate(Typo::cleanX($_POST['token']))) {
        $data['alertDanger'][] = _("Invalid token.");
    } else {
        $csv    = $_POST['csv_data'];
        $result = Newsletter::importSubscribers($csv);
        $data['alertSuccess'][] = sprintf(_("Imported: %d rows added."), $result['added']);
        if (!empty($result['errors'])) {
            $data['alertDanger'][] = _("Skipped: ") . implode(', ', array_slice($result['errors'], 0, 10));
        }
    }
    $tab = 'subscribers';
}

if (isset($_GET['del_sub'])) {
    $sid = Typo::int($_GET['del_sub']);
    if (!Token::validate(Typo::cleanX($_GET['token'] ?? ''))) {
        $data['alertDanger'][] = _("Invalid token.");
    } else {
        Newsletter::subscriberDelete($sid);
        $data['alertSuccess'][] = _("Subscriber removed.");
    }
    $tab = 'subscribers';
}

if (isset($_GET['toggle_sub'])) {
    $sid = Typo::int($_GET['toggle_sub']);
    if (!Token::validate(Typo::cleanX($_GET['token'] ?? ''))) {
        $data['alertDanger'][] = _("Invalid token.");
    } else {
        Newsletter::subscriberToggle($sid);
    }
    $tab = 'subscribers';
}

// ── DATA FOR CURRENT TAB ────────────────────────────────────────────────────
$stats       = Newsletter::stats();
$paging      = max(1, Typo::int($_GET['paging'] ?? 1));
$perPage     = 25;
$offset      = ($paging - 1) * $perPage;
$searchSub   = Typo::cleanX($_GET['q'] ?? '');
$editCampaign = null;

if ($tab === 'subscribers') {
    $subscribers   = Newsletter::subscriberList($offset, $perPage, $searchSub);
    $subCount      = Newsletter::subscriberCount($searchSub);
} elseif ($tab === 'campaigns') {
    $campaigns     = Newsletter::campaignList($offset, $perPage);
    $campCount     = Newsletter::campaignCount();
} elseif ($tab === 'compose') {
    $editId = Typo::int($_GET['edit'] ?? 0);
    if ($editId > 0) $editCampaign = Newsletter::campaignGet($editId);
}

$logCampId  = Typo::int($_GET['cid'] ?? 0);
$logCamp    = $logCampId > 0 ? Newsletter::campaignGet($logCampId) : null;
$logs       = $logCampId > 0 ? Newsletter::campaignLogs($logCampId) : [];

require_once GX_LIB . '/UiBuilder.class.php';

// Prepare schema structure
$schema = [
    'header' => [
        'title' => 'Newsletter Module', 
        'subtitle' => 'Design, manage, and broadcast professional email campaigns to your audience.',
        'icon' => 'bi bi-mailbox2', 
        'button' => [
            'label' => 'Compose New', 
            'url' => 'index.php?page=mods&mod=newsletter&tab=compose', 
            'icon' => 'bi bi-plus-circle'
        ]
    ],
    'default_tab' => 'dashboard',
    'tabs' => [
        'dashboard'   => ['label' => 'Dashboard', 'icon' => 'bi bi-graph-up', 'content' => []],
        'campaigns'   => ['label' => 'Campaigns', 'icon' => 'bi bi-paper-plane', 'content' => []],
        'compose'     => ['label' => 'Compose', 'icon' => 'bi bi-pencil-square', 'content' => []],
        'subscribers' => ['label' => 'Subscribers', 'icon' => 'bi bi-people', 'content' => []],
        'logs'        => ['label' => 'Send Logs', 'icon' => 'bi bi-journal-text', 'content' => []]
    ]
];

// Alert System handling native to genixcms
$htmlAlerts = "";
if (!empty($data['alertSuccess'])) {
    foreach ($data['alertSuccess'] as $msg) $htmlAlerts .= "<div class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert'>&times;</button>{$msg}</div>";
}
if (!empty($data['alertDanger'])) {
    foreach ($data['alertDanger'] as $msg) $htmlAlerts .= "<div class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert'>&times;</button>{$msg}</div>";
}
if ($htmlAlerts) {
    $schema['tabs'][$tab]['content'][] = ['type' => 'raw', 'html' => $htmlAlerts];
}

// ── BUILD DASHBOARD TAB ──────────────────────────────────────────
$schema['tabs']['dashboard']['content'][] = [
    'type' => 'stat_cards',
    'items' => [
        [
            'label' => 'Total Subscribers', 
            'value' => number_format($stats['total_subscribers'] ?? 0), 
            'icon' => 'bi bi-people', 
            'color' => 'primary',
            'footer_link' => 'index.php?page=mods&mod=newsletter&tab=subscribers',
            'footer_text' => 'Manage database'
        ],
        [
            'label' => 'Active Members', 
            'value' => number_format($stats['active_subscribers'] ?? 0), 
            'icon' => 'bi bi-person-check', 
            'color' => 'success',
            'footer_link' => 'index.php?page=mods&mod=newsletter&tab=subscribers&q=active',
            'footer_text' => 'Verified only'
        ],
        [
            'label' => 'Campaigns Created', 
            'value' => number_format($stats['total_campaigns'] ?? 0), 
            'icon' => 'bi bi-envelope-paper', 
            'color' => 'info',
            'footer_link' => 'index.php?page=mods&mod=newsletter&tab=campaigns',
            'footer_text' => 'View library'
        ],
        [
            'label' => 'Sent Successfully', 
            'value' => number_format($stats['sent_campaigns'] ?? 0), 
            'icon' => 'bi bi-send-check', 
            'color' => 'secondary',
            'footer_link' => 'index.php?page=mods&mod=newsletter&tab=logs',
            'footer_text' => 'Delivery reports'
        ]
    ]
];

$schema['tabs']['dashboard']['content'][] = [
    'type' => 'row',
    'items' => [
        [
            'width' => 9,
            'content' => [
                'type' => 'card',
                'title' => 'Personalization Tags',
                'subtitle' => 'Use these dynamic tags in your email body to personalize content for each recipient.',
                'icon' => 'bi bi-tags',
                'no_padding' => true,
                'body_elements' => [
                    [
                        'type' => 'table',
                        'headers' => ['Tag', 'Description', 'Real-world Example'],
                        'rows' => [
                            ['<code>{{name}}</code>', 'Full name of the recipient', 'John Doe'],
                            ['<code>{{email}}</code>', 'Recipient email address', 'john@example.com'],
                            ['<code>{{sitename}}</code>', 'Your website name', (defined('GX_SITENAME') ? GX_SITENAME : Options::v('sitename'))],
                            ['<code>{{siteurl}}</code>', 'Full website link', Options::v('siteurl')],
                            ['<code>{{unsubscribe}}</code>', 'One-click removal link', '<a href="#" class="text-danger fw-bold">Unsubscribe</a>']
                        ]
                    ]
                ]
            ]
        ],
        [
            'width' => 3,
            'content' => [
                'type' => 'card',
                'title' => 'System Shortcuts',
                'icon' => 'bi bi-lightning-charge',
                'body_elements' => [
                    [
                        'type' => 'raw',
                        'html' => '
                        <a href="index.php?page=mods&mod=newsletter&tab=compose" class="btn btn-primary btn-lg rounded-pill w-100 mb-3 fw-bold"><i class="bi bi-pencil-square me-2"></i>New Campaign</a>
                        <a href="index.php?page=mods&mod=newsletter&tab=subscribers" class="btn btn-outline-dark btn-sm rounded-pill w-100 mb-2 fw-medium">Manage Base</a>
                        <a href="index.php?page=mods&mod=newsletter&tab=logs" class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-medium">Reports log</a>'
                    ]
                ]
            ]
        ]
    ]
];

// ── BUILD CAMPAIGNS TAB ──────────────────────────────────────────
$campaignRows = [];
foreach ($campaigns ?? [] as $c) {
    if ($c->status === 'draft') {
        $btnEdit = "<a href='index.php?page=mods&mod=newsletter&tab=compose&edit={$c->id}' class='btn btn-xs btn-warning'><i class='fa fa-edit'></i></a>";
        $btnSend = "<a href='index.php?page=mods&mod=newsletter&tab=campaigns&send_campaign={$c->id}&token={$token}' class='btn btn-xs btn-success' onclick='return confirm(\"Send?\")'><i class='fa fa-send'></i></a>";
    } else {
        $btnEdit = "";
        $btnSend = "";
    }
    $btnLog = "<a href='index.php?page=mods&mod=newsletter&tab=logs&cid={$c->id}' class='btn btn-xs btn-info'><i class='fa fa-list-alt'></i></a>";
    $btnDel = "<a href='index.php?page=mods&mod=newsletter&tab=campaigns&del_campaign={$c->id}&token={$token}' class='btn btn-xs btn-danger' onclick='return confirm(\"Delete?\")'><i class='fa fa-trash'></i></a>";
    
    $recipientLabel = match($c->recipient) {
        'all' => 'All (Mem+Sub)', 'subscribers' => 'Subscribers Only',
        'group_0' => 'Admins', 'group_4' => 'Members', default => $c->recipient
    };
    $statsBadge = match($c->status) {
        'sent' => "<span class='badge' style='background:#d4edda;color:#155724;'>Sent</span>",
        'draft' => "<span class='badge' style='background:#fff3cd;color:#856404;'>Draft</span>",
        default => "<span class='badge bg-primary'>{$c->status}</span>"
    };

    $campaignRows[] = [
        $c->id, htmlspecialchars($c->subject), "<small>{$recipientLabel}</small>", strtoupper($c->type),
        $statsBadge, $c->sent_count, $c->fail_count, "<small>{$c->created_at}</small>",
        $btnEdit . ' ' . $btnSend . ' ' . $btnLog . ' ' . $btnDel
    ];
}
$schema['tabs']['campaigns']['content'][] = [
    'type' => 'card',
    'title' => 'Campaign Management',
    'icon' => 'bi bi-paper-plane',
    'no_padding' => true,
    'body_elements' => [
        [
            'type' => 'table',
            'headers' => ['#', 'Subject', 'Recipients', 'Type', 'Status', 'Sent', 'Failed', 'Created', 'Actions'],
            'rows' => $campaignRows,
            'empty_message' => 'No campaigns found in your library.'
        ]
    ],
    'footer' => '<p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i> You can view detailed delivery logs for each campaign by clicking the list icon.</p>'
];

// ── BUILD COMPOSE TAB ────────────────────────────────────────────
$recOpts = [
    'all'         => 'All Members + Subscribers', 
    'subscribers' => 'Subscribers Only', 
    'group_0'     => 'Administrators Only', 
    'group_4'     => 'General Members Only'
];

$formFields = [
    [
        'type' => 'row',
        'items' => [
            [
                'width' => 9,
                'content' => [
                    'type' => 'card',
                    'title' => 'Campaign Message',
                    'icon' => 'bi bi-envelope-paper',
                    'body_elements' => [
                        [
                            'type' => 'input',
                            'name' => 'subject',
                            'label' => 'Subject Line',
                            'placeholder' => 'Enter the email subject...',
                            'required' => true,
                            'value' => $editCampaign?->subject ?? ''
                        ],
                        [
                            'type' => 'textarea',
                            'name' => 'body',
                            'label' => 'Message Body',
                            'class' => 'form-control editor content',
                            'rows' => 15,
                            'value' => $editCampaign?->body ?? '',
                            'help' => 'Available tags: {{name}}, {{email}}, {{sitename}}, {{siteurl}}, {{unsubscribe}}'
                        ]
                    ]
                ]
            ],
            [
                'width' => 3,
                'content' => [
                    'type' => 'card',
                    'title' => 'Settings',
                    'icon' => 'bi bi-gear-fill',
                    'body_elements' => [
                        [
                            'type' => 'select',
                            'name' => 'recipient',
                            'label' => 'Target Audience',
                            'options' => $recOpts,
                            'selected' => $editCampaign?->recipient ?? 'all'
                        ],
                        [
                            'type' => 'select',
                            'name' => 'mailtype',
                            'label' => 'Email Format',
                            'options' => ['html' => 'Modern HTML', 'text' => 'Plain Text'],
                            'selected' => $editCampaign?->type ?? 'html'
                        ],
                        [
                            'type' => 'button',
                            'name' => 'save_campaign',
                            'label' => ($editCampaign ? 'Update Campaign' : 'Save Draft'),
                            'icon' => 'bi bi-save',
                            'class' => 'btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm'
                        ],
                        [
                            'type' => 'raw',
                            'html' => '<div class="mt-4 pt-3 border-top text-muted small">
                                        <p class="mb-1"><i class="bi bi-info-circle me-1"></i> <strong>Pro-tip:</strong></p>
                                        <p>Save your campaign as a draft first to preview it in the Campaigns list before sending.</p>
                                      </div>'
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$schema['tabs']['compose']['content'][] = [
    'type' => 'form', 
    'action' => 'index.php?page=mods&mod=newsletter&tab=campaigns', 
    'hidden' => ['token' => $token, 'campaign_id' => $editCampaign?->id ?? 0],
    'fields' => $formFields
];

// ── BUILD SUBSCRIBERS TAB ────────────────────────────────────────
$subRows = [];
foreach ($subscribers ?? [] as $s) {
    if ($s->status == 1) {
        $stat = "<span style='color:#28a745'><i class='fa fa-check-circle'></i> Active</span>";
    } else {
        $stat = "<span style='color:#dc3545'><i class='fa fa-times-circle'></i> Unsubscribed</span>";
    }
    $btnToggle = "<a href='index.php?page=mods&mod=newsletter&tab=subscribers&toggle_sub={$s->id}&token={$token}' class='btn btn-xs btn-warning'><i class='fa fa-toggle-on'></i></a>";
    $btnDel = "<a href='index.php?page=mods&mod=newsletter&tab=subscribers&del_sub={$s->id}&token={$token}' class='btn btn-xs btn-danger' onclick='return confirm(\"Remove subscriber?\")'><i class='fa fa-trash'></i></a>";
    $subRows[] = [$s->id, htmlspecialchars($s->email), htmlspecialchars($s->name), $stat, "<small>{$s->created_at}</small>", "{$btnToggle} {$btnDel}"];
}

$schema['tabs']['subscribers']['content'][] = [
    'type' => 'row', 'items' => [
        ['width' => 4, 'content' => [
            'type' => 'card', 'title' => 'Add Subscriber', 'icon' => 'bi bi-person-plus',
            'body_elements' => [
                [
                    'type' => 'form', 'action' => 'index.php?page=mods&mod=newsletter&tab=subscribers',
                    'hidden' => ['token' => $token],
                    'fields' => [
                        ['type' => 'input', 'input_type' => 'email', 'name' => 'sub_email', 'label' => 'Email *', 'required' => true],
                        ['type' => 'input', 'input_type' => 'text', 'name' => 'sub_name', 'label' => 'Name'],
                        ['type' => 'button', 'name' => 'add_subscriber', 'label' => 'Add', 'icon' => 'bi bi-plus', 'class' => 'btn btn-success btn-lg rounded-pill w-100 fw-bold']
                    ]
                ],
                [
                    'type' => 'raw',
                    'html' => '<div class="mt-4 border-top pt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-cloud-upload text-primary me-2"></i>Import CSV</h6>
                        <form method="post" action="index.php?page=mods&mod=newsletter&tab=subscribers">
                            <input type="hidden" name="token" value="'.$token.'">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">CSV Data (email, name)</label>
                                <textarea name="csv_data" class="form-control form-control-lg rounded-4 bg-light shadow-none border-0" rows="4"></textarea>
                            </div>
                            <button type="submit" name="import_csv" class="btn btn-info btn-lg rounded-pill w-100 fw-bold"><i class="bi bi-upload mt-1 me-1"></i> Import</button>
                        </form>
                    </div>'
                ]
            ]
        ]],
        ['width' => 8, 'content' => [
            'type' => 'card', 'title' => "Subscribers List", 'icon' => 'bi bi-people',
            'header_action' => (new UiBuilder())->renderElement([
                'type' => 'search_group', 
                'action' => 'index.php',
                'hidden' => ['page' => 'mods', 'mod' => 'newsletter', 'tab' => 'subscribers'],
                'value' => $searchSub,
                'placeholder' => 'Search by email...'
            ], true),
            'no_padding' => true,
            'body_elements' => [
                [
                    'type' => 'table', 'headers' => ['#','Email','Name','Status','Joined','Actions'], 'rows' => $subRows, 'empty_message' => 'No subscribers found.'
                ]
            ],
            'footer' => '<span class="badge bg-secondary rounded-pill px-3">Total: '.($subCount ?? 0).' records</span>'
        ]]
    ]
];

// ── BUILD SEND LOGS TAB ──────────────────────────────────────────
if ($tab === 'logs' && $logCampId > 0 && $logCamp) {
    $logRows = [];
    foreach ($logs as $log) {
        $st = $log->status === 'sent' 
            ? "<span class='badge bg-success bg-opacity-10 text-success rounded-pill px-3 fw-bold small'><i class='bi bi-check-circle me-1'></i>Sent</span>" 
            : "<span class='badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 fw-bold small'><i class='bi bi-exclamation-triangle me-1'></i>Failed</span>";
        $err = "<small class='text-muted'>".htmlspecialchars($log->error ?? '-')."</small>";
        $logRows[] = [$log->id, "<strong>".htmlspecialchars($log->email)."</strong>", $st, $err, "<small class='text-muted'>{$log->sent_at}</small>"];
    }

    $schema['tabs']['logs']['content'][] = [
        'type' => 'stat_cards',
        'items' => [
            ['label' => 'Delivered', 'value' => $logCamp->sent_count, 'icon' => 'bi bi-check2-all', 'color' => 'success'],
            ['label' => 'Bounced/Fail', 'value' => $logCamp->fail_count, 'icon' => 'bi bi-x-circle', 'color' => 'danger'],
            ['label' => 'Dispatch Date', 'value' => date('d M Y', strtotime($logCamp->sent_at)), 'icon' => 'bi bi-calendar-event', 'color' => 'info']
        ]
    ];

    $schema['tabs']['logs']['content'][] = [
        'type' => 'card',
        'title' => "Delivery History: " . htmlspecialchars($logCamp->subject),
        'icon' => 'bi bi-journal-text',
        'no_padding' => true,
        'body_elements' => [
            ['type' => 'table', 'headers' => ['#','Recipient email','Status','Extended Log','Timestamp'], 'rows' => $logRows]
        ],
        'footer' => "<a href='index.php?page=mods&mod=newsletter&tab=campaigns' class='btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold'><i class='bi bi-arrow-left me-1'></i> Back to Campaigns</a>"
    ];

} else {
    $schema['tabs']['logs']['content'][] = [
        'type' => 'alert',
        'style' => 'light',
        'content' => '<i class="bi bi-info-circle me-2 text-primary"></i> Please select a campaign from the <a href="index.php?page=mods&mod=newsletter&tab=campaigns" class="fw-bold">Campaigns list</a> to view its detailed delivery log.'
    ];
}

// Instantiate UiBuilder and render
$builder = new UiBuilder($schema);
$builder->render();
