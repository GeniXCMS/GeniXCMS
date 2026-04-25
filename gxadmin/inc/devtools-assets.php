<?php
/**
 * GeniXCMS - Developer Tools: Asset Inspector
 * Lists all registered and enqueued assets.
 *
 * @since 2.4.0
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$registry = Asset::getRegistry();
$queue    = Asset::getQueue();

// Flatten queue into a set of enqueued IDs
$enqueuedIds = array_merge(
    array_keys($queue['header'] ?? []),
    array_keys($queue['footer'] ?? [])
);

// Build filter
$filterType = isset($_GET['filter_type']) ? Typo::cleanX($_GET['filter_type']) : '';
$filterCtx  = isset($_GET['filter_ctx'])  ? Typo::cleanX($_GET['filter_ctx'])  : '';
$filterQ    = isset($_GET['q'])           ? Typo::cleanX($_GET['q'])           : '';

// Stats
$totalReg     = count($registry);
$totalEnqueued = count(array_unique($enqueuedIds));
$totalJs  = count(array_filter($registry, fn($a) => $a['type'] === 'js'));
$totalCss = count(array_filter($registry, fn($a) => $a['type'] === 'css'));

// Build rows
$rows = [];
foreach ($registry as $id => $asset) {
    // Apply filters
    if ($filterType && $asset['type'] !== $filterType) continue;
    if ($filterCtx  && ($asset['context'] ?? 'admin') !== $filterCtx) continue;
    if ($filterQ    && stripos($id, $filterQ) === false && stripos($asset['src'], $filterQ) === false) continue;

    $isEnqueued = in_array($id, $enqueuedIds);
    $pos        = $asset['pos'] ?? 'footer';
    $type       = $asset['type'] ?? 'js';
    $context    = $asset['context'] ?? 'admin';
    $priority   = $asset['priority'] ?? 20;
    $deps       = implode(', ', $asset['deps'] ?? []);
    $src        = $asset['src'] ?? '';

    // Truncate long src for display
    $srcDisplay = strlen($src) > 80 ? substr($src, 0, 77) . '...' : $src;
    $isLocal    = !str_starts_with($src, 'http');
    $isRaw      = $type === 'raw';

    $typeBadge = match($type) {
        'js'  => "<span class='badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2'>JS</span>",
        'css' => "<span class='badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2'>CSS</span>",
        'raw' => "<span class='badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2'>RAW</span>",
        default => "<span class='badge bg-light text-muted border rounded-pill px-2'>{$type}</span>",
    };

    $ctxBadge = match($context) {
        'admin'    => "<span class='badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 extra-small'>admin</span>",
        'frontend' => "<span class='badge bg-success bg-opacity-10 text-success rounded-pill px-2 extra-small'>frontend</span>",
        'all'      => "<span class='badge bg-dark bg-opacity-10 text-dark rounded-pill px-2 extra-small'>all</span>",
        default    => "<span class='badge bg-light text-muted rounded-pill px-2 extra-small'>{$context}</span>",
    };

    $posBadge = $pos === 'header'
        ? "<span class='extra-small text-muted'><i class='bi bi-arrow-up-circle me-1'></i>header</span>"
        : "<span class='extra-small text-muted'><i class='bi bi-arrow-down-circle me-1'></i>footer</span>";

    $enqueuedBadge = $isEnqueued
        ? "<span class='badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2'><i class='bi bi-check-circle me-1'></i>Enqueued</span>"
        : "<span class='badge bg-light text-muted border rounded-pill px-2'>Registered</span>";

    $localBadge = $isRaw ? '' : ($isLocal
        ? "<span class='badge bg-success bg-opacity-10 text-success rounded-pill px-2 extra-small ms-1'><i class='bi bi-hdd me-1'></i>local</span>"
        : "<span class='badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 extra-small ms-1'><i class='bi bi-cloud me-1'></i>cdn</span>");

    $srcHtml = $isRaw
        ? "<span class='text-muted extra-small fst-italic'>inline raw HTML/JS</span>"
        : "<code class='extra-small text-break' style='word-break:break-all'>{$srcDisplay}</code>{$localBadge}";

    $rows[] = [
        ['content' => "<div class='ps-4 py-2'><div class='fw-bold text-dark small'>{$id}</div><div class='mt-1'>{$typeBadge} {$ctxBadge}</div></div>", 'class' => 'p-0'],
        ['content' => $srcHtml, 'class' => 'py-2'],
        ['content' => "{$posBadge}<br><span class='extra-small text-muted'>priority: {$priority}</span>", 'class' => 'py-2'],
        ['content' => $deps ? "<code class='extra-small text-muted'>{$deps}</code>" : "<span class='text-muted extra-small'>—</span>", 'class' => 'py-2'],
        ['content' => "<div class='pe-4'>{$enqueuedBadge}</div>", 'class' => 'text-end pe-0 py-2'],
    ];
}

$schema = [
    'header' => [
        'title'    => _('Asset Inspector'),
        'subtitle' => _('All registered and enqueued assets in the current request.'),
        'icon'     => 'bi bi-box-seam',
        'button'   => [
            'type'  => 'link',
            'url'   => 'index.php?page=devtools-hooks',
            'label' => _('View Hooks'),
            'icon'  => 'bi bi-diagram-3',
            'class' => 'btn btn-outline-secondary rounded-pill px-4 fw-bold',
        ],
    ],
    'content' => [
        // Stats row
        [
            'type'  => 'stat_cards',
            'size'  => 'small',
            'items' => [
                ['label' => _('Registered'),  'value' => $totalReg,      'icon' => 'bi bi-archive',       'color' => 'primary', 'width' => 3],
                ['label' => _('Enqueued'),    'value' => $totalEnqueued, 'icon' => 'bi bi-check2-circle',  'color' => 'success', 'width' => 3],
                ['label' => _('JS Files'),    'value' => $totalJs,       'icon' => 'bi bi-filetype-js',    'color' => 'warning', 'width' => 3],
                ['label' => _('CSS Files'),   'value' => $totalCss,      'icon' => 'bi bi-filetype-css',   'color' => 'info',    'width' => 3],
            ],
        ],
        // Filter bar
        [
            'type' => 'raw',
            'html' => '
            <form method="get" class="d-flex flex-wrap gap-2 mb-4 align-items-center">
                <input type="hidden" name="page" value="devtools-assets">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-white" style="max-width:260px">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted small"></i></span>
                    <input type="text" name="q" class="form-control border-0 shadow-none small" placeholder="' . _('Search ID or URL...') . '" value="' . htmlspecialchars($filterQ) . '">
                </div>
                <select name="filter_type" class="form-select form-select-sm rounded-pill border shadow-sm fw-bold" style="max-width:120px">
                    <option value="">' . _('All Types') . '</option>
                    <option value="js"'  . ($filterType === 'js'  ? ' selected' : '') . '>JS</option>
                    <option value="css"' . ($filterType === 'css' ? ' selected' : '') . '>CSS</option>
                    <option value="raw"' . ($filterType === 'raw' ? ' selected' : '') . '>RAW</option>
                </select>
                <select name="filter_ctx" class="form-select form-select-sm rounded-pill border shadow-sm fw-bold" style="max-width:140px">
                    <option value="">' . _('All Contexts') . '</option>
                    <option value="admin"'    . ($filterCtx === 'admin'    ? ' selected' : '') . '>Admin</option>
                    <option value="frontend"' . ($filterCtx === 'frontend' ? ' selected' : '') . '>Frontend</option>
                    <option value="all"'      . ($filterCtx === 'all'      ? ' selected' : '') . '>All</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">' . _('Filter') . '</button>
                <a href="index.php?page=devtools-assets" class="btn btn-light btn-sm rounded-pill px-3 border fw-bold">' . _('Reset') . '</a>
            </form>',
        ],
        // Table
        [
            'type'       => 'card',
            'no_padding' => true,
            'title'      => sprintf(_('%d assets shown'), count($rows)),
            'icon'       => 'bi bi-list-ul',
            'body_elements' => [[
                'type'    => 'table',
                'headers' => [
                    ['content' => _('ID / Type'), 'class' => 'ps-4 py-3'],
                    _('Source'),
                    _('Position / Priority'),
                    _('Dependencies'),
                    ['content' => _('Status'), 'class' => 'text-end pe-4'],
                ],
                'rows'          => $rows,
                'empty_message' => _('No assets match the current filter.'),
            ]],
        ],
    ],
];

$builder = new UiBuilder($schema);
$builder->render();
