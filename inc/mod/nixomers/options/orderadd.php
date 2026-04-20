<?php
/**
 * Nixomers Manual Order Creation View
 * Enhanced with full address form & shipping method selector
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// Handle Submission
if (isset($_POST['save_manual_order'])) {
    if (!Token::validate($_POST['token'])) {
        $GLOBALS['alertDanger'][] = _("Invalid Security Token.");
    } else {
        $customerName    = Typo::cleanX($_POST['customer_name']);
        $customerEmail   = Typo::cleanX($_POST['customer_email']);
        $customerPhone   = Typo::cleanX($_POST['customer_phone']);

        // Build full shipping address from components
        $provinceName  = Typo::cleanX($_POST['province_name'] ?? '');
        $cityName      = Typo::cleanX($_POST['city_name'] ?? '');
        $districtName  = Typo::cleanX($_POST['district_name'] ?? '');
        $villageName   = Typo::cleanX($_POST['village_name'] ?? '');
        $streetAddress = Typo::cleanX($_POST['street_address'] ?? '');

        $shippingAddress = trim(implode(', ', array_filter([
            $streetAddress, $villageName, $districtName, $cityName, $provinceName
        ])));

        // Shipping details
        $shippingType    = Typo::cleanX($_POST['shipping_type'] ?? 'pickup');
        $shippingCourier = Typo::cleanX($_POST['shipping_courier'] ?? '');
        $shippingService = Typo::cleanX($_POST['shipping_service'] ?? '');
        $shippingEtd     = Typo::cleanX($_POST['shipping_etd'] ?? '');
        $shippingEngine  = Typo::cleanX($_POST['shipping_engine'] ?? '');

        // Parse Items
        $productIds  = $_POST['product_id'] ?? [];
        $productQtys = $_POST['product_qty'] ?? [];

        $cartItems = [];
        $subtotal  = 0;

        foreach ($productIds as $k => $pId) {
            $pId = (int) $pId;
            $qty = (int) ($productQtys[$k] ?? 1);
            if ($pId > 0 && $qty > 0) {
                $price = (float) Posts::getParam('price', $pId);
                $cartItems[$pId] = $qty;
                $subtotal += ($price * $qty);
            }
        }

        $taxRate     = (float) Options::v('nixomers_tax') ?: 0;
        $tax         = ($subtotal * $taxRate) / 100;
        $shippingCost = 0;

        if ($shippingType === 'courier') {
            $shippingCost = (float) ($_POST['shipping_cost'] ?? 0);
        } elseif ($shippingType === 'drop') {
            $shippingCost = (float) ($_POST['shipping_cost_manual'] ?? 0);
        }
        // pickup = 0

        $total = $subtotal + $tax + $shippingCost;

        // Generate Order ID
        $orderId = Nixomers::generateId('invoice');

        // Deduct Stock immediately since this is manual admin entry
        foreach ($cartItems as $pid => $qty) {
            NixInventory::deduct($pid, $qty, $orderId, 'POS/Manual Order Entry');
        }

        $paymentMethod = Typo::cleanX($_POST['payment_method'] ?? 'manual');
        $paymentStatus = Typo::cleanX($_POST['payment_status'] ?? 'pending');
        $orderStatus   = ($paymentStatus === 'paid') ? 'waiting' : 'pending';

        $data = [
            'order_id'         => $orderId,
            'customer_name'    => $customerName,
            'customer_email'   => $customerEmail,
            'customer_phone'   => $customerPhone,
            'shipping_province' => $provinceName,
            'shipping_city'    => $cityName,
            'shipping_district' => $districtName,
            'shipping_village' => $villageName,
            'shipping_street'  => $streetAddress,
            'shipping_address' => $shippingAddress,
            'shipping_courier' => $shippingType === 'courier' ? $shippingCourier : strtoupper($shippingType),
            'shipping_service' => $shippingType === 'courier' ? $shippingService : ($shippingType === 'pickup' ? 'Self Pickup' : 'Store Drop'),
            'shipping_etd'     => $shippingEtd,
            'shipping_engine'  => $shippingEngine,
            'shipping_cost'    => $shippingCost,
            'cart_items'       => json_encode($cartItems),
            'subtotal'         => $subtotal,
            'tax'              => $tax,
            'total'            => $total,
            'status'           => $orderStatus,
            'payment_method'   => $paymentMethod,
            'date'             => date('Y-m-d H:i:s')
        ];

        Query::table('nix_orders')->insert($data);

        // Trigger hook for new order submission
        Hooks::run('nix_order_submitted', $orderId, $data);

        // Initial Transaction Entry
        $fee = 0;
        $net = $total - $fee - $tax - $shippingCost;

        $trx_id = Nixomers::generateId('trx');

        Query::table('nix_transactions')->insert([
            'trans_id'      => $trx_id,
            'order_id'      => $orderId,
            'type'          => 'income',
            'amount'        => $total,
            'fee'           => $fee,
            'tax'           => $tax,
            'shipping_cost' => $shippingCost,
            'net'           => $net,
            'description'   => 'Manual Order POS / Entry: ' . $orderId,
            'method'        => $paymentMethod,
            'status'        => $paymentStatus,
            'paid_date'     => ($paymentStatus === 'paid') ? date('Y-m-d H:i:s') : null,
            'date'          => date('Y-m-d H:i:s')
        ]);

        // Initial Audit Log
        Query::table('nix_order_logs')->insert([
            'order_id'   => $orderId,
            'old_status' => 'SYSTEM',
            'new_status' => 'ORDER: CREATED_MANUALLY (Shipping: ' . strtoupper($shippingType) . ')',
            'updated_by' => Session::val('username') ?: 'admin',
            'date'       => date('Y-m-d H:i:s')
        ]);

        $GLOBALS['alertSuccess'] = _("Order {$orderId} has been successfully created!");
        Http::redirect($mod_url . '&sel=orderdetail&id=' . $orderId);
    }
}

// Fetch available products
$products = Query::table('posts')
    ->select('id, title')
    ->where('type', 'nixomers')
    ->where('status', '1')
    ->orderBy('title', 'ASC')
    ->get();

$prodDataJS = [];
foreach ($products as $p) {
    $price  = (float) Posts::getParam('price', $p->id);
    $stock  = (int)   Posts::getParam('stock', $p->id);
    $weight = (float) (Posts::getParam('weight', $p->id) ?: 0);
    $cleanTitle = htmlspecialchars_decode(Typo::Xclean($p->title), ENT_QUOTES);
    $prodDataJS[$p->id] = ['price' => $price, 'title' => $cleanTitle, 'stock' => $stock, 'weight' => $weight];
}

$currency = Options::v('nixomers_currency') ?: 'IDR';
$taxRate  = (float) Options::v('nixomers_tax') ?: 0;

$gateways = Hooks::filter('nix_payment_gateways', [
    'manual'       => ['name' => 'Cash / Manual POS'],
    'bank_transfer' => ['name' => 'Bank Transfer'],
]);
$enabledStr = Options::v('nix_enabled_gateways') ?: 'bank_transfer';
$enabled    = explode(',', $enabledStr);

$pmOptions = '<option value="manual">Cash / Manual POS</option>';
foreach ($gateways as $k => $v) {
    if (!in_array($k, $enabled) && $k !== 'manual') continue;
    if ($k !== 'manual') {
        $pmOptions .= '<option value="' . $k . '">' . $v['name'] . '</option>';
    }
}

// Shipping rates AJAX URL (same as frontend)
$shippingRatesUrl  = Url::ajax('api', ['action' => 'shipping_rates']);
$shippingRegionUrl = Url::ajax('api', ['action' => 'shipping_regions']);

$schema = [
    'header' => [
        'title'    => 'Manual Order Creation',
        'subtitle' => 'Draft a new POS order or record offline transactions.',
        'icon'     => 'bi bi-plus-square-dotted',
        'button'   => [
            'type'  => 'link',
            'href'  => $mod_url . '&sel=orders',
            'label' => 'Back to Ledger',
            'icon'  => 'bi bi-arrow-left',
            'class' => 'btn btn-light rounded-pill px-4 border shadow-sm'
        ]
    ],
    'content' => [
        [
            'type'   => 'form',
            'action' => '',
            'method' => 'POST',
            'fields' => [
                [
                    'type'  => 'row',
                    'items' => [
                        [
                            'width'   => 8,
                            'content' => [
                                'type'          => 'card',
                                'title'         => 'Cart Items',
                                'icon'          => 'bi bi-basket',
                                'body_elements' => [
                                    [
                                        'type' => 'raw',
                                        'html' => '
                                        <div id="orderItemsContainer" class="mb-4">
                                            <!-- Dynamic items go here -->
                                        </div>
                                        <button type="button" class="btn btn-outline-primary rounded-pill btn-sm fw-bold px-4 border-2" onclick="addOrderItem()"><i class="bi bi-cart-plus me-1"></i> Add Product Row</button>

                                        <hr class="my-4">
                                        <div class="row text-end align-items-center">
                                            <div class="col-8">
                                                <div class="text-muted small fw-bold text-uppercase tracking-widest">Calculated Subtotal</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="h4 fw-black text-primary mb-0">' . $currency . ' <span id="displaySubtotal">0</span></div>
                                            </div>
                                        </div>
                                        '
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width'   => 4,
                            'content' => [
                                'type'          => 'card',
                                'title'         => 'Order Summary',
                                'icon'          => 'bi bi-receipt',
                                'body_elements' => [
                                    [
                                        'type' => 'raw',
                                        'html' => '
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Subtotal</span>
                                            <span class="fw-bold" id="sum_subtotal">' . $currency . ' 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span class="text-muted">Tax (' . $taxRate . '%)</span>
                                            <span class="fw-bold" id="sum_tax">' . $currency . ' 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 small" id="sum_ship_row" style="display:none!important;">
                                            <span class="text-muted">Shipping</span>
                                            <span class="fw-bold text-primary" id="sum_shipping">' . $currency . ' 0</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Total</span>
                                            <span class="h4 fw-black text-primary mb-0" id="sum_total">' . $currency . ' 0</span>
                                        </div>
                                        '
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type'  => 'row',
                    'items' => [
                        [
                            'width'   => 8,
                            'content' => [
                                'type'          => 'card',
                                'title'         => 'Customer & Shipping Details',
                                'icon'          => 'bi bi-person-badge',
                                'body_elements' => [
                                    [
                                        'type' => 'raw',
                                        'html' => '
                                        <!-- Customer Info -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-muted text-uppercase">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="customer_name" class="form-control" placeholder="Customer name" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-muted text-uppercase">Phone / WA</label>
                                                <input type="text" name="customer_phone" class="form-control" placeholder="+62...">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-muted text-uppercase">Email</label>
                                                <input type="email" name="customer_email" class="form-control" placeholder="email@example.com">
                                            </div>
                                        </div>

                                        <!-- Shipping Type Selector -->
                                        <div class="mb-4">
                                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Delivery Method</label>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <label class="flex-fill">
                                                    <input type="radio" name="shipping_type" value="courier" class="btn-check" id="st_courier" onchange="onShippingTypeChange(this)">
                                                    <span class="btn btn-outline-primary w-100 rounded-3 py-3"><i class="bi bi-truck me-1"></i> Via Courier</span>
                                                </label>
                                                <label class="flex-fill">
                                                    <input type="radio" name="shipping_type" value="drop" class="btn-check" id="st_drop" onchange="onShippingTypeChange(this)">
                                                    <span class="btn btn-outline-warning w-100 rounded-3 py-3 text-dark"><i class="bi bi-box-seam me-1"></i> Store Drop/Delivery</span>
                                                </label>
                                                <label class="flex-fill">
                                                    <input type="radio" name="shipping_type" value="pickup" class="btn-check" id="st_pickup" checked onchange="onShippingTypeChange(this)">
                                                    <span class="btn btn-outline-success w-100 rounded-3 py-3"><i class="bi bi-shop me-1"></i> Self Pickup (Free)</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Pickup notice -->
                                        <div id="block_pickup" class="alert alert-success border-0 rounded-4 small">
                                            <i class="bi bi-check-circle-fill me-2"></i> Customer picks up in store. <strong>Shipping cost: FREE (0)</strong>
                                            <input type="hidden" name="shipping_cost" value="0">
                                        </div>

                                        <!-- SHARED: Full Address Form (shown for Courier AND Drop) -->
                                        <div id="block_address" class="d-none mb-4">
                                            <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-3">
                                                <i class="bi bi-geo-alt text-primary"></i>
                                                <span class="fw-bold">Shipping Address</span>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small text-muted text-uppercase fw-bold" style="font-size:10px;">Province</label>
                                                    <select name="province" id="pos_province" class="form-select border-0 bg-light rounded-3 shadow-none">
                                                        <option value="">Select Province...</option>
                                                    </select>
                                                    <input type="hidden" name="province_name" id="pos_province_name">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small text-muted text-uppercase fw-bold" style="font-size:10px;">City / Regency</label>
                                                    <select name="city" id="pos_city" class="form-select border-0 bg-light rounded-3 shadow-none" disabled>
                                                        <option value="">Select City...</option>
                                                    </select>
                                                    <input type="hidden" name="city_name" id="pos_city_name">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small text-muted text-uppercase fw-bold" style="font-size:10px;">District</label>
                                                    <select name="district" id="pos_district" class="form-select border-0 bg-light rounded-3 shadow-none" disabled>
                                                        <option value="">Select District...</option>
                                                    </select>
                                                    <input type="hidden" name="district_name" id="pos_district_name">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small text-muted text-uppercase fw-bold" style="font-size:10px;">Village</label>
                                                    <select name="village" id="pos_village" class="form-select border-0 bg-light rounded-3 shadow-none" disabled>
                                                        <option value="">Select Village...</option>
                                                    </select>
                                                    <input type="hidden" name="village_name" id="pos_village_name">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small text-muted text-uppercase fw-bold" style="font-size:10px;">Street Address / Detail</label>
                                                    <textarea name="street_address" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Jl. ..., RT/RW, No. ..."></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Drop: Manual Shipping Cost (only for Drop mode) -->
                                        <div id="block_drop_cost" class="d-none mb-3">
                                            <div class="alert alert-warning border-0 rounded-4 mb-3 small">
                                                <i class="bi bi-info-circle-fill me-2"></i> Store delivers directly. Enter shipping cost manually.
                                            </div>
                                            <label class="form-label small fw-bold text-muted text-uppercase">Shipping Cost (' . $currency . ')</label>
                                            <input type="number" name="shipping_cost_manual" id="drop_shipping_cost" class="form-control" value="0" min="0" step="1000" onchange="updateSummary()">
                                        </div>

                                        <!-- Courier rates (only for Courier mode) -->
                                        <div id="block_courier" class="d-none">
                                            <!-- Shipping Rates Section -->
                                            <div id="pos_shipping_section" class="d-none mt-3">
                                                <div class="d-flex align-items-center gap-2 border-bottom pb-2 mb-3">
                                                    <i class="bi bi-truck text-primary"></i>
                                                    <span class="fw-bold">Available Courier Services</span>
                                                </div>
                                                <div id="pos_shipping_loader" class="d-none align-items-center gap-2 text-muted small py-2">
                                                    <div class="spinner-border spinner-border-sm text-primary"></div> Fetching shipping rates...
                                                </div>
                                                <div id="pos_shipping_rates" class="d-flex flex-column gap-2"></div>

                                                <input type="hidden" name="shipping_courier" id="pos_courier_hidden">
                                                <input type="hidden" name="shipping_service" id="pos_service_hidden">
                                                <input type="hidden" name="shipping_cost"    id="pos_cost_hidden" value="0">
                                                <input type="hidden" name="shipping_etd"     id="pos_etd_hidden">
                                                <input type="hidden" name="shipping_engine"  id="pos_engine_hidden">
                                            </div>
                                        </div>
                                        '
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width'   => 4,
                            'content' => [
                                'type'          => 'card',
                                'title'         => 'Payment Info',
                                'icon'          => 'bi bi-wallet2',
                                'body_elements' => [
                                    [
                                        'type' => 'raw',
                                        'html' => '
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Payment Method</label>
                                            <select name="payment_method" class="form-select border-0 bg-light rounded-3 fw-bold shadow-none">
                                                ' . $pmOptions . '
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Payment Status</label>
                                            <select name="payment_status" class="form-select border-0 bg-light rounded-3 fw-bold text-primary shadow-none">
                                                <option value="pending">Pending</option>
                                                <option value="paid">Mark as Paid</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-light border rounded-3 small mt-3">
                                            <i class="bi bi-info-circle me-1"></i> If <strong>Paid</strong>, order moves to <strong>Waiting Process</strong> status automatically.
                                        </div>
                                        '
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'raw',
                    'html' => '<div class="text-end mt-2"><input type="hidden" name="token" value="' . TOKEN . '"><button type="submit" name="save_manual_order" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm py-3"><i class="bi bi-save me-2"></i> Register New Order</button></div>'
                ]
            ]
        ]
    ]
];

$ui = new UiBuilder($schema);
echo $ui->render();
?>

<script>
    const inventoryData = <?php echo json_encode($prodDataJS); ?>;
    const currencySym = '<?php echo $currency; ?>';
    const taxRate = <?php echo (float) $taxRate; ?>;
    const shippingRatesUrl  = '<?php echo $shippingRatesUrl; ?>';
    const shippingRegionUrl = '<?php echo $shippingRegionUrl; ?>';

    // Store origin codes (from Nixomers Settings > Shipping Origin)
    const storeProvCode = '<?php echo Options::v("nix_orig_province") ?: ""; ?>';
    const storeCityCode = '<?php echo Options::v("nix_orig_city") ?: ""; ?>';

    // ─── SHIPPING TYPE TOGGLE ─────────────────────────────────────────
    function onShippingTypeChange(radio) {
        // Hide all blocks first
        document.getElementById('block_pickup').classList.add('d-none');
        document.getElementById('block_address').classList.add('d-none');
        document.getElementById('block_drop_cost').classList.add('d-none');
        document.getElementById('block_courier').classList.add('d-none');

        if (radio.value === 'pickup') {
            document.getElementById('block_pickup').classList.remove('d-none');
        } else if (radio.value === 'drop') {
            // Drop: Show address form + manual cost input + prefill store location
            document.getElementById('block_address').classList.remove('d-none');
            document.getElementById('block_drop_cost').classList.remove('d-none');
            prefillStoreAddress(); // Auto-fill Province + City from store settings
        } else if (radio.value === 'courier') {
            // Courier: Show address form + API rate fetcher
            document.getElementById('block_address').classList.remove('d-none');
            document.getElementById('block_courier').classList.remove('d-none');
            loadProvinces();
        }
        updateSummary();
    }

    /**
     * Async: Pre-fill Province + City from store settings when Drop mode is selected.
     * District and Village are intentionally left blank for manual customisation.
     */
    async function prefillStoreAddress() {
        await loadProvinces();

        const selProv = document.getElementById('pos_province');
        const selCity = document.getElementById('pos_city');
        const selDist = document.getElementById('pos_district');
        const selVill = document.getElementById('pos_village');
        if (!selProv) return;

        // ── Select Province ──────────────────────────────────────────
        let provMatched = false;
        if (storeProvCode) {
            for (let opt of selProv.options) {
                if (opt.value === storeProvCode) {
                    selProv.value = storeProvCode;
                    document.getElementById('pos_province_name').value = opt.dataset.name || opt.text;
                    provMatched = true;
                    break;
                }
            }
        }
        if (!provMatched) return; // Province not configured — skip city prefill

        // ── Load Cities ───────────────────────────────────────────────
        selCity.innerHTML = '<option value="">Loading...</option>';
        selCity.disabled = true;
        selDist.innerHTML = '<option value="">Select...</option>';
        selVill.innerHTML = '<option value="">Select...</option>';
        selVill.disabled = selDist.disabled = true;

        const cities = await fetchRegions('city', storeProvCode);
        selCity.innerHTML = '<option value="">Select City/Regency...</option>';
        cities.forEach(r => {
            const o = new Option(r.name, r.id);
            o.dataset.name = r.name;
            selCity.add(o);
        });
        selCity.disabled = false;

        // ── Auto-select Store City ────────────────────────────────────
        if (storeCityCode) {
            for (let opt of selCity.options) {
                if (opt.value === storeCityCode) {
                    selCity.value = storeCityCode;
                    document.getElementById('pos_city_name').value = opt.dataset.name || opt.text;
                    // Enable district dropdown but leave it unselected (user customises)
                    selDist.innerHTML = '<option value="">Select District...</option>';
                    selDist.disabled = false;
                    // Pre-load district options for faster UX
                    fetchRegions('district', storeCityCode).then(districts => {
                        selDist.innerHTML = '<option value="">Select District...</option>';
                        districts.forEach(r => {
                            const o = new Option(r.name, r.id);
                            o.dataset.name = r.name;
                            selDist.add(o);
                        });
                    });
                    break;
                }
            }
        }
    }

    // ─── REGION DROPDOWNS ────────────────────────────────────────────
    let provincesLoaded = false;
    async function fetchRegions(type, parentId = '') {
        const res  = await fetch(`${shippingRegionUrl}&type=${type}&parent=${parentId}`);
        const data = await res.json();
        if (data.is_success) return data.data;
        return [];
    }

    async function loadProvinces() {
        if (provincesLoaded) return;
        const selProv = document.getElementById('pos_province');
        selProv.innerHTML = '<option value="">Loading...</option>';
        const regions = await fetchRegions('province');
        selProv.innerHTML = '<option value="">Select Province...</option>';
        regions.forEach(r => {
            const opt = new Option(r.name, r.id);
            opt.dataset.name = r.name;
            selProv.add(opt);
        });
        provincesLoaded = true;
    }

    document.getElementById('pos_province').addEventListener('change', async function() {
        const selCity = document.getElementById('pos_city');
        const selDist = document.getElementById('pos_district');
        const selVill = document.getElementById('pos_village');
        selDist.innerHTML = selVill.innerHTML = '<option value="">Select...</option>';
        selDist.disabled = selVill.disabled = true;
        document.getElementById('pos_province_name').value = this.options[this.selectedIndex]?.dataset.name || '';

        selCity.innerHTML = '<option value="">Loading...</option>';
        selCity.disabled = false;
        const regions = await fetchRegions('city', this.value);
        selCity.innerHTML = '<option value="">Select City/Regency...</option>';
        regions.forEach(r => { const o = new Option(r.name, r.id); o.dataset.name = r.name; selCity.add(o); });
    });

    document.getElementById('pos_city').addEventListener('change', async function() {
        const selDist = document.getElementById('pos_district');
        const selVill = document.getElementById('pos_village');
        selVill.innerHTML = '<option value="">Select...</option>';
        selVill.disabled = true;
        document.getElementById('pos_city_name').value = this.options[this.selectedIndex]?.dataset.name || '';

        selDist.innerHTML = '<option value="">Loading...</option>';
        selDist.disabled = false;
        const regions = await fetchRegions('district', this.value);
        selDist.innerHTML = '<option value="">Select District...</option>';
        regions.forEach(r => { const o = new Option(r.name, r.id); o.dataset.name = r.name; selDist.add(o); });
    });

    document.getElementById('pos_district').addEventListener('change', async function() {
        const selVill = document.getElementById('pos_village');
        document.getElementById('pos_district_name').value = this.options[this.selectedIndex]?.dataset.name || '';

        selVill.innerHTML = '<option value="">Loading...</option>';
        selVill.disabled = false;
        const regions = await fetchRegions('village', this.value);
        selVill.innerHTML = '<option value="">Select Village...</option>';
        regions.forEach(r => { const o = new Option(r.name, r.id); o.dataset.name = r.name; selVill.add(o); });
    });

    document.getElementById('pos_village').addEventListener('change', function() {
        document.getElementById('pos_village_name').value = this.options[this.selectedIndex]?.dataset.name || '';
        if (this.value) fetchShippingRates(this.value);
    });

    // ─── SHIPPING RATE FETCHER ────────────────────────────────────────
    function getTotalWeight() {
        let weight = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const pId = row.querySelector('.product-select').value;
            const qty = parseInt(row.querySelector('.product-qty').value) || 0;
            if (pId && inventoryData[pId]) {
                weight += (inventoryData[pId].weight || 0) * qty;
            }
        });
        return weight < 10 ? 1000 : weight;
    }

    let rateTimer;
    function fetchShippingRates(villageId) {
        const section = document.getElementById('pos_shipping_section');
        const loader  = document.getElementById('pos_shipping_loader');
        const list    = document.getElementById('pos_shipping_rates');
        section.classList.remove('d-none');
        loader.classList.remove('d-none');
        loader.classList.add('d-flex');
        list.innerHTML = '';

        clearTimeout(rateTimer);
        rateTimer = setTimeout(() => {
            const weight = getTotalWeight();
            fetch(`${shippingRatesUrl}&village_id=${villageId}&weight=${weight}`)
            .then(r => r.json())
            .then(res => {
                loader.classList.add('d-none');
                loader.classList.remove('d-flex');
                if (res.status && res.data && res.data.length > 0) {
                    list.innerHTML = res.data.map(s => {
                        const rid = s.courier_name + '-' + s.service_name;
                        return `<label class="d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer w-100" style="cursor:pointer;">
                            <input type="radio" name="shipping_option" value="${rid}" class="form-check-input flex-shrink-0" required
                                data-cost="${s.cost}" data-courier="${s.courier_name}" data-service="${s.service_name}"
                                data-etd="${s.etd}" data-engine="${s.engine || 'api'}">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong>${s.courier_name} - ${s.service_name}</strong>
                                    <strong class="text-primary">${currencySym} ${Number(s.cost).toLocaleString('id-ID')}</strong>
                                </div>
                                <small class="text-muted">ETD: ${s.etd} days</small>
                            </div>
                        </label>`;
                    }).join('');

                    list.querySelectorAll('input[name="shipping_option"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            document.getElementById('pos_courier_hidden').value = this.dataset.courier;
                            document.getElementById('pos_service_hidden').value = this.dataset.service;
                            document.getElementById('pos_cost_hidden').value    = this.dataset.cost;
                            document.getElementById('pos_etd_hidden').value     = this.dataset.etd;
                            document.getElementById('pos_engine_hidden').value  = this.dataset.engine;
                            updateSummary();
                        });
                    });
                } else {
                    list.innerHTML = `<div class="alert alert-warning small"><i class="bi bi-exclamation-triangle me-2"></i> ${res.message || 'No shipping rates available for this destination.'}</div>`;
                }
            }).catch(() => {
                loader.classList.add('d-none');
                list.innerHTML = '<div class="alert alert-danger small">Failed to fetch shipping rates.</div>';
            });
        }, 500);
    }

    // ─── CART PRODUCT ROWS ────────────────────────────────────────────
    function addOrderItem() {
        const container = document.getElementById('orderItemsContainer');
        const id = 'item_' + Date.now();

        const html = `
        <div class="row g-2 mb-3 align-items-end rounded-4 bg-light p-2 border item-row" id="${id}">
            <div class="col-md-6 position-relative">
                <label class="small text-muted fw-bold mb-1">Search Product</label>
                <input type="text" class="form-control border-0 shadow-none product-search" placeholder="Type to search..." autocomplete="off" onkeyup="filterProducts(this, '${id}')" onfocus="filterProducts(this, '${id}')">
                <div class="dropdown-menu w-100 shadow-sm border-0 mt-1 product-dropdown" style="max-height: 250px; overflow-y: auto;"></div>
                <input type="hidden" name="product_id[]" class="product-select" onchange="calculateTotals()">
            </div>
            <div class="col-md-2">
                <label class="small text-muted fw-bold mb-1">Qty <span class="stock-badge ms-1 small"></span></label>
                <input type="number" name="product_qty[]" class="form-control border-0 shadow-none product-qty" value="1" min="1" onchange="calculateTotals()" onkeyup="calculateTotals()">
            </div>
            <div class="col-md-3 text-end pb-2">
                <div class="small text-muted fw-bold mb-1">Line Total</div>
                <div class="fw-bold text-dark line-total">${currencySym} 0</div>
            </div>
            <div class="col-md-1 text-center pb-2">
                <button type="button" class="btn btn-white text-danger btn-sm rounded-circle p-1" onclick="document.getElementById('${id}').remove(); calculateTotals();"><i class="bi bi-trash"></i></button>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
    }

    function filterProducts(input, rowId) {
        const dropdown = input.nextElementSibling;
        const val = input.value.toLowerCase();
        let chunks = [], count = 0;

        for (const [pId, data] of Object.entries(inventoryData)) {
            if (val === '' || data.title.toLowerCase().includes(val)) {
                const stockLabel = data.stock > 0
                    ? `<span class="badge bg-success bg-opacity-10 text-success ms-2 px-2">Stock: ${data.stock}</span>`
                    : `<span class="badge bg-danger bg-opacity-10 text-danger ms-2 px-2">Out of Stock</span>`;
                chunks.push(`<a class="dropdown-item py-2 border-bottom" href="javascript:void(0)" onclick="selectProduct('${rowId}', '${pId}')">
                    <div class="fw-bold text-dark text-truncate">${data.title} ${stockLabel}</div>
                    <small class="text-muted">${currencySym} ${Number(data.price).toLocaleString('id-ID')}</small>
                </a>`);
                if (++count >= 20) break;
            }
        }
        dropdown.innerHTML = chunks.length ? chunks.join('') : '<div class="px-3 py-2 text-muted small">No products found.</div>';
        dropdown.style.display = 'block';
    }

    function selectProduct(rowId, pId) {
        const row    = document.getElementById(rowId);
        const input  = row.querySelector('.product-search');
        const hidden = row.querySelector('.product-select');
        const drop   = row.querySelector('.product-dropdown');
        const qty    = row.querySelector('.product-qty');

        if (inventoryData[pId]) input.value = inventoryData[pId].title;
        hidden.value = pId;
        drop.style.display = 'none';
        qty.value = 1;
        calculateTotals();
    }

    document.addEventListener('click', function(e) {
        if (!e.target.matches('.product-search')) {
            document.querySelectorAll('.product-dropdown').forEach(d => d.style.display = 'none');
        }
    });

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const select   = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.product-qty');
            const lineEl   = row.querySelector('.line-total');
            const badge    = row.querySelector('.stock-badge');
            const pId  = select.value;
            let qty    = parseInt(qtyInput.value) || 0;
            let line   = 0;

            if (pId && inventoryData[pId]) {
                const stock = inventoryData[pId].stock;
                if (stock <= 0) {
                    badge.innerHTML = `<span class="text-danger fw-bold">(Out!)</span>`;
                    qty = 0; qtyInput.value = 0;
                    qtyInput.classList.add('border-danger', 'text-danger');
                } else if (qty > stock) {
                    badge.innerHTML = `<span class="text-warning fw-bold">(Max: ${stock})</span>`;
                    qty = stock; qtyInput.value = stock;
                    qtyInput.classList.remove('border-danger', 'text-danger');
                } else {
                    badge.innerHTML = `<span class="text-success">(${stock})</span>`;
                    qtyInput.classList.remove('border-danger', 'text-danger');
                }
                line = inventoryData[pId].price * qty;
            }
            subtotal += line;
            lineEl.innerHTML = currencySym + ' ' + Number(line).toLocaleString('id-ID');
        });

        document.getElementById('displaySubtotal').innerHTML = Number(subtotal).toLocaleString('id-ID');
        updateSummary(subtotal);
    }

    function updateSummary(forcedSubtotal) {
        let subtotal = forcedSubtotal !== undefined ? forcedSubtotal : (() => {
            let s = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const pId = row.querySelector('.product-select').value;
                const qty = parseInt(row.querySelector('.product-qty').value) || 0;
                if (pId && inventoryData[pId]) s += inventoryData[pId].price * qty;
            });
            return s;
        })();

        const tax  = subtotal * (taxRate / 100);
        let   ship = 0;

        const activeType = document.querySelector('input[name="shipping_type"]:checked')?.value || 'pickup';
        if (activeType === 'courier') {
            ship = parseFloat(document.getElementById('pos_cost_hidden')?.value || 0);
        } else if (activeType === 'drop') {
            ship = parseFloat(document.getElementById('drop_shipping_cost')?.value || 0);
        }

        const total = subtotal + tax + ship;
        document.getElementById('sum_subtotal').textContent = currencySym + ' ' + Number(subtotal).toLocaleString('id-ID');
        document.getElementById('sum_tax').textContent      = currencySym + ' ' + Number(tax).toLocaleString('id-ID');
        document.getElementById('sum_shipping').textContent = currencySym + ' ' + Number(ship).toLocaleString('id-ID');
        document.getElementById('sum_total').textContent    = currencySym + ' ' + Number(total).toLocaleString('id-ID');

        const shipRow = document.getElementById('sum_ship_row');
        if (ship > 0) { shipRow.style.removeProperty('display'); }
        else { shipRow.style.setProperty('display', 'none', 'important'); }
    }

    document.addEventListener('DOMContentLoaded', () => {
        addOrderItem();
        // Set initial state per default checked radio
        document.getElementById('block_pickup').classList.remove('d-none');
    });
</script>
