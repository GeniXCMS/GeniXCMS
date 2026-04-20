## 1. Template Override Mastery
Nixomers implements a "Theme-First" rendering strategy. Before rendering any standard frontend view, the module checks your active theme for a specific file prefix.

### 1.1 The `nixomers-` Prefix Convention
To override a module view, you must place a file in your theme's **root directory** (not a subfolder) using the following naming pattern:
`{theme_dir}/nixomers-{view_name}.latte`

### 1.2 Override Mapping Table
| View Name | File to Create in Theme | Impact |
| :--- | :--- | :--- |
| `catalog` | `nixomers-catalog.latte` | Controls the main store/product listing page. |
| `cart` | `nixomers-cart.latte` | Full control over the shopping cart UI. |
| `checkout` | `nixomers-checkout.latte` | Redesign the shipping and gateway selection forms. |
| `payment` | `nixomers-payment.latte` | Customize the payment instruction and proof upload area. |

### 1.3 How to Access Variables
When overriding, you have access to the same data context as the original module. Common variables include:
- `{$site_url}`: Base URL of the site.
- `{$currency}`: Active currency code (e.g., IDR).
- `{$token}`: Required security token for POST forms.
- `{$cart_items}`: Array of products in the current session.

## 2. Practical Case Study: Floating Financial Sidebar
Templates have access to the `$transaction` object:
```latte
<div class="financials">
    <span>Gross: {$transaction->gross_amount|formatCurrency}</span>
    <span>Net: {$transaction->net_income|formatCurrency}</span>
</div>
```
*Note: The `formatCurrency` filter is automatically registered by the Nixomers module.*

## 2. Practical Case Study: Floating Financial Sidebar

**Goal**: Move the financial breakdown to a floating sticky sidebar on large screens while keeping it standard on mobile.

### Step 1: Template Override
Create `mod/nixomers/orderdetail.latte` in your theme:

```latte
<div class="row">
    <!-- Main Order Details -->
    <div class="col-lg-8">
        {include 'order-main-content.latte'}
    </div>
    
    <!-- Floating Sidebar -->
    <div class="col-lg-4">
        <div class="sticky-top" style="top: 80px;">
            <div class="gx-glass p-4 rounded-4 shadow-lg border-white">
                <h4 class="fw-bold mb-3">Ringkasan Pembayaran</h4>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span class="fw-bold">{$transaction->subtotal|formatCurrency}</span>
                </div>
                <hr>
                <div class="h2 text-primary fw-bold">
                    {$transaction->gross_amount|formatCurrency}
                </div>
            </div>
        </div>
    </div>
</div>
```

### Step 2: Custom Design Tokens (CSS)
In your theme's `style.css`:

```css
/* Custom Nixomers Theme Tokens */
.gx-glass {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.4);
}

/* Make totals pop */
.h2.text-primary.fw-bold {
    letter-spacing: -0.05em;
    text-shadow: 0 10px 20px rgba(71, 136, 199, 0.2);
}
```

## 3. Best Practices for Theme Devs
1. **Never Modify Core CSS**: Always use your theme's `style.css` to override Nixomers components.
2. **Handle Empty States**: Your theme should provide a clear "No orders found" UI.
   ```latte
   {if empty($orders)}
       <div class="alert alert-light text-center p-5">
            <i class="bi bi-cart-x display-1"></i>
            <p>Belum ada pesanan yang masuk.</p>
       </div>
   {/if}
   ```
