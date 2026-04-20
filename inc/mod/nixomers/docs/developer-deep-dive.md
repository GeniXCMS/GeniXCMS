# Nixomers Developer Deep Dive & API (v2.3.0)

A comprehensive technical guide for developers building on top of the Nixomers ecosystem.

## 1. Data Integrity & Financial Engines

### 1.1 The Recalculation Engine (`Nixomers::calculateNetTrans`)
This is the core "Source of Truth" for the entire financial module.

- **Atomic Operations**: The engine performs several database writes in a single execution to ensure consistency between `nix_orders` and `nix_transactions`.
- **Logic Sequence**:

  1. **Fetch Cart Data**: Raw items and prices are retrieved from `nix_order_items`.
  2. **Calculate Tax**: Uses the system variable `tax_rate`.
  3. **Compare with Transaction**: If `nix_transactions` is empty, it initializes the record from order data.
  4. **Final Sync**: The `net_income` is physically stored in the database, not just calculated on the fly, for fast reporting.

### 1.2 Inventory Logic Handling
Inventory is managed via the `NixInventory` class.

- **Global Deduction**: Happens at the moment of checkout (`NixPayment::handleOrderSubmission`).
- **Granular Restoration**: If an order item is deleted, the stock is automatically restored via a trigger-like hook in the `update_order_item` handler.

## 2. Hooks and Filters: Real-World Case Studies

### 2.1 Case Study: WhatsApp Order Notifications
**Goal**: Send an automated message whenever a new order is submitted or its status changes.
**Hook**: `nix_order_submitted` or `nixomers_order_status_updated`

```php
// New Order Notification
Hooks::add('nix_order_submitted', function($orderId, $orderData) {
    $message = "Halo Admin, ada pesanan baru #{$orderId} dari {$orderData['customer_name']}!";
    // Call your notification logic here
});

// Order Status Update Notification
Hooks::add('nixomers_order_status_updated', function($args) {
    $orderId = $args['order_id'];
    $newStatus = $args['status'];
    $orderData = Nixomers::getOrderByID($orderId);
    
    $message = "Halo {$orderData->name}, pesanan Anda #{$orderId} sekarang berstatus: {$newStatus}.";
    
    // Call your WA API here
    MyWaModule::send($orderData->phone, $message);
});
```

### 2.2 Case Study: Dynamic Admin Fees (Packing Costs)
**Goal**: Add a 5% "Safe Packing" fee to orders over $500.
**Hook**: `nixomers_admin_fee_filter`

```php
// In your custom fee module
Hooks::filter('nixomers_admin_fee_filter', function($currentFee, $orderId) {
    $subtotal = Nixomers::getOrderSubtotal($orderId);
    
    if ($subtotal > 500) {
        // Add 5% extra fee to existing fee
        $extra = $subtotal * 0.05;
        return $currentFee + $extra;
    }
    
    return $currentFee;
});
```

## 3. Data Sync Snippets

### 3.1 Triggering a Manual Financial Recalculation
If you build a module that modifies cart items directly, you must trigger a re-sync:

```php
// After modifying items
$orderId = 'NX-12345';
Nixomers::calculateNetTrans($orderId); // Atomically updates orders and transactions
```

## 4. Advanced Database Relations
To join Nixomers orders with fulfillment data for a custom dashboard:

```sql
SELECT o.order_id, o.name, t.net_amount, f.tracking_number
FROM nix_orders o
LEFT JOIN nix_transactions t ON o.order_id = t.order_id
LEFT JOIN nix_fulfillment f ON o.order_id = f.order_id
WHERE o.status = 'paid';
```
