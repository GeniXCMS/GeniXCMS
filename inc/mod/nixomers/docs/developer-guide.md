# Nixomers Developer & Backend Guide (v2.3.0)

Technical documentation for developers maintaining or extending the Nixomers module.

## 1. Core Architecture
The module follows the GeniXCMS modular pattern, located in `inc/mod/nixomers/`.

### 1.1 Core Business Logic (`lib/Nixomers.class.php`)
The central hub for financial and order management.
- **`calculateNetTrans($orderId)`**: The "Deep Recalculation Engine". It re-scans the `nix_order_items` (cart items), re-applies tax rates, updates the order total, and synchronizes with the `nix_transactions` table to compute the final Net Income.
- **`formatCurrency($amount)`**: Wrapper for consistent currency formatting (IDR, USD, etc.) across all views.

### 1.2 Checkout & Payment Handling (`lib/NixPayment.class.php`)
- **`handleOrderSubmission()`**: Manages the transition from cart to order. Note: As of v2.3.0, granular unit tracking is **deferred** (not automatic) to optimize database load.
- **`syncGranularItems($orderId)`**: Logic to populate the `nix_order_items` table from the order data when explicitly requested by an admin.

### 1.3 Inventory Management (`lib/NixInventory.class.php`)
- **`deduct($productId, $qty, $refId, $notes)`**: Global stock deduction logic. Ensures stock levels are updated regardless of whether granular tracking is active.

## 2. Database Schema

### `nix_orders`
Stores order headers, customer data, and current status.
- Primary Link: `order_id` (varchars, unique).

### `nix_transactions`
Stores financial records mapping to orders.
- Fields: `gross_amount`, `admin_fee`, `tax_amount`, `shipping_amount`, `net_amount`.

### `nix_order_items`
Optional granular tracking table.
- Stores individual units (Serial Numbers, QC Status, Barcodes).
- Relation: `order_id` -> `nix_orders.order_id`.

## 3. Financial Recalculation Flow
When the **Recalculate** action is triggered:
1. System fetches all items belonging to the order.
2. Sums the total (Subtotal).
3. Re-calculates Tax based on current system settings.
4. Re-calculates Gross (Subtotal + Shipping + Tax).
5. Updates `nix_orders.total`.
6. Updates `nix_transactions` with new Gross, Fee, Tax, and Shipping.
7. Computes `Net = Gross - Fee - Tax - Shipping`.

## 4. Admin Integration
- **Options Handling**: Managed in `options.php`.
- **View Routing**: Controlled via the `sel` parameter (e.g., `?page=nixomers&sel=orderdetail`).
- **Security**: Every action (recalculate, update status) MUST validate the `TOKEN` constant.
