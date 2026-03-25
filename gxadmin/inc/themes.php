<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>

<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data);?>
</div>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-start">
            <h3 class="fw-bold text-dark mb-0"><?=_("Visual Experience");?></h3>
            <p class="text-muted small mb-0"><?=_("Switch, customize, and manage your website's interface themes.");?></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
            <a href="https://genixcms.web.id/themes" target="_blank" class="btn btn-light rounded-pill px-4 border shadow-sm">
                <i class="bi bi-shop me-1"></i> <?=_("Browse Marketplace");?>
            </a>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#myModal">
                <i class="bi bi-cloud-upload me-1"></i> <?=_("Install Theme");?>
            </button>
        </div>
    </div>

    <!-- Active Theme Section -->
    <div class="row mb-5">
        <div class="col-12 mb-3 ps-1 d-flex align-items-center">
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-widest mb-0"><?=_("Active Masterpiece");?></h6>
            <div class="ms-3 flex-grow-1 border-bottom opacity-10"></div>
        </div>
        <?php
        $active = Options::v('themes');
        $adata = Theme::data($active);
        $screenshot = file_exists(GX_THEME.'/'.$active.'/screenshot.png') 
            ? Site::$url.'/inc/themes/'.$active.'/screenshot.png' 
            : Site::$url.'/assets/images/noimagetheme.png';
        ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-5 overflow-hidden active-theme-showcase">
                <div class="row g-0">
                    <div class="col-lg-5 col-xl-4 position-relative">
                        <div class="screenshot-wrapper h-100">
                            <img src="<?=$screenshot;?>" class="img-fluid h-100 w-100 object-fit-cover shadow-lg" alt="Active Theme">
                            <div class="overlay-glow"></div>
                            <div class="status-indicator">
                                <span class="badge bg-success rounded-pill px-3 py-2 shadow-lg border border-3 border-white">
                                    <span class="pulse-dot me-2"></span> <?=_("LIVE ON WEB");?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-xl-8">
                        <div class="card-body p-4 p-xl-5 d-flex flex-column h-100">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h1 class="fw-bold text-dark mb-0"><?=$adata['name'];?></h1>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">v<?=$adata['version'];?></span>
                                    </div>
                                    <div class="text-muted d-flex align-items-center gap-2">
                                        <i class="bi bi-person-circle"></i>
                                        <span><?=_("Created by");?> <a href="<?=$adata['url'];?>" target="_blank" class="text-dark fw-bold text-decoration-none hover-primary"><?=$adata['developer'];?></a></span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light rounded-pill border shadow-sm px-3" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                        <li><a class="dropdown-item rounded-3 py-2" href="index.php?page=themes&act=options&themes=<?=$active;?>"><i class="bi bi-sliders2 me-2 text-primary"></i><?=_("Advanced Config");?></a></li>
                                        <li><a class="dropdown-item rounded-3 py-2" href="index.php?page=themes&act=editor&themes=<?=$active;?>"><i class="bi bi-braces-asterisk me-2 text-warning"></i><?=_("Template Editor");?></a></li>
                                        <li><hr class="dropdown-divider mx-2"></li>
                                        <li><a class="dropdown-item rounded-3 py-2 text-danger" href="index.php?page=themes&act=remove&themes=<?=$active;?>&token=<?=TOKEN;?>" onclick="return confirm('WARNING: You are about to delete the active theme.');"><i class="bi bi-trash3 me-2"></i><?=_("Delete Theme");?></a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="theme-description-box p-4 rounded-4 bg-light bg-opacity-50 mb-4 border border-white flex-grow-1">
                                <p class="text-muted mb-0 lh-lg">
                                    <?=$adata['desc'];?>
                                </p>
                            </div>

                            <div class="d-flex flex-wrap gap-3">
                                <a href="index.php?page=themes&act=options" class="btn btn-primary rounded-pill px-5 py-2 shadow-lg hover-up">
                                    <i class="bi bi-magic me-2"></i> <?=_("Customize Look");?>
                                </a>
                                <a href="<?=Site::$url;?>" target="_blank" class="btn btn-white border rounded-pill px-4 py-2 shadow-sm hover-up">
                                    <i class="bi bi-eye me-2"></i> <?=_("Live Preview");?>
                                </a>
                                <button class="btn btn-light border rounded-pill px-4 py-2 shadow-sm ms-auto" title="Check for Updates">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Library Section -->
    <div class="row">
        <div class="col-12 mb-4 ps-1 d-flex align-items-center">
            <h6 class="text-muted extra-small fw-bold text-uppercase tracking-widest mb-0"><?=_("Your Collection");?></h6>
            <div class="ms-3 flex-grow-1 border-bottom opacity-10"></div>
            <div class="ms-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="themeSearch" class="form-control border-start-0 rounded-end-pill pe-3" placeholder="Search themes...">
                </div>
            </div>
        </div>
        
        <div class="row g-4" id="themesGrid">
        <?php
        $availableThemes = $data['themes'];
        $hasOtherThemes = false;
        if (count($availableThemes) > 0) {
            foreach ($availableThemes as $thm) {
                if ($thm == $active) continue;
                $hasOtherThemes = true;
                $t = Theme::data($thm);
                $thumb = file_exists(GX_THEME.'/'.$thm.'/screenshot.png') 
                    ? Site::$url.'/inc/themes/'.$thm.'/screenshot.png' 
                    : Site::$url.'/assets/images/noimagetheme.png';
        ?>
        <div class="col-xl-3 col-lg-4 col-md-6 theme-item" data-name="<?=strtolower($t['name']);?>">
            <div class="card theme-card-modern border-0 shadow-sm h-100 position-relative group">
                <div class="theme-thumb-container position-relative overflow-hidden">
                    <img src="<?=$thumb;?>" class="card-img-top object-fit-cover" style="height: 220px;" alt="<?=$t['name'];?>">
                    <div class="theme-actions-overlay position-absolute bottom-0 start-0 end-0 p-3 translate-y-full transition-transform">
                        <div class="d-grid gap-2">
                            <a href="index.php?page=themes&act=activate&themes=<?=$thm;?>&token=<?=TOKEN;?>" class="btn btn-primary rounded-pill shadow-lg">
                                <i class="bi bi-lightning-fill me-1"></i> <?=_("Apply Design");?>
                            </a>
                        </div>
                    </div>
                    <div class="position-absolute top-0 end-0 m-2">
                        <a href="index.php?page=themes&act=remove&themes=<?=$thm;?>&token=<?=TOKEN;?>" 
                           class="btn btn-danger btn-sm rounded-circle opacity-0 group-hover-opacity-100 shadow" 
                           onclick="return confirm('<?=_("Permanent removal of this theme?");?>');">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="fw-bold text-dark mb-0 text-truncate"><?=$t['name'];?></h6>
                        <span class="badge bg-light text-muted border py-0 extra-small">v<?=$t['version'];?></span>
                    </div>
                    <div class="extra-small text-muted text-truncate"><?=$t['developer'];?></div>
                </div>
            </div>
        </div>
        <?php 
            }
        }
        
        if (!$hasOtherThemes) {
        ?>
        <div class="col-12 py-5 text-center empty-state-container">
            <div class="empty-state-icon mb-4">
                <i class="bi bi-palette2"></i>
            </div>
            <h5 class="fw-bold text-dark">Your design library is currently restricted.</h5>
            <p class="text-muted px-md-5">Explore new aesthetics or build your own unique experience. No additional themes were found in your directory.</p>
            <button class="btn btn-outline-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#myModal">
                <i class="bi bi-plus-lg me-1"></i> <?=_("Import Theme");?>
            </button>
        </div>
        <?php } ?>
        </div>
    </div>
</div>

<!-- Installation Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <form action="index.php?page=themes" method="post" enctype="multipart/form-data">
                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center">
                        <i class="bi bi-cloud-arrow-up text-primary me-2"></i> <?=_("Package Deployment");?>
                    </h5>
                    <button type="button" class="btn btn-light rounded-pill p-2" data-bs-dismiss="modal" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-x-lg small"></i>
                    </button>
                </div>
                <div class="modal-body p-4 text-center">
                    <label class="theme-drop-zone border-2 border-dashed rounded-5 p-5 mb-3 bg-light d-block cursor-pointer position-relative">
                        <input type="file" name="theme" class="position-absolute opacity-0 start-0 top-0 w-100 h-100 cursor-pointer" id="fileInput">
                        <div id="dropZoneContent">
                            <div class="icon-circle bg-white shadow-sm mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 25px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark-zip text-primary fs-2"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1"><?=_("Drop Theme Package");?></h6>
                            <p class="extra-small text-muted mb-0"><?=_("Drag & Drop or Click to Browse .zip file");?></p>
                        </div>
                        <div id="fileSelected" class="d-none">
                            <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                            <h6 class="fw-bold text-dark mb-1" id="fileName">File name here</h6>
                            <button type="button" class="btn btn-sm btn-link text-muted extra-small py-0" onclick="resetFileSelection(event)">Change file</button>
                        </div>
                    </label>
                    <div class="alert bg-warning bg-opacity-10 border-0 rounded-4 p-3 extra-small text-start d-flex">
                        <i class="bi bi-shield-lock text-warning fs-5 me-3"></i>
                        <div class="text-dark opacity-75">
                            <strong>Security Note:</strong> Only use themes from trusted sources. System will pre-scan for suspicious scripts during installation.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Later</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" name="upload"><?=_("Install Design");?></button>
                </div>
                <input type="hidden" name="token" value="<?=TOKEN;?>">
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --transition-standard: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .active-theme-showcase {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid rgba(0,0,0,0.03);
    }
    
    .screenshot-wrapper { overflow: hidden; }
    .screenshot-wrapper img { transition: var(--transition-standard); }
    .active-theme-showcase:hover .screenshot-wrapper img { transform: scale(1.05); }
    
    .overlay-glow {
        position: absolute; inset: 0;
        background: radial-gradient(circle at center, transparent 0%, rgba(0,0,0,0.05) 100%);
        pointer-events: none;
    }
    
    .status-indicator { position: absolute; top: 1.5rem; left: 1.5rem; z-index: 5; }
    
    .pulse-dot {
        display: inline-block; width: 8px; height: 8px;
        background-color: #fff; border-radius: 50%;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(255, 255, 255, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }
    
    .theme-description-box { line-height: 1.8; }
    .hover-primary:hover { color: #0d6efd !important; }
    .hover-up { transition: var(--transition-standard); }
    .hover-up:hover { transform: translateY(-3px); }
    
    .theme-card-modern {
        border-radius: 24px;
        overflow: hidden;
        transition: var(--transition-standard);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .theme-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
    }
    
    .theme-thumb-container img { transition: var(--transition-standard); filter: grayscale(20%); }
    .theme-card-modern:hover .theme-thumb-container img { transform: scale(1.1); filter: grayscale(0%); }
    
    .theme-actions-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        opacity: 0;
        transition: var(--transition-standard);
    }
    .theme-card-modern:hover .theme-actions-overlay {
        opacity: 1;
        transform: translateY(0);
    }
    .translate-y-full { transform: translateY(100%); }
    
    .group:hover .group-hover-opacity-100 { opacity: 1 !important; }
    
    .theme-drop-zone { transition: var(--transition-standard); }
    .theme-drop-zone:hover { background-color: #fff !important; border-color: #0d6efd !important; box-shadow: 0 10px 30px rgba(13, 110, 253, 0.05); }
    
    .empty-state-icon {
        font-size: 5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        width: 150px; height: 150px; border-radius: 50px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto; color: #adb5bd;
    }
    
    .tracking-widest { letter-spacing: 0.1em; }
</style>

<script>
    document.getElementById('fileInput').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            document.getElementById('dropZoneContent').classList.add('d-none');
            document.getElementById('fileSelected').classList.remove('d-none');
            document.getElementById('fileName').textContent = this.files[0].name;
        }
    });

    function resetFileSelection(e) {
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('fileInput').value = '';
        document.getElementById('dropZoneContent').classList.remove('d-none');
        document.getElementById('fileSelected').classList.add('d-none');
    }

    document.getElementById('themeSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.theme-item');
        
        items.forEach(function(item) {
            let name = item.getAttribute('data-name');
            if (name.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
