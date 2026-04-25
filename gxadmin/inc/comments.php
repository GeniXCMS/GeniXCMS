<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.4.0
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
    ['label' => _('Total Feed'), 'value' => (string) Stats::totalComments(), 'icon' => 'bi bi-chat-dots', 'color' => 'primary'],
    ['label' => _('Active'), 'value' => (string) Stats::activeComments(), 'icon' => 'bi bi-patch-check', 'color' => 'success'],
    ['label' => _('Pending'), 'value' => (string) Stats::pendingComments(), 'icon' => 'bi bi-hourglass-split', 'color' => 'warning'],
    ['label' => _('Spam/Blocked'), 'value' => (string) Stats::inactiveComments(), 'icon' => 'bi bi-shield-exclamation', 'color' => 'danger']
];

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Engagement Audit'),
        'subtitle' => _('Review and moderate user interactions across your digital assets.'),
        'icon' => 'bi bi-chat-dots',
    ],
    'content' => [
        ['type' => 'stat_cards', 'size' => 'small', 'items' => $statsItems],
        [
            'type' => 'card',
            'title' => _('Message Queue'),
            'icon' => 'bi bi-chat-left-text-fill',
            'no_padding' => true,
            'footer_no_padding' => true,
            'footer_class' => 'card-footer bg-transparent border-0 p-0',
            'header_action' => '
                <form id="comments-filter-form" class="d-flex gap-2 flex-wrap justify-content-end align-items-center" onsubmit="loadComments(); return false;">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="' . _("Keyword...") . '" style="width:140px;" value="' . Typo::cleanX($_GET['q'] ?? '') . '">
                    </div>
                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:110px;">
                        <option value="">' . _("All Status") . '</option>
                        <option value="1" ' . (isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '') . '>' . _("Approved") . '</option>
                        <option value="2" ' . (isset($_GET['status']) && $_GET['status'] == '2' ? 'selected' : '') . '>' . _("Pending") . '</option>
                        <option value="0" ' . (isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '') . '>' . _("Hidden") . '</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> ' . _("Filter") . '</button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="loadComments()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
                </form>',
            'body_elements' => [
                [
                    'type' => 'raw',
                    'html' => '
                        <form action="" method="post" id="comments-bulk-form">
                            <div id="comments-desktop-container" class="p-0">
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
                    'button_label' => _('Process'),
                    'options' => [
                        'approve' => _('Approve Selected'),
                        'unapprove' => _('Hide Selected'),
                        'delete' => _('Purge Selected')
                    ],
                    'form' => 'comments-bulk-form'
                ], true)) . '
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="comments-pagination-info" class="small text-muted fw-bold"></div>
                        <nav id="comments-pagination-nav"></nav>
                    </div>
                </div>'
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

// Allow developers to modify the entire comments dashboard schema
$schema = Hooks::filter('admin_comments_schema', $schema);

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
</style>

<script>
    $(document).ready(function () {
        loadComments();
    });

    function loadComments(offset = 0) {
        const container = $('#comments-desktop-container');
        const filterData = $('#comments-filter-form').serialize();
        const ajaxUrl = '<?= Url::ajax("comments", "list_comments") ?>&offset=' + offset + '&' + filterData;

        container.html(`
            <div class="d-flex justify-content-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.getJSON(ajaxUrl, function(response) {
            if (response.status === 'success') {
                renderCommentsTable(response.headers, response.data);
                renderCommentsPagination(response.total, response.limit, response.offset);
            } else {
                container.html(`<div class="alert alert-danger m-3">${response.message || 'Error loading records'}</div>`);
            }
        }).fail(function() {
            container.html('<div class="alert alert-danger m-3">Failed to connect to server.</div>');
        });
    }

    function renderCommentsTable(headers, data) {
        const container = $('#comments-desktop-container');
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

    function renderCommentsPagination(total, limit, offset) {
        const nav = $('#comments-pagination-nav');
        const info = $('#comments-pagination-info');
        
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
                    <a class="page-link" href="#" onclick="loadComments(${offset - limit}); return false;"><i class="bi bi-chevron-left"></i></a>
                 </li>`;

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="loadComments(${(i - 1) * limit}); return false;">${i}</a>
                         </li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadComments(${offset + limit}); return false;"><i class="bi bi-chevron-right"></i></a>
                 </li>`;

        html += '</ul>';
        nav.html(html);
    }
</script>
