<?php
/**
 * Nixomers Orders Management View (AJAX Based)
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$q = Typo::cleanX($_REQUEST['q'] ?? '');
$sort = Typo::cleanX($_REQUEST['sort'] ?? 'newest');
$statusFilter = Typo::cleanX($_REQUEST['status'] ?? 'all');
$startDate = Typo::cleanX($_REQUEST['start_date'] ?? '');
$endDate = Typo::cleanX($_REQUEST['end_date'] ?? '');

$currency = Options::v('nixomers_currency') ?: 'IDR';

// Header Rendering (Analytics Style)
$schema = [
    'header' => [
        'title' => 'Orders Management',
        'subtitle' => 'Track and manage your incoming customer orders.',
        'icon' => 'bi bi-cart3',
        'button' => [
            'type' => 'link',
            'href' => 'index.php?page=mods&mod=nixomers&sel=orders&act=add',
            'label' => 'Create New Order',
            'icon' => 'bi bi-plus-lg',
            'class' => 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm'
        ]
    ],
    'content' => [
        [
            'type' => 'stat_cards',
            'size' => 'small',
            'items' => [
                ['label' => 'Total Orders', 'value' => (string) Query::table('nix_orders')->count(), 'icon' => 'bi bi-box-seam', 'color' => 'dark'],
                ['label' => 'Pending Process', 'value' => (string) Query::table('nix_orders')->where('status', 'pending')->count(), 'icon' => 'bi bi-clock-history', 'color' => 'warning'],
                ['label' => 'Total Completed', 'value' => (string) Query::table('nix_orders')->where('status', 'completed')->count(), 'icon' => 'bi bi-check-all', 'color' => 'success'],
                ['label' => 'Gross Revenue', 'value' => $currency . ' ' . number_format((float) Query::table('nix_orders')->sum('total'), 0), 'icon' => 'bi bi-graph-up-arrow', 'color' => 'primary']
            ]
        ],
        [
            'type' => 'card',
            'title' => 'Recent Orders',
            'icon' => 'bi bi-receipt',
            'no_padding' => true,
            'footer_no_padding' => true,
            'footer_class' => 'card-footer bg-transparent border-0 p-0',
            'header_action' => '
                <form id="orders-filter-form" class="d-flex flex-wrap flex-xl-nowrap gap-2 align-items-center justify-content-end" onsubmit="loadOrders(); return false;">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="Search orders..." style="width:140px;" value="' . htmlspecialchars($q) . '">
                    </div>

                    <div class="d-flex gap-1 align-items-center bg-white border rounded-pill px-2 shadow-sm">
                        <i class="bi bi-calendar-event text-muted ms-1" style="font-size:0.75rem;"></i>
                        <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . $startDate . '">
                        <span class="text-muted small">-</span>
                        <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . $endDate . '">
                    </div>

                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:130px;">
                        <option value="all" ' . ($statusFilter == 'all' ? 'selected' : '') . '>All Status</option>
                        <option value="pending" ' . ($statusFilter == 'pending' ? 'selected' : '') . '>Pending</option>
                        <option value="waiting" ' . ($statusFilter == 'waiting' ? 'selected' : '') . '>Waiting Process</option>
                        <option value="onprocess" ' . ($statusFilter == 'onprocess' ? 'selected' : '') . '>On Process</option>
                        <option value="ready_to_ship" ' . ($statusFilter == 'ready_to_ship' ? 'selected' : '') . '>Ready to Ship</option>
                        <option value="shipped" ' . ($statusFilter == 'shipped' ? 'selected' : '') . '>Shipped</option>
                        <option value="delivered" ' . ($statusFilter == 'delivered' ? 'selected' : '') . '>Delivered</option>
                        <option value="completed" ' . ($statusFilter == 'completed' ? 'selected' : '') . '>Completed</option>
                        <option value="cancelled" ' . ($statusFilter == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                    </select>

                    <select name="sort" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:120px;">
                        <option value="newest" ' . ($sort == 'newest' ? 'selected' : '') . '>Newest</option>
                        <option value="oldest" ' . ($sort == 'oldest' ? 'selected' : '') . '>Oldest</option>
                        <option value="highest" ' . ($sort == 'highest' ? 'selected' : '') . '>Highest</option>
                        <option value="lowest" ' . ($sort == 'lowest' ? 'selected' : '') . '>Lowest</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="loadOrders()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
                </form>',
            'body_elements' => [
                [
                    'type' => 'raw',
                    'html' => '
                        <form action="" method="post" id="orders-bulk-form">
                            <div id="orders-desktop-container" class="p-0">
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
                    'button_label' => _('Apply Governance'),
                    'options' => [
                        'pending' => 'Mark Pending',
                        'waiting' => 'Mark Waiting Process',
                        'onprocess' => 'Mark On Process',
                        'ready_to_ship' => 'Mark Ready to Ship',
                        'shipped' => 'Mark Shipped',
                        'completed' => 'Mark Completed',
                        'cancelled' => 'Mark Cancelled',
                        'delete' => 'Delete Permanently'
                    ],
                    'form' => 'orders-bulk-form'
                ], true)) . '
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="orders-pagination-info" class="small text-muted fw-bold"></div>
                        <nav id="orders-pagination-nav"></nav>
                    </div>
                </div>'
        ]
    ]
];

$ui = new UiBuilder($schema);
echo $ui->render();
?>
<form id="formCancelOrder" action="<?php echo $mod_url; ?>&sel=orders" method="POST" style="display:none;">
    <input type="hidden" name="token" value="<?php echo TOKEN; ?>">
    <input type="hidden" name="order_id" id="cancelOrderId" value="">
    <input type="hidden" name="notes" id="cancelOrderNotes" value="">
    <input type="hidden" name="origin" value="orders">
    <input type="hidden" name="cancel_order" value="1">
</form>

<script>
    $(document).ready(function () {
        loadOrders();
    });

    function loadOrders(offset = 0) {
        const container = $('#orders-desktop-container');
        const filterData = $('#orders-filter-form').serialize();
        const ajaxUrl = '<?= Url::ajax("nixomers", "list_orders") ?>&offset=' + offset + '&' + filterData;

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
                renderOrdersTable(response.headers, response.data);
                renderOrdersPagination(response.total, response.limit, response.offset);
            } else {
                container.html(`<div class="alert alert-danger m-3">${response.message || 'Error loading orders'}</div>`);
            }
        }).fail(function() {
            container.html('<div class="alert alert-danger m-3">Failed to connect to server.</div>');
        });
    }

    function renderOrdersTable(headers, data) {
        const container = $('#orders-desktop-container');
        if (data.length === 0) {
            container.html('<div class="p-5 text-center text-muted">No orders found.</div>');
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
    }

    function renderOrdersPagination(total, limit, offset) {
        const info = $('#orders-pagination-info');
        const nav = $('#orders-pagination-nav');
        
        const start = offset + 1;
        const end = Math.min(offset + limit, total);
        info.html(`Showing ${start} to ${end} of ${total} entries`);

        let paginationHtml = '<ul class="pagination pagination-sm mb-0 gap-1">';
        const totalPages = Math.ceil(total / limit);
        const currentPage = Math.floor(offset / limit) + 1;

        // Previous
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadOrders(${(currentPage - 2) * limit}); return false;"><i class="bi bi-chevron-left"></i></a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadOrders(${(i - 1) * limit}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadOrders(${currentPage * limit}); return false;"><i class="bi bi-chevron-right"></i></a>
            </li>
        `;

        paginationHtml += '</ul>';
        nav.html(paginationHtml);
    }

    function toggleCheckboxes(source) {
        let checkboxes = document.getElementsByName("order_id[]");
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function cancelOrder(id) {
        if (confirm('Are you sure you want to cancel order ' + id + '? This will replenish stock.')) {
            document.getElementById('cancelOrderId').value = id;
            document.getElementById('formCancelOrder').submit();
        }
    }
</script>
