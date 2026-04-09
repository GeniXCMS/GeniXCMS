<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */


// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Digital Asset Library'),
        'subtitle' => _('Manage, organize, and optimize your site media and file uploads.'),
        'icon' => 'bi bi-hdd-network',
        'button' => [
            'url' => '#',
            'label' => _('Storage Active'),
            'icon' => 'bi bi-hdd-network-fill',
            'class' => 'btn btn-white border rounded-pill px-4 shadow-sm fw-bold text-muted extra-small disabled'
        ],
    ],
    'content' => [
        [
            'type' => 'card',
            'no_padding' => true,
            'title' => _('File System Explorer'),
            'subtitle' => _('Drag & drop enabled for streamlined management.'),
            'icon' => 'bi bi-folder2-open',
            'body_elements' => [
                ['type' => 'raw', 'html' => '<div id="elfinder" class="border-0" style="min-height: 500px;"></div>']
            ]
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

$builder = new UiBuilder($schema);
$builder->render();
?>

<style>
    .extra-small {
        font-size: 0.75rem;
    }

    #elfinder {
        border: none !important;
    }

    .elfinder-workzone {
        background: #fff !important;
    }
</style>