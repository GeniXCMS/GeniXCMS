<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */

// ── PREPARE DATA ──────────────────────────────────────────────────
$active = Options::v('themes');
$adata = Theme::data($active);
$screenshot = file_exists(GX_THEME.'/'.$active.'/screenshot.png') 
    ? Site::$url.'/inc/themes/'.$active.'/screenshot.png' 
    : Site::$url.'/assets/images/noimagetheme.png';

$activeThemeHtml = "
<div class='card border-0 shadow-sm rounded-5 overflow-hidden active-theme-showcase'>
    <div class='row g-0'>
        <div class='col-lg-5 col-xl-4 position-relative'>
            <div class='screenshot-wrapper h-100'>
                <img src='{$screenshot}' class='img-fluid h-100 w-100 object-fit-cover shadow-lg' alt='Active Theme'>
                <div class='overlay-glow'></div>
                <div class='status-indicator'>
                    <span class='badge bg-success rounded-pill px-3 py-2 shadow-lg border border-3 border-white'>
                        <span class='pulse-dot me-2'></span> "._("LIVE ON WEB")."
                    </span>
                </div>
            </div>
        </div>
        <div class='col-lg-7 col-xl-8'>
            <div class='card-body p-4 p-xl-5 d-flex flex-column h-100'>
                <div class='d-flex justify-content-between align-items-start mb-4'>
                    <div>
                        <div class='d-flex align-items-center gap-2 mb-2'>
                            <h1 class='fw-bold text-dark mb-0'>{$adata['name']}</h1>
                            <span class='badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3'>v{$adata['version']}</span>
                        </div>
                        <div class='text-muted d-flex align-items-center gap-2'>
                            <i class='bi bi-person-circle'></i>
                            <span>"._("Created by")." <a href='{$adata['url']}' target='_blank' class='text-dark fw-bold text-decoration-none hover-primary'>{$adata['developer']}</a></span>
                        </div>
                    </div>
                </div>
                
                <div class='theme-description-box p-4 rounded-4 bg-light bg-opacity-50 mb-4 border border-white flex-grow-1'>
                    <p class='text-muted mb-0 lh-lg'>{$adata['desc']}</p>
                </div>

                <div class='d-flex flex-wrap gap-3'>
                    <a href='index.php?page=themes&view=options' class='btn btn-primary rounded-pill px-5 py-2 shadow-lg hover-up'>
                        <i class='bi bi-magic me-2'></i> "._("Customize Look")."
                    </a>
                    <a href='".Site::$url."' target='_blank' class='btn btn-white border rounded-pill px-4 py-2 shadow-sm hover-up'>
                        <i class='bi bi-eye me-2'></i> "._("Live Preview")."
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>";

$themeCards = [];
$availableThemes = $data['themes'];
foreach ($availableThemes as $thm) {
    if ($thm == $active) continue;
    $t = Theme::data($thm);
    $thumb = file_exists(GX_THEME.'/'.$thm.'/screenshot.png') 
        ? Site::$url.'/inc/themes/'.$thm.'/screenshot.png' 
        : Site::$url.'/assets/images/noimagetheme.png';
    
    $themeCards[] = "
    <div class='col-xl-3 col-lg-4 col-md-6 theme-item' data-name='".strtolower($t['name'])."'>
        <div class='card theme-card-modern border-0 shadow-sm h-100 position-relative group'>
            <div class='theme-thumb-container position-relative overflow-hidden'>
                <img src='{$thumb}' class='card-img-top object-fit-cover' style='height: 200px;' alt='{$t['name']}'>
                <div class='theme-actions-overlay position-absolute bottom-0 start-0 end-0 p-3 translate-y-full transition-transform'>
                    <a href='index.php?page=themes&act=activate&themes={$thm}&token=".TOKEN."' class='btn btn-primary rounded-pill w-100 shadow-lg fw-bold'>
                        <i class='bi bi-lightning-fill me-1'></i> "._("Apply Design")."
                    </a>
                </div>
                <div class='position-absolute top-0 end-0 m-2'>
                    <a href='index.php?page=themes&act=remove&themes={$thm}&token=".TOKEN."' 
                       class='btn btn-danger btn-sm rounded-circle opacity-0 group-hover-opacity-100 shadow' 
                       onclick=\"return confirm('"._("Permanent removal of this theme?")."');\">
                        <i class='bi bi-trash'></i>
                    </a>
                </div>
            </div>
            <div class='card-footer bg-white border-0 p-3'>
                <div class='d-flex justify-content-between align-items-start mb-1'>
                    <h6 class='fw-bold text-dark mb-0 text-truncate'>{$t['name']}</h6>
                    <span class='badge bg-light text-muted border py-0 extra-small'>v{$t['version']}</span>
                </div>
                <div class='extra-small text-muted text-truncate'>{$t['developer']}</div>
            </div>
        </div>
    </div>";
}

// Marketplace HTML Component
$marketplaceHtml = "
<div id='marketplace-container'>
    <div class='row mb-4 mt-2 align-items-center'>
        <div class='col-md-6'>
            <h6 class='text-muted extra-small fw-bold text-uppercase tracking-widest mb-0'>"._("Marketplace Explorer")."</h6>
        </div>
        <div class='col-md-6 text-end'>
            <div class='input-group input-group shadow-sm rounded-pill overflow-hidden border bg-white'>
                <span class='input-group-text bg-white border-0 ps-3'><i class='bi bi-search text-muted'></i></span>
                <input type='text' id='marketplaceSearch' class='form-control border-0 ps-2 bg-white' placeholder='Search marketplace...'>
                <button class='btn btn-primary px-4 fw-bold' id='btnMarketplaceSearch'>"._("Search Repository")."</button>
            </div>
        </div>
    </div>
    <div id='marketplaceResults' class='row g-4'>
        <div class='col-12 text-center py-5'>
            <div class='spinner-border text-primary' role='status' style='width: 3rem; height: 3rem;'></div>
            <p class='mt-3 text-muted fw-medium'>"._("Connecting to Marketplace Repo...")."</p>
        </div>
    </div>
    <div id='marketplacePagination' class='mt-5'></div>
</div>";

// JS Component for Marketplace
$marketplaceJs = "
<script>
    const GX_TOKEN = '".TOKEN."';
    const GX_TYPE = 'theme';
    const GX_AJAX_URL = '".Url::ajax('marketplace')."';
    const GX_DOMAIN = '".$_SERVER['HTTP_HOST']."';
    let mpPage = 1;

    function loadMarketplace(q = '', page = 1) {
        $('#marketplaceResults').html('<div class=\"col-12 text-center py-5\"><div class=\"spinner-border text-primary\" role=\"status\" style=\"width: 3rem; height: 3rem;\"></div><p class=\"mt-3 text-muted fw-medium\">Searching items...</p></div>');
        
        const separator = (GX_AJAX_URL.indexOf('?') !== -1) ? '&' : '?';
        const url = GX_AJAX_URL + separator + 'action=search&q=' + encodeURIComponent(q) + '&type=' + GX_TYPE + '&page=' + page;
        
        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    renderMarketplaceItems(res.data.items);
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
            $('#marketplaceResults').html('<div class=\"col-12 text-center py-5 text-muted\"><i class=\"bi bi-search fs-1 d-block mb-3\"></i> No items matching your search were found.</div>');
            return;
        }

        let html = '';
        items.forEach(item => {
            let screenshots = JSON.parse(item.screenshots || item.mp_screenshots || '[]');
            let thumb = (screenshots.length > 0 && screenshots[0]) ? screenshots[0] : '".Site::$url."/assets/images/noimagetheme.png';
            let priceVal = parseFloat(item.price || item.mp_price || 0);
            let price = (priceVal > 0) ? '$'+priceVal : 'FREE';
            
            html += `
            <div class='col-xl-3 col-lg-4 col-md-6 theme-item-market'>
                <div class='card theme-card-modern border-0 shadow-sm h-100 position-relative group'>
                    <div class='theme-thumb-container position-relative overflow-hidden'>
                        <img src='\${thumb}' class='card-img-top object-fit-cover' style='height: 220px;' alt='\${item.title}'>
                        <div class='theme-actions-overlay position-absolute bottom-0 start-0 end-0 p-4 translate-y-full transition-transform'>
                            <button class='btn btn-primary rounded-pill w-100 shadow-lg fw-bold btn-install-marketplace p-2' 
                                data-id='\${item.id}' 
                                data-price='\${priceVal}'
                                data-name='\${item.title}'>
                                <i class='bi bi-cloud-download-fill me-1'></i> "._("Install Now")."
                            </button>
                        </div>
                    </div>
                    <div class='card-footer bg-white border-0 p-4 pt-3'>
                        <div class='d-flex justify-content-between align-items-start mb-2'>
                            <h6 class='fw-bold text-dark mb-0 text-truncate' style='max-width: 70%;'>\${item.title}</h6>
                            <span class='badge bg-light text-muted border py-1 px-2 extra-small'>v\${item.mp_version || '1.0.0'}</span>
                        </div>
                        <div class='d-flex justify-content-between align-items-center'>
                             <span class='extra-small text-muted'>By <b>\${item.author}</b></span>
                             <span class='badge bg-primary bg-opacity-10 text-primary fw-black fs-9'>\${price}</span>
                        </div>
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
            license = prompt('"._("This is a Paid Item. Please enter your License Key for")." ' + name + ':', '');
            if (license === null) return; // Cancel
            if (license.trim() === '') {
                alert('"._("License Key is required for paid items.")."');
                return;
            }
        }
        
        if (confirm('"._("Install this Theme from Marketplace? This will download and extract it to your repository.")."')) {
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class=\"spinner-border spinner-border-sm\" role=\"status\"></span> Installing...');
            
            const separator = (GX_AJAX_URL.indexOf('?') !== -1) ? '&' : '?';
            fetch(GX_AJAX_URL + separator + 'action=install&id=' + id + '&type=' + GX_TYPE + '&license_key=' + encodeURIComponent(license) + '&domain=' + encodeURIComponent(GX_DOMAIN))
                .then(res => res.json())
                .then(res => {
                    if (res.status === true) {
                        alert('"._("Success! Theme has been installed.")."');
                        location.reload();
                    } else {
                        alert(res.message || '"._("Failed to install theme.")."');
                        btn.prop('disabled', false).html(originalHtml);
                    }
                })
                .catch(err => {
                    alert('"._("Error! Could not connect to API.")."');
                    btn.prop('disabled', false).html(originalHtml);
                });
        }
    });

    $(document).on('shown.bs.tab', 'button[data-bs-target=\"#content-market\"]', function() {
        if ($('#marketplaceResults .theme-card-modern').length === 0) {
            loadMarketplace();
        }
    });
</script>";

// ── DEFINE UI SCHEMA ──────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Visual Experience'),
        'subtitle' => _('Switch, customize, and manage your website\'s interface themes.'),
        'icon' => 'bi bi-palette2',
        'button' => [
            'url' => '#',
            'label' => _('Install Theme'),
            'icon' => 'bi bi-cloud-upload',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'data-bs-toggle="modal" data-bs-target="#installModal"'
        ],
    ],
    'tab_mode' => 'js',
    'tabs' => [
        'local' => [
            'label' => _('My Themes'),
            'icon' => 'bi bi-laptop',
            'content' => [
                ['type' => 'raw', 'html' => "<div class='row mb-2'><div class='col-12'><h6 class='text-muted extra-small fw-bold text-uppercase tracking-widest mb-3'>"._("Active Masterpiece")."</h6></div></div>"],
                ['type' => 'raw', 'html' => $activeThemeHtml],
                ['type' => 'raw', 'html' => "
                    <div class='row mt-5 mb-4 align-items-center'>
                        <div class='col-6'><h6 class='text-muted extra-small fw-bold text-uppercase tracking-widest mb-0'>"._("Your Collection")."</h6></div>
                        <div class='col-6 text-end'>
                            <div class='input-group input-group-sm w-50 ms-auto shadow-sm rounded-pill overflow-hidden border'>
                                <span class='input-group-text bg-white border-0 ps-3'><i class='bi bi-search text-muted'></i></span>
                                <input type='text' id='themeSearch' class='form-control border-0 ps-2 bg-white' placeholder='Search library...'>
                            </div>
                        </div>
                    </div>"],
                ['type' => 'raw', 'html' => "<div class='row g-4' id='themesGrid'>".(empty($themeCards) ? '<div class="col-12 text-center py-5 text-muted opacity-50">'._("No alternative themes found in your repository.").'</div>' : implode('', $themeCards))."</div>"],
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
        // Modal (remains global to the page)
        [
            'type' => 'modal',
            'id' => 'installModal',
            'header' => _("Package Deployment"),
            'size' => 'md',
            'body_elements' => [
                [
                    'type' => 'form',
                    'action' => 'index.php?page=themes',
                    'fields' => [
                        ['type' => 'raw', 'html' => '
                            <label class="theme-drop-zone border-2 border-dashed rounded-5 p-5 mb-4 bg-light d-block cursor-pointer position-relative">
                                <input type="file" name="theme" class="position-absolute opacity-0 start-0 top-0 w-100 h-100 cursor-pointer" id="fileInput">
                                <div id="dropZoneContent">
                                    <div class="icon-circle bg-white shadow-sm mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-file-earmark-zip text-primary fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">'._("Drop Theme Package").'</h6>
                                    <p class="extra-small text-muted mb-0">'._("Drag & Drop or Click to Browse .zip file").'</p>
                                </div>
                                <div id="fileSelected" class="d-none">
                                    <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-1" id="fileName"></h6>
                                    <button type="button" class="btn btn-sm btn-link text-muted extra-small py-0" onclick="resetFileSelection(event)">'._("Change file").'</button>
                                </div>
                            </label>
                            <input type="hidden" name="token" value="'.TOKEN.'">'],
                        ['type' => 'button', 'name' => 'upload', 'label' => _("Deploy & Install"), 'class' => 'btn btn-primary rounded-pill px-5 fw-bold w-100 shadow-sm']
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

$builder = new UiBuilder($schema);
$builder->render();
echo $marketplaceJs;
?>

<style>
    .active-theme-showcase { background: #fff; border: 1px solid #f1f5f9; }
    .status-indicator { position: absolute; top: 1rem; left: 1rem; }
    .pulse-dot { display: inline-block; width: 8px; height: 8px; background-color: #fff; border-radius: 50%; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(255,255,255,0.7); } 70% { box-shadow: 0 0 0 6px rgba(255,255,255,0); } 100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); } }
    .theme-card-modern { border-radius: 20px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid #f1f5f9; }
    .theme-card-modern:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .theme-actions-overlay { background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); opacity: 0; transition: all 0.3s; transform: translateY(100%); }
    .theme-card-modern:hover .theme-actions-overlay { opacity: 1; transform: translateY(0); }
    .group:hover .group-hover-opacity-100 { opacity: 1 !important; }
    .theme-drop-zone { transition: all 0.3s; }
    .theme-drop-zone:hover { border-color: var(--gx-primary); background: #fff; }
    .tracking-widest { letter-spacing: 0.1em; }
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

<script>
    document.getElementById('fileInput')?.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            document.getElementById('dropZoneContent').classList.add('d-none');
            document.getElementById('fileSelected').classList.remove('d-none');
            document.getElementById('fileName').textContent = this.files[0].name;
        }
    });
    function resetFileSelection(e) { e.preventDefault(); document.getElementById('fileInput').value = ''; document.getElementById('dropZoneContent').classList.remove('d-none'); document.getElementById('fileSelected').classList.add('d-none'); }
    document.getElementById('themeSearch')?.addEventListener('keyup', function() {
        let f = this.value.toLowerCase();
        document.querySelectorAll('.theme-item').forEach(i => { i.style.display = i.getAttribute('data-name').includes(f) ? '' : 'none'; });
    });
</script>
