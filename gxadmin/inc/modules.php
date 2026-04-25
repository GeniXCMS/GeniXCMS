<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 0.0.1
 * @version 2.4.0
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$rows = [];
if (count($data['mods']) > 0) {
    foreach ($data['mods'] as $mod) {
        $m = Mod::data($mod);
        $isActive = Mod::isActive($mod);
        $statusClass = $isActive ? 'success' : 'secondary';
        $btnClass = $isActive ? 'warning' : 'success';
        $actLabel = $isActive ? _("Deactivate") : _("Activate");
        $actIcon = $isActive ? 'bi-toggle-on' : 'bi-toggle-off';
        $actUri = $isActive ? 'deactivate' : 'activate';
        $icon = (isset($m['icon']) ? $m['icon'] : 'bi bi-puzzle');

        $rows[] = [
            [
                'content' => "
                <div class='ps-4 py-3 d-flex align-items-center'>
                    <input type='checkbox' name='modules[]' value='{$mod}' class='form-check-input me-3 mod-checkbox'>
                    <div class='bg-{$statusClass} bg-opacity-10 p-3 rounded-4 text-{$statusClass} me-3 shadow-sm'>
                        <i class='{$icon} fs-4'></i>
                    </div>
                    <div>
                        <div class='fw-bold text-dark h6 mb-1'>{$m['name']}</div>
                        <div class='d-flex gap-2 align-items-center'>
                            <span class='badge bg-light text-muted border extra-small px-3 py-1 rounded-pill fw-bold'>v{$m['version']}</span>
                            <span class='extra-small text-muted opacity-50 fw-bold text-uppercase ls-1'>{$m['license']}</span>
                        </div>
                    </div>
                </div>",
                'class' => 'p-0'
            ],
            "<div>
                <p class='text-muted small mb-1 lh-base' style='max-width: 400px;'>" . (strlen($m['desc']) > 140 ? substr($m['desc'], 0, 137) . '...' : $m['desc']) . "</p>
                <div class='extra-small text-muted d-flex align-items-center gap-1'>
                    <i class='bi bi-person-circle'></i> " . _("Authored by") . ": <a href='{$m['url']}' target='_blank' class='text-primary fw-bold text-decoration-none'>{$m['developer']}</a>
                </div>
             </div>",
            [
                'content' => "
                <div class='btn-group gap-2'>
                    <a href='index.php?page=modules&act={$actUri}&modules={$mod}&token=" . TOKEN . "' 
                       class='btn btn-{$btnClass} btn-sm rounded-pill px-4 shadow-sm d-inline-flex align-items-center fw-bold'>
                        <i class='bi {$actIcon} me-2'></i> {$actLabel}
                    </a>
                    " . (!$isActive ? "
                    <a href='index.php?page=modules&act=remove&modules={$mod}&token=" . TOKEN . "' 
                       class='btn btn-light btn-sm rounded-circle border p-2' 
                       onclick=\"return confirm('" . _("Permanent removal of this module?") . "');\" title='Remove Module'>
                        <i class='bi bi-trash text-danger'></i>
                    </a>" : "") . "
                </div>",
                'class' => 'text-end pe-4'
            ]
        ];
    }
}

// Marketplace HTML Component
$marketplaceHtml = "
<div id='marketplace-container'>
    <div class='row mb-4 mt-2 align-items-center'>
        <div class='col-md-6'>
            <h6 class='text-muted extra-small fw-bold text-uppercase tracking-widest mb-0'>" . _("Module Marketplace") . "</h6>
        </div>
        <div class='col-md-6 text-end'>
            <div class='input-group shadow-sm rounded-pill overflow-hidden border bg-white'>
                <span class='input-group-text bg-white border-0 ps-3'><i class='bi bi-search text-muted'></i></span>
                <input type='text' id='marketplaceSearch' class='form-control border-0 ps-2 bg-white' placeholder='Search plugins...'>
                <button class='btn btn-primary px-4 fw-bold rounded-pill m-1' id='btnMarketplaceSearch'>" . _("Find Plugins") . "</button>
            </div>
        </div>
    </div>
    <div id='marketplaceResults' class='row g-4'>
        <div class='col-12 text-center py-5'>
            <div class='spinner-border text-primary' role='status' style='width: 3rem; height: 3rem;'></div>
            <p class='mt-3 text-muted fw-medium'>" . _("Connecting to Marketplace Repo...") . "</p>
        </div>
    </div>
    <div id='marketplacePagination' class='mt-5'></div>
</div>";

// JS Component for Marketplace
$marketplaceJs = "
<script>
    const GX_TOKEN = '" . TOKEN . "';
    const GX_TYPE = 'module';
    const GX_AJAX_URL = '" . Url::ajax('marketplace') . "';
    const GX_DOMAIN = '" . $_SERVER['HTTP_HOST'] . "';
    let mpPage = 1;

    function loadMarketplace(q = '', page = 1) {
        $('#marketplaceResults').html('<div class=\"col-12 text-center py-5\"><div class=\"spinner-border text-primary\" role=\"status\" style=\"width: 3rem; height: 3rem;\"></div><p class=\"mt-3 text-muted fw-medium\">Searching items...</p></div>');
        
        const separator = (GX_AJAX_URL.indexOf('?') !== -1) ? '&' : '?';
        const url = GX_AJAX_URL + separator + 'action=search&q=' + encodeURIComponent(q) + '&type=' + GX_TYPE + '&page=' + page;
        
        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderMarketplaceItems(res.data.items || []);
                    renderMarketplacePagination(res.data.pagination);
                } else {
                    $('#marketplaceResults').html('<div class=\"col-12 text-center py-5 text-danger\"><i class=\"bi bi-exclamation-triangle fs-1 d-block mb-3\"></i>' + (res.message || 'Error connecting to marketplace') + '</div>');
                }
            })
            .catch(err => {
                $('#marketplaceResults').html('<div class=\"col-12 text-center py-5 text-danger\"><i class=\"bi bi-wifi-off fs-1 d-block mb-3\"></i> Failed to connect to marketplace repository.</div>');
            });
    }

    function renderMarketplaceItems(items) {
        if (!items || items.length === 0) {
            $('#marketplaceResults').html('<div class=\"col-12 text-center py-5 text-muted\"><i class=\"bi bi-search fs-1 d-block mb-3\"></i> No plugins matching your search were found.</div>');
            return;
        }

        let html = '';
        items.forEach(item => {
            let screenshots = JSON.parse(item.screenshots || item.mp_screenshots || '[]');
            let thumb = (screenshots.length > 0 && screenshots[0]) ? screenshots[0] : '" . Site::$url . "/assets/images/noimagetheme.png';
            let priceVal = parseFloat(item.price || item.mp_price || 0);
            let price = (priceVal > 0) ? '$'+priceVal : 'FREE';
            
            html += `
            <div class='col-xl-4 col-lg-6 col-md-12'>
                <div class='card border-0 shadow-sm rounded-5 h-100 p-4 transition-all hover-up-small border-hover-primary'>
                    <div class='d-flex align-items-center mb-3'>
                        <div class='bg-primary bg-opacity-10 p-3 rounded-4 text-primary me-3'>
                            <i class='bi bi-puzzle fs-4'></i>
                        </div>
                        <div class='flex-grow-1 overflow-hidden'>
                            <h6 class='fw-bold text-dark mb-0 text-truncate'>\${item.title}</h6>
                            <div class='extra-small text-muted'>By <b>\${item.author}</b></div>
                        </div>
                        <div class='text-end'>
                            <span class='badge bg-primary bg-opacity-10 text-primary fw-black fs-9'>\${price}</span>
                        </div>
                    </div>
                    <div class='mb-4 flex-grow-1'>
                        <p class='text-muted extra-small mb-0 lh-base opacity-75' style='display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;'>\${item.content.replace(/<[^>]*>?/gm, '')}</p>
                    </div>
                    <div class='d-flex gap-2 mt-auto pt-3 border-top border-light'>
                        <button class='btn btn-primary rounded-pill px-4 btn-sm fw-bold btn-install-marketplace shadow-sm w-100' 
                            data-id='\${item.id}'
                            data-price='\${priceVal}'
                            data-name='\${item.title}'>
                            <i class='bi bi-cloud-download-fill me-1'></i> " . _("Install plugin") . "
                        </button>
                    </div>
                </div>
            </div>`;
        });
        $('#marketplaceResults').html(html);
    }

    function renderMarketplacePagination(p) {
        if (!p || p.total_pages <= 1) {
            $('#marketplacePagination').empty();
            return;
        }

        let html = '<nav><ul class=\"pagination pagination-md justify-content-center gap-2\">';
        for (let i = 1; i <= p.total_pages; i++) {
            let active = (i === p.current_page) ? 'active shadow-lg' : 'bg-white border';
            html += `<li class=\"page-item\"><a class=\"page-link rounded-circle d-flex align-items-center justify-content-center \${active}\" href=\"#\" data-page=\"\${i}\" style=\"width:40px; height:40px;\">\${i}</a></li>`;
        }
        html += '</ul></nav>';
        $('#marketplacePagination').html(html);
    }

    $(document).on('click', '#btnMarketplaceSearch', function() {
        loadMarketplace($('#marketplaceSearch').val(), 1);
    });

    $(document).on('keypress', '#marketplaceSearch', function(e) {
        if (e.which === 13) $('#btnMarketplaceSearch').click();
    });

    $(document).on('click', '#marketplacePagination .page-link', function(e) {
        e.preventDefault();
        loadMarketplace($('#marketplaceSearch').val(), $(this).data('page'));
    });

    $(document).on('click', '.btn-install-marketplace', function() {
        const id = $(this).data('id');
        const price = $(this).data('price');
        const name = $(this).data('name');
        const btn = $(this);
        let license = '';

        if (price > 0) {
            license = prompt('" . _("This is a Paid Plugin. Please enter your License Key for") . " ' + name + ':', '');
            if (license === null) return; // Cancel
            if (license.trim() === '') {
                alert('" . _("License Key is required for paid items.") . "');
                return;
            }
        }
        
        if (confirm('" . _("Install this Plugin from Marketplace? This will download and extract it to your repository.") . "')) {
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span> Installing...');
            
            const separator = (GX_AJAX_URL.indexOf('?') !== -1) ? '&' : '?';
            fetch(GX_AJAX_URL + separator + 'action=install&id=' + id + '&type=' + GX_TYPE + '&license_key=' + encodeURIComponent(license) + '&domain=' + encodeURIComponent(GX_DOMAIN))
                .then(res => res.json())
                .then(res => {
                    if (res.status === true) {
                        alert('" . _("Success! Plugin has been installed.") . "');
                        location.reload();
                    } else {
                        alert(res.message || '" . _("Failed to install plugin.") . "');
                        btn.prop('disabled', false).html(originalHtml);
                    }
                })
                .catch(err => {
                    alert('" . _("Error! Could not connect to API.") . "');
                    btn.prop('disabled', false).html(originalHtml);
                });
        }
    });

    $(document).on('shown.bs.tab', 'button[data-bs-target=\"#content-market\"]', function() {
        if ($('#marketplaceResults .card').length === 0) {
            loadMarketplace();
        }
    });
</script>";

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Plugin Ecosystem'),
        'subtitle' => _('Extend core features and add new capabilities with modular extensions.'),
        'icon' => 'bi bi-cpu',
        'button' => [
            'url' => '#',
            'label' => _('New Module'),
            'icon' => 'bi bi-plus-circle-fill',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#installModal"'
        ],
    ],
    'tab_mode' => 'js',
    'tabs' => [
        'local' => [
            'label' => _('Installed Extensions'),
            'icon' => 'bi bi-cpu',
            'content' => [
                [
                    'type' => 'card',
                    'no_padding' => true,
                    'title' => _('Library Management'),
                    'subtitle' => _('Manage system extensions and operational status.'),
                    'icon' => 'bi bi-cpu',
                    'body_elements' => [
                        [
                            'type' => 'table',
                            'headers' => [
                                ['content' => '<input type="checkbox" id="checkAllMods" class="form-check-input ms-4"> ' . _('Extension Identity'), 'class' => 'ps-0 py-3'],
                                _('Capability & Origin'),
                                ['content' => _('Operational Control'), 'class' => 'text-end pe-4']
                            ],
                            'rows' => $rows,
                            'empty_message' => _('No modular extensions found in your library.')
                        ]
                    ],
                    'footer_elements' => [
                        [
                            'type' => 'bulk_actions',
                            'form' => 'bulk-modules-form',
                            'options' => [
                                'activate' => _('Activate'),
                                'deactivate' => _('Deactivate'),
                                'remove' => _('Remove')
                            ]
                        ]
                    ],
                    'footer' => '
                        <div class="extra-small text-muted text-uppercase tracking-widest fw-bold d-flex align-items-center mx-3 my-1">
                            <i class="bi bi-info-circle-fill me-2 text-primary fs-5"></i>
                            ' . _("Activated modules may add new operational nodes to your administrative sidebar.") . '
                        </div>'
                ]
            ]
        ],
        'market' => [
            'label' => _('Marketplace'),
            'icon' => 'bi bi-shop',
            'content' => [
                ['type' => 'raw', 'html' => $marketplaceHtml]
            ]
        ]
    ],
    'content' => [
        // Modal
        [
            'type' => 'modal',
            'id' => 'installModal',
            'header' => _("Deploy New Module Package"),
            'size' => 'md',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php?page=modules',
                    'fields' => [
                        [
                            'type' => 'raw',
                            'html' => '
                            <div class="upload-zone border-2 border-dashed rounded-5 p-5 mb-4 bg-light text-center">
                                <i class="bi bi-plugin text-success fs-1 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">' . _("Upload Module Archive") . '</h6>
                                <p class="extra-small text-muted mb-4">' . _("Select the .zip package to expand and install") . '</p>
                                <input type="file" name="module" id="modFile" class="form-control border-0 bg-white rounded-pill px-4 py-2 border shadow-sm">
                            </div>
                            <div class="alert bg-info bg-opacity-10 border-0 rounded-4 p-3 extra-small mb-4 d-flex">
                                <i class="bi bi-shield-check text-info fs-5 me-3"></i>
                                <div class="text-dark opacity-75">
                                    <strong>Installation Note:</strong> Modules are executed with core system privileges. Ensure your package is from a verified developer or the official marketplace.
                                </div>
                            </div>
                            <input type="hidden" name="token" value="' . TOKEN . '">'
                        ],
                        ['type' => 'button', 'name' => 'upload', 'label' => _("Initialize Installation"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100 shadow-sm']
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

echo '<form action="index.php?page=modules" method="POST" id="bulk-modules-form">';
echo '<input type="hidden" name="token" value="' . TOKEN . '">';

$builder = new UiBuilder($schema);
$builder->render();

echo '</form>';
echo $marketplaceJs;
?>

<script>
    $(document).ready(function() {
        $('#checkAllMods').on('click', function() {
            $('.mod-checkbox').prop('checked', this.checked);
        });

        $('.mod-checkbox').on('click', function() {
            if ($('.mod-checkbox:checked').length === $('.mod-checkbox').length) {
                $('#checkAllMods').prop('checked', true);
            } else {
                $('#checkAllMods').prop('checked', false);
            }
        });
    });
</script>

<style>
    .upload-zone {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border-color: #e2e8f0 !important;
    }

    .upload-zone:hover {
        background-color: #fff !important;
        border-color: var(--gx-primary) !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    .tracking-widest {
        letter-spacing: 0.1em;
    }

    .hover-up-small:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }

    .border-hover-primary:hover {
        border-color: var(--bs-primary) !important;
    }

    /* Tab Modern Styling */
    .nav-pills .nav-link {
        color: var(--gx-primary, #0d6efd);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    .nav-pills .nav-link.active {
        background-color: var(--gx-primary, #0d6efd) !important;
        color: #fff !important;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
    }

    .nav-pills .nav-link.active i,
    .nav-pills .nav-link.active svg {
        color: #fff !important;
    }
</style>