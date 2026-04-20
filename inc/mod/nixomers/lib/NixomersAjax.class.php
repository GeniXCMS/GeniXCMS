<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 * @since 2.3.0
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class NixomersAjax
{
    /**
     * GeniXCMS - Content Management System
     */
    public function index($param = null)
    {
        $action = isset($_GET['action']) ? Typo::cleanX($_GET['action']) : '';
        if (method_exists($this, $action)) {
            return $this->$action($param);
        }
        return Ajax::error(404, 'Unknown action');
    }

    /**
     * AJAX Endpoint for Product Types by Brand
     */
    public function product_types($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $brand = Typo::cleanX($_GET['require_val'] ?? $_GET['brand'] ?? '');
        if ($brand != '') {
            $brand = Typo::Xclean($brand);
            $types = Nixomers::getProductTypes($brand);
            return Ajax::response($types);
        }
        return Ajax::error(400, 'Missing Brand parameter');
    }

    /**
     * AJAX Endpoint for Product List (Desktop-based render experiment)
     * Now uses the core PostsAjax logic for consistency and hook support.
     */
    public function list_products($param = null)
    {
        $postsAjax = new PostsAjax();
        return $postsAjax->list_posts($param);
    }

    /**
     * AJAX Endpoint for Orders List (Desktop-based render)
     */
    public function list_orders($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 10;
        $offset = isset($_GET['offset']) ? Typo::int($_GET['offset']) : 0;
        $q = Typo::cleanX($_GET['q'] ?? '');
        $status = Typo::cleanX($_GET['status'] ?? 'all');
        $from = Typo::cleanX($_GET['start_date'] ?? '');
        $to = Typo::cleanX($_GET['end_date'] ?? '');
        $sort = Typo::cleanX($_GET['sort'] ?? 'newest');

        $query = Query::table('nix_orders')
            ->select(['nix_orders.*', 'nix_transactions.status as payment_status', 'nix_transactions.method as payment_method'])
            ->join('nix_transactions', 'nix_orders.order_id', '=', 'nix_transactions.order_id', 'LEFT');

        if (!empty($q)) {
            $query->groupWhere(function ($query) use ($q) {
                $query->where('nix_orders.customer_name', 'LIKE', "%{$q}%")
                    ->orWhere('nix_orders.order_id', 'LIKE', "%{$q}%")
                    ->orWhere('nix_orders.customer_email', 'LIKE', "%{$q}%");
            });
        }

        if ($status !== 'all') {
            $query->where('nix_orders.status', $status);
        }

        if (!empty($from)) {
            $query->where('nix_orders.date', '>=', $from . ' 00:00:00');
        }
        if (!empty($to)) {
            $query->where('nix_orders.date', '<=', $to . ' 23:59:59');
        }

        $total = (clone $query)->count();

        // Sort logic
        switch ($sort) {
            case 'oldest':
                $query->orderBy('nix_orders.id', 'ASC');
                break;
            case 'highest':
                $query->orderBy('nix_orders.total', 'DESC');
                break;
            case 'lowest':
                $query->orderBy('nix_orders.total', 'ASC');
                break;
            default:
                $query->orderBy('nix_orders.id', 'DESC');
                break;
        }

        $orders = $query->limit($num)->offset($offset)->get();
        $currency = Options::v('nixomers_currency') ?: 'IDR';
        $mod_url = Site::$url . 'gxadmin/index.php?page=mods&mod=nixomers';

        $rows = [];
        foreach ($orders as $o) {
            $status_color = match ($o->status) {
                'paid', 'completed', 'delivered' => 'success',
                'cancelled', 'expired' => 'danger',
                'shipped', 'onprocess' => 'primary',
                'ready_to_ship' => 'warning',
                'waiting' => 'info',
                default => 'secondary'
            };

            $pStatus_color = match ($o->payment_status) {
                'paid', 'completed' => 'success',
                'cancelled', 'expired' => 'danger',
                'pending' => 'warning',
                default => 'secondary'
            };

            $status_label = match ($o->status) {
                'waiting' => 'waiting process',
                'ready_to_ship' => 'ready to ship',
                default => $o->status
            };

            $pStatus_label = ($o->payment_status === 'completed') ? 'paid' : ($o->payment_status ?: 'pending');

            // Action Menu
            $actionMenu = [
                'view' => [
                    'label' => 'View Detail',
                    'icon' => 'bi bi-eye',
                    'href' => $mod_url . '&sel=orderdetail&id=' . $o->order_id,
                    'class' => 'dropdown-item rounded-3 small fw-bold'
                ],
                'sep1' => ['type' => 'divider'],
                'complete' => [
                    'label' => 'Mark Completed',
                    'icon' => 'bi bi-check2-circle',
                    'href' => '#',
                    'class' => 'dropdown-item rounded-3 small fw-bold text-success'
                ],
                'cancel' => [
                    'label' => 'Cancel Order',
                    'icon' => 'bi bi-x-circle',
                    'href' => 'javascript:void(0)',
                    'onclick' => 'cancelOrder(\'' . $o->order_id . '\')',
                    'class' => 'dropdown-item rounded-3 small fw-bold text-danger ' . ($o->status == 'cancelled' ? 'disabled' : '')
                ]
            ];
            $actionMenu = Hooks::filter('nixomers_orders_action_menu', $actionMenu, $o);

            $menuHtml = '';
            foreach ($actionMenu as $mv) {
                if (isset($mv['type']) && $mv['type'] === 'divider') {
                    $menuHtml .= '<li><hr class="dropdown-divider"></li>';
                } else {
                    $attr = isset($mv['onclick']) ? ' onclick="' . $mv['onclick'] . '"' : '';
                    $menuHtml .= '<li><a class="' . ($mv['class'] ?? 'dropdown-item') . '" href="' . ($mv['href'] ?? '#') . '"' . $attr . '><i class="' . ($mv['icon'] ?? '') . ' me-2"></i> ' . $mv['label'] . '</a></li>';
                }
            }

            $row = [
                ['content' => '<input type="checkbox" name="order_id[]" value="' . $o->id . '" class="form-check-input ms-4">'],
                ['content' => '<div><a href="' . $mod_url . '&sel=orderdetail&id=' . ($o->order_id ?? '') . '" class="text-decoration-none"><strong class="text-primary">' . ($o->order_id ?? 'N/A') . '</strong></a><br><small class="text-muted">ID: #' . $o->id . '</small></div>'],
                ['content' => '<div><strong>' . ($o->customer_name ?? 'N/A') . '</strong><br><small class="text-muted">' . ($o->customer_phone ?? 'N/A') . '</small></div>'],
                ['content' => '<div>' . ($o->shipping_city ?? '-') . '</div><small class="text-muted">' . ($o->shipping_province ?? '-') . '</small>'],
                ['content' => '<span class="fw-bold">' . $currency . ' ' . number_format($o->total, 0) . '</span>'],
                ['content' => '<span class="badge bg-' . $pStatus_color . ' bg-opacity-10 text-' . $pStatus_color . ' px-3 rounded-pill fw-bold small text-uppercase">' . $pStatus_label . '</span>'],
                ['content' => '<span class="badge bg-' . $status_color . ' bg-opacity-10 text-' . $status_color . ' px-3 rounded-pill fw-bold small text-uppercase">' . $status_label . '</span>'],
                ['content' => date('d M Y, H:i', strtotime($o->date))],
            ];

            $row = Hooks::filter('nixomers_orders_table_row', $row, $o);

            // Final Action Column
            $row[] = [
                'content' => '
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle border shadow-none" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2" style="z-index: 9999;" data-bs-boundary="viewport">
                        ' . $menuHtml . '
                    </ul>
                </div>',
                'class' => 'text-center pe-4'
            ];

            $rows[] = $row;
        }

        $headers = [
            ['content' => '<input type="checkbox" id="selectall" class="form-check-input ms-4" onchange="toggleCheckboxes(this)">', 'width' => '50px'],
            ['content' => 'Order ID'],
            ['content' => 'Customer'],
            ['content' => 'Location'],
            ['content' => 'Total Amount'],
            ['content' => 'Payment'],
            ['content' => 'Order Status'],
            ['content' => 'Date'],
            ['content' => 'Action', 'class' => 'text-center pe-4']
        ];
        $headers = Hooks::filter('nixomers_orders_table_headers', $headers);

        return Ajax::response([
            'status' => 'success',
            'headers' => $headers,
            'data' => $rows,
            'total' => $total,
            'limit' => $num,
            'offset' => $offset
        ]);
    }

    /**
     * AJAX Endpoint for Transactions Ledger (Desktop-based render)
     */
    public function list_transactions($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $num = isset($_GET['num']) ? Typo::int($_GET['num']) : 15;
        $offset = isset($_GET['offset']) ? Typo::int($_GET['offset']) : 0;
        $q = Typo::cleanX($_GET['q'] ?? '');
        $type = Typo::cleanX($_GET['type'] ?? 'all');
        $from = Typo::cleanX($_GET['start_date'] ?? '');
        $to = Typo::cleanX($_GET['end_date'] ?? '');

        $query = Query::table('nix_transactions');
        if (!empty($q)) {
            $query->groupWhere(function ($query) use ($q) {
                $query->where('description', 'LIKE', "%{$q}%")
                    ->orWhere('trans_id', 'LIKE', "%{$q}%")
                    ->orWhere('order_id', 'LIKE', "%{$q}%");
            });
        }
        if ($type != 'all') {
            $query->where('type', $type);
        }
        if ($from) $query->where('date', '>=', $from . ' 00:00:00');
        if ($to) $query->where('date', '<=', $to . ' 23:59:59');

        $total = (clone $query)->count();
        $transactions = $query->orderBy('id', 'DESC')->limit($num)->offset($offset)->get();

        $currency = Options::v('nixomers_currency') ?: 'IDR';
        $mod_url = Site::$url . 'gxadmin/index.php?page=mods&mod=nixomers';

        $rows = [];
        foreach ($transactions as $t) {
            $badgeColor = ($t->type == 'income') ? 'success' : 'danger';
            $statusColor = match ($t->status) {
                'completed' => 'success',
                'refunded' => 'warning',
                'cancelled' => 'danger',
                default => 'secondary'
            };
            $amountPrefix = ($t->type == 'income') ? '+' : '-';

            $row = [
                ['content' => '<div><a href="' . $mod_url . '&sel=transactiondetail&id=' . $t->id . '" class="text-decoration-none"><strong class="text-primary">#TX-' . str_pad($t->id, 5, '0', STR_PAD_LEFT) . '</strong></a><br><small class="text-muted extra-small" style="font-size: 10px;">' . ($t->trans_id ?: '-') . '</small></div>', 'class' => 'ps-4'],
                ['content' => '<span class="badge bg-' . $badgeColor . ' bg-opacity-10 text-' . $badgeColor . ' rounded-pill px-3 py-2 fw-bold text-uppercase small">' . $t->type . '</span>'],
                ['content' => '<div><strong>' . $t->description . '</strong><br><small class="text-muted">Ref: <a href="' . $mod_url . '&sel=orders&q=' . $t->order_id . '" class="text-decoration-none">Order #' . $t->order_id . '</a></small></div>'],
                ['content' => '<span class="fw-bold text-dark">' . $currency . ' ' . number_format((float) ($t->amount ?? 0), 2) . '</span>'],
                ['content' => '<span class="text-danger extra-small">-' . $currency . ' ' . number_format((float) ($t->fee ?? 0), 2) . '</span>'],
                ['content' => '<span class="text-warning extra-small">-' . $currency . ' ' . number_format((float) ($t->tax ?? 0), 2) . '</span>'],
                ['content' => '<span class="fw-bold text-' . $badgeColor . '">' . $amountPrefix . ' ' . $currency . ' ' . number_format((float) ($t->net ?? 0), 2) . '</span>'],
                ['content' => '<div><span class="badge bg-light text-dark border px-2 py-1 small rounded-3 mb-1">' . strtoupper($t->method ?? 'Manual') . '</span><br><span class="text-' . $statusColor . ' extra-small fw-bold text-uppercase">' . ($t->status ?: 'Pending') . '</span></div>'],
                ['content' => date('d M Y, H:i', strtotime($t->date))],
            ];

            $row = Hooks::filter('nixomers_transactions_table_row', $row, $t);

            $actionMenu = [
                'view' => [
                    'label' => 'View Detail',
                    'icon' => 'bi bi-eye',
                    'href' => $mod_url . '&sel=transactiondetail&id=' . $t->id,
                    'class' => 'dropdown-item rounded-3 small fw-bold'
                ],
                'print' => [
                    'label' => 'Print Proof',
                    'icon' => 'bi bi-printer',
                    'href' => '#',
                    'class' => 'dropdown-item rounded-3 small fw-bold'
                ],
                'sep1' => ['type' => 'divider'],
                'delete' => [
                    'label' => 'Delete Record',
                    'icon' => 'bi bi-trash',
                    'href' => '#',
                    'class' => 'dropdown-item rounded-3 small fw-bold text-danger'
                ]
            ];
            $actionMenu = Hooks::filter('nixomers_transactions_action_menu', $actionMenu, $t);

            $menuHtml = '';
            foreach ($actionMenu as $mv) {
                if (isset($mv['type']) && $mv['type'] === 'divider') {
                    $menuHtml .= '<li><hr class="dropdown-divider"></li>';
                } else {
                    $attr = isset($mv['onclick']) ? ' onclick="' . $mv['onclick'] . '"' : '';
                    $menuHtml .= '<li><a class="' . ($mv['class'] ?? 'dropdown-item') . '" href="' . ($mv['href'] ?? '#') . '"' . $attr . '><i class="' . ($mv['icon'] ?? '') . ' me-2"></i> ' . $mv['label'] . '</a></li>';
                }
            }

            $row[] = [
                'content' => '
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle border shadow-none" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2" style="z-index: 9999;" data-bs-boundary="viewport">
                        ' . $menuHtml . '
                    </ul>
                </div>',
                'class' => 'text-center pe-4'
            ];

            $rows[] = $row;
        }

        $headers = [
            ['content' => 'Txn ID / Ref', 'class' => 'ps-4 py-3'],
            ['content' => 'Type'],
            ['content' => 'Description'],
            ['content' => 'Gross Amount'],
            ['content' => 'Fee'],
            ['content' => 'Tax'],
            ['content' => 'Net Amount'],
            ['content' => 'Method / Status'],
            ['content' => 'Date'],
            ['content' => 'Action', 'class' => 'pe-4 text-center']
        ];
        $headers = Hooks::filter('nixomers_transactions_table_headers', $headers);

        return Ajax::response([
            'status' => 'success',
            'headers' => $headers,
            'data' => $rows,
            'total' => $total,
            'limit' => $num,
            'offset' => $offset
        ]);
    }

    /**
     * AJAX Endpoint for Notification Polling
     */
    public function notifications($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }

        $userGroup = (int) Session::val('group');
        $username = Session::val('username');
        
        // Map user group to roles they can see
        $allowedRoles = ['all'];
        if ($userGroup <= 1) $allowedRoles = array_merge($allowedRoles, ['admin', 'billing', 'fulfillment', 'cs', 'sales']);
        elseif ($userGroup == 2) $allowedRoles[] = 'billing';
        elseif ($userGroup == 3) $allowedRoles[] = 'fulfillment';
        elseif ($userGroup == 4) $allowedRoles[] = 'cs';
        elseif ($userGroup == 5) $allowedRoles[] = 'sales';

        $rolesSql = "'" . implode("','", $allowedRoles) . "'";

        // Count unread notifications
        $newNotifs = Db::$pdo->query("SELECT COUNT(n.id) FROM nix_notifications n 
            LEFT JOIN nix_notifications_read nr ON n.id = nr.notification_id AND nr.username = '$username'
            WHERE n.target_role IN ($rolesSql) AND nr.id IS NULL")->fetchColumn();

        $latest = Db::$pdo->query("SELECT n.* FROM nix_notifications n 
            LEFT JOIN nix_notifications_read nr ON n.id = nr.notification_id AND nr.username = '$username'
            WHERE n.target_role IN ($rolesSql) AND nr.id IS NULL 
            ORDER BY n.id DESC LIMIT 5")->fetchAll(PDO::FETCH_OBJ);

        $formatted = [];
        foreach ($latest as $n) {
            $formatted[] = [
                'title' => $n->title,
                'message' => $n->message,
                'time' => date('H:i', strtotime($n->created_at)),
                'url' => Site::$url . 'gxadmin/' . $n->url . '&mark_read=' . $n->id
            ];
        }

        return Ajax::response([
            'count' => (int) $newNotifs,
            'latest' => $formatted
        ]);
    }

    /**
     * AJAX Endpoint for Cart Count
     */
    public function cart_count($param = null)
    {
        return Ajax::response([
            'count' => (int) NixCart::count()
        ]);
    }

    /**
     * AJAX Endpoint for Print Label
     */
    public function print_label($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }
        require_once GX_MOD . 'nixomers/options/printlabel.php';
        exit;
    }

    /**
     * AJAX Endpoint for Print Invoice
     */
    public function print_invoice($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }
        require_once GX_MOD . 'nixomers/options/printinvoice.php';
        exit;
    }

    /**
     * AJAX Endpoint for Print Payment Proof
     */
    public function print_payment_proof($param = null)
    {
        if (!Ajax::auth($param)) {
            return Ajax::error(401, 'Unauthorized');
        }
        require_once GX_MOD . 'nixomers/options/printproof.php';
        exit;
    }

    /**
     * AJAX Endpoint for Store Catalog
     */
    public function store_catalog($param = null)
    {
        return NixCatalog::ajaxCatalog();
    }

    /**
     * AJAX Endpoint for Cascading Region Selection
     */
    public function shipping_regions($param = null)
    {
        $type = Typo::cleanX($_GET['type'] ?? '');
        $id = Typo::cleanX($_GET['parent'] ?? $_GET['id'] ?? '');
        $engine = Options::v('nix_shipping_engine') ?: 'kiriminaja';

        if ($engine === 'apicoid') {
            return NixShipping::ajaxSearchRegion();
        }

        // KiriminAja Cascading
        $token = Options::v('nix_kiriminaja_token');
        $mode = Options::v('nix_kiriminaja_mode') ?: 'sandbox';
        if (empty($token)) {
            return Ajax::error(400, 'KiriminAja Token not configured');
        }

        \KiriminAja\Base\Config\KiriminAjaConfig::setApiTokenKey($token);
        \KiriminAja\Base\Config\KiriminAjaConfig::setMode($mode === 'sandbox' ? \KiriminAja\Base\Config\Cache\Mode::Staging : \KiriminAja\Base\Config\Cache\Mode::Production);

        try {
            $res = null;
            if ($type === 'province') {
                $svc = new \KiriminAja\Services\Address\ProvinceService();
                $res = $svc->call();
            } elseif ($type === 'city' && $id) {
                $svc = new \KiriminAja\Services\Address\CityService((int) $id);
                $res = $svc->call();
            } elseif ($type === 'district' && $id) {
                $svc = new \KiriminAja\Services\Address\DistrictService((int) $id);
                $res = $svc->call();
            } elseif ($type === 'village') {
                return Ajax::response([]);
            }

            if ($res && $res->status) {
                $norm = [];
                foreach ($res->data as $item) {
                    $itemId = $item['id'] ?? $item['kabupaten_id'] ?? $item['kecamatan_id'] ?? null;
                    if (!$itemId) {
                        if ($type === 'province') $itemId = $item['province_id'] ?? $item['id'];
                        if ($type === 'city') $itemId = $item['city_id'] ?? $item['kabupaten_id'] ?? $item['id'];
                        if ($type === 'district') $itemId = $item['district_id'] ?? $item['kecamatan_id'] ?? $item['id'];
                    }
                    $norm[] = ['id' => $itemId, 'name' => $item['name']];
                }
                return Ajax::response($norm);
            } else {
                return Ajax::error(500, $res->message ?? 'API Error');
            }
        } catch (\Exception $e) {
            return Ajax::error(500, $e->getMessage());
        }
    }

    /**
     * AJAX Endpoint for Search Village / Region Search (Global Search)
     */
    public function search_village($param = null)
    {
        $q = Typo::cleanX($_GET['q'] ?? '');
        if (strlen($q) < 3) {
            return Ajax::error(400, 'Minimum 3 characters');
        }

        $engine = Options::v('nix_shipping_engine') ?: 'kiriminaja';
        if ($engine === 'apicoid') {
            return NixShipping::ajaxSearchRegion(); // This should be updated to handle search if it doesn't
        }

        // Fallback or KiriminAja search
        return $this->search_district($param);
    }

    /**
     * AJAX Endpoint for KiriminAja District Search
     */
    public function search_district($param = null)
    {
        $q = Typo::cleanX($_GET['q'] ?? '');
        if (strlen($q) < 3) {
            return Ajax::error(400, 'Minimum 3 characters');
        }

        $token = Options::v('nix_kiriminaja_token');
        $mode = Options::v('nix_kiriminaja_mode') ?: 'sandbox';

        if (empty($token)) {
            return Ajax::error(400, 'KiriminAja API Token not configured');
        }

        try {
            \KiriminAja\Base\Config\KiriminAjaConfig::setApiTokenKey($token);
            \KiriminAja\Base\Config\KiriminAjaConfig::setMode($mode === 'sandbox' ? \KiriminAja\Base\Config\Cache\Mode::Staging : \KiriminAja\Base\Config\Cache\Mode::Production);

            $service = new \KiriminAja\Services\Address\DistrictByNameService($q);
            $response = $service->call();

            if ($response->status) {
                return Ajax::response($response->data);
            } else {
                return Ajax::error(500, $response->message);
            }
        } catch (\Exception $e) {
            return Ajax::error(500, $e->getMessage());
        }
    }

    /**
     * AJAX Endpoint for Shipping Rates
     */
    public function shipping_rates($param = null)
    {
        return NixShipping::ajaxFetchRates();
    }

}
