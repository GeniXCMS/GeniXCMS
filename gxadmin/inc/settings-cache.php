<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.3.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Persistence Layer'),
        'subtitle' => _('Manage temporary data storage to significantly boost platform performance and responsiveness.'),
        'icon' => 'bi bi-lightning-charge-fill',
        'button' => [
            'type' => 'button',
            'name' => 'change',
            'label' => _('Apply Persistence Protocols'),
            'icon' => 'bi bi-cpu-fill',
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
                                            'width' => 4,
                                            'content' => [
                                                [
                                                    'type' => 'raw',
                                                    'html' => '
                                <div class="form-check form-switch bg-light rounded-4 p-2 ps-5 border-start border-4 border-success shadow-none h-100 d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="cache_enabled" id="enableCache" ' . ($data['cache_enabled'] === 'on' ? 'checked' : '') . '>
                                    <label class="form-check-label ps-2" for="enableCache">
                                        <div class="fw-black text-dark text-uppercase tracking-wider fs-9 mb-0">' . _("Engine Status") . '</div>
                                        <div class="extra-small text-muted fw-bold">' . _("Active Object Caching") . '</div>
                                    </label>
                                </div>'
                                                ]
                                            ]
                                        ],
                                        [
                                            'width' => 4,
                                            'content' => [
                                                [
                                                    'type' => 'raw',
                                                    'html' => '
                                <div class="p-2 bg-light rounded-4 border h-100 d-flex align-items-center gap-3 px-3">
                                    <div class="flex-fill">
                                        <label class="form-label fw-black text-muted text-uppercase tracking-wider mb-0" style="font-size:0.55rem;">' . _("Engine Architecture") . '</label>
                                        <select name="cache_type" id="cacheType" class="form-select border-0 bg-transparent py-0 fs-8 fw-bold px-0 shadow-none" style="height:28px;">
                                            <option value="file" ' . ($data['cache_type'] == 'file' ? 'selected' : '') . '>File Cache (Default)</option>
                                            <option value="redis" ' . ($data['cache_type'] == 'redis' ? 'selected' : '') . '>Redis Memory Hub</option>
                                        </select>
                                    </div>
                                    <i class="bi bi-hdd-stack text-muted opacity-50"></i>
                                </div>'
                                                ]
                                            ]
                                        ],
                                        [
                                            'width' => 4,
                                            'content' => [
                                                [
                                                    'type' => 'raw',
                                                    'html' => '
                                <div class="p-2 bg-light rounded-4 border-start border-4 border-info h-100 d-flex align-items-center px-3">
                                    <div class="flex-fill">
                                        <label class="form-label fw-black text-muted text-uppercase tracking-wider mb-0" style="font-size:0.55rem;">' . _("Cache Lifespan") . '</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="cache_timeout" class="form-control border-0 bg-transparent py-0 fs-8 fw-bold px-0 shadow-none" value="' . $data['cache_timeout'] . '" style="height:28px;">
                                            <span class="input-group-text border-0 bg-transparent fs-9 fw-black opacity-50 px-1">S</span>
                                        </div>
                                    </div>
                                    <i class="bi bi-clock-history text-muted opacity-50"></i>
                                </div>'
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
            'type' => 'row',
            'items' => [
                [
                    'width' => 12,
                    'content' => [
                        [
                            'type' => 'card',
                            'body_elements' => [
                                [
                                    'type' => 'raw',
                                    'html' => '
                        <div id="file-config" style="display: ' . ($data["cache_type"] == "file" ? "block" : "none") . ';">
                            <h6 class="fw-black text-dark text-uppercase fs-9 mb-3 tracking-widest"><i class="bi bi-folder-symlink me-2"></i>' . _("System Path Protocol") . '</h6>
                            <div class="bg-light rounded-4 p-4 border">
                                <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider mb-2" style="font-size:0.6rem;">' . _("Registry Directory") . '</label>
                                <input type="text" class="form-control border bg-white rounded-3 shadow-none py-1 fs-8 fw-bold" name="cache_path" value="' . $data['cache_path'] . '">
                                <div class="extra-small text-danger mt-2 fw-bold"><i class="bi bi-exclamation-square-fill me-1"></i>' . _("UNIX: Ensure full write permissions (777) for the directory above.") . '</div>
                            </div>
                        </div>
                        
                        <div id="redis-config" style="display: ' . ($data["cache_type"] == "redis" ? "block" : "none") . ';">
                            <h6 class="fw-black text-primary text-uppercase fs-9 mb-3 tracking-widest"><i class="bi bi-cpu-fill me-2"></i>' . _("Redis Memory Hub Architecture") . '</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider mb-1" style="font-size:0.55rem;">' . _("Protocol Host") . '</label>
                                    <input type="text" class="form-control border bg-light rounded-3 py-1 fs-8 fw-bold px-3" name="redis_host" value="' . $data['redis_host'] . '" placeholder="127.0.0.1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider mb-1" style="font-size:0.55rem;">' . _("Port") . '</label>
                                    <input type="number" class="form-control border bg-light rounded-3 py-1 fs-8 fw-bold px-3" name="redis_port" value="' . $data['redis_port'] . '" placeholder="6379">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider mb-1" style="font-size:0.55rem;">' . _("Auth Cipher") . '</label>
                                    <input type="password" class="form-control border bg-light rounded-3 py-1 fs-8 fw-bold px-3" name="redis_pass" value="' . $data['redis_pass'] . '">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider mb-1" style="font-size:0.55rem;">' . _("DB Registry Index") . '</label>
                                    <input type="number" class="form-control border bg-light rounded-3 py-1 fs-8 fw-bold px-3" name="redis_db" value="' . $data['redis_db'] . '" placeholder="0">
                                </div>
                            </div>
                        </div>'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        [
            'type' => 'row',
            'items' => [
                [
                    'width' => 12,
                    'content' => [
                        [
                            'type' => 'alert',
                            'style' => 'info',
                            'content' => '
                    <div class="d-flex align-items-center gap-4">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3"><i class="bi bi-info-circle text-info fs-4"></i></div>
                        <div>
                            <h6 class="fw-black text-info text-uppercase fs-9 mb-1 tracking-widest">' . _("Strategy Advisor") . '</h6>
                            <p class="extra-small text-muted mb-0 fw-bold">' . _("Redis Memory Hub is recommended for high-performance scale. File Caching is optimized for lower resource environments without secondary memory engines.") . "</p>
                        </div>
                    </div>"
                        ]
                    ]
                ]
            ]
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

echo '<form action="index.php?page=settings-cache" method="post">';
$builder = new UiBuilder($schema);
$builder->render();
echo '<input type="hidden" name="token" value="' . TOKEN . '">';
echo '</form>';
?>

<script>
    $(document).on('change', '#cacheType', function () {
        const type = $(this).val();
        $('#file-config, #redis-config').hide();
        if (type === 'file') $('#file-config').fadeIn(200);
        else if (type === 'redis') $('#redis-config').fadeIn(200);
    });
</script>