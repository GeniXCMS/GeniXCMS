<?php
/**
 * Nixomers Order Detail View
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$orderId = Typo::cleanX($_GET['id'] ?? '');
$order = Query::table('nix_orders')->where('order_id', $orderId)->first();
$transaction = Query::table('nix_transactions')->where('order_id', $orderId)->first();

if (!$order) {
    echo '<div class="alert alert-danger">Order not found.</div>';
    return;
}

// Fetch Granular Items
$orderItems = Query::table('nix_order_items')->where('order_id', $orderId)->get();
$hasGranular = !empty($orderItems);

// ── 3. AUTO-COMPLETE LOGIC ──────────────────────────────────────────
// Jika status shipped dan sudah lewat estimasi + 3 hari
if ($order->status == 'shipped' && !empty($order->shipped_at)) {
    $etdRaw = $order->shipping_etd ?: '3'; // Default 3 days if empty
    preg_match_all('/\d+/', $etdRaw, $matches);
    $maxEtd = (int) (!empty($matches[0]) ? max($matches[0]) : 3);
    $gracePeriod = 3; // Tambahan 3 hari sesuai permintaan
    $shippedTime = strtotime($order->shipped_at);
    $autoCompleteTime = $shippedTime + (($maxEtd + $gracePeriod) * 86400);

    if (time() > $autoCompleteTime) {
        Query::table('nix_orders')->where('order_id', $orderId)->update(['status' => 'completed']);
        // Refresh order data
        $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
    }
}

// Check if all granular items are ready for shipping
$allReady = true;
if ($hasGranular) {
    foreach ($orderItems as $item) {
        if ($item->status !== 'ready') {
            $allReady = false;
            break;
        }
    }
} else {
    $allReady = false; // Cannot ship if no items tracked yet (for safety)
}

$items = json_decode($order->cart_items ?? '[]', true) ?: [];
$currency = Options::v('nixomers_currency') ?: 'IDR';

// Status Color Mapping
$statusColor = match ($order->status) {
    'paid', 'completed', 'delivered' => 'success',
    'cancelled', 'expired'           => 'danger',
    'shipped', 'onprocess'           => 'primary',
    'waiting'                        => 'info',
    default                          => 'warning'
};

$itemStatusColors = [
    'pending' => 'secondary',
    'checking' => 'info',
    'functional' => 'primary',
    'packed' => 'dark',
    'ready' => 'success'
];

// Stamp CSS for Cancelled Status
$stampCss = '
<style>
    .stamp-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-15deg);
        border: 12px solid rgba(220, 53, 69, 0.25);
        color: rgba(220, 53, 69, 0.25);
        padding: 15px 50px;
        font-size: 6rem;
        font-weight: 900;
        text-transform: uppercase;
        z-index: 100;
        pointer-events: none;
        border-radius: 25px;
        letter-spacing: 15px;
        user-select: none;
        white-space: nowrap;
        font-family: sans-serif;
    }
    .card-relative { position: relative; overflow: hidden; }
</style>';
echo $stampCss;

$isPaid = (($transaction->status ?? 'pending') == 'paid' || ($transaction->status ?? 'pending') == 'completed');
$isProcess = ($order->status == 'onprocess' || $order->status == 'process');
$stampHtml = ($order->status == 'cancelled' ? '<div class="stamp-overlay">CANCELED</div>' : '');

if ($hasGranular) {
    $granularHtml = '<div class="list-group list-group-flush">';
    $modalsCollector = '';
    foreach ($orderItems as $item) {
        $pTitle = Posts::title($item->product_id);
        $sBadge = '<span class="badge bg-' . ($itemStatusColors[$item->status] ?? 'secondary') . '">' . strtoupper($item->status ?? '') . '</span>';
        
        $granularHtml .= '
        <div class="list-group-item p-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1 fw-black text-primary">' . $pTitle . '</h6>
                    <div class="small text-muted">ID: #' . $item->id . ' | Product ID: ' . $item->product_id . '</div>
                </div>
                <div>' . $sBadge . '</div>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.65rem">Serial Number</div>
                    <div class="small fw-bold">' . ($item->serial_number ?: '<span class="text-danger italic" style="font-size:0.75rem">No SN</span>') . '</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.65rem">Barcode</div>
                    <div class="small fw-bold">' . ($item->barcode ?: '-') . '</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.65rem">Location</div>
                    <div class="small fw-bold">' . ($item->location ?: '-') . '</div>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-sm btn-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalItem' . $item->id . '">
                        <i class="bi bi-pencil-square me-1"></i> Update Info
                    </button>
                </div>
            </div>
        </div>';

        // Fetch Logs for this item
        $logs = Query::table('nix_order_item_logs')
            ->where('item_id', $item->id)
            ->orderBy('date', 'DESC')
            ->get();
            
        $logsHtml = '<div class="mt-4 border-top pt-3"><h6 class="fw-black mb-3 small text-uppercase text-secondary">Processing History</h6>';
        if (empty($logs)) {
            $logsHtml .= '<div class="alert alert-light border small text-muted py-2">No history recorded yet.</div>';
        } else {
            $logsHtml .= '<div class="timeline-small">';
            foreach ($logs as $log) {
                $logsHtml .= '
                <div class="mb-3 border-start border-primary border-3 ps-3">
                    <div class="d-flex justify-content-between">
                        <span class="small fw-bold text-dark">'.strtoupper($log->old_status ?? '').' <i class="bi bi-arrow-right mx-1"></i> '.strtoupper($log->new_status ?? '').'</span>
                        <span class="small text-muted" style="font-size:0.7rem">'.date('d/m H:i', strtotime($log->date)).'</span>
                    </div>
                    <div class="small text-muted" style="font-size:0.75rem">By: <strong>'.$log->updated_by.'</strong></div>
                    '.($log->notes ? '<div class="mt-1 p-2 bg-light rounded text-dark" style="font-size:0.75rem">'.$log->notes.'</div>' : '').'
                </div>';
            }
            $logsHtml .= '</div>';
        }
        $logsHtml .= '</div>';

        $modalsCollector .= '
            <!-- Modal Update Item -->
            <div class="modal fade text-start" id="modalItem' . $item->id . '" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form action="' . Url::current() . '" method="POST" class="modal-content border-0 shadow-lg">
                        <input type="hidden" name="token" value="' . TOKEN . '">
                        <input type="hidden" name="item_id" value="' . $item->id . '">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-black">Info & Tracking ' . $pTitle . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Processing Status</label>
                                        <select name="item_status" class="form-select border-2">
                                            <option value="pending" ' . ($item->status == 'pending' ? 'selected' : '') . '>Pending / Waiting</option>
                                            <option value="checking" ' . ($item->status == 'checking' ? 'selected' : '') . '>Checking Physical (QC)</option>
                                            <option value="functional" ' . ($item->status == 'functional' ? 'selected' : '') . '>Functional Test OK</option>
                                            <option value="packed" ' . ($item->status == 'packed' ? 'selected' : '') . '>Packed & Sealed</option>
                                            <option value="ready" ' . ($item->status == 'ready' ? 'selected' : '') . '>Ready to Ship</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Serial Number / Unique ID</label>
                                        <input type="text" name="serial_number" class="form-control" value="' . $item->serial_number . '" placeholder="Enter SN or IMEI">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Barcode (Unit/Product)</label>
                                        <input type="text" name="barcode" class="form-control" value="' . ($item->barcode ?: Posts::getParam('barcode', $item->product_id)) . '" placeholder="Scan or type barcode">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Room / Shelf Location</label>
                                        <input type="text" name="location" class="form-control" value="' . $item->location . '" placeholder="e.g: Room A - Shelf 5">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Current Activity Notes</label>
                                        <textarea name="notes" class="form-control" rows="2" placeholder="Explain current action..."></textarea>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" name="update_order_item" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                            <i class="bi bi-save me-1"></i> Save Changes & Log
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    ' . $logsHtml . '
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>';
    }
    $granularHtml .= '</div>';
    
    $orderItemsCard = [
        'type' => 'card',
        'title' => 'Order Items Tracking (Granular)',
        'icon' => 'bi bi-qr-code-scan',
        'class' => 'card-relative',
        'no_padding' => true,
        'body_elements' => [
            ['type' => 'html', 'html' => $stampHtml . $granularHtml]
        ]
    ];
} else {
    // Layouting the table of items
    $itemRows = [];
    foreach ($items as $id => $qty) {
        $pPrice = (float) Posts::getParam('price', $id) ?: 0;
        $pSubtotal = $pPrice * (int)$qty;
        $itemRows[] = [
            ['content' => Posts::title($id)],
            ['content' => (string)$qty, 'class' => 'text-center'],
            ['content' => $currency . ' ' . Nixomers::formatCurrency($pPrice), 'class' => 'text-end'],
            ['content' => $currency . ' ' . Nixomers::formatCurrency($pSubtotal), 'class' => 'text-end fw-bold']
        ];
    }

    $showSync = (!$hasGranular && $isPaid && $isProcess);

    $orderItemsCard = [
        'type' => 'card',
        'title' => 'Order Items',
        'icon' => 'bi bi-box-seam',
        'class' => 'card-relative',
        'header_action' => $showSync ? [
            [
                'type' => 'html',
                'html' => '
                <form action="' . Url::current() . '" method="POST">
                    <input type="hidden" name="token" value="' . TOKEN . '">
                    <button type="submit" name="sync_granular_items" class="btn btn-sm btn-warning rounded-pill px-3">
                        <i class="bi bi-arrow-repeat me-1"></i> Sync Granular Tracking
                    </button>
                </form>'
            ]
        ] : null,
        'no_padding' => true,
        'body_elements' => [
            ['type' => 'html', 'html' => $stampHtml],
            [
                'type' => 'table',
                'headers' => ['Product Name', ['content' => 'Qty', 'class' => 'text-center'], ['content' => 'Price', 'class' => 'text-end'], ['content' => 'Subtotal', 'class' => 'text-end']],
                'rows' => $itemRows
            ],
            [
                'type' => 'html',
                'html' => '
                <div class="px-4 py-3 bg-light border-top text-end">
                    <div class="mb-1"><span class="text-muted me-2 small">Subtotal:</span> <strong>' . $currency . ' ' . Nixomers::formatCurrency($order->subtotal) . '</strong></div>
                    <div class="mb-1"><span class="text-muted me-2 small">Tax:</span> <strong>' . $currency . ' ' . Nixomers::formatCurrency($order->tax) . '</strong></div>
                    <div class="mb-1"><span class="text-muted me-2 small">Shipping:</span> <strong>' . $currency . ' ' . Nixomers::formatCurrency($order->shipping_cost) . '</strong></div>
                    <div class="mt-2 h4 text-primary fw-black">Total: ' . $currency . ' ' . Nixomers::formatCurrency($order->total) . '</div>
                </div>'
            ]
        ]
    ];
}

$displayStatus = ($order->status === 'waiting') ? 'waiting process' : $order->status;

$headerButtons = [
    [
        'label' => strtoupper($displayStatus),
        'class' => 'badge bg-' . $statusColor . ' px-4 py-2 rounded-pill fw-black fs-6 text-uppercase text-white shadow-sm',
        'url' => 'javascript:void(0)',
        'attr' => 'style="text-decoration: none; cursor: default; vertical-align: middle; ' . ($statusColor == 'info' ? 'color: #000 !important;' : '') . '"'
    ],
    [
        'label' => '<i class="bi bi-file-earmark-pdf me-1"></i> Invoice PDF',
        'type' => 'link',
        'class' => 'btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm',
        'url' => Url::ajax('nixomers', 'print_invoice', ['id' => $order->order_id]),
        'attr' => 'target="_blank"'
    ]
];
$headerButtons = Hooks::filter('nixomers_orderdetail_header_buttons', $headerButtons, $order);

$header = [
    'title' => 'Order Detail: ' . $orderId,
    'subtitle' => 'Placed on ' . date('d F Y, H:i', strtotime($order->date)),
    'icon' => 'bi bi-receipt',
    'buttons' => $headerButtons,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => 'index.php'],
        ['label' => 'Nixomers', 'url' => 'index.php?page=mods&mod=nixomers'],
        ['label' => 'Orders', 'url' => 'index.php?page=mods&mod=nixomers&sel=orders'],
        ['label' => $orderId, 'active' => true]
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
                    'class' => 'col-lg-8',
                    'content' => [
                        $orderItemsCard,
                        [
                            'type' => 'card',
                            'title' => 'Customer Details',
                            'icon' => 'bi bi-person-badge',
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Name</div>
                                            <div class="fw-bold fs-5 text-primary">' . $order->customer_name . '</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Email Address</div>
                                            <div class="fw-bold">' . $order->customer_email . '</div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Phone Number</div>
                                            <div class="fw-bold">' . $order->customer_phone . '</div>
                                        </div>
                                    </div>'
                                ]
                            ]
                        ],
                        [
                            'type' => 'card',
                            'title' => 'Shipping Information',
                            'icon' => 'bi bi-truck',
                            'header_action' => [
                                [
                                    'type' => 'html',
                                    'html' => '<a href="' . Url::ajax('nixomers', 'print_label', ['id' => $order->order_id]) . '" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3"><i class="bi bi-printer me-1"></i> Cetak Label</a>'
                                ]
                            ],
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Courier Service</div>
                                            <div class="fw-bold fs-5">' . strtoupper($order->shipping_courier ?? '') . ' - ' . ($order->shipping_service ?? 'Manual Drop') . '</div>
                                            <div class="small text-secondary">Estimated Delivery: ' . ($order->shipping_etd ?: '-') . '</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Full Shipping Address</div>
                                            <div class="p-3 bg-light border rounded-3 small">
                                                ' . nl2br($order->shipping_address ?? '') . '
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
                    'class' => 'col-lg-4',
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => 'Order Management',
                            'icon' => 'bi bi-gear-wide-connected',
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="mb-4">
                                        <label class="small text-muted text-uppercase fw-bold mb-2">Shipping Receipt (AWB/RESI)</label>
                                        <form action="' . $mod_url . '&sel=orderdetail&id=' . $order->order_id . '" method="POST">
                                            <input type="hidden" name="token" value="' . TOKEN . '">
                                            <div class="input-group">
                                                <input type="text" name="shipping_resi" class="form-control" placeholder="' . (in_array($order->status, ['shipped', 'delivered', 'completed']) ? 'Enter receipt number...' : 'Locked until Shipped') . '" value="' . ($order->shipping_resi ?? '') . '" ' . (!in_array($order->status, ['shipped', 'delivered', 'completed']) ? 'disabled' : '') . '>
                                                <button type="submit" name="save_resi" class="btn btn-primary" ' . (!in_array($order->status, ['shipped', 'delivered', 'completed']) ? 'disabled' : '') . '><i class="bi bi-save me-1"></i> Update</button>
                                            </div>
                                            ' . ($order->shipping_resi ? '<div class="mt-1 small text-success fw-bold"><i class="bi bi-check2-circle"></i> Receipt recorded.</div>' : '') . '
                                        </form>
                                    </div>
                                    <hr>
                                    <label class="small text-muted text-uppercase fw-bold mb-3 d-block">Quick Status Action</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            ' . (($order->status == 'waiting' && $isPaid) ? '
                                            <a href="' . $mod_url . '&sel=orderdetail&id=' . $order->order_id . '&act=setstatus&status=onprocess&token=' . TOKEN . '" class="btn btn-outline-primary w-100 rounded-3 py-2 small fw-bold">
                                                <i class="bi bi-gear me-1"></i> Process
                                            </a>' : '
                                            <button class="btn btn-outline-secondary w-100 rounded-3 py-2 small fw-bold" disabled>
                                                <i class="bi bi-' . ($isPaid ? 'check2-circle' : 'hourglass-split') . ' me-1"></i> ' . ($isPaid ? 'Processed' : 'Waiting Payment') . '
                                            </button>') . '
                                        </div>
                                        <div class="col-6">
                                            ' . ($allReady && $order->status === 'onprocess' ? '
                                            <button class="btn btn-warning w-100 rounded-3 py-2 small fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalReadyShip">
                                                <i class="bi bi-box-seam me-1"></i> Ready to Ship
                                            </button>' : '
                                            <button class="btn btn-outline-secondary w-100 rounded-3 py-2 small fw-bold" disabled>
                                                <i class="bi bi-lock me-1"></i> ' . (in_array($order->status, ['ready_to_ship', 'shipped', 'delivered', 'completed']) ? 'Validated' : 'QC Pending') . '
                                            </button>') . '
                                        </div>
                                        <div class="col-6">
                                            ' . ($order->status === 'ready_to_ship' ? '
                                            <a href="' . $mod_url . '&sel=orderdetail&id=' . $order->order_id . '&act=setstatus&status=shipped&token=' . TOKEN . '" class="btn btn-primary w-100 rounded-3 py-2 small fw-bold">
                                                <i class="bi bi-truck me-1"></i> Ship Now
                                            </a>' : '
                                            <button class="btn btn-outline-secondary w-100 rounded-3 py-2 small fw-bold" disabled>
                                                <i class="bi bi-truck me-1"></i> Ship (Locked)
                                            </button>') . '
                                        </div>
                                        <div class="col-6">
                                            ' . ($order->status == 'shipped' ? '
                                            <a href="' . $mod_url . '&sel=orderdetail&id=' . $order->order_id . '&act=setstatus&status=delivered&token=' . TOKEN . '" class="btn btn-outline-success w-100 rounded-3 py-2 small fw-bold">
                                                <i class="bi bi-house-check me-1"></i> Delivered
                                            </a>' : '
                                            <button class="btn btn-outline-secondary w-100 rounded-3 py-2 small fw-bold" disabled>
                                                <i class="bi bi-lock me-1"></i> Delivered
                                            </button>') . '
                                        </div>
                                    </div>
                                    ' . (isset($order->packaging_code) && $order->packaging_code ? '
                                    <div class="mt-3 p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 text-center">
                                        <div class="row g-2">
                                            <div class="col-6 border-end">
                                                <div class="small text-muted text-uppercase fw-bold" style="font-size:0.65rem">Packaging Code</div>
                                                <div class="fw-black text-warning h5 mb-0">' . $order->packaging_code . '</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="small text-muted text-uppercase fw-bold" style="font-size:0.65rem">Warehouse Position</div>
                                                <div class="fw-black text-dark h5 mb-0">' . (isset($order->staging_location) ? $order->staging_location : '-') . '</div>
                                            </div>
                                        </div>
                                    </div>' : '') . '
                                    <hr class="my-3">
                                    ' . (($order->status !== 'cancelled') ? '
                                    <div class="d-grid gap-2">
                                        <form action="' . $mod_url . '&sel=orderdetail&id=' . $order->order_id . '" method="POST">
                                            <input type="hidden" name="token" value="' . TOKEN . '">
                                            <input type="hidden" name="order_id" value="' . $order->order_id . '">

                                            ' . (($order->status !== 'cancelled' && $order->status !== 'returned') ? '
                                            <button type="submit" name="cancel_order" class="btn btn-outline-danger w-100 rounded-3 py-2 small fw-bold mb-2" onclick="return confirm(\'Cancel this order? Stock will be returned.\')">
                                                <i class="bi bi-x-circle me-1"></i> Cancel Entire Order
                                            </button>' : '') . '

                                            ' . (in_array($order->status, ['delivered', 'completed']) ? '
                                            <button type="submit" name="refund_order" class="btn btn-outline-warning text-dark w-100 rounded-3 py-2 small fw-bold" onclick="return confirm(\'Process Refund & Return? Stock will be replenished and transaction status updated.\')">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Refund & Return
                                            </button>' : '') . '
                                        </form>
                                    </div>
                                    ' : '') . '
                                '
                                ],
                                [
                                    'type' => 'html',
                                    'html' => Hooks::filter('nixomers_orderdetail_management_actions', '', $order)
                                ]
                            ]
                        ],
                        [
                            'type' => 'card',
                            'title' => 'Payment Information',
                            'icon' => 'bi bi-credit-card',
                            'header_action' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="d-flex gap-2">
                                        <a href="' . $mod_url . '&sel=orderdetail&id=' . $order->order_id . '&act=recalculate_payment&token=' . TOKEN . '" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3" title="Recalculate Net Amount">
                                            <i class="bi bi-calculator me-1"></i> Recalculate
                                        </a>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPayment">
                                            <i class="bi bi-pencil-square me-1"></i> Update Payment
                                        </button>
                                    </div>'
                                ]
                            ],
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="mb-3">
                                        <div class="small text-muted text-uppercase fw-bold mb-1">Payment Method</div>
                                        <div class="fw-bold fs-5">' . strtoupper(($order->payment_method && $order->payment_method !== 'pending') ? $order->payment_method : ($transaction->method ?? 'pending')) . '</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="small text-muted text-uppercase fw-bold mb-1">Transaction ID / Ref</div>
                                        <div class="p-2 bg-light border rounded small font-monospace">
                                            ' . ($transaction && !empty($transaction->trans_id) ? $transaction->trans_id : '-') . '
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3 border-top pt-3">
                                        <div class="col-6">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Gross (Kotor)</div>
                                            <div class="fw-bold text-dark">' . $currency . ' ' . Nixomers::formatCurrency($transaction->amount ?? 0) . '</div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Net (Bersih)</div>
                                            <div class="fw-black text-success fs-5">' . $currency . ' ' . Nixomers::formatCurrency($transaction->net ?? 0) . '</div>
                                        </div>
                                    </div>

                                    <div class="row g-0 border-top pt-3">
                                        <div class="col-6 border-end pe-3">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Status</div>
                                            ' . (($transaction->status ?? 'pending') == 'paid' || ($transaction->status ?? 'pending') == 'completed' ? '<span class="badge bg-success">PAID</span>' : '<span class="badge bg-warning">' . strtoupper($transaction->status ?? 'pending') . '</span>') . '
                                        </div>
                                        <div class="col-6 ps-3 text-end">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Transaction Created</div>
                                            <div class="small fw-bold">' . ($transaction->date ?? $order->date) . '</div>
                                        </div>
                                    </div>
                                    ' . (!empty($transaction->paid_date) ? '
                                    <div class="row g-0 border-top pt-3 mt-3">
                                        <div class="col-12">
                                            <div class="small text-muted text-uppercase fw-bold mb-1">Paid / Settled Date</div>
                                            <div class="fw-bold text-success"><i class="bi bi-calendar-check me-1"></i>' . $transaction->paid_date . '</div>
                                        </div>
                                    </div>' : '') . '
                                    '
                                ]
                            ]
                        ],
                        [
                            'type' => 'card',
                            'title' => 'Order Status Info',
                            'icon' => 'bi bi-info-circle',
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => '
                                    <div class="text-center py-3">
                                        <div class="badge bg-' . $statusColor . ' fs-4 px-4 py-2 rounded-pill fw-black mb-3">' . strtoupper($order->status) . '</div>
                                        <div class="small text-muted">Order Date: ' . date('d M Y, H:i', strtotime($order->date)) . '</div>
                                    </div>'
                                ]
                            ]
                        ],
                        [
                            'type' => 'card',
                            'title' => 'Lifecycle Tracking',
                            'icon' => 'bi bi-clock-history',
                            'body_elements' => [
                                [
                                    'type' => 'html',
                                    'html' => (function() use ($orderId) {
                                        $logs = Query::table('nix_order_logs')->where('order_id', $orderId)->orderBy('date', 'DESC')->get();
                                        if (empty($logs)) return '<div class="text-center text-muted small py-3">No status changes recorded yet.</div>';
                                        
                                        $h = '<div class="timeline-order">';
                                        foreach ($logs as $log) {
                                            $h .= '
                                            <div class="d-flex mb-3 align-items-center">
                                                <div class="flex-shrink-0 me-3 text-center" style="width: 60px">
                                                    <div class="small fw-bold text-primary">'.date('d M', strtotime($log->date)).'</div>
                                                    <div class="text-muted" style="font-size: 0.65rem">'.date('H:i', strtotime($log->date)).'</div>
                                                </div>
                                                <div class="flex-grow-1 border-start ps-3 border-2">
                                                    <div class="small fw-black text-dark">' . strtoupper($log->new_status) . '</div>
                                                    <div class="small text-muted" style="font-size: 0.75rem">Updated by: <strong>' . $log->updated_by . '</strong></div>
                                                </div>
                                            </div>';
                                        }
                                        $h .= '</div>';
                                        return $h;
                                    })()
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
echo $modalsCollector ?? '';

echo '
<div class="modal fade" id="modalPayment" tabindex="-1">
    <div class="modal-dialog">
        <form action="' . Url::current() . '" method="POST" class="modal-content border-0 shadow-lg text-start">
            <input type="hidden" name="token" value="' . TOKEN . '">
            <input type="hidden" name="order_id" value="' . $orderId . '">
            <div class="modal-header">
                <h5 class="modal-title fw-black">Update Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 text-start">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Payment Status</label>
                        <select name="payment_status" class="form-select border-2">
                            <option value="pending" ' . (($transaction->status ?? '') == 'pending' ? 'selected' : '') . '>Pending</option>
                            <option value="completed" ' . (in_array(($transaction->status ?? ''), ['paid', 'completed']) ? 'selected' : '') . '>Paid / Completed</option>
                            <option value="cancelled" ' . (($transaction->status ?? '') == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                            <option value="expired" ' . (($transaction->status ?? '') == 'expired' ? 'selected' : '') . '>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Payment Method</label>
                        <select name="payment_method" class="form-select border-2">
                        ';
                            $gateways = Hooks::filter('nix_payment_gateways', []);
                            $isManual = (($transaction->method ?? '') == 'manual') ? 'selected' : '';
                            echo '<option value="manual" ' . $isManual . '>Manual / POS Cash</option>';
                            
                            foreach ($gateways as $k => $v) {
                                $sel = (($transaction->method ?? '') == $k) ? 'selected' : '';
                                echo '<option value="' . $k . '" ' . $sel . '>' . ($v['name'] ?? $k) . '</option>';
                            }
                            echo '
                            <option value="pending" ' . (($transaction->method ?? '') == 'pending' ? 'selected' : '') . '>Still Pending</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Transaction ID / Reference</label>
                        <input type="text" name="payment_trx_id" class="form-control" value="' . ($transaction->trans_id ?? '') . '" placeholder="Ref ID">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Gross Amount (Total Paid)</label>
                        <input type="number" name="payment_amount" class="form-control" value="' . ($transaction->amount ?? $order->total) . '" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Admin/Gateway Fee</label>
                        <input type="number" name="payment_fee" class="form-control" value="' . ($transaction->fee ?? 0) . '" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tax Amount</label>
                        <input type="number" name="payment_tax" class="form-control" value="' . ($transaction->tax ?? $order->tax) . '" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Actual Shipping Cost</label>
                        <input type="number" name="payment_shipping" class="form-control" value="' . ($transaction->shipping_cost ?? $order->shipping_cost) . '" step="0.01">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Payment Notes</label>
                        <textarea name="payment_notes" class="form-control" rows="2" placeholder="Additional info (optional)">' . ($transaction->notes ?? '') . '</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Paid / Settled Date <span class="text-muted fw-normal">(auto-set if empty)</span></label>
                        <input type="datetime-local" name="payment_paid_date" class="form-control" value="' . (!empty($transaction->paid_date) ? date("Y-m-d\TH:i", strtotime($transaction->paid_date)) : '') . '">
                        <div class="form-text">Only applies when status is <strong>Paid</strong> or <strong>Completed</strong>.</div>
                    </div>
                </div>
                <div class="alert alert-warning small mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Merubah data ini akan otomatis menghitung <strong>Net Amount</strong> (Gross - Fee - Tax - Shipping).
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_payment" class="btn btn-primary rounded-pill px-4">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalReadyShip" tabindex="-1">
    <div class="modal-dialog">
        <form action="' . Url::current() . '" method="POST" class="modal-content border-0 shadow-lg">
            <input type="hidden" name="token" value="' . TOKEN . '">
            <input type="hidden" name="order_id" value="' . $orderId . '">
            <div class="modal-header">
                <h5 class="modal-title fw-black">Validate Packaging</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="alert alert-success border-0 small mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i> All items have passed Quality Control and are ready for packing.
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Packaging Code (Kode Kemasan)</label>
                    <input type="text" name="packaging_code" class="form-control form-control-lg border-2 fw-bold" value="' . ($order->packaging_code ?: 'PKG-' . $order->order_id) . '" required>
                    <p class="text-muted small mt-2">Gunakan kode unik ini pada label kemasan fisik agar tidak tertukar dengan paket lain.</p>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Warehouse Staging Location / Posisi Rak</label>
                    <input type="text" name="staging_location" class="form-control border-2" value="' . (isset($order->staging_location) ? $order->staging_location : '') . '" placeholder="Contoh: Rak A1, Meja Packing 2, dsb.">
                    <p class="text-muted small mt-2">Informasi ini akan membantu tim fulfillment mencari paket dengan cepat.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="confirm_ready_to_ship" class="btn btn-warning rounded-pill px-4 fw-bold">
                    <i class="bi bi-box-seam me-1"></i> Mark Ready to Ship
                </button>
            </div>
        </form>
    </div>
</div>';
