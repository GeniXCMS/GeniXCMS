<?php
/**
 * Nixomers Transaction Detail View
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$id = Typo::int($_GET['id'] ?? 0);
$transaction = Query::table('nix_transactions')->where('id', $id)->first();

if (!$transaction) {
    echo '<div class="alert alert-danger">Transaction not found.</div>';
    return;
}

$order = Query::table('nix_orders')->where('order_id', $transaction->order_id)->first();
$currency = Options::v('nixomers_currency') ?: 'IDR';

// Status Color Mapping
$statusColor = match($transaction->status) {
    'completed', 'paid' => 'success',
    'refunded'          => 'warning',
    'cancelled', 'failed' => 'danger',
    default             => 'secondary'
};

$typeColor = ($transaction->type == 'income') ? 'success' : 'danger';
$amountPrefix = ($transaction->type == 'income') ? '+' : '-';

$header = [
    'title' => 'Transaction Details: #TX' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT),
    'icon' => 'bi bi-bank',
    'buttons' => [
        [
            'label' => strtoupper($transaction->status ?: 'pending'),
            'class' => 'badge bg-' . $statusColor . ' px-4 py-2 rounded-pill fw-black fs-6 text-uppercase text-white shadow-sm',
            'url' => 'javascript:void(0)',
            'attr' => 'style="text-decoration: none; cursor: default; vertical-align: middle;"'
        ]
    ],
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => 'index.php'],
        ['label' => 'Nixomers', 'url' => 'index.php?page=mods&mod=nixomers'],
        ['label' => 'Transactions', 'url' => 'index.php?page=mods&mod=nixomers&sel=transactions'],
        ['label' => 'TX#' . $transaction->id, 'active' => true]
    ]
];

$schema = [
    'header' => $header,
    'content' => [
        [
            'type' => 'grid',
            'content' => [
                [
                    'type' => 'col',
                    'class' => 'col-lg-7',
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => 'Financial Breakdown',
                            'icon' => 'bi bi-calculator',
                            'header_action' => [
                                [
                                    'type' => 'html',
                                    'html' => '<a href="' . $mod_url . '&sel=transactiondetail&id=' . $transaction->id . '&act=recalculate_payment&oid=' . $transaction->order_id . '&token=' . TOKEN . '" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3">
                                        <i class="bi bi-arrow-repeat me-1"></i> Recalculate
                                    </a>'
                                ]
                            ],
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="p-4 bg-light rounded-5 border mb-4">
                                        <div class="row align-items-center">
                                            <div class="col-8">
                                                <div class="small text-muted text-uppercase fw-bold mb-1">Gross Amount (' . $transaction->type . ')</div>
                                                <h1 class="fw-black text-' . $typeColor . ' m-0">' . $amountPrefix . ' ' . $currency . ' ' . Nixomers::formatCurrency($transaction->amount ?? 0) . '</h1>
                                            </div>
                                            <div class="col-4 text-end">
                                                <div class="small text-muted text-uppercase fw-bold mb-1">Payment Method</div>
                                                <div class="badge bg-white text-dark border px-3 py-2 rounded-pill fw-bold">' . strtoupper($transaction->method ?: 'MANUAL') . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless align-middle mb-0">
                                            <tr class="border-bottom">
                                                <td class="py-3 text-muted">Description</td>
                                                <td class="py-3 text-end fw-bold">' . $transaction->description . '</td>
                                            </tr>
                                            <tr class="border-bottom text-danger">
                                                <td class="py-3">Gateway / Merchant Fee</td>
                                                <td class="py-3 text-end">- ' . $currency . ' ' . Nixomers::formatCurrency($transaction->fee ?? 0) . '</td>
                                            </tr>
                                            <tr class="border-bottom text-warning">
                                                <td class="py-3">Tax / Charges</td>
                                                <td class="py-3 text-end">- ' . $currency . ' ' . Nixomers::formatCurrency($transaction->tax ?? 0) . '</td>
                                            </tr>
                                            <tr class="border-bottom text-muted">
                                                <td class="py-3 small">Shipping Cost (Allocated)</td>
                                                <td class="py-3 text-end">- ' . $currency . ' ' . Nixomers::formatCurrency($transaction->shipping_cost ?? 0) . '</td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td class="py-4 h5 fw-bold text-dark">Net Settlement Amount</td>
                                                <td class="py-4 text-end h5 fw-black text-primary">' . $currency . ' ' . Nixomers::formatCurrency($transaction->net ?? 0) . '</td>
                                            </tr>
                                        </table>
                                    </div>'
                                ]
                            ]
                        ],
                        [
                            'type' => 'card',
                            'title' => 'Related Information',
                            'icon' => 'bi bi-link-45deg',
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="row g-4">
                                        <div class="col-md-6 border-end">
                                            <div class="small text-muted text-uppercase fw-bold mb-2">Order Reference</div>
                                            ' . ($order ? '
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                                                    <i class="bi bi-cart-check fs-3 text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold fs-5">' . ($order->order_id ?: 'N/A') . '</div>
                                                    <a href="' . $mod_url . '&sel=orderdetail&id=' . ($order->order_id ?? '') . '" class="small text-decoration-none">View Order Details <i class="bi bi-arrow-right"></i></a>
                                                </div>
                                            </div>' : '<div class="alert alert-secondary py-2 small mb-0">No direct order associated.</div>') . '
                                        </div>
                                        <div class="col-md-6">
                                            <div class="small text-muted text-uppercase fw-bold mb-2">Transaction Metadata</div>
                                            <div class="bg-light p-3 rounded-4">
                                                <div class="mb-2"><span class="small text-muted me-2">Gateway ID:</span> <code class="small">' . ($transaction->trans_id ?: '-') . '</code></div>
                                                <div class="mb-0"><span class="small text-muted me-2">Record Date:</span> <span class="small fw-bold">' . date('d F Y, H:i:s', strtotime($transaction->date)) . '</span></div>
                                            </div>
                                        </div>
                                    </div>'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'col',
                    'class' => 'col-lg-5',
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => 'Transaction & Payment Log',
                            'icon' => 'bi bi-journal-text',
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => (function() use ($transaction) {
                                        $logs = Query::table('nix_order_logs')
                                            ->where('order_id', $transaction->order_id)
                                            ->where('new_status', 'LIKE', 'PAYMENT:%')
                                            ->orderBy('id', 'DESC')
                                            ->get();

                                        if (empty($logs)) {
                                            return '
                                            <div class="text-center py-5">
                                                <i class="bi bi-clock-history fs-1 text-muted opacity-25 d-block mb-3"></i>
                                                <div class="text-muted small">No payment lifecycle logs found for this transition.</div>
                                            </div>';
                                        }

                                        $html = '<div class="timeline-small mt-2">';
                                        foreach ($logs as $l) {
                                            $html .= '
                                            <div class="d-flex gap-3 mb-4 position-relative">
                                                <div class="flex-shrink-0 text-muted small" style="width: 70px;">' . date('H:i', strtotime($l->date)) . '<br><span style="font-size: 9px;">' . date('d M', strtotime($l->date)) . '</span></div>
                                                <div class="bg-primary bg-opacity-10 rounded-circle flex-shrink-0" style="width: 12px; height: 12px; margin-top: 5px; z-index: 2;"></div>
                                                <div>
                                                    <div class="fw-bold small">' . str_replace('PAYMENT: ', '', $l->new_status ?? '') . '</div>
                                                    <div class="extra-small text-muted">Updated by ' . $l->updated_by . '</div>
                                                </div>
                                            </div>';
                                        }
                                        $html .= '</div>';
                                        return $html;
                                    })()
                                ]
                            ]
                        ],
                        [
                            'type' => 'card',
                            'title' => 'Record Management',
                            'icon' => 'bi bi-shield-lock',
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="d-grid gap-2">
                                        <a href="' . Url::ajax("nixomers", "print_payment_proof", ["id" => $transaction->id]) . '" target="_blank" class="btn btn-outline-dark rounded-pill py-2 shadow-none">
                                            <i class="bi bi-printer me-2"></i> Print Proof of Payment
                                        </a>
                                        <button class="btn btn-outline-danger rounded-pill py-2 shadow-none" disabled>
                                            <i class="bi bi-trash-fill me-2"></i> Delete Financial Record
                                        </button>
                                        <div class="small text-muted text-center mt-2 italic" style="font-size:10px">Financial records are locked for audit purposes.</div>
                                    </div>'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$ui = new UiBuilder($schema);
echo $ui->render();
