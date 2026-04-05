/**
 * GxEditor Renderer Module
 * Handles drawing blocks to DOM and managing block interactions
 */

/**
 * Main entry point to redraw all blocks in a shell
 */
window.renderAllBlocks = function(state) {
    if (!state || !state.shell) return;
    
    // Safety: ensure we belong to the global GxEditor tracking
    if (window.GxEditor && window.GxEditor._editors) {
        if (window.GxEditor._editors.indexOf(state) === -1) window.GxEditor._editors.push(state);
    }

    // Clean current blocks (keeping the add button wrap)
    var currentBlocks = state.shell.querySelectorAll(':scope > .gxb-block');
    currentBlocks.forEach(function(el) { el.remove(); });
    
    var addBtnWrap = state.shell.querySelector('.gxb-addbtn-wrap');
    
    state.blocks.forEach(function(block) {
        var blockEl = window.createBlockEl(state, block);
        if (addBtnWrap) {
            state.shell.insertBefore(blockEl, addBtnWrap);
        } else {
            state.shell.appendChild(blockEl);
        }
    });
    
    if (typeof serializeToTextarea === 'function') serializeToTextarea(state);
    else if (window.serializeToTextarea) window.serializeToTextarea(state);
};

/**
 * Builds the DOM for a single block
 */
window.createBlockEl = function(state, block) {
    var typeDef = GxEditor._blocks[block.type] || GxEditor._blocks['paragraph'];
    var wrapper = document.createElement('div');
    wrapper.className = 'gxb-block';
    wrapper.dataset.blockId = block.id;
    wrapper.dataset.type = block.type;
    wrapper.draggable = true;

    // Handle area (LEFT)
    var handleWrap = document.createElement('div');
    handleWrap.className = 'gxb-handle-wrap';
    handleWrap.innerHTML = 
        '<button class="gxb-add-inline" type="button" title="Add block above"><i class="bi bi-plus"></i></button>' +
        '<span class="gxb-handle" title="Drag to reorder"><i class="bi bi-grid-3x2-gap-fill"></i></span>';
    
    handleWrap.querySelector('.gxb-add-inline').addEventListener('click', function(e) {
        e.preventDefault(); e.stopPropagation();
        var anchor = wrapper.getBoundingClientRect();
        if (typeof openPickerBefore === 'function') openPickerBefore(state, block.id, { top: Math.max(0, anchor.top - 10), left: anchor.left });
        else if (window.openPickerBefore) window.openPickerBefore(state, block.id, { top: Math.max(0, anchor.top - 10), left: anchor.left });
    });
    
    var tagToUse = (block.type === 'ul' || block.type === 'ol') ? block.type : 'div';
    var content = document.createElement(tagToUse);
    content.className = 'gxb-content';
    content.dataset.placeholder = typeDef.placeholder || '';
    
    // Core property rendering logic
    if (typeDef.render && typeof typeDef.render === 'function') {
        typeDef.render(state, block, content, wrapper);
    } else if (block.type === 'divider') {
        content.innerHTML = '<hr>';
        content.contentEditable = 'false';
    } else if (block.type === 'image') {
        window.renderImageBlock(state, block, content);
    } else if (block.type === 'ul' || block.type === 'ol') {
        content.contentEditable = 'true';
        var lines = (block.content || '').split('\n');
        content.innerHTML = lines.map(function(l){ return l.trim() ? '<li>'+l+'</li>' : '<li><br></li>'; }).join('') || '<li><br></li>';
    } else if (block.type === 'code') {
        content.contentEditable = 'true';
        content.spellcheck = false;
        content.textContent = block.content;
    } else if (block.type === 'grid2' || block.type === 'grid2x2') {
        content.className = 'gxb-content';
        content.style.cssText = 'display:flex; flex-direction:column; gap:15px; padding:8px 0;';
        var rowCount = block.rowCount || (block.type === 'grid2x2' ? 2 : 1);
        var colCount = block.colCount || 2;
        var ratio = (block.colRatio || '6:6').split(':');
        for (var r = 0; r < rowCount; r++) {
            var rowDiv = document.createElement('div');
            rowDiv.style.cssText = 'display:flex; gap:15px;';
            for (var c = 0; c < colCount; c++) {
                var idx = (r * colCount) + (c + 1);
                var key = 'col' + idx;
                var cell = document.createElement('div');
                cell.className = 'gxb-col';
                cell.style.flex = (colCount == 2) ? (ratio[c] || '6') : '1';
                var subTa = document.createElement('textarea');
                subTa.className = 'editor gxb-nested-editor';
                subTa.id = 'gxb-inner-' + block.id + '-' + key;
                subTa.style.display = 'none';
                var cellHtml = block[key] || '';
                subTa.value = cellHtml;
                subTa.dataset.gxcontent = cellHtml;
                subTa.dataset.blocks = JSON.stringify(GxEditor.getBlocksExclude(['grid2','grid2x2']));
                cell.appendChild(subTa);
                rowDiv.appendChild(cell);
                setTimeout((function(ta) { return function(){ if (typeof initShell === 'function') initShell(ta); else if (window.initShell) window.initShell(ta); }; })(subTa), 10);
            }
            content.appendChild(rowDiv);
        }
    } else if (block.type === 'card') {
        content.className = 'card gxb-content';
        content.style.background = 'transparent';
        if (block.hasHeader) {
            var h = document.createElement('div'); h.className = 'card-header'; h.contentEditable = 'true';
            h.innerHTML = block.header || 'Card Header';
            window.attachBlockEvents(state, block, h, wrapper, 'header');
            content.appendChild(h);
        }
        var bBody = document.createElement('div'); bBody.className = 'card-body';
        var subTa = document.createElement('textarea');
        subTa.className = 'editor gxb-nested-editor'; subTa.id = 'gxb-inner-' + block.id + '-content';
        subTa.style.display = 'none'; subTa.value = block.content || '';
        subTa.dataset.blocks = JSON.stringify(GxEditor.getBlocksExclude(['card']));
        bBody.appendChild(subTa);
        setTimeout((function(ta) { return function(){ if (typeof initShell === 'function') initShell(ta); else if (window.initShell) window.initShell(ta); }; })(subTa), 0);
        content.appendChild(bBody);
        if (block.hasFooter) {
            var f = document.createElement('div'); f.className = 'card-footer'; f.contentEditable = 'true';
            f.innerHTML = block.footer || 'Card Footer';
            window.attachBlockEvents(state, block, f, wrapper, 'footer');
            content.appendChild(f);
        }
    } else if (block.type === 'button') {
        content.style.textAlign = 'center';
        var btn = document.createElement('a'); btn.className = 'btn ' + (block.btnClass || 'btn-primary');
        btn.textContent = block.content || 'Button Text'; btn.contentEditable = 'true';
        content.addEventListener('input', function() { block.content = btn.textContent; });
        content.appendChild(btn);
    } else if (block.type === 'icon') {
        content.className = 'gxb-content gx-icon-block text-center p-3';
        var iPrv = document.createElement('i');
        iPrv.className = block.iconClass || 'bi bi-star';
        iPrv.style.cssText = 'font-size:'+(block.iconSize||'2.5rem')+'; color:'+(block.iconColor||'#6366f1')+'; cursor:pointer;';
        content.appendChild(iPrv);
        content.contentEditable = 'false';
        var triggerIconSettings = function(e) { e.preventDefault(); e.stopPropagation(); if (typeof openIconContext === 'function') openIconContext(e, state, block); else if (window.openIconContext) window.openIconContext(e, state, block); };
        content.addEventListener('contextmenu', triggerIconSettings);
        iPrv.addEventListener('contextmenu', triggerIconSettings);
    } else if (block.type === 'table') {
        content.className = 'gxb-content table-responsive';
        content.innerHTML = block.content || '<table class="table table-bordered"><tr><td>Cell</td></tr></table>';
        content.contentEditable = 'true';
        content.oncontextmenu = function(e){ 
            e.preventDefault(); e.stopPropagation(); 
            if (typeof openTableContext === 'function') openTableContext(e, state, block, e.target); 
            else if (window.openTableContext) window.openTableContext(e, state, block, e.target); 
        };
    } else if (block.type === 'icon_list') {
        content.className = 'gx-icon-list gxb-content';
        content.contentEditable = 'true';
        var icon = block.listIcon || 'bi bi-check2-circle';
        var color = block.listColor || '#6366f1';
        var items = (block.content || 'List Item').split('\n');
        content.innerHTML = items.map(function(it){ 
            return '<li class="d-flex align-items-start gap-2 mb-2"><i class="'+icon+'" style="color:'+color+'; margin-top:0.2rem;"></i><span>'+(it.replace(/<[^>]*>/g, '') || 'Item')+'</span></li>'; 
        }).join('') || '<li>Item</li>';
        
        content.oncontextmenu = function(e){ 
            e.preventDefault(); e.stopPropagation(); 
            if (typeof openIconListContext === 'function') openIconListContext(e, state, block); 
            else if (window.openIconListContext) window.openIconListContext(e, state, block); 
        };
    } else if (block.type === 'single_post') {
        content.style.cssText = 'background:#f0fdf4; border:2px dashed #bbf7d0; border-radius:6px; padding:15px; text-align:center;';
        content.innerHTML = '<strong>Post ID: ' + (block.content || '0') + '</strong>';
    } else if (block.type === 'toc') {
        content.style.cssText = 'background:#fafafa; border:1px solid #ddd; padding:10px; opacity:0.8;';
        content.innerHTML = '<strong>TOC: ' + (block.tocTitle || 'Daftar Isi') + '</strong>';
    } else if (block.type === 'recent_posts' || block.type === 'random_posts') {
        var isRecent = (block.type === 'recent_posts');
        content.style.cssText = 'background:#eff6ff; border:2px dashed #bfdbfe; border-radius:8px; padding:20px; text-align:center; color:#1d4ed8;';
        content.innerHTML = '<div class="mb-1"><i class="bi '+(isRecent ? 'bi-clock-history' : 'bi-shuffle')+' fs-3"></i></div>' +
                            '<div class="fw-bold">' + (isRecent ? 'Recent Posts' : 'Random Posts') + ' Block</div>' +
                            '<div class="small opacity-75">This list will be automatically populated on the public site</div>';
        content.contentEditable = 'false';
    } else {
        content.contentEditable = 'true';
        content.innerHTML = shortcodeToHtml(block.content) || '<br>';
    }

    // Actions area (RIGHT)
    var actions = document.createElement('div');
    actions.className = 'gxb-actions';
    actions.innerHTML =
        '<button type="button" title="Move up" class="gxb-move-up"><i class="bi bi-chevron-up"></i></button>' +
        '<button type="button" title="Move down" class="gxb-move-dn"><i class="bi bi-chevron-down"></i></button>' +
        '<button type="button" title="Delete block" class="gxb-del"><i class="bi bi-trash3"></i></button>';

    actions.querySelector('.gxb-move-up').onclick = function(){ window.moveBlock(state, block.id, -1); };
    actions.querySelector('.gxb-move-dn').onclick = function(){ window.moveBlock(state, block.id, 1); };
    actions.querySelector('.gxb-del').onclick = function(){ if(confirm('Delete block?')) window.deleteBlock(state, block.id); };

    // Apply TOC styling to wrapper if present
    if (block.type === 'toc') {
        if (block.tocFloat === 'float-start') wrapper.classList.add('float-start', 'me-2');
        else if (block.tocFloat === 'float-end') wrapper.classList.add('float-end', 'ms-2');
        if (block.tocWidth && block.tocWidth !== '100%') wrapper.style.width = block.tocWidth;
    }

    wrapper.appendChild(handleWrap);
    wrapper.appendChild(content);
    wrapper.appendChild(actions);

    window.attachBlockEvents(state, block, content, wrapper);
    if (typeof setupDrag === 'function') setupDrag(state, wrapper, block);
    else if (window.setupDrag) window.setupDrag(state, wrapper, block);
    
    return wrapper;
};

/**
 * Specialized renderer for Image Blocks
 */
window.renderImageBlock = function(state, block, content) {
    content.className = 'gxb-content gx-image-block';
    content.innerHTML = '';
    
    var hasImage = block.content && (block.content.startsWith('http') || block.content.startsWith('/') || block.content.startsWith('data:'));

    if (!hasImage) {
        var drop = document.createElement('div');
        drop.className = 'gxb-img-drop';
        drop.innerHTML = '<i class="bi bi-cloud-arrow-up"></i>Click to upload image<br><small>or paste URL below</small>';
        
        var urlRow = document.createElement('div');
        urlRow.style.cssText = 'display:flex; gap:6px; margin-top:8px;';
        var urlIn = document.createElement('input');
        urlIn.type = 'url'; urlIn.placeholder = 'Image URL...';
        urlIn.style.cssText = 'flex:1; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px; font-size:.8rem; outline:none;';
        var urlBtn = document.createElement('button');
        urlBtn.type = 'button'; urlBtn.textContent = 'Add';
        urlBtn.className = 'btn btn-primary btn-sm';
        urlRow.appendChild(urlIn);
        urlRow.appendChild(urlBtn);

        drop.onclick = function(e){
            if (e.target === urlIn || e.target === urlBtn) return;
            window.triggerImageReplace(state, block, content);
        };
        urlBtn.onclick = function(){ if(urlIn.value) { block.content = urlIn.value; window.renderImageBlock(state, block, content); } };
        
        content.appendChild(drop);
        content.appendChild(urlRow);
    } else {
        var wrap = document.createElement('div');
        wrap.className = 'gx-image-wrap ' + (block.imgAlign || '');
        if (block.imgAlign === 'mx-auto d-block') wrap.style.textAlign = 'center';
        
        var img = document.createElement('img');
        img.src = block.content;
        img.className = 'img-fluid ' + (block.imgWidth || '') + ' ' + (block.imgStyle || 'rounded');
        img.style.cursor = 'pointer';
        img.title = 'Click to replace, Right-click for properties';
        
        // RESTORE ORIGINAL BEHAVIOR: Left Click = Replace, Right Click = Context
        img.onclick = function(e){ e.stopPropagation(); window.triggerImageReplace(state, block, content); };
        img.oncontextmenu = function(e){ e.preventDefault(); e.stopPropagation(); if (typeof openImageContext === 'function') openImageContext(e, state, block, img); else if (window.openImageContext) window.openImageContext(e, state, block, img); };
        
        wrap.appendChild(img);
        
        var cap = document.createElement('div');
        cap.className = 'gxb-caption-text text-muted small mt-1';
        cap.contentEditable = 'true';
        cap.textContent = block.caption || '';
        cap.oninput = function(){ block.caption = cap.textContent; };
        wrap.appendChild(cap);
        content.appendChild(wrap);
    }
};

/**
 * Handle image file upload via AJAX/elFinder
 */
window.uploadImageFile = function(file, block, content, state) {
    if (!file) return;
    var base = (typeof GX_ELFINDER_URL !== 'undefined') ? GX_ELFINDER_URL : '';
    var sep  = base.indexOf('?') !== -1 ? '&' : '?';
    
    fetch(base + sep + 'cmd=open&init=1&target=', { credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(initData) {
        var fd = new FormData();
        fd.append('cmd', 'upload');
        fd.append('target', initData.cwd.hash);
        fd.append('upload[]', file);
        return fetch(base, { method: 'POST', body: fd, credentials: 'same-origin' });
    }).then(function(r) { return r.json(); }).then(function(parsed) {
        if (parsed.added && parsed.added.length > 0) {
            block.content = parsed.added[0].url;
            window.renderImageBlock(state, block, content);
            if (typeof serializeToTextarea === 'function') serializeToTextarea(state);
            else if (window.serializeToTextarea) window.serializeToTextarea(state);
        }
    });
};

window.refreshImageBlock = function(state, block) {
    var els = state.shell.querySelectorAll('.gxb-block[data-type="image"]');
    els.forEach(function(el) {
        if(el.dataset.blockId === block.id) {
            var content = el.querySelector('.gxb-content');
            window.renderImageBlock(state, block, content);
        }
    });
};

window.triggerImageReplace = function(state, block, content) {
    var fi = document.createElement('input');
    fi.type = 'file'; fi.accept = 'image/*';
    fi.onchange = function() {
        if (fi.files[0]) window.uploadImageFile(fi.files[0], block, content, state);
    };
    fi.click();
};

/**
 * Attaches standard listeners to a block's editable area
 */
window.attachBlockEvents = function(state, block, el, wrapper, field) {
    el.addEventListener('focus', function() {
        state.shell.querySelectorAll('.gxb-block').forEach(function(b){ b.classList.remove('gxb-border'); });
        wrapper.classList.add('gxb-border');
    });
    el.addEventListener('input', function() {
        if (field === 'header') block.header = el.innerHTML;
        else if (field === 'footer') block.footer = el.innerHTML;
        else block.content = (block.type === 'code') ? el.textContent : el.innerHTML;
        if (typeof serializeToTextarea === 'function') serializeToTextarea(state);
        else if (window.serializeToTextarea) window.serializeToTextarea(state);
    });
};

/**
 * Global drag-and-drop logic for reordering blocks
 */
window.setupDrag = function(state, wrapper, block) {
    wrapper.ondragstart = function(e){ wrapper.style.opacity='0.5'; e.dataTransfer.setData('blockId', block.id); };
    wrapper.ondragend = function(e){ wrapper.style.opacity='1'; };
    wrapper.ondragover = function(e){ e.preventDefault(); wrapper.classList.add('drag-over'); };
    wrapper.ondragleave = function(){ wrapper.classList.remove('drag-over'); };
    wrapper.ondrop = function(e){
        e.preventDefault(); wrapper.classList.remove('drag-over');
        var dragId = e.dataTransfer.getData('blockId');
        if (!dragId || dragId === block.id) return;
        var from = state.blocks.findIndex(function(b){return b.id === dragId; });
        var to = state.blocks.findIndex(function(b){return b.id === block.id; });
        if (from !== -1 && to !== -1) {
            var moved = state.blocks.splice(from, 1)[0];
            state.blocks.splice(to, 0, moved);
            window.renderAllBlocks(state);
        }
    };
};
