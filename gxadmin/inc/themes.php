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
        
        // Modal
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
