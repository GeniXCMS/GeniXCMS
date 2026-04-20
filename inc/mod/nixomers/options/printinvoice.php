<?php
/**
 * Nixomers Order Invoice Printer
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$orderId = Typo::cleanX($_GET['id'] ?? '');
$order = Query::table('nix_orders')->where('order_id', $orderId)->first();

if (!$order) {
    echo '<div style="padding: 20px; font-family: sans-serif; text-align: center;">Order not found.</div>';
    return;
}

$items = json_decode($order->cart_items ?? '[]', true) ?: [];
$currency = Options::v('nixomers_currency') ?: 'IDR';

// Get store info & visibility
$storeName = (Options::v('nix_invoice_show_store_name') !== 'no') ? (Options::v('nix_store_name') ?: Options::v('sitename')) : '';
$storeAddr = (Options::v('nix_invoice_show_store_address') !== 'no') ? (Options::v('nix_store_address') ?: Options::v('siteaddress')) : '';
$storePhone = (Options::v('nix_invoice_show_store_phone') !== 'no') ? (Options::v('nix_store_phone') ?: Options::v('sitephone')) : '';
$storeLogo = (Options::v('nix_invoice_show_logo') !== 'no') ? Options::v('logo') : '';
if (!filter_var($storeLogo, FILTER_VALIDATE_URL) && $storeLogo != "") {
    $storeLogo = Site::$url . $storeLogo;
}

$primaryColor = Options::v('nix_invoice_color') ?: '#1a73e8';
$footerNote = Options::v('nix_invoice_footer_note') ?: 'Thank you for your business!<br>This is a computer-generated invoice and no signature is required.';
$extraInfo = Options::v('nix_invoice_extra_info') ?: '';
$extraType = Options::v('nix_invoice_extra_type') ?: 'info';

$alertColors = [
    'info' => ['bg' => '#e7f3ff', 'border' => '#b3d7ff', 'text' => '#004085'],
    'warning' => ['bg' => '#fff3cd', 'border' => '#ffeeba', 'text' => '#856404'],
    'danger' => ['bg' => '#f8d7da', 'border' => '#f5c6cb', 'text' => '#721c24'],
    'success' => ['bg' => '#d4edda', 'border' => '#c3e6cb', 'text' => '#155724'],
    'light' => ['bg' => '#fefefe', 'border' => '#fdfdfe', 'text' => '#818182'],
];
$activeAlert = $alertColors[$extraType] ?? $alertColors['info'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $order->order_id; ?></title>
    <style>
        /* Force Hide GeniXCMS Admin UI */
        nav, header, .navbar, .sidebar, .footer, .breadcrumb, #sidebar-wrapper, .gx-admin-header { 
            display: none !important; 
        }
        #page-wrapper, .container-fluid, .row { margin: 0 !important; padding: 0 !important; border: none !important; }
        
        @page { size: A4; margin: 0; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 40px; background: #fff; line-height: 1.5; color: #333; }
        
        @media print {
            body { padding: 20px; background: #fff; }
            .no-print { display: none !important; }
            .invoice-container { box-shadow: none !important; border: none !important; margin: 0 !important; width: 100% !important; }
        }

        .invoice-container { 
            background: #fff; 
            max-width: 800px; 
            margin: 0 auto; 
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 20px;
        }
        .store-info h1 { margin: 0; font-size: 24px; font-weight: 900; color: <?php echo $primaryColor; ?>; }
        .store-info p { margin: 5px 0 0 0; font-size: 13px; color: #666; max-width: 300px; }
        
        .invoice-title { text-align: right; }
        .invoice-title h2 { margin: 0; font-size: 28px; font-weight: 900; text-transform: uppercase; color: #333; }
        .invoice-title p { margin: 5px 0 0 0; font-size: 14px; font-weight: 700; color: #888; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .info-block h4 { margin: 0 0 10px 0; font-size: 12px; text-transform: uppercase; color: #888; letter-spacing: 1px; }
        .info-block p { margin: 0; font-size: 14px; font-weight: 600; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #f8f9fa; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; color: #666; border-bottom: 2px solid #dee2e6; }
        .items-table td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }

        .totals-wrapper { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; }
        .extra-info-box { 
            flex: 1; 
            padding: 15px; 
            border-radius: 8px; 
            font-size: 13px; 
            border: 1px solid <?php echo $activeAlert['border']; ?>;
            background: <?php echo $activeAlert['bg']; ?>;
            color: <?php echo $activeAlert['text']; ?>;
        }
        .totals-section { width: 300px; }
        .totals-table { width: 100%; }
        .totals-table td { padding: 8px 0; font-size: 14px; }
        .totals-table .grand-total { border-top: 2px solid #333; padding-top: 15px; margin-top: 10px; font-size: 18px; font-weight: 900; color: <?php echo $primaryColor; ?>; }
        
        .footer-note { margin-top: 60px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        .status-stamp {
            display: inline-block;
            padding: 5px 15px;
            border: 3px solid;
            border-radius: 8px;
            font-weight: 900;
            text-transform: uppercase;
            transform: rotate(-10deg);
            margin-top: 10px;
        }
        .status-paid { border-color: #28a745; color: #28a745; opacity: 0.6; }
        .status-unpaid { border-color: #dc3545; color: #dc3545; opacity: 0.6; }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center; margin-bottom: 30px;">
    <button onclick="window.print()" style="padding: 12px 25px; cursor: pointer; background: #2563eb; color: #fff; border: none; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 10px rgba(37,99,235,0.2);">PRINT INVOICE</button>
    <button onclick="window.close()" style="padding: 12px 25px; cursor: pointer; background: #fff; color: #333; border: 1px solid #ddd; font-weight: 700; border-radius: 8px; margin-left: 10px;">CLOSE</button>
</div>

<div class="invoice-container">
    <div class="header-section">
        <div class="store-info">
            <?php if ($storeLogo): ?>
                <img src="<?php echo $storeLogo; ?>" style="max-height: 60px; margin-bottom: 10px;">
            <?php endif; ?>
            <?php if ($storeName): ?>
                <h1><?php echo $storeName; ?></h1>
            <?php endif; ?>
            <?php if ($storeAddr): ?>
                <p><?php echo nl2br($storeAddr); ?></p>
            <?php endif; ?>
            <?php if ($storePhone): ?>
                <p>Telp: <?php echo $storePhone; ?></p>
            <?php endif; ?>
        </div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <p><?php echo $order->order_id; ?></p>
            <div class="status-stamp <?php echo (in_array($order->status, ['paid', 'completed', 'delivered', 'shipped']) ? 'status-paid' : 'status-unpaid'); ?>">
                <?php echo (in_array($order->status, ['paid', 'completed', 'delivered', 'shipped']) ? 'PAID' : 'UNPAID'); ?>
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-block">
            <h4>Billed To:</h4>
            <p><?php echo $order->customer_name; ?></p>
            <p style="font-weight: 400; color: #666; font-size: 13px;">
                <?php echo $order->customer_email; ?><br>
                <?php echo $order->customer_phone; ?>
            </p>
        </div>
        <div class="info-block" style="text-align: right;">
            <h4>Order Date:</h4>
            <p><?php echo date('d F Y', strtotime($order->date)); ?></p>
            <h4 style="margin-top: 15px;">Payment Method:</h4>
            <p><?php echo strtoupper($order->payment_method ?: 'Manual Transfer'); ?></p>
        </div>
    </div>

    <div class="info-block" style="margin-bottom: 40px;">
        <h4>Shipping Address:</h4>
        <p style="font-weight: 400; font-size: 13px; color: #444;">
            <?php echo nl2br($order->shipping_address); ?>
        </p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($items as $pId => $qty): 
                $pPrice = (float) Posts::getParam('price', $pId) ?: 0;
                $pTotal = $pPrice * (int)$qty;
            ?>
            <tr>
                <td>
                    <div style="font-weight: 700;"><?php echo Posts::title($pId); ?></div>
                    <div style="font-size: 11px; color: #888;">SKU: <?php echo Posts::getParam('sku', $pId) ?: '-'; ?></div>
                </td>
                <td class="text-center"><?php echo $qty; ?></td>
                <td class="text-right"><?php echo $currency; ?> <?php echo number_format($pPrice, 0, ',', '.'); ?></td>
                <td class="text-right" style="font-weight: 700;"><?php echo $currency; ?> <?php echo number_format($pTotal, 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals-wrapper">
        <div class="extra-info-box" style="<?php echo (empty($extraInfo) ? 'visibility: hidden;' : ''); ?>">
            <div style="font-weight: 700; margin-bottom: 5px; text-transform: uppercase; font-size: 11px; opacity: 0.8;">Important Information:</div>
            <?php echo nl2br($extraInfo); ?>
        </div>
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td style="color: #888;">Subtotal</td>
                    <td class="text-right" style="font-weight: 600;"><?php echo $currency; ?> <?php echo number_format((float)$order->subtotal, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td style="color: #888;">Tax (PPN)</td>
                    <td class="text-right" style="font-weight: 600;"><?php echo $currency; ?> <?php echo number_format((float)$order->tax, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td style="color: #888;">Shipping (<?php echo strtoupper($order->shipping_courier); ?>)</td>
                    <td class="text-right" style="font-weight: 600;"><?php echo $currency; ?> <?php echo number_format((float)$order->shipping_cost, 0, ',', '.'); ?></td>
                </tr>
                <tr class="grand-total">
                    <td>Total Amount</td>
                    <td class="text-right"><?php echo $currency; ?> <?php echo number_format((float)$order->total, 0, ',', '.'); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer-note">
        <?php echo nl2br($footerNote); ?>
    </div>
</div>

<div style="text-align: center; margin-top: 30px; font-size: 11px; color: #bbb;">
    &copy; <?php echo date('Y'); ?> <?php echo $storeName; ?>. All rights reserved.
</div>

</body>
</html>
