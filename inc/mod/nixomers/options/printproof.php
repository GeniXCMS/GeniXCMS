<?php
/**
 * Nixomers Transaction Proof Printer
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$id = Typo::int($_GET['id'] ?? 0);
$transaction = Query::table('nix_transactions')->where('id', $id)->first();

if (!$transaction) {
    echo '<div style="padding: 20px; font-family: sans-serif; text-align: center;">Transaction record not found.</div>';
    return;
}

$order = Query::table('nix_orders')->where('order_id', $transaction->order_id)->first();
$currency = Options::v('nixomers_currency') ?: 'IDR';

// Get store info
$storeName = Options::v('nix_store_name') ?: Options::v('sitename');
$storeAddr = Options::v('nix_store_address') ?: Options::v('siteaddress');
$storeLogo = Options::v('logo');
if (!filter_var($storeLogo, FILTER_VALIDATE_URL) && $storeLogo != "") {
    $storeLogo = Site::$url . $storeLogo;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payment Proof - TX#<?php echo $transaction->id; ?></title>
    <style>
        /* Force Hide GeniXCMS Admin UI */
        nav, header, .navbar, .sidebar, .footer, .breadcrumb, #sidebar-wrapper, .gx-admin-header { 
            display: none !important; 
        }
        #page-wrapper, .container-fluid, .row { margin: 0 !important; padding: 0 !important; border: none !important; }
        
        @page { size: auto; margin: 0; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 40px; background: #f0f2f5; line-height: 1.5; color: #333; }
        
        @media print {
            body { padding: 0; background: #fff; }
            .no-print { display: none !important; }
            .proof-container { box-shadow: none !important; border: 1px solid #eee !important; margin: 0 !important; max-width: 100% !important; }
        }

        .proof-container { 
            background: #fff; 
            max-width: 700px; 
            margin: 0 auto; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            border-radius: 12px;
            overflow: hidden;
        }
        .header-top {
            background: #f8f9fa;
            padding: 15px 30px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-badge {
            background: #e6f7ed;
            color: #1a8a4d;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content-body { padding: 40px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; }
        .info-label { font-size: 11px; color: #888; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; }
        .info-val { font-size: 15px; font-weight: 600; color: #111; }
        
        .divider { height: 1px; background: #eee; margin: 30px 0; }
        
        .amount-table { width: 100%; border-collapse: collapse; }
        .amount-table td { padding: 6px 0; font-size: 14px; }
        .amount-table .total-row td { 
            border-top: 2px solid #333; 
            padding-top: 15px; 
            margin-top: 10px;
            font-size: 20px; 
            font-weight: 900; 
            color: #000;
        }
        
        .footer-note { padding: 30px; text-align: center; color: #999; font-size: 12px; border-top: 1px dashed #eee; }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center; margin-bottom: 30px;">
    <button onclick="window.print()" style="padding: 12px 25px; cursor: pointer; background: #2563eb; color: #fff; border: none; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 10px rgba(37,99,235,0.2);">PRINT RECEIPT</button>
    <button onclick="window.close()" style="padding: 12px 25px; cursor: pointer; background: #fff; color: #333; border: 1px solid #ddd; font-weight: 700; border-radius: 8px; margin-left: 10px;">CLOSE</button>
</div>

<div class="proof-container">
    <div class="header-top">
        <div>
            <?php if ($storeLogo): ?>
                <img src="<?php echo $storeLogo; ?>" style="max-height: 50px; margin-bottom: 5px;">
            <?php endif; ?>
            <div style="font-size: 20px; font-weight: 900;"><?php echo $storeName; ?></div>
            <div style="font-size: 12px; color: #666;"><?php echo $storeAddr; ?></div>
        </div>
        <div class="status-badge"><?php echo $transaction->status ?: 'COMPLETED'; ?></div>
    </div>
    
    <div class="content-body">
        <h3 style="margin: 0 0 30px 0; font-weight: 900; letter-spacing: -0.5px; font-size: 24px;">Proof of Payment</h3>
        
        <div class="info-grid">
            <div>
                <div class="info-label">Transaction Reference</div>
                <div class="info-val">#TX-<?php echo str_pad($transaction->id, 5, '0', STR_PAD_LEFT); ?></div>
            </div>
            <div>
                <div class="info-label">Payment Date</div>
                <div class="info-val"><?php echo date('d F Y, H:i', strtotime($transaction->date)); ?></div>
            </div>
            <div>
                <div class="info-label">Payment Method</div>
                <div class="info-val"><?php echo strtoupper($transaction->method ?: 'Manual Transfer'); ?></div>
            </div>
            <div>
                <div class="info-label">Related Invoice</div>
                <div class="info-val"><?php echo ($order->order_id ?? 'N/A'); ?></div>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div style="margin-bottom: 30px;">
            <div class="info-label">Customer Details</div>
            <div class="info-val"><?php echo ($order->customer_name ?? 'Walk-in Customer'); ?></div>
            <div style="font-size: 13px; color: #666;"><?php echo ($order->customer_phone ?? ''); ?></div>
        </div>

        <table class="amount-table">
            <tr>
                <td class="text-muted">Payment Amount (Gross)</td>
                <td style="text-align: right; font-weight: bold;"><?php echo $currency; ?> <?php echo number_format((float)$transaction->amount, 2); ?></td>
            </tr>
            <tr>
                <td class="text-muted">Transaction Fee</td>
                <td style="text-align: right; color: #d32f2f;">- <?php echo $currency; ?> <?php echo number_format((float)$transaction->fee, 2); ?></td>
            </tr>
            <tr>
                <td class="text-muted">Tax / Service Charge</td>
                <td style="text-align: right; color: #d32f2f;">- <?php echo $currency; ?> <?php echo number_format((float)$transaction->tax, 2); ?></td>
            </tr>
            <tr>
                <td class="text-muted">Shipping Allocation</td>
                <td style="text-align: right; color: #d32f2f;">- <?php echo $currency; ?> <?php echo number_format((float)$transaction->shipping_cost, 2); ?></td>
            </tr>
            <tr class="total-row">
                <td>Net Settlement Total</td>
                <td style="text-align: right;"><?php echo $currency; ?> <?php echo number_format((float)$transaction->net, 2); ?></td>
            </tr>
        </table>
    </div>
    
    <div class="footer-note">
        This is a computer-generated proof of payment through Nixomers Merchant Suite.<br>
        Transaction Log Hash: <?php echo md5($transaction->id . $transaction->date . $transaction->amount); ?>
    </div>
</div>

<div style="text-align: center; margin-top: 30px; font-size: 11px; color: #bbb;">
    &copy; <?php echo date('Y'); ?> <?php echo $storeName; ?>. All rights reserved.
</div>

</body>
</html>
