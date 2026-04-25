<?php
/**
 * GeniXCMS - Admin Dashboard
 * @since 0.0.1
 * @version 2.4.0
 */

$username = Session::val('username');
$userGroup = (int) Session::val('group');

// ── STATS ─────────────────────────────────────────────────────────
$stats = [
    'posts'          => Stats::totalPost('post'),
    'posts_active'   => Stats::activePost('post'),
    'pages'          => Stats::totalPost('page'),
    'comments'       => Stats::pendingComments(),
    'comments_total' => Stats::totalComments(),
    'users'          => Stats::totalUser(),
    'users_active'   => Stats::activeUser(),
];

// ── RECENT POSTS ──────────────────────────────────────────────────
$recentPosts = Posts::recent(['num' => 8, 'type' => 'post']);
$postRows = [];
if (!isset($recentPosts['error'])) {
    foreach ($recentPosts as $p) {
        $isActive = ($p->status == '1');
        $statusBadge = $isActive
            ? "<span class='badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 extra-small'>" . _('Live') . "</span>"
            : "<span class='badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2 extra-small'>" . _('Draft') . "</span>";
        $actions = "
            <div class='d-flex gap-1 justify-content-end pe-3'>
                <a href='" . Url::post($p->id) . "' target='_blank'
                   class='btn btn-light btn-sm rounded-circle border d-inline-flex align-items-center justify-content-center'
                   style='width:28px;height:28px' title='" . _('View') . "'>
                    <i class='bi bi-eye' style='font-size:.7rem'></i>
                </a>
                <a href='index.php?page=posts&act=edit&id={$p->id}'
                   class='btn btn-light btn-sm rounded-circle border d-inline-flex align-items-center justify-content-center'
                   style='width:28px;height:28px' title='" . _('Edit') . "'>
                    <i class='bi bi-pencil' style='font-size:.7rem'></i>
                </a>
            </div>";
        $postRows[] = [
            "<div class='ps-3 py-2'>
                <div class='fw-semibold text-dark' style='font-size:1rem;line-height:1.3'>" . Typo::Xclean($p->title) . "</div>
                <div class='d-flex align-items-center gap-2 mt-1'>
                    <span class='extra-small text-muted'><i class='bi bi-person me-1'></i>" . Typo::Xclean($p->author) . "</span>
                    <span class='extra-small text-muted opacity-50'>·</span>
                    <span class='extra-small text-muted'>" . Date::format($p->date) . "</span>
                </div>
             </div>",
            ['content' => $statusBadge, 'class' => 'align-middle'],
            ['content' => $actions, 'class' => 'text-end p-0 align-middle'],
        ];
    }
}

// ── MOST VIEWED ───────────────────────────────────────────────────
$mostViewed = Stats::mostViewed(5);
$viewedRows = [];
if (!isset($mostViewed['error'])) {
    foreach ($mostViewed as $i => $p) {
        $rank = $i + 1;
        $rankColor = $rank === 1 ? '#f59e0b' : ($rank === 2 ? '#94a3b8' : ($rank === 3 ? '#cd7c2f' : '#e2e8f0'));
        $viewedRows[] = [
            "<div class='d-flex align-items-center gap-3 ps-3 py-2'>
                <span class='fw-black' style='font-size:.75rem;color:{$rankColor};min-width:16px'>#{$rank}</span>
                <div>
                    <div class='fw-semibold text-dark' style='font-size:.8rem'>" . Typo::Xclean($p->title) . "</div>
                    <div class='extra-small text-muted'><i class='bi bi-eye me-1'></i>" . number_format($p->views) . " " . _('views') . "</div>
                </div>
             </div>",
            ['content' => "<a href='" . Url::post($p->id) . "' target='_blank' class='btn btn-light btn-sm rounded-circle border me-3' style='width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center'><i class='bi bi-arrow-up-right' style='font-size:.7rem'></i></a>", 'class' => 'text-end align-middle p-0'],
        ];
    }
}

// ── RECENT USERS ──────────────────────────────────────────────────
$recentUsers = Db::result("SELECT * FROM `user` ORDER BY `join_date` DESC LIMIT 5");

// ── CONTENT HEALTH ────────────────────────────────────────────────
$totalPosts   = max(1, $stats['posts']);
$publishRate  = $stats['posts'] > 0 ? round(($stats['posts_active'] / $totalPosts) * 100) : 0;
$commentRate  = $stats['comments_total'] > 0 ? round((($stats['comments_total'] - $stats['comments']) / $stats['comments_total']) * 100) : 100;
$userActRate  = $stats['users'] > 0 ? round(($stats['users_active'] / max(1, $stats['users'])) * 100) : 0;

// ── SYSTEM INFO ───────────────────────────────────────────────────
$diskFree  = function_exists('disk_free_space')  ? disk_free_space('.')  : 0;
$diskTotal = function_exists('disk_total_space') ? disk_total_space('.') : 0;
$diskUsed  = $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100) : 0;
$memLimit  = ini_get('memory_limit');
$uptime    = '';
if (PHP_OS_FAMILY !== 'Windows' && file_exists('/proc/uptime')) {
    $up = (int) file_get_contents('/proc/uptime');
    $uptime = sprintf('%dd %dh %dm', floor($up/86400), floor(($up%86400)/3600), floor(($up%3600)/60));
}

// ── SCHEMA ────────────────────────────────────────────────────────
$schema = [
    'show_header' => false,
    'content' => [

        // ── GREETING ──────────────────────────────────────────────
        [
            'type' => 'raw',
            'html' => '
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                <div>
                    <p class="mb-1 extra-small text-muted fw-semibold">'
                        . Date::local(date('Y-m-d H:i:s'), 'EEE, d MMMM yyyy')
                        . ' &nbsp;·&nbsp; <span id="dash-clock" class="fw-bold text-dark">' . date('H:i') . '</span>
                    </p>
                    <h1 class="fw-black text-dark mb-1" style="font-size:1.75rem;letter-spacing:-.03em">
                        ' . _('Hello') . ', <span style="color:var(--gx-primary,#00A3EA)">' . htmlspecialchars((string)$username) . '</span> <span class="wave-emoji">👋</span>
                    </h1>
                    <p class="text-muted mb-0" style="font-size:.88rem">' . _("Here's what's happening with your site today.") . '</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-1">
                    <a href="index.php?page=posts&act=add&type=post"
                       style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:var(--gx-primary,#00A3EA);border:1px solid var(--gx-primary,#00A3EA);border-radius:9999px;font-size:.78rem;font-weight:600;color:#fff;text-decoration:none;transition:opacity .15s">
                        <i class="bi bi-plus-lg"></i> ' . _('New Post') . '
                    </a>
                    <a href="index.php?page=pages&act=add&type=page"
                       style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#fff;border:1px solid #e2e8f0;border-radius:9999px;font-size:.78rem;font-weight:600;color:#374151;text-decoration:none;box-shadow:0 1px 2px rgba(0,0,0,.05)">
                        <i class="bi bi-journal-plus"></i> ' . _('New Page') . '
                    </a>
                    <a href="index.php?page=media"
                       style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#fff;border:1px solid #e2e8f0;border-radius:9999px;font-size:.78rem;font-weight:600;color:#374151;text-decoration:none;box-shadow:0 1px 2px rgba(0,0,0,.05)">
                        <i class="bi bi-images"></i> ' . _('Media') . '
                    </a>
                </div>
            </div>'
        ],

        // ── STAT CARDS ────────────────────────────────────────────
        [
            'type'  => 'stat_cards',
            'style' => 'classic',
            'items' => [
                [
                    'label'       => _('Published Posts'),
                    'value'       => number_format($stats['posts_active']),
                    'icon'        => 'bi bi-file-earmark-richtext',
                    'color'       => 'primary',
                    'width'       => 3,
                    'footer_link' => 'index.php?page=posts',
                    'footer_text' => number_format($stats['posts']) . ' ' . _('total posts'),
                ],
                [
                    'label'       => _('Pages'),
                    'value'       => number_format($stats['pages']),
                    'icon'        => 'bi bi-journal-text',
                    'color'       => 'success',
                    'width'       => 3,
                    'footer_link' => 'index.php?page=pages',
                    'footer_text' => _('Manage Pages'),
                ],
                [
                    'label'       => _('Pending Comments'),
                    'value'       => number_format($stats['comments']),
                    'icon'        => 'bi bi-chat-left-dots',
                    'color'       => 'warning',
                    'width'       => 3,
                    'footer_link' => 'index.php?page=comments',
                    'footer_text' => number_format($stats['comments_total']) . ' ' . _('total comments'),
                ],
                [
                    'label'       => _('Active Users'),
                    'value'       => number_format($stats['users_active']),
                    'icon'        => 'bi bi-people',
                    'color'       => 'info',
                    'width'       => 3,
                    'footer_link' => 'index.php?page=users',
                    'footer_text' => number_format($stats['users']) . ' ' . _('total users'),
                ],
            ]
        ],

        // ── MAIN ROW: Recent Posts + Sidebar ──────────────────────
        [
            'type' => 'row',
            'items' => [
                // Recent Posts
                [
                    'width' => 8,
                    'content' => [
                        'type' => 'card',
                        'title' => _('Recent Posts'),
                        'icon' => 'bi bi-file-earmark-richtext',
                        'subtitle' => _('Latest content activity'),
                        'no_padding' => true,
                        'header_action' => '
                            <a href="index.php?page=posts&act=add&type=post"
                               class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
                                <i class="bi bi-plus-lg me-1"></i>' . _('Add New') . '
                            </a>',
                        'body_elements' => [[
                            'type' => 'table',
                            'headers' => [
                                ['content' => _('Title'), 'class' => 'ps-3 py-3'],
                                _('Status'),
                                ['content' => '', 'class' => 'text-end pe-3'],
                            ],
                            'rows' => $postRows,
                            'empty_message' => _('No posts yet. Create your first post!'),
                        ]],
                        'footer' => '<div class="text-center w-100">
                            <a href="index.php?page=posts" class="text-primary text-decoration-none extra-small fw-bold text-uppercase" style="letter-spacing:.06em">
                                ' . _('View All Posts') . ' <i class="bi bi-arrow-right ms-1"></i>
                            </a></div>',
                    ]
                ],

                // Right sidebar
                [
                    'width' => 4,
                    'content' => [
                        // Content Health
                        [
                            'type' => 'card',
                            'title' => _('Content Health'),
                            'icon' => 'bi bi-heart-pulse',
                            'body_elements' => [[
                                'type' => 'raw',
                                'html' => '
                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="extra-small fw-semibold text-muted">' . _('Publish Rate') . '</span>
                                            <span class="extra-small fw-bold text-dark">' . $publishRate . '%</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height:6px;background:#f1f5f9">
                                            <div class="progress-bar rounded-pill" style="width:' . $publishRate . '%;background:var(--gx-primary,#00A3EA)"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="extra-small fw-semibold text-muted">' . _('Comment Approval') . '</span>
                                            <span class="extra-small fw-bold text-dark">' . $commentRate . '%</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height:6px;background:#f1f5f9">
                                            <div class="progress-bar rounded-pill" style="width:' . $commentRate . '%;background:#10b981"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="extra-small fw-semibold text-muted">' . _('User Activity') . '</span>
                                            <span class="extra-small fw-bold text-dark">' . $userActRate . '%</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height:6px;background:#f1f5f9">
                                            <div class="progress-bar rounded-pill" style="width:' . $userActRate . '%;background:#f59e0b"></div>
                                        </div>
                                    </div>
                                </div>'
                            ]],
                        ],

                        // Recent Users
                        [
                            'type' => 'card',
                            'title' => _('New Members'),
                            'icon' => 'bi bi-person-plus',
                            'class' => 'mt-4',
                            'body_elements' => [[
                                'type' => 'raw',
                                'html' => (function() use ($recentUsers) {
                                    if (!$recentUsers || Db::$num_rows === 0) {
                                        return '<p class="text-muted extra-small text-center py-2">' . _('No users yet.') . '</p>';
                                    }
                                    $html = '<div class="d-flex flex-column gap-2">';
                                    foreach ($recentUsers as $u) {
                                        $av = Image::getGravatar($u->email, 32);
                                        $html .= "
                                        <a href='index.php?page=users&act=edit&id={$u->id}&token=" . TOKEN . "'
                                           class='d-flex align-items-center gap-3 text-decoration-none p-2 rounded-3 hover-bg'
                                           style='transition:background .12s'>
                                            <img src='{$av}' class='rounded-circle flex-shrink-0' width='32' height='32' style='object-fit:cover'>
                                            <div class='flex-grow-1 min-width-0'>
                                                <div class='fw-semibold text-dark' style='font-size:.78rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis'>"
                                                    . Typo::Xclean($u->userid) . "
                                                </div>
                                                <div class='extra-small text-muted'>" . Date::format($u->join_date) . "</div>
                                            </div>
                                            <i class='bi bi-chevron-right extra-small text-muted opacity-50'></i>
                                        </a>";
                                    }
                                    $html .= '</div>';
                                    return $html;
                                })()
                            ]],
                            'footer' => '<a href="index.php?page=users" class="extra-small fw-bold text-primary text-decoration-none">' . _('All Users') . ' <i class="bi bi-arrow-right ms-1"></i></a>',
                        ],
                    ]
                ],
            ]
        ],

        // ── BOTTOM ROW: Top Content + World Map + System ──────────
        [
            'type' => 'row',
            'items' => [
                // Top Content
                [
                    'width' => 4,
                    'content' => [
                        'type' => 'card',
                        'title' => _('Top Content'),
                        'icon' => 'bi bi-trophy',
                        'subtitle' => _('Most viewed posts'),
                        'no_padding' => true,
                        'body_elements' => [[
                            'type' => 'table',
                            'headers' => [],
                            'rows' => $viewedRows,
                            'empty_message' => _('No view data yet.'),
                        ]],
                    ]
                ],

                // World Map
                [
                    'width' => 5,
                    'content' => [
                        'type' => 'card',
                        'title' => _('Audience Map'),
                        'icon' => 'bi bi-globe-americas',
                        'subtitle' => _('Geographic distribution'),
                        'no_padding' => true,
                        'body_elements' => [[
                            'type' => 'raw',
                            'html' => '<div id="world-map" style="height:280px;background:#f8fafc"></div>',
                        ]],
                    ]
                ],

                // System Info
                [
                    'width' => 3,
                    'content' => [
                        'type' => 'card',
                        'title' => _('System'),
                        'icon' => 'bi bi-cpu',
                        'body_elements' => [[
                            'type' => 'raw',
                            'html' => '
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="extra-small text-muted fw-semibold">' . _('PHP') . '</span>
                                    <span class="badge bg-light text-dark border extra-small fw-bold">' . PHP_VERSION . '</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="extra-small text-muted fw-semibold">' . _('CMS') . '</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 extra-small fw-bold">v' . System::$version . '</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="extra-small text-muted fw-semibold">' . _('DB Driver') . '</span>
                                    <span class="badge bg-light text-dark border extra-small fw-bold">' . strtoupper(DB_DRIVER) . '</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="extra-small text-muted fw-semibold">' . _('Memory') . '</span>
                                    <span class="badge bg-light text-dark border extra-small fw-bold">' . $memLimit . '</span>
                                </div>
                                ' . ($diskTotal > 0 ? '
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="extra-small text-muted fw-semibold">' . _('Disk Usage') . '</span>
                                        <span class="extra-small fw-bold text-dark">' . $diskUsed . '%</span>
                                    </div>
                                    <div class="progress rounded-pill" style="height:5px;background:#f1f5f9">
                                        <div class="progress-bar rounded-pill" style="width:' . $diskUsed . '%;background:' . ($diskUsed > 80 ? '#ef4444' : ($diskUsed > 60 ? '#f59e0b' : '#10b981')) . '"></div>
                                    </div>
                                    <div class="extra-small text-muted mt-1">' . round(($diskTotal - $diskFree) / 1073741824, 1) . ' GB / ' . round($diskTotal / 1073741824, 1) . ' GB</div>
                                </div>' : '') . '
                                ' . ($uptime ? '<div class="d-flex justify-content-between align-items-center">
                                    <span class="extra-small text-muted fw-semibold">' . _('Uptime') . '</span>
                                    <span class="badge bg-light text-dark border extra-small fw-bold">' . $uptime . '</span>
                                </div>' : '') . '
                                <div class="pt-2 border-top">
                                    <a href="index.php?page=health" class="extra-small fw-bold text-primary text-decoration-none">
                                        <i class="bi bi-heart-pulse me-1"></i>' . _('System Health') . '
                                    </a>
                                    &nbsp;·&nbsp;
                                    <a href="index.php?page=updates" class="extra-small fw-bold text-primary text-decoration-none">
                                        <i class="bi bi-arrow-repeat me-1"></i>' . _('Updates') . '
                                    </a>
                                </div>
                            </div>'
                        ]],
                    ]
                ],
            ]
        ],
    ]
];
?>

<style>
.wave-emoji { display:inline-block; animation:wave 2.5s infinite; transform-origin:70% 70% }
@keyframes wave {
    0%,60%,100%{transform:rotate(0deg)}
    10%{transform:rotate(14deg)} 20%{transform:rotate(-8deg)}
    30%{transform:rotate(14deg)} 40%{transform:rotate(-4deg)}
    50%{transform:rotate(10deg)}
}
.hover-bg:hover { background:#f8fafc !important }
.extra-small { font-size:.72rem !important }
.fw-black { font-weight:900 !important }
.fs-7 { font-size:.88rem !important }
.fs-8 { font-size:.78rem !important }
</style>

<script>
(function(){
    // Live clock
    function updateClock(){
        var el = document.getElementById('dash-clock');
        if(!el) return;
        var n = new Date();
        el.textContent = String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0');
    }
    setInterval(updateClock, 30000);

    // World map
    document.addEventListener('DOMContentLoaded', function(){
        var mapEl = document.getElementById('world-map');
        if(!mapEl || typeof jsVectorMap === 'undefined') return;
        new jsVectorMap({
            selector: '#world-map',
            map: 'world',
            zoomButtons: false,
            regionStyle: {
                initial: { fill:'#e2e8f0', stroke:'none' },
                hover:   { fill:'var(--gx-primary,#00A3EA)', cursor:'pointer' }
            },
            visualizeData: {
                scale: ['#e0f2fe','var(--gx-primary,#00A3EA)'],
                values: { US:100, ID:350, GB:80, IN:120, DE:60, FR:45, AU:90, JP:150, BR:70, CA:55 }
            }
        });
    });
})();
</script>

<?php
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo Hooks::run('admin_page_top_action', $data);
echo '</div>';

$schema = Hooks::filter('admin_dashboard_schema', $schema);
$builder = new UiBuilder($schema);
$builder->render();

echo '<div class="col-md-12">';
echo Hooks::run('admin_page_bottom_action', $data);
echo '</div>';
