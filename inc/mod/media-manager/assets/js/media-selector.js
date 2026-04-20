/**
 * GeniXCMS Native Media Selector
 * Global component for selecting media assets on-the-fly.
 */
var GxMedia = {
    modal: null,
    grid: null,
    bread: null,
    search: null,
    loader: null,
    callback: null,
    currentPath: '',
    userFolder: null,

    init: function() {
        if (this.modal) return;

        var modalHtml = `
            <div class="modal fade" id="gxMediaSelectorModal" tabindex="-1" aria-hidden="true" style="z-index: 1000001;">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="min-height: 70vh;">
                        <div class="modal-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h5 class="modal-title fw-bold mb-0 me-3"><i class="bi bi-images me-2 text-primary"></i>Media Selector</h5>
                                <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0" id="gxMediaBreadcrumbs" style="font-size: 0.85rem;"></ol></nav>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="input-group input-group-sm bg-light border rounded-pill px-2" style="width: 250px;">
                                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search"></i></span>
                                    <input type="text" id="gxMediaSearch" class="form-control border-0 bg-transparent shadow-none" placeholder="Search...">
                                </div>
                                <button type="button" id="gxMediaUploadBtn" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm">
                                    <i class="bi bi-cloud-arrow-up"></i> Upload
                                </button>
                                <input type="file" id="gxMediaFileInput" class="d-none" accept="image/*,video/*,audio/*,.pdf,.zip,.txt">
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body p-4 bg-light" id="gxMediaSelectorBody" style="max-height: 75vh; overflow-y: auto;">
                            <div class="row g-3" id="gxMediaGrid"></div>
                            <div id="gxMediaLoader" class="text-center py-5 d-none"><div class="spinner-border text-primary"></div></div>
                        </div>
                    </div>
                </div>
            </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        this.modal = document.getElementById('gxMediaSelectorModal');
        this.grid = document.getElementById('gxMediaGrid');
        this.bread = document.getElementById('gxMediaBreadcrumbs');
        this.search = document.getElementById('gxMediaSearch');
        this.loader = document.getElementById('gxMediaLoader');

        this.bread.onclick = (e) => {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                this.loadMedia(e.target.dataset.path);
            }
        };

        this.search.onkeyup = (e) => {
            var q = e.target.value.toLowerCase();
            document.querySelectorAll('#gxMediaGrid .gx-tile').forEach(t => {
                const name = t.querySelector('.small').innerText.toLowerCase();
                t.parentElement.style.display = name.includes(q) ? '' : 'none';
            });
        };

        const uploadBtn = document.getElementById('gxMediaUploadBtn');
        const fileInput = document.getElementById('gxMediaFileInput');
        
        uploadBtn.onclick = () => fileInput.click();
        
        fileInput.onchange = (e) => {
            if (e.target.files.length > 0) {
                this.handleUpload(e.target.files[0]);
            }
        };
    },

    handleUpload: function(file) {
        this.loader.classList.remove('d-none');
        this.grid.style.opacity = '0.5';

        var fd = new FormData();
        fd.append('action', 'upload');
        fd.append('file', file);
        fd.append('dir', this.getEffectivePath(this.currentPath));
        if (this.userFolder) {
            fd.append('user_folder', this.userFolder);
        }
        fd.append('token', (window.GX_TOKEN || '').trim());

        var ajaxUrl = window.GX_AJAX_URL || (window.location.origin + '/index.php?ajax=media-manager');
        fetch(ajaxUrl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            this.grid.style.opacity = '1';
            if (res.status === 'success') {
                this.loadMedia(this.currentPath);
            } else {
                this.loader.classList.add('d-none');
                alert('Upload failed: ' + (res.message || 'Unknown error'));
            }
            document.getElementById('gxMediaFileInput').value = '';
        })
        .catch(err => {
            this.loader.classList.add('d-none');
            this.grid.style.opacity = '1';
            alert('Network error during upload.');
            console.error('GxMedia Upload Error:', err);
        });
    },

    select: function(callback, userFolder) {
        this.init();
        this.callback = callback;
        // Capture user folder at the time select() is called (avoids window var conflicts)
        this.userFolder = userFolder || window.GX_USER_FOLDER || null;
        this.currentPath = '';
        this.loadMedia('');
        var bsModal = new bootstrap.Modal(this.modal);
        bsModal.show();

        // Ensure we handle modal hiding clean
        this.modal.addEventListener('hidden.bs.modal', function() {
            // cleanup if needed
        }, { once: true });
    },

    loadMedia: function(path) {
        this.currentPath = path;
        this.grid.innerHTML = '';
        this.loader.classList.remove('d-none');
        this.updateBreadcrumbs(path);

        var offset = 0;
        var ajaxUrl = window.GX_AJAX_URL || (window.location.origin + '/index.php?ajax=media-manager');
        var url = new URL(ajaxUrl, window.location.origin);
        url.searchParams.set('action', 'get_media_page');
        url.searchParams.set('dir', this.getEffectivePath(path));
        if (this.userFolder) {
            url.searchParams.set('user_folder', this.userFolder);
        }
        url.searchParams.set('offset', offset);
        url.searchParams.set('limit', 24);

        fetch(url.toString(), { method: 'GET' })
        .then(r => r.json())
        .then(res => {
            this.loader.classList.add('d-none');
            if (res.status === 'success' && res.data) {
                if (res.data.length === 0) {
                    this.grid.innerHTML = '<div class="col-12 text-center py-5 text-muted"><i class="bi bi-folder2-open display-4 d-block mb-3"></i> This folder is empty</div>';
                } else {
                    this.renderItems(res.data);
                }
            } else {
                this.grid.innerHTML = `<div class="col-12 text-center py-5 text-danger"><i class="bi bi-exclamation-triangle display-4 d-block mb-3"></i> Error: ${res.message || 'Failed to load media'}</div>`;
            }
        })
        .catch(err => {
            this.loader.classList.add('d-none');
            this.grid.innerHTML = '<div class="col-12 text-center py-5 text-danger">Network error or invalid server response.</div>';
            console.error('GxMedia Error:', err);
        });
    },

    updateBreadcrumbs: function(path) {
        this.bread.innerHTML = '<li class="breadcrumb-item"><a href="#" data-path="">Root</a></li>';
        var folder = this.userFolder || window.GX_USER_FOLDER || null;
        if (path) {
            let acc = '';
            path.split('/').forEach(p => {
                if (!p || (folder && folder.split('/').includes(p))) return;
                acc += (acc ? '/' : '') + p;
                this.bread.innerHTML += `<li class="breadcrumb-item"><a href="#" data-path="${acc}">${p}</a></li>`;
            });
        }
    },

    getEffectivePath: function(relativePath) {
        var folder = this.userFolder || window.GX_USER_FOLDER || null;
        if (folder) {
            var base = folder.replace(/\/+$/, '');
            return relativePath ? base + '/' + relativePath.replace(/^\/+/, '') : base;
        }
        return relativePath;
    },

    stripUserFolder: function(path) {
        var folder = this.userFolder || window.GX_USER_FOLDER || null;
        if (folder) {
            let prefix = folder.replace(/^\/+|\/+$/g, '');
            if (path.startsWith(prefix)) {
                return path.substring(prefix.length).replace(/^\/+/, '');
            }
        }
        return path;
    },

    renderItems: function(items) {
        items.forEach(item => {
            var col = document.createElement('div');
            col.className = 'col-lg-2 col-md-3 col-6';
            var thumb = item.type === 'image' ? item.thumb_tiles : '';
            var icon = item.is_dir ? 'bi bi-folder-fill text-warning' : (item.icon || 'bi bi-file-earmark');
            
            col.innerHTML = `
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden text-center cursor-pointer gx-tile" 
                     style="cursor: pointer; transition: transform 0.2s;" data-path="${item.path}" data-type="${item.is_dir ? 'dir' : 'file'}" data-url="${item.url}">
                    <div class="card-img-top bg-white d-flex align-items-center justify-content-center p-2" style="height: 120px;">
                        ${item.type === 'image' ? `<img src="${thumb}" class="img-fluid rounded" style="max-height: 100%;">` : `<i class="${icon}" style="font-size: 3rem;"></i>`}
                    </div>
                    <div class="card-body p-2 border-top bg-white">
                        <div class="small fw-bold text-truncate" title="${item.name}">${item.name}</div>
                    </div>
                </div>`;
            
            const itemPath = this.stripUserFolder(item.path);
            col.querySelector('.gx-tile').onclick = () => {
                if (item.is_dir) {
                    this.loadMedia(itemPath);
                } else {
                    if (this.callback) {
                        const finalUrl = item.type === 'image' ? item.thumb : item.url;
                        this.callback(finalUrl);
                    }
                    bootstrap.Modal.getInstance(this.modal).hide();
                }
            };
            this.grid.appendChild(col);
        });
    }
};
