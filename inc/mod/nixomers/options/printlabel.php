<?php
/**
 * Nixomers Shipping Label Printer
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$orderId = Typo::cleanX($_GET['id'] ?? '');
$order = Query::table('nix_orders')->where('order_id', $orderId)->first();

if (!$order) {
    echo '<div style="padding: 20px; font-family: sans-serif; text-align: center;">Order not found.</div>';
    return;
}

$items = json_decode($order->cart_items, true) ?: [];

// Get toggle settings
$showLogo   = Options::v('nix_label_show_logo') ?: 'text';
$showItems  = Options::v('nix_label_show_items') ?: 'yes';
$showSender = Options::v('nix_label_show_sender') ?: 'yes';

// Get barcode settings
$showOrderBarcode   = Options::v('nix_label_show_order_barcode') ?: 'yes';
$showPkgBarcode     = Options::v('nix_label_show_package_barcode') ?: 'no';

$siteLogo   = Options::v('logo');
if (!filter_var($siteLogo, FILTER_VALIDATE_URL) && $siteLogo != "") {
    $siteLogo = Site::$url . $siteLogo;
}

// Get sender info & visibility
$labelHead = Options::v('nix_label_header') ?: 'SHIPPING';
$origName  = (Options::v('nix_label_show_store_name') !== 'no') ? (Options::v('nix_store_name') ?: Options::v('sitename')) : '';
$origPhone = (Options::v('nix_label_show_store_phone') !== 'no') ? (Options::v('nix_store_phone') ?: Options::v('sitephone')) : '';
$origAddr  = (Options::v('nix_label_show_store_address') !== 'no') ? (Options::v('nix_store_address') ?: Options::v('siteaddress')) : '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Shipping Label - <?php echo $orderId; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        /* Force Hide GeniXCMS Admin UI */
        nav, header, .navbar, .sidebar, .footer, .breadcrumb, #sidebar-wrapper, .gx-admin-header { 
            display: none !important; 
        }
        #page-wrapper, .container-fluid, .row { margin: 0 !important; padding: 0 !important; border: none !important; }
        
        @page { size: auto; margin: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: #fff; line-height: 1.4; color: #000; }
        
        /* Ensure only label is visible in print */
        @media print {
            body { padding: 0; visibility: hidden; }
            .label-container { visibility: visible; border-width: 2px; max-width: 100%; position: absolute; left: 0; top: 0; }
            .no-print { display: none !important; }
        }

        .label-container { 
            visibility: visible;
            width: 100%; 
            max-width: 500px; 
            border: 2px solid #000; 
            margin: 0 auto; 
            padding: 0; 
            position: relative;
            overflow: hidden;
            background: #fff;
        }
        .header { 
            background: #000; 
            color: #fff; 
            padding: 10px; 
            text-align: center; 
            font-weight: bold; 
            font-size: 20px; 
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .section { 
            border-bottom: 1px solid #000; 
            padding: 12px; 
        }
        .section:last-child { border-bottom: none; }
        .label-text { font-size: 10px; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; display: block; }
        .content-main { font-size: 16px; font-weight: bold; }
        .content-sub { font-size: 13px; margin-top: 2px; }
        
        .row { display: flex; }
        .col { flex: 1; }
        .col-border { border-right: 1px solid #000; }
        
        .courier-box { text-align: center; display: flex; flex-direction: column; justify-content: center; }
        .courier-name { font-size: 24px; font-weight: 800; border-bottom: 1px solid #000; margin-bottom: 5px; padding-bottom: 5px; }
        .service-name { font-size: 14px; }
        
        .resi-box { padding: 15px; text-align: center; border-bottom: 2px solid #000; }
        .resi-text { font-size: 12px; margin-bottom: 5px; }
        .resi-code { font-size: 22px; font-weight: 900; font-family: 'Courier New', Courier, monospace; letter-spacing: 2px; }
        
        .items-list { font-size: 11px; padding: 10px; background: #f9f9f9; }
        .items-list table { width: 100%; border-collapse: collapse; }
        .items-list td { padding: 2px 0; }
        
        #barcode_order_container { margin-top: 5px; }
        #barcode_pkg_container { text-align: center; border-top: 2px dashed #000; padding: 10px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .label-container { border-width: 2px; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #000; color: #fff; border: none; font-weight: bold; border-radius: 5px; margin-right: 5px;">PRINT LABEL NOW</button>
    <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #eee; color: #333; border: 1px solid #ccc; font-weight: bold; border-radius: 5px;">CLOSE TAB</button>
</div>

<div class="label-container">
    <div class="header"><?php echo $labelHead; ?></div>
    
    <!-- Courier Info -->
    <div class="row section" style="padding: 0;">
        <div class="col col-border courier-box" style="padding: 10px;">
            <div class="courier-name"><?php echo strtoupper($order->shipping_courier ?? 'MANUAL'); ?></div>
            <div class="service-name"><?php echo strtoupper($order->shipping_service ?? 'POS DROP'); ?></div>
        </div>
        <div class="col" style="padding: 10px; text-align: center;">
            <div class="label-text">ORDER ID</div>
            <div class="content-main" style="font-size: 14px;"><?php echo $orderId; ?></div>
            <?php if ($showOrderBarcode == 'yes'): ?>
                <div id="barcode_order_container">
                    <svg id="barcode_order"></svg>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- AWB / RESI -->
    <?php if ($order->shipping_resi): ?>
    <div class="resi-box">
        <div class="resi-text">Nomor Resi / AWB:</div>
        <div class="resi-code"><?php echo $order->shipping_resi; ?></div>
    </div>
    <?php endif; ?>

    <!-- Receiver & Sender -->
    <div class="row">
        <!-- Receiver -->
        <div class="col <?php echo ($showSender == 'yes') ? 'col-border' : ''; ?> section">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="label-text">PENERIMA:</span>
                    <div class="content-main"><?php echo $order->customer_name; ?></div>
                    <div class="content-sub"><?php echo $order->customer_phone; ?></div>
                </div>
                <?php if ($showLogo == 'logo' && $siteLogo): ?>
                    <img src="<?php echo $siteLogo; ?>" style="max-height: 40px; max-width: 120px; object-fit: contain;">
                <?php endif; ?>
            </div>
            <div class="content-sub" style="margin-top: 8px;">
                <div class="label-text" style="font-size: 8px; color: #666;">Alamat Pengiriman:</div>
                <?php echo nl2br($order->shipping_address); ?>
            </div>
        </div>

        <!-- Sender -->
        <?php if ($showSender == 'yes'): ?>
        <div class="col section">
            <span class="label-text">PENGIRIM:</span>
            <?php if ($origName): ?>
                <div class="content-main"><?php echo $origName; ?></div>
            <?php endif; ?>
            <?php if ($origPhone): ?>
                <div class="content-sub"><?php echo $origPhone; ?></div>
            <?php endif; ?>
            <?php if ($origAddr): ?>
                <div class="content-sub" style="margin-top: 8px; font-size: 11px;">
                    <?php echo nl2br($origAddr); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Mini Checklist for Packing -->
    <?php if ($showItems == 'yes'): ?>
    <div class="items-list section">
        <span class="label-text">ITEM SUMMARY:</span>
        <table>
            <?php foreach ($items as $id => $qty): ?>
            <tr>
                <td width="20" valign="top">[ ]</td>
                <td valign="top"><?php echo $qty; ?>x <?php echo Posts::title($id); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Packaging/Sorting Barcode -->
    <?php if ($showPkgBarcode == 'yes' && !empty($order->packaging_code)): ?>
    <div id="barcode_pkg_container">
        <div class="label-text">PACKAGING & SORTING CODE</div>
        <div style="font-size: 18px; font-weight: 900;"><?php echo $order->packaging_code; ?></div>
        <svg id="barcode_pkg"></svg>
    </div>
    <?php endif; ?>
    
    <div style="padding: 5px; text-align: center; font-size: 8px; font-style: italic; opacity: 0.6;">
        Generated by Nixomers @ <?php echo date('Y-m-d H:i:s'); ?>
    </div>
</div>

<script>
    window.onload = function() {
        <?php if ($showOrderBarcode == 'yes'): ?>
        JsBarcode("#barcode_order", "<?php echo $orderId; ?>", {
            format: "CODE128",
            width: 1.5,
            height: 35,
            displayValue: false
        });
        <?php endif; ?>

        <?php if ($showPkgBarcode == 'yes' && !empty($order->packaging_code)): ?>
        JsBarcode("#barcode_pkg", "<?php echo $order->packaging_code; ?>", {
            format: "CODE128",
            width: 2,
            height: 50,
            displayValue: false
        });
        <?php endif; ?>
        
        // window.print();
    }
</script>

</body>
</html>
