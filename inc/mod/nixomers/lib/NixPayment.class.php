<?php
/**
 * NixPayment Class
 * Handles Payment Selection and Processing logic for Nixomers
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class NixPayment
{
    /**
     * Renders Step 2: Payment Selector or Step 3: Success Page
     */
    public static function render()
    {
        $orderId = $_GET['order_id'] ?? '';
        
        // 1. Handle Order Submission from Checkout (Step 1 -> Step 2)
        if (isset($_POST['address']) && !isset($_GET['order_id'])) {
            return self::handleOrderSubmission();
        }

        // 2. Handle Payment Selection Submission (Step 2 -> Step 3)
        if (isset($_POST['payment_method']) && !empty($orderId)) {
            self::handlePaymentMethodSubmission($orderId);
            // Redirect back to the same URL to prevent form re-submission on refresh
            header('Location: ' . Url::mod('payment&order_id=' . $orderId));
            exit;
        }

        // 3. Render View based on order state
        $order = !empty($orderId) ? Query::table('nix_orders')->where('order_id', $orderId)->first() : null;
        if (!$order) {
             return '<div class="alert alert-warning">Order not found.</div>';
        }

        // Phase 2: Payment Method Selection
        if ($order->payment_method === 'pending' || empty($order->payment_method)) {
            return self::showSelection($order);
        }

        // Phase 3: Payment Instructions (gateway processes & shows payment info)
        // Only advance to success when status is explicitly 'paid'
        if (($order->status ?? 'pending') !== 'paid') {
            // Allow gateway to render its own payment instructions page
            $gatewayHtml = Hooks::run('nix_payment_show_' . $order->payment_method, $order);
            if (!empty($gatewayHtml)) {
                return $gatewayHtml;
            }
            // Fallback: generic waiting page
            return self::showPaymentWaiting($order);
        }

        // Phase 4: Success Page
        // Theme Override
        $themeOut = Nixomers::renderThemeView('payment', ['order' => $order]);
        if ($themeOut !== false) return $themeOut;

        $framework = Nixomers::getFramework();

        if ($framework === 'tailwindcss') {
            $html = '
            <div class="text-center py-20 px-4">
                <div class="mb-12">
                    <div class="text-8xl text-green-500 mb-6 drop-shadow-sm"><i class="bi bi-check-circle-fill"></i></div>
                    <h1 class="text-4xl font-black text-gray-900 mb-4">Payment Success!</h1>
                    <p class="text-gray-500 text-xl max-w-xl mx-auto leading-relaxed">Thank you for your order. We are processing your delivery right now.</p>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-3xl p-10 max-w-lg mx-auto transform transition-all hover:shadow-xl">
                    <h5 class="text-xl font-bold mb-4 text-gray-900">What\'s Next?</h5>
                    <p class="text-sm text-gray-400 mb-8 leading-relaxed">An email confirmation has been sent to your address. You can track your order status in your account dashboard.</p>
                    <a href="' . Url::mod('store') . '" class="inline-block px-12 py-4 bg-blue-600 text-white font-bold rounded-full shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Return to Store</a>
                </div>
            </div>';
        } else {
            $html = '
            <div class="text-center py-5">
                <div class="mb-5">
                    <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                    <h1 class="fw-bold">Payment Success!</h1>
                    <p class="text-muted fs-5">Thank you for your order. We are processing your delivery right now.</p>
                </div>
                <div class="card border-0 shadow-sm rounded-4 p-5 max-w-500 mx-auto">
                    <h5 class="fw-bold mb-4">What\'s Next?</h5>
                    <p class="small text-muted mb-4">An email confirmation has been sent to your address. You can track your order status in your account dashboard.</p>
                    <a href="' . Url::mod('store') . '" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold">Return to Store</a>
                </div>
            </div>';
        }
        return $html;
    }

    /**
     * Render a generic "waiting for payment" page when a gateway has no custom show hook.
     */
    private static function showPaymentWaiting($order)
    {
        $method = strtoupper($order->payment_method ?? 'UNKNOWN');
        $orderId = $order->order_id ?? '';
        $currency = Options::v('nixomers_currency') ?: 'IDR';
        $total = number_format((float)($order->total ?? 0), 0, ',', '.');
        $framework = Nixomers::getFramework();

        if ($framework === 'tailwindcss') {
            return '
            <div class="max-w-lg mx-auto py-16 px-4 text-center">
                <div class="text-7xl text-blue-500 mb-6"><i class="bi bi-hourglass-split"></i></div>
                <h2 class="text-3xl font-black mb-2">Waiting for Payment</h2>
                <p class="text-gray-500 mb-8">Complete your payment via <strong>' . $method . '</strong></p>
                <div class="bg-white border shadow-sm rounded-2xl p-8 text-left mb-6">
                    <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Order ID</span><strong>' . htmlspecialchars($orderId) . '</strong></div>
                    <div class="flex justify-between py-2 border-b"><span class="text-gray-500">Method</span><strong>' . $method . '</strong></div>
                    <div class="flex justify-between py-2 font-bold text-lg"><span>Total</span><span class="text-blue-600">' . $currency . ' ' . $total . '</span></div>
                </div>
                <p class="text-sm text-gray-400">Your order is being held. Once your payment is confirmed, it will be processed automatically.</p>
            </div>';
        } else {
            return '
            <div class="text-center py-5">
                <div class="display-1 text-primary mb-3"><i class="bi bi-hourglass-split"></i></div>
                <h2 class="fw-bold">Waiting for Payment</h2>
                <p class="text-muted">Complete your payment via <strong>' . $method . '</strong></p>
                <div class="card border-0 shadow-sm rounded-4 p-4 mx-auto mt-4" style="max-width:480px">
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Order ID</span><strong>' . htmlspecialchars($orderId) . '</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Method</span><strong>' . $method . '</strong></div>
                    <div class="d-flex justify-content-between py-2 fs-5 fw-bold"><span>Total</span><span class="text-primary">' . $currency . ' ' . $total . '</span></div>
                </div>
                <p class="small text-muted mt-4">Your order is being held. Once your payment is confirmed, it will be processed automatically.</p>
            </div>';
        }
    }

    private static function handleOrderSubmission()
    {
        $cart = $_SESSION['nix_cart'] ?? [];
        if (empty($cart)) return '';

        $subtotal = 0;
        foreach ($cart as $id => $qty) {
            $subtotal += (Posts::getParam('price', $id) ?: 0) * $qty;
        }
        $taxRate = (float) Options::v('nixomers_tax') ?: 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $shippingCost = (float) ($_POST['shipping_cost'] ?? 0);
        $total = $subtotal + $taxAmount + $shippingCost;

        $addr = Typo::cleanX($_POST['address']);
        $country = Typo::cleanX($_POST['country'] ?? 'Indonesia');
        $prov = Typo::cleanX($_POST['province_name'] ?? '');
        $city = Typo::cleanX($_POST['city_name'] ?? '');
        $dist = Typo::cleanX($_POST['district_name'] ?? '');
        $vill = Typo::cleanX($_POST['village_name'] ?? '');

        $inv_id = strtoupper(substr(uniqid(), -5));
        $order_id = Nixomers::generateId('invoice');
        $fullAddress = "{$addr}\n{$vill}, {$dist}, {$city}, {$prov}, {$country}";

        $vars = [
            'order_id' => $order_id,
            'customer_name' => Typo::cleanX($_POST['name'] ?? ($_POST['fname'] . ' ' . $_POST['lname'])),
            'customer_email' => Typo::cleanX($_POST['email'] ?? ''),
            'customer_phone' => Typo::cleanX($_POST['phone'] ?? ''),
            'shipping_country' => $country,
            'shipping_province' => $prov,
            'shipping_city' => $city,
            'shipping_district' => $dist,
            'shipping_village' => $vill,
            'shipping_street' => $addr,
            'shipping_address' => $fullAddress,
            'shipping_courier' => Typo::cleanX($_POST['shipping_courier'] ?? ''),
            'shipping_service' => Typo::cleanX($_POST['shipping_service'] ?? ''),
            'shipping_cost' => $shippingCost,
            'shipping_etd' => Typo::cleanX($_POST['shipping_etd'] ?? ''),
            'shipping_engine' => Typo::cleanX($_POST['shipping_engine'] ?? ''),
            'payment_method' => 'pending',
            'cart_items' => json_encode($cart),
            'subtotal' => (float) $subtotal,
            'tax' => (float) $taxAmount,
            'total' => (float) $total,
            'status' => 'pending',
            'date' => date('Y-m-d H:i:s')
        ];

        try {
            Query::table('nix_orders')->insert($vars);
            
            // Notification: New Order
            Nixomers::addNotification(
                'ORDER_NEW',
                'New Order: ' . $order_id,
                'New order received from ' . $vars['customer_name'] . ' for ' . $vars['total'],
                'index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $order_id,
                'admin'
            );
            Nixomers::addNotification(
                'ORDER_NEW',
                'New Sales Opportunity: ' . $order_id,
                'New order received from ' . $vars['customer_name'] . ' for ' . $vars['total'],
                'index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $order_id,
                'sales'
            );

            // Trigger hook for new order submission
            Hooks::run('nix_order_submitted', $order_id, $vars);

            // Transaction entry - use consolidated ID generator
            $trx_id = Nixomers::generateId('trx');

            Query::table('nix_transactions')->insert([
                'trans_id' => $trx_id,
                'order_id' => $order_id,
                'type' => 'income',
                'amount' => (float) $total,
                'fee' => 0,
                'tax' => (float) $taxAmount,
                'shipping_cost' => (float) $shippingCost,
                'net' => (float) $total - (float) $taxAmount - (float) $shippingCost,
                'description' => 'Order ' . $order_id . ' (Pending Payment)',
                'method' => 'pending',
                'status' => 'pending',
                'date' => date('Y-m-d H:i:s')
            ]);

            foreach ($cart as $id => $qty) {
                // Deduct Inventory
                NixInventory::deduct($id, $qty, $order_id, 'New Order: ' . $order_id);
            }

            $_SESSION['nix_cart'] = [];
            header("Location: " . Url::mod('payment&order_id='.$order_id));
            exit;
        } catch (Exception $e) {
            return '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }

    private static function handlePaymentMethodSubmission($orderId)
    {
        $method = Typo::cleanX($_POST['payment_method']);
        Query::table('nix_orders')->where('order_id', $orderId)->update(['payment_method' => $method]);
        Query::table('nix_transactions')->where('order_id', $orderId)->update([
            'method' => $method,
            'description' => 'Order ' . $orderId . ' via ' . $method
        ]);

        $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
        if ($order) {
            Hooks::run('nix_payment_process', $order);
            Hooks::run('nix_payment_process_' . $method, $order);
        }
    }

    public static function showSelection($order)
    {

        $framework = Nixomers::getFramework();
        $currency = Options::v('nixomers_currency') ?: 'IDR';
        
        $gateways = Hooks::filter('nix_payment_gateways', [
            'bank_transfer' => ['name' => 'Bank Transfer', 'icon' => 'bi bi-bank'],
            'paypal' => ['name' => 'PayPal', 'icon' => 'bi bi-paypal']
        ]);
        $enabledStr = Options::v('nix_enabled_gateways') ?: 'bank_transfer';
        $enabled = explode(',', $enabledStr);

        if ($framework === 'tailwindcss') {
            $gwHtml = '';
            foreach ($gateways as $k => $v) {
                if (!in_array($k, $enabled)) continue;
                $gwHtml .= '
                <label class="flex items-center gap-4 p-5 border border-gray-200 rounded-3xl cursor-pointer hover:bg-gray-50 transition-all select-none has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:ring-2 has-[:checked]:ring-blue-100">
                    <input type="radio" name="payment_method" value="' . $k . '" class="h-5 w-5 text-blue-600" required>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-white rounded-2xl border border-gray-100 flex items-center justify-center text-2xl text-gray-400">
                            <i class="' . ($v['icon'] ?? 'bi bi-credit-card') . '"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">' . $v['name'] . '</p>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mt-1">Gateway</p>
                        </div>
                    </div>
                </label>';
            }

            return '
            <div class="max-w-2xl mx-auto py-12 px-4 text-center">
                <div class="mb-10 text-center">
                    <span class="inline-block px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full mb-4">Step 2: Payment Choice</span>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">Select Payment Method</h1>
                    <p class="text-gray-500">Order ID: <span class="font-bold text-gray-900">' . $order->order_id . '</span></p>
                </div>

                <div class="bg-white border border-gray-100 shadow-xl rounded-[40px] p-8 md:p-12 mb-8 text-left">
                    <div class="mb-8 p-6 bg-gray-50 rounded-3xl border border-gray-100 flex justify-between items-center">
                        <span class="text-gray-500 font-bold uppercase text-xs tracking-wider">Total Amount Due</span>
                        <span class="text-2xl font-black text-blue-600">' . $currency . ' ' . Nixomers::formatCurrency($order->total) . '</span>
                    </div>

                    <form method="post">
                        <div class="mb-8">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Preferred Payment Gateway</label>
                            <div class="grid grid-cols-1 gap-4">
                                ' . $gwHtml . '
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white font-black py-5 rounded-full shadow-2xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all flex items-center justify-center text-lg">
                            Complete Order <i class="bi bi-shield-check ms-3"></i>
                        </button>
                    </form>
                </div>
                <p class="text-xs text-gray-400 font-bold"><i class="bi bi-lock-fill"></i> Secure SSL Connection</p>
            </div>';
        } else {
            // Bootstrap
            $gwHtml = '';
            foreach ($gateways as $k => $v) {
                if (!in_array($k, $enabled)) continue;
                $gwHtml .= '
                <div class="col-12">
                    <label class="d-flex align-items-center gap-3 p-3 border rounded-4 position-relative cursor-pointer h-100 transition-all shadow-sm-hover border-light-subtle bg-white text-decoration-none">
                        <input type="radio" name="payment_method" value="' . $k . '" class="form-check-input mt-0 shadow-none border-2" style="width:1.2rem;height:1.2rem;" required>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                <i class="' . ($v['icon'] ?? 'bi bi-credit-card') . ' fs-4 text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6 text-decoration-none">' . $v['name'] . '</div>
                                <div class="small text-muted text-uppercase fw-bold" style="font-size:10px;letter-spacing:1px;">Gateway</div>
                            </div>
                        </div>
                    </label>
                </div>';
            }

            return '
            <div class="container py-5">
                <style>
                    .shadow-sm-hover:hover { shadow: 0 .5rem 1.5rem rgba(0,0,0,.08)!important; border-color: #0d6efd !important; }
                    .fw-black { font-weight: 900; }
                </style>
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 text-center">
                        <div class="mb-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-3 small fw-bold text-uppercase">Step 2: Payment Choice</span>
                            <h2 class="fw-black text-dark mb-1">Select Payment Method</h2>
                            <p class="text-muted small">Order Reference: <strong>' . $order->order_id . '</strong></p>
                        </div>

                        <div class="card border-0 shadow-lg rounded-5 p-4 p-md-5 mb-4 overflow-hidden">
                            <div class="bg-light rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center border">
                                <span class="small fw-bold text-muted text-uppercase">Total Bill</span>
                                <span class="h4 mb-0 fw-black text-primary">' . $currency . ' ' . Nixomers::formatCurrency($order->total) . '</span>
                            </div>

                            <form method="post" class="text-start">
                                <div class="mb-4">
                                    <h6 class="text-uppercase fw-bold small text-muted mb-3 tracking-wider">Available Payment Channels</h6>
                                    <div class="row g-3">
                                        ' . $gwHtml . '
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm">
                                    Complete Order <i class="bi bi-chevron-right ms-2"></i>
                                </button>
                            </form>
                        </div>
                        <div class="text-muted small d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-lock-fill"></i> SSL Secure Connection
                        </div>
                    </div>
                </div>
            </div>';
        }
    }
}
