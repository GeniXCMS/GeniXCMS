<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * NixOrderView Class
 *
 * Handles the frontend order detail view.
 */
class NixOrderView
{
    /**
     * Renders the complete order detail page.
     *
     * @param array $args Arguments from mod_control hook.
     * @return string HTML content.
     */
    public static function render(array $args): string
    {
        $orderId = $args['order_id'] ?? ($_GET['order_id'] ?? ($_GET['id'] ?? ''));
        if (empty($orderId)) {
            return '<div class="gx-p-5 gx-text-center gx-card gx-rounded-lg">
                <span class="material-symbols-outlined gx-text-muted gx-mb-4" style="font-size: 3rem;">search_off</span>
                <h2 class="gx-h3 gx-fw-bold">Order Not Found</h2>
                <p class="gx-text-muted gx-mt-2">Please check your order ID and try again.</p>
            </div>';
        }

        $order = Query::table('nix_orders')->where('order_id', $orderId)->orWhere('id', $orderId)->first();
        if (!$order) {
            return '<div class="gx-p-5 gx-text-center gx-card gx-rounded-lg">
                <span class="material-symbols-outlined gx-text-danger gx-mb-4" style="font-size: 3rem;">error</span>
                <h2 class="gx-h3 gx-fw-bold">Invalid Order</h2>
                <p class="gx-text-muted gx-mt-2">The order ID #' . htmlspecialchars($orderId) . ' does not exist in our records.</p>
            </div>';
        }

        // Security check: only the customer (by email) or admin can view
        $isLoggedIn = User::isLoggedin();
        $viewerEmail = Session::val('email');
        if (!$isLoggedIn || ($order->customer_email !== $viewerEmail && Session::val('group') > 0)) {
            return '<div class="gx-p-5 gx-text-center gx-card gx-rounded-lg">
                <span class="material-symbols-outlined gx-text-danger gx-mb-4" style="font-size: 3rem;">lock</span>
                <h2 class="gx-h3 gx-fw-bold">Access Denied</h2>
                <p class="gx-text-muted gx-mt-2">You don\'t have permission to view this order details.</p>
                <a href="' . Url::login() . '" class="gx-btn gx-btn-primary gx-mt-4 gx-rounded-full">Please Login</a>
            </div>';
        }

        $items = json_decode($order->cart_items ?? '[]', true);
        $currency = Options::v('nixomers_currency') ?: 'IDR';

        // Prepare Status UI
        $statusMap = [
            'pending' => ['color' => 'secondary', 'icon' => 'schedule', 'label' => 'Waiting for Action'],
            'unpaid' => ['color' => 'danger', 'icon' => 'payments', 'label' => 'Awaiting Payment'],
            'paid' => ['color' => 'primary', 'icon' => 'check_circle', 'label' => 'Payment Confirmed'],
            'processing' => ['color' => 'primary', 'icon' => 'package_2', 'label' => 'Harvesting Process'],
            'waiting' => ['color' => 'warning', 'icon' => 'hourglass_empty', 'label' => 'Awaiting Fulfillment'],
            'ready_to_ship' => ['color' => 'info', 'icon' => 'inventory_2', 'label' => 'Ready to Ship'],
            'shipped' => ['color' => 'primary', 'icon' => 'local_shipping', 'label' => 'On the Way'],
            'completed' => ['color' => 'success', 'icon' => 'verified', 'label' => 'Delivered & Completed'],
            'cancelled' => ['color' => 'danger', 'icon' => 'cancel', 'label' => 'Order Cancelled']
        ];
        $st = $statusMap[$order->status] ?? ['color' => 'muted', 'icon' => 'help', 'label' => ucfirst($order->status)];

        ob_start();
        ?>
        <div class="gx-order-detail-view gx-mb-5">
            <!-- Order Hero / Status -->
            <div class="gx-d-flex gx-flex-column gx-flex-row-md gx-justify-between gx-items-center gx-mb-4 gx-gap-3">
                <div>
                    <div class="gx-d-flex gx-items-center gx-gap-2 gx-mb-2">
                        <a href="<?= Url::user(Session::val('username'), 'purchase') ?>"
                            class="gx-btn gx-btn-secondary gx-rounded-full gx-p-1" style="min-width: 32px; height: 32px; display:inline-flex; align-items:center; justify-content:center;">
                            <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_back</span>
                        </a>
                        <span class="gx-text-muted gx-fw-bold gx-text-uppercase gx-text-xs" style="letter-spacing: 0.1em;">Transaction Details</span>
                    </div>
                    <h1 class="gx-h2 gx-fw-bold gx-m-0">Order #<?= htmlspecialchars($order->order_id ?? $order->id) ?></h1>
                    <?php if (!empty($order->shipping_resi)): ?>
                        <div class="gx-mt-2 gx-d-flex gx-items-center gx-gap-2">
                            <span class="gx-badge gx-bg-info gx-text-uppercase" style="letter-spacing: 0.1em; font-size: 0.65rem;">AWB / No. Resi</span>
                            <span class="gx-h5 gx-fw-bold gx-m-0" style="font-family: monospace;"><?= htmlspecialchars($order->shipping_resi) ?></span>
                        </div>
                    <?php endif; ?>
                    <p class="gx-text-muted gx-fw-bold gx-mt-1">Placed on <?= Date::format($order->date, 'l, d F Y') ?>
                        at <?= Date::format($order->date, 'H:i') ?> WIB
                    </p>
                </div>
                <div class="gx-d-flex gx-items-center gx-gap-3 gx-bg-soft gx-rounded-lg gx-p-3 gx-border">
                    <span class="material-symbols-outlined gx-text-<?= $st['color'] ?>" style="font-size: 2.5rem;"><?= $st['icon'] ?></span>
                    <div>
                        <div class="gx-text-xs gx-fw-bold gx-text-uppercase gx-text-muted" style="letter-spacing: 0.1em;">Status</div>
                        <div class="gx-h5 gx-fw-bold gx-m-0 gx-text-uppercase"><?= $st['label'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="gx-row gx-mt-4">
                <!-- Left: Items & Payment Detail -->
                <div class="gx-col-12 gx-md-col-8">
                    <!-- Products Table -->
                    <section class="gx-card gx-rounded-lg gx-mb-4 gx-p-0" style="overflow: hidden;">
                        <div class="gx-p-3 gx-bg-soft gx-border-bottom gx-d-flex gx-items-center gx-justify-between">
                            <h2 class="gx-h5 gx-m-0 gx-d-flex gx-items-center gx-gap-2">
                                <span class="material-symbols-outlined gx-text-primary">shopping_basket</span>
                                Items Ordered
                            </h2>
                            <span class="gx-badge gx-bg-secondary gx-rounded-full"><?= count($items) ?> Items</span>
                        </div>
                        <div>
                            <?php foreach ($items as $pId => $qty):
                                $product = Query::table('posts')->where('id', $pId)->first();
                                if (!$product)
                                    continue;
                                $price = (float) Posts::getParam('price', $pId) ?: 0;
                                $img = Posts::getPostImage($pId);
                                ?>
                                <div class="gx-p-4 gx-border-bottom gx-d-flex gx-items-center gx-gap-3">
                                    <div class="gx-bg-soft gx-rounded" style="width: 80px; height: 80px; flex-shrink: 0; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                        <?php if ($img): ?>
                                            <img src="<?= Url::thumb($img, 'crop', '200x200') ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="">
                                        <?php else: ?>
                                            <span class="material-symbols-outlined gx-text-muted" style="font-size:2rem;">eco</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <h4 class="gx-h6 gx-fw-bold gx-m-0"><?= Typo::Xclean($product->title) ?></h4>
                                        <div class="gx-d-flex gx-items-center gx-gap-2 gx-text-xs gx-fw-bold gx-text-muted gx-text-uppercase gx-mt-1">
                                            <span><?= $currency ?> <?= number_format($price, 0, ',', '.') ?> / unit</span>
                                            <span>&#8226;</span>
                                            <span>Qty: <?= $qty ?></span>
                                        </div>
                                    </div>
                                    <div class="gx-h5 gx-fw-bold gx-text-primary gx-m-0">
                                        <?= $currency ?> <?= number_format($price * $qty, 0, ',', '.') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Totals Footer -->
                        <div class="gx-p-4 gx-bg-soft">
                            <div class="gx-d-flex gx-justify-between gx-text-muted gx-fw-bold gx-mb-1">
                                <span>Subtotal</span>
                                <span><?= $currency ?> <?= number_format($order->subtotal, 0, ',', '.') ?></span>
                            </div>
                            <div class="gx-d-flex gx-justify-between gx-text-muted gx-fw-bold gx-mb-1">
                                <span>Tax/Fee</span>
                                <span><?= $currency ?> <?= number_format($order->tax, 0, ',', '.') ?></span>
                            </div>
                            <div class="gx-d-flex gx-justify-between gx-text-muted gx-fw-bold gx-mb-1">
                                <span>Shipping Cost (<?= htmlspecialchars($order->shipping_service ?: 'Standard') ?>)</span>
                                <span><?= $currency ?> <?= number_format((float) ($order->shipping_cost ?: 0), 0, ',', '.') ?></span>
                            </div>
                            <div class="gx-pt-3 gx-mt-3 gx-d-flex gx-justify-between gx-items-end" style="border-top: 2px dashed var(--gx-border);">
                                <div>
                                    <div class="gx-text-xs gx-fw-bold gx-text-muted gx-text-uppercase" style="letter-spacing: 0.1em;">Grand Total</div>
                                    <div class="gx-h3 gx-fw-bold gx-text-primary gx-m-0">
                                        <?= $currency ?> <?= number_format($order->total, 0, ',', '.') ?>
                                    </div>
                                </div>
                                <?php if ($order->status == 'unpaid' || $order->status == 'pending'): ?>
                                    <a href="<?= NixomersUrl::payment($orderId) ?>" class="gx-btn gx-btn-danger gx-rounded-full gx-fw-bold">
                                        Proceed to Pay Now
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Payment Information -->
                    <?php $trans = Query::table('nix_transactions')->where('order_id', $order->order_id)->first(); ?>
                    <div class="gx-row">
                        <div class="gx-col-12 gx-md-col-6 gx-mb-4">
                            <section class="gx-card gx-rounded-lg" style="height: 100%;">
                                <h3 class="gx-text-xs gx-fw-bold gx-text-primary gx-text-uppercase gx-mb-3" style="letter-spacing: 0.1em;">Payment Method</h3>
                                <div class="gx-d-flex gx-items-center gx-gap-3">
                                    <div class="gx-bg-soft gx-text-primary gx-rounded-full gx-d-flex gx-items-center gx-justify-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                        <span class="material-symbols-outlined">account_balance_wallet</span>
                                    </div>
                                    <div>
                                        <div class="gx-fw-bold gx-h6 gx-m-0">
                                            <?= strtoupper(str_replace('_', ' ', $order->payment_method ?? '')) ?>
                                        </div>
                                        <?php
                                        $currentStatus = strtolower(trim($order->status));
                                        $hiddenStatuses = ['pending', 'unpaid', 'cancelled'];
                                        if (!in_array($currentStatus, $hiddenStatuses)):
                                            $paymentDataDisplay = $order->payment_data ?? '';
                                            $decodedData = json_decode($paymentDataDisplay, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
                                                $paymentDataDisplay = 'Verified Payment';
                                            } else {
                                                $paymentDataDisplay = $paymentDataDisplay ?: 'Verified Payment';
                                            }
                                            ?>
                                            <div class="gx-text-xs gx-fw-bold gx-text-muted" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= htmlspecialchars($paymentDataDisplay) ?>
                                            </div>
                                            <?php if ($trans): ?>
                                                <div class="gx-mt-2 gx-pt-2 gx-border-top">
                                                    <div class="gx-text-xs gx-fw-bold gx-text-primary gx-text-uppercase gx-mb-1" style="font-size: 0.65rem;">Verification Details</div>
                                                    <div class="gx-text-xs gx-fw-bold gx-m-0">Verified on <?= Date::format($trans->paid_date, 'd M Y') ?></div>
                                                    <div class="gx-text-muted" style="font-size: 0.65rem;">Ref: <?= htmlspecialchars($trans->trans_id) ?></div>
                                                    <?php if ($trans->notes): ?>
                                                        <div class="gx-text-muted gx-mt-1" style="font-size: 0.65rem; font-style: italic;">
                                                            <?= htmlspecialchars($trans->notes) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="gx-mt-2 gx-pt-2 gx-border-top">
                                                    <div class="gx-text-xs gx-fw-bold gx-text-info gx-text-uppercase gx-mb-1" style="font-size: 0.65rem;">Status</div>
                                                    <div class="gx-text-xs gx-fw-bold gx-m-0">Payment Confirmed & Secured</div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="gx-col-12 gx-md-col-6 gx-mb-4">
                            <section class="gx-card gx-rounded-lg" style="height: 100%;">
                                <h3 class="gx-text-xs gx-fw-bold gx-text-primary gx-text-uppercase gx-mb-3" style="letter-spacing: 0.1em;">Transaction ID</h3>
                                <div class="gx-d-flex gx-items-center gx-gap-3">
                                    <div class="gx-bg-soft gx-text-primary gx-rounded-full gx-d-flex gx-items-center gx-justify-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                        <span class="material-symbols-outlined">receipt</span>
                                    </div>
                                    <div>
                                        <div class="gx-fw-bold gx-h6 gx-m-0">
                                            <?= $trans ? htmlspecialchars($trans->trans_id) : 'PENDING' ?>
                                        </div>
                                        <div class="gx-text-xs gx-fw-bold gx-text-muted">Validated Secure Transaction</div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>

                <!-- Right: Shipping Info -->
                <div class="gx-col-12 gx-md-col-4">
                    <section class="gx-card gx-rounded-lg gx-mb-4" style="position: sticky; top: 2rem;">
                        <h3 class="gx-text-xs gx-fw-bold gx-text-primary gx-text-uppercase gx-mb-4 gx-border-bottom gx-pb-2" style="letter-spacing: 0.1em;">
                            Shipping Destination
                        </h3>

                        <div style="display:flex; flex-direction:column; gap: 1.5rem;">
                            <div class="gx-d-flex gx-gap-3">
                                <span class="material-symbols-outlined gx-text-primary gx-bg-soft gx-rounded-lg gx-d-flex gx-items-center gx-justify-center" style="width: 40px; height: 40px; flex-shrink:0;">person</span>
                                <div>
                                    <div class="gx-text-xs gx-fw-bold gx-text-uppercase gx-text-muted" style="font-size: 0.65rem; letter-spacing: 0.1em;">Recipient</div>
                                    <div class="gx-fw-bold gx-m-0"><?= htmlspecialchars($order->customer_name ?? '') ?></div>
                                    <div class="gx-text-sm gx-fw-medium gx-text-muted">
                                        <?= htmlspecialchars($order->customer_phone ?? '') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="gx-d-flex gx-gap-3">
                                <span class="material-symbols-outlined gx-text-primary gx-bg-soft gx-rounded-lg gx-d-flex gx-items-center gx-justify-center" style="width: 40px; height: 40px; flex-shrink:0;">location_on</span>
                                <div>
                                    <div class="gx-text-xs gx-fw-bold gx-text-uppercase gx-text-muted" style="font-size: 0.65rem; letter-spacing: 0.1em;">Delivery Address</div>
                                    <div class="gx-fw-bold gx-m-0">
                                        <?= htmlspecialchars($order->shipping_street ?? '') ?>
                                    </div>
                                    <div class="gx-text-sm gx-fw-medium gx-text-muted" style="line-height: 1.6;">
                                        <?= htmlspecialchars($order->shipping_village ?? '') ?>,
                                        <?= htmlspecialchars($order->shipping_district ?? '') ?><br>
                                        <?= htmlspecialchars($order->shipping_city ?? '') ?>,
                                        <?= htmlspecialchars($order->shipping_province ?? '') ?><br>
                                        <?= htmlspecialchars($order->shipping_country ?? '') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="gx-d-flex gx-gap-3">
                                <span class="material-symbols-outlined gx-text-primary gx-bg-soft gx-rounded-lg gx-d-flex gx-items-center gx-justify-center" style="width: 40px; height: 40px; flex-shrink:0;">local_shipping</span>
                                <div>
                                    <div class="gx-text-xs gx-fw-bold gx-text-uppercase gx-text-muted" style="font-size: 0.65rem; letter-spacing: 0.1em;">Courier Service</div>
                                    <div class="gx-fw-bold gx-m-0">
                                        <?= strtoupper($order->shipping_courier ?: 'GeniX Logistic') ?>
                                        - <?= htmlspecialchars($order->shipping_service ?: 'Standard') ?>
                                    </div>
                                    <?php if (!empty($order->shipping_resi)): ?>
                                        <div class="gx-mt-2 gx-p-2 gx-bg-soft gx-text-primary gx-rounded-lg gx-text-xs gx-fw-bold gx-d-inline-flex gx-items-center gx-gap-2 gx-border">
                                            <span class="material-symbols-outlined" style="font-size: 1rem;">label</span>
                                            AWB: <?= htmlspecialchars($order->shipping_resi) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($order->status == 'shipped'): ?>
                            <div class="gx-mt-4 gx-p-3 gx-bg-soft gx-rounded-lg gx-border">
                                <h4 class="gx-text-sm gx-fw-bold gx-text-primary gx-mb-1 gx-m-0">Tracking Update</h4>
                                <p class="gx-text-xs gx-text-muted gx-m-0" style="line-height: 1.5;">Your order is currently in transit. We expect delivery within the timeframe provided by the courier.</p>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
