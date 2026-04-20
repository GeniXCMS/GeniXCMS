<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * NixomersProfile Class
 *
 * Handles the integration of Nixomers features into the User Profile system.
 */
class NixomersProfile
{
    /**
     * Renders the Purchase History tab in User Profile.
     *
     * @param string $username Profile owner's username
     * @param object $user     Full user object
     * @return string          HTML content
     */
    public static function purchaseHistory(string $username, object $user): string
    {
        $email = $user->email ?? '';
        if (empty($email)) {
            return '<div class="p-8 text-center text-on-surface-variant bg-surface-container rounded-xl">No valid email found for this user.</div>';
        }

        try {
            // Get orders for this user
            $orders = Query::table('nix_orders')
                ->where('customer_email', $email)
                ->orderBy('date', 'DESC')
                ->get();

            if (empty($orders)) {
                return '<div class="p-12 text-center bg-surface-container-lowest rounded-2xl border-2 border-dashed border-surface-container">
                    <span class="material-symbols-outlined !text-6xl text-surface-container-high mb-4">shopping_cart_off</span>
                    <h3 class="text-xl font-bold text-on-surface">No orders found</h3>
                    <p class="text-on-surface-variant mt-2">You haven\'t started your harvest collection yet.</p>
                    <a href="' . Site::$url . '" class="inline-block mt-8 px-8 py-3 bg-primary text-on-primary rounded-full font-bold shadow-sm">Start Shopping</a>
                </div>';
            }

            $currency = Options::v('nixomers_currency') ?: 'IDR';
            $html = '<div class="gx-d-flex gx-flex-column gx-gap-3">';

            // Header for the list
            $html .= '<div class="gx-d-none gx-d-block-md gx-row gx-px-3 gx-py-2 gx-text-xs gx-fw-black gx-text-muted gx-text-uppercase gx-border-bottom">
                <div class="gx-col-4">Order / ID</div>
                <div class="gx-col-2 gx-text-center">Status</div>
                <div class="gx-col-3 gx-text-center">Date</div>
                <div class="gx-col-3 gx-text-right">Total</div>
            </div>';

            foreach ($orders as $order) {
                $statusColor = 'success'; 
                $statusValue = (string) ($order->status ?? 'pending');
                $statusText  = strtoupper($statusValue);
                
                if (in_array($statusValue, ['pending', 'unpaid', 'waiting'])) {
                    $statusColor = 'warning';
                } elseif (in_array($statusValue, ['cancelled', 'failed', 'refunded'])) {
                    $statusColor = 'danger';
                }

                $orderIdent = ($order->order_id ?? $order->id);
                $detailUrl  = NixomersUrl::orderDetail($orderIdent);

                $html .= '<div class="gx-card gx-rounded gx-p-3 gx-row gx-items-center gx-transition hover-shadow-sm">
                    <div class="gx-col-12 gx-md-col-4 gx-d-flex gx-items-center gx-gap-3">
                        <div class="gx-d-flex gx-items-center gx-justify-center gx-bg-soft gx-rounded" style="width:48px; height:48px; flex-shrink:0;">
                             <span class="material-symbols-outlined gx-text-primary">receipt_long</span>
                        </div>
                        <div>
                            <div class="gx-fw-black gx-text-dark gx-text-lg">#' . htmlspecialchars($order->order_id ?? $order->id) . '</div>
                            <div class="gx-text-xs gx-text-muted gx-fw-bold gx-text-uppercase opacity-70">' . count(json_decode($order->cart_items ?? '[]', true)) . ' Items</div>
                        </div>
                    </div>
                    
                    <div class="gx-col-6 gx-md-col-2 gx-text-center">
                        <span class="gx-badge gx-badge-' . $statusColor . '">
                            ' . $statusText . '
                        </span>
                    </div>

                    <div class="gx-col-6 gx-md-col-3 gx-text-center">
                        <div class="gx-text-sm gx-fw-bold gx-text-dark">' . Date::format($order->date, 'd M Y') . '</div>
                        <div class="gx-text-xs gx-text-muted">' . Date::format($order->date, 'H:i') . ' WIB</div>
                    </div>

                    <div class="gx-col-12 gx-md-col-3 gx-text-right">
                        <div class="gx-text-xl gx-fw-black gx-text-primary gx-mb-2">' . $currency . ' ' . number_format($order->total, 0, ',', '.') . '</div>
                        ';
                if ($statusValue === 'pending') {
                    $btnUrl = NixomersUrl::payment($orderIdent);
                    $btnLabel = 'Track/Pay';
                    $btnClass = 'gx-btn-primary';
                } else {
                    $btnUrl = $detailUrl;
                    $btnLabel = 'Details';
                    $btnClass = 'gx-btn-secondary';
                }
                $html .= '<a href="' . $btnUrl . '" class="gx-btn ' . $btnClass . ' gx-rounded-full gx-text-xs gx-fw-black">' . $btnLabel . '</a>
                    </div>
                </div>';
            }

            $html .= '</div>';
            return $html;

        } catch (Exception $e) {
            return '<div class="p-8 text-center text-error bg-error/10 border border-error/20 rounded-xl">Error loading purchase history.</div>';
        }
    }
}
