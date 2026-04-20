<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$username = Session::val('username');
$group = Session::val('group');

// Stats Data
$statsItems = [
    ['label' => _('Total Accounts'), 'value' => (string) Stats::totalUser(), 'icon' => 'bi bi-people', 'color' => 'primary'],
    ['label' => _('Active Staff'), 'value' => (string) Stats::activeUser(), 'icon' => 'bi bi-person-check', 'color' => 'success'],
    ['label' => _('Inactive'), 'value' => (string) Stats::inactiveUser(), 'icon' => 'bi bi-person-x', 'color' => 'warning']
];

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('User Infrastructure'),
        'subtitle' => _('Monitor and manage administrative access and user community.'),
        'icon' => 'bi bi-people',
        'button' => [
            'type' => 'link',
            'href' => '#',
            'label' => _('New User'),
            'icon' => 'bi bi-person-plus-fill',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#adduser"'
        ],
    ],
    'content' => [
        ['type' => 'stat_cards', 'size' => 'small', 'items' => $statsItems],
        [
            'type' => 'card',
            'title' => _('Identity Directory'),
            'icon' => 'bi bi-database-fill',
            'no_padding' => true,
            'footer_no_padding' => true,
            'footer_class' => 'card-footer bg-transparent border-0 p-0',
            'header_action' => '
                <form id="users-filter-form" class="d-flex gap-2 flex-wrap justify-content-end align-items-center" onsubmit="loadUsers(); return false;">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="' . _("Search users...") . '" style="width:140px;" value="' . Typo::cleanX($_GET['q'] ?? '') . '">
                    </div>
                    <select name="group" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:130px;">
                        <option value="">' . _("All Roles") . '</option>
                        ' . (function() {
                            $html = '';
                            foreach (User::$group as $k => $v) {
                                $selected = (isset($_GET['group']) && $_GET['group'] !== '' && (int)$_GET['group'] == $k) ? 'selected' : '';
                                $html .= "<option value='{$k}' {$selected}>{$v}</option>";
                            }
                            return $html;
                        })() . '
                    </select>
                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:110px;">
                        <option value="">' . _("All Status") . '</option>
                        <option value="1" ' . (isset($_GET['status']) && $_GET['status'] !== '' && (int)$_GET['status'] == 1 ? 'selected' : '') . '>' . _("Active") . '</option>
                        <option value="0" ' . (isset($_GET['status']) && $_GET['status'] !== '' && (int)$_GET['status'] == 0 ? 'selected' : '') . '>' . _("Inactive") . '</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> ' . _("Filter") . '</button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="loadUsers()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
                </form>',
            'body_elements' => [
                [
                    'type' => 'raw',
                    'html' => '
                        <form action="" method="post" id="users-bulk-form">
                            <div id="users-desktop-container" class="p-0">
                                <div class="d-flex justify-content-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="token" value="' . TOKEN . '">
                        </form>
                    '
                ]
            ],
            'footer' => '
                <div class="d-flex justify-content-between align-items-center w-100 p-3">
                    <div class="bulk-action-wrapper">
                        ' . ((new UiBuilder())->renderElement([
                    'type' => 'bulk_actions',
                    'button_label' => _('Apply to Selected'),
                    'options' => [
                        'activate' => _('Set as Active'),
                        'inactive' => _('Set as Inactive'),
                        'delete' => _('Remove Permanently')
                    ],
                    'form' => 'users-bulk-form'
                ], true)) . '
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="users-pagination-info" class="small text-muted fw-bold"></div>
                        <nav id="users-pagination-nav"></nav>
                    </div>
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

// Allow developers to modify the entire users dashboard schema
$schema = Hooks::filter('admin_users_schema', $schema);

$builder = new UiBuilder($schema);
$builder->render();
?>

<style>
    .ls-1 { letter-spacing: 0.5px; }
    .ls-n1 { letter-spacing: -0.5px; }
    .extra-small { font-size: 0.65rem !important; }
    .pagination-wrapper.pagination { margin-bottom: 0; gap: 5px; }
    .pagination-wrapper .page-link {
        border-radius: 50% !important;
        width: 35px; height: 35px;
        display: flex; align-items: center; justify-content: center;
        border: 0; background: #f8f9fa; color: #6c757d;
        font-weight: bold; font-size: 0.85rem;
    }
    .pagination-wrapper.pagination .page-item.active .page-link {
        background: var(--gx-primary); color: #fff;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }
    .flag-icon {
        width: 22px;
        height: 16px;
        display: inline-block;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>

<script>
    $(document).ready(function () {
        loadUsers();
    });

    function loadUsers(offset = 0) {
        const container = $('#users-desktop-container');
        const filterData = $('#users-filter-form').serialize();
        const ajaxUrl = '<?= Url::ajax("user", "list_users") ?>&offset=' + offset + '&' + filterData;

        container.html(`
            <div class="d-flex justify-content-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.getJSON(ajaxUrl, function(response) {
            if (response.status === 'success') {
                renderUsersTable(response.headers, response.data);
                renderUsersPagination(response.total, response.limit, response.offset);
            } else {
                container.html(`<div class="alert alert-danger m-3">${response.message || 'Error loading records'}</div>`);
            }
        }).fail(function() {
            container.html('<div class="alert alert-danger m-3">Failed to connect to server.</div>');
        });
    }

    function renderUsersTable(headers, data) {
        const container = $('#users-desktop-container');
        if (data.length === 0) {
            container.html('<div class="p-5 text-center text-muted">No records found.</div>');
            return;
        }

        let html = `
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
        `;

        headers.forEach(h => {
            html += `<th class="${h.class || ''}" ${h.width ? 'width="'+h.width+'"' : ''}>${h.content}</th>`;
        });

        html += `
                    </tr>
                </thead>
                <tbody>
        `;

        data.forEach(row => {
            html += `<tr>`;
            row.forEach(cell => {
                html += `<td class="${cell.class || ''}">${cell.content}</td>`;
            });
            html += `</tr>`;
        });

        html += `
                </tbody>
            </table>
        `;

        container.html(html);

        $('#checkAll').click(function () {
            $('.check').prop('checked', this.checked);
        });
        $('.check').click(function () {
            if (!this.checked) {
                $('#checkAll').prop('checked', false);
            }
            if ($('.check:checked').length == $('.check').length && $('.check').length > 0) {
                $('#checkAll').prop('checked', true);
            }
        });
    }

    function renderUsersPagination(total, limit, offset) {
        const nav = $('#users-pagination-nav');
        const info = $('#users-pagination-info');
        
        const start = total === 0 ? 0 : offset + 1;
        const end = Math.min(offset + limit, total);
        info.text(`Showing ${start} to ${end} of ${total} records`);

        if (total <= limit) {
            nav.empty();
            return;
        }

        const currentPage = Math.floor(offset / limit) + 1;
        const totalPages = Math.ceil(total / limit);

        let html = '<ul class="pagination pagination-sm pagination-wrapper mb-0 gap-1">';
        
        // Prev
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadUsers(${offset - limit}); return false;"><i class="bi bi-chevron-left"></i></a>
                 </li>`;

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="loadUsers(${(i - 1) * limit}); return false;">${i}</a>
                         </li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadUsers(${offset + limit}); return false;"><i class="bi bi-chevron-right"></i></a>
                 </li>`;

        html += '</ul>';
        nav.html(html);
    }
</script>
