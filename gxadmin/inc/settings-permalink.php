<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 1.0.0
 * @version 2.2.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Clean URLs'),
        'subtitle' => _('Configure permalink structures and internal routing protocols for optimal SEO performance.'),
        'icon' => 'bi bi-signpost-2',
        'button' => [
            'type' => 'button',
            'name' => 'change',
            'label' => _('Apply Route Configuration'),
            'icon' => 'bi bi-check2-circle',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'value="Change"'
        ],
    ],
    'card_wrapper' => false,
    'content' => [
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 12,
                    'content' => [
                        [
                            'type' => 'card',
                            'body_elements' => [
                                [
                                    'type' => 'row',
                                    'items' => [
                                        [
                                            'width' => 6,
                                            'content' => [
                                                [
                                                    'type' => 'raw',
                                                    'html' => '
                                <div class="form-check form-switch bg-light rounded-4 p-4 ps-5 border-start border-4 border-danger shadow-none h-100 d-flex align-items-center mb-4">
                                    <input class="form-check-input" type="checkbox" name="permalink_use_index_php" id="useIndex" ' . ($data['permalink_use_index_php'] === 'on' ? 'checked' : '') . '>
                                    <label class="form-check-label ps-3" for="useIndex">
                                        <div class="fw-black text-dark text-uppercase tracking-wider extra-small mb-1">' . _("Legacy Compatibility Hub") . '</div>
                                        <div class="small text-muted fw-bold">' . _("Force index.php in URLs (for servers without native rewrites).") . '</div>
                                    </label>
                                </div>'
                                                ]
                                            ]
                                        ],
                                        [
                                            'width' => 6,
                                            'content' => [
                                                [
                                                    'type' => 'alert',
                                                    'style' => 'info',
                                                    'content' => '
                                <h6 class="fw-black text-info text-uppercase extra-small mb-2"><i class="bi bi-shield-check me-1"></i> SEO Recommendation</h6>
                                <p class="extra-small mb-0 lh-base fw-bold">' . _("Standard Clean URLs (Off) are highly recommended. Ensure your .htaccess or Nginx configuration is synchronized with this protocol to avoid 404 errors.") . '</p>'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            'type' => 'raw',
            'html' => '
            <div class="mt-4 p-4 bg-light rounded-5 border-2 border-dashed text-center">
                <i class="bi bi-hdd-network fs-1 text-muted opacity-25"></i>
                <div class="text-muted small mt-3 fw-bold">' . _("Changing routing protocols can affect search engine indexing. Proceed with caution during high-traffic windows.") . "</div>
            </div>"
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

echo '<form action="index.php?page=settings-permalink" method="post">';
$builder = new UiBuilder($schema);
$builder->render();
echo '<input type="hidden" name="token" value="' . TOKEN . '">';
echo '</form>';
?>