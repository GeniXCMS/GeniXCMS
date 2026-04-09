<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 2.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */


// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$rows = [];
foreach ($data['requirements'] as $name => $info) {
    if ($info['status'] === true) {
        $badgeClass = 'bg-success text-white shadow-sm';
        $iconClass = 'bi bi-check-circle-fill';
        $rowClass = '';
    } elseif ($info['status'] === false) {
        $badgeClass = 'bg-danger text-white shadow-sm';
        $iconClass = 'bi bi-dash-circle-fill';
        $rowClass = 'table-danger bg-opacity-25';
    } else {
        $badgeClass = 'bg-warning text-dark shadow-sm';
        $iconClass = 'bi bi-exclamation-circle-fill';
        $rowClass = '';
    }

    $rows[] = [
        'data' => [
            ['content' => "<span class='fw-bold text-dark font-primary'><i class='{$iconClass} me-2'></i> {$name}</span>", 'class' => 'ps-4 py-3 align-middle'],
            ['content' => "<span class='badge bg-light text-muted border px-3 py-1 rounded-pill'>{$info['req']}</span>", 'class' => 'align-middle'],
            ['content' => "<span class='badge {$badgeClass} px-3 py-1 rounded-pill'>{$info['val']}</span>", 'class' => 'align-middle'],
        ],
        'class' => $rowClass
    ];
}

$schema = [
    'header' => [
        'title' => _('System Diagnostic'),
        'subtitle' => _('Comprehensive status overview and environmental requirements check.'),
        'icon' => 'bi bi-shield-heart',
    ],
    'content' => [
        [
            'type' => 'card',
            'no_padding' => true,
            'title' => _('Operational Constraints & Requirements'),
            'subtitle' => _('Analysis of the current server environment and PHP capabilities.'),
            'icon' => 'bi bi-cpu',
            'body_elements' => [
                [
                    'type' => 'table',
                    'headers' => [
                        ['content' => _('Standard Identity'), 'class' => 'ps-4 py-3'],
                        _('Minimum Requirement'),
                        ['content' => _('Detected Environment'), 'class' => 'pe-4']
                    ],
                    'rows' => array_column($rows, 'data'),
                    'empty_message' => _('Diagnostic module could not gather environment data.')
                ]
            ],
            'footer' => '
                <div class="extra-small text-muted text-uppercase tracking-widest fw-bold d-flex align-items-center mx-3 my-1">
                    <i class="bi bi-info-circle-fill me-2 text-primary fs-5"></i>
                    ' . _("Regular health checks ensure your site remains secure and high-performing.") . '
                </div>'
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
$builder = new UiBuilder($schema);
$builder->render();
?>

<style>
    .font-primary {
        font-family: 'Outfit', sans-serif;
    }

    .extra-small {
        font-size: 0.75rem;
    }

    .tracking-widest {
        letter-spacing: 0.1em;
    }
</style>