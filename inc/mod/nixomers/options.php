<?php
/**
 * Nixomers Module Options Panel
 * Acts as a router to specific partials in the options/ directory.
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// Base URL for the module
$mod_url = "index.php?page=mods&mod=nixomers";

echo '<style>
    .fw-black { font-weight: 900 !important; }
    .content-body { padding: 1rem 1.5rem !important; }
    @media (max-width: 768px) {
        .content-body { padding: 1rem !important; }
    }
</style>';

// ── 1. POST ACTION HANDLERS ──────────────────────────────────────────

// Handle settings update
if (isset($_POST['nixomers_settings_save'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('page_settings')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk mengubah pengaturan.");
    } else {
        Options::update('nixomers_currency', Typo::cleanX($_POST['currency'] ?? 'IDR'));
        Options::update('nixomers_format', Typo::cleanX($_POST['format'] ?? 'dot'));
        Options::update('nixomers_tax', Typo::cleanX($_POST['tax_rate'] ?? '0'));
        Options::update('nixomers_framework', Typo::cleanX($_POST['framework'] ?? 'bootstrap'));
        Options::update('nixomers_invoice_prefix', Typo::cleanX($_POST['invoice_prefix'] ?? 'INV'));
        Options::update('nixomers_invoice_format', Typo::cleanX($_POST['invoice_format'] ?? '{PREFIX}-{YYYY}{MM}{DD}-{ID}'));
        Options::update('nixomers_order_prefix', Typo::cleanX($_POST['order_prefix'] ?? 'ORD'));
        Options::update('nixomers_order_format', Typo::cleanX($_POST['order_format'] ?? '{PREFIX}-{YYYY}{MM}{DD}-{ID}'));
        Options::update('nixomers_product_prefix', Typo::cleanX($_POST['product_prefix'] ?? 'PRD'));
        Options::update('nixomers_product_format', Typo::cleanX($_POST['product_format'] ?? '{PREFIX}-{ID}'));
        Options::update('nixomers_trx_prefix', Typo::cleanX($_POST['trx_prefix'] ?? 'TRX'));
        Options::update('nixomers_trx_format', Typo::cleanX($_POST['trx_format'] ?? '{PREFIX}-{ID}'));

        // Store Identity (Global)
        Options::update('nix_store_name', Typo::cleanX($_POST['nix_store_name'] ?? ''));
        Options::update('nix_store_phone', Typo::cleanX($_POST['nix_store_phone'] ?? ''));
        Options::update('nix_store_address', Typo::cleanX($_POST['nix_store_address'] ?? ''));

        Options::update('nix_orig_country', Typo::cleanX($_POST['orig_country'] ?? 'Indonesia'));
        Options::update('nix_orig_province', Typo::cleanX($_POST['orig_province'] ?? ''));
        Options::update('nix_orig_city', Typo::cleanX($_POST['orig_city'] ?? ''));
        Options::update('nix_orig_district', Typo::cleanX($_POST['orig_district'] ?? ''));
        Options::update('nix_orig_village', Typo::cleanX($_POST['orig_village'] ?? ''));
        Options::update('nix_shipping_engine', Typo::cleanX($_POST['shipping_engine'] ?? 'kiriminaja'));
        Options::update('nix_kiriminaja_token', Typo::cleanX($_POST['kiriminaja_token'] ?? ''));
        Options::update('nix_kiriminaja_mode', Typo::cleanX($_POST['kiriminaja_mode'] ?? 'sandbox'));
        Options::update('nix_kiriminaja_origin_id', Typo::int($_POST['kiriminaja_origin_id'] ?? 0));
        Options::update('nix_apicoid_token', Typo::cleanX($_POST['apicoid_token'] ?? ''));
        Options::update('nix_enabled_couriers', implode(',', $_POST['enabled_couriers'] ?? []));
        Options::update('nix_enabled_gateways', implode(',', $_POST['nix_enabled_gateways'] ?? []));

        // Label Settings
        Options::update('nix_label_header', Typo::cleanX($_POST['nix_label_header'] ?? 'SHIPPING'));
        Options::update('nix_label_show_store_name', Typo::cleanX($_POST['nix_label_show_store_name'] ?? 'yes'));
        Options::update('nix_label_show_store_phone', Typo::cleanX($_POST['nix_label_show_store_phone'] ?? 'yes'));
        Options::update('nix_label_show_store_address', Typo::cleanX($_POST['nix_label_show_store_address'] ?? 'yes'));

        Options::update('nix_label_show_logo', Typo::cleanX($_POST['nix_label_show_logo'] ?? 'text'));
        Options::update('nix_label_show_items', Typo::cleanX($_POST['nix_label_show_items'] ?? 'yes'));
        Options::update('nix_label_show_sender', Typo::cleanX($_POST['nix_label_show_sender'] ?? 'yes'));
        Options::update('nix_label_show_order_barcode', Typo::cleanX($_POST['nix_label_show_order_barcode'] ?? 'yes'));
        Options::update('nix_label_show_package_barcode', Typo::cleanX($_POST['nix_label_show_package_barcode'] ?? 'no'));

        // Invoice Settings
        Options::update('nix_invoice_show_store_name', Typo::cleanX($_POST['nix_invoice_show_store_name'] ?? 'yes'));
        Options::update('nix_invoice_show_store_phone', Typo::cleanX($_POST['nix_invoice_show_store_phone'] ?? 'yes'));
        Options::update('nix_invoice_show_store_address', Typo::cleanX($_POST['nix_invoice_show_store_address'] ?? 'yes'));
        Options::update('nix_invoice_footer_note', Typo::cleanX($_POST['nix_invoice_footer_note'] ?? ''));
        Options::update('nix_invoice_color', Typo::cleanX($_POST['nix_invoice_color'] ?? '#1a73e8'));
        Options::update('nix_invoice_show_logo', Typo::cleanX($_POST['nix_invoice_show_logo'] ?? 'yes'));
        Options::update('nix_invoice_extra_info', Typo::cleanX($_POST['nix_invoice_extra_info'] ?? ''));
        Options::update('nix_invoice_extra_type', Typo::cleanX($_POST['nix_invoice_extra_type'] ?? 'info'));

        // Notification Settings
        Options::update('nix_notif_email_active', Typo::cleanX($_POST['nix_notif_email_active'] ?? 'none'));
        Options::update('nix_notif_wa_active', Typo::cleanX($_POST['nix_notif_wa_active'] ?? 'none'));
        Hooks::run('nix_notification_save');
        
        // Dynamic hooks for saving gateway settings
        Hooks::run('nix_payment_gateways_save');

        Options::update('nix_bank_name', Typo::cleanX($_POST['nix_bank_name'] ?? ''));
        Options::update('nix_bank_account', Typo::cleanX($_POST['nix_bank_account'] ?? ''));
        Options::update('nix_bank_owner', Typo::cleanX($_POST['nix_bank_owner'] ?? ''));
        Options::update('nix_paypal_email', Typo::cleanX($_POST['nix_paypal_email'] ?? ''));
        Options::update('nix_paypal_mode', Typo::cleanX($_POST['nix_paypal_mode'] ?? 'sandbox'));

        $GLOBALS['alertSuccess'] = _("Nixomers settings saved successfully!");
        Http::redirect(Url::current());
    }
}

// Handle Bulk Process for Orders
if (isset($_POST['bulk_process']) && isset($_POST['order_id'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_order_delete')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk menghapus pesanan.");
    } else {
        $action = Typo::cleanX($_POST['action']);
        $ids = $_POST['order_id'];

        foreach ($ids as $id) {
            $id = Typo::int($id);
            if ($action == 'delete') {
                Query::table('nix_orders')->where('id', $id)->delete();
            } else {
                Query::table('nix_orders')->where('id', $id)->update(['status' => $action]);
            }
        }
        $GLOBALS['alertSuccess'] = _("Bulk action '{$action}' applied successfully!");
            foreach ($ids as $id) {
                $order = Query::table('nix_orders')->where('id', $id)->first();
                if ($order) {
                    Hooks::run('nixomers_order_status_updated', ['order_id' => $order->order_id, 'status' => $action]);
                }
            }
            Http::redirect(Url::current());
    }
}

// Handle Stock Update
if (isset($_POST['nixomers_stock_update'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_stock_update')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk mengupdate stok.");
    } else {
        $pId = Typo::int($_POST['p_id']);
        $amount = Typo::int($_POST['amount']);
        $type = Typo::cleanX($_POST['move_type']); // IN or OUT
        $location = Typo::int($_POST['location_id']);
        $ref = Typo::cleanX($_POST['reference']);
        $notes = Typo::cleanX($_POST['notes']);

        Nixomers::logInventory($pId, $amount, $type, $ref, $location, $notes);
        $GLOBALS['alertSuccess'] = _("Stock updated and logged successfully!");
        Http::redirect(Url::current());
    }
}
if (isset($_POST['confirm_ready_to_ship'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_order_update_status')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk mengubah status pesanan.");
    } else {
        $orderId = Typo::cleanX($_POST['order_id']);
        $pkgCode = Typo::cleanX($_POST['packaging_code']);
        $location = Typo::cleanX($_POST['staging_location']);
        
        $oldOrder = Query::table('nix_orders')->where('order_id', $orderId)->first();
        if ($oldOrder) {
            Query::table('nix_orders')->where('order_id', $orderId)->update([
                'status' => 'ready_to_ship',
                'packaging_code' => $pkgCode,
                'staging_location' => $location
            ]);

            Query::table('nix_order_logs')->insert([
                'order_id' => $orderId,
                'old_status' => $oldOrder->status,
                'new_status' => 'ORDER: READY_TO_SHIP',
                'updated_by' => Session::val('username') ?: 'admin',
                'notes' => 'Packaging Code: ' . $pkgCode . ' | Location: ' . $location,
                'date' => date('Y-m-d H:i:s')
            ]);
            Hooks::run('nixomers_order_status_updated', ['order_id' => $orderId, 'status' => 'ready_to_ship']);

            // Notification: Ready to Ship
            Nixomers::addNotification(
                'ORDER_READY',
                'Order Ready to Ship: ' . $orderId,
                'Order ' . $orderId . ' has been validated and ready for fulfillment.',
                'index.php?page=mods&mod=nix_fulfillment&sel=details&id=' . $oldOrder->id,
                'fulfillment'
            );

            $GLOBALS['alertSuccess'] = _("Order successfully validated! Pkg: {$pkgCode} at {$location}");
        }
        Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
    }
}

// Handle Order Status Update (GET)
if (isset($_GET['act']) && $_GET['act'] == 'setstatus') {
    if (!Token::validate($_GET['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_order_update_status')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk mengubah status pesanan.");
    } else {
        $orderId = Typo::cleanX($_GET['id']);
        $status = Typo::cleanX($_GET['status']);
        $oldOrder = Query::table('nix_orders')->where('order_id', $orderId)->first();
        
        $updateData = ['status' => $status];
        if ($status == 'shipped') {
            $updateData['shipped_at'] = date('Y-m-d H:i:s');
        }
        Query::table('nix_orders')->where('order_id', $orderId)->update($updateData);

        // Record Global Order Log
        if ($oldOrder) {
            Query::table('nix_order_logs')->insert([
                'order_id' => $orderId,
                'old_status' => $oldOrder->status,
                'new_status' => 'ORDER: ' . strtoupper($status),
                'updated_by' => Session::val('username') ?: 'admin',
                'date' => date('Y-m-d H:i:s')
            ]);
            Hooks::run('nixomers_order_status_updated', ['order_id' => $orderId, 'status' => $status]);

            // Notification for specific status changes
            if ($status == 'shipped') {
                Nixomers::addNotification(
                    'ORDER_SHIPPED',
                    'Order Shipped: ' . $orderId,
                    'Order ' . $orderId . ' has been shipped.',
                    'index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId,
                    'admin'
                );
            }

            if ($status == 'delivered') {
                Nixomers::addNotification(
                    'ORDER_FOLLOWUP',
                    'Order Delivered: ' . $orderId,
                    'Order ' . $orderId . ' has been delivered. Please follow up with customer.',
                    'index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId,
                    'cs'
                );
            }
        }

        $GLOBALS['alertSuccess'] = _("Order status updated to '{$status}'!");
        Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
    }
}

if (isset($_POST['save_resi'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } else {
        $orderId = Typo::cleanX($_GET['id']);
        $oldOrder = Query::table('nix_orders')->where('order_id', $orderId)->first();
        $resi = Typo::cleanX($_POST['shipping_resi']);
        Query::table('nix_orders')->where('order_id', $orderId)->update(['shipping_resi' => $resi]);

        // Record Log for Resi Update
        if ($oldOrder && $oldOrder->shipping_resi !== $resi) {
            Query::table('nix_order_logs')->insert([
                'order_id' => $orderId,
                'old_status' => 'RESI: ' . ($oldOrder->shipping_resi ?: 'Empty'),
                'new_status' => 'RESI: ' . ($resi ?: 'Removed'),
                'updated_by' => Session::val('username') ?: 'admin',
                'date' => date('Y-m-d H:i:s')
            ]);
        }

        $GLOBALS['alertSuccess'] = _("Shipping receipt (Resi) updated!");
        Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
    }
}

// Handle Sync Granular Items (POST)
if (isset($_POST['sync_granular_items'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_granular_update')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk mengelola data granular.");
    } else {
        $orderId = Typo::cleanX($_GET['id']);
        $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
        if ($order) {
            $cart = json_decode($order->cart_items, true) ?: [];
            foreach ($cart as $pId => $qty) {
                for ($i = 0; $i < $qty; $i++) {
                    Query::table('nix_order_items')->insert([
                        'order_id' => $orderId,
                        'product_id' => (int) $pId,
                        'qty' => 1,
                        'status' => 'pending'
                    ]);
                }
            }
            $GLOBALS['alertSuccess'] = _("Unit tracking initiated for this order.");
            Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
        }
    }
}

// Handle Update Order Item (POST)
if (isset($_POST['update_order_item'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_granular_update')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk mengupdate data unit.");
    } else {
        $itemId = Typo::int($_POST['item_id']);
        $oldItem = Query::table('nix_order_items')->where('id', $itemId)->first();
        $orderId = $oldItem->order_id;
        
        $newStatus = Typo::cleanX($_POST['item_status']);
        $data = [
            'status' => $newStatus,
            'serial_number' => Typo::cleanX($_POST['serial_number'] ?? ''),
            'barcode' => Typo::cleanX($_POST['barcode'] ?? ''),
            'location' => Typo::cleanX($_POST['location'] ?? ''),
            'notes' => Typo::cleanX($_POST['notes'] ?? '')
        ];
        Query::table('nix_order_items')->where('id', $itemId)->update($data);

        // Logging the change
        if ($oldItem) {
            Query::table('nix_order_item_logs')->insert([
                'item_id' => $itemId,
                'old_status' => $oldItem->status,
                'new_status' => $newStatus,
                'notes' => $data['notes'],
                'updated_by' => Session::val('username') ?: 'admin',
                'date' => date('Y-m-d H:i:s')
            ]);
        }
        Hooks::run('nixomers_order_item_updated', ['item_id' => $itemId, 'order_id' => $orderId, 'data' => $data]);

        $GLOBALS['alertSuccess'] = _("Item information updated!");
        Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
    }
}


if (isset($_POST['refund_order'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_order_refund')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk melakukan refund.");
    } else {
        $orderId = Typo::cleanX($_POST['order_id']);
        $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
        
        if ($order && $order->status !== 'returned') {
            $oldStatus = $order->status;
            
            // 1. Revert Stock (Assuming full return)
            $items = json_decode($order->cart_items, true) ?: [];
            foreach ($items as $pid => $qty) {
                NixInventory::add($pid, $qty, $orderId, 'Order Returned & Refunded: ' . $orderId);
            }

            // 2. Update Order Status
            Query::table('nix_orders')->where('order_id', $orderId)->update(['status' => 'returned']);

            // 3. Update Transaction Status to Refunded
            Query::table('nix_transactions')->where('order_id', $orderId)->update(['status' => 'refunded']);

            // 4. Record Audit Log
            Query::table('nix_order_logs')->insert([
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => 'returned',
                'notes' => 'Full Refund & Inventory Return processed.',
                'updated_by' => Session::val('username') ?: 'admin',
                'date' => date('Y-m-d H:i:s')
            ]);
            Hooks::run('nixomers_order_status_updated', ['order_id' => $orderId, 'status' => 'returned']);

            $GLOBALS['alertSuccess'] = _("Order marked as Returned. Stock replenished and Transaction set to Refunded.");
        }
        Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
    }
}


if (isset($_POST['cancel_order'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_order_cancel')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk membatalkan pesanan.");
    } else {
        $orderId = Typo::cleanX($_POST['order_id']);
        $reason = Typo::cleanX($_POST['notes'] ?? 'No reason provided');
        $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
        
        if ($order && $order->status !== 'cancelled') {
            $oldStatus = $order->status;
            
            // 1. Revert Stock
            $items = json_decode($order->cart_items, true) ?: [];
            foreach ($items as $pid => $qty) {
                NixInventory::add($pid, $qty, $orderId, 'Order Cancelled: ' . $orderId);
            }

            // 2. Update Order Status
            Query::table('nix_orders')->where('order_id', $orderId)->update(['status' => 'cancelled']);

            // 3. Update Transaction Status (if exists)
            Query::table('nix_transactions')->where('order_id', $orderId)->update(['status' => 'cancelled']);

            // 4. Record Audit Log
            Query::table('nix_order_logs')->insert([
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
                'notes' => $reason,
                'updated_by' => Session::val('username') ?: 'admin',
                'date' => date('Y-m-d H:i:s')
            ]);
            Hooks::run('nixomers_order_status_updated', ['order_id' => $orderId, 'status' => 'cancelled']);

            $GLOBALS['alertSuccess'] = _("Order successfully cancelled and stock replenished.");
        }
        
        // Redirect back to either Detail or List based on origin
        $origin = $_POST['origin'] ?? 'orderdetail';
        if ($origin == 'orders') {
            Http::redirect('index.php?page=mods&mod=nixomers&sel=orders');
        } else {
            Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
        }
    }
}


if (isset($_POST['update_payment'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } elseif (!Nixomers::checkACL('action_payment_update')) {
        $GLOBALS['alertDanger'][] = _("Akses ditolak: Anda tidak memiliki izin untuk mengupdate pembayaran.");
    } else {
        $orderId    = Typo::cleanX($_POST['order_id']);
        $newStatus  = Typo::cleanX($_POST['payment_status']);
        $newMethod  = Typo::cleanX($_POST['payment_method'] ?? 'manual');
        $newTrxId   = Typo::cleanX($_POST['payment_trx_id']);
        $newNotes   = Typo::cleanX($_POST['payment_notes'] ?? '');
        $newAmount  = (float) ($_POST['payment_amount'] ?? 0);
        $newFee     = (float) ($_POST['payment_fee'] ?? 0);
        $newTax     = (float) ($_POST['payment_tax'] ?? 0);
        $newShipping = (float) ($_POST['payment_shipping'] ?? 0);
        $newPaidDate = Typo::cleanX($_POST['payment_paid_date'] ?? '');

        $oldTrans = Query::table('nix_transactions')->where('order_id', $orderId)->first();

        // Auto-stamp paid_date when marking as paid; clear it if payment is rolled back
        $isPaid = in_array($newStatus, ['paid', 'completed']);
        if ($isPaid) {
            $paidDate = $newPaidDate ?: date('Y-m-d H:i:s');
        } else {
            $paidDate = null;
        }

        $data = [
            'status'        => $newStatus,
            'method'        => $newMethod,
            'trans_id'      => $newTrxId,
            'notes'         => $newNotes,
            'amount'        => $newAmount,
            'fee'           => $newFee,
            'tax'           => $newTax,
            'shipping_cost' => $newShipping,
            'paid_date'     => $paidDate
        ];
        
        // Update or Insert Transaction
        if ($oldTrans) {
            Query::table('nix_transactions')->where('order_id', $orderId)->update($data);
        } else {
            // New transaction record - generate configurable TRX ID
            $trxPrefix = Options::v('nixomers_trx_prefix') ?: 'TRX';
            $trxFormat = Options::v('nixomers_trx_format') ?: '{PREFIX}-{ID}';
            $newTrxAuto = str_replace(
                ['{PREFIX}', '{ID}'],
                [$trxPrefix, strtoupper(substr(uniqid(), -8))],
                $trxFormat
            );
            $data['trans_id'] = $newTrxId ?: $newTrxAuto;
            $data['order_id'] = $orderId;
            $data['type'] = 'income';
            $data['date'] = date('Y-m-d H:i:s');
            Query::table('nix_transactions')->insert($data);
        }

        // Sync Payment Method to Order
        Query::table('nix_orders')->where('order_id', $orderId)->update(['payment_method' => $newMethod]);

        // Sync Order Status if payment is successful
        if ($newStatus === 'completed' || $newStatus === 'paid') {
            Query::table('nix_orders')->where('order_id', $orderId)->update(['status' => 'waiting']);
        }
        
        // Re-calculate Net
        Nixomers::calculateNetTrans($orderId);
        Hooks::run('nixomers_payment_updated', ['order_id' => $orderId, 'transaction' => $data]);

        // Record Audit Log
        if ($oldTrans) {
            Query::table('nix_order_logs')->insert([
                'order_id' => $orderId,
                'old_status' => 'PAYMENT_ADJUST',
                'new_status' => 'PAYMENT: ' . strtoupper($newStatus), // Prefix added
                'updated_by' => Session::val('username') ?: 'admin',
                'date' => date('Y-m-d H:i:s')
            ]);
        }

        // Notification: Payment Received
        if ($newStatus == 'paid' || $newStatus == 'completed') {
            Nixomers::addNotification(
                'PAYMENT_PAID',
                'Payment Received: ' . $orderId,
                'Order ' . $orderId . ' has been paid. Please process for fulfillment.',
                'index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId,
                'billing'
            );
            Nixomers::addNotification(
                'PAYMENT_PAID',
                'Payment Received: ' . $orderId,
                'Order ' . $orderId . ' has been paid. Please process for fulfillment.',
                'index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId,
                'fulfillment'
            );
        }

        $GLOBALS['alertSuccess'] = _("Payment details updated successfully!");
        // Http::redirect('index.php?page=mods&mod=nixomers&sel=orderdetail&id=' . $orderId);
    }
}

if (isset($_GET['act']) && $_GET['act'] == 'recalculate_payment') {
    if (!Token::validate($_GET['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } else {
        $orderId = Typo::cleanX($_GET['oid'] ?? $_GET['id']);
        Nixomers::calculateNetTrans($orderId);
        $GLOBALS['alertSuccess'] = _("Nominal transaksi berhasil dikalkulasi ulang!");
        
        // Redirect back to original view
        $sel = Typo::cleanX($_GET['sel'] ?? 'orderdetail');
        $id = Typo::cleanX($_GET['id']);
        Http::redirect("index.php?page=mods&mod=nixomers&sel={$sel}&id={$id}");
    }
}


// ── 2. VIEW DISPATCHER ───────────────────────────────────────────────

$sel = isset($_GET['sel']) ? Typo::cleanX($_GET['sel']) : 'dashboard';

// Pass common variables to partials
$data = [
    'mod_url' => $mod_url,
    'sel' => $sel,
    'token' => TOKEN
];

// Page Header Configuration
$data['header'] = [
    'title' => 'Nixomers Commerce',
    'version' => '1.0.0',
    'icon' => 'bi bi-shop',
    'subtitle' => 'Core e-Commerce Dashboard & Configuration'
];

// Routing based on 'sel' parameter
$optionsDir = __DIR__ . '/options';

switch ($sel) {
    case 'transactions':
        if (!Nixomers::checkACL('page_analytics')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('transactions', $data, $optionsDir);
        break;
    case 'analytics':
        if (!Nixomers::checkACL('page_analytics')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('analytics', $data, $optionsDir);
        break;
    case 'transactiondetail':
        if (!Nixomers::checkACL('page_analytics')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('transactiondetail', $data, $optionsDir);
        break;
    case 'stock':
        if (!Nixomers::checkACL('page_inventory')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('stock', $data, $optionsDir);
        break;
    case 'settings':
        if (!Nixomers::checkACL('page_settings')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('settings', $data, $optionsDir);
        break;
    case 'orders':
        if (isset($_GET['act']) && $_GET['act'] == 'add') {
            if (!Nixomers::checkACL('action_order_create')) {
                Control::error('noaccess');
                break;
            }
            echo Mod::inc('orderadd', $data, $optionsDir);
        } else {
            if (!Nixomers::checkACL('page_orders')) {
                Control::error('noaccess');
                break;
            }
            echo Mod::inc('orders', $data, $optionsDir);
        }
        break;
    case 'orderadd':
        if (!Nixomers::checkACL('action_order_create')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('orderadd', $data, $optionsDir);
        break;
    case 'orderdetail':
        if (!Nixomers::checkACL('page_orderdetail')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('orderdetail', $data, $optionsDir);
        break;
    case 'dashboard':
    default:
        if (!Nixomers::checkACL('page_dashboard')) {
            Control::error('noaccess');
            break;
        }
        echo Mod::inc('dashboard', $data, $optionsDir);
        break;
}
