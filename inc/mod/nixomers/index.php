<?php
/**
 * Name: Nixomers
 * Desc: A amazing powerfull E-Commerce Module
 * Version: 1.0.0
 * Build: 2.3.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-cart4
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Nixomers Module Entry Point
 * Implements Autoloading for core libraries and initializes the module.
 */

// Register Autoloader for Nixomers Classes
spl_autoload_register(function ($class) {
    // Standard Nixomers Class Naming: ClassName.class.php
    $file = __DIR__ . '/lib/' . $class . '.class.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load Composer Autoloader if exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Add CSP exception for Region APIs (Centralized in module)
Hooks::attach('system_security_headers_args', function ($args) {
    $rules = $args[0] ?? [];
    if (isset($rules['connect-src'])) {
        $rules['connect-src'][] = 'https://countriesnow.space';
        $rules['connect-src'][] = 'https://use.api.co.id';
        $rules['connect-src'][] = 'https://tdev.kiriminaja.com';
        $rules['connect-src'][] = 'https://api.kiriminaja.com';
    }
    return $rules;
});

// Register Standard Payment Gateways (Bank Transfer, PayPal, etc.)
Hooks::attach('nix_payment_gateways', function ($args) {
    $gateways = is_array($args) && isset($args[0]) ? $args[0] : (is_array($args) ? $args : []);

    $gateways['bank_transfer'] = [
        'name' => 'Bank Transfer (Manual)',
        'icon' => 'bi bi-bank'
    ];
    $gateways['paypal'] = [
        'name' => 'PayPal / Credit Card',
        'icon' => 'bi bi-paypal'
    ];

    return $gateways;
});

// Attach Order History to User Profile Overview
Hooks::attach('user_profile_overview_extra', function ($args) {
    if (!is_array($args))
        return $args;
    $html = $args[0] ?? '';
    $context = $args[1] ?? [];

    $user = $context['user'] ?? null;
    $email = $user->email ?? '';

    if (empty($email)) {
        return $args;
    }

    try {
        $orders = Query::table('nix_orders')
            ->where('customer_email', $email)
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        if (!empty($orders)) {
            $currency = Options::v('nixomers_currency') ?: 'IDR';

            $html .= '<section class="gx-card gx-mb-4">
                <div class="gx-d-flex gx-items-center gx-justify-between gx-mb-4">
                    <div>
                        <h2 class="gx-h4 gx-mb-1">Recent Orders</h2>
                        <p class="gx-text-muted gx-text-sm">Track your seasonal harvests and curated deliveries.</p>
                    </div>
                </div>
                <div class="gx-list-group gx-list-group-flush">';

            foreach ($orders as $order) {
                // Determine color based on status
                $statusColor = 'success'; // default green logic
                $statusValue = (string) ($order->status ?? 'unknown');
                $statusText = ucfirst($statusValue);
                if (in_array($statusValue, ['pending', 'unpaid'])) {
                    $statusColor = 'warning';
                } elseif (in_array($statusValue, ['cancelled', 'failed'])) {
                    $statusColor = 'danger';
                }

                $html .= '<div class="gx-list-group-item gx-p-3 gx-d-flex gx-flex-column gx-flex-row-md gx-items-center gx-gap-3">
                    <div class="gx-d-flex gx-items-center gx-justify-center gx-bg-soft gx-rounded" style="width:60px; height:60px; flex-shrink:0;">
                         <span class="material-symbols-outlined gx-text-muted" style="font-size:2rem;">inventory_2</span>
                    </div>
                    <div style="flex-grow:1;">
                        <div class="gx-d-flex gx-justify-between gx-items-start gx-mb-1">
                            <h3 class="gx-fw-bold gx-text-md gx-m-0">Order ' . htmlspecialchars($order->order_id ?? $order->id) . '</h3>
                            <span class="gx-text-primary gx-fw-bold">' . $currency . ' ' . number_format($order->total, 0, ',', '.') . '</span>
                        </div>
                        <div class="gx-text-xs gx-fw-bold gx-text-muted gx-text-uppercase gx-mb-2">
                            ' . Date::format($order->date, 'M d, Y') . '
                        </div>
                        <div class="gx-d-flex gx-items-center gx-gap-2">
                             <div class="gx-badge gx-badge-' . $statusColor . '">' . $statusText . '</div>
                        </div>
                    </div>
                    <div>';
                $orderIdent = ($order->order_id ?? $order->id);
                if ($statusValue === 'pending') {
                    $actionUrl = NixomersUrl::payment($orderIdent);
                    $btnLabel = 'Track/Pay';
                    $btnClass = 'gx-btn-primary';
                } else {
                    $actionUrl = NixomersUrl::orderDetail($orderIdent);
                    $btnLabel = 'Details';
                    $btnClass = 'gx-btn-secondary';
                }

                $html .= '<a href="' . $actionUrl . '" class="gx-btn ' . $btnClass . ' gx-rounded-full gx-text-xs">' . $btnLabel . '</a>
                    </div>
                </div>';
            }

            $html .= '</div></section>';
        }
    } catch (Exception $e) {
        // Table probably not ready yet
    }

    return $html;
});


// Initialize the core module if the class exists
if (class_exists('Nixomers')) {
    new Nixomers();

    // Add to mod list for routing
    Mod::addMenuList([
        'purchase_detail' => [
            'label' => 'Order Detail',
            'show_title' => false
        ]
    ]);

    // Add clean URL routing for order detail
    Router::add(['order/detail/(.*)' . GX_URL_PREFIX => ['mod' => 'purchase_detail', 'order_id' => 1]]);

    // Handle Frontend Styling (GeniXCMS Core CSS)
    Asset::enqueue('genixcms-css');

    // Register User Profile Section & Dropdown for Nixomers (Purchase History)
    Hooks::attach('init', function () {
        if (class_exists('UserProfile')) {
            // Register dedicated profile section
            UserProfile::registerSection('purchase', [
                'label' => 'Purchase History',
                'icon' => 'shopping_bag',
                'callback' => 'NixomersProfile::purchaseHistory',
                'min_group' => 6,   // General Members
                'own_only' => true, // Only own history
                'order' => 15
            ]);

            // Register quick access in header dropdown
            UserProfile::registerDropdownItem('purchase', [
                'label' => 'Purchase History',
                'url' => Url::user(Session::val('username'), 'purchase'),
                'icon' => 'shopping_bag',
                'order' => 15
            ]);
        }
    });

    // Handle Frontend Rendering for Nixomers Actions
    Hooks::attach('mod_control', function ($args) {
        $data = $args[0];
        $mod = $data['mod'] ?? '';

        // Handle Purchase Detail Page
        if ($mod == 'purchase_detail') {
            return NixOrderView::render($data);
        }

        return;
    });
} else {
    // Fallback error reporting
    Hooks::attach('init', function () {
        if (class_exists('AdminMenu')) {
            AdminMenu::add([
                'id' => 'nixomers_error',
                'label' => 'Nixomers Init Error',
                'icon' => 'bi bi-exclamation-triangle',
                'url' => '#',
                'access' => 1
            ]);
        }
    });
}