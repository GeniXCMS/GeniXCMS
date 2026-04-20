<?php
/**
 * Media Manager UI - Manager View
 */

$subdir = Typo::cleanX($_GET['dir'] ?? '');
$isSelector = (isset($_GET['view']) && $_GET['view'] == 'selector');
$limit = 24;
$files = MediaManager::listFiles($subdir, 0, $limit);

// Build Breadcrumbs
$selectorParam = $isSelector ? '&view=selector' : '';
$breads = [['label' => 'Assets', 'url' => 'index.php?page=mods&mod=media-manager&sel=manager' . $selectorParam]];
if ($subdir) {
    $parts = explode('/', $subdir);
    $pathAcc = '';
    foreach ($parts as $p) {
        $pathAcc .= ($pathAcc ? '/' : '') . $p;
        $breads[] = ['label' => $p, 'url' => 'index.php?page=mods&mod=media-manager&sel=manager&dir=' . urlencode($pathAcc) . $selectorParam];
    }
}
$breads[count($breads)-1]['active'] = true;

// Define UI Schema
$schema = [
    'header' => [
        'title' => 'Digital Asset Navigator',
        'subtitle' => 'Unified management and precision editing for enterprise media assets.',
        'icon' => 'bi bi-grid-3x3-gap-fill',
        'buttons' => [
            [
                'label' => 'Synchronize Storage',
                'icon' => 'bi bi-arrow-clockwise',
                'class' => 'btn btn-light border bg-white rounded-pill px-4 shadow-sm',
                'attr' => 'id="btnSyncStorage" onclick="syncStorage()"'
            ],
            [
                'label' => 'Upload Asset',
                'icon' => 'bi bi-cloud-arrow-up-fill',
                'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
                'attr' => 'id="btnUploadTrigger"'
            ]
        ]
    ]
];

$ui = new UiBuilder($schema);
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

echo '<div class="media-manager-header">';
$ui->renderHeader();
echo '</div>';

echo '<div class="container-fluid px-0">
    <div class="row mb-4">
        <div class="col-md-6">';
            $ui->renderElement([
                'type' => 'breadcrumb',
                'items' => $breads
            ]);
echo '  </div>
        <div class="col-md-6 text-end">
            <div class="input-group input-group-sm bg-white rounded-pill shadow-sm border px-2 py-1" style="max-width:350px; display:inline-flex;">
                <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="mediaSearch" class="form-control border-0 shadow-none bg-transparent" placeholder="Find assets in this folder...">
            </div>
        </div>
    </div>';

// Hidden elements for upload handling
echo '<input type="file" id="mediaFileInput" style="display:none;" accept="image/*,video/*,audio/*,application/pdf,application/zip">
      <input type="hidden" id="currentDir" value="' . $subdir . '">
      <input type="hidden" id="securityToken" value="' . TOKEN . '">';

echo '<div class="row g-3" id="media-grid">';
foreach ($files as $file) {
    $isImage = ($file['type'] === 'image');
    $isNextDir = ($file['is_dir'] ? 'index.php?page=mods&mod=media-manager&sel=manager&dir=' . urlencode($file['path']) . $selectorParam : '#');
    $clickAction = '';
    
    if (!$file['is_dir']) {
        if ($isSelector) {
            $clickAction = "onclick=\"selectAsset('" . $file['url'] . "')\"";
            $titleAttr = 'title="Click to select this asset"';
        } else {
            $thumbPreviewUrl = ($file['type'] === 'image') ? Url::thumb($file['url'], '', '800') : '';
            $clickAction = "onclick=\"viewMedia('" . $file['url'] . "', '" . $thumbPreviewUrl . "', '" . $file['type'] . "', '" . $file['name'] . "', '" . addslashes($file['path']) . "')\"";
            $titleAttr = 'title="Click to view/edit"';
        }
    } else {
        $clickAction = "onclick=\"window.location.href='{$isNextDir}'\"";
        $titleAttr = 'title="Open folder"';
    }

    echo "
    <div class='col-xl-2 col-lg-3 col-md-4 col-6 media-item-container'>
        <div class='card border-0 shadow-sm rounded-4 h-100 overflow-hidden media-card position-relative' style='cursor:pointer;' {$clickAction} {$titleAttr}>
            <div class='media-checkbox-wrapper' onclick='event.stopPropagation();'>
                <input type='checkbox' class='form-check-input media-checkbox shadow-none' value='{$file['path']}'>
            </div>
            <div class='card-img-top bg-light d-flex align-items-center justify-content-center overflow-hidden position-relative' style='height:140px;'>";
            if ($isImage) {
                // Use thumb for display
                $thumbUrl = Url::thumb($file['url'], 'crop', '250x250');
                echo "<img src='{$thumbUrl}' style='width:100%; height:100%; object-fit:cover;' loading='lazy'>";
                if ($isSelector) {
                    echo "<div class='position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 opacity-0 hover-opacity-1' style='transition:opacity 0.2s;'>
                            <span class='badge bg-primary rounded-pill px-3 py-2 shadow-sm'><i class='bi bi-check2-circle me-1'></i> SELECT</span>
                          </div>";
                }
            } else {
                echo "<i class='{$file['icon']}' style='font-size:3rem;'></i>";
            }
    echo "  </div>
            <div class='card-body p-3'>
                <div class='fw-bold text-dark small truncation-ellipsis mb-1 media-filename' title='{$file['name']}'>{$file['name']}</div>
                <div class='d-flex justify-content-between align-items-center'>
                    <span class='extra-small text-muted'>" . ($file['is_dir'] ? 'Folder' : MediaManager::formatSize($file['size'])) . "</span>
                    <div class='dropdown' onclick='event.stopPropagation();'>
                        <i class='bi bi-three-dots-vertical text-muted' data-bs-toggle='dropdown' style='cursor:pointer;'></i>
                        <ul class='dropdown-menu dropdown-menu-end shadow border-0 rounded-3 small-dropdown'>
                            " . ($isSelector && !$file['is_dir'] ? "<li><a class='dropdown-item py-2 fw-bold text-primary' href='#' onclick=\"selectMedia('{$file['url']}')\"><i class='bi bi-check-circle-fill me-2'></i> Insert Asset</a></li><li><hr class='dropdown-divider opacity-50'></li>" : "") . "
                            <li><a class='dropdown-item py-2' href='#' onclick=\"renameMedia('{$file['path']}', '{$file['name']}')\"><i class='bi bi-pencil me-2'></i> Rename</a></li>
                            <li><hr class='dropdown-divider opacity-50'></li>
                            <li><a class='dropdown-item py-2 text-danger' href='#' onclick=\"deleteMedia('{$file['path']}')\"><i class='bi bi-trash me-2'></i> Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>";
}
echo '</div></div>';

// Modals & Styling
?>
<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow">
            <div class="modal-header border-0 py-3 px-4">
                <h6 class="modal-title fw-bold" id="previewTitle">Asset Preview</h6>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" id="previewBody" style="min-height:300px;">
                <!-- DYNAMIC CONTENT -->
            </div>
            <div class="modal-footer border-0 p-3 d-flex justify-content-between">
                <div id="previewMeta" class="small text-muted ps-2"></div>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnEditImage" style="display:none;">Edit Image</button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Editor Modal -->
<div class="modal fade" id="editorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-5 shadow">
            <div class="modal-body p-0 bg-light d-flex align-items-center justify-content-center" style="height:60vh; overflow:hidden;">
                <img id="editingImage" src="" style="max-width:100%; display:block;">
            </div>
            <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <!-- Mode Switcher -->
                    <div class="btn-group rounded-pill overflow-hidden shadow-sm border me-2">
                        <button type="button" class="btn btn-primary px-3 border-0 py-1 extra-small fw-bold" id="btnModeCrop" onclick="setEditorMode('crop')">CROP</button>
                        <button type="button" class="btn btn-white px-3 border-0 py-1 extra-small fw-bold" id="btnModeResize" onclick="setEditorMode('resize')">SCALE</button>
                    </div>

                    <div id="cropControls" class="d-flex align-items-center gap-2">
                        <div class="btn-group rounded-pill overflow-hidden shadow-sm border">
                            <button type="button" class="btn btn-white px-3 border-0 py-1" onclick="cropper.rotate(-90)" title="Rotate Left"><i class="bi bi-arrow-counterclockwise"></i></button>
                            <button type="button" class="btn btn-white px-3 border-0 py-1" onclick="cropper.rotate(90)" title="Rotate Right"><i class="bi bi-arrow-clockwise"></i></button>
                        </div>
                        <div class="btn-group rounded-pill overflow-hidden shadow-sm border ms-1">
                            <button type="button" class="btn btn-white px-2 border-0 py-1 extra-small fw-bold" onclick="cropper.setAspectRatio(1/1)">1:1</button>
                            <button type="button" class="btn btn-white px-2 border-0 py-1 extra-small fw-bold" onclick="cropper.setAspectRatio(16/9)">16:9</button>
                            <button type="button" class="btn btn-white px-2 border-0 py-1 extra-small fw-bold" onclick="cropper.setAspectRatio(NaN)">Free</button>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1 border shadow-sm">
                    <i class="bi bi-aspect-ratio text-muted extra-small"></i>
                    <div class="input-group input-group-sm border-0" style="width: 80px;">
                        <input type="number" id="resizeW" class="form-control border-0 bg-transparent fw-bold text-center p-0" placeholder="W" title="Width">
                    </div>
                    <span class="text-muted extra-small">×</span>
                    <div class="input-group input-group-sm border-0" style="width: 80px;">
                        <input type="number" id="resizeH" class="form-control border-0 bg-transparent fw-bold text-center p-0" placeholder="H" title="Height">
                    </div>
                    <span class="text-muted extra-small px-1">px</span>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="btnSaveCrop">Save Transform</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sync Progress Modal -->
<div class="modal fade" id="syncModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-body p-5 text-center">
                <div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
                <h5 class="fw-bold mb-2">Synchronizing Storage</h5>
                <p class="text-muted mb-4">Optimizing your asset catalog and refreshing metadata. Please do not close this window.</p>
                <div class="progress rounded-pill bg-light" style="height: 12px;">
                    <div id="syncProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<style>
    <?php if ($isSelector) : ?>
    #sidebar, .top-navbar, footer, header.top-navbar { display: none !important; }
    #main-wrapper { margin: 0 !important; padding: 0 !important; width: 100% !important; left: 0 !important; min-width: 100% !important; }
    .content-body { padding: 0 !important; margin: 0 !important; }
    .main-content { margin-top: 0 !important; padding: 35px 40px !important; }
    body { background: #fff !important; }
    /* Hide branding and management buttons in selector mode */
    .media-manager-header, #btnSyncStorage, .breadcrumb { display: none !important; }
    <?php endif; ?>
    .media-card { transition: all 0.3s ease; }
    .media-card:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important; }
    .media-card img { transition: transform 0.5s ease; }
    .media-card:hover img { transform: scale(1.05); }
    .truncation-ellipsis { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .extra-small { font-size: 0.7rem; }
    #media-grid .card-img-top { background-image: radial-gradient(#dee2e6 1px, transparent 1px); background-size: 10px 10px; }
    .dropdown-menu.small-dropdown { min-width: 120px; font-size: 0.8rem; padding: 0.35rem 0; }
    .dropdown-menu.small-dropdown .dropdown-item { padding: 0.3rem 1rem; }
    
    .media-checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
        width: 1.2rem;
        height: 1.2rem;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.2) !important;
        box-shadow: 0 0 0 2px rgba(255,255,255,0.8);
        border-radius: 4px;
    }
    .media-checkbox-wrapper { position: absolute; top: 12px; left: 12px; z-index: 5; opacity: 0; transition: opacity 0.2s; }
    .media-card:hover .media-checkbox-wrapper, .media-card.selected .media-checkbox-wrapper { opacity: 1; }
    .media-card.selected { border: 2px solid var(--bs-primary) !important; }
    .hover-opacity-1 { opacity: 0; }
    .media-card:hover .hover-opacity-1 { opacity: 1 !important; }
    
    #bulk-toolbar { position: fixed; bottom: -100px; left: 50%; transform: translateX(-50%); transition: bottom 0.4s ease; z-index: 1050; }
    #bulk-toolbar.show { bottom: 30px; }
</style>

<div id="bulk-toolbar" class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-auto">
            <div class="bg-dark text-white rounded-pill shadow-lg px-4 py-2 d-flex align-items-center gap-3 border border-secondary">
                <div class="fw-bold small px-2 border-end border-secondary me-2"><span id="selected-count">0</span> Selected</div>
                <button class="btn btn-link text-white text-decoration-none small p-0 fw-bold" onclick="selectAll()">Select All</button>
                <button class="btn btn-link text-white text-decoration-none small p-0 fw-bold" onclick="clearSelection()">Clear</button>
                <div class="vr bg-secondary mx-1"></div>
                <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" onclick="bulkDelete()">
                    <i class="bi bi-trash-fill me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<div id="scroll-sentinel" class="text-center py-5 mt-4" style="min-height: 100px;">
    <div id="load-more-spinner" class="spinner-border text-primary opacity-50 d-none" role="status"></div>
</div>

<script>
let cropper;
let currentPreviewUrl = '';
let currentRelativePath = '';
let selectedFiles = [];
let editorMode = 'crop'; // crop or scale
let originalSize = { width: 0, height: 0 };

// Infinite Scroll State
let mediaOffset = 24;
let mediaLimit = 24;
let isMediaLoading = false;
let hasMoreMedia = true;

const mediaObserver = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting && !isMediaLoading && hasMoreMedia) {
        loadMoreMedia();
    }
}, { rootMargin: '300px' });

$(document).ready(function() {
    mediaObserver.observe(document.getElementById('scroll-sentinel'));
});

function selectAsset(url) {
    if (window.opener) {
        window.opener.postMessage({ type: 'gx_media_selected', url: url }, '*');
        window.close();
    } else if (window.parent) {
        window.parent.postMessage({ type: 'gx_media_selected', url: url }, '*');
    }
}

function loadMoreMedia() {
    isMediaLoading = true;
    $('#load-more-spinner').removeClass('d-none');

    const formData = new FormData();
    formData.append('action', 'get_media_page');
    formData.append('dir', $('#currentDir').val());
    formData.append('offset', mediaOffset);
    formData.append('limit', mediaLimit);
    formData.append('token', $('#securityToken').val());

    fetch(window.GX_AJAX_URL, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success' && res.data.length > 0) {
            res.data.forEach(file => {
                appendMediaItem(file);
            });
            mediaOffset += res.data.length;
            if (res.data.length < mediaLimit) hasMoreMedia = false;
        } else {
            hasMoreMedia = false;
        }
    })
    .catch(err => console.error('Paging error:', err))
    .finally(() => {
        isMediaLoading = false;
        $('#load-more-spinner').addClass('d-none');
        if (!hasMoreMedia) $('#scroll-sentinel').addClass('d-none');
    });
}

function appendMediaItem(file) {
    const isImage = (file.type === 'image');
    const isNextDir = file.is_dir ? `index.php?page=mods&mod=media-manager&sel=manager&dir=${encodeURIComponent(file.path)}` : '#';
    let clickAction = '';
    let thumbPreviewUrl = '';

    if (!file.is_dir) {
        if (isImage) {
            // We can't easily call PHP Url::thumb here, so we'll use a standardized thumb URL if possible
            // or just the raw URL if thumbfly logic isn't easily accessible via JS.
            // In GeniXCMS, thumb is handled by thumb.php?thumb=PATH
            thumbPreviewUrl = `thumb.php?thumb=${encodeURIComponent(file.path)}&size=800`;
            displayThumb = `thumb.php?thumb=${encodeURIComponent(file.path)}&type=crop&size=250x250`;
        }
        clickAction = `onclick="viewMedia('${file.url}', '${thumbPreviewUrl}', '${file.type}', '${file.name}', '${file.path.replace(/'/g, "\\'")}')"`;
    } else {
        clickAction = `onclick="window.location.href='${isNextDir}'"`;
        displayThumb = `<i class='${file.icon}' style='font-size:3rem;'></i>`;
    }

    const imgHtml = isImage ? `<img src='${displayThumb}' style='width:100%; height:100%; object-fit:cover;' loading='lazy'>` : displayThumb;

    const html = `
    <div class='col-xl-2 col-lg-3 col-md-4 col-6 media-item-container'>
        <div class='card border-0 shadow-sm rounded-4 h-100 overflow-hidden media-card position-relative' style='cursor:pointer;' ${clickAction}>
            <div class='media-checkbox-wrapper' onclick='event.stopPropagation();'>
                <input type='checkbox' class='form-check-input media-checkbox shadow-none' value='${file.path}'>
            </div>
            <div class='card-img-top bg-light d-flex align-items-center justify-content-center overflow-hidden position-relative' style='height:140px;'>
                ${imgHtml}
            </div>
            <div class='card-body p-3'>
                <div class='fw-bold text-dark small truncation-ellipsis mb-1 media-filename' title='${file.name}'>${file.name}</div>
                <div class='d-flex justify-content-between align-items-center'>
                    <span class='extra-small text-muted'>${file.is_dir ? 'Folder' : formatBytes(file.size)}</span>
                    <div class='dropdown' onclick='event.stopPropagation();'>
                        <i class='bi bi-three-dots-vertical text-muted' data-bs-toggle='dropdown' style='cursor:pointer;'></i>
                        <ul class='dropdown-menu dropdown-menu-end shadow border-0 rounded-3 small-dropdown'>
                            <li><a class='dropdown-item py-2' href='#' onclick="renameMedia('${file.path}', '${file.name}')"><i class='bi bi-pencil me-2'></i> Rename</a></li>
                            <li><hr class='dropdown-divider opacity-50'></li>
                            <li><a class='dropdown-item py-2 text-danger' href='#' onclick="deleteMedia('${file.path}')"><i class='bi bi-trash me-2'></i> Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
    $('#media-grid').append(html);
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

function setEditorMode(mode) {
    editorMode = mode;
    if (mode === 'crop') {
        $('#btnModeCrop').addClass('btn-primary').removeClass('btn-white');
        $('#btnModeResize').addClass('btn-white').removeClass('btn-primary');
        $('#cropControls').fadeIn(200);
        cropper.enable();
    } else {
        $('#btnModeCrop').addClass('btn-white').removeClass('btn-primary');
        $('#btnModeResize').addClass('btn-primary').removeClass('btn-white');
        $('#cropControls').fadeOut(200);
        
        // Scale mode: Select full image and reset inputs to current scale
        const data = cropper.getImageData();
        originalSize = { width: data.naturalWidth, height: data.naturalHeight };
        cropper.setAspectRatio(NaN);
        cropper.setCropBoxData({
            left: 0,
            top: 0,
            width: data.width,
            height: data.height
        });
        cropper.disable(); // Prevent resizing the full boxes
        $('#resizeW').val(originalSize.width);
        $('#resizeH').val(originalSize.height);
    }
}

function updateBulkToolbar() {
    const count = $('.media-checkbox:checked').length;
    $('#selected-count').text(count);
    if (count > 0) {
        $('#bulk-toolbar').addClass('show');
    } else {
        $('#bulk-toolbar').removeClass('show');
    }
}

function selectAll() {
    $('.media-checkbox').prop('checked', true);
    $('.media-card').addClass('selected');
    updateBulkToolbar();
}

function clearSelection() {
    $('.media-checkbox').prop('checked', false);
    $('.media-card').removeClass('selected');
    updateBulkToolbar();
}

$(document).on('change', '.media-checkbox', function(e) {
    const card = $(this).closest('.media-card');
    if ($(this).is(':checked')) {
        card.addClass('selected');
    } else {
        card.removeClass('selected');
    }
    updateBulkToolbar();
});

function bulkDelete() {
    const files = [];
    $('.media-checkbox:checked').each(function() {
        files.push($(this).val());
    });

    if (files.length === 0) return;

    if (confirm(`Are you sure you want to delete ${files.length} items?`)) {
        const formData = new FormData();
        formData.append('action', 'bulk_delete');
        files.forEach(f => formData.append('files[]', f));
        formData.append('token', $('#securityToken').val());

        fetch(window.GX_AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}

function syncStorage() {
    const syncModal = new bootstrap.Modal(document.getElementById('syncModal'));
    syncModal.show();

    const formData = new FormData();
    formData.append('action', 'sync_storage');
    formData.append('token', $('#securityToken').val());

    fetch(window.GX_AJAX_URL, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Add a slight delay for better UX
        setTimeout(() => {
            syncModal.hide();
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        }, 800);
    })
    .catch(error => {
        syncModal.hide();
        console.error('Error:', error);
        alert('Sync failed.');
    });
}

// Handle Upload
$('#btnUploadTrigger').click(function() {
    $('#mediaFileInput').click();
});

$('#mediaFileInput').change(function() {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('action', 'upload');
    formData.append('dir', $('#currentDir').val());
    formData.append('token', $('#securityToken').val());

    const originalHtml = $('#btnUploadTrigger').html();
    $('#btnUploadTrigger').html('<span class="spinner-border spinner-border-sm me-2"></span>Uploading...').addClass('disabled');

    fetch(window.GX_AJAX_URL, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message);
            $('#btnUploadTrigger').html(originalHtml).removeClass('disabled');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Upload failed. Check console for details.');
        $('#btnUploadTrigger').html(originalHtml).removeClass('disabled');
    });
});

function viewMedia(originalUrl, thumbUrl, type, name, relPath) {
    currentPreviewUrl = originalUrl;
    currentRelativePath = relPath;
    $('#previewTitle').text(name);
    $('#previewBody').empty();
    $('#btnEditImage').hide();
    
    if (type === 'image') {
        const displayUrl = thumbUrl || originalUrl;
        $('#previewBody').append(`<img src="${displayUrl}" class="img-fluid" style="max-height:80vh;">`);
        $('#btnEditImage').show();
    } else if (type === 'video') {
        $('#previewBody').append(`<video controls autoplay class="w-100"><source src="${originalUrl}" type="video/mp4"></video>`);
    } else if (type === 'audio') {
        $('#previewBody').append(`<audio controls autoplay><source src="${originalUrl}" type="audio/mpeg"></audio>`);
    }
    
    $('#previewModal').modal('show');
}

$('#btnEditImage').click(function() {
    $('#previewModal').modal('hide');
    $('#editingImage').attr('src', currentPreviewUrl);
    $('#editorModal').modal('show');
});

$('#editorModal').on('shown.bs.modal', function() {
    const image = document.getElementById('editingImage');
    cropper = new Cropper(image, {
        viewMode: 2,
        responsive: true,
        background: true,
        crop(event) {
            if (editorMode === 'crop') {
                $('#resizeW').val(Math.round(event.detail.width));
                $('#resizeH').val(Math.round(event.detail.height));
            }
        }
    });
    setEditorMode('crop');
}).on('hidden.bs.modal', function() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
});

// Manual dimension input
$('#resizeW, #resizeH').on('change input', function(e) {
    if (!cropper) return;
    
    let w = parseInt($('#resizeW').val());
    let h = parseInt($('#resizeH').val());
    
    if (editorMode === 'crop') {
        cropper.setData({ width: w, height: h });
    } else {
        // Scaling mode logic: keep aspect ratio if one is changed
        const ratio = originalSize.width / originalSize.height;
        if (e.target.id === 'resizeW') {
            h = Math.round(w / ratio);
            $('#resizeH').val(h);
        } else {
            w = Math.round(h * ratio);
            $('#resizeW').val(w);
        }
    }
});

$('#btnSaveCrop').click(function() {
    let canvasOptions = {
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    };

    if (editorMode === 'crop') {
        canvasOptions.width = Math.round($('#resizeW').val());
        canvasOptions.height = Math.round($('#resizeH').val());
    } else {
        // For scale, we want the WHOLE image scaled to W/H
        // In scale mode we disabled cropper, but it's still at full selection
        canvasOptions.width = Math.round($('#resizeW').val());
        canvasOptions.height = Math.round($('#resizeH').val());
    }

    const canvas = cropper.getCroppedCanvas(canvasOptions);
    const imageData = canvas.toDataURL('image/jpeg', 0.9);

    const formData = new FormData();
    formData.append('action', 'save_image');
    formData.append('image', imageData);
    formData.append('file', currentRelativePath);
    formData.append('token', $('#securityToken').val());

    $('#btnSaveCrop').html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...').addClass('disabled');

    fetch(window.GX_AJAX_URL, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message);
            $('#btnSaveCrop').html('Save Transform').removeClass('disabled');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Save failed.');
        $('#btnSaveCrop').html('Save Transform').removeClass('disabled');
    });
});

function deleteMedia(file) {
    if (confirm('Are you sure you want to permanently delete this asset?')) {
        const formData = new FormData();
        formData.append('action', 'delete_media');
        formData.append('file', file);
        formData.append('token', $('#securityToken').val());

        fetch(window.GX_AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}

function renameMedia(file, oldName) {
    const newName = prompt('Enter new name:', oldName);
    if (newName && newName !== oldName) {
        const formData = new FormData();
        formData.append('action', 'rename_media');
        formData.append('file', file);
        formData.append('new_name', newName);
        formData.append('token', $('#securityToken').val());

        fetch(window.GX_AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}

// Search Filter Handler
$('#mediaSearch').on('keyup', function() {
    const value = $(this).val().toLowerCase();
    $('#media-grid .media-item-container').filter(function() {
        $(this).toggle($(this).find('.media-filename').text().toLowerCase().indexOf(value) > -1)
    });
});

function selectMedia(url) {
    if (window.opener) {
        window.opener.postMessage({ type: 'gx_media_selected', url: url }, '*');
        window.close();
    } else if (window.parent && window.parent !== window) {
        window.parent.postMessage({ type: 'gx_media_selected', url: url }, '*');
    } else {
        alert('Selected: ' + url);
    }
}
</script>
