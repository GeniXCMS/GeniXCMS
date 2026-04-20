<?php
/**
 * Nix Confirmation - Admin Options Panel
 * Handles viewing & approving manual payment confirmations.
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$mod_url = 'index.php?page=mods&mod=nix_confirmation';

// Handle Actions
if (isset($_GET['act']) && isset($_GET['id'])) {
    $confirmId = Typo::int($_GET['id']);
    $confirm = Query::table('nix_confirmations')->where('id', $confirmId)->first();

    if ($confirm) {
        if ($_GET['act'] === 'approve') {
            Query::table('nix_confirmations')->where('id', $confirmId)->update(['status' => 'approved']);
            Query::table('nix_orders')->where('order_id', $confirm->order_id)->update(['status' => 'waiting']);
            
            // Check if transaction already exists for this order
            $trans = Query::table('nix_transactions')->where('order_id', $confirm->order_id)->first();
            if ($trans) {
                Query::table('nix_transactions')->where('id', $trans->id)->update([
                    'status' => 'completed',
                    'method' => 'bank_transfer',
                    'amount' => $confirm->amount,
                    'type'   => 'income'
                ]);
            } else {
                Query::table('nix_transactions')->insert([
                    'order_id'    => $confirm->order_id,
                    'amount'      => $confirm->amount,
                    'type'        => 'income', // Crucial for ledger calculation
                    'description' => "Payment for Order #{$confirm->order_id}",
                    'method'      => 'bank_transfer',
                    'status'      => 'completed',
                    'date'        => date('Y-m-d H:i:s')
                ]);
            }
            Nixomers::calculateNetTrans($confirm->order_id);
            $GLOBALS['alertSuccess'] = "Confirmation approved and Order updated!";

        } elseif ($_GET['act'] === 'reject') {
            Query::table('nix_confirmations')->where('id', $confirmId)->update(['status' => 'rejected']);
            $GLOBALS['alertWarning'] = "Confirmation rejected.";

        } elseif ($_GET['act'] === 'delete') {
            Query::table('nix_confirmations')->where('id', $confirmId)->delete();
            $GLOBALS['alertSuccess'] = "Confirmation deleted.";
        }
    }
}

$schema = [
    'header' => [
        'title'    => 'Payment Confirmations',
        'version'  => '1.0.0',
        'icon'     => 'bi bi-check2-circle',
        'subtitle' => 'Verify and approve manual payment submissions from customers.'
    ],
    'content' => [
        [
            'type'         => 'card',
            'title'        => 'Confirmation Submissions',
            'icon'         => 'bi bi-check2-all',
            'no_padding'   => true,
            'footer_no_padding' => true,
            'footer_class' => 'card-footer bg-transparent border-0 p-0',
            'header_action' => '
                <form id="confirms-filter-form" class="d-flex gap-2 flex-wrap justify-content-end align-items-center" onsubmit="loadConfirms(); return false;">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="' . _("Search...") . '" style="width:140px;">
                    </div>
                    <select name="status" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:110px;">
                        <option value="">' . _("All Status") . '</option>
                        <option value="pending">' . _("Pending") . '</option>
                        <option value="approved">' . _("Approved") . '</option>
                        <option value="rejected">' . _("Rejected") . '</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> ' . _("Filter") . '</button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="loadConfirms()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
                </form>',
            'body_elements' => [
                [
                    'type' => 'raw',
                    'html' => '
                        <div id="confirms-desktop-container" class="p-0">
                            <div class="d-flex justify-content-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    '
                ]
            ],
            'footer' => '
                <div class="d-flex justify-content-between align-items-center w-100 p-3">
                    <div></div>
                    <div class="d-flex align-items-center gap-3">
                        <div id="confirms-pagination-info" class="small text-muted fw-bold"></div>
                        <nav id="confirms-pagination-nav"></nav>
                    </div>
                </div>'
        ]
    ]
];

$ui = new UiBuilder($schema);
$ui->render();
?>

<style>
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
        loadConfirms();
    });

    function loadConfirms(offset = 0) {
        const container = $('#confirms-desktop-container');
        const filterData = $('#confirms-filter-form').serialize();
        const ajaxUrl = '<?= Url::ajax("NixConfirmation", "list") ?>&offset=' + offset + '&' + filterData;

        container.html(`
            <div class="d-flex justify-content-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        $.getJSON(ajaxUrl, function(response) {
            if (response.status === 'success') {
                renderConfirmsTable(response.data);
                renderConfirmsPagination(response.total, response.limit, response.offset);
            } else {
                container.html(`<div class="alert alert-danger m-3">${response.message || 'Error loading records'}</div>`);
            }
        }).fail(function() {
            container.html('<div class="alert alert-danger m-3">Failed to connect to server.</div>');
        });
    }

    function renderConfirmsTable(data) {
        const container = $('#confirms-desktop-container');
        if (data.length === 0) {
            container.html('<div class="p-5 text-center text-muted">No records found.</div>');
            return;
        }

        let html = `
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Order Reference</th>
                        <th>Customer / Bank</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Proof</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3"></th>
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

    function renderConfirmsPagination(total, limit, offset) {
        const nav = $('#confirms-pagination-nav');
        const info = $('#confirms-pagination-info');
        
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
                    <a class="page-link" href="#" onclick="loadConfirms(${offset - limit}); return false;"><i class="bi bi-chevron-left"></i></a>
                 </li>`;

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="loadConfirms(${(i - 1) * limit}); return false;">${i}</a>
                         </li>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadConfirms(${offset + limit}); return false;"><i class="bi bi-chevron-right"></i></a>
                 </li>`;

        html += '</ul>';
        nav.html(html);
    }
</script>
