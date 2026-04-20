<?php
/**
 * Nixomers Analytics Dashboard
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$currency = Options::v('nixomers_currency') ?: 'IDR';

// Date Range Filter Logic
$startDate = Typo::cleanX($_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days')));
$endDate   = Typo::cleanX($_GET['end_date'] ?? date('Y-m-d'));

// 1. Fetch Top Level Metrics with Date Filter
$totalSales = Query::table('nix_orders')
    ->where('status', '!=', 'cancelled')
    ->whereRaw("date BETWEEN ? AND ?", [$startDate . " 00:00:00", $endDate . " 23:59:59"])
    ->sum('total');

$netIncome = Query::table('nix_transactions')
    ->where('status', 'completed')
    ->whereRaw("date BETWEEN ? AND ?", [$startDate . " 00:00:00", $endDate . " 23:59:59"])
    ->sum('net');

$totalOrders = Query::table('nix_orders')
    ->whereRaw("date BETWEEN ? AND ?", [$startDate . " 00:00:00", $endDate . " 23:59:59"])
    ->count();

$avgOrder = ($totalOrders > 0) ? ($totalSales / $totalOrders) : 0;

// 2. Sales Trend (Dynamic based on selected range)
$salesTrend = ['labels' => [], 'gross' => [], 'net' => []];
$period = new DatePeriod(
    new DateTime($startDate),
    new DateInterval('P1D'),
    (new DateTime($endDate))->modify('+1 day')
);

foreach ($period as $dateObj) {
    $date = $dateObj->format('Y-m-d');
    $gross = Query::table('nix_orders')
        ->where('date', 'LIKE', "{$date}%")
        ->where('status', '!=', 'cancelled')
        ->sum('total');
        
    $net = Query::table('nix_transactions')
        ->where('date', 'LIKE', "{$date}%")
        ->where('status', 'completed')
        ->sum('net');
    
    $salesTrend['labels'][] = $dateObj->format('d M');
    $salesTrend['gross'][]  = (float) $gross;
    $salesTrend['net'][]    = (float) $net;
}

// 3. Order Status Distribution with Date Filter
$statuses = Query::table('nix_orders')
    ->select('status, COUNT(status) as total_count')
    ->whereRaw("date BETWEEN ? AND ?", [$startDate . " 00:00:00", $endDate . " 23:59:59"])
    ->groupBy('status')
    ->get();

$statusDist = ['labels' => [], 'data' => []];
if ($statuses) {
    foreach ($statuses as $s) {
        $statusDist['labels'][] = strtoupper(Typo::Xclean($s->status));
        $statusDist['data'][] = (int) ($s->total_count ?? 0);
    }
}

// 4. Product Statistics with Date Filter
$allProducts = Query::table('posts')->where('type', 'nixomers')->where('status', '1')->get();
$productStats = [];
foreach ($allProducts as $p) {
    $productStats[$p->id] = [
        'title' => Typo::Xclean($p->title),
        'qty' => 0,
        'revenue' => 0
    ];
}

$recentOrders = Query::table('nix_orders')
    ->where('status', '!=', 'cancelled')
    ->whereRaw("date BETWEEN ? AND ?", [$startDate . " 00:00:00", $endDate . " 23:59:59"])
    ->orderBy('date', 'DESC')
    ->get();
foreach ($recentOrders as $ro) {
    $items = json_decode($ro->cart_items ?? '', true) ?: [];
    foreach ($items as $pId => $qty) {
        if (isset($productStats[$pId])) {
            $productStats[$pId]['qty'] += $qty;
            $price = (float) Posts::getParam('price', $pId) ?: 0;
            $productStats[$pId]['revenue'] += ($price * $qty);
        }
    }
}

// Top Selling
$topProductsList = $productStats;
uasort($topProductsList, function($a, $b) { return $b['qty'] <=> $a['qty']; });
$topProducts = array_slice($topProductsList, 0, 5, true);

// Least Selling (Dead Stock)
$leastProductsList = $productStats;
 uasort($leastProductsList, function($a, $b) { return $a['qty'] <=> $b['qty']; });
$leastProducts = array_slice($leastProductsList, 0, 5, true);

// 5. Top Cities Data
$cities = Query::table('nix_orders')
    ->select('shipping_city, COUNT(id) as total_orders')
    ->whereRaw("date BETWEEN ? AND ?", [$startDate . " 00:00:00", $endDate . " 23:59:59"])
    ->where('status', '!=', 'cancelled')
    ->groupBy('shipping_city')
    ->orderBy('total_orders', 'DESC')
    ->limit(10)
    ->get();

$cityStats = ['labels' => [], 'data' => []];
if ($cities) {
    foreach ($cities as $c) {
        $cityStats['labels'][] = $c->shipping_city ? Typo::Xclean($c->shipping_city) : 'Unknown';
        $cityStats['data'][] = (int) $c->total_orders;
    }
}

// 6. Payment Methods Ratio
$pmQuery = Query::table('nix_orders')
    ->select('payment_method, COUNT(id) as total_count')
    ->whereRaw("date BETWEEN ? AND ?", [$startDate . " 00:00:00", $endDate . " 23:59:59"])
    ->groupBy('payment_method')
    ->get();

$paymentStats = ['labels' => [], 'data' => []];
if ($pmQuery) {
    foreach ($pmQuery as $p) {
        $paymentStats['labels'][] = strtoupper(Typo::Xclean($p->payment_method ?: 'pending'));
        $paymentStats['data'][] = (int) $p->total_count;
    }
}

// 7. Process Top 10 Products for Chart
$top10Products = array_slice($topProductsList, 0, 10, true);
$productChart = ['labels' => [], 'data' => []];
foreach ($top10Products as $pId => $stat) {
    $productChart['labels'][] = $stat['title'];
    $productChart['data'][] = $stat['qty'];
}

$schema = [
    'header' => [
        'title' => 'Commerce Analytics',
        'subtitle' => 'Insights and performance data for ' . date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate)),
        'icon' => 'bi bi-graph-up-arrow'
    ],
    'content' => [
        // 0. Date Filters (Raw HTML)
        [
            'type' => 'raw',
            'html' => '
            <form method="get" action="index.php" id="analyticsFilterForm" class="mb-4">
                <input type="hidden" name="page" value="mods">
                <input type="hidden" name="mod" value="nixomers">
                <input type="hidden" name="sel" value="analytics">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-2 bg-light bg-opacity-50">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-auto ms-3 fw-bold small text-muted"><i class="bi bi-calendar-range me-1"></i> PERIOD FILTER</div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm rounded-pill overflow-hidden border bg-white">
                                    <span class="input-group-text bg-white border-0 ps-3 small text-muted">FROM</span>
                                    <input type="date" name="start_date" class="form-control border-0 ps-1" value="'.$startDate.'">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm rounded-pill overflow-hidden border bg-white">
                                    <span class="input-group-text bg-white border-0 ps-3 small text-muted">TO</span>
                                    <input type="date" name="end_date" class="form-control border-0 ps-1" value="'.$endDate.'">
                                </div>
                            </div>
                            <div class="col-md-auto ms-auto pe-3">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-arrow-repeat me-1"></i> Sync Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            '
        ],
        // 1. Core Metrics
        [
            'type' => 'stat_cards',
            'style' => 'modern',
            'items' => [
                [
                    'label' => 'TOTAL GROSS SALES',
                    'value' => $currency . ' ' . number_format($totalSales, 0, ',', '.'),
                    'icon' => 'bi bi-graph-up',
                    'color' => 'primary',
                    'sub' => 'Lifetime accumulated'
                ],
                [
                    'label' => 'NET SETTLEMENT',
                    'value' => $currency . ' ' . number_format($netIncome, 0, ',', '.'),
                    'icon' => 'bi bi-shield-check',
                    'color' => 'success',
                    'sub' => 'After fees & tax'
                ],
                [
                    'label' => 'TOTAL ORDERS',
                    'value' => number_format($totalOrders),
                    'icon' => 'bi bi-cart-check',
                    'color' => 'info',
                    'sub' => 'Processed units'
                ],
                [
                    'label' => 'AVG. ORDER VALUE',
                    'value' => $currency . ' ' . number_format($avgOrder, 0, ',', '.'),
                    'icon' => 'bi bi-calculator',
                    'color' => 'warning',
                    'sub' => 'Per customer visit'
                ]
            ]
        ],
        // 2. Charts Row
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 8,
                    'content' => [
                        'type' => 'card',
                        'title' => 'Sales Performance',
                        'icon' => 'bi bi-activity',
                        'body_elements' => [
                            ['type' => 'raw', 'html' => '<div style="height:320px;"><canvas id="salesChart"></canvas></div>']
                        ]
                    ]
                ],
                [
                    'width' => 4,
                    'content' => [
                        'type' => 'card',
                        'title' => 'Order Status',
                        'icon' => 'bi bi-pie-chart',
                        'body_elements' => [
                            ['type' => 'raw', 'html' => '<div style="height:320px;"><canvas id="statusChart"></canvas></div>']
                        ]
                    ]
                ]
            ]
        ],
        // 2.1 Regional & Payment Analytics
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 7,
                    'content' => [
                        'type' => 'card',
                        'title' => 'Top Buyer Cities',
                        'icon' => 'bi bi-geo-alt',
                        'body_elements' => [
                            ['type' => 'raw', 'html' => '<div style="height:320px;"><canvas id="cityChart"></canvas></div>']
                        ]
                    ]
                ],
                [
                    'width' => 5,
                    'content' => [
                        'type' => 'card',
                        'title' => 'Payment Method Ratio',
                        'icon' => 'bi bi-credit-card',
                        'body_elements' => [
                            ['type' => 'raw', 'html' => '<div style="height:320px;"><canvas id="paymentChart"></canvas></div>']
                        ]
                    ]
                ]
            ]
        ],
        // 3. Product Tables Row
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 6,
                    'content' => [
                        'type' => 'card',
                        'title' => 'Top Selling Products',
                        'icon' => 'bi bi-star-fill',
                        'class' => 'h-100',
                        'body_elements' => [
                            ['type' => 'raw', 'html' => (function() use ($topProducts, $currency) {
                                $out = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
                                $out .= '<thead class="bg-light"><tr><th class="ps-4">Product</th><th class="text-center">Sold</th><th class="text-end pe-4">Revenue</th></tr></thead><tbody>';
                                foreach ($topProducts as $pId => $stat) {
                                    $out .= '<tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">'.$stat['title'].'</div>
                                            <div class="small text-muted">ID: #'.$pId.'</div>
                                        </td>
                                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">'.$stat['qty'].'</span></td>
                                        <td class="text-end pe-4 fw-bold text-dark">'.$currency.' '.number_format($stat['revenue'], 0, ',', '.').'</td>
                                    </tr>';
                                }
                                $out .= '</tbody></table></div>';
                                return $out;
                            })()]
                        ]
                    ]
                ],
                [
                    'width' => 6,
                    'content' => [
                        'type' => 'card',
                        'title' => 'Least Sold / Dead Stock',
                        'icon' => 'bi bi-arrow-down-circle',
                        'class' => 'h-100',
                        'body_elements' => [
                            ['type' => 'raw', 'html' => (function() use ($leastProducts, $currency) {
                                $out = '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
                                $out .= '<thead class="bg-light"><tr><th class="ps-4">Product</th><th class="text-center">Sold</th><th class="text-end pe-4">Revenue</th></tr></thead><tbody>';
                                foreach ($leastProducts as $pId => $stat) {
                                    $out .= '<tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">'.$stat['title'].'</div>
                                            <div class="small text-muted">ID: #'.$pId.'</div>
                                        </td>
                                        <td class="text-center"><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">'.$stat['qty'].'</span></td>
                                        <td class="text-end pe-4 fw-bold text-dark">'.$currency.' '.number_format($stat['revenue'], 0, ',', '.').'</td>
                                    </tr>';
                                }
                                $out .= '</tbody></table></div>';
                                return $out;
                            })()]
                        ]
                    ]
                ]
            ]
        ],
        // 3.1 Best Selling Products Chart
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 12,
                    'content' => [
                        'type' => 'card',
                        'title' => 'Top 10 Best Selling Items (Current Period)',
                        'icon' => 'bi bi-graph-up-arrow',
                        'body_elements' => [
                            ['type' => 'raw', 'html' => '<div style="height:400px;"><canvas id="productChart"></canvas></div>']
                        ]
                    ]
                ]
            ]
        ],
        // 4. Chart Scripts
        [
            'type' => 'raw',
            'html' => '
            <script>
            (function() {
                function initCharts() {
                    if (typeof Chart === "undefined") {
                        setTimeout(initCharts, 100);
                        return;
                    }
                    console.log("Initializing charts...");
                    
                    const chartConfigs = [
                        {
                            id: "salesChart",
                            type: "line",
                            data: {
                                labels: '.json_encode($salesTrend['labels']).',
                                datasets: [
                                    {
                                        label: "Gross Sales",
                                        data: '.json_encode($salesTrend['gross']).',
                                        borderColor: "#2563eb",
                                        backgroundColor: "rgba(37, 99, 235, 0.1)",
                                        fill: true,
                                        tension: 0.4,
                                        borderWidth: 3
                                    },
                                    {
                                        label: "Net Income",
                                        data: '.json_encode($salesTrend['net']).',
                                        borderColor: "#10b981",
                                        backgroundColor: "rgba(16, 185, 129, 0.1)",
                                        fill: true,
                                        tension: 0.4,
                                        borderWidth: 3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: "top", labels: { usePointStyle: true, font: { weight: "bold" } } } },
                                scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
                            }
                        },
                        {
                            id: "statusChart",
                            type: "doughnut",
                            data: {
                                labels: '.json_encode($statusDist['labels']).',
                                datasets: [{
                                    data: '.json_encode($statusDist['data']).',
                                    backgroundColor: ["#2563eb", "#10b981", "#f59e0b", "#ef4444", "#6366f1", "#8b5cf6"],
                                    borderWidth: 0,
                                    cutout: "70%"
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: "bottom", labels: { padding: 20, usePointStyle: true } } }
                            }
                        },
                        {
                            id: "cityChart",
                            type: "bar",
                            data: {
                                labels: '.json_encode($cityStats['labels']).',
                                datasets: [{
                                    label: "Total Orders",
                                    data: '.json_encode($cityStats['data']).',
                                    backgroundColor: "#2563eb",
                                    borderRadius: 8
                                }]
                            },
                            options: {
                                indexAxis: "y",
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { x: { beginAtZero: true, grid: { display: false } }, y: { grid: { display: false } } }
                            }
                        },
                        {
                            id: "paymentChart",
                            type: "doughnut",
                            data: {
                                labels: '.json_encode($paymentStats['labels']).',
                                datasets: [{
                                    data: '.json_encode($paymentStats['data']).',
                                    backgroundColor: ["#6366f1", "#8b5cf6", "#ec4899", "#f43f5e", "#f97316"],
                                    borderWidth: 0,
                                    cutout: "70%"
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { position: "bottom", labels: { padding: 20, usePointStyle: true } } }
                            }
                        },
                        {
                            id: "productChart",
                            type: "bar",
                            data: {
                                labels: '.json_encode($productChart['labels']).',
                                datasets: [{
                                    label: "Units Sold",
                                    data: '.json_encode($productChart['data']).',
                                    backgroundColor: "rgba(16, 185, 129, 0.8)",
                                    borderColor: "#10b981",
                                    borderWidth: 1,
                                    borderRadius: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true, grid: { display: true, drawBorder: false } }, x: { grid: { display: false } } }
                            }
                        }
                    ];

                    chartConfigs.forEach(config => {
                        const canvas = document.getElementById(config.id);
                        if (canvas) {
                            console.log("Rendering chart: " + config.id);
                            new Chart(canvas.getContext("2d"), {
                                type: config.type,
                                data: config.data,
                                options: config.options
                            });
                        } else {
                            console.warn("Canvas not found: " + config.id);
                        }
                    });
                }

                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", initCharts);
                } else {
                    initCharts();
                }
            })();
            </script>
            <style>
                canvas { min-height: 250px !important; width: 100% !important; }
                .card-body { position: relative; }
            </style>
            '
        ]
    ]
];

$ui = new UiBuilder($schema);
echo $ui->render();

