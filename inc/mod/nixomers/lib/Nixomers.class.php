<?php
/**
 * Nixomers Core Class
 * Handles database, admin integration, and frontend logic
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class Nixomers
{
    private static $initiated = false;
    private static $actionsProcessed = false;

    public function __construct()
    {
        if (self::$initiated)
            return;

        // Register Nixomers specific permissions
        $this->registerPermissions();

        // $this->initDatabase(); // Removed from Construct to improve performance

        // Register Activation Hook
        Hooks::attach('nixomers_activate', function () {
            $this->initDatabase();
        });
        // Add to Admin Menu automatically when initialized
        Hooks::attach('init', function () {
            if (isset($_GET['page']) && $_GET['page'] == 'mods' && isset($_GET['mod']) && $_GET['mod'] == 'nixomers' && isset($_GET['sel']) && $_GET['sel'] == 'analytics') {
                Asset::enqueue('chartjs');
            }
            if (class_exists('AdminMenu')) {
                AdminMenu::add([
                    'id' => 'nixomers',
                    'label' => 'Nixomers',
                    'icon' => 'bi bi-shop',
                    'url' => 'index.php?page=mods&mod=nixomers',
                    'access' => 1,
                    'position' => 'main', // Place it under "Main Navigation"
                    'order' => 0,
                    'children' => Hooks::filter('nix_admin_menu_children', [
                        ['label' => _('Dashboard'),      'icon' => 'bi bi-speedometer2',        'url' => 'index.php?page=mods&mod=nixomers&sel=dashboard',    'access' => 1],
                        ['label' => _('Orders'),         'icon' => 'bi bi-bag-check',           'url' => 'index.php?page=mods&mod=nixomers&sel=orders',       'access' => 1, 'aliases' => ['index.php?page=mods&mod=nixomers&sel=orderdetail']],
                        ['label' => _('Analytics'),      'icon' => 'bi bi-bar-chart-line',      'url' => 'index.php?page=mods&mod=nixomers&sel=analytics',    'access' => 1],
                        ['label' => _('Transactions'),   'icon' => 'bi bi-credit-card',         'url' => 'index.php?page=mods&mod=nixomers&sel=transactions', 'access' => 1],
                        ['label' => _('Stock Inventory'),'icon' => 'bi bi-boxes',               'url' => 'index.php?page=mods&mod=nixomers&sel=stock',        'access' => 1],
                        ['label' => _('Product List'),   'icon' => 'bi bi-grid',                'url' => 'index.php?page=posts&type=nixomers',                'access' => 1],
                        ['label' => _('Add Product'),    'icon' => 'bi bi-plus-square',         'url' => 'index.php?page=posts&act=add&type=nixomers&token=' . TOKEN, 'access' => 1],
                        ['label' => _('Categories'),     'icon' => 'bi bi-tag',                 'url' => 'index.php?page=categories&type=nixomers',           'access' => 1],
                        ['label' => _('Brands'),         'icon' => 'bi bi-award',               'url' => 'index.php?page=categories&type=brand',              'access' => 1],
                        ['label' => _('Suppliers'),      'icon' => 'bi bi-truck',               'url' => 'index.php?page=categories&type=supplier',           'access' => 1],
                        ['label' => _('Product Types'),  'icon' => 'bi bi-collection',          'url' => 'index.php?page=categories&type=product_type',       'access' => 1],
                        ['label' => _('Warranty Options'),'icon' => 'bi bi-shield-check',       'url' => 'index.php?page=categories&type=warranty',           'access' => 1],
                        ['label' => _('Materials'),      'icon' => 'bi bi-layers',              'url' => 'index.php?page=categories&type=material',           'access' => 1],
                        ['label' => _('Stock Locations'),'icon' => 'bi bi-geo-alt',             'url' => 'index.php?page=categories&type=stock_location',     'access' => 1],
                        ['label' => _('Settings'),       'icon' => 'bi bi-gear',                'url' => 'index.php?page=mods&mod=nixomers&sel=settings',     'access' => 0],
                    ])
                ]);
            }
        });

        // Add custom route and url generator for product item
        Hooks::attach('init', function () {
            Router::add(['product/(.*)' . GX_URL_PREFIX => ['post' => 1]]);
        });

        Hooks::attach('post_url', function ($args) {
            $data = $args[0];
            $id = $data['id'];
            if (Posts::type($id) === 'nixomers' && SMART_URL) {
                $inFold = (Options::v('permalink_use_index_php') == 'on') ? 'index.php/' : '';
                $data['url'] = Site::$url . $inFold . 'product/' . Url::slug($id) . GX_URL_PREFIX;
            }
            $args[0] = $data;
            return $args;
        });

        // Global Cart Action Handler
        Hooks::attach('init', function () {
            if (isset($_POST['nix_action'])) {
                if (session_status() === PHP_SESSION_NONE)
                    session_start();
                if (!isset($_SESSION['nix_cart']))
                    $_SESSION['nix_cart'] = [];
                $this->handleActions();
            }
        });



        // Attachment for automatic stock logging on new products
        Hooks::attach('post_submit_add_action', ['Nixomers', 'logInitialStock']);

        Params::register([
            'category' => [
                [
                    'groupname' => 'warranty_params',
                    'grouptitle' => 'Warranty Details',
                    'post_type' => 'warranty',
                    'fields' => [
                        [
                            'title' => 'Warranty Duration',
                            'name' => 'duration',
                            'type' => 'text',
                            'placeholder' => 'e.g. 1 Year, 6 Months',
                            'boxclass' => 'col-md-12'
                        ]
                    ]
                ],
                [
                    'groupname' => 'product_type_params',
                    'grouptitle' => 'Brand Relation',
                    'post_type' => 'product_type',
                    'fields' => [
                        [
                            'title' => 'Parent Brand',
                            'name' => 'brand',
                            'type' => 'dropdown',
                            'value' => self::getBrands(),
                            'boxclass' => 'col-md-12'
                        ]
                    ]
                ]
            ]
        ]);

        // Dynamic Product Type filtering
        Hooks::attach('admin_categories_card', [$this, 'nixomersCategoryCardInfo']);

        Hooks::attach('nix_gateway_settings_body_bank_transfer', function () {
            $ui = new UiBuilder();
            return $ui->renderElement([
                'type' => 'row',
                'class' => 'g-3',
                'items' => [
                    [
                        'width' => 4,
                        'content' => [
                            'type' => 'input',
                            'input_type' => 'text',
                            'name' => 'nix_bank_name',
                            'label' => 'Bank Name',
                            'value' => Options::v('nix_bank_name'),
                            'placeholder' => 'e.g. BCA, Mandiri, BNI'
                        ]
                    ],
                    [
                        'width' => 4,
                        'content' => [
                            'type' => 'input',
                            'input_type' => 'text',
                            'name' => 'nix_bank_account',
                            'label' => 'Account Number',
                            'value' => Options::v('nix_bank_account'),
                            'placeholder' => 'e.g. 1234567890'
                        ]
                    ],
                    [
                        'width' => 4,
                        'content' => [
                            'type' => 'input',
                            'input_type' => 'text',
                            'name' => 'nix_bank_owner',
                            'label' => 'Account Holder Name',
                            'value' => Options::v('nix_bank_owner'),
                            'placeholder' => 'e.g. John Doe'
                        ]
                    ]
                ]
            ], true);
        });

        // Register Show Instruction Hook for Bank Transfer
        Hooks::attach('nix_payment_show_bank_transfer', function ($args) {
            $order = is_array($args) && isset($args[0]) ? $args[0] : $args;

            // Flexible access for both object and array
            $orderId = is_object($order) ? ($order->order_id ?? '') : ($order['order_id'] ?? '');
            $totalVal = is_object($order) ? ($order->total ?? 0) : ($order['total'] ?? 0);

            $bank = Options::v('nix_bank_name') ?: 'Not Configured';
            $accNum = Options::v('nix_bank_account') ?: '-';
            $accName = Options::v('nix_bank_owner') ?: '-';
            $currency = Options::v('nixomers_currency') ?: 'IDR';
            $total = Nixomers::formatCurrency($totalVal);

            // Using Tailwind check from Nixomers helper or just assume both styles
            $framework = Nixomers::getFramework();

            if ($framework === 'tailwindcss') {
                return '
                <div class="max-w-2xl mx-auto py-12 px-4">
                    <div class="text-center mb-10">
                        <div class="inline-flex items-center justify-center h-20 w-20 bg-blue-50 text-blue-600 rounded-full text-4xl mb-6 shadow-sm">
                            <i class="bi bi-bank"></i>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 mb-2">Payment Instruction</h2>
                        <p class="text-gray-500">Please complete your transfer for order <span class="font-bold text-gray-900">' . $orderId . '</span></p>
                    </div>

                    <div class="bg-white border border-gray-100 shadow-2xl rounded-[40px] p-8 md:p-12 mb-8 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 opacity-[0.03] text-[10rem] pointer-events-none -mr-12 -mt-12">
                            <i class="bi bi-bank"></i>
                        </div>
                        
                        <div class="space-y-8 relative z-10">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Amount to Transfer</p>
                                <p class="text-4xl font-black text-blue-600 font-mono tracking-tighter">' . $currency . ' ' . $total . '</p>
                            </div>

                            <hr class="border-gray-100">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Bank Name</p>
                                    <p class="text-xl font-bold text-gray-900">' . $bank . '</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Account Number</p>
                                    <p class="text-xl font-black text-gray-900 font-mono tracking-widest p-2 bg-gray-50 rounded-lg inline-block border border-gray-100">' . $accNum . '</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Account Holder</p>
                                    <p class="text-xl font-bold text-gray-900 uppercase">' . $accName . '</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6 mb-10">
                        <div class="flex gap-4">
                            <i class="bi bi-info-circle-fill text-blue-500 text-xl"></i>
                            <div>
                                <p class="text-blue-950 font-bold mb-1">Payment Confirmation</p>
                                <p class="text-blue-700 text-sm leading-relaxed">Setelah melakukan transfer, mohon kirimkan bukti bayar melalui Admin kami atau tunggu proses verifikasi oleh sistem.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="' . NixomersUrl::store() . '" class="px-10 py-4 bg-white border border-gray-200 text-gray-900 font-bold rounded-full hover:bg-gray-50 transition-all text-center">Back to Store</a>
                        <a href="javascript:window.print()" class="px-10 py-4 bg-gray-900 text-white font-bold rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all text-center flex items-center justify-center gap-2">
                            <i class="bi bi-printer"></i> Print Instruction
                        </a>
                    </div>
                </div>';
            } else {
                return '
                <div class="container py-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-7 col-md-9 text-center">
                            <div class="mb-5">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                                    <i class="bi bi-bank display-4"></i>
                                </div>
                                <h1 class="fw-black mb-1">Payment Instruction</h1>
                                <p class="text-muted">Order ID: <strong>' . $orderId . '</strong></p>
                            </div>

                            <div class="card border-0 shadow-lg rounded-5 overflow-hidden text-start mb-4">
                                <div class="card-body p-4 p-md-5">
                                    <div class="mb-5 bg-light p-4 rounded-4 border">
                                        <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:10px; letter-spacing:2px;">Amount Due</div>
                                        <div class="h2 fw-black text-primary mb-0">' . $currency . ' ' . $total . '</div>
                                    </div>

                                    <div class="row g-4 font-monospace">
                                        <div class="col-md-6">
                                            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:10px;">Bank Name</div>
                                            <div class="fs-4 fw-bold">' . $bank . '</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:10px;">Account Number</div>
                                            <div class="fs-4 fw-black border-bottom border-primary border-2 d-inline-block tracking-widest">' . $accNum . '</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size:10px;">Beneficiary Name</div>
                                            <div class="fs-5 fw-bold text-uppercase">' . $accName . '</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info border-0 rounded-4 p-4 text-start mb-4">
                                <div class="d-flex gap-3">
                                    <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Waiting for Payment</h6>
                                        <p class="mb-0 small leading-relaxed">Your order will be processed once we receive the payment. Please keep the receipt as proof of transfer.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mt-5">
                                <a href="' . NixomersUrl::store() . '" class="btn btn-light rounded-pill px-5 py-3 fw-bold">Back to Store</a>
                                <button onclick="window.print()" class="btn btn-dark rounded-pill px-5 py-3 fw-bold shadow-sm">
                                    <i class="bi bi-printer me-2"></i> Print Instruction
                                </button>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        });

        Hooks::attach('nix_gateway_settings_body_paypal', function () {
            $ui = new UiBuilder();
            return $ui->renderElement([
                'type' => 'row',
                'class' => 'g-3',
                'items' => [
                    [
                        'width' => 8,
                        'content' => [
                            'type' => 'input',
                            'input_type' => 'email',
                            'name' => 'nix_paypal_email',
                            'label' => 'PayPal Email (Merchant)',
                            'value' => Options::v('nix_paypal_email'),
                            'placeholder' => 'your-paypal@email.com'
                        ]
                    ],
                    [
                        'width' => 4,
                        'content' => [
                            'type' => 'select',
                            'name' => 'nix_paypal_mode',
                            'label' => 'Environment Mode',
                            'selected' => Options::v('nix_paypal_mode'),
                            'options' => [
                                'sandbox' => 'Sandbox (Testing)',
                                'live' => 'Live (Production)'
                            ]
                        ]
                    ]
                ]
            ], true);
        });



        // Programmatically register Nixomers parameters into the core CRM/Post System
        if (class_exists('Params')) {
            // Register UI Labels for Nixomers Post Type
            if (method_exists('Posts', 'setTypeLabel')) {
                Posts::setTypeLabel('nixomers', [
                    'label' => 'Product',
                    'create' => 'New',
                    'edit' => 'Edit',
                    'title_label' => 'Product Name',
                    'title_placeholder' => 'Enter product name...',
                    'repository_title' => 'Product Inventory',
                    'new_item' => 'New Product',
                    'records_library' => 'Product Catalog'
                ]);
            }

            // Register Category UI Labels for Nixomers
            if (method_exists('Categories', 'setTypeLabel')) {
                Categories::setTypeLabel('nixomers', [
                    'title' => 'Product Categories',
                    'subtitle' => 'Manage your storefront departments and product classifications.',
                    'new_item' => 'New Category',
                    'modal_title' => 'Create Product Category'
                ]);

                Categories::setTypeLabel('material', [
                    'title' => 'Product Materials',
                    'subtitle' => 'Define the materials used in your products (e.g. Linen, Cotton, Ceramic).',
                    'new_item' => 'New Material',
                    'modal_title' => 'Add New Material',
                    'name_label' => 'Material Name'
                ]);

                Categories::setTypeLabel('brand', [
                    'title' => 'Product Brands',
                    'subtitle' => 'Manage the brands associated with your products.',
                    'new_item' => 'New Brand',
                    'modal_title' => 'Add New Brand',
                    'name_label' => 'Brand Name'
                ]);

                Categories::setTypeLabel('supplier', [
                    'title' => 'Product Suppliers',
                    'subtitle' => 'Manage the estate suppliers and partners for your produce.',
                    'new_item' => 'New Supplier',
                    'modal_title' => 'Add New Supplier',
                    'name_label' => 'Supplier Name'
                ]);

                Categories::setTypeLabel('product_type', [
                    'title' => 'Product Types',
                    'subtitle' => 'Define various types of products in your catalog.',
                    'new_item' => 'New Product Type',
                    'modal_title' => 'Add New Product Type',
                    'name_label' => 'Type Name'
                ]);

                Categories::setTypeLabel('warranty', [
                    'title' => 'Warranty Options',
                    'subtitle' => 'Define available warranty options for your products.',
                    'new_item' => 'New Warranty',
                    'modal_title' => 'Add New Warranty',
                    'name_label' => 'Warranty Description'
                ]);

                Categories::setTypeLabel('stock_location', [
                    'title' => 'Warehouse Locations',
                    'subtitle' => 'Track your inventory across multiple storage points and branches.',
                    'new_item' => 'New Location',
                    'stats_label' => 'Registered Locations',
                    'empty_label' => 'No locations defined',
                    'modal_title' => 'Register Stock Location',
                    'name_label' => 'Location Name'
                ]);
            }

            Params::register([
                'bottom' => [
                    [
                        'groupname' => 'nixomers_info',
                        'grouptitle' => 'Product Information',
                        'icon' => 'bi bi-cart4',
                        'post_type' => 'nixomers',
                        'fields' => [
                            [
                                'title' => 'SKU',
                                'name' => 'sku',
                                'type' => 'text',
                                'placeholder' => 'E.g: PROD-001',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Price',
                                'name' => 'price',
                                'type' => 'number',
                                'default' => '0',
                                'placeholder' => '0',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Stock',
                                'name' => 'stock',
                                'type' => 'number',
                                'default' => '10',
                                'placeholder' => '10',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Barcode / EAN',
                                'name' => 'barcode',
                                'type' => 'text',
                                'placeholder' => 'Scan/Type Barcode',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Weight (Grams)',
                                'name' => 'weight',
                                'type' => 'number',
                                'default' => '0',
                                'placeholder' => '0',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Product Unit',
                                'name' => 'unit',
                                'type' => 'dropdown',
                                'value' => [
                                    'Pcs' => 'Pieces (Pcs)',
                                    'Box' => 'Box/Carton',
                                    'Kg' => 'Kilogram (Kg)',
                                    'Gr' => 'Gram (gr)',
                                    'Mtr' => 'Meter (m)',
                                    'Ltr' => 'Liter (L)'
                                ],
                                'default' => 'Pcs',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Brand / Merek',
                                'name' => 'brand',
                                'type' => 'dropdown',
                                'value' => self::getBrands(),
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Product Type / Tipe',
                                'name' => 'product_type',
                                'type' => 'dropdown',
                                'value' => self::getProductTypes(),
                                'require' => 'brand',
                                'ajax_url' => Url::ajax('nixomers', 'product_types'),
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Warranty / Garansi',
                                'name' => 'warranty',
                                'type' => 'dropdown',
                                'value' => self::getWarranties(),
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Length (cm)',
                                'name' => 'length',
                                'type' => 'number',
                                'placeholder' => '0',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Width (cm)',
                                'name' => 'width',
                                'type' => 'number',
                                'placeholder' => '0',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Height (cm)',
                                'name' => 'height',
                                'type' => 'number',
                                'placeholder' => '0',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Stock Location',
                                'name' => 'stock_location',
                                'type' => 'dropdown',
                                'value' => self::getStockLocations(),
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Origin / Asal Produksi',
                                'name' => 'origin',
                                'type' => 'text',
                                'placeholder' => 'e.g. North Estate, Bandung',
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Supplier',
                                'name' => 'supplier',
                                'type' => 'dropdown',
                                'value' => self::getSuppliers(),
                                'boxclass' => 'col-md-4'
                            ],
                            [
                                'title' => 'Material',
                                'name' => 'material',
                                'type' => 'dropdown',
                                'value' => self::getMaterials(),
                                'boxclass' => 'col-md-6'
                            ],
                            [
                                'title' => 'Care Instructions',
                                'name' => 'note',
                                'type' => 'text',
                                'placeholder' => 'E.g: Hand wash only',
                                'boxclass' => 'col-md-6'
                            ]
                        ]
                    ]
                ]
            ]);
        }

        // Register frontend module menus
        if (class_exists('Mod')) {
            Mod::addMenuList([
                'store' => [
                    'label' => 'Store: Catalog',
                    'post_type' => 'nixomers',
                    'layout' => 'full',
                    'show_title' => false
                ],
                'cart' => [
                    'label' => 'Store: Shopping Cart',
                    'post_type' => 'nixomers',
                    'layout' => 'full',
                    'show_title' => false
                ],
                'checkout' => [
                    'label' => 'Store: Checkout',
                    'post_type' => 'nixomers',
                    'layout' => 'full',
                    'show_title' => false
                ],
                'payment' => [
                    'label' => 'Store: Payment Confirmation',
                    'post_type' => 'nixomers',
                    'layout' => 'full',
                    'show_title' => false
                ]
            ]);
        }

        // Hook into the frontend controller
        Hooks::attach('mod_control', [$this, 'frontendDispatcher']);
        // Hook into single post view
        Hooks::attach('post_content_after_action', [$this, 'injectAddToCartButton']);

        // Add Price and Stock columns to Product List (Nixomers)
        Hooks::attach('admin_posts_table_headers', function ($args) {
            $headers = $args[0] ?? [];
            $postType = $args[1] ?? '';
            if ($postType === 'nixomers') {
                // Remove Ownership (index 1) and insert Price/Stock
                // Current headers: 0:Details, 1:Ownership, 2:Engagement, 3:Timeline, 4:Management, 5:Selection
                $newHeaders = [];
                $newHeaders[] = $headers[0]; // Details
                $newHeaders[] = ['content' => _('Price'), 'class' => 'text-center'];
                $newHeaders[] = ['content' => _('Stock'), 'class' => 'text-center'];
                $newHeaders[] = $headers[2]; // Engagement
                $newHeaders[] = $headers[3]; // Timeline
                $newHeaders[] = $headers[4]; // Management
                $newHeaders[] = $headers[5]; // Selection
                return [$newHeaders, $postType];
            }
            return $args;
        });

        Hooks::attach('admin_posts_table_row', function ($args) {
            $row = $args[0] ?? [];
            $pObj = $args[1] ?? null;
            if ($pObj && isset($pObj->type) && $pObj->type === 'nixomers') {
                $id = $pObj->id;
                $price = (float) Posts::getParam('price', $id) ?: 0;
                $stock = (int) Posts::getParam('stock', $id) ?: 0;
                $currency = Options::v('nixomers_currency') ?: 'IDR';

                $priceHtml = '<div class="text-center"><span class="fw-bold text-primary">' . $currency . ' ' . self::formatCurrency($price) . '</span></div>';
                $stockColor = ($stock <= 5) ? 'danger' : ($stock <= 10 ? 'warning' : 'success');
                $stockHtml = '<div class="text-center"><span class="badge bg-' . $stockColor . ' rounded-pill px-3">' . $stock . '</span></div>';

                // Remove Ownership (index 1) and insert Price/Stock
                // Current row: 0:Details, 1:Ownership, 2:Engagement, 3:Timeline
                $newRow = [];
                $newRow[] = $row[0]; // Details
                $newRow[] = ['content' => $priceHtml, 'class' => 'text-center'];
                $newRow[] = ['content' => $stockHtml, 'class' => 'text-center'];
                $newRow[] = $row[2]; // Engagement
                $newRow[] = $row[3]; // Timeline
                return [$newRow, $pObj];
            }
            return $args;
        });

        // Register Notification Bell in Admin Header
        Hooks::attach('admin_header_top_right_action', [$this, 'renderNotificationBell']);
        Hooks::attach('admin_footer_action', [$this, 'renderNotificationScript']);

        // Handle Mark as Read
        if (isset($_GET['mark_read'])) {
            $notifId = (int) $_GET['mark_read'];
            $username = Session::val('username');
            if ($username) {
                self::markAsRead($notifId, $username);
            }
        }

        // Register Cart Badge Hook
        Hooks::attach('nix_cart_badge', ['NixCart', 'getBadge']);
        Hooks::attach('header_load_lib', function () {
            echo '<style>
                .nix-cart-container { position: relative; }
                .nix-cart-count {
                    background: #ff3b30;
                    color: white;
                    border-radius: 999px;
                    padding: 0 4px;
                    font-size: 9px;
                    font-weight: 800;
                    line-height: 16px;
                    min-width: 16px;
                    height: 16px;
                    text-align: center;
                    position: absolute;
                    top: -6px;
                    right: -8px;
                    border: 1.5px solid white;
                    z-index: 10;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    pointer-events: none;
                }
            </style>
            <script>
                window.nixUpdateCartCount = function() {
                    fetch("' . Url::ajax('nixomers', 'cart_count') . '")
                    .then(r => r.json())
                    .then(res => {
                        const badges = document.querySelectorAll(".nix-cart-count");
                        badges.forEach(b => {
                            b.innerText = res.count;
                            b.style.display = res.count > 0 ? "inline-block" : "none";
                        });
                    }).catch(e => console.error("Cart update error", e));
                }
            </script>';
        });

        self::$initiated = true;
    }

    /**
     * Renders a notification bell for new orders in the admin header.
     */
    public function renderNotificationBell()
    {
        if (!User::access(5))
            return ''; // Access up to VIP/Sales level

        $userGroup = (int) Session::val('group');
        $username = Session::val('username');

        // Map user group to roles they can see
        $allowedRoles = ['all'];
        if ($userGroup <= 1)
            $allowedRoles = array_merge($allowedRoles, ['admin', 'billing', 'fulfillment', 'cs', 'sales']);
        elseif ($userGroup == 2)
            $allowedRoles[] = 'billing';
        elseif ($userGroup == 3)
            $allowedRoles[] = 'fulfillment';
        elseif ($userGroup == 4)
            $allowedRoles[] = 'cs';
        elseif ($userGroup == 5)
            $allowedRoles[] = 'sales';

        $rolesSql = "'" . implode("','", $allowedRoles) . "'";

        // Count unread notifications
        $newNotifs = Db::$pdo->query("SELECT COUNT(n.id) FROM nix_notifications n 
            LEFT JOIN nix_notifications_read nr ON n.id = nr.notification_id AND nr.username = '$username'
            WHERE n.target_role IN ($rolesSql) AND nr.id IS NULL")->fetchColumn();

        $badgeStyle = ($newNotifs > 0) ? 'display: block;' : 'display: none;';

        $html = '
        <div class="dropdown" id="nixomers-notif-dropdown">
            <a href="#" class="btn btn-light btn-sm rounded-pill border position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span id="nix-notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 0.25em 0.5em; ' . $badgeStyle . '">
                    ' . $newNotifs . '
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-3" style="min-width: 320px;">
                <li class="px-2 py-1 border-bottom mb-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">' . _("Store Notifications") . '</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small" id="nix-notif-count-label">' . $newNotifs . ' New</span>
                </li>
                <div id="nix-notif-list-container">';

        if ($newNotifs > 0) {
            $latest = Db::$pdo->query("SELECT n.* FROM nix_notifications n 
                LEFT JOIN nix_notifications_read nr ON n.id = nr.notification_id AND nr.username = '$username'
                WHERE n.target_role IN ($rolesSql) AND nr.id IS NULL 
                ORDER BY n.id DESC LIMIT 5")->fetchAll(PDO::FETCH_OBJ);

            foreach ($latest as $n) {
                $html .= '
                <li>
                    <a class="dropdown-item rounded-3 p-2 mb-1 border-start border-4 border-primary bg-light bg-opacity-50" href="' . $n->url . '&mark_read=' . $n->id . '">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-dark">' . $n->title . '</span>
                            <span class="extra-small text-muted">' . date('H:i', strtotime($n->created_at)) . '</span>
                        </div>
                        <div class="extra-small text-truncate text-secondary">' . $n->message . '</div>
                    </a>
                </li>';
            }
        } else {
            $html .= '<li class="text-center py-4 text-muted small nix-empty-notif"><i class="bi bi-check2-circle d-block fs-2 mb-2 opacity-25"></i> ' . _("No new notifications") . '</li>';
        }

        $html .= '
                </div>
                <li class="mt-2 text-center border-top pt-2">
                    <a href="index.php?page=mods&mod=nixomers&sel=orders" class="btn btn-link btn-sm text-decoration-none fw-bold p-0">' . _("View All Orders") . '</a>
                </li>
            </ul>
        </div>';

        return $html;
    }

    /**
     * Injects JavaScript for real-time notification polling.
     */
    public function renderNotificationScript()
    {
        if (!User::access(1))
            return;

        $ajaxUrl = Url::ajax('nixomers', 'notifications');
        echo '
        <script>
        (function() {
            let lastCount = -1;
            const pollNotifications = () => {
                fetch("' . $ajaxUrl . '")
                .then(r => r.json())
                .then(res => {
                    if (res.status === "success") {
                        const count = res.count;
                        const badge = document.getElementById("nix-notif-badge");
                        const countLabel = document.getElementById("nix-notif-count-label");
                        const container = document.getElementById("nix-notif-list-container");

                        if (badge) {
                            badge.innerText = count;
                            badge.style.display = (count > 0) ? "block" : "none";
                        }
                        if (countLabel) {
                            countLabel.innerText = count + " New";
                        }

                        // Only re-render list if count changed and dropdown is closed
                        const dropdown = document.getElementById("nixomers-notif-dropdown");
                        const isOpen = dropdown && dropdown.classList.contains("show");
                        
                        if (count !== lastCount && container && !isOpen) {
                            if (count > 0) {
                                let html = "";
                                res.latest.forEach(o => {
                                    html += `
                                    <li>
                                        <a class="dropdown-item rounded-3 p-2 mb-1 border-start border-4 border-primary bg-light bg-opacity-50" href="${o.url}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small text-dark">${o.title}</span>
                                                <span class="extra-small text-muted">${o.time}</span>
                                            </div>
                                            <div class="extra-small text-truncate text-secondary">${o.message}</div>
                                        </a>
                                    </li>`;
                                });
                                container.innerHTML = html;
                            } else {
                                container.innerHTML = `<li class="text-center py-4 text-muted small nix-empty-notif"><i class="bi bi-check2-circle d-block fs-2 mb-2 opacity-25"></i> ' . _("No new notifications") . '</li>`;
                            }

                            // Trigger sound or browser notification if count increased
                            if (lastCount !== -1 && count > lastCount) {
                                try {
                                    const audio = new Audio("' . Site::$url . 'assets/sounds/notification.mp3");
                                    audio.play().catch(e => console.log("Audio play blocked"));
                                } catch(e) {}
                            }
                        }
                        lastCount = count;
                    }
                })
                .catch(e => console.error("Notif Poll Error", e));
            };

            // Initial poll and set interval
            setTimeout(pollNotifications, 2000);
            setInterval(pollNotifications, 30000); // Poll every 30 seconds
        })();
        </script>';
    }

    /**
     * Get Current Logged in User
     */
    public static function getUser()
    {
        return User::isLoggedin() ? User::userdata(User::id(Session::val('username'))) : null;
    }

    /**
     * Get Current UI Framework
     */
    public static function getFramework()
    {
        return Options::v('nixomers_framework') ?: 'bootstrap';
    }

    /**
     * Hook to display custom parameters on Category Card
     */
    public function nixomersCategoryCardInfo($args)
    {
        $html = $args[0] ?? '';
        $cat = $args[1] ?? null;

        if (!$cat)
            return $html;

        $extraInfo = "";
        if ($cat->type === 'warranty') {
            $duration = Categories::getParam('duration', $cat->id);
            if ($duration) {
                $extraInfo = "<div class='mt-2'><span class='badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 rounded-pill px-3'><i class='bi bi-clock-history me-1'></i> Duration: {$duration}</span></div>";
            }
        } elseif ($cat->type === 'product_type') {
            $brand = Categories::getParam('brand', $cat->id);
            if ($brand) {
                $extraInfo = "<div class='mt-2'><span class='badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-3'><i class='bi bi-tag me-1'></i> Brand: {$brand}</span></div>";
            }
        }

        if ($extraInfo) {
            // Insert extra info after description
            $search = "</h5>";
            $replace = "</h5>" . $extraInfo;
            $html = str_replace($search, $replace, $html);
        }

        return $html;
    }

    /**
     * Render Theme View if exists
     */
    /**
     * Renders a theme-specific view if it exists.
     */
    public static function renderThemeView($view, $data = [])
    {
        $theme_file = 'nixomers-' . $view;
        $active_theme = defined('THEME') ? THEME : Options::v('themes');
        $theme_dir = rtrim(GX_THEME, '/\\') . DIRECTORY_SEPARATOR . $active_theme . DIRECTORY_SEPARATOR;

        if (file_exists($theme_dir . $theme_file . '.latte') || file_exists($theme_dir . $theme_file . '.php')) {
            // If it's pure PHP and no latte file found
            if (!file_exists($theme_dir . $theme_file . '.latte') && file_exists($theme_dir . $theme_file . '.php')) {
                extract($data);
                ob_start();
                include $theme_dir . $theme_file . '.php';
                return ob_get_clean();
            }

            // Otherwise use Latte
            $latte = new Latte\Engine;
            $latte->addExtension(new Latte\Essential\RawPhpExtension);
            $latte->addExtension(new Latte\Essential\TranslatorExtension(
                Typo::translate(...),
                Options::v('system_lang')
            ));
            $latte->setCacheDirectory(GX_CACHE . '/temp');
            $latte->setAutoRefresh(true);

            // Add common filters
            $latte->addFilter('nl2br', fn($s) => is_string($s) ? nl2br($s) : $s);
            $latte->addFilter('stripHtml', fn($s) => is_string($s) ? Shortcode::strip(strip_tags($s)) : $s);

            // Prepare common data
            $viewData = array_merge([
                'site_url' => Site::$url,
                'theme_url' => Site::$url . 'inc/themes/' . $active_theme . '/',
                'currency' => Options::v('nixomers_currency') ?: 'IDR',
                'token' => TOKEN,
                'framework' => self::getFramework()
            ], $data);

            $v_file = file_exists($theme_dir . $theme_file . '.latte') ? $theme_file . '.latte' : $theme_file . '.php';

            return $latte->renderToString($theme_dir . $v_file, $viewData);
        }
        return false;
    }


    /**
     * Format currency based on settings
     */
    public static function formatCurrency($number)
    {
        $number = (float) ($number ?? 0);
        $fmt = Options::v('nixomers_format') ?: 'dot';
        switch ($fmt) {
            case 'comma':
                return number_format($number, 2, '.', ',');
            case 'space':
                return number_format($number, 2, ',', ' ');
            case 'none':
                return number_format($number, 2, '.', '');
            case 'dot':
            default:
                // Special check for IDR usually 0 decimal
                $curr = Options::v('nixomers_currency') ?: 'IDR';
                $dec = ($curr === 'IDR' || $curr === 'Rp') ? 0 : 2;
                return number_format($number, $dec, ',', '.');
        }
    }

    /**
     * Calculate and sync Net Amount for a transaction
     */
    public static function calculateNetTrans($orderId)
    {
        $order = Query::table('nix_orders')->where('order_id', $orderId)->first();
        if (!$order)
            return 0;

        // 1. Recalculate Subtotal from Cart Items
        $cart = json_decode($order->cart_items ?? '[]', true) ?: [];
        $newSubtotal = 0;
        foreach ($cart as $pId => $qty) {
            $price = (float) Posts::getParam('price', $pId) ?: 0;
            $newSubtotal += ($price * (int) $qty);
        }

        // 2. Recalculate Tax (Use stored rate if possible, or global)
        $taxRate = (float) Options::v('nixomers_tax') ?: 0;
        $newTax = ($newSubtotal * $taxRate) / 100;
        $newTotal = $newSubtotal + $newTax + (float) $order->shipping_cost;

        // 3. Update nix_orders
        Query::table('nix_orders')->where('order_id', $orderId)->update([
            'subtotal' => $newSubtotal,
            'tax' => $newTax,
            'total' => $newTotal
        ]);

        // 4. Sync to nix_transactions
        $trans = Query::table('nix_transactions')->where('order_id', $orderId)->first();
        if ($trans) {
            $fee = (float) $trans->fee;
            $net = $newTotal - $fee - $newTax - (float) $order->shipping_cost;

            $update = [
                'amount' => $newTotal,
                'tax' => $newTax,
                'shipping_cost' => (float) $order->shipping_cost,
                'net' => $net
            ];
            Query::table('nix_transactions')->where('order_id', $orderId)->update($update);
            Hooks::run('nixomers_recalculate_after', $orderId);
            return $net;
        }

        Hooks::run('nixomers_recalculate_after', $orderId);
        return $newTotal;
    }

    /**
     * Register all Nixomers specific permissions to the core ACL system.
     */
    public function registerPermissions()
    {
        if (class_exists('Acl')) {
            // Pages
            Acl::register('NIXOMERS_DASHBOARD', 'Access Nixomers Dashboard', [0, 1, 2, 3, 4, 5]);
            Acl::register('NIXOMERS_ORDERS_VIEW', 'View Orders List', [0, 1, 2, 3, 4, 5]);
            Acl::register('NIXOMERS_ORDERS_DETAIL', 'View Order Details', [0, 1, 2, 3, 4]);
            Acl::register('NIXOMERS_ANALYTICS', 'Access Analytics & Reports', [0, 1]);
            Acl::register('NIXOMERS_INVENTORY', 'Manage Inventory/Stock', [0, 1, 3]);
            Acl::register('NIXOMERS_SETTINGS', 'Modify Nixomers Settings', [0]);

            // Actions
            Acl::register('NIXOMERS_ORDER_CREATE', 'Create Manual Orders (POS)', [0, 1, 5]);
            Acl::register('NIXOMERS_ORDER_STATUS', 'Update Order Status', [0, 1, 3]);
            Acl::register('NIXOMERS_ORDER_CANCEL', 'Cancel Orders', [0, 1]);
            Acl::register('NIXOMERS_ORDER_REFUND', 'Process Refunds', [0, 1, 2]);
            Acl::register('NIXOMERS_ORDER_DELETE', 'Delete Orders Permanently', [0]);
            Acl::register('NIXOMERS_PAYMENT_UPDATE', 'Update Payment Details', [0, 1, 2]);
            Acl::register('NIXOMERS_STOCK_UPDATE', 'Manual Stock Adjustment', [0, 1, 3]);
            Acl::register('NIXOMERS_GRANULAR_UPDATE', 'Update SN/Barcode/Unit Tracking', [0, 1, 3]);
        }
    }

    /**
     * Check if the current user has access to a specific ACL action or page.
     * Uses the core GeniXCMS Acl class.
     * 
     * @param string $action
     * @return bool
     */
    public static function checkACL($action)
    {
        if (!class_exists('Acl')) {
            return User::access(0);
        }

        // Map old internal action keys to new ACL keys
        $map = [
            'page_dashboard' => 'NIXOMERS_DASHBOARD',
            'page_orders' => 'NIXOMERS_ORDERS_VIEW',
            'page_orderdetail' => 'NIXOMERS_ORDERS_DETAIL',
            'page_analytics' => 'NIXOMERS_ANALYTICS',
            'page_inventory' => 'NIXOMERS_INVENTORY',
            'page_settings' => 'NIXOMERS_SETTINGS',

            'action_order_create' => 'NIXOMERS_ORDER_CREATE',
            'action_order_update_status' => 'NIXOMERS_ORDER_STATUS',
            'action_order_delete' => 'NIXOMERS_ORDER_DELETE',
            'action_order_cancel' => 'NIXOMERS_ORDER_CANCEL',
            'action_order_refund' => 'NIXOMERS_ORDER_REFUND',
            'action_payment_update' => 'NIXOMERS_PAYMENT_UPDATE',
            'action_stock_update' => 'NIXOMERS_STOCK_UPDATE',
            'action_granular_update' => 'NIXOMERS_GRANULAR_UPDATE',
        ];

        $permKey = isset($map[$action]) ? $map[$action] : strtoupper($action);
        return Acl::check($permKey);
    }

    /**
     * Generate a formatted ID based on prefix and format from options.
     * 
     * @param string $type 'invoice', 'order', 'product', 'trx'
     * @return string
     */
    public static function generateId($type = 'order')
    {
        $prefixKey = "nixomers_{$type}_prefix";
        $formatKey = "nixomers_{$type}_format";

        $defaultPrefix = match ($type) {
            'invoice' => 'INV',
            'order' => 'ORD',
            'product' => 'PRD',
            'trx' => 'TRX',
            default => 'ID'
        };

        $prefix = Options::v($prefixKey) ?: $defaultPrefix;
        $format = Options::v($formatKey) ?: '{PREFIX}-{YYYY}{MM}{DD}-{ID}';

        $randomId = match ($type) {
            'trx' => strtoupper(substr(uniqid(), -8)),
            default => strtoupper(substr(uniqid(), -5))
        };

        return str_replace(
            ['{PREFIX}', '{YYYY}', '{MM}', '{DD}', '{ID}'],
            [$prefix, date('Y'), date('m'), date('d'), $randomId],
            $format
        );
    }

    /**
     * Create a notification.
     * 
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string $url
     * @param string $targetRole
     */
    public static function addNotification($type, $title, $message, $url = '', $targetRole = 'all')
    {
        try {
            return Query::table('nix_notifications')->insert([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'target_role' => $targetRole,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mark a notification as read for a specific user.
     * 
     * @param int $notificationId
     * @param string $username
     */
    public static function markAsRead($notificationId, $username)
    {
        try {
            // Check if already read
            $exists = Query::table('nix_notifications_read')
                ->where('notification_id', $notificationId)
                ->where('username', $username)
                ->first();

            if (!$exists) {
                return Query::table('nix_notifications_read')->insert([
                    'notification_id' => $notificationId,
                    'username' => $username,
                    'read_at' => date('Y-m-d H:i:s')
                ]);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Initialize Database Table
     */
    public function initDatabase()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `nix_orders` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `customer_name` TEXT,
            `customer_email` TEXT,
            `customer_phone` TEXT,
            `shipping_country` TEXT,
            `shipping_province` TEXT,
            `shipping_city` TEXT,
            `shipping_district` TEXT,
            `shipping_village` TEXT,
            `shipping_street` TEXT,
            `shipping_address` TEXT,
            `shipping_courier` TEXT,
            `shipping_service` TEXT,
            `shipping_cost` REAL DEFAULT 0,
            `shipping_etd` TEXT,
            `shipping_engine` TEXT,
            `payment_method` TEXT,
            `payment_data` TEXT,
            `cart_items` TEXT,
            `subtotal` REAL,
            `tax` REAL,
            `total` REAL,
            `status` TEXT DEFAULT 'pending',
            `date` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $sqlTrans = "CREATE TABLE IF NOT EXISTS `nix_transactions` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `trans_id` TEXT, -- external transaction ID from gateway
            `order_id` INTEGER, -- link to nix_orders
            `expense_id` INTEGER, -- future link to nix_expenses
            `type` TEXT, -- income/expense
            `amount` REAL, -- gross amount
            `fee` REAL DEFAULT 0, -- merchant/gateway fee
            `tax` REAL DEFAULT 0, -- tax amount
            `net` REAL, -- amount received after fees and tax
            `shipping_cost` REAL DEFAULT 0,
            `description` TEXT,
            `method` TEXT, -- manual, midtrans, stripe, etc.
            `status` TEXT, -- completed, refund, cancelled, etc.
            `notes` TEXT,
            `date` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $sqlInv = "CREATE TABLE IF NOT EXISTS `nix_inventory` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `post_id` INTEGER, -- link to posts (nixomers type)
            `type` TEXT, -- IN / OUT
            `amount` INTEGER, -- amount changed
            `current_stock` INTEGER, -- stock after change
            `location_id` INTEGER, -- link to categories type stock_location
            `reference` TEXT, -- Supplier, Order ID, etc
            `notes` TEXT, -- Adjustment reason
            `date` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $sqlItems = "CREATE TABLE IF NOT EXISTS `nix_order_items` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `order_id` TEXT, -- link to nix_orders.order_id
            `product_id` INTEGER,
            `qty` INTEGER DEFAULT 1,
            `status` TEXT DEFAULT 'pending', -- pending, checking, functional, packed, ready
            `serial_number` TEXT,
            `barcode` TEXT,
            `location` TEXT, -- room/shelf location
            `notes` TEXT,
            `date` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $sqlItemLogs = "CREATE TABLE IF NOT EXISTS `nix_order_item_logs` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `item_id` INTEGER, -- link to nix_order_items.id
            `old_status` TEXT,
            `new_status` TEXT,
            `notes` TEXT,
            `updated_by` TEXT, -- username
            `date` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $sqlOrderLogs = "CREATE TABLE IF NOT EXISTS `nix_order_logs` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `order_id` TEXT,
            `old_status` TEXT,
            `new_status` TEXT,
            `updated_by` TEXT,
            `date` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $sqlNotifications = "CREATE TABLE IF NOT EXISTS `nix_notifications` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `type` TEXT,
            `target_role` TEXT DEFAULT 'all', -- admin, billing, fulfillment, all
            `title` TEXT,
            `message` TEXT,
            `url` TEXT,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $sqlNotificationsRead = "CREATE TABLE IF NOT EXISTS `nix_notifications_read` (
            `id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `notification_id` INTEGER,
            `username` TEXT,
            `read_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        try {
            if (Db::connect()) {
                Db::$pdo->exec($sql);
                Db::$pdo->exec($sqlTrans);
                Db::$pdo->exec($sqlInv);
                Db::$pdo->exec($sqlItems);
                Db::$pdo->exec($sqlItemLogs);
                Db::$pdo->exec($sqlOrderLogs);
                Db::$pdo->exec($sqlNotifications);
                Db::$pdo->exec($sqlNotificationsRead);

                // --- MIGRATION: Ensure new columns exist ---
                $newCols = [
                    'customer_phone',
                    'shipping_country',
                    'shipping_province',
                    'shipping_city',
                    'shipping_district',
                    'shipping_village',
                    'shipping_street',
                    'shipping_courier',
                    'shipping_service',
                    'shipping_cost',
                    'shipping_etd',
                    'shipping_engine',
                    'order_id',
                    'payment_method',
                    'payment_data',
                    'shipped_at'
                ];
                foreach ($newCols as $col) {
                    try {
                        Db::$pdo->exec("ALTER TABLE `nix_orders` ADD COLUMN `{$col}` TEXT");
                    } catch (Exception $e) {
                        // Likely column already exists
                    }
                }

                // --- MIGRATION: Ensure nix_transactions columns exist ---
                $newTransCols = ['trans_id', 'order_id', 'expense_id', 'fee', 'tax', 'net', 'status', 'shipping_cost', 'method', 'notes', 'paid_date', 'description'];
                foreach ($newTransCols as $tcol) {
                    try {
                        Db::$pdo->exec("ALTER TABLE `nix_transactions` ADD COLUMN `{$tcol}` TEXT");
                    } catch (Exception $e) {
                    }
                }

                // --- MIGRATION: Ensure nix_order_items columns exist ---
                $newItemCols = ['order_id', 'serial_number', 'barcode', 'location', 'notes'];
                foreach ($newItemCols as $icol) {
                    try {
                        Db::$pdo->exec("ALTER TABLE `nix_order_items` ADD COLUMN `{$icol}` TEXT");
                    } catch (Exception $e) {
                    }
                }
            }
        } catch (Exception $e) {
            // Silently fail if table exists or other error
        }
    }

    public function frontendDispatcher($args)
    {
        $data = $args[0] ?? [];
        $allowed = ['store', 'cart', 'checkout', 'payment', 'ajax_catalog'];
        if (!isset($data['mod']) || !in_array($data['mod'], $allowed))
            return;

        // Start session for cart if not exists
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        if (!isset($_SESSION['nix_cart']))
            $_SESSION['nix_cart'] = [];

        // Handle POST Actions
        $this->handleActions();

        // Determine View
        $view = $data['mod'];
        if ($view === 'store') {
            $view = $_GET['view'] ?? 'catalog';
        }

        switch ($view) {
            case 'cart':
                return NixCart::render();
            case 'checkout':
                return NixCheckout::render();
            case 'payment':
                return NixPayment::render();
            case 'catalog':
            default:
                return NixCatalog::render();
            case 'ajax_catalog':
                return NixCatalog::ajaxCatalog();
        }
    }

    /**
     * Action Controller (Add/Remove from Cart)
     */
    public function handleActions()
    {
        if (self::$actionsProcessed)
            return;

        if (isset($_POST['nix_action'])) {
            $action = $_POST['nix_action'];
            $id = Typo::int($_POST['product_id'] ?? 0);

            if ($action == 'add' && $id > 0) {
                $qty = Typo::int($_POST['qty'] ?? 1);
                $_SESSION['nix_cart'][$id] = ($_SESSION['nix_cart'][$id] ?? 0) + $qty;
                $GLOBALS['alertSuccess'] = "Product added to cart!";
            }

            if ($action == 'remove' && $id > 0) {
                unset($_SESSION['nix_cart'][$id]);
            }

            if ($action == 'update' && $id > 0) {
                $qty = Typo::int($_POST['qty'] ?? 1);
                if ($qty <= 0)
                    unset($_SESSION['nix_cart'][$id]);
                else
                    $_SESSION['nix_cart'][$id] = $qty;
            }
        }
        self::$actionsProcessed = true;
    }

    /**
     * Injects Add to Cart button on single post view
     */
    public function injectAddToCartButton($args)
    {
        $post = $args[0] ?? null;
        if (!$post)
            return '';

        // Handle both object and array inputs
        $type = is_object($post) ? ($post->type ?? '') : ($post['type'] ?? '');
        $id = is_object($post) ? ($post->id ?? 0) : ($post['id'] ?? 0);

        if ($type !== 'nixomers')
            return '';

        $price = Posts::getParam('price', $id) ?: 0;
        $currency = Options::v('nixomers_currency') ?: 'IDR';
        $stock = Posts::getParam('stock', $id) ?: 0;
        $framework = $this->getFramework();

        if ($framework === 'tailwindcss') {
            $html = '
            <div class="bg-gray-50 border border-gray-100 rounded-3xl p-8 mt-12 shadow-sm">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-center md:text-left">
                        <h3 class="text-3xl font-black text-blue-600 mb-1">' . $currency . ' ' . $this->formatCurrency($price) . '</h3>
                        <p class="text-gray-400 text-sm flex items-center justify-center md:justify-start gap-2"><i class="bi bi-box-seam"></i> Stock Availability: <span class="font-bold text-gray-600">' . $stock . '</span></p>
                    </div>
                    <div class="w-full md:w-auto">
                        <form method="post" action="' . Url::mod('cart') . '" class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="relative w-24">
                                <input type="number" name="qty" value="1" min="1" max="' . $stock . '" class="w-full py-3 px-4 text-center bg-white border border-gray-200 rounded-full focus:ring-2 focus:ring-blue-100 outline-none transition-all font-bold">
                            </div>
                            <input type="hidden" name="product_id" value="' . $id . '">
                            <input type="hidden" name="nix_action" value="add">
                            <button type="submit" class="w-full sm:w-auto px-10 py-3.5 bg-blue-600 text-white font-extrabold rounded-full shadow-lg shadow-blue-100 hover:bg-blue-700 hover:-translate-y-1 transition-all flex items-center justify-center">
                                <i class="bi bi-cart-plus mr-2"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>';
        } else {
            $html = '
            <div class="card border-0 bg-light rounded-4 p-4 mt-5 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="fw-bold text-primary mb-1">' . $currency . ' ' . $this->formatCurrency($price) . '</h3>
                        <p class="text-muted small mb-0"><i class="bi bi-box-seam me-1"></i> Stock Availability: ' . $stock . '</p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <form method="post" action="' . Url::mod('cart') . '" class="d-inline-flex gap-2">
                            <input type="number" name="qty" value="1" min="1" max="' . $stock . '" class="form-control rounded-pill text-center" style="width: 80px;">
                            <input type="hidden" name="product_id" value="' . $id . '">
                            <input type="hidden" name="nix_action" value="add">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-cart-plus me-2"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>';
        }
        return $html;
    }

    /**
     * Log Inventory Movement and update stock level
     */
    public static function logInventory($pId, $amount, $type, $ref = '', $location = 0, $notes = '')
    {
        $pId = Typo::int($pId);
        $amount = Typo::int($amount);
        $location = Typo::int($location);

        // Get current stock
        $curStock = (int) Posts::getParam('stock', $pId);

        // Calculate new stock
        if (strtoupper($type) == 'IN') {
            $newStock = $curStock + $amount;
        } else {
            $newStock = $curStock - $amount;
        }

        // Persist new stock
        if (Posts::existParam('stock', $pId)) {
            Posts::editParam('stock', $newStock, $pId);
        } else {
            Posts::addParam('stock', $newStock, $pId);
        }

        // Record Log
        return Query::table('nix_inventory')->insert([
            'post_id' => $pId,
            'type' => strtoupper($type),
            'amount' => $amount,
            'current_stock' => $newStock,
            'location_id' => $location,
            'reference' => Typo::cleanX($ref),
            'notes' => Typo::cleanX($notes)
        ]);
    }

    /**
     * Hook handler to log initial stock when new product is added
     */
    public static function logInitialStock($data)
    {
        // We need the last inserted ID
        $pId = Posts::$last_id;
        $postType = $data['type'] ?? '';

        if ($postType == 'nixomers' && isset($data['param']['stock'])) {
            $initialStock = (int) $data['param']['stock'];
            if ($initialStock > 0) {
                self::logInventory($pId, $initialStock, 'IN', 'Initial Stock', 0, 'Automatically logged on product creation');
            }
        }
    }

    /**
     * Fetch stock locations from categories table
     * @return array
     */
    public static function getStockLocations()
    {
        $locations = ['0' => 'Main Warehouse']; // Default fallback
        if (class_exists('Query')) {
            $cat = Query::table('cat')->where('type', 'stock_location')->get();
            if (!empty($cat)) {
                foreach ($cat as $c) {
                    $locations[$c->id] = $c->name;
                }
            }
        }
        return $locations;
    }

    public static function getMaterials()
    {
        return self::getCategoryByType('material', '-- Select Material --');
    }

    public static function getBrands()
    {
        return self::getCategoryByType('brand', '-- Select Brand --');
    }

    public static function getSuppliers()
    {
        return self::getCategoryByType('supplier', '-- Select Supplier --');
    }

    public static function getProductTypes($brand = '')
    {
        $items = ['' => '-- Select Type --'];
        if (class_exists('Query')) {
            $q = Query::table('cat')->where('type', 'product_type');
            $cat = $q->get();
            if (!empty($cat)) {
                foreach ($cat as $c) {
                    $catBrand = Categories::getParam('brand', $c->id);
                    if ($brand != '') {
                        if ($catBrand == $brand) {
                            $items[$c->name] = $c->name;
                        }
                    } else {
                        $label = $c->name . ($catBrand ? " [{$catBrand}]" : "");
                        $items[$c->name] = $label;
                    }
                }
            }
        }
        return $items;
    }

    public static function getWarranties()
    {
        $items = ['' => '-- Select Warranty --'];
        if (class_exists('Query')) {
            $cat = Query::table('cat')->where('type', 'warranty')->get();
            if (!empty($cat)) {
                foreach ($cat as $c) {
                    $duration = Categories::getParam('duration', $c->id);
                    $label = $c->name . ($duration ? " ({$duration})" : "");
                    $items[$c->name] = $label;
                }
            }
        }
        return $items;
    }

    /**
     * Generic helper to fetch category list by type
     */
    public static function getCategoryByType($type, $placeholder = '-- Select --')
    {
        $items = ['' => $placeholder];
        if (class_exists('Query')) {
            $cat = Query::table('cat')->where('type', $type)->get();
            if (!empty($cat)) {
                foreach ($cat as $c) {
                    $items[$c->name] = $c->name;
                }
            }
        }
        return $items;
    }

    /**
     * Get Shipping Rates from KiriminAja
     * @param int $destinationId
     * @param int $weight
     * @return array
     */
    public function getKiriminajaRates($destinationId, $weight = 1000)
    {
        $token = Options::v('nix_kiriminaja_token');
        $mode = Options::v('nix_kiriminaja_mode') ?: 'sandbox';

        // Origin city ID - for demo or if not set, we might need a default
        $originId = (int) Options::v('nix_kiriminaja_origin_id') ?: 0;

        if (empty($token) || $originId === 0) {
            return ['status' => false, 'message' => 'KiriminAja settings not configured'];
        }

        try {
            \KiriminAja\Base\Config\KiriminAjaConfig::setApiTokenKey($token);
            \KiriminAja\Base\Config\KiriminAjaConfig::setMode($mode === 'sandbox' ? \KiriminAja\Base\Config\Cache\Mode::Staging : \KiriminAja\Base\Config\Cache\Mode::Production);

            $data = new \KiriminAja\Models\ShippingPriceData();
            $data->origin = $originId;
            $data->destination = (int) $destinationId;
            $data->weight = (int) $weight;

            $service = new \KiriminAja\Services\Shipping\PriceService($data);
            $response = $service->call();

            if ($response->status) {
                return [
                    'status' => true,
                    'data' => $response->data
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $response->message
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get Shipping Rates from API.CO.ID
     */
    public function getApicoidRates($origin_village_id, $dest_village_id, $weight = 1000)
    {
        $token = Options::v('nix_apicoid_token');
        if (empty($token))
            return ['status' => false, 'message' => 'API.CO.ID Token not configured'];

        $url = "https://use.api.co.id/expedition/shipping-cost";
        $params = http_build_query([
            'origin' => $origin_village_id,
            'destination' => $dest_village_id,
            'weight' => $weight
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$url}?{$params}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-co-id: {$token}",
            "Accept: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        unset($ch);

        $data = json_decode($response, true);
        if ($httpCode === 200 && isset($data['is_success']) && $data['is_success'] === true) {
            return ['status' => true, 'data' => $data['data']];
        } else {
            return ['status' => false, 'message' => $data['message'] ?? 'Failed to fetch rates from API.CO.ID'];
        }
    }


}

