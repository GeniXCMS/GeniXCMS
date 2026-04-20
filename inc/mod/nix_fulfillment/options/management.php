<?php
/**
 * Nixomers Fulfillment Management View
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$paging = Typo::int($_GET['paging'] ?? 1);
$q = Typo::cleanX($_GET['q'] ?? '');
$statusFilter = Typo::cleanX($_GET['status'] ?? 'ready_to_ship'); // Only show ready for fulfillment
$cityFilter = Typo::cleanX($_GET['city'] ?? '');
$courierFilter = Typo::cleanX($_GET['courier'] ?? 'all');
$sort = Typo::cleanX($_GET['sort'] ?? 'newest');

$limit = 15;
$offset = ($paging - 1) * $limit;

// Calculate Dashboard Stats for Fulfillment
$statReady = Query::table('nix_orders')->where('status', 'ready_to_ship')->count();
$statShipped = Query::table('nix_orders')->where('status', 'shipped')->count();
$statDelivered = Query::table('nix_orders')->where('status', 'delivered')->count();

$currency = Options::v('nixomers_currency') ?: 'IDR';

$schema = [
    'header' => [
        'title' => 'Fulfillment Control',
        'subtitle' => 'Manage warehouse processing, staging, and courier handovers.',
        'icon' => 'bi bi-truck',
        'buttons' => [
            [
                'type' => 'link',
                'href' => 'index.php?page=mods&mod=nixomers&sel=analytics',
                'label' => 'Logistics History',
                'icon' => 'bi bi-clock-history',
                'class' => 'btn btn-light rounded-pill px-4 border shadow-none fw-bold'
            ],
            [
                'type' => 'button',
                'label' => 'Bulk Print Labels',
                'icon' => 'bi bi-printer',
                'class' => 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm',
                'attr' => 'onclick="bulkPrintLabels()"'
            ]
        ]
    ],
    'content' => [
        [
            'type' => 'stat_cards',
            'size' => 'small',
            'items' => [
                ['label' => 'Awaiting Shipment', 'value' => (string)$statReady, 'icon' => 'bi bi-box-seam', 'color' => 'warning'],
                ['label' => 'In Transit', 'value' => (string)$statShipped, 'icon' => 'bi bi-truck', 'color' => 'primary'],
                ['label' => 'Total Delivered', 'value' => (string)$statDelivered, 'icon' => 'bi bi-check-all', 'color' => 'success']
            ]
        ],
        [
            'type' => 'card',
            'title' => 'Processing Queue',
            'icon' => 'bi bi-truck',
            'no_padding' => true,
            'header_action' => '
                <form id="fulfillment-filter-form" class="d-flex flex-wrap flex-xl-nowrap gap-2 align-items-center justify-content-end" onsubmit="loadFulfillment(); return false;">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="Search Order/Resi..." style="width:140px;" value="' . htmlspecialchars($q) . '">
                    </div>

                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-geo-alt text-muted"></i></span>
                        <input type="text" name="city" class="form-control border-0 ps-1 bg-white" placeholder="Sort City..." style="width:110px;" value="' . htmlspecialchars($cityFilter) . '">
                    </div>

                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:140px;" onchange="loadFulfillment();">
                        <option value="all" ' . ($statusFilter == 'all' ? 'selected' : '') . '>All Delivery</option>
                        <option value="waiting" ' . ($statusFilter == 'waiting' ? 'selected' : '') . '>Waiting</option>
                        <option value="processing" ' . ($statusFilter == 'processing' ? 'selected' : '') . '>Packing</option>
                        <option value="ready_to_ship" ' . ($statusFilter == 'ready_to_ship' ? 'selected' : '') . '>Ready to Ship</option>
                        <option value="shipped" ' . ($statusFilter == 'shipped' ? 'selected' : '') . '>Shipped</option>
                        <option value="delivered" ' . ($statusFilter == 'delivered' ? 'selected' : '') . '>Delivered</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel me-1"></i> Filter</button>
                </form>',
            'body_elements' => [
                [
                    'type' => 'raw',
                    'html' => '
                        <form action="' . $mod_url . '&sel=management" method="post" id="fulfillment-bulk-form">
                            <input type="hidden" name="token" value="' . TOKEN . '">
                            <input type="hidden" name="fulfillment_bulk_process" value="1">
                            <div id="fulfillment-table-container">
                                <div class="d-flex justify-content-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    '
                ]
            ],
            'footer' => '
                <div class="d-flex justify-content-between align-items-center w-100 p-3">
                    <div class="bulk-action-wrapper">
                        ' . ((new UiBuilder())->renderElement([
                            'type' => 'bulk_actions',
                            'form' => 'fulfillment-bulk-form',
                            'options' => [
                                'processing' => 'Start Packing',
                                'ready_to_ship' => 'Mark as Ready to Ship',
                                'shipped' => 'Mark as Shipped (Input Resi)',
                                'delivered' => 'Mark as Delivered'
                            ]
                        ], true)) . '
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="fulfillment-pagination-info" class="small text-muted fw-bold"></div>
                        <nav id="fulfillment-pagination-nav"></nav>
                    </div>
                </div>'
        ]
    ]
];

$ui = new UiBuilder($schema);
echo $ui->render();

// Generic Update Modal
?>
<div class="modal fade" id="modalGenericFulfillment" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= Url::current() ?>" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="token" value="<?= TOKEN ?>">
            <input type="hidden" name="id" id="modal-order-id-hidden">
            <div class="modal-header">
                <h5 class="modal-title fw-black">Update Fulfillment: <span id="modal-order-id-label"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Package Status</label>
                    <select name="fulfillment_status" id="modal-fstatus" class="form-select border-2">
                        <option value="ready_to_ship">Validated & Ready</option>
                        <option value="packing">📦 Sedang Dikemas (Packing)</option>
                        <option value="packed">✅ Sudah Dikemas</option>
                        <option value="ready">🚀 Siap Kirim</option>
                        <option value="shipping_to_courier">🚚 Sedang Dikirim ke Kurir</option>
                        <option value="shipping_to_customer">👤 Sedang Dikirim ke Pelanggan (SHIPPED)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Staging Location / Posisi Rak</label>
                    <input type="text" name="staging_location" id="modal-location" class="form-control border-2" placeholder="e.g. Rak A1">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Processing Photo / Bukti</label>
                    <input type="file" name="fulfillment_image" class="form-control border-2">
                    <div id="modal-current-image" class="mt-2"></div>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold">Fulfillment Notes</label>
                    <textarea name="fulfillment_notes" id="modal-notes" class="form-control border-2" rows="2" placeholder="Informasi tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_fulfillment_status" class="btn btn-primary rounded-pill px-4 fw-bold">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        loadFulfillment();
    });

    function loadFulfillment(offset = 0) {
        const container = $('#fulfillment-table-container');
        const filterData = $('#fulfillment-filter-form').serialize();
        const ajaxUrl = '<?= Url::ajax("nixFulfillment", "list_fulfillment") ?>&offset=' + offset + '&token=<?= TOKEN ?>&' + filterData;

        container.html(`
            <div class="d-flex justify-content-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.getJSON(ajaxUrl, function(res) {
            if (res.status === 'success') {
                renderFulfillmentTable(res.headers, res.data);
                renderFulfillmentPagination(res.total, res.limit, res.offset);
            } else {
                container.html(`<div class="alert alert-danger m-3">${res.message || 'Error loading records'}</div>`);
            }
        }).fail(function() {
            container.html('<div class="alert alert-danger m-3">Failed to connect to server.</div>');
        });
    }

    function renderFulfillmentTable(headers, data) {
        const container = $('#fulfillment-table-container');
        if (data.length === 0) {
            container.html('<div class="p-5 text-center text-muted">No orders ready for fulfillment found.</div>');
            return;
        }

        let html = `<table class="table table-hover align-middle mb-0"><thead class="bg-light"><tr>`;
        headers.forEach(h => {
            const content = typeof h === 'object' ? h.content : h;
            const className = typeof h === 'object' ? h.class : '';
            html += `<th class="${className}">${content}</th>`;
        });
        html += `</tr></thead><tbody>`;

        data.forEach(row => {
            html += `<tr>`;
            row.forEach(cell => {
                html += `<td class="${cell.class || ''}">${cell.content}</td>`;
            });
            html += `</tr>`;
        });
        html += `</tbody></table>`;

        container.html(html);

        // Bind Checkbox logic
        $('#selectall').click(function() {
            $('.check').prop('checked', this.checked);
        });
        $('.check').click(function() {
            if (!this.checked) $('#selectall').prop('checked', false);
            if ($('.check:checked').length == $('.check').length && $('.check').length > 0) {
                $('#selectall').prop('checked', true);
            }
        });

        // Add handler for dynamic edit buttons to populate generic modal
        // Note: In a real app we might fetch the specific order data from another endpoint
        // but for now we'll rely on what's in the rows if possible, or just open the modal.
        // For the sake of this demo, I'll add a helper to fetch single order data if needed.
    }

    function renderFulfillmentPagination(total, limit, offset) {
        const info = $('#fulfillment-pagination-info');
        const nav = $('#fulfillment-pagination-nav');
        
        const start = offset + 1;
        const end = Math.min(offset + limit, total);
        info.html(`Showing ${start} to ${end} of ${total} entries`);

        let paginationHtml = '<ul class="pagination pagination-sm mb-0 gap-1">';
        const totalPages = Math.ceil(total / limit);
        const currentPage = Math.floor(offset / limit) + 1;

        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadFulfillment(${(currentPage - 2) * limit}); return false;"><i class="bi bi-chevron-left"></i></a>
            </li>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadFulfillment(${(i - 1) * limit}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadFulfillment(${currentPage * limit}); return false;"><i class="bi bi-chevron-right"></i></a>
            </li>
        `;

        paginationHtml += '</ul>';
        nav.html(paginationHtml);
    }

    function toggleCheckboxes(source) {
        $('.check').prop('checked', source.checked);
    }

    function populateFulfillmentModal(btn) {
        const id = $(btn).data('id');
        const orderId = $(btn).data('orderid');
        const status = $(btn).data('status');
        const location = $(btn).data('location');
        const notes = $(btn).data('notes');

        $('#modal-order-id-hidden').val(id);
        $('#modal-order-id-label').text(orderId);
        $('#modal-fstatus').val(status);
        $('#modal-location').val(location);
        $('#modal-notes').val(notes);
        
        // Clear file input
        $('input[name="fulfillment_image"]').val('');
    }
    
    function bulkPrintLabels() {
        const selected = $('.check:checked').map(function() { return $(this).val(); }).get();
        if (selected.length === 0) {
            alert('Please select at least one order.');
            return;
        }
        // Logic for bulk printing ...
        window.open('<?= Url::ajax("nixomers", "print_label") ?>&bulk=1&ids=' + selected.join(','), '_blank');
    }
</script>
<style>
    #fulfillment-pagination-nav .page-link {
        border-radius: 50% !important;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        background: #f8f9fa;
        color: #6c757d;
        font-weight: bold;
        font-size: 0.75rem;
    }
    #fulfillment-pagination-nav .page-item.active .page-link {
        background: var(--gx-primary);
        color: #fff;
    }
</style>
