<?php
/**
 * Nixomers BI Dashboard View
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$currency = Options::v('nixomers_currency') ?: 'IDR';

// Aggregated Metrics
$totalRevenue = Query::table('nix_transactions')->where('type', 'income')->sum('amount');
$todayOrders = Query::table('nix_orders')->where('date', 'LIKE', date('Y-m-d') . '%')->count();
$todaySales = Query::table('nix_transactions')->where('type', 'income')->where('date', 'LIKE', date('Y-m-d') . '%')->sum('amount');
$totalCustomers = Query::table('nix_orders')->select('customer_email')->distinct()->count();
$totalOrders = Query::table('nix_orders')->count();

// Chart Logic: Last 7 Days Sales
$chartLabels = [];
$chartData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chartLabels[] = date('D', strtotime($date));
    $val = Query::table('nix_transactions')
        ->where('type', 'income')
        ->where('date', 'LIKE', $date . '%')
        ->sum('amount');
    $chartData[] = (float) $val;
}

// Recent Orders fetch
$recentOrders = Query::table('nix_orders')->orderBy('id', 'DESC')->limit(5)->get();
$recentRows = [];
foreach ($recentOrders as $ro) {
    $stColor = match ($ro->status) {
        'paid', 'completed', 'delivered' => 'success',
        'cancelled', 'expired' => 'danger',
        'shipped', 'onprocess' => 'primary',
        default => 'warning'
    };
    $recentRows[] = [
        ['content' => '<strong>#' . $ro->order_id . '</strong>'],
        ['content' => $ro->customer_name],
        ['content' => $currency . ' ' . number_format($ro->total, 0)],
        ['content' => '<span class="badge bg-' . $stColor . ' bg-opacity-10 text-' . $stColor . ' rounded-pill px-2 fw-bold">' . strtoupper($ro->status) . '</span>'],
        ['content' => '<a href="' . $mod_url . '&sel=orders&q=' . $ro->order_id . '" class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm">View</a>']
    ];
}

$schema = [
    'header' => [
        'title' => 'Business Overview',
        'subtitle' => 'At a glance performance and growth metrics for your commerce operations.',
        'icon' => 'bi bi-pie-chart-fill',
        'button' => [
            'type' => 'link',
            'href' => 'index.php?page=mods&mod=nixomers&sel=analytics',
            'label' => 'Full Analytics Report',
            'icon' => 'bi bi-graph-up',
            'class' => 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm'
        ]
    ],
    'content' => [
        [
            'type' => 'stat_cards',
            'size' => 'large',
            'items' => Hooks::filter('nixomers_dashboard_stat_cards', [
                ['label' => 'Total Revenue', 'value' => $currency . ' ' . number_format($totalRevenue, 0), 'icon' => 'bi bi-cash-stack', 'color' => 'success'],
                ['label' => 'Today\'s Sales', 'value' => $currency . ' ' . number_format($todaySales, 0), 'icon' => 'bi bi-lightning-charge', 'color' => 'primary'],
                ['label' => 'Today\'s Orders', 'value' => (string) $todayOrders, 'icon' => 'bi bi-cart-dash', 'color' => 'warning'],
                ['label' => 'Total Customers', 'value' => (string) $totalCustomers, 'icon' => 'bi bi-people', 'color' => 'dark']
            ])
        ],
        [
            'type' => 'grid',
            'content' => [
                [
                    'type' => 'col',
                    'class' => 'col-lg-8',
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => 'Sales Performance Trend',
                            'icon' => 'bi bi-graph-up',
                            'subtitle' => 'Revenue growth and sales velocity over the last 7 days',
                            'body_elements' => [
                                [
                                    'type' => 'chart',
                                    'id' => 'salesChart',
                                    'chart_type' => 'line',
                                    'height' => '320px',
                                    'chart_data' => [
                                        'labels' => $chartLabels,
                                        'datasets' => [
                                            [
                                                'label' => 'Sales (' . $currency . ')',
                                                'data' => $chartData,
                                                'borderColor' => '#0d6efd',
                                                'backgroundColor' => 'rgba(13, 110, 253, 0.05)',
                                                'fill' => true,
                                                'tension' => 0.4,
                                                'pointRadius' => 5,
                                                'pointHoverRadius' => 8,
                                                'borderWidth' => 3
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'col',
                    'class' => 'col-lg-4',
                    'content' => [
                        [
                            'type' => 'card',
                            'title' => 'Quick Management',
                            'icon' => 'bi bi-lightning-fill',
                            'body_elements' => [
                                [
                                    'type' => 'raw',
                                    'html' => '
                                    <div class="d-grid gap-3">
                                        <a href="index.php?page=posts&act=add&type=nixomers&token=' . TOKEN . '" class="btn btn-primary rounded-4 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-between">
                                            <span><i class="bi bi-plus-circle me-2"></i> Add New Product</span>
                                            <i class="bi bi-chevron-right opacity-50"></i>
                                        </a>
                                        <a href="' . $mod_url . '&sel=orders" class="btn btn-white border rounded-4 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-between text-dark">
                                            <span><i class="bi bi-cart3 me-2 text-success"></i> Manage Orders</span>
                                            <span class="badge bg-success rounded-pill">' . $totalOrders . '</span>
                                        </a>
                                        <a href="' . $mod_url . '&sel=transactions" class="btn btn-white border rounded-4 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-between text-dark">
                                            <span><i class="bi bi-bank me-2 text-warning"></i> Financial Ledger</span>
                                            <i class="bi bi-chevron-right opacity-50"></i>
                                        </a>
                                    </div>
                                    '
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            'type' => 'card',
            'title' => 'Recent Activity',
            'subtitle' => 'Latest incoming orders needing attention',
            'icon' => 'bi bi-activity',
            'no_padding' => true,
            'header_action' => '<a href="' . $mod_url . '&sel=orders" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border shadow-sm">View All Orders</a>',
            'body_elements' => [
                [
                    'type' => 'table',
                    'headers' => [['content' => 'ID', 'class' => 'ps-4 py-3'], 'Customer', 'Amount', 'Status', ['content' => 'Action', 'class' => 'pe-4 text-center']],
                    'rows' => $recentRows,
                    'empty_message' => 'No activity found.'
                ]
            ]
        ]
    ]
];

// Allow developers to modify the entire dashboard schema
$schema = Hooks::filter('nixomers_dashboard_schema', $schema);

$ui = new UiBuilder($schema);
echo $ui->render();
