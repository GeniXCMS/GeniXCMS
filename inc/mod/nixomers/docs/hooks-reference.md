# Nixomers Hooks & Filters Reference (v2.3.0)

This document provides a complete list of hooks available in the Nixomers module for integration and extension.

## 1. Action Hooks
Actions allow you to trigger custom code when specific events occur.

| Hook Name | Parameters | Description |
| :--- | :--- | :--- |
| `nix_order_submitted` | `string $order_id, array $data` | Triggered when a new order is submitted (frontend or manual). |
| `nixomers_order_status_updated` | `array $args` | Triggered when an order status is updated. Contains `order_id` and `status`. |
| `nixomers_payment_updated` | `array $args` | Triggered when payment details are manually updated in admin. Contains `order_id` and `transaction` (data array). |
| `nixomers_order_item_updated` | `array $args` | Triggered when a granular item (unit tracking) is updated. Contains `item_id`, `order_id`, and `data`. |
| `nixomers_recalculate_after` | `string $order_id` | Triggered after a financial recalculation is completed. |
| `nixomers_orderdetail_header_buttons` | `array $buttons, object $order` | Filter to add or modify buttons in the header of order detail page. |
| `nixomers_orderdetail_management_actions` | `string $html, object $order` | Filter to add custom action buttons/links in Order Management card. |
| `nixomers_orders_extra_footer` | `string $html, string $q, string $status, string $sort` | Filter to add extra content below the orders table in order management page. |
| `nix_payment_process` | `object $order` | Triggered when any payment method is submitted. |
| `nix_payment_process_{method}` | `object $order` | Triggered for a specific payment method (e.g., `nix_payment_process_paypal`). |
| `nix_ajax_api_{action}` | `none` | Triggered via `index.php?ajax=api&action={action}`. |
| `nix_notification_save` | `none` | Triggered when notification settings are saved in admin. |
| `nix_payment_gateways_save` | `none` | Triggered when payment gateway settings are saved in admin. |
| `nixomers_activate` | `none` | Triggered when the Nixomers module is activated. |

## 2. Filter Hooks
Filters allow you to intercept and modify data before it is processed or displayed.

| Hook Name | Parameters | Description |
| :--- | :--- | :--- |
| `nixomers_shipping_cost_filter` | `float $cost`, `array $args` | Modify the shipping cost before order creation. |
| `nixomers_admin_fee_filter` | `float $fee`, `string $order_id` | Modify or add extra administrative/service fees. |
| `nix_payment_gateways` | `array $gateways` | Register new payment methods into the checkout system. |
| `nix_admin_menu_children` | `array $menu` | Add custom sub-menus to the Nixomers admin sidebar. |
| `nix_payment_gateways_list` | `array $list` | Filter the enabled gateways before displaying them to the user. |

## 3. UI & Settings Hooks (Filters)
Used to inject HTML components into the Nixomers Admin Dashboard.

| Hook Name | Type | Description |
| :--- | :--- | :--- |
| `nix_gateway_settings_body_{id}` | `Filter` | Return HTML string for a gateway's settings form. |
| `nix_notification_settings_email_{id}` | `Filter` | Return HTML string for email service settings. |
| `nix_notification_settings_wa_{id}` | `Filter` | Return HTML string for WhatsApp service settings. |
| `nix_payment_show_{id}` | `Filter` | Return HTML string for payment instructions on the frontend. |

## 4. Implementation Snippet
How to use a filter to add a custom service charge:

```php
// In your support module
Hooks::filter('nixomers_admin_fee_filter', function($fee, $orderId) {
    // Add a flat $1.50 "Tech Fee"
    return $fee + 1.50;
});
```
