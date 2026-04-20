<?php
/**
 * Name: Nixomers Pakasir
 * Desc: Pakasir Payment Gateway integration for Nixomers (QRIS & Virtual Account).
 * Version: 2.0.0
 * Build: 2.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-qr-code-scan
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// Fail-safe migration: Ensure payment_data exists
try {
    Db::$pdo->exec("ALTER TABLE `nix_orders` ADD COLUMN `payment_data` TEXT");
} catch (Exception $e) {
}


// ─────────────────────────────────────────────────────────────────────────────
// 1. Register gateway to Nixomers payment list
// ─────────────────────────────────────────────────────────────────────────────
Hooks::attach('nix_payment_gateways', function ($args) {
    $gateways = is_array($args) && isset($args[0]) ? $args[0] : $args;
    $gateways['pakasir'] = [
        'name' => 'Pakasir Payment (QRIS/VA)',
        'icon' => 'bi bi-qr-code-scan'
    ];
    return $gateways;
});

// ─────────────────────────────────────────────────────────────────────────────
// 2. Admin settings panel for Pakasir
// ─────────────────────────────────────────────────────────────────────────────
Hooks::attach('nix_gateway_settings_body_pakasir', function () {
    $methods = [
        'qris' => 'QRIS',
        'bni_va' => 'BNI Virtual Account',
        'bri_va' => 'BRI Virtual Account',
        'cimb_niaga_va' => 'CIMB Niaga Virtual Account',
        'bca_va' => 'BCA Virtual Account',
        'sampoerna_va' => 'Sampoerna Virtual Account',
        'bnc_va' => 'BNC Virtual Account',
        'maybank_va' => 'Maybank Virtual Account',
        'permata_va' => 'Permata Virtual Account',
        'atm_bersama_va' => 'ATM Bersama Virtual Account',
        'artha_graha_va' => 'Artha Graha Virtual Account',
    ];

    $webhookUrl = htmlspecialchars(Url::api('nixomers', '', ['action' => 'pakasir_webhook']));

    $ui = new UiBuilder();
    return $ui->renderElement([
        'type' => 'row',
        'items' => [
            [
                'width' => 6,
                'content' => [
                    'type' => 'input',
                    'input_type' => 'password',
                    'name' => 'nix_pakasir_key',
                    'label' => 'API Key',
                    'value' => Options::v('nix_pakasir_key'),
                    'placeholder' => 'API Key from Pakasir project',
                    'wrapper_class' => 'mb-2'
                ]
            ],
            [
                'width' => 6,
                'content' => [
                    'type' => 'input',
                    'input_type' => 'text',
                    'name' => 'nix_pakasir_project',
                    'label' => 'Project Slug',
                    'value' => Options::v('nix_pakasir_project'),
                    'placeholder' => 'e.g. depodomain',
                    'help' => 'Found on your Pakasir project dashboard.',
                    'wrapper_class' => 'mb-2'
                ]
            ],
            [
                'width' => 12,
                'content' => [
                    'type' => 'select',
                    'name' => 'nix_pakasir_method',
                    'label' => 'Default Payment Method',
                    'options' => $methods,
                    'selected' => Options::v('nix_pakasir_method') ?: 'qris',
                    'wrapper_class' => 'mb-2'
                ]
            ],
            [
                'width' => 12,
                'content' => [
                    'type' => 'raw',
                    'html' => '
                    <div class="mb-2">
                        <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">Webhook URL</label>
                        <div class="input-group mb-1">
                            <input type="text" class="form-control font-monospace py-2 px-3 fs-8 fw-bold bg-light shadow-none border rounded-start-4" readonly value="' . $webhookUrl . '">
                            <button class="btn btn-outline-secondary rounded-end-4 px-3 bg-white" type="button"
                                    onclick="navigator.clipboard.writeText(\'' . $webhookUrl . '\')">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                        <div class="form-text text-muted small mt-1">Paste this URL in your Pakasir project\'s Webhook URL field.</div>
                    </div>
                    '
                ]
            ]
        ]
    ], true);
});

// ─────────────────────────────────────────────────────────────────────────────
// 3. Save settings
// ─────────────────────────────────────────────────────────────────────────────
Hooks::attach('nix_payment_gateways_save', function () {
    Options::update('nix_pakasir_key', trim($_POST['nix_pakasir_key'] ?? ''));
    Options::update('nix_pakasir_project', trim($_POST['nix_pakasir_project'] ?? ''));
    Options::update('nix_pakasir_method', Typo::cleanX($_POST['nix_pakasir_method'] ?? 'qris'));
});

// ─────────────────────────────────────────────────────────────────────────────
// 4. Process: create Pakasir transaction when customer selects this gateway
// ─────────────────────────────────────────────────────────────────────────────
Hooks::attach('nix_payment_process_pakasir', function ($args) {
    $order = is_array($args) && isset($args[0]) ? $args[0] : $args;
    // Use Options::get() instead of v() for direct DB access
    $apiKey = Options::get('nix_pakasir_key');
    $project = Options::get('nix_pakasir_project');
    $method = Options::get('nix_pakasir_method') ?: 'qris';
    $orderId = $order->order_id ?? '';
    $amount = (int) round((float) ($order->total ?? 0));

    if (empty($apiKey) || empty($project)) {
        $error = ['error' => 'Pakasir API Key or Project Slug is not configured. Please set up Pakasir in Nixomers Settings.'];
        Query::table('nix_orders')->where('order_id', $orderId)->update(['payment_data' => json_encode($error)]);
        return;
    }

    $raw = Http::fetch([
        'url' => "https://app.pakasir.com/api/transactioncreate/{$method}",
        'curl' => true,
        'curl_options' => [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'project' => $project,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $apiKey,
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 15
        ]
    ]);
    // DEBUG: Log API call
    file_put_contents(GX_PATH . '/nix_pakasir_debug.log', "[" . date('Y-m-d H:i:s') . "] Order: $orderId, Method: $method, Response: " . $raw . "\n", FILE_APPEND);

    $res = json_decode($raw, true);

    // Store response payload for the show hook in nix_orders table
    if (!empty($res['payment'])) {
        $data = json_encode($res['payment']);
        // Use direct PDO update to be absolutely sure of status
        $stmt = Db::$pdo->prepare("UPDATE nix_orders SET payment_data = ? WHERE order_id = ?");
        $stmt->execute([$data, $orderId]);

        if ($stmt->rowCount() === 0) {
            file_put_contents(GX_PATH . '/nix_pakasir_debug.log', "[" . date('Y-m-d H:i:s') . "] DB Update Failed: No rows affected for $orderId. (SQL: UPDATE nix_orders SET payment_data = data WHERE order_id = $orderId)\n", FILE_APPEND);
        } else {
            file_put_contents(GX_PATH . '/nix_pakasir_debug.log', "[" . date('Y-m-d H:i:s') . "] DB Update Success for $orderId.\n", FILE_APPEND);
        }
    } else {
        $error = ['error' => $res['error'] ?? ($res['message'] ?? 'Unknown error from Pakasir API or connection failed.')];
        $data = json_encode($error);
        $stmt = Db::$pdo->prepare("UPDATE nix_orders SET payment_data = ? WHERE order_id = ?");
        $stmt->execute([$data, $orderId]);
        file_put_contents(GX_PATH . '/nix_pakasir_debug.log', "[" . date('Y-m-d H:i:s') . "] API Error for $orderId: " . $data . "\n", FILE_APPEND);
    }
});

// ─────────────────────────────────────────────────────────────────────────────
// 5. Show: render payment instructions with QR / VA number
// ─────────────────────────────────────────────────────────────────────────────
Hooks::attach('nix_payment_show_pakasir', function ($args) {
    $order = is_array($args) && isset($args[0]) ? $args[0] : $args;
    $currency = Options::get('nixomers_currency') ?: 'IDR';
    $orderId = $order->order_id ?? '';

    // Read transaction data stored in nix_orders.payment_data
    $txn = !empty($order->payment_data) ? json_decode($order->payment_data, true) : null;

    // If no transaction data yet or previous error — show error/retry
    // If no transaction data yet or previous error — show error/retry
    if (empty($txn) || isset($txn['error'])) {
        $errMsg = $txn['error'] ?? 'Transaction data not found. Please contact support.';
        $debugInfo = (defined('DEBUG') && DEBUG) ? '<div class="mt-3 small text-start p-3 bg-light border rounded-3"><code>Order ID: ' . $orderId . '<br>Raw Data: ' . htmlspecialchars(json_encode($order->payment_data)) . '</code></div>' : '';

        return '
        <div class="text-center py-5">
            <div class="display-1 text-danger mb-2"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <h2 class="fw-bold">Payment Error</h2>
            <p class="text-muted">' . htmlspecialchars($errMsg) . '</p>
            ' . $debugInfo . '
            <a href="' . Url::mod('payment', '', ['order_id' => $orderId]) . '" class="btn btn-primary px-4 rounded-pill mt-3">Retry Payment</a>
        </div>';
    }

    $paymentMethod = $txn['payment_method'] ?? 'unknown';
    $paymentNumber = htmlspecialchars($txn['payment_number'] ?? '');
    $totalPayment = number_format((float) ($txn['total_payment'] ?? $txn['amount'] ?? 0), 0, ',', '.');
    $expiredAt = $txn['expired_at'] ?? '';
    $expiredFmt = '';
    if ($expiredAt) {
        try {
            $dt = new DateTime($expiredAt);
            $dt->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $expiredFmt = $dt->format('d M Y, H:i') . ' WIB';
        } catch (Exception $e) {
            $expiredFmt = $expiredAt;
        }
    }

    $isQris = (strpos($paymentMethod, 'qris') !== false);
    $statusCheckUrl = Url::api('nixomers', '', ['action' => 'pakasir_status', 'order_id' => $orderId]);
    $successUrl = Url::mod('payment', '', ['order_id' => $orderId]);
    $bank = strtoupper(str_replace('_va', '', $paymentMethod));
    $qrImgUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($paymentNumber);

    return '
    <style>
        :root {
            --pakasir-primary: #4f46e5;
            --pakasir-success: #10b981;
            --pakasir-bg: #f8fafc;
        }
        #pakasir-payment-view {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Inter", sans-serif;
            padding: 2rem;
        }
        .payment-card {
            max-width: 420px !important;
            width: 100%;
            margin: 0 auto !important;
            background: #ffffff;
            border-radius: 2.5rem;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.15);
            border: none !important;
            overflow: hidden;
            text-align: center;
        }
        .pakasir-header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            padding: 1.5rem 1.5rem 5.5rem;
            position: relative;
        }
        .pakasir-header i { font-size: 1.5rem !important; opacity: 0.4; margin-bottom: 0.25rem; display: block; }
        .pakasir-header::after {
            content: "";
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 2rem;
            background: white;
            clip-path: polygon(0 100%, 100% 100%, 100% 0);
        }
        .pakasir-qr-wrapper {
            background: white;
            padding: 1.2rem;
            border-radius: 2rem;
            display: inline-block;
            box-shadow: 0 15px 30px -10px rgba(79, 70, 229, 0.2);
            position: relative;
            z-index: 10;
            margin-top: -5rem;
            border: 1px solid #f1f5f9;
        }
        .pakasir-qr-wrapper img { width: 180px; border-radius: 1rem; }
        
        .receipt-section { text-align: left; padding: 2rem 2.5rem; }
        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .receipt-label { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .receipt-value { font-weight: 700; color: #1e293b; font-size: 0.9rem; }
        
        .status-container {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 1.2rem;
            padding: 1rem;
            margin: 0 2.5rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .timer-badge {
            background: #fef2f2;
            color: #ef4444;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.2rem 0.6rem;
            border-radius: 0.5rem;
        }
    </style>

    <div id="pakasir-payment-view">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-6 col-md-8">
                    
                    <div class="card payment-card border-0">
                        <div class="pakasir-header">
                            <i class="bi bi-shield-lock-fill"></i>
                            <h4 class="fw-bold mb-1">Billing Overview</h4>
                            <p class="small opacity-75 mb-0 font-monospace">Ref: ' . htmlspecialchars($orderId) . '</p>
                        </div>
                        
                        <div class="card-body px-4 px-md-5 pb-5 pt-0">
                            
                            ' . ($isQris ? '
                            <div class="text-center mb-5">
                                <div class="pakasir-qr-wrapper">
                                    <img src="' . $qrImgUrl . '" class="img-fluid" style="width:200px" alt="QRIS">
                                </div>
                                <div class="mt-3">
                                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill border shadow-sm">
                                        <i class="bi bi-qr-code-scan me-1 text-primary"></i> Scan QRIS to Pay
                                    </span>
                                </div>
                            </div>
                            ' : '
                            <div class="mb-5 text-center">
                                <div class="p-4 bg-primary bg-opacity-5 rounded-4 border border-primary border-opacity-10 mb-2">
                                    <div class="receipt-label mb-1">Virtual Account Number</div>
                                    <div class="h2 fw-black text-primary font-monospace mb-2 letter-spacing-1">' . $paymentNumber . '</div>
                                    <button class="copy-btn" onclick="navigator.clipboard.writeText(\'' . $paymentNumber . '\'); this.innerHTML=\'Done!\'; setTimeout(()=>this.innerHTML=\'Copy VA\', 2000)">
                                        <i class="bi bi-clipboard me-1"></i> Copy VA
                                    </button>
                                </div>
                                <div class="small text-secondary fw-bold text-uppercase">' . $bank . ' Network</div>
                            </div>
                            ') . '

                            <div class="receipt-section mb-4">
                                <div class="receipt-item">
                                    <span class="receipt-label">Total Amount</span>
                                    <span class="receipt-value fs-4 text-primary fw-black">' . $currency . ' ' . $totalPayment . '</span>
                                </div>
                                <div class="receipt-item">
                                    <span class="receipt-label">Payment Gateway</span>
                                    <span class="receipt-value">' . strtoupper($paymentMethod) . '</span>
                                </div>
                                ' . ($expiredFmt ? '
                                <div class="receipt-item">
                                    <span class="receipt-label">Valid Until</span>
                                    <div class="text-end">
                                        <div class="receipt-value small">' . $expiredFmt . '</div>
                                        <span class="timer-badge">Auto-Expiry</span>
                                    </div>
                                </div>' : '') . '
                            </div>

                            <div class="status-container" id="pakasir-status-box">
                                <div class="spinner-border spinner-border-sm text-primary opacity-50" role="status"></div>
                                <span class="small fw-bold text-secondary" id="pakasir-status-text">Synchronizing payment status…</span>
                            </div>

                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="' . Url::mod('store') . '" class="text-decoration-none text-secondary small fw-bold">
                            <i class="bi bi-arrow-left-short fs-5 align-middle"></i> Back to Marketplace
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const checkUrl   = ' . json_encode($statusCheckUrl) . ';
        const successUrl = ' . json_encode($successUrl) . ';
        const statusText = document.getElementById("pakasir-status-text");
        const statusBox  = document.getElementById("pakasir-status-box");
        let attempts = 0;

        const checkStatus = () => {
            attempts++;
            fetch(checkUrl)
                .then(r => r.json())
                .then(d => {
                    // Fix: prioritize d.data.status which contains the actual payment state
                    const txStatus = (d.data && d.data.status ? d.data.status : "pending").toLowerCase();
                    
                    if (txStatus === "completed" || txStatus === "paid" || txStatus === "success") {
                        if (statusBox) {
                            statusBox.classList.remove("bg-light");
                            statusBox.classList.add("bg-success", "bg-opacity-10", "text-success");
                            statusBox.innerHTML = \'<i class="bi bi-check-circle-fill"></i> <span class="fw-bold">Payment Verified! Redirecting...</span>\';
                        }
                        setTimeout(() => window.location.href = successUrl, 1200);
                    } else {
                        if (statusText) statusText.innerText = "Syncing with Gateway... (" + attempts + ")";
                        setTimeout(checkStatus, 30000);
                    }
                })
                .catch(err => {
                    console.error("Polling error:", err);
                    setTimeout(checkStatus, 30000);
                });
        };

        setTimeout(checkStatus, 15000); // Initial check slightly faster, then 30s
    })();
    </script>';
});



// ─────────────────────────────────────────────────────────────────────────────
// 6. Modular API Handlers (Called via NixomersApi dispatcher)
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Handle Pakasir Webhook (POST)
 */
Hooks::attach('nix_api_pakasir_webhook', function ($args) {
    $data = is_array($args) && isset($args[0]) ? $args[0] : $args;

    if (empty($data['order_id']) || empty($data['amount'])) {
        return Api::error(400, 'Invalid payload');
    }

    $orderId = $data['order_id'];
    $amount = (float) $data['amount'];
    $status = $data['status'] ?? '';
    $fee = (float) ($data['fee'] ?? 0);

    $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
    if (!$order) {
        return Api::error(404, 'Order not found');
    }

    // Verify amount matches
    if (abs($amount - (float) $order->total) > 1) {
        return Api::error(400, 'Amount mismatch');
    }

    if ($status === 'completed' || $status === 'paid' || $status === 'success') {
        Query::table('nix_orders')
            ->where('order_id', $orderId)
            ->update(['status' => 'waiting', 'payment_method' => 'pakasir']);

        Query::table('nix_transactions')
            ->where('order_id', $orderId)
            ->update([
                'status' => 'completed',
                'method' => 'pakasir',
                'fee' => $fee,
                'tax' => (float) ($order->tax ?? 0),
                'shipping_cost' => (float) ($order->shipping_cost ?? 0),
                'description' => 'Paid via Pakasir ' . ($data['payment_method'] ?? 'QRIS/VA')
            ]);

        Nixomers::calculateNetTrans($orderId);

        Hooks::run('nix_order_paid', $order);
    }

    return Api::success(['status' => $status], 'Webhook processed');
});

/**
 * Handle Pakasir Status Polling (GET)
 */
Hooks::attach('nix_api_pakasir_status', function ($args) {
    $params = is_array($args) && isset($args[0]) ? $args[0] : $args;
    $orderId = $params['order_id'] ?? '';

    $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
    if (!$order) {
        return Api::error(404, 'Order not found');
    }

    // If already waiting/processed in DB, no need to call API
    if ($order->status === 'waiting' || $order->status === 'shipped' || $order->status === 'completed') {
        return Api::success(['status' => 'completed'], 'Payment already confirmed');
    }

    $apiKey = Options::get('nix_pakasir_key');
    $project = Options::get('nix_pakasir_project');
    $amount = (int) round((float) $order->total);

    if ($apiKey && $project) {
        $url = "https://app.pakasir.com/api/transactiondetail?project={$project}&amount={$amount}&order_id={$orderId}&api_key={$apiKey}";
        $raw = Http::fetch([
            'url' => $url,
            'curl' => true,
            'curl_options' => [CURLOPT_TIMEOUT => 10]
        ]);
        $res = json_decode($raw, true);

        $tx = $res['transaction'] ?? [];
        $txStatus = ($tx['status'] ?? 'pending');
        $fee = (float) ($tx['fee'] ?? 0);

        if ($txStatus === 'completed' || $txStatus === 'paid' || $txStatus === 'success') {
            Query::table('nix_orders')->where('order_id', $orderId)->update(['status' => 'waiting']);
            Query::table('nix_transactions')->where('order_id', $orderId)->update([
                'status' => 'completed',
                'method' => 'pakasir',
                'fee' => $fee,
                'tax' => (float) ($order->tax ?? 0),
                'shipping_cost' => (float) ($order->shipping_cost ?? 0)
            ]);
            Nixomers::calculateNetTrans($orderId);
        }

        return Api::success(['status' => $txStatus], 'Status check complete');
    }

    return Api::error(500, 'API credentials not configured');
});
