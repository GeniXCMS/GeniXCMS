/**
 * GxEditor UI Module
 * Handles Context Menus, Modals and UI positioning
 */

// UI State Variables (exposed globally for inter-module access)
window._pickerTargetShell = null;
window._pickerInsertAfter = null; // block id to insert after, null = end
window._activeShell = null;

window._activeImgBlock = null; window._activeImgState = null; window._activeImgEl = null;
window._activeGridBlock = null; window._activeGridState = null;
window._activeBtnBlock = null; window._activeBtnState = null; window._activeBtnEl = null;
window._activeCardBlock = null; window._activeCardState = null;
window._activePostBlock = null; window._activePostState = null;
window._activeTocBlock = null; window._activeTocState = null;
window._activeIconBlock = null; window._activeIconState = null;
window._activeTextEl = null; window._activeTextBlock = null; window._activeTextState = null;
window._activeTableBlock = null; window._activeTableState = null; window._activeTableEl = null;
window._activeIconListBlock = null; window._activeIconListState = null;

/**
 * Closes all open block context menus
 */
function closeAllContextMenus() {
    if (typeof closeImageContext === 'function') closeImageContext();
    if (typeof closeButtonContext === 'function') closeButtonContext();
    if (typeof closeCardContext === 'function') closeCardContext();
    if (typeof closeGridContext === 'function') closeGridContext();
    if (typeof closePostContext === 'function') closePostContext();
    if (typeof closeTocContext === 'function') closeTocContext();
    if (typeof closeIconContext === 'function') closeIconContext();
    if (typeof closeTextContext === 'function') closeTextContext();
    if (typeof closeTableContext === 'function') closeTableContext();
    if (typeof closeIconListContext === 'function') closeIconListContext();
    if (typeof closePicker === 'function') closePicker();
}

/**
 * Generic function to position and show a context menu
 */
function showContextMenu(ctx, e) {
    if (!ctx) return;
    ctx.style.display = 'block';
    ctx.style.position = 'fixed';
    ctx.style.zIndex = '9999999';
    
    var x = e.clientX;
    var y = e.clientY;
    ctx.style.left = x + 'px';
    ctx.style.top = y + 'px';

    var r = ctx.getBoundingClientRect();
    if (r.bottom > window.innerHeight) {
        ctx.style.top = (window.innerHeight - r.height - 10) + 'px';
    }
    if (r.right > window.innerWidth) {
        ctx.style.left = (window.innerWidth - r.width - 10) + 'px';
    }
}

// Inline Toolbar Positioning
var _inlineTbTimer = null;
function showInlineToolbar() {
    var sel = window.getSelection();
    if (!sel || sel.rangeCount === 0 || sel.isCollapsed) {
        hideInlineToolbar(); return;
    }
    var tb = document.getElementById('gxb-inline-toolbar');
    if (!tb) return;

    var range = sel.getRangeAt(0);
    var rects = range.getClientRects();
    if (rects.length === 0) { hideInlineToolbar(); return; }

    var rect = rects[0];
    if (rect.width === 0 || rect.height === 0) { hideInlineToolbar(); return; }

    var node = range.commonAncestorContainer;
    if (node.nodeType === 3) node = node.parentNode;
    var shell = node.closest('.gxb-shell');
    if (!shell) { hideInlineToolbar(); return; }

    var top = rect.top - 52;
    var left = rect.left + (rect.width / 2) - 100;
    if (top < 10) top = rect.bottom + 10;
    if (left < 10) left = 10;
    if (left + 220 > window.innerWidth) left = window.innerWidth - 230;

    tb.style.top = top + 'px';
    tb.style.left = left + 'px';
    tb.style.setProperty('display', 'flex', 'important');
    tb.style.setProperty('visibility', 'visible', 'important');
    tb.style.opacity = '1';
}

function hideInlineToolbar() {
    var tb = document.getElementById('gxb-inline-toolbar');
    if (tb) tb.style.setProperty('display', 'none', 'important');
}

document.addEventListener('selectionchange', function() {
    clearTimeout(_inlineTbTimer);
    _inlineTbTimer = setTimeout(showInlineToolbar, 60);
});

function openImageContext(e, state, block, el) {
    window._activeImgState = state; window._activeImgBlock = block; window._activeImgEl = el;
    var ctx = document.getElementById('gxb-img-context'); if (!ctx) return;
    var w = (block && block.imgWidth) ? block.imgWidth : '';
    var a = (block && block.imgAlign) ? block.imgAlign : '';
    var s = (block && block.imgStyle) ? block.imgStyle : 'rounded';
    var selW = document.getElementById('gxb-prop-width'); if(selW) selW.value = w;
    var selA = document.getElementById('gxb-prop-align'); if(selA) selA.value = a;
    var selS = document.getElementById('gxb-prop-style'); if(selS) selS.value = s;
    showContextMenu(ctx, e);
}
function closeImageContext() { var c=document.getElementById('gxb-img-context'); if(c)c.style.display='none'; window._activeImgBlock=null; window._activeImgState=null; window._activeImgEl=null; }

function openGridContext(e, state, block) {
    window._activeGridState = state; window._activeGridBlock = block;
    var ctx = document.getElementById('gxb-grid-context'); if (!ctx) return;
    var selC = document.getElementById('gxb-prop-grid-count'); if(selC) selC.value = block.colCount || 2;
    var selR = document.getElementById('gxb-prop-grid-rows'); if(selR) selR.value = block.rowCount || 1;
    var selRa = document.getElementById('gxb-prop-ratio'); if(selRa) selRa.value = block.colRatio || '6:6';
    var rWrap = document.getElementById('gxb-grid-ratio-wrap'); if(rWrap) rWrap.style.display = (block.colCount == 2) ? 'block' : 'none';
    showContextMenu(ctx, e);
}
function closeGridContext() { var c=document.getElementById('gxb-grid-context'); if(c)c.style.display='none'; window._activeGridBlock=null; window._activeGridState=null; }

function openButtonContext(e, state, block, el) {
    window._activeBtnState = state; window._activeBtnBlock = block; window._activeBtnEl = el;
    var ctx = document.getElementById('gxb-btn-context'); if (!ctx) return;
    var inpU = document.getElementById('gxb-prop-btn-url'); if(inpU) inpU.value = block.btnUrl || '';
    var selS = document.getElementById('gxb-prop-btn-class') || document.getElementById('gxb-prop-btn-style'); 
    if(selS) selS.value = block.btnClass || 'btn-primary';
    showContextMenu(ctx, e);
}
function closeButtonContext() { var c=document.getElementById('gxb-btn-context'); if(c)c.style.display='none'; window._activeBtnBlock=null; window._activeBtnState=null; window._activeBtnEl=null; }

function openCardContext(e, state, block) {
    window._activeCardState = state; window._activeCardBlock = block;
    var ctx = document.getElementById('gxb-card-context'); if (!ctx) return;
    var chkH = document.getElementById('gxb-prop-card-header'); if(chkH) chkH.checked = !!block.hasHeader;
    var chkF = document.getElementById('gxb-prop-card-footer'); if(chkF) chkF.checked = !!block.hasFooter;
    showContextMenu(ctx, e);
}
function closeCardContext() { var c=document.getElementById('gxb-card-context'); if(c)c.style.display='none'; window._activeCardBlock=null; window._activeCardState=null; }

function openPostContext(e, state, block) {
    window._activePostState = state; window._activePostBlock = block;
    var ctx = document.getElementById('gxb-post-context'); if (!ctx) return;
    var inpP = document.getElementById('gxb-prop-post-id'); if(inpP) inpP.value = block.postId || block.content || '';
    showContextMenu(ctx, e);
}
function closePostContext() { var c=document.getElementById('gxb-post-context'); if(c)c.style.display='none'; window._activePostBlock=null; window._activePostState=null; }

function openTocContext(e, state, block) {
    window._activeTocState = state; window._activeTocBlock = block;
    var ctx = document.getElementById('gxb-toc-context'); if (!ctx) return;
    var inpT = document.getElementById('gxb-prop-toc-title'); if(inpT) inpT.value = block.tocTitle || 'Daftar Isi';
    var selF = document.getElementById('gxb-prop-toc-float'); if(selF) selF.value = block.tocFloat || 'none';
    var inpW = document.getElementById('gxb-prop-toc-width'); if(inpW) inpW.value = block.tocWidth || '450px';
    var selC = document.getElementById('gxb-prop-toc-collapse'); if(selC) selC.value = block.tocCollapse || 'no';
    showContextMenu(ctx, e);
}
function closeTocContext() { var c=document.getElementById('gxb-toc-context'); if(c)c.style.display='none'; window._activeTocBlock=null; window._activeTocState=null; }

function openIconContext(e, state, block) {
    window._activeIconState = state; window._activeIconBlock = block;
    var ctx = document.getElementById('gxb-icon-context'); if (!ctx) return;
    var inpC = document.getElementById('gxb-prop-icon-class'); if(inpC) inpC.value = block.iconClass || 'bi bi-star';
    var inpS = document.getElementById('gxb-prop-icon-size');  if(inpS) inpS.value  = block.iconSize  || '2.5rem';
    var inpCl = document.getElementById('gxb-prop-icon-color'); if(inpCl) inpCl.value = (block.iconColor && block.iconColor.indexOf('#') === 0) ? block.iconColor : '#6366f1';
    showContextMenu(ctx, e);
}
function closeIconContext() { var c=document.getElementById('gxb-icon-context'); if(c)c.style.display='none'; window._activeIconBlock=null; window._activeIconState=null; }

function openTextContext(e, state, block, el) {
    window._activeTextState = state; window._activeTextBlock = block; window._activeTextEl = el;
    var ctx = document.getElementById('gxb-text-context'); if (!ctx) return;
    var selA = document.getElementById('gxb-prop-text-align'); if(selA) selA.value = (block && block.textAlign) ? block.textAlign : '';
    var selL = document.getElementById('gxb-prop-text-lineheight'); if(selL) selL.value = (block && block.lineHeight) ? block.lineHeight : '';
    showContextMenu(ctx, e);
}
function closeTextContext() { var c=document.getElementById('gxb-text-context'); if(c)c.style.display='none'; window._activeTextBlock=null; window._activeTextState=null; window._activeTextEl=null; }

function openTableContext(e, state, block, el) {
    window._activeTableState = state; window._activeTableBlock = block; window._activeTableEl = el;
    var ctx = document.getElementById('gxb-table-context'); if (!ctx) return;
    var selB = document.getElementById('gxb-prop-table-border'); if(selB) selB.value = block.tableBorder || 'yes';
    var selSt = document.getElementById('gxb-prop-table-striped'); if(selSt) selSt.value = block.tableStriped || 'no';
    showContextMenu(ctx, e);
}
function closeTableContext() { var c=document.getElementById('gxb-table-context'); if(c)c.style.display='none'; window._activeTableBlock=null; window._activeTableState=null; window._activeTableEl=null; }

function openIconListContext(e, state, block) {
    window._activeIconListState = state; window._activeIconListBlock = block;
    var ctx = document.getElementById('gxb-iconlist-context'); if (!ctx) return;
    var inpI = document.getElementById('gxb-prop-iconlist-class'); if(inpI) inpI.value = block.listIcon || 'bi bi-check2-circle';
    var inpC = document.getElementById('gxb-prop-iconlist-color'); if(inpC) inpC.value = block.listColor || '#6366f1';
    showContextMenu(ctx, e);
}
function closeIconListContext() { var c=document.getElementById('gxb-iconlist-context'); if(c)c.style.display='none'; window._activeIconListBlock=null; window._activeIconListState=null; }

/**
 * Picker UI Handlers
 */
window.openPickerBefore = function(state, beforeId, pos) {
    // Re-use openPicker but mark it as a 'before' operation
    window._pickerInsertBefore = beforeId;
    window.openPicker(state, beforeId, pos);
};

window.openPicker = function(state, targetId, pos) {
    window._pickerTargetState = state;
    // We use window._pickerInsertBefore to know if we are doing a 'before' or 'after' operation
    var isBefore = (window._pickerInsertBefore === targetId);
    
    var p = document.getElementById('gxb-picker'); if(!p) return;
    
    // 1. Show and Populate
    p.style.display = 'block';
    p.style.visibility = 'hidden'; 
    
    var list = document.getElementById('gxb-picker-list');
    if (list) {
        list.innerHTML = '';
        var blocks = window.GX_EDITOR_BLOCKS || [];
        
        // Categorize blocks
        var cats = {};
        blocks.forEach(function(b) {
            var c = b.cat || 'General';
            if (!cats[c]) cats[c] = [];
            cats[c].push(b);
        });

        // Preferred category order (if present)
        var order = ['Basic', 'Layout', 'Standard Sections'];
        var sortedCats = Object.keys(cats).sort(function(a, b) {
            var ia = order.indexOf(a); var ib = order.indexOf(b);
            if (ia !== -1 && ib !== -1) return ia - ib;
            if (ia !== -1) return -1; if (ib !== -1) return 1;
            return a.localeCompare(b);
        });

        sortedCats.forEach(function(catName) {
            // Add Category Header
            var head = document.createElement('div');
            head.className = 'gxb-picker-cat-header';
            head.textContent = catName;
            list.appendChild(head);

            cats[catName].forEach(function(bDefinition) {
                var item = document.createElement('div');
                item.className = 'gxb-picker-item';
                item.dataset.type = bDefinition.type; // for filtering
                item.innerHTML = '<div class="gxb-picker-icon"><i class="'+(bDefinition.icon||'bi bi-box')+'"></i></div>' +
                                 '<div><div class="gxb-picker-label">'+bDefinition.label+'</div><div class="gxb-picker-desc">'+(bDefinition.desc||'')+'</div></div>';
                
                // Use mousedown instead of click to prevent issues with document.mousedown clearing state
                item.onmousedown = function(e) {
                    e.preventDefault(); e.stopPropagation();
                    if (window.addBlock) {
                        var newB;
                        if (isBefore && targetId) {
                            // Insert BEFORE specific ID
                            var idx = state.blocks.findIndex(function(blk){ return blk.id === targetId; });
                            newB = { id: 'blk-' + Math.random().toString(36).slice(2,10), type: bDefinition.type, content: '' };
                            if (idx !== -1) state.blocks.splice(idx, 0, newB);
                            else state.blocks.push(newB);
                        } else {
                            // Regular add (at end or afterId)
                            newB = window.addBlock(state, bDefinition.type, '', targetId);
                        }
                        
                        if (window.renderAllBlocks) window.renderAllBlocks(state);
                        
                        // Focus newly created block
                        setTimeout(function() {
                            var el = state.shell.querySelector('.gxb-block[data-block-id="'+newB.id+'"] .gxb-content');
                            if (el) { el.focus(); el.scrollIntoView({ behavior: "smooth", block: "center" }); }
                        }, 50);
                    }
                    window.closePicker();
                };
                list.appendChild(item);
            });
        });
    }

    // 2. Position
    var rPos = (pos ? pos.top : 100);
    var lPos = (pos ? pos.left : 100);
    p.style.top = rPos + 'px';
    p.style.left = lPos + 'px';
    p.style.visibility = 'visible';

    var rect = p.getBoundingClientRect();
    if (rect.bottom > window.innerHeight) {
        var flippedTop = rPos - rect.height - 10;
        p.style.top = (flippedTop > 10 ? flippedTop : (window.innerHeight - rect.height - 15)) + 'px';
    }
    if (parseFloat(p.style.top) < 10) p.style.top = '10px';
    if (rect.right > window.innerWidth) p.style.left = (window.innerWidth - rect.width - 20) + 'px';
    
    var search = document.getElementById('gxb-picker-search');
    if (search) { search.value = ''; search.focus(); }
};

window.closePicker = function() {
    var p = document.getElementById('gxb-picker');
    if (p) p.style.display = 'none';
    window._pickerTargetState = null;
    window._pickerInsertBefore = null;
};

window.filterPicker = function(query) {
    var list = document.getElementById('gxb-picker-list');
    if (!list) return;
    
    var q = query.toLowerCase();
    var headers = list.querySelectorAll('.gxb-picker-cat-header');
    
    headers.forEach(function(h) {
        var next = h.nextElementSibling;
        var hasVisible = false;
        while (next && !next.classList.contains('gxb-picker-cat-header')) {
            var txt = next.textContent.toLowerCase();
            if (txt.indexOf(q) !== -1) {
                next.style.display = 'flex';
                hasVisible = true;
            } else {
                next.style.display = 'none';
            }
            next = next.nextElementSibling;
        }
        h.style.display = hasVisible ? 'block' : 'none';
    });
};


/**
 * Global UI Initialization - Run once per page
 */
window.initGlobalUI = function() {
    // 1. Image context listeners
    ['width', 'align', 'style'].forEach(function(prop) {
        var sel = document.getElementById('gxb-prop-' + prop);
        if (sel) {
            sel.addEventListener('change', function() {
                if (window._activeImgBlock) {
                    if (prop === 'width') window._activeImgBlock.imgWidth = this.value;
                    if (prop === 'align') window._activeImgBlock.imgAlign = this.value;
                    if (prop === 'style') window._activeImgBlock.imgStyle = this.value;
                    window.refreshImageBlock(window._activeImgState, window._activeImgBlock);
                    window.serializeToTextarea(window._activeImgState);
                }
            });
        }
    });
    var replBtn = document.getElementById('gxb-btn-img-replace');
    if (replBtn) {
        replBtn.addEventListener('click', function() {
            if (window._activeImgBlock) {
                if (window.triggerImageReplace) window.triggerImageReplace(window._activeImgState, window._activeImgBlock, window._activeImgEl ? window._activeImgEl.closest('.gxb-content') : null);
            }
        });
    }

    // 2. Post Save
    var postSave = document.getElementById('gxb-post-save');
    if (postSave) {
        postSave.addEventListener('click', function() {
            if (window._activePostBlock) {
                window._activePostBlock.postId = document.getElementById('gxb-prop-post-id').value;
                window.renderAllBlocks(window._activePostState);
            }
            closePostContext();
        });
    }

    // 3. Delete Block buttons
    var deleteHanders = [
        { btnId: 'gxb-img-delete',  getState: function(){return window._activeImgState;},  getBlock: function(){return window._activeImgBlock;} },
        { btnId: 'gxb-btn-delete',  getState: function(){return window._activeBtnState;},  getBlock: function(){return window._activeBtnBlock;} },
        { btnId: 'gxb-card-delete', getState: function(){return window._activeCardState;}, getBlock: function(){return window._activeCardBlock;} },
        { btnId: 'gxb-grid-delete', getState: function(){return window._activeGridState;}, getBlock: function(){return window._activeGridBlock;} },
        { btnId: 'gxb-icon-delete', getState: function(){return window._activeIconState;}, getBlock: function(){return window._activeIconBlock;} },
        { btnId: 'gxb-post-delete', getState: function(){return window._activePostState;}, getBlock: function(){return window._activePostBlock;} },
        { btnId: 'gxb-toc-delete',  getState: function(){return window._activeTocState;},  getBlock: function(){return window._activeTocBlock;} },
        { btnId: 'gxb-text-delete', getState: function(){return window._activeTextState;}, getBlock: function(){return window._activeTextBlock;} },
        { btnId: 'gxb-table-delete', getState: function(){return window._activeTableState;}, getBlock: function(){return window._activeTableBlock;} },
        { btnId: 'gxb-iconlist-delete', getState: function(){return window._activeIconListState;}, getBlock: function(){return window._activeIconListBlock;} }
    ];
    deleteHanders.forEach(function(item) {
        var btn = document.getElementById(item.btnId);
        if (btn) {
            btn.addEventListener('click', function() {
                if (confirm('Delete this block?')) {
                    var s = item.getState(); var b = item.getBlock();
                    if (s && b) window.deleteBlock(s, b.id);
                    closeAllContextMenus();
                }
            });
        }
    });

    // 4. Grid Save
    var gridSave = document.getElementById('gxb-grid-save');
    if (gridSave) {
        gridSave.addEventListener('click', function() {
            if (window._activeGridBlock) {
                window._activeGridBlock.colCount = parseInt(document.getElementById('gxb-prop-grid-count').value);
                window._activeGridBlock.rowCount = parseInt(document.getElementById('gxb-prop-grid-rows').value);
                window._activeGridBlock.colRatio = document.getElementById('gxb-prop-ratio').value;
                window.renderAllBlocks(window._activeGridState);
                window.serializeToTextarea(window._activeGridState);
            }
            closeGridContext();
        });
    }

    // 5. TOC Save
    var tocSave = document.getElementById('gxb-toc-save');
    if (tocSave) {
        tocSave.addEventListener('click', function() {
            if (window._activeTocBlock) {
                window._activeTocBlock.tocTitle = document.getElementById('gxb-prop-toc-title').value;
                window._activeTocBlock.tocFloat = document.getElementById('gxb-prop-toc-float').value;
                window._activeTocBlock.tocWidth = document.getElementById('gxb-prop-toc-width').value;
                window._activeTocBlock.tocCollapse = document.getElementById('gxb-prop-toc-collapse').value;
                window.renderAllBlocks(window._activeTocState);
                window.serializeToTextarea(window._activeTocState);
            }
            closeTocContext();
        });
    }

    // 6. Table Actions
    var tableAddRow = document.getElementById('gxb-table-add-row');
    if (tableAddRow) {
        tableAddRow.onclick = function() {
            var b = window._activeTableBlock; var s = window._activeTableState;
            if (b && s) {
                var div = document.createElement('div'); div.innerHTML = b.content;
                var table = div.querySelector('table');
                if (table) {
                    var row = table.insertRow();
                    var cols = table.rows[0] ? table.rows[0].cells.length : 2;
                    for (var i=0; i<cols; i++) row.insertCell().innerHTML = 'New Cell';
                    b.content = table.innerHTML;
                    window.renderAllBlocks(s);
                    window.serializeToTextarea(s);
                }
            }
            closeAllContextMenus();
        };
    }
    var tableAddCol = document.getElementById('gxb-table-add-col');
    if (tableAddCol) {
        tableAddCol.onclick = function() {
            var b = window._activeTableBlock; var s = window._activeTableState;
            if (b && s) {
                var div = document.createElement('div'); div.innerHTML = b.content;
                var table = div.querySelector('table');
                if (table) {
                    for (var i=0; i<table.rows.length; i++) table.rows[i].insertCell().innerHTML = 'New';
                    b.content = table.innerHTML;
                    window.renderAllBlocks(s);
                    window.serializeToTextarea(s);
                }
            }
            closeAllContextMenus();
        };
    }

    // 7. IconList Save
    var ilSave = document.getElementById('gxb-iconlist-save');
    if (ilSave) {
        ilSave.onclick = function() {
            if (window._activeIconListBlock) {
                window._activeIconListBlock.listIcon = document.getElementById('gxb-prop-iconlist-class').value;
                window._activeIconListBlock.listColor = document.getElementById('gxb-prop-iconlist-color').value;
                window.renderAllBlocks(window._activeIconListState);
                window.serializeToTextarea(window._activeIconListState);
            }
            closeAllContextMenus();
        };
    }

    // 8. Inline toolbar buttons
    var tb = document.getElementById('gxb-inline-toolbar');
    if (tb) {
        tb.querySelectorAll('[data-cmd]').forEach(function(btn) {
            btn.addEventListener('mousedown', function(e) {
                e.preventDefault();
                var cmd = btn.dataset.cmd;
                if (cmd === 'createLink') {
                    var url = prompt('Enter URL:');
                    if (url) document.execCommand('createLink', false, url);
                } else if (cmd === 'insertImageGX') {
                    if (typeof window.elfinderDialog === 'function') {
                        var sel = window.getSelection();
                        var range = (sel.rangeCount > 0) ? sel.getRangeAt(0).cloneRange() : null;
                        var fakeContext = {
                            invoke: function (dummy, url) {
                                if (range) {
                                    var s = window.getSelection();
                                    s.removeAllRanges();
                                    s.addRange(range);
                                }
                                document.execCommand('insertImage', false, url);
                                // Trigger sync
                                var node = range ? range.commonAncestorContainer : null;
                                if (node && node.nodeType === 3) node = node.parentNode;
                                var contentEl = node ? node.closest('.gxb-content') : null;
                                if (contentEl) {
                                    contentEl.dispatchEvent(new Event('input', { bubbles: true }));
                                }
                            }
                        };
                        window.elfinderDialog(fakeContext);
                    }
                } else if (['icon_list', 'table', 'grid2', 'toc'].indexOf(cmd) !== -1) {
                    var shell = btn.closest('.gxb-shell') || document.querySelector('.gxb-shell.gxb-selected');
                    var st = window.GxEditor._editors.find(function (ed) { return ed.shell === shell; });
                    if (st && window.addBlock) {
                        window.addBlock(st, cmd, '', null);
                        window.renderAllBlocks(st);
                    }
                } else if (['h1', 'h2', 'h3', 'paragraph'].indexOf(cmd) !== -1) {
                    var tag = cmd === 'paragraph' ? '<p>' : '<' + cmd.toUpperCase() + '>';
                    document.execCommand('formatBlock', false, tag);
                } else {
                    document.execCommand(cmd, false, null);
                }
                setTimeout(hideInlineToolbar, 100);
            });
        });
    }

    // 7. Picker Search
    var pickerSearch = document.getElementById('gxb-picker-search');
    if (pickerSearch) {
        pickerSearch.addEventListener('input', function() {
            if (window.filterPicker) window.filterPicker(this.value);
        });
        pickerSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                var first = document.querySelector('.gxb-picker-item[style*="display: flex"], .gxb-picker-item:not([style*="display: none"])');
                if (first) first.click();
            }
        });
    }
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideInlineToolbar();
        if (typeof closePicker === 'function') closePicker();
        closeAllContextMenus();
    }
});

/**
 * Common event listeners for Context Menus
 */
document.addEventListener('contextmenu', function(e) {
    var targetEl = e.target;
    if (targetEl.nodeType === 3) targetEl = targetEl.parentElement;
    var shell = targetEl.closest('.gxb-shell');
    if (!shell) return;
    
    closeAllContextMenus();
    var state = window.GxEditor._editors.find(function(ed){ return ed.shell === shell; });
    if (!state) return;

    var blockEl = targetEl.closest('.gxb-block');
    if (!blockEl) return;
    var block = state.blocks.find(function(b){ return b.id === blockEl.dataset.blockId; });
    if (!block) return;
    
    state.shell.querySelectorAll('.gxb-block').forEach(function(el){ el.classList.remove('gxb-selected'); });
    blockEl.classList.add('gxb-selected');

    if (targetEl.tagName === 'IMG' || (targetEl.tagName === 'I' && block.type === 'icon')) {
        e.preventDefault(); e.stopPropagation();
        if (targetEl.tagName === 'IMG') openImageContext(e, state, block, targetEl);
        else openIconContext(e, state, block);
        return;
    }

    var btn = targetEl.closest('.btn');
    if (btn && blockEl.contains(btn)) {
        e.preventDefault(); e.stopPropagation();
        openButtonContext(e, state, block, btn);
        return;
    }

    e.preventDefault(); e.stopPropagation();
    if (block.type === 'grid2' || block.type === 'grid2x2') openGridContext(e, state, block);
    else if (block.type === 'card') openCardContext(e, state, block);
    else if (block.type === 'single_post') openPostContext(e, state, block);
    else if (block.type === 'toc') openTocContext(e, state, block);
    else if (block.type === 'icon_list') openIconListContext(e, state, block);
    else if (block.type === 'table') openTableContext(e, state, block, targetEl);
    else openTextContext(e, state, block, targetEl);
});

document.addEventListener('mousedown', function(e) {
    if (e.button !== 2 && !e.target.closest('.gxb-context-menu') && !e.target.closest('[id$="-context"]')) {
        closeAllContextMenus();
    }
    if (!e.target.closest('#gxb-picker') && !e.target.closest('.gxb-add-inline') && !e.target.closest('.gxb-addbtn')) {
        if (typeof closePicker === 'function') closePicker();
    }
});
