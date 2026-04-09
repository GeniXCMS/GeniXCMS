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

// ── PREPARE DATA ──────────────────────────────────────────────────
$rows = [];
if ($data['num'] > 0) {
    foreach ($data['usr'] as $p) {
        $pObj = (object) $p;
        $grp = User::$group[$pObj->group] ?? "Unknown ({$pObj->group})";

        if ($pObj->status == '0') {
            $statusBadge = '<a href="index.php?page=users&act=active&id=' . $pObj->id . '&token=' . TOKEN . '" class="badge bg-danger bg-opacity-10 text-danger text-decoration-none px-3 py-2 rounded-pill fw-bold" style="font-size: 0.65rem;">' . _("Inactive") . '</a>';
        } else {
            $statusBadge = '<a href="index.php?page=users&act=inactive&id=' . $pObj->id . '&token=' . TOKEN . '" class="badge bg-success bg-opacity-10 text-success text-decoration-none px-3 py-2 rounded-pill fw-bold" style="font-size: 0.65rem;">' . _("Active") . '</a>';
        }
        $country = ($pObj->country != "") ? strtolower($pObj->country) : "unknown";
        $originToken = ($country != "unknown")
            ? "<span class='flag-icon flag-icon-{$country} shadow-sm rounded-1' title='" . strtoupper($country) . "'></span>"
            : "<i class='bi bi-geo-alt text-muted opacity-50'></i>";

        $rows[] = [
            ['content' => "<span class='text-muted extra-small'>#{$pObj->id}</span>", 'class' => 'ps-4 py-3'],
            "<div class='d-flex align-items-center py-2'>
                <div class='bg-primary bg-opacity-10 p-2 rounded-circle text-primary me-3 d-flex align-items-center justify-content-center border border-primary border-opacity-10' style='width: 42px; height: 42px;'><i class='bi bi-person fs-5'></i></div>
                <div>
                    <div class='fw-bold text-dark mb-0 ls-n1' style='font-size: 0.95rem;'>{$pObj->userid}</div>
                    <div class='text-muted extra-small'>{$pObj->email}</div>
                </div>
             </div>",
            ['content' => "<span class='badge bg-dark bg-opacity-10 text-dark border-0 px-3 py-2 rounded-pill fw-bold text-uppercase' style='font-size: 0.65rem;'>{$grp}</span>", 'class' => 'text-center'],
            ['content' => "<div class='mb-1'>{$statusBadge}</div><div class='extra-small text-muted fw-bold'>" . Date::format($pObj->join_date, 'd M Y') . "</div>", 'class' => 'text-center'],
            ['content' => $originToken, 'class' => 'text-center'],
            [
                'content' => "
                <div class='btn-group gap-1'>
                    <a href='index.php?page=users&act=edit&id={$pObj->id}&token=" . TOKEN . "' class='btn btn-light btn-sm rounded-circle border' title='Edit Profile'><i class='bi bi-pencil-square text-success'></i></a>
                    <a href='index.php?page=users&act=del&id={$pObj->id}&token=" . TOKEN . "' class='btn btn-light btn-sm rounded-circle border' onclick=\"return confirm('" . _("Remove user permanently?") . "');\"><i class='bi bi-trash text-danger'></i></a>
                </div>",
                'class' => 'text-end pe-4'
            ],
            ['content' => "<div class='text-center pe-4'><input type='checkbox' name='user_id[]' value='{$pObj->id}' class='check form-check-input shadow-none border'></div>", 'class' => 'p-0']
        ];
    }
}

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('User Infrastructure'),
        'subtitle' => _('Monitor and manage administrative access and user community.'),
        'icon' => 'bi bi-people',
        'button' => [
            'url' => '#',
            'label' => _('New User'),
            'icon' => 'bi bi-person-plus-fill',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#adduser"'
        ],
    ],
    'content' => [
        [
            'type' => 'stat_cards',
            'size' => 'small',
            'items' => [
                ['label' => _('Total Library'), 'value' => (string) Stats::totalUser(), 'icon' => 'bi bi-people', 'color' => 'primary'],
                ['label' => _('Active Accounts'), 'value' => (string) Stats::activeUser(), 'icon' => 'bi bi-person-check', 'color' => 'success'],
                ['label' => _('Pending Verification'), 'value' => (string) Stats::pendingUser(), 'icon' => 'bi bi-clock-history', 'color' => 'warning'],
                ['label' => _('Inactive/Blocked'), 'value' => (string) Stats::inactiveUser(), 'icon' => 'bi bi-person-x', 'color' => 'danger']
            ]
        ],
        [
            'type' => 'card',
            'title' => _('Account Registry'),
            'icon' => 'bi bi-person-lines-fill',
            'no_padding' => true,
            'header_action' => '
                <form action="index.php?page=users" method="get" class="d-flex gap-2 flex-wrap justify-content-end align-items-center">
                    <input type="hidden" name="page" value="users">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="' . _("Account...") . '" style="width:140px;" value="' . ($_GET['q'] ?? '') . '">
                    </div>
                    <div class="d-flex gap-1 align-items-center bg-white border rounded-pill px-2 shadow-sm">
                        <i class="bi bi-calendar-check text-muted ms-1" style="font-size:0.75rem;"></i>
                        <input type="date" name="from" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . ($_GET['from'] ?? '') . '" title="' . _("Joined From") . '">
                        <span class="text-muted small">-</span>
                        <input type="date" name="to" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . ($_GET['to'] ?? '') . '" title="' . _("Joined To") . '">
                    </div>
                    ' . User::dropdown(['name' => 'group', 'selected' => ($_GET['group'] ?? ''), 'class' => 'form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm', 'attr' => 'style="width:140px;"']) . '
                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:110px;">
                        <option value="">' . _("All Status") . '</option>
                        <option value="1" ' . (isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '') . '>' . _("Active") . '</option>
                        <option value="0" ' . (isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '') . '>' . _("Inactive") . '</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> ' . _("Filter") . '</button>
                    <a href="index.php?page=users" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm" title="' . _("Reset") . '"><i class="bi bi-arrow-counterclockwise"></i></a>
                </form>',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => '',
                    'attr' => 'id="users-bulk-form"',
                    'fields' => [
                        [
                            'type' => 'table',
                            'headers' => [
                                ['content' => _('ID'), 'class' => 'ps-4 py-3', 'width' => '60px'],
                                _('Identity'),
                                ['content' => _('Permission Level'), 'class' => 'text-center'],
                                ['content' => _('Journey Status'), 'class' => 'text-center'],
                                ['content' => _('Origin'), 'class' => 'text-center'],
                                ['content' => _('Interaction'), 'class' => 'text-end pe-4'],
                                ['content' => '<input type="checkbox" id="selectall" class="form-check-input">', 'class' => 'text-center pe-4', 'width' => '50px']
                            ],
                            'rows' => $rows,
                            'empty_message' => _('No users found matching your search criteria.')
                        ]
                    ]
                ]
            ],
            'footer' => '
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        ' . ((new UiBuilder())->renderElement([
                    'type' => 'bulk_actions',
                    'options' => [
                        'activate' => _('Batch Activate'),
                        'deactivate' => _('Batch Deactivate'),
                        'delete' => _('Batch Delete')
                    ],
                    'button_label' => _('Start Action'),
                    'form' => 'users-bulk-form'
                ], true)) . '
                    </div>
                    <div>' . $data['paging'] . '</div>
                </div>'
        ],
        // Modal
        [
            'type' => 'modal',
            'id' => 'adduser',
            'header' => _("Onboard New User"),
            'size' => 'md',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php?page=users',
                    'fields' => [
                        ['type' => 'input', 'name' => 'userid', 'label' => _("Username"), 'required' => true],
                        [
                            'type' => 'row',
                            'items' => [
                                ['width' => 6, 'content' => ['type' => 'input', 'name' => 'pass1', 'label' => _("Password"), 'input_type' => 'password', 'required' => true]],
                                ['width' => 6, 'content' => ['type' => 'input', 'name' => 'pass2', 'label' => _("Verify"), 'input_type' => 'password', 'required' => true]]
                            ]
                        ],
                        ['type' => 'input', 'name' => 'email', 'label' => _("Email Address"), 'input_type' => 'email', 'required' => true],
                        [
                            'type' => 'raw',
                            'html' => '
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">' . _("Permission Group") . '</label>
                                ' . User::dropdown(['name' => 'group', 'selected' => '6', 'update' => true, 'class' => 'form-select border-0 bg-light rounded-4 py-2 px-3 shadow-none']) . '
                            </div>
                            <input type="hidden" name="token" value="' . TOKEN . '">'
                        ],
                        ['type' => 'button', 'name' => 'adduser', 'label' => _("Create Account"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100']
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

<script>
    $(document).ready(function () {
        $('#selectall').click(function () { $('.check').prop('checked', this.checked); });
        $('.check').click(function () {
            if (!this.checked) $('#selectall').prop('checked', false);
            if ($('.check:checked').length == $('.check').length && $('.check').length > 0) $('#selectall').prop('checked', true);
        });
    });
</script>

<style>
    .flag-icon {
        width: 22px;
        height: 16px;
        display: inline-block;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>