# Nixomers Ecosystem Development Guide (v2.3.0)

This guide provides a blueprint for creating external modules that extend or support the Nixomers e-commerce suite.

## 1. Creating a Notification Module (WhatsApp/Email)
Notification modules listen for state changes in Nixomers and trigger external APIs.

### 1.1 Hook: `nixomers_order_status_updated`
This hook is triggered every time an order status changes (Pending -> Paid -> Processing, etc.).

**Implementation Example:**
```php
// inc/mod/my_wa_notifier/index.php
Hooks::add('nixomers_order_status_updated', function($args) {
    $orderId = $args['order_id'];
    $newStatus = $args['status'];
    
    // Fetch order details for the customer name and phone
    $order = Nixomers::getOrderByID($orderId);
    
    if ($newStatus === 'paid') {
        $message = "Terima kasih {$order->customer_name}, pembayaran pesanan #{$orderId} telah kami terima.";
        MyWaAPI::send($order->customer_phone, $message);
    }
});
```

## 2. Creating a Payment Gateway Module
You can add new payment methods (e.g., Midtrans, Stripe, or local Bank) to Nixomers.

### 2.1 Registering the Gateway
Use the `nix_payment_gateways` filter to add your method to the checkout list.

```php
// inc/mod/my_gateway/index.php
Hooks::filter('nix_payment_gateways', function($gateways) {
    $gateways['my_custom_bank'] = [
        'name' => 'My Custom Bank',
        'icon' => 'bi bi-credit-card-2-front'
    ];
    return $gateways;
});
```

### 2.2 Displaying Payment Instructions
Nixomers looks for a hook named `nix_payment_show_{gateway_id}` to render instructions on the confirmation page.

```php
Hooks::attach('nix_payment_show_my_custom_bank', function($order) {
    // You can use UiBuilder or raw HTML
    return '<div class="alert alert-primary">Silahkan transfer ke virtual account: 889912344</div>';
});
```

### 2.3 Adding Settings to Nixomers
Register your gateway settings into the Nixomers settings panel.

```php
Hooks::attach('nix_gateway_settings_body_my_custom_bank', function() {
    $ui = new UiBuilder();
    return $ui->renderElement([
        'type' => 'input',
        'name' => 'my_bank_api_key',
        'label' => 'API Key',
        'value' => Options::v('my_bank_api_key')
    ], true);
});
```

## 3. Creating Support Modules (Fees & Shipping)

### 3.1 Dynamic Admin Fees
Use `nixomers_admin_fee_filter` to inject extra charges (Packaging, Insurance, Service Fee).

```php
Hooks::filter('nixomers_admin_fee_filter', function($fee, $orderId) {
    // Logic: Add $2 if order contains fragile items
    if (MyFragileMod::hasFragile($orderId)) {
        return $fee + 2;
    }
    return $fee;
});
```

### 3.2 Dynamic Shipping Rates
Inject rates from your own shipping aggregator module.

```php
Hooks::filter('nixomers_shipping_cost_filter', function($cost, $args) {
    // $args contains destination, weight, etc.
    $newRate = MyExpeditionMod::getRates($args['city'], $args['weight']);
    return $newRate;
});
```

## 4. Best Practices for Support Modules
1. **Dependency Check**: Always check if Nixomers is active before running your logic.
   ```php
   if (!array_key_exists('nixomers', Module::$modules)) return;
   ```
2. **Namespace Classes**: Use clear naming conventions like `NixMyGateway` to avoid class collisions.
3. **Use nix_action**: When handling callbacks, use the centralized AJAX API endpoint: `index.php?ajax=api&action=my_callback`.
