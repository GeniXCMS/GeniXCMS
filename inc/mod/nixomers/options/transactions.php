<?php
/**
 * Nixomers Transactions Ledger View (AJAX Based)
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$q = Typo::cleanX($_GET['q'] ?? '');
$typeFilter = Typo::cleanX($_GET['type'] ?? 'all');
$startDate = Typo::cleanX($_GET['start_date'] ?? '');
$endDate = Typo::cleanX($_GET['end_date'] ?? '');

$currency = Options::v('nixomers_currency') ?: 'IDR';

// Calculate Dynamic Stats
$totalIncome = Query::table('nix_transactions')->where('type', 'income')->sum('amount');
$totalFees = Query::table('nix_transactions')->sum('fee');
$totalTax = Query::table('nix_transactions')->sum('tax');
$totalNet = Query::table('nix_transactions')->where('type', 'income')->sum('net');
$totalExpense = Query::table('nix_transactions')->where('type', 'expense')->sum('amount');
$balance = $totalNet - $totalExpense;

$schema = [
    'header' => [
        'title' => 'Financial Transactions',
        'subtitle' => 'Detailed ledger of all income and expenses for your commerce activity.',
        'icon' => 'bi bi-bank',
        'buttons' => [
            [
                'type' => 'link',
                'href' => 'index.php?page=mods&mod=nixomers&sel=analytics',
                'label' => 'View Analytics',
                'icon' => 'bi bi-graph-up',
                'class' => 'btn btn-light rounded-pill px-4 border shadow-none fw-bold'
            ],
            [
                'type' => 'button',
                'label' => 'Export Data',
                'icon' => 'bi bi-file-earmark-excel',
                'class' => 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm'
            ]
        ]
    ],
    'content' => [
        [
            'type' => 'stat_cards',
            'size' => 'small',
            'items' => [
                ['label' => 'Gross Revenue', 'value' => $currency . ' ' . number_format((float) ($totalIncome ?? 0), 0), 'icon' => 'bi bi-graph-up-arrow', 'color' => 'dark'],
                ['label' => 'Merchant Fees', 'value' => $currency . ' ' . number_format((float) ($totalFees ?? 0), 0), 'icon' => 'bi bi-percent', 'color' => 'danger'],
                ['label' => 'Total Tax', 'value' => $currency . ' ' . number_format((float) ($totalTax ?? 0), 0), 'icon' => 'bi bi-briefcase', 'color' => 'warning'],
                ['label' => 'Net Income', 'value' => $currency . ' ' . number_format((float) ($totalNet ?? 0), 0), 'icon' => 'bi bi-piggy-bank', 'color' => 'success'],
                ['label' => 'Net Balance', 'value' => $currency . ' ' . number_format((float) ($balance ?? 0), 0), 'icon' => 'bi bi-wallet2', 'color' => 'primary']
            ]
        ],
        [
            'type' => 'card',
            'title' => 'Transaction History',
            'icon' => 'bi bi-book',
            'no_padding' => true,
            'footer_no_padding' => true,
            'footer_class' => 'card-footer bg-transparent border-0 p-0',
            'header_action' => '
                <form id="trans-filter-form" class="d-flex flex-wrap flex-xl-nowrap gap-2 align-items-center justify-content-end" onsubmit="loadTransactions(); return false;">
                    <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 bg-white" placeholder="Search..." style="width:140px;" value="' . htmlspecialchars($q) . '">
                    </div>

                    <div class="d-flex gap-1 align-items-center bg-white border rounded-pill px-2 shadow-sm">
                        <i class="bi bi-calendar-event text-muted ms-1" style="font-size:0.75rem;"></i>
                        <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . $startDate . '">
                        <span class="text-muted small">-</span>
                        <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-transparent p-1" style="font-size:0.75rem; width:110px;" value="' . $endDate . '">
                    </div>

                    <select name="type" class="form-select form-select-sm rounded-pill px-3 shadow-none border bg-white shadow-sm" style="width:130px;">
                        <option value="all" ' . ($typeFilter == 'all' ? 'selected' : '') . '>All Types</option>
                        <option value="income" ' . ($typeFilter == 'income' ? 'selected' : '') . '>Income Only</option>
                        <option value="expense" ' . ($typeFilter == 'expense' ? 'selected' : '') . '>Expense Only</option>
                    </select>
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="loadTransactions()"><i class="bi bi-arrow-clockwise me-1"></i> Refresh</button>
                </form>',
            'body_elements' => [
                [
                    'type' => 'raw',
                    'html' => '
                        <div id="trans-desktop-container" class="p-0">
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
                    <div class="small text-muted fw-bold" id="trans-pagination-info"></div>
                    <nav id="trans-pagination-nav"></nav>
                </div>'
        ]
    ]
];

$ui = new UiBuilder($schema);
echo $ui->render();
?>

<script>
    $(document).ready(function () {
        loadTransactions();
    });

    function loadTransactions(offset = 0) {
        const container = $('#trans-desktop-container');
        const filterData = $('#trans-filter-form').serialize();
        const ajaxUrl = '<?= Url::ajax("nixomers", "list_transactions") ?>&offset=' + offset + '&' + filterData;

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
                renderTransTable(response.headers, response.data);
                renderTransPagination(response.total, response.limit, response.offset);
            } else {
                container.html(`<div class="alert alert-danger m-3">${response.message || 'Error loading transactions'}</div>`);
            }
        }).fail(function() {
            container.html('<div class="alert alert-danger m-3">Failed to connect to server.</div>');
        });
    }

    function renderTransTable(headers, data) {
        const container = $('#trans-desktop-container');
        if (data.length === 0) {
            container.html('<div class="p-5 text-center text-muted">No transactions found.</div>');
            return;
        }

        let html = `
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
        `;

        headers.forEach(h => {
            const hContent = typeof h === 'object' ? h.content : h;
            const hClass = typeof h === 'object' ? (h.class || '') : '';
            const hWidth = typeof h === 'object' ? (h.width || '') : '';
            html += `<th class="${hClass}" ${hWidth ? 'width="'+hWidth+'"' : ''}>${hContent}</th>`;
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

    function renderTransPagination(total, limit, offset) {
        const info = $('#trans-pagination-info');
        const nav = $('#trans-pagination-nav');
        
        const start = offset + 1;
        const end = Math.min(offset + limit, total);
        info.html(`Showing ${start} to ${end} of ${total} entries`);

        let paginationHtml = '<ul class="pagination pagination-sm mb-0 gap-1">';
        const totalPages = Math.ceil(total / limit);
        const currentPage = Math.floor(offset / limit) + 1;

        // Previous
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadTransactions(${(currentPage - 2) * limit}); return false;"><i class="bi bi-chevron-left"></i></a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadTransactions(${(i - 1) * limit}); return false;">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="loadTransactions(${currentPage * limit}); return false;"><i class="bi bi-chevron-right"></i></a>
            </li>
        `;

        paginationHtml += '</ul>';
        nav.html(paginationHtml);
    }
</script>
