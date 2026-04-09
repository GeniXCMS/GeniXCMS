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

$username = Session::val('username');

// ── PREPARE DATA ──────────────────────────────────────────────────
$stats = [
    'posts' => Stats::totalPost('post'),
    'pages' => Stats::totalPost('page'),
    'comments' => Stats::pendingComments(),
    'users' => Stats::totalUser()
];

// Recent Posts Data
$recentPosts = Posts::recent(['num' => 6, 'type' => 'post']);
$postRows = [];
if (!isset($recentPosts['error'])) {
    foreach ($recentPosts as $p) {
        $status = "<span class=\"badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 extra-small border border-success border-opacity-10\">" . _("Live") . "</span>";
        $actions = "
            <div class=\"btn-group\">
                <a href=\"" . Url::post($p->id) . "\" target=\"_blank\" class=\"btn btn-white btn-sm rounded-circle border d-inline-flex align-items-center justify-content-center\" style=\"width: 30px; height: 30px;\"><i class=\"bi bi-eye text-primary text-opacity-75\"></i></a>
                <a href=\"index.php?page=posts&act=edit&id={$p->id}\" class=\"btn btn-white btn-sm rounded-circle border d-inline-flex align-items-center justify-content-center ms-1\" style=\"width: 30px; height: 30px;\"><i class=\"bi bi-pencil text-dark text-opacity-75\"></i></a>
            </div>";

        $postRows[] = [
            "<div class='fw-bold text-dark fs-7 mb-0'>" . htmlspecialchars($p->title) . "</div><div class='extra-small text-muted opacity-75'>" . Date::format($p->date) . "</div>",
            "<div class='d-flex align-items-center gap-2'><div class='bg-light rounded-circle p-1 d-flex align-items-center justify-content-center' style='width: 24px; height: 24px;'><i class='bi bi-person extra-small'></i></div><span class='small fw-semibold text-muted'>" . htmlspecialchars($p->author) . "</span></div>",
            $status,
            ['content' => $actions, 'class' => 'text-end pe-4']
        ];
    }
}

// Most Viewed Data
$mostViewed = Stats::mostViewed(6);
$viewedRows = [];
if (!isset($mostViewed['error'])) {
    foreach ($mostViewed as $p) {
        $viewedRows[] = [
            "<div class='d-flex align-items-center gap-3'>
                <div class='bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center' style='width: 40px; height: 40px;'><i class='bi bi-graph-up-arrow fs-5'></i></div>
                <div><div class='fw-bold text-dark fs-7 mb-0'>" . htmlspecialchars($p->title) . "</div><div class='extra-small text-muted'><i class='bi bi-eye me-1'></i> " . number_format($p->views) . " " . _("Views") . "</div></div>
            </div>",
            ['content' => "<a href='" . Url::post($p->id) . "' target='_blank' class='btn btn-light btn-sm rounded-pill p-2 fs-8 fw-bold'><i class='bi bi-arrow-up-right'></i></a>", 'class' => 'text-end pe-4']
        ];
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'show_header' => false,
    'title' => 'Dashboard',
    'subtitle' => 'System Overview',
    'icon' => 'bi bi-speedometer2',
    'content' => [
        // Welcome Header (Raw for specific styles)
        [
            'type' => 'raw',
            'html' => '
            <div class="row align-items-center mb-5 mt-n2">
                <div class="col-lg-8 text-start">
                    <h2 class="fw-black text-dark mb-1 d-flex align-items-center gap-2">
                        <span>' . _("Welcome back") . ',</span>
                        <span class="text-primary text-gradient">' . htmlspecialchars((string) $username) . '!</span>
                        <span class="wave-emoji fs-3">👋</span>
                    </h2>
                    <p class="text-muted fw-medium fs-6 mb-0">
                        <i class="bi bi-calendar3 me-2 text-primary opacity-50"></i>
                        ' . Date::local(date('Y-m-d H:i:s'), 'EEEE, d MMMM yyyy') . ' · <span class="text-dark fw-bold" id="dashboard-clock">' . date('H:i') . '</span>
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">' .
                (new UiBuilder())->renderElement([
                    'type' => 'dropdown_button',
                    'label' => '<i class="bi bi-speedometer2 text-primary me-1"></i> ' . _("System Status") . ' <span class="pulse-success ms-1"></span>',
                    'class' => 'btn btn-white shadow-sm rounded-pill px-4 border py-2 fw-bold d-flex align-items-center gap-2',
                    'align' => 'end',
                    'items' => [
                        ['type' => 'header', 'label' => _("Server Vitals")],
                        ['type' => 'raw', 'label' => '<div class="d-flex justify-content-between mb-2"><span class="small text-muted">' . _("PHP Version") . '</span><span class="badge bg-light text-dark fw-bold">' . PHP_VERSION . '</span></div>'],
                        ['type' => 'raw', 'label' => '<div class="d-flex justify-content-between mb-2"><span class="small text-muted">' . _("OS") . '</span><span class="badge bg-light text-dark fw-bold">' . PHP_OS . '</span></div>'],
                        ['type' => 'raw', 'label' => '<div class="d-flex justify-content-between"><span class="small text-muted">' . _("Engine") . '</span><span class="badge bg-primary bg-opacity-10 text-primary fw-bold">v' . System::$version . '</span></div>'],
                        ['type' => 'divider'],
                        ['label' => _('Manage Configuration'), 'url' => 'index.php?page=settings', 'icon' => 'bi bi-gear-fill']
                    ]
                ], true) . '
                </div>
            </div>'
        ],

        // 4 Stat Cards
        [
            'type' => 'stat_cards',
            'items' => [
                ['label' => _('Articles'), 'value' => (string) $stats['posts'], 'icon' => 'bi bi-journal-text', 'color' => 'primary', 'footer_link' => 'index.php?page=posts', 'footer_text' => _('View All')],
                ['label' => _('Pages'), 'value' => (string) $stats['pages'], 'icon' => 'bi bi-stack', 'color' => 'success', 'footer_link' => 'index.php?page=pages', 'footer_text' => _('Manage')],
                ['label' => _('Engagement'), 'value' => (string) $stats['comments'], 'icon' => 'bi bi-chat-left-dots', 'color' => 'warning', 'footer_link' => 'index.php?page=comments', 'footer_text' => _('Verify')],
                ['label' => _('Entities'), 'value' => (string) $stats['users'], 'icon' => 'bi bi-people', 'color' => 'info', 'footer_link' => 'index.php?page=users', 'footer_text' => _('Analyze')]
            ]
        ],

        // Main Content Row
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 8,
                    'content' => [
                        'type' => 'card',
                        'title' => _('Content Velocity'),
                        'subtitle' => _('Automated log of recently published architecture.'),
                        'no_padding' => true,
                        'header_action' => '<a href="index.php?page=posts&act=add" class="btn btn-primary rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;"><i class="bi bi-plus-lg"></i></a>',
                        'body_elements' => [
                            [
                                'type' => 'table',
                                'headers' => [_('Object Identity'), _('Author'), _('Status'), ['content' => _('Aesthetics'), 'class' => 'text-end pe-4']],
                                'rows' => $postRows,
                                'empty_message' => _('Static Database: No recent signals detected.')
                            ]
                        ],
                        'footer' => '<div class="text-center w-100"><a href="index.php?page=posts" class="text-primary text-decoration-none extra-small fw-black text-uppercase tracking-wider">' . _("Access Entire Content Repository") . ' <i class="bi bi-arrow-right-short ms-1"></i></a></div>'
                    ]
                ],
                [
                    'width' => 4,
                    'content' => [
                        'type' => 'card',
                        'title' => _('New Entities'),
                        'subtitle' => _('Recently registered collaborators.'),
                        'body_elements' => [
                            [
                                'type' => 'raw',
                                'html' => '<div class="row g-4 text-center">' . (function () {
                                    $html = '';
                                    $users = Db::result("SELECT * FROM `user` ORDER BY `join_date` DESC LIMIT 6");
                                    if (Db::$num_rows > 0) {
                                        foreach ($users as $u) {
                                            $av = Image::getGravatar($u->email);
                                            $userUrl = "index.php?page=users&act=edit&id={$u->id}";
                                            $html .= "<div class='col-4'>
                                                <a href='{$userUrl}' class='member-card p-2 rounded-4 d-block text-decoration-none transition-all'>
                                                    <div class='position-relative d-inline-block mb-2'>
                                                        <img src='{$av}' class='rounded-circle shadow-sm border border-3 border-white' width='55'>
                                                        <span class='position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle' style='width: 12px; height: 12px;'></span>
                                                    </div>
                                                    <div class='fw-bold fs-8 text-dark text-truncate mb-0 px-1'>" . htmlspecialchars($u->userid) . "</div>
                                                    <div class='extra-small text-muted opacity-50'>" . Date::format($u->join_date, 'M d') . "</div>
                                                </a>
                                            </div>";
                                        }
                                    }
                                    return $html;
                                })() . '</div>'
                            ]
                        ],
                        'footer' => '<a href="index.php?page=users" class="btn btn-white btn-sm rounded-pill fw-bold border px-4 shadow-sm w-100">' . _("User Directory Management") . '</a>'
                    ]
                ]
            ]
        ],

        // Geo and Performance Row
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 6,
                    'content' => [
                        'type' => 'card',
                        'title' => '<i class="bi bi-globe-americas me-2 text-primary"></i>' . _('Geographical Footprint'),
                        'subtitle' => _('Origin signals of your audience base.'),
                        'no_padding' => true,
                        'body_elements' => [
                            ['type' => 'raw', 'html' => '<div id="world-map" class="bg-light" style="height: 350px;"></div>']
                        ]
                    ]
                ],
                [
                    'width' => 6,
                    'content' => [
                        'type' => 'card',
                        'title' => '<i class="bi bi-lightning-charge-fill me-2 text-warning"></i>' . _('Peak Performance'),
                        'subtitle' => _('Content units with the highest engagement signal.'),
                        'no_padding' => true,
                        'body_elements' => [
                            [
                                'type' => 'table',
                                'headers' => [], // Flat table style
                                'rows' => $viewedRows,
                                'empty_message' => _('Signal data missing.')
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

// ── CUSTOM CSS ────────────────────────────────────────────────────
?>
<style>
    .fw-black {
        font-weight: 900 !important;
    }

    .text-gradient {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .wave-emoji {
        display: inline-block;
        animation: wave 2.5s infinite;
        transform-origin: 70% 70%;
    }

    .member-card:hover {
        background-color: #f8f9fa;
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .pulse-success {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #198754;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    @keyframes wave {

        0%,
        60%,
        100% {
            transform: rotate(0.0deg)
        }

        10% {
            transform: rotate(14.0deg)
        }

        20% {
            transform: rotate(-8.0deg)
        }

        30% {
            transform: rotate(14.0deg)
        }

        40% {
            transform: rotate(-4.0deg)
        }

        50% {
            transform: rotate(10.0deg)
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
        }
    }

    .fs-7 {
        font-size: 0.9rem !important;
    }

    .fs-8 {
        font-size: 0.8rem !important;
    }

    .extra-small {
        font-size: 0.7rem !important;
    }
</style>

<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const el = document.getElementById('dashboard-clock');
        if (el) el.textContent = hours + ':' + minutes;
    }
    setInterval(updateClock, 30000);

    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById('world-map')) {
            new jsVectorMap({
                selector: "#world-map",
                map: "world",
                zoomButtons: true,
                regionStyle: {
                    initial: {
                        fill: '#e2e8f0',
                        stroke: 'none',
                        strokeWidth: 1,
                        strokeOpacity: 1
                    },
                    hover: {
                        fill: '#3b82f6',
                        cursor: 'pointer'
                    }
                },
                visualizeData: {
                    scale: ['#eff6ff', '#1d4ed8'],
                    values: {
                        "US": 100,
                        "ID": 350,
                        "GB": 80,
                        "IN": 120,
                        "DE": 60,
                        "FR": 45,
                        "AU": 90,
                        "JP": 150
                    }
                }
            });
        }
    });
</script>

<?php
// Render Notification and Top Hooks
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo Hooks::run('admin_page_top_action', $data);
echo '</div>';

// Allow modules and themes to modify the dashboard structure
$schema = Hooks::filter('admin_dashboard_schema', $schema);

$builder = new UiBuilder($schema);
// Set header visibility to false because we handled it with our custom 'Welcome' raw block 
// to maintain the specific animations and style of the dashboard.
$builder->render();

echo '<div class="col-md-12">';
echo Hooks::run('admin_page_bottom_action', $data);
echo '</div>';
?>