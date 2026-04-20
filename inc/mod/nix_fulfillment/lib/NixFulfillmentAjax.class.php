<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class NixFulfillmentAjax
{
    /**
     * AJAX Endpoint for Fulfillment List
     */
    public function list_fulfillment($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 15;
        $offset = isset($_GET['offset']) ? Typo::int($_GET['offset']) : 0;
        $q = Typo::cleanX($_GET['q'] ?? '');
        $statusFilter = Typo::cleanX($_GET['status'] ?? 'ready_to_ship');
        $cityFilter = Typo::cleanX($_GET['city'] ?? '');
        $courierFilter = Typo::cleanX($_GET['courier'] ?? 'all');
        $sort = Typo::cleanX($_GET['sort'] ?? 'newest');

        $query = Query::table('nix_orders');

        $validStatuses = ['ready_to_ship', 'shipped', 'delivered', 'waiting', 'processing'];
        if ($statusFilter != 'all' && in_array($statusFilter, $validStatuses)) {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['ready_to_ship', 'shipped', 'delivered']);
        }

        if (!empty($q)) {
            $query->groupWhere(function ($query) use ($q) {
                $query->where('order_id', 'LIKE', "%{$q}%")
                    ->orWhere('customer_name', 'LIKE', "%{$q}%")
                    ->orWhere('shipping_resi', 'LIKE', "%{$q}%");
            });
        }

        if (!empty($cityFilter)) {
            $query->where('shipping_city', 'LIKE', "%{$cityFilter}%");
        }

        if ($courierFilter != 'all') {
            $query->where('shipping_courier', $courierFilter);
        }

        $total = (clone $query)->count();

        $orderBy = ($sort == 'oldest') ? 'ASC' : 'DESC';
        $orders = $query->orderBy('date', $orderBy)->limit($num)->offset($offset)->get();

        $rows = [];
        foreach ($orders as $o) {
            $oId = $o->id;
            $fStatus = isset($o->fulfillment_status) ? $o->fulfillment_status : 'ready_to_ship';
            
            $statusColor = match ($fStatus) {
                'shipping_to_customer' => 'success',
                'shipping_to_courier'  => 'info',
                'ready'                => 'warning',
                'packed'               => 'dark',
                'packing'              => 'primary',
                default                => 'secondary'
            };
            
            $fStatusLabel = match ($fStatus) {
                'shipping_to_customer' => 'to customer',
                'shipping_to_courier'  => 'to courier',
                'ready'                => 'ready',
                'packed'               => 'packed',
                'packing'              => 'packing',
                default                => 'ship ready'
            };

            $courier = strtoupper(($o->shipping_courier ?? '') ?: 'Manual');
            $service = $o->shipping_service ?: '-';
            $resi = $o->shipping_resi ?: '<span class="text-danger small fw-bold">NO RECEIPT</span>';

            $rows[] = [
                ['content' => '<input type="checkbox" name="order_id[]" value="' . $oId . '" class="form-check-input check ms-4">'],
                ['content' => '<div><strong class="text-primary">' . $o->order_id . '</strong><br><small class="text-muted">#' . $oId . '</small></div>'],
                ['content' => '<div><strong>' . (isset($o->packaging_code) ? $o->packaging_code : '-') . '</strong></div>'],
                ['content' => '<div><span class="badge bg-light text-dark border">' . (isset($o->staging_location) ? $o->staging_location : '-') . '</span></div>'],
                ['content' => '<div><strong>' . $o->customer_name . '</strong><br><small class="text-muted">' . $o->customer_phone . '</small></div>'],
                ['content' => '<div>' . ($o->shipping_city ?? '-') . '</div><small class="text-muted">' . ($o->shipping_province ?? '-') . '</small>'],
                ['content' => '<div><strong>' . $courier . '</strong></div><small class="text-muted">' . $service . '</small>'],
                ['content' => '<div>' . $resi . '</div>'],
                ['content' => '<span class="badge bg-' . $statusColor . ' bg-opacity-10 text-' . $statusColor . ' px-3 rounded-pill fw-bold small text-uppercase">' . $fStatusLabel . '</span>'],
                ['content' => date('d M Y', strtotime($o->date))],
                [
                    'content' => '
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-primary btn-sm rounded-circle border shadow-sm d-inline-flex align-items-center justify-content-center p-0" 
                            style="width:32px !important; height:32px !important; min-width:32px;" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalGenericFulfillment" 
                            data-id="' . $oId . '"
                            data-orderid="' . $o->order_id . '"
                            data-status="' . $fStatus . '"
                            data-location="' . (isset($o->staging_location) ? $o->staging_location : '') . '"
                            data-notes="' . htmlspecialchars(isset($o->fulfillment_notes) ? $o->fulfillment_notes : '') . '"
                            title="Update Status"
                            onclick="populateFulfillmentModal(this)">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="index.php?page=mods&mod=nix_fulfillment&sel=details&id=' . $oId . '" class="btn btn-light btn-sm rounded-circle border shadow-none d-inline-flex align-items-center justify-content-center p-0" style="width:32px !important; height:32px !important; min-width:32px;" title="View Detail"><i class="bi bi-eye text-primary"></i></a>
                        <a href="' . Url::ajax('nixomers', 'print_label', ['id' => $o->order_id]) . '" target="_blank" class="btn btn-light btn-sm rounded-circle border shadow-none d-inline-flex align-items-center justify-content-center p-0" style="width:32px !important; height:32px !important; min-width:32px;" title="Print Label"><i class="bi bi-printer text-warning"></i></a>
                    </div>',
                    'class' => 'text-center pe-4'
                ]
            ];
        }

        $headers = [
            ['content' => '<input type="checkbox" id="selectall" class="form-check-input ms-4">', 'class' => 'py-3'],
            'Order ID',
            'Pkg Code',
            'Position',
            'Customer',
            'Destination',
            'Courier',
            'Receipt (Resi)',
            'Status',
            'Date',
            ['content' => 'Action', 'class' => 'text-center pe-4']
        ];

        return Ajax::response([
            'status' => 'success',
            'headers' => $headers,
            'data' => $rows,
            'total' => $total,
            'limit' => $num,
            'offset' => $offset
        ]);
    }
}
