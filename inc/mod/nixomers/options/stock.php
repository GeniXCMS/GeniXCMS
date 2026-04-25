<?php
/**
 * Nixomers Stock Management View
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$paging = Typo::int($_GET['paging'] ?? 1);
$q = Typo::cleanX($_GET['q'] ?? '');

$limit = 10;
$offset = ($paging - 1) * $limit;

$query = Query::table('posts')->where('type', 'nixomers');
if (!empty($q)) {
    $query->whereRaw("(`title` LIKE ? OR `content` LIKE ?)", ["%{$q}%", "%{$q}%"]);
}

$totalProducts = (clone $query)->count();
$products = $query->orderBy('id', 'DESC')->limit($limit, $offset)->get();
$locations = Nixomers::getStockLocations();

// Stats Calculation
$lowStockThreshold = 5;
$outOfStock = 0;
$lowStock = 0;
$totalAvailable = 0;

$allProd = Query::table('posts')->where('type', 'nixomers')->get();
foreach ($allProd as $ap) {
    $s = (int) Posts::getParam('stock', $ap->id);
    if ($s <= 0) $outOfStock++;
    elseif ($s < $lowStockThreshold) $lowStock++;
    else $totalAvailable++;
}

$stockRows = [];
$modals = [];
foreach ($products as $p) {
    $curStock = (int) Posts::getParam('stock', $p->id);
    $sku = Posts::getParam('sku', $p->id) ?: '-';
    $unit = Posts::getParam('unit', $p->id) ?: 'Pcs';
    $stColor = ($curStock <= 0) ? 'danger' : (($curStock < $lowStockThreshold) ? 'warning' : 'success');

    $stockRows[] = [
        ['content' => '<strong>#' . $p->id . '</strong>', 'class' => 'ps-4 py-3'],
        ['content' => '<div><strong>' . $p->title . '</strong><br><small class="text-muted">SKU: ' . $sku . '</small></div>'],
        ['content' => '<span class="badge bg-' . $stColor . ' bg-opacity-10 text-' . $stColor . ' rounded-pill px-3 fw-bold">' . $curStock . ' ' . $unit . '</span>'],
        ['content' => '<button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#opname' . $p->id . '"><i class="bi bi-arrow-down-up me-1"></i> Update Stock</button>', 'class' => 'pe-4 text-center']
    ];

    // Prepare Modals
    $modals[] = [
        'type' => 'modal',
        'id' => 'opname' . $p->id,
        'header' => 'Inventory Adjustment: ' . $p->title,
        'body_elements' => [
            [
                'type' => 'form',
                'action' => $mod_url . '&sel=stock&paging=' . $paging . '&q=' . $q,
                'hidden' => ['token' => TOKEN, 'nixomers_stock_update' => '1', 'p_id' => $p->id],
                'fields' => [
                    [
                        'type' => 'row',
                        'items' => [
                            [
                                'width' => 6,
                                'content' => [
                                    'type' => 'select',
                                    'name' => 'move_type',
                                    'label' => 'Movement Type',
                                    'options' => ['IN' => 'Stock In (Restock)', 'OUT' => 'Stock Out (Release/Correction)'],
                                    'selected' => 'IN'
                                ]
                            ],
                            [
                                'width' => 6,
                                'content' => ['type' => 'input', 'name' => 'amount', 'label' => 'Quantity', 'input_type' => 'number', 'value' => '1', 'required' => true]
                            ]
                        ]
                    ],
                    [
                        'type' => 'row',
                        'items' => [
                            [
                                'width' => 6,
                                'content' => [
                                    'type' => 'select',
                                    'name' => 'location_id',
                                    'label' => 'Target Warehouse',
                                    'options' => $locations
                                ]
                            ],
                            [
                                'width' => 6,
                                'content' => ['type' => 'input', 'name' => 'reference', 'label' => 'Supplier / Ref #', 'placeholder' => 'E.g: Supplier ABC']
                            ]
                        ]
                    ],
                    ['type' => 'input', 'name' => 'notes', 'label' => 'Adjustment Notes', 'placeholder' => 'Reason for this movement...'],
                    ['type' => 'button', 'name' => 'save', 'label' => 'Record Movement', 'class' => 'btn btn-primary w-100 rounded-pill py-3 fw-bold mt-3']
                ]
            ]
        ]
    ];
}

$pagingHtml = Paging::create([
    'paging' => $paging,
    'table' => 'posts',
    'max' => $limit,
    'url' => $mod_url . '&sel=stock&q=' . urlencode($q),
    'type' => 'number',
    'total' => $totalProducts
]);

// Fetch Logs
$logs = Query::table('nix_inventory')
    ->join('posts', 'nix_inventory.post_id', '=', 'posts.id')
    ->select(['nix_inventory.*', 'posts.title as product_name'])
    ->orderBy('nix_inventory.id', 'DESC')
    ->limit(10)
    ->get();

$logRows = [];
foreach ($logs as $l) {
    $badgeClass = ($l->type == 'IN') ? 'success' : 'danger';
    $prefix = ($l->type == 'IN') ? '+' : '-';
    $locName = $locations[(string)($l->location_id ?? '')] ?? 'Main Warehouse';
    
    $logRows[] = [
        ['content' => date('d/m/y H:i', strtotime($l->date)), 'class' => 'ps-4 py-3'],
        $l->product_name,
        ['content' => '<span class="text-'.$badgeClass.' fw-black">'.$prefix.$l->amount.'</span>'],
        $l->current_stock,
        $locName,
        ['content' => $l->reference, 'class' => 'pe-4']
    ];
}

$schema = [
    'header' => [
        'title' => 'Inventory Control',
        'subtitle' => 'Monitor stock levels, movements, and warehouse adjustments.',
        'icon' => 'bi bi-box-seam',
        'buttons' => [
            [
                'type' => 'link',
                'href' => 'index.php?page=categories&type=stock_location',
                'label' => 'Manage Warehouses',
                'icon' => 'bi bi-geo-alt',
                'class' => 'btn btn-light rounded-pill px-4 border shadow-none fw-bold'
            ],
            [
                'type' => 'button',
                'label' => 'Stock Report',
                'icon' => 'bi bi-file-earmark-spreadsheet',
                'class' => 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm'
            ]
        ]
    ],
    'content' => [
        [
            'type' => 'stat_cards',
            'size' => 'small',
            'items' => [
                ['label' => 'Available Products', 'value' => (string)$totalAvailable, 'icon' => 'bi bi-check-circle', 'color' => 'success'],
                ['label' => 'Low Stock Items', 'value' => (string)$lowStock, 'icon' => 'bi bi-exclamation-triangle', 'color' => 'warning'],
                ['label' => 'Out of Stock', 'value' => (string)$outOfStock, 'icon' => 'bi bi-x-circle', 'color' => 'danger']
            ]
        ],
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 7,
                    'content' => [[
                        'type' => 'card',
                        'title' => 'Stock List',
                        'icon' => 'bi bi-box-seam',
                        'no_padding' => true,
                        'header_action' => [
                            'type' => 'search_group',
                            'placeholder' => 'Search products...',
                            'value' => $q,
                            'hidden' => ['page' => 'mods', 'mod' => 'nixomers', 'sel' => 'stock']
                        ],
                        'body_elements' => [
                            [
                                'type' => 'table',
                                'headers' => [['content' => 'ID', 'class' => 'ps-4 py-3'], 'Product Name', 'Stock', ['content' => 'Adjustment', 'class' => 'pe-4 text-center']],
                                'rows' => $stockRows,
                                'empty_message' => 'No products found matching your search.'
                            ]
                        ],
                        'footer_elements' => [
                            ['type' => 'pagination', 'html' => $pagingHtml]
                        ]
                    ]]
                ],
                [
                    'width' => 5,
                    'content' => [[
                        'type' => 'card',
                        'title' => 'Movement Logs',
                        'subtitle' => 'Latest 10 stock transactions',
                        'icon' => 'bi bi-clock-history',
                        'no_padding' => true,
                        'body_elements' => [
                            [
                                'type' => 'table',
                                'headers' => [['content' => 'Date', 'class' => 'ps-4 py-3'], 'Product', 'Qty', 'After', 'Location', 'Ref'],
                                'rows' => $logRows,
                                'empty_message' => 'No movements recorded yet.'
                            ]
                        ]
                    ]]
                ]
            ]
        ]
    ]
];

// Append modals
foreach ($modals as $m) $schema['content'][] = $m;

$ui = new UiBuilder($schema);
echo $ui->render();
