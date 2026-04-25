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
$postType = $data['postType'];

// Stats Data
$statsItems = [
    ['label' => _('Total Library'), 'value' => (string) Stats::totalPost($postType), 'icon' => 'bi bi-journal-album', 'color' => 'primary'],
    ['label' => _('Live Assets'), 'value' => (string) Stats::activePost($postType), 'icon' => 'bi bi-broadcast-pin', 'color' => 'success'],
    ['label' => _('Archived / Draft'), 'value' => (string) Stats::inactivePost($postType), 'icon' => 'bi bi-archive', 'color' => 'warning']
];

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => Posts::getTypeLabel($postType, 'repository_title'),
        'subtitle' => _('Manage your digital publication assets with real-time engagement insights.'),
        'icon' => 'bi bi-journal-text',
        'button' => [
            'type' => 'link',
            'href' => 'index.php?page=posts&act=add&type=' . $postType . '&token=' . TOKEN,
            'label' => Posts::getTypeLabel($postType, 'new_item'),
            'icon' => 'bi bi-plus-lg',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold'
        ],
    ],
    'content' => [
        ['type' => 'stat_cards', 'size' => 'small', 'items' => $statsItems],
        [
            'type' => 'card',
            'title' => Posts::getTypeLabel($postType, 'records_library'),
            'icon' => 'bi bi-database-fill',
            'no_padding' => true,
            'footer_no_padding' => true,
            'footer_class' => 'card-footer bg-transparent border-0 p-0',
            'header_action' => '
                <form id="posts-filter-form" class="d-flex gap-2 flex-wrap justify-content-end align-items-center" onsubmit="loadPosts(); return false;">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="' . _("Search...") . '" style="width:140px;" value="' . Typo::cleanX($_GET['q'] ?? '') . '">
                    </div>
                    <div class="d-flex gap-1 align-items-center bg-white border rounded-pill px-2 shadow-sm">
                        <i class="bi bi-calendar-event text-muted ms-1" style="font-size:0.75rem;"></i>
                        <input type="date" name="from" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . Typo::cleanX($_GET['from'] ?? '') . '" title="' . _("From Date") . '">
                        <span class="text-muted small">-</span>
                        <input type="date" name="to" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . Typo::cleanX($_GET['to'] ?? '') . '" title="' . _("To Date") . '">
                    </div>
                    ' . Categories::dropdown(['name' => 'cat', 'type' => $postType, 'class' => 'form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm', 'selected' => Typo::int($_GET['cat'] ?? ''), 'attr' => 'style="width:130px;"']) . '
                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:110px;">
                        <option value="">' . _("All Status") . '</option>
                        <option value="1" ' . (isset($_GET['status']) && $_GET['status'] !== '' && Typo::int($_GET['status']) == 1 ? 'selected' : '') . '>' . _("Live") . '</option>
                        <option value="0" ' . (isset($_GET['status']) && $_GET['status'] !== '' && Typo::int($_GET['status']) == 0 ? 'selected' : '') . '>' . _("Draft") . '</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> ' . _("Filter") . '</button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="loadPosts()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
                </form>',
            'body_elements' => [
                [
                    'type' => 'raw',
                    'html' => '
                        <form action="" method="post" id="posts-bulk-form">
                            <div id="posts-desktop-container" class="p-0">
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
                        'publish' => _('Commit to Live'),
                        'unpublish' => _('Revert to Draft'),
                        'delete' => _('Permanent Deletion')
                    ],
                    'form' => 'posts-bulk-form'
                ], true)) . '
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="posts-pagination-info" class="small text-muted fw-bold"></div>
                        <nav id="posts-pagination-nav"></nav>
                    </div>
                </div>'
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

// Allow developers to modify the entire posts dashboard schema
$schema = Hooks::filter('admin_posts_schema', $schema, $data['postType']);

$builder = new UiBuilder($schema);
$builder->render();
?>

<style>
    .ls-1 {
        letter-spacing: 0.5px;
    }

    .ls-n1 {
        letter-spacing: -0.5px;
    }

    .extra-small {
        font-size: 0.65rem !important;
    }

    .pagination-wrapper .pagination {
        margin-bottom: 0;
        gap: 5px;
    }

    .pagination-wrapper .page-link {
        border-radius: 50% !important;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        background: #f8f9fa;
        color: #6c757d;
        font-weight: bold;
        font-size: 0.85rem;
    }

    .pagination-wrapper .page-item.active .page-link {
        background: var(--gx-primary);
        color: #fff;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);
    }
</style>

<script>
    $(document).ready(function () {
        $('#selectall').click(function () {
            $('.check').prop('checked', this.checked);
        });
        $('.check').click(function () {
            if (!this.checked) {
                $('#selectall').prop('checked', false);
            }
            if ($('.check:checked').length == $('.check').length && $('.check').length > 0) {
                $('#selectall').prop('checked', true);
            }
        });

        loadPosts();
    });

    function loadPosts(offset = 0) {
        const container = $('#posts-desktop-container');
        const filterData = $('#posts-filter-form').serialize();
        const ajaxUrl = '<?= Url::ajax("posts", "list_posts") ?>&type=<?= $postType ?>&offset=' + offset + '&' + filterData;

        // Show loading state
        container.html(`
            <div class="d-flex justify-content-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.getJSON(ajaxUrl, function(response) {
            if (response.status === 'success') {
                renderPostsTable(response.headers, response.data);
                renderPostsPagination(response.total, response.limit, response.offset);
            } else {
                container.html(`<div class="alert alert-danger m-3">${response.message || 'Error loading records'}</div>`);
            }
        }).fail(function() {
            container.html('<div class="alert alert-danger m-3">Failed to connect to server.</div>');
        });
    }

    function renderPostsTable(headers, data) {
        const container = $('#posts-desktop-container');
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

        // Re-bind checkbox logic for dynamic content
        $('#selectall').click(function () {
            $('.check').prop('checked', this.checked);
        });
        $('.check').click(function () {
            if (!this.checked) {
                $('#selectall').prop('checked', false);
            }
            if ($('.check:checked').length == $('.check').length && $('.check').length > 0) {
                $('#selectall').prop('checked', true);
            }
        });
    }

    function renderPostsPagination(total, limit, offset) {
        const info = $('#posts-pagination-info');
        const nav = $('#posts-pagination-nav');
        
        const start = offset + 1;
        const end = Math.min(offset + limit, total);
        info.html(`Showing ${start} to ${end} of ${total} entries`);

        let paginationHtml = '<ul class="pagination pagination-sm mb-0 gap-1">';
        const totalPages = Math.ceil(total / limit);
        const currentPage = Math.floor(offset / limit) + 1;

        // Previous
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadPosts(${(currentPage - 2) * limit}); return false;"><i class="bi bi-chevron-left"></i></a>
            </li>
        `;

        // Page numbers (simplified)
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadPosts(${(i - 1) * limit}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadPosts(${currentPage * limit}); return false;"><i class="bi bi-chevron-right"></i></a>
            </li>
        `;

        paginationHtml += '</ul>';
        nav.html(paginationHtml);
    }
</script>