<?php
/**
 * GeniXCMS - Developer Tools: Hook Inspector
 * Lists all registered hooks and their attached callbacks.
 *
 * @since 2.4.0
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

$allHooks = Hooks::$hooks ?? [];

$filterQ = isset($_GET['q']) ? Typo::cleanX($_GET['q']) : '';
$filterStatus = isset($_GET['filter_status']) ? Typo::cleanX($_GET['filter_status']) : '';

// Stats
$totalHooks    = count($allHooks);
$attachedHooks = count(array_filter($allHooks, fn($cbs) => !empty($cbs)));
$totalCallbacks = array_sum(array_map('count', $allHooks));

$rows = [];
foreach ($allHooks as $hookName => $callbacks) {
    // Apply filters
    if ($filterQ && stripos($hookName, $filterQ) === false) continue;
    $hasCallbacks = !empty($callbacks);
    if ($filterStatus === 'attached'  && !$hasCallbacks) continue;
    if ($filterStatus === 'empty'     &&  $hasCallbacks) continue;

    $count = count($callbacks);

    $statusBadge = $hasCallbacks
        ? "<span class='badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2'><i class='bi bi-lightning-charge me-1'></i>{$count} " . _('callback(s)') . "</span>"
        : "<span class='badge bg-light text-muted border rounded-pill px-2'><i class='bi bi-dash me-1'></i>" . _('No callbacks') . "</span>";

    // Describe each callback
    $callbackList = '';
    if ($hasCallbacks) {
        $callbackList = '<div class="mt-2 d-flex flex-column gap-1">';
        foreach ($callbacks as $i => $cb) {
            if (is_array($cb)) {
                $obj   = is_object($cb[0]) ? get_class($cb[0]) : (string)$cb[0];
                $meth  = $cb[1] ?? '?';
                $label = "<code class='extra-small'>{$obj}::{$meth}()</code>";
            } elseif ($cb instanceof Closure) {
                // Try to get file/line from ReflectionFunction
                try {
                    $rf    = new ReflectionFunction($cb);
                    $file  = basename($rf->getFileName() ?? '');
                    $line  = $rf->getStartLine();
                    $label = "<code class='extra-small'>Closure</code> <span class='extra-small text-muted'>in {$file}:{$line}</span>";
                } catch (Throwable $e) {
                    $label = "<code class='extra-small'>Closure</code>";
                }
            } elseif (is_string($cb)) {
                $label = "<code class='extra-small'>{$cb}()</code>";
            } else {
                $label = "<code class='extra-small'>" . gettype($cb) . "</code>";
            }
            $callbackList .= "<div class='d-flex align-items-center gap-2'>"
                . "<span class='badge bg-light text-muted border rounded-circle d-flex align-items-center justify-content-center' style='width:18px;height:18px;font-size:.6rem'>" . ($i + 1) . "</span>"
                . $label
                . "</div>";
        }
        $callbackList .= '</div>';
    }

    $rows[] = [
        ['content' => "<div class='ps-4 py-2'><code class='fw-bold text-dark'>{$hookName}</code></div>", 'class' => 'p-0'],
        ['content' => $statusBadge, 'class' => 'py-2'],
        ['content' => "<div class='pe-4 py-2'>{$callbackList}</div>", 'class' => 'pe-4 py-2'],
    ];
}

$schema = [
    'header' => [
        'title'    => _('Hook Inspector'),
        'subtitle' => _('All registered hooks and their attached callbacks in the current request.'),
        'icon'     => 'bi bi-diagram-3',
        'button'   => [
            'type'  => 'link',
            'url'   => 'index.php?page=devtools-assets',
            'label' => _('View Assets'),
            'icon'  => 'bi bi-box-seam',
            'class' => 'btn btn-outline-secondary rounded-pill px-4 fw-bold',
        ],
    ],
    'content' => [
        // Stats
        [
            'type'  => 'stat_cards',
            'size'  => 'small',
            'items' => [
                ['label' => _('Total Hooks'),     'value' => $totalHooks,     'icon' => 'bi bi-diagram-3',       'color' => 'primary', 'width' => 4],
                ['label' => _('With Callbacks'),  'value' => $attachedHooks,  'icon' => 'bi bi-lightning-charge', 'color' => 'success', 'width' => 4],
                ['label' => _('Total Callbacks'), 'value' => $totalCallbacks, 'icon' => 'bi bi-code-slash',       'color' => 'warning', 'width' => 4],
            ],
        ],
        // Filter bar
        [
            'type' => 'raw',
            'html' => '
            <form method="get" class="d-flex flex-wrap gap-2 mb-4 align-items-center">
                <input type="hidden" name="page" value="devtools-hooks">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border bg-white" style="max-width:280px">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted small"></i></span>
                    <input type="text" name="q" class="form-control border-0 shadow-none small" placeholder="' . _('Search hook name...') . '" value="' . htmlspecialchars($filterQ) . '">
                </div>
                <select name="filter_status" class="form-select form-select-sm rounded-pill border shadow-sm fw-bold" style="max-width:160px">
                    <option value="">' . _('All Hooks') . '</option>
                    <option value="attached"' . ($filterStatus === 'attached' ? ' selected' : '') . '>' . _('Has Callbacks') . '</option>
                    <option value="empty"'    . ($filterStatus === 'empty'    ? ' selected' : '') . '>' . _('Empty') . '</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">' . _('Filter') . '</button>
                <a href="index.php?page=devtools-hooks" class="btn btn-light btn-sm rounded-pill px-3 border fw-bold">' . _('Reset') . '</a>
            </form>',
        ],
        // Table
        [
            'type'       => 'card',
            'no_padding' => true,
            'title'      => sprintf(_('%d hooks shown'), count($rows)),
            'icon'       => 'bi bi-list-ul',
            'body_elements' => [[
                'type'    => 'table',
                'headers' => [
                    ['content' => _('Hook Name'), 'class' => 'ps-4 py-3'],
                    _('Status'),
                    ['content' => _('Attached Callbacks'), 'class' => 'pe-4'],
                ],
                'rows'          => $rows,
                'empty_message' => _('No hooks match the current filter.'),
            ]],
        ],
    ],
];

$builder = new UiBuilder($schema);
$builder->render();
