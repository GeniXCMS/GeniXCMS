<?php
/**
 * Nixomers Fulfillment Options Router
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$mod_url = "index.php?page=mods&mod=nix_fulfillment";
$sel = $_GET['sel'] ?? 'management';

echo '<style>.fw-black { font-weight: 900 !important; }</style>';

// Header configuration
$header = [
    'title' => 'Nixomers Fulfillment',
    'subtitle' => 'Manage logistics, packing, and shipping of your digital orders.',
    'icon' => 'bi bi-box-seam',
];

// Handle Bulk Process for Fulfillment
if (isset($_POST['fulfillment_bulk_process']) && isset($_POST['order_id'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } else {
        $action = Typo::cleanX($_POST['action']);
        $ids = $_POST['order_id'];

        foreach ($ids as $id) {
            $id = Typo::int($id);
            if ($action == 'shipped') {
                Query::table('nix_orders')->where('id', $id)->update(['status' => 'shipped']);
            } elseif ($action == 'delivered') {
                Query::table('nix_orders')->where('id', $id)->update(['status' => 'delivered']);
            } elseif ($action == 'processing') {
                Query::table('nix_orders')->where('id', $id)->update(['status' => 'processing']);
            } elseif ($action == 'ready_to_ship') {
                Query::table('nix_orders')->where('id', $id)->update(['status' => 'ready_to_ship']);
            }
        }
        $GLOBALS['alertSuccess'] = _("Fulfillment bulk action '{$action}' applied successfully!");
    }
}

// Handle Individual Fulfillment Update
if (isset($_POST['update_fulfillment_status'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Token tidak valid.");
    } else {
        $id = Typo::int($_POST['id']);
        $fStatus = Typo::cleanX($_POST['fulfillment_status']);
        $location = Typo::cleanX($_POST['staging_location']);
        $notes = Typo::cleanX($_POST['fulfillment_notes']);
        
        $updateData = [
            'fulfillment_status' => $fStatus,
            'staging_location' => $location,
            'fulfillment_notes' => $notes
        ];

        // Handle Image Upload
        if (!empty($_FILES['fulfillment_image']['name'])) {
            $path = '/assets/media/images/fulfillment/';
            if (!file_exists(GX_PATH . $path)) {
                mkdir(GX_PATH . $path, 0777, true);
            }
            $up = Upload::go('fulfillment_image', $path, ['jpg', 'jpeg', 'png'], true);
            if (isset($up['path'])) {
                $updateData['fulfillment_image'] = $up['path'];
            }
        }

        // Trigger Order Status Change
        if ($fStatus == 'shipping_to_customer' || $fStatus == 'shipping_to_courier') {
            $updateData['status'] = 'shipped';
            $updateData['shipped_at'] = date('Y-m-d H:i:s');
        }

        Query::table('nix_orders')->where('id', $id)->update($updateData);

        // Log Change
        $order = Query::table('nix_orders')->where('id', $id)->first();
        if ($order) {
            Query::table('nix_order_logs')->insert([
                'order_id' => $order->order_id,
                'old_status' => 'FULFILLMENT',
                'new_status' => strtoupper($fStatus ?? ''),
                'updated_by' => Session::val('username') ?: 'admin',
                'notes' => $notes . " | Location: " . $location,
                'date' => date('Y-m-d H:i:s')
            ]);
        }

        $GLOBALS['alertSuccess'] = _("Fulfillment status updated successfully!");
    }
}

// Router
switch ($sel) {
    case 'details':
        include 'options/details.php';
        break;
        
    case 'management':
    default:
        include 'options/management.php';
        break;
}
