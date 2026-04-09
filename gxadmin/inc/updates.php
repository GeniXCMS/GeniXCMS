<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 2.0.0
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

// ── PREPARE PLACEHOLDERS ──────────────────────────────────────────
$rows = [];

// Helper for loading spinner
$loader = '<div class="spinner-border spinner-border-sm text-primary opacity-50" role="status"><span class="visually-hidden">Loading...</span></div>';

// Core row
$rows[] = [
    [
        'content' => "
        <div class='d-flex align-items-center ps-4 py-3'>
            <div class='bg-primary bg-opacity-10 p-3 rounded-4 text-primary me-3 shadow-sm'>
                <i class='bi bi-cpu fs-4'></i>
            </div>
            <div>
                <div class='fw-bold text-dark h6 mb-1'>{$data['core']['name']}</div>
                <div class='extra-small text-muted opacity-75 fw-bold text-uppercase ls-1'>Current: v{$data['core']['v_current']}</div>
            </div>
        </div>",
        'class' => 'p-0'
    ],
    ['content' => "<span id='core-v-latest' class='badge bg-light text-muted border px-3 py-1 rounded-pill'>$loader</span>", 'class' => 'align-middle'],
    ['content' => "<span id='core-status' class='badge bg-light text-muted border px-3 py-1 rounded-pill shadow-sm'>$loader</span>", 'class' => 'align-middle'],
    ['content' => "<div id='core-action' class='text-end pe-4 align-middle'>$loader</div>", 'class' => 'text-end pe-4 align-middle']
];

// Modules rows
foreach ($data['mods'] as $mod) {
    $rows[] = [
        [
            'content' => "
            <div class='d-flex align-items-center ps-4 py-3'>
                <div class='bg-info bg-opacity-10 p-3 rounded-4 text-info me-3 shadow-sm'>
                    <i class='{$mod['icon']} fs-4'></i>
                </div>
                <div>
                    <div class='fw-bold text-dark h6 mb-1'>{$mod['name']}</div>
                    <div class='extra-small text-muted opacity-75 fw-bold text-uppercase ls-1'>Module: {$mod['id']}</div>
                </div>
            </div>",
            'class' => 'p-0'
        ],
        ['content' => "<span class='badge bg-light text-muted border px-3 py-1 rounded-pill'>v{$mod['v_current']}</span>", 'class' => 'align-middle'],
        ['content' => "<span id='mod-status-{$mod['id']}' class='badge bg-light text-muted border px-3 py-1 rounded-pill shadow-sm'>$loader</span>", 'class' => 'align-middle'],
        ['content' => "<div id='mod-action-{$mod['id']}' class='text-end pe-4 align-middle'>$loader</div>", 'class' => 'text-end pe-4 align-middle']
    ];
}

// Themes rows
foreach ($data['themes'] as $thm) {
    $rows[] = [
        [
            'content' => "
            <div class='d-flex align-items-center ps-4 py-3'>
                <div class='bg-warning bg-opacity-10 p-3 rounded-4 text-warning me-3 shadow-sm'>
                    <i class='bi bi-palette fs-4'></i>
                </div>
                <div>
                    <div class='fw-bold text-dark h6 mb-1'>{$thm['name']}</div>
                    <div class='extra-small text-muted opacity-75 fw-bold text-uppercase ls-1'>Theme: {$thm['id']}</div>
                </div>
            </div>",
            'class' => 'p-0'
        ],
        ['content' => "<span class='badge bg-light text-muted border px-3 py-1 rounded-pill'>v{$thm['v_current']}</span>", 'class' => 'align-middle'],
        ['content' => "<span id='thm-status-{$thm['id']}' class='badge bg-light text-muted border px-3 py-1 rounded-pill shadow-sm'>$loader</span>", 'class' => 'align-middle'],
        ['content' => "<div id='thm-action-{$thm['id']}' class='text-end pe-4 align-middle'>$loader</div>", 'class' => 'text-end pe-4 align-middle']
    ];
}

$schema = [
    'header' => [
        'title' => _('System Update Repository'),
        'subtitle' => _('Monitor and manage version history for core, modules, and themes.'),
        'icon' => 'bi bi-arrow-repeat',
        'button' => [
            'url' => '#',
            'label' => _('Manual Sync'),
            'icon' => 'bi bi-arrow-clockwise',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'onclick="checkUpdates(); return false;"'
        ],
    ],
    'content' => [
        [
            'type' => 'card',
            'no_padding' => true,
            'title' => _('Managed Ecosystem Components'),
            'subtitle' => _('Real-time comparison between local environment and cloud update server.'),
            'icon' => 'bi bi-hdd-network',
            'body_elements' => [
                [
                    'type' => 'table',
                    'headers' => [
                        ['content' => _('Component Identity'), 'class' => 'ps-4 py-3'],
                        _('Stable Version'),
                        _('Security Status'),
                        ['content' => _('Actions'), 'class' => 'text-end pe-4']
                    ],
                    'rows' => $rows,
                    'empty_message' => _('No system components detected for analysis.')
                ]
            ],
            'footer' => '
                <div id="update-footer" class="extra-small text-muted text-uppercase tracking-widest fw-bold d-flex align-items-center mx-3 my-1">
                    <div class="spinner-grow spinner-grow-sm text-primary me-2" role="status"></div>
                    ' . _("Synchronizing with marketplace repository... Please wait.") . '
                </div>'
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
$builder = new UiBuilder($schema);
$builder->render();
?>

<script>
    async function checkUpdates() {
        const footer = document.getElementById('update-footer');
        footer.innerHTML = '<div class="spinner-grow spinner-grow-sm text-primary me-2" role="status"></div> Synchronizing with marketplace repository...';

        try {
            const response = await fetch('index.php?page=updates&ajax=1');
            const data = await response.json();

            if (data.status === 'success') {
                // Update Core
                document.getElementById('core-v-latest').innerText = 'v' + data.core.v_latest;
                updateStatus('core-status', 'core-action', data.core.can_update, data.core.download_url);

                // Update Modules
                for (const id in data.mods) {
                    const m = data.mods[id];
                    updateStatus('mod-status-' + id, 'mod-action-' + id, m.can_update, m.download_url);
                }

                // Update Themes
                for (const id in data.themes) {
                    const t = data.themes[id];
                    updateStatus('thm-status-' + id, 'thm-action-' + id, t.can_update, t.download_url);
                }

                footer.innerHTML = '<i class="bi bi-shield-check-fill me-2 text-success fs-5"></i> System successfully synchronized with repository.';
            }
        } catch (error) {
            console.error('Update check failed:', error);
            footer.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2 text-danger fs-5"></i> Failed to communicate with update server.';
        }
    }

    function updateStatus(statusId, actionId, canUpdate, downloadUrl) {
        const statusEl = document.getElementById(statusId);
        const actionEl = document.getElementById(actionId);

        if (canUpdate) {
            statusEl.className = 'badge bg-danger text-white px-3 py-1 rounded-pill shadow-sm';
            statusEl.innerText = 'New Version Available!';
            actionEl.innerHTML = `<a href="${downloadUrl}" target="_blank" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm pulse-animation">Download Update</a>`;
        } else {
            statusEl.className = 'badge bg-success text-white px-3 py-1 rounded-pill shadow-sm';
            statusEl.innerText = 'Up-to-Date';
            actionEl.innerHTML = `<a href="#" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm disabled opacity-50">Up-to-Date</a>`;
        }
    }

    // Initial check on load
    document.addEventListener('DOMContentLoaded', checkUpdates);
</script>

<style>
    .extra-small {
        font-size: 0.75rem;
    }

    .tracking-widest {
        letter-spacing: 0.1em;
    }

    .ls-1 {
        letter-spacing: 0.05em;
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
</style>