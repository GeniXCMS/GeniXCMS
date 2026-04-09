(function(window) {
    'use strict';

    // ── GxEditor Global API ───────────────────────────────────────────
    var GxEditor = {
        _blocks: {},
        registerBlock: function(id, config) {
            this._blocks[id] = Object.assign({
                icon: 'bi bi-box',
                label: id,
                desc: '',
                placeholder: '',
                render: null, 
                serialize: null,
                parse: null
            }, config);
        }
    };
    window.GxEditor = GxEditor;
    window.closeAllContextMenus = closeAllContextMenus;

    // ── Core Block Definitions ────────────────────────────────────────
    var CORE_BLOCKS = [
        { id: 'paragraph', icon: 'bi bi-paragraph',    label: 'Paragraph',    desc: 'Plain text',           placeholder: 'Start typing...' },
        { id: 'h1',        icon: 'bi bi-type-h1',      label: 'Heading 1',    desc: 'Large title',          placeholder: 'Heading 1' },
        { id: 'h2',        icon: 'bi bi-type-h2',      label: 'Heading 2',    desc: 'Medium heading',       placeholder: 'Heading 2' },
        { id: 'h3',        icon: 'bi bi-type-h3',      label: 'Heading 3',    desc: 'Small heading',        placeholder: 'Heading 3' },
        { id: 'quote',     icon: 'bi bi-quote',        label: 'Quote',        desc: 'Blockquote',           placeholder: 'Enter quote text...' },
        { id: 'code',      icon: 'bi bi-code-slash',   label: 'Code',         desc: 'Code block',           placeholder: '// Enter code here...' },
        { id: 'ul',        icon: 'bi bi-list-ul',      label: 'Bullet List',  desc: 'Unordered list',       placeholder: 'List item' },
        { id: 'ol',        icon: 'bi bi-list-ol',      label: 'Numbered List',desc: 'Ordered list',         placeholder: 'List item' },
        { id: 'image',     icon: 'bi bi-image',        label: 'Image',        desc: 'Upload or embed',      placeholder: '' },
        { id: 'button',    icon: 'bi bi-hand-index',   label: 'Button',       desc: 'Interactive button',   placeholder: 'Button Text' },
        { id: 'grid2',     icon: 'bi bi-layout-split', label: '2 Columns',    desc: 'Side-by-side grid',    placeholder: '' },
        { id: 'grid2x2',   icon: 'bi bi-grid-fill',    label: '2x2 Grid',     desc: '4 cells layout',       placeholder: '' },
        { id: 'card',      icon: 'bi bi-card-text',    label: 'Card',         desc: 'Boxed content container', placeholder: 'Card body...' },
        { id: 'icon',      icon: 'bi bi-star',         label: 'Icon',         desc: 'Bootstrap/FA icon',    placeholder: '' },
        { id: 'divider',   icon: 'bi bi-dash-lg',      label: 'Divider',      desc: 'Horizontal separator', placeholder: '' },
        { id: 'single_post', icon: 'bi bi-card-checklist',  label: 'Single Post', desc: 'Display a specific post', placeholder: '' },
        { id: 'toc',         icon: 'bi bi-list-nested',    label: 'Table of Contents', desc: 'Auto-generate from H1-H4', placeholder: '' },
        { id: 'icon_list',   icon: 'bi bi-check2-square',  label: 'Icon List',    desc: 'List with custom icons', placeholder: 'List item' },
        { id: 'table',       icon: 'bi bi-table',          label: 'Table',        desc: 'Data grid table',        placeholder: '' },
        { id: 'recent_posts', icon: 'bi bi-clock-history', label: 'Recent Posts', desc: 'Dynamic recent list', placeholder: '' },
        { id: 'random_posts', icon: 'bi bi-shuffle',       label: 'Random Posts', desc: 'Dynamic random list', placeholder: '' },
    ];

    CORE_BLOCKS.forEach(function(b) { GxEditor.registerBlock(b.id, b); });

    var editors = []; // array of {textarea, blocks:[{id,type,content,caption}]}
    var _pickerTargetShell = null;
    var _pickerInsertAfter = null; // block id to insert after, null = end
    var _activeShell = null;

    // ── Global Context Menu States ────────────────────────────────────
    var _activeImgBlock = null; var _activeImgState = null; var _activeImgEl = null;
    var _activeGridBlock = null; var _activeGridState = null;
    var _activeBtnBlock = null; var _activeBtnState = null; var _activeBtnEl = null;
    var _activeCardBlock = null; var _activeCardState = null;
    var _activePostBlock = null; var _activePostState = null;
    var _activeTocBlock = null; var _activeTocState = null;
    var _activeIconBlock = null; var _activeIconState = null;
    var _activeTextEl = null; var _activeTextBlock = null; var _activeTextState = null;
    var _activeTableBlock = null; var _activeTableState = null; var _activeTableEl = null;
    var _activeIconListBlock = null; var _activeIconListState = null;

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
        closePicker();
    }

    function showContextMenu(ctx, e) {
        if (!ctx) return;
        ctx.style.display = 'block';
        ctx.style.position = 'fixed';
        ctx.style.zIndex = '9999999';
        
        // Initial position
        var x = e.clientX;
        var y = e.clientY;
        ctx.style.left = x + 'px';
        ctx.style.top = y + 'px';

        // Viewport check
        var r = ctx.getBoundingClientRect();
        if (r.bottom > window.innerHeight) {
            ctx.style.top = (window.innerHeight - r.height - 10) + 'px';
        }
        if (r.right > window.innerWidth) {
            ctx.style.left = (window.innerWidth - r.width - 10) + 'px';
        }
    }

    // ── Image Context ──
    function openImageContext(e, state, block, el) {
        _activeImgState = state; _activeImgBlock = block; _activeImgEl = el;
        var ctx = document.getElementById('gxb-img-context'); if (!ctx) return;
        var w = (block && block.imgWidth) ? block.imgWidth : '';
        var a = (block && block.imgAlign) ? block.imgAlign : '';
        var s = (block && block.imgStyle) ? block.imgStyle : 'rounded';
        
        var selW = document.getElementById('gxb-prop-width'); if(selW) selW.value = w;
        var selA = document.getElementById('gxb-prop-align'); if(selA) selA.value = a;
        var selS = document.getElementById('gxb-prop-style'); if(selS) selS.value = s;
        
        showContextMenu(ctx, e);
    }
    function closeImageContext() { var c=document.getElementById('gxb-img-context'); if(c)c.style.display='none'; _activeImgBlock=null; _activeImgState=null; _activeImgEl=null; }

    // ── Grid Context ──
    function openGridContext(e, state, block) {
        _activeGridState = state; _activeGridBlock = block;
        var ctx = document.getElementById('gxb-grid-context'); if (!ctx) return;
        var selC = document.getElementById('gxb-prop-grid-count'); if(selC) selC.value = block.colCount || 2;
        var selR = document.getElementById('gxb-prop-grid-rows'); if(selR) selR.value = block.rowCount || 1;
        var selRa = document.getElementById('gxb-prop-ratio'); if(selRa) selRa.value = block.colRatio || '6:6';
        var rWrap = document.getElementById('gxb-grid-ratio-wrap'); if(rWrap) rWrap.style.display = (block.colCount == 2) ? 'block' : 'none';
        
        showContextMenu(ctx, e);
    }
    function closeGridContext() { var c=document.getElementById('gxb-grid-context'); if(c)c.style.display='none'; _activeGridBlock=null; _activeGridState=null; }

    // ── Button Context ──
    function openButtonContext(e, state, block, el) {
        _activeBtnState = state; _activeBtnBlock = block; _activeBtnEl = el;
        var ctx = document.getElementById('gxb-btn-context'); if (!ctx) return;
        var inpU = document.getElementById('gxb-prop-btn-url'); if(inpU) inpU.value = block.btnUrl || '';
        var selS = document.getElementById('gxb-prop-btn-class') || document.getElementById('gxb-prop-btn-style'); 
        if(selS) selS.value = block.btnClass || 'btn-primary';
        
        showContextMenu(ctx, e);
    }
    function closeButtonContext() { var c=document.getElementById('gxb-btn-context'); if(c)c.style.display='none'; _activeBtnBlock=null; _activeBtnState=null; _activeBtnEl=null; }

    // ── Card Context ──
    function openCardContext(e, state, block) {
        _activeCardState = state; _activeCardBlock = block;
        var ctx = document.getElementById('gxb-card-context'); if (!ctx) return;
        var chkH = document.getElementById('gxb-prop-card-header'); if(chkH) chkH.checked = !!block.hasHeader;
        var chkF = document.getElementById('gxb-prop-card-footer'); if(chkF) chkF.checked = !!block.hasFooter;
        
        showContextMenu(ctx, e);
    }
    function closeCardContext() { var c=document.getElementById('gxb-card-context'); if(c)c.style.display='none'; _activeCardBlock=null; _activeCardState=null; }

    // ── Post Context ──
    function openPostContext(e, state, block) {
        _activePostState = state; _activePostBlock = block;
        var ctx = document.getElementById('gxb-post-context'); if (!ctx) return;
        var inpP = document.getElementById('gxb-prop-post-id'); if(inpP) inpP.value = block.postId || block.content || '';
        
        showContextMenu(ctx, e);
    }
    function closePostContext() { var c=document.getElementById('gxb-post-context'); if(c)c.style.display='none'; _activePostBlock=null; _activePostState=null; }

    // ── TOC Context ──
    function openTocContext(e, state, block) {
        _activeTocState = state; _activeTocBlock = block;
        var ctx = document.getElementById('gxb-toc-context'); if (!ctx) return;
        var inpT = document.getElementById('gxb-prop-toc-title'); if(inpT) inpT.value = block.tocTitle || 'Daftar Isi';
        var selF = document.getElementById('gxb-prop-toc-float'); if(selF) selF.value = block.tocFloat || 'none';
        var inpW = document.getElementById('gxb-prop-toc-width'); if(inpW) inpW.value = block.tocWidth || '450px';
        var selC = document.getElementById('gxb-prop-toc-collapse'); if(selC) selC.value = block.tocCollapse || 'no';
        
        showContextMenu(ctx, e);
    }
    function closeTocContext() { var c=document.getElementById('gxb-toc-context'); if(c)c.style.display='none'; _activeTocBlock=null; _activeTocState=null; }

    // ── Icon Context ──
    function openIconContext(e, state, block) {
        _activeIconState = state; _activeIconBlock = block;
        var ctx = document.getElementById('gxb-icon-context'); if (!ctx) return;
        var inpC = document.getElementById('gxb-prop-icon-class'); if(inpC) inpC.value = block.iconClass || 'bi bi-star';
        var inpS = document.getElementById('gxb-prop-icon-size');  if(inpS) inpS.value  = block.iconSize  || '2.5rem';
        var inpCl = document.getElementById('gxb-prop-icon-color'); if(inpCl) inpCl.value = (block.iconColor && block.iconColor.indexOf('#') === 0) ? block.iconColor : '#6366f1';
        
        showContextMenu(ctx, e);
    }
    function closeIconContext() { var c=document.getElementById('gxb-icon-context'); if(c)c.style.display='none'; _activeIconBlock=null; _activeIconState=null; }

    // ── Text Context ──
    function openTextContext(e, state, block, el) {
        _activeTextState = state; _activeTextBlock = block; _activeTextEl = el;
        var ctx = document.getElementById('gxb-text-context'); if (!ctx) return;
        var selA = document.getElementById('gxb-prop-text-align'); if(selA) selA.value = (block && block.textAlign) ? block.textAlign : '';
        var selL = document.getElementById('gxb-prop-text-lineheight'); if(selL) selL.value = (block && block.lineHeight) ? block.lineHeight : '';
        
        showContextMenu(ctx, e);
    }
    function closeTextContext() { var c=document.getElementById('gxb-text-context'); if(c)c.style.display='none'; _activeTextBlock=null; _activeTextState=null; _activeTextEl=null; }

    // ── Table Context ──
    function openTableContext(e, state, block, el) {
        _activeTableState = state; _activeTableBlock = block; _activeTableEl = el;
        var ctx = document.getElementById('gxb-table-context'); if (!ctx) return;
        var selB = document.getElementById('gxb-prop-table-border'); if(selB) selB.value = block.tableBorder || 'yes';
        var selSt = document.getElementById('gxb-prop-table-striped'); if(selSt) selSt.value = block.tableStriped || 'no';
        
        showContextMenu(ctx, e);
    }
    function closeTableContext() { var c=document.getElementById('gxb-table-context'); if(c)c.style.display='none'; _activeTableBlock=null; _activeTableState=null; _activeTableEl=null; }

    // ── Icon List Context ──
    function openIconListContext(e, state, block) {
        _activeIconListState = state; _activeIconListBlock = block;
        var ctx = document.getElementById('gxb-iconlist-context'); if (!ctx) return;
        var inpI = document.getElementById('gxb-prop-iconlist-class'); if(inpI) inpI.value = block.listIcon || 'bi bi-check2-circle';
        var inpC = document.getElementById('gxb-prop-iconlist-color'); if(inpC) inpC.value = block.listColor || '#6366f1';
        
        showContextMenu(ctx, e);
    }
    function closeIconListContext() { var c=document.getElementById('gxb-iconlist-context'); if(c)c.style.display='none'; _activeIconListBlock=null; _activeIconListState=null; }


    // ── Boot all textarea.editor ───────────────────────────────────────
    var _uiInitialized = false;
    function boot() {
        if (!_uiInitialized) {
            initGlobalUI();
            _uiInitialized = true;
        }

        document.querySelectorAll('textarea.editor').forEach(function(ta) {
            // Prevent double initialization
            if (ta.dataset.gxbInit) return;
            ta.dataset.gxbInit = '1';
            initShell(ta);
        });
        document.querySelectorAll('form').forEach(function(form) {
            if (form.dataset.gxbInit) return;
            form.dataset.gxbInit = '1';
            form.addEventListener('submit', function() {
                editors.forEach(function(e) { serializeToTextarea(e); });
            });
        });

        // Periodic sync to ensure non-standard saves (AJAX) work
        setInterval(function() {
            editors.forEach(function(e) { serializeToTextarea(e); });
        }, 1000);
    }

    // ── Create block editor shell ──────────────────────────────────────
    function initShell(textarea) {
        var shell = document.createElement('div');
        shell.className = 'gxb-shell';
        shell.dataset.editorId = textarea.id || ('gxb-' + Date.now());
        textarea.parentNode.insertBefore(shell, textarea);
        textarea.style.display = 'none';

        var isClassic = (typeof GX_EDITOR_STYLE !== 'undefined' && GX_EDITOR_STYLE === 'classic');
        if (isClassic) shell.classList.add('classic-mode');

        var state = { 
            textarea: textarea, 
            shell: shell, 
            blocks: [],
            isClassic: isClassic,
            allowedBlocks: textarea.dataset.blocks ? JSON.parse(textarea.dataset.blocks) : (typeof GX_EDITOR_BLOCKS !== 'undefined' && GX_EDITOR_BLOCKS.length > 0 ? GX_EDITOR_BLOCKS : [])
        };

        if (isClassic) {
            var tpl = document.getElementById('gxb-classic-toolbar-template');
            if (tpl) {
                var tb = tpl.firstElementChild.cloneNode(true);
                shell.appendChild(tb);
                // Bind buttons & dropdown items with data-cmd
                tb.querySelectorAll('[data-cmd]').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault(); e.stopPropagation();
                        var cmd = btn.dataset.cmd;
                        
                        // Smart Cursor/Block Detection
                        var sel = window.getSelection();
                        var currentBlockId = null;
                        if (sel.rangeCount) {
                            var node = sel.anchorNode;
                            var blockEl = node.nodeType === 3 ? node.parentNode.closest('.gxb-block') : node.closest('.gxb-block');
                            if (blockEl) currentBlockId = blockEl.dataset.blockId;
                        }

                        if (cmd === 'insertImageGX') {
                             addBlock(state, 'image', '', currentBlockId); 
                             renderAllBlocks(state);
                        } else if (cmd === 'createLink') {
                             var url = prompt('Enter URL:', 'https://');
                             if (url) document.execCommand('createLink', false, url);
                        } else if (['h1','h2','h3','paragraph','ul','ol'].indexOf(cmd) !== -1) {
                             if (currentBlockId) {
                                 var idx = state.blocks.findIndex(function(b){return b.id === currentBlockId;});
                                 if(idx !== -1) { 
                                     state.blocks[idx].type = cmd; 
                                     renderAllBlocks(state); 
                                     setTimeout(function(){ 
                                         var newEl = state.shell.querySelector('[data-block-id="'+currentBlockId+'"] .gxb-content');
                                         if(newEl) newEl.focus(); 
                                     }, 10);
                                 }
                             }
                        } else if (['icon_list','table','grid2','toc'].indexOf(cmd) !== -1) {
                             addBlock(state, cmd, '', currentBlockId); 
                             renderAllBlocks(state);
                        } else {
                             document.execCommand(cmd, false, null);
                        }
                    });
                });
                var addBtn = tb.querySelector('#gxb-classic-add-btn');
                if (addBtn) {
                    addBtn.addEventListener('click', function(e) {
                         e.preventDefault(); e.stopPropagation();
                         openPicker(state, null, getPickerAnchor(addBtn));
                    });
                }
            }
            var wrap = document.createElement('div');
            wrap.className = 'gxb-classic-content-wrap';
            shell.appendChild(wrap);
            state.contentWrap = wrap;

            // Click empty space at bottom to focus last paragraph or add new one
            wrap.addEventListener('click', function(e) {
                if (e.target === wrap) {
                    var last = state.blocks[state.blocks.length - 1];
                    if (last && last.type === 'paragraph') {
                        var lastEl = wrap.querySelector('.gxb-block:last-child .gxb-content');
                        if (lastEl) lastEl.focus();
                    } else {
                        addBlock(state, 'paragraph', '', null);
                        renderAllBlocks(state);
                        var newEl = wrap.querySelector('.gxb-block:last-child .gxb-content');
                        if (newEl) newEl.focus();
                    }
                }
            });
        }

        editors.push(state);

        // Parse existing HTML into blocks
        // Read from backup data attribute if .value is unexpectedly empty (nested editor safety)
        var htmlToParse = textarea.value || textarea.dataset.gxcontent || '';
        parseHTML(htmlToParse, state);

        // Add initial paragraph if empty
        if (!state.blocks.length) {
            addBlock(state, 'paragraph', '', null);
        }

        renderAllBlocks(state);

        // Add-block button at bottom
        var addWrap = document.createElement('div');
        addWrap.className = 'gxb-addbtn-wrap';
        addWrap.innerHTML = '<button class="gxb-addbtn" type="button"><i class="bi bi-plus-circle me-2"></i>Add Block</button>';
        addWrap.querySelector('.gxb-addbtn').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openPicker(state, null, getPickerAnchor(addWrap));
        });
        shell.appendChild(addWrap);
    }

    // ── Parse HTML → blocks ────────────────────────────────────────────
    function parseHTML(html, state) {
        if (!html.trim()) return;
        var div = document.createElement('div');
        div.innerHTML = html;
        div.childNodes.forEach(function(node) {
            var tag = node.nodeType === 1 ? node.tagName.toLowerCase() : '';
            var content = (node.textContent || '').trim();
            var innerHTML = (node.innerHTML || '').trim();
            
            // Check if this is a "Simple Node" (Text node, or a p/div without classes)
            // We ONLY detect standalone shortcodes in Simple Nodes to avoid hijacking Grids/Cards
            var isSimple = (node.nodeType === 3) || (node.nodeType === 1 && (tag === 'p' || tag === 'div') && node.classList.length === 0);

            if (isSimple) {
                // 1. Check for STANDALONE Image Shortcode
                var imgMatch = content.match(/^\[image\b([^\]]*)\]$/i);
                if (imgMatch) {
                    var attrStr = imgMatch[1];
                    var src = (attrStr.match(/src="([^"]*)"/) || [0, ''])[1];
                    if (src) {
                        var b = addBlock(state, 'image', src, null);
                        b.imgWidth = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '';
                        b.imgAlign = (attrStr.match(/align="([^"]*)"/) || [0, ''])[1] || '';
                        b.imgStyle = (attrStr.match(/style="([^"]*)"/) || [0, ''])[1] || 'rounded';
                        b.alt = (attrStr.match(/alt="([^"]*)"/) || [0, ''])[1] || '';
                        b.caption = (attrStr.match(/caption="([^"]*)"/) || [0, ''])[1] || '';
                        return;
                    }
                }
                // 2. Check for STANDALONE TOC Shortcode
                var tocMatch = content.match(/^\[toc\b([^\]]*)\]$/i);
                if (tocMatch) {
                    var b = addBlock(state, 'toc', '', null);
                    var attrStr = tocMatch[1];
                    b.tocTitle = (attrStr.match(/title="([^"]*)"/) || [0, ''])[1] || 'Daftar Isi';
                    b.tocFloat = (attrStr.match(/float="([^"]*)"/) || [0, ''])[1] || 'none';
                    b.tocWidth = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '450px';
                    b.tocCollapse = (attrStr.match(/collapse="([^"]*)"/) || [0, ''])[1] || 'no';
                    return;
                }
                // 3. Check for STANDALONE Post Shortcodes
                var postMatch = content.match(/^\[post id="(\d+)"\]$/i);
                if (postMatch) { addBlock(state, 'single_post', postMatch[1], null); return; }
                if (content === '[recent_posts]') { addBlock(state, 'recent_posts', '', null); return; }
                if (content === '[random_posts]') { addBlock(state, 'random_posts', '', null); return; }
                // 4. Check for STANDALONE Icon Shortcode
                var iconMatch = content.match(/^\[icon\b([^\]]*)\]$/i);
                if (iconMatch) {
                    var attrStr = iconMatch[1];
                    var b = addBlock(state, 'icon', '', null);
                    b.iconClass = (attrStr.match(/class="([^"]*)"/) || [0, 'bi bi-star'])[1];
                    b.iconSize  = (attrStr.match(/size="([^"]*)"/) || [0, '2.5rem'])[1];
                    b.iconColor = (attrStr.match(/color="([^"]*)"/) || [0, '#6366f1'])[1];
                    return;
                }
                // 5. Check for STANDALONE Icon List
                if (content.indexOf('[icon_list') === 0) {
                   var match = content.match(/\[icon_list\b([^\]]*)\]([\s\S]*)\[\/icon_list\]/i);
                   if (match) {
                       var b = addBlock(state, 'icon_list', match[2].trim(), null);
                       b.listIcon = (match[1].match(/icon="([^"]*)"/) || [0, 'bi bi-check2-circle'])[1];
                       b.listColor = (match[1].match(/color="([^"]*)"/) || [0, '#6366f1'])[1];
                       return;
                   }
                }
                // 6. Check for STANDALONE Table
                if (content.indexOf('[table') === 0) {
                   var match = content.match(/\[table\b([^\]]*)\]([\s\S]*)\[\/table\]/i);
                   if (match) {
                       var b = addBlock(state, 'table', match[2].trim(), null);
                       b.tableBorder = (match[1].match(/border="([^"]*)"/) || [0, 'yes'])[1];
                       b.tableStriped = (match[1].match(/striped="([^"]*)"/) || [0, 'no'])[1];
                       return;
                   }
                }
            }

            if (node.nodeType !== 1) {
                if (content) addBlock(state, 'paragraph', content, null);
                return;
            }

            // 4. Handle Complex Blocks (Grid, Card, etc.)
            
            // Single Post Wrapper
            var spWrap = node.classList && (node.classList.contains('gx-single-post') ? node : node.querySelector('.gx-single-post'));
            if (spWrap) {
                var tid = (spWrap.textContent.match(/id="(\d+)"/) || [0, '0'])[1];
                if (tid === '0') tid = spWrap.getAttribute('data-postid') || '0';
                addBlock(state, 'single_post', tid, null);
                return;
            }
            // Recent/Random posts
            if (node.classList && (node.classList.contains('gx-recent-posts') || node.querySelector('.gx-recent-posts'))) {
                addBlock(state, 'recent_posts', '', null); return;
            }
            if (node.classList && (node.classList.contains('gx-random-posts') || node.querySelector('.gx-random-posts'))) {
                addBlock(state, 'random_posts', '', null); return;
            }
            // TOC Wrapper
            var tocWrap = node.classList && (node.classList.contains('gx-toc') ? node : node.querySelector('.gx-toc'));
            if (tocWrap) {
                var b = addBlock(state, 'toc', '', null);
                var tMatch = tocWrap.textContent.match(/\[toc\b([^\]]*)\]/i);
                if (tMatch) {
                    var attrStr = tMatch[1];
                    b.tocTitle = (attrStr.match(/title="([^"]*)"/) || [0, ''])[1] || 'Daftar Isi';
                    b.tocFloat = (attrStr.match(/float="([^"]*)"/) || [0, ''])[1] || 'none';
                    b.tocWidth = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '450px';
                    b.tocCollapse = (attrStr.match(/collapse="([^"]*)"/) || [0, ''])[1] || 'no';
                }
                return;
            }

            if (tag === 'h1' || tag === 'h2' || tag === 'h3') {
                addBlock(state, tag, node.innerHTML, null);
            } else if (tag === 'blockquote') {
                var inner = node.querySelector('p') || node;
                addBlock(state, 'quote', inner.innerHTML, null);
            } else if (tag === 'pre') {
                var code = node.querySelector('code') || node;
                addBlock(state, 'code', code.textContent, null);
            } else if (tag === 'ul' || tag === 'ol') {
                var items = [].slice.call(node.querySelectorAll('li')).map(function(li){ return li.innerHTML; }).join('\n');
                addBlock(state, tag, items, null);
            } else if (tag === 'hr') {
                addBlock(state, 'divider', '', null);
            } else if (tag === 'div' && node.classList.contains('row')) {
                var cols = node.querySelectorAll('.col-12');
                if (cols.length > 2) {
                    var b = addBlock(state, 'grid2x2', '', null);
                    b.col1 = cols.length > 0 ? cols[0].innerHTML : '';
                    b.col2 = cols.length > 1 ? cols[1].innerHTML : '';
                    b.col3 = cols.length > 2 ? cols[2].innerHTML : '';
                    b.col4 = cols.length > 3 ? cols[3].innerHTML : '';
                    b.colCount = cols.length;
                } else {
                    var block = addBlock(state, 'grid2', '', null);
                    block.col1 = cols.length > 0 ? cols[0].innerHTML : '';
                    block.col2 = cols.length > 1 ? cols[1].innerHTML : '';
                    block.colCount = cols.length;
                    if (cols.length == 2) {
                       var cl1 = cols[0].className.match(/col-[a-z]+-(\d+)/) || cols[0].className.match(/col-(\d+)/);
                       var cl2 = cols[1].className.match(/col-[a-z]+-(\d+)/) || cols[1].className.match(/col-(\d+)/);
                       if(cl1 && cl2) block.colRatio = cl1[1] + ':' + cl2[1];
                    }
                }
            } else if (tag === 'div' && node.classList.contains('gx-icon-block')) {
                var b = addBlock(state, 'icon', '', null);
                var match = node.innerHTML.match(/\[icon\b([^\]]*)\]/i);
                if (match) {
                    var attrStr = match[1];
                    b.iconClass = (attrStr.match(/class="([^"]*)"/) || [0, ''])[1];
                    b.iconSize  = (attrStr.match(/size="([^"]*)"/) || [0, ''])[1];
                    b.iconColor = (attrStr.match(/color="([^"]*)"/) || [0, ''])[1];
                } else {
                    var iconEl = node.querySelector('i');
                    if (iconEl) {
                        b.iconClass = iconEl.className;
                        b.iconSize  = iconEl.style.fontSize || '2.5rem';
                        b.iconColor = iconEl.style.color || '#6366f1';
                    }
                }
                return;
            } else if (tag === 'ul' && node.classList.contains('gx-icon-list')) {
                var b = addBlock(state, 'icon_list', '', null);
                var items = [].slice.call(node.querySelectorAll('li')).map(function(li){ return li.textContent.trim(); }).join('\n');
                b.content = items;
                var firstIcon = node.querySelector('i');
                if (firstIcon) {
                    b.listIcon = firstIcon.className;
                    b.listColor = firstIcon.style.color;
                }
            } else if (tag === 'div' && node.classList.contains('table-responsive')) {
                var b = addBlock(state, 'table', '', null);
                var tbl = node.querySelector('table');
                if (tbl) {
                    b.content = tbl.innerHTML;
                    b.tableBorder = tbl.classList.contains('table-bordered') ? 'yes' : 'no';
                    b.tableStriped = tbl.classList.contains('table-striped') ? 'yes' : 'no';
                }
            } else if (tag === 'div' && node.classList.contains('card')) {
                var b = addBlock(state, 'card', '', null);
                var head = node.querySelector('.card-header'); var body = node.querySelector('.card-body'); var foot = node.querySelector('.card-footer');
                if (head) { b.hasHeader = true; b.header = shortcodeToHtml(head.innerHTML); }
                if (foot) { b.hasFooter = true; b.footer = shortcodeToHtml(foot.innerHTML); }
                b.content = body ? body.innerHTML : node.innerHTML;
            } else if (tag === 'a' && node.classList.contains('btn')) {
                var block = addBlock(state, 'button', node.innerHTML.trim(), null);
                block.btnUrl = node.getAttribute('href');
                block.btnClass = node.className.replace('btn', '').trim() || 'btn-primary';
            } else {
                // Fallback: Paragraph
                var pContent = shortcodeToHtml(node.innerHTML.trim());
                if (pContent) {
                    var block = addBlock(state, 'paragraph', pContent, null);
                    if (node.style.textAlign) block.textAlign = node.style.textAlign;
                    if (node.style.lineHeight) block.lineHeight = node.style.lineHeight;
                }
            }
        });
    }

    // ── Add block to state ─────────────────────────────────────────────
    function addBlock(state, type, content, afterId, position) {
        var block = {
            id: 'blk-' + Math.random().toString(36).slice(2),
            type: type,
            content: content || ''
        };
        if (afterId === null) {
            if (position === 'start') state.blocks.unshift(block);
            else state.blocks.push(block);
        } else {
            var idx = state.blocks.findIndex(function(b){ return b.id === afterId; });
            if (idx !== -1) {
                if (position === 'before') state.blocks.splice(idx, 0, block);
                else state.blocks.splice(idx + 1, 0, block);
            } else {
                state.blocks.push(block);
            }
        }
        return block;
    }

    function renderAllBlocks(state) {
        var target = state.isClassic ? state.contentWrap : state.shell;
        
        // CLEANUP NESTED EDITORS FOR THIS SHELL to prevent memory leak
        editors = editors.filter(function(e) {
            if (e !== state && e.shell && target.contains(e.shell)) {
                return false;
            }
            return true;
        });

        // Clear existing block elements (keep add button in block mode)
        target.querySelectorAll(':scope > .gxb-block').forEach(function(el){ el.remove(); });
        
        var addWrap = state.shell.querySelector('.gxb-addbtn-wrap');
        state.blocks.forEach(function(block) {
            var el = createBlockEl(state, block);
            if (state.isClassic) {
                target.appendChild(el);
            } else {
                state.shell.insertBefore(el, addWrap || null);
            }
        });
        serializeToTextarea(state);
    }

    // ── Create a single block DOM element ─────────────────────────────
    function createBlockEl(state, block) {
        var typeDef = GxEditor._blocks[block.type] || GxEditor._blocks['paragraph'];

        var wrapper = document.createElement('div');
        wrapper.className = 'gxb-block';
        wrapper.dataset.blockId = block.id;
        wrapper.dataset.type = block.type;
        wrapper.draggable = true;

        // ── Handle + add-before button ──
        var handleWrap = document.createElement('div');
        handleWrap.className = 'gxb-handle-wrap';
        handleWrap.innerHTML =
            '<button class="gxb-add-inline" type="button" title="Add block above"><i class="bi bi-plus"></i></button>' +
            '<span class="gxb-handle" title="Drag to reorder"><i class="bi bi-grid-3x2-gap-fill"></i></span>';
        handleWrap.querySelector('.gxb-add-inline').addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var anchor = wrapper.getBoundingClientRect();
            // override: we want to insert before
            openPickerBefore(state, block.id, { top: Math.max(0, anchor.top - 10), left: anchor.left });
        });


        // ── Content area ──
        var tagToUse = 'div';
        if (block.type === 'ul') tagToUse = 'ul';
        if (block.type === 'ol') tagToUse = 'ol';
        
        var content = document.createElement(tagToUse);
        content.className = 'gxb-content';
        content.dataset.placeholder = typeDef.placeholder || '';

        // Custom Render Support
        if (typeDef.render && typeof typeDef.render === 'function') {
            typeDef.render(state, block, content, wrapper);
        } else if (block.type === 'divider') {
            content.innerHTML = '<hr>';
            content.contentEditable = 'false';
        } else if (block.type === 'ul' || block.type === 'ol') {
            content.contentEditable = 'true';
            var lines = (block.content || '').split('\n');
            content.innerHTML = lines.map(function(l){ 
                return l.trim() ? '<li>' + l + '</li>' : '<li><br></li>'; 
            }).join('') || '<li><br></li>';
        } else if (block.type === 'code') {
            content.contentEditable = 'true';
            content.spellcheck = false;
            content.textContent = block.content;
        } else if (block.type === 'grid2' || block.type === 'grid2x2') {
            content.className = 'gxb-content';
            content.style.display = 'flex';
            content.style.flexDirection = 'column';
            content.style.gap = '15px';
            content.style.padding = '8px 0';
            content.title = 'Right-click for Grid Settings (Rows/Cols)';
            
            var rowCount = block.rowCount || (block.type === 'grid2x2' ? 2 : 1);
            var colCount = block.colCount || 2;
            var ratio    = (block.colRatio || '6:6').split(':');

            for (var r = 0; r < rowCount; r++) {
                var rowDiv = document.createElement('div');
                rowDiv.style.display = 'flex';
                rowDiv.style.gap = '15px';
                
                for (var c = 0; c < colCount; c++) {
                    var idx = (r * colCount) + (c + 1);
                    var key = 'col' + idx;
                    
                    // Fallback for grid2x2 migration
                    if (block.type === 'grid2x2') {
                        if (idx == 1 && !block[key]) block[key] = block.c1;
                        if (idx == 2 && !block[key]) block[key] = block.c2;
                        if (idx == 3 && !block[key]) block[key] = block.c3;
                        if (idx == 4 && !block[key]) block[key] = block.c4;
                    }

                    var cell = document.createElement('div');
                    cell.className = 'gxb-col';
                    
                    if (colCount == 2) {
                        cell.style.flex = ratio[c] || '6';
                    } else {
                        cell.style.flex = '1';
                    }

                    // Nested Editor Instance!
                    var subTa = document.createElement('textarea');
                    subTa.className = 'editor gxb-nested-editor';
                    subTa.id = 'gxb-inner-' + block.id + '-' + key;
                    subTa.style.display = 'none';
                    var cellHtml = block[key] || '';
                    subTa.value = cellHtml;
                    // Backup content in data attribute in case .value is cleared by browser
                    subTa.dataset.gxcontent = cellHtml;
                    subTa.dataset.blocks = JSON.stringify(typeof GX_EDITOR_BLOCKS !== 'undefined' ? GX_EDITOR_BLOCKS.filter(function(x){return ['grid2','grid2x2'].indexOf(x) === -1;}) : []);
                    
                    cell.appendChild(subTa);
                    rowDiv.appendChild(cell);
                    // Use setTimeout to init nested shell after DOM is fully settled
                    // data-gxcontent serves as backup if .value is emptied
                    setTimeout((function(ta) { return function(){ initShell(ta); }; })(subTa), 10);
                }
                content.appendChild(rowDiv);
            }
        } else if (block.type === 'card') {
            content.className = 'card gxb-content';
            content.style.padding = '0';
            content.style.background = 'transparent';
            content.title = 'Right-click for Card Header/Footer settings';

            if (block.hasHeader) {
                var h = document.createElement('div');
                h.className = 'card-header'; h.contentEditable = 'true';
                h.innerHTML = block.header || 'Card Header';
                attachBlockEvents(state, block, h, wrapper, 'header');
                content.appendChild(h);
            }
            
            var b = document.createElement('div');
            b.className = 'card-body'; 
            
            var subTa = document.createElement('textarea');
            subTa.className = 'editor gxb-nested-editor';
            subTa.id = 'gxb-inner-' + block.id + '-content';
            subTa.style.display = 'none';
            subTa.value = block.content || '';
            subTa.dataset.blocks = JSON.stringify(typeof GX_EDITOR_BLOCKS !== 'undefined' ? GX_EDITOR_BLOCKS.filter(function(x){return ['card'].indexOf(x) === -1;}) : []);
            b.appendChild(subTa);
            setTimeout((function(ta) { return function(){ initShell(ta); }; })(subTa), 0);
            
            content.appendChild(b);
            
            if (block.hasFooter) {
                var f = document.createElement('div');
                f.className = 'card-footer'; f.contentEditable = 'true';
                f.innerHTML = block.footer || 'Card Footer';
                attachBlockEvents(state, block, f, wrapper, 'footer');
                content.appendChild(f);
            }
        } else if (block.type === 'button') {
            content.style.textAlign = 'center';
            var btn = document.createElement('a');
            btn.className = 'btn ' + (block.btnClass || 'btn-primary');
            btn.textContent = block.content || 'Button Text';
            btn.contentEditable = 'true';
            btn.title = 'Right-click for Button settings';
            content.addEventListener('input', function() { block.content = btn.textContent; });
            content.appendChild(btn);
        } else if (block.type === 'icon') {
            content.className = 'gxb-content gx-icon-block';
            content.style.display = 'flex'; content.style.justifyContent = 'center'; content.style.padding = '20px 0';
            var iPrv = document.createElement('i');
            iPrv.className = block.iconClass || 'bi bi-star';
            iPrv.style.cssText = 'display:inline-block; position:relative; z-index:10; cursor:pointer;';
            iPrv.style.fontSize = block.iconSize || '2.5rem'; 
            iPrv.style.color = block.iconColor || '#6366f1';
            iPrv.title = 'Right-click to change icon & settings';
            content.appendChild(iPrv);
            content.contentEditable = 'false';
            
            // Apply a direct listener to BOTH the icon and its container
            var triggerIconSettings = function(e) {
                e.preventDefault(); e.stopPropagation();
                closeAllContextMenus();
                openIconContext(e, state, block);
            };
            content.addEventListener('contextmenu', triggerIconSettings);
            iPrv.addEventListener('contextmenu', triggerIconSettings);
            
            content.addEventListener('click', function(e) { e.stopPropagation(); });
        } else if (block.type === 'single_post') {
            content.style.background = '#f0fdf4';
            content.style.border = '2px dashed #bbf7d0';
            content.style.borderRadius = '6px';
            content.style.padding = '15px';
            content.style.textAlign = 'center';
            
            var header = document.createElement('div');
            header.innerHTML = '<i class="bi bi-card-checklist me-2"></i><strong>Single Post Picker</strong>';
            header.style.color = '#166534';
            header.style.marginBottom = '8px';
            
            var inp = document.createElement('input');
            inp.type = 'text';
            inp.placeholder = 'Post ID (e.g. 11)';
            inp.value = block.content || '';
            inp.style.cssText = 'width:120px; padding:4px 8px; border:1px solid #bbf7d0; border-radius:4px; text-align:center; outline:none; font-weight:bold;';
            inp.addEventListener('input', function() {
                block.content = inp.value;
            });
            inp.addEventListener('click', function(e) { e.stopPropagation(); }); // Prevent triggering drag or focus wrapper
            
            content.appendChild(header);
            content.appendChild(inp);
            content.contentEditable = 'false';
        } else if (block.type === 'toc') {
            var title = block.tocTitle || 'Table of Contents';
            content.innerHTML = '<div style="background:#f1f5f9; padding:15px; border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; gap:12px; color:#475569;">'
                + '<i class="bi bi-list-nested fs-3 text-primary"></i>'
                + '<div><strong>' + title + '</strong><br><small>Auto-generated Nav (H1-H4)</small></div>'
                + '</div>';
            content.contentEditable = 'false';
        } else if (block.type === 'recent_posts' || block.type === 'random_posts') {
            content.innerHTML = '<div style="background:#f8fafc; padding:15px; text-align:center; color:#64748b; border:2px dashed #cbd5e1; border-radius:6px;"><i class="' + typeDef.icon + ' me-2"></i><strong>' + typeDef.label + '</strong><br><small>Widget Placeholder</small></div>';
            content.contentEditable = 'false';
        } else if (block.type === 'icon_list') {
            content.className = 'gxb-content gx-icon-list-editor';
            content.contentEditable = 'true';
            var items = (block.content || '').split('\n').filter(Boolean);
            if (items.length === 0) items = ['List item'];
            var icon = block.listIcon || 'bi bi-check2-circle';
            var color = block.listColor || '#6366f1';
            content.innerHTML = items.map(function(it) {
                return '<div class="d-flex align-items-start gap-2 mb-1"><i class="' + icon + '" style="color:' + color + '; margin-top:4px;"></i><div class="flex-1">' + it + '</div></div>';
            }).join('');
            content.addEventListener('input', function() {
                var lines = [].slice.call(content.querySelectorAll('.flex-1')).map(function(d){ return d.innerHTML; });
                block.content = lines.join('\n');
            });
        } else if (block.type === 'table') {
            content.className = 'gxb-content table-responsive';
            content.contentEditable = 'false';
            var tbl = document.createElement('table');
            tbl.className = 'table ' + (block.tableBorder === 'no' ? '' : 'table-bordered ') + (block.tableStriped === 'yes' ? 'table-striped' : '');
            tbl.innerHTML = block.content || '<tr><td contenteditable="true">Cell 1</td><td contenteditable="true">Cell 2</td></tr><tr><td contenteditable="true">Cell 3</td><td contenteditable="true">Cell 4</td></tr>';
            content.innerHTML = '';
            content.appendChild(tbl);
            tbl.addEventListener('input', function() { block.content = tbl.innerHTML; });
        } else if (block.type !== 'image') {
            content.contentEditable = 'true';
            content.innerHTML = block.content;
        }

        // Apply Text Properties
        if (block.textAlign) content.style.textAlign = block.textAlign;
        if (block.lineHeight) content.style.lineHeight = block.lineHeight;

        // ── Inline events ──
        if (content.contentEditable === 'true' || block.type === 'table') {
            attachBlockEvents(state, block, content, wrapper, 'content');
        }

        // ── Action buttons ──
        var actions = document.createElement('div');
        actions.className = 'gxb-actions';
        actions.innerHTML =
            '<button type="button" title="Move up" class="gxb-move-up"><i class="bi bi-chevron-up"></i></button>' +
            '<button type="button" title="Move down" class="gxb-move-dn"><i class="bi bi-chevron-down"></i></button>' +
            '<button type="button" title="Delete block" class="gxb-del"><i class="bi bi-trash3"></i></button>';

        actions.querySelector('.gxb-move-up').addEventListener('click', function() { moveBlock(state, block.id, -1); });
        actions.querySelector('.gxb-move-dn').addEventListener('click', function() { moveBlock(state, block.id,  1); });
        actions.querySelector('.gxb-del').addEventListener('click', function() { deleteBlock(state, block.id); });

        if (block.type === 'image') {
            // Wrap image content + caption in a column so caption sits BELOW the image
            var imgCol = document.createElement('div');
            imgCol.style.cssText = 'flex:1; display:flex; flex-direction:column; gap:4px;';
            renderImageBlock(state, block, content, wrapper);
            imgCol.appendChild(content);
            var cap = buildImageCaption(state, block);
            imgCol.appendChild(cap);
            wrapper.appendChild(handleWrap);
            wrapper.appendChild(imgCol);
            wrapper.appendChild(actions);
        } else {
            wrapper.appendChild(handleWrap);
            wrapper.appendChild(content);
            wrapper.appendChild(actions);
        }

        // ── Drag & Drop ──
        setupDrag(state, wrapper, block);

        // ── Block Specific Styling (Float/Width) ──
        if (block.type === 'toc') {
            if (block.tocFloat === 'float-start') {
                wrapper.classList.add('float-start', 'me-2');
            } else if (block.tocFloat === 'float-end') {
                wrapper.classList.add('float-end', 'ms-2');
            }
            if (block.tocWidth && block.tocWidth !== '100%') {
                wrapper.style.width = block.tocWidth;
            }
        }

        return wrapper;
    }

    // ── Image block rendering ──────────────────────────────────────────
    function renderImageBlock(state, block, content, wrapper) {
        var hasImage = block.content && (
            block.content.indexOf('http') === 0 ||
            block.content.indexOf('/') === 0 ||
            block.content.indexOf('data:') === 0
        );

        if (hasImage) {
            var img = document.createElement('img');
            img.src = block.content;
            img.alt = block.caption || '';
            
            // Apply properties
            var style = block.imgStyle || 'rounded';
            var w = block.imgWidth || '';
            var a = block.imgAlign || '';
            
            img.className = 'img-fluid ' + style + ' ' + w + ' ' + a;
            img.style.maxWidth = '100%';
            img.style.display = 'block';
            if (a === 'mx-auto d-block') img.style.margin = '0 auto';

            content.innerHTML = '';
            content.appendChild(img);
            // Click image to replace
            img.style.cursor = 'pointer';
            img.title = 'Click to replace image (Right-click for properties)';
            img.addEventListener('click', function() { triggerImageReplace(state, block); });
            img.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                openImageContext(e, state, block);
            });
        } else {
            var drop = document.createElement('div');
            drop.className = 'gxb-img-drop';
            drop.style.position = 'relative'; // For close button
            drop.innerHTML = '<i class="bi bi-cloud-arrow-up"></i>Click or drop to upload image<br><small>or paste URL below and press Enter</small>';
            
            // X (Close) button for accidental clicks
            var closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.innerHTML = '<i class="bi bi-x"></i>';
            closeBtn.style.cssText = 'position:absolute; top:8px; right:8px; border:none; background:#fef2f2; color:#ef4444; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.2rem;';
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault(); e.stopPropagation();
                if (confirm('Remove this image placeholder?')) deleteBlock(state, block.id);
            });
            drop.appendChild(closeBtn);

            var fileIn = document.createElement('input');
            fileIn.type = 'file'; fileIn.accept = 'image/*'; fileIn.hidden = true;
            drop.appendChild(fileIn);

            // URL input
            var urlRow = document.createElement('div');
            urlRow.style.cssText = 'display:flex; gap:6px; margin-top:8px;';
            var urlIn = document.createElement('input');
            urlIn.type = 'url'; urlIn.placeholder = 'Or paste image URL here...';
            urlIn.style.cssText = 'flex:1; border:1px solid #e2e8f0; border-radius:6px; padding:5px 10px; font-size:.8rem; outline:none;';
            var urlBtn = document.createElement('button');
            urlBtn.type = 'button'; urlBtn.textContent = 'Insert';
            urlBtn.style.cssText = 'border:none; background:#6366f1; color:#fff; border-radius:6px; padding:5px 12px; font-size:.8rem; cursor:pointer;';
            urlRow.appendChild(urlIn);
            urlRow.appendChild(urlBtn);

            drop.addEventListener('click', function(e) {
                if (e.target === urlIn || e.target === urlBtn) return;
                fileIn.click();
            });
            fileIn.addEventListener('change', function() { uploadImageFile(fileIn.files[0], state, block); });
            urlBtn.addEventListener('click', function() {
                var u = urlIn.value.trim();
                if (u) { block.content = u; refreshImageBlock(state, block); }
            });
            urlIn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { var u = urlIn.value.trim(); if (u) { block.content = u; refreshImageBlock(state, block); } }
            });

            content.appendChild(drop);
            content.appendChild(urlRow);
        }
    }

    function uploadImageFile(file, state, block) {
        if (!file) return;

        // Show loading state
        var blockEl = state.shell.querySelector('[data-block-id="' + block.id + '"] .gxb-content');
        if (blockEl) {
            blockEl.innerHTML = '<div style="text-align:center; padding:24px; color:#94a3b8;">'
                + '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>'
                + 'Uploading...</div>';
        }

        // Build elfinder URL — detect query string vs path-style (SMART_URL)
        var base = GX_ELFINDER_URL;
        var sep  = base.indexOf('?') !== -1 ? '&' : '?';

        // Step 1: Init to get root folder hash
        fetch(base + sep + 'cmd=open&init=1&target=', { credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(initData) {
            if (!initData || !initData.cwd) throw new Error('ElFinder init failed — check token');

            var rootHash = initData.cwd.hash;

            // Step 2: Upload — send auto_sort=1 as GET param (PHP checks $_GET['auto_sort'])
            // so elfinder routes image → assets/media/images/ automatically
            var uploadUrl = base + sep + 'cmd=upload&auto_sort=1';
            var fd = new FormData();
            fd.append('target', rootHash);
            fd.append('upload[]', file);

            return fetch(uploadUrl, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            });
        })
        .then(function(r) { return r.json(); })
        .then(function(upRes) {
            if (upRes.error) {
                var msg = Array.isArray(upRes.error) ? upRes.error.join(' ') : upRes.error;
                throw new Error(msg);
            }
            if (!upRes.added || !upRes.added.length) throw new Error('No file returned from server');

            var fileUrl = upRes.added[0].url;
            if (!fileUrl) throw new Error('Server returned no URL');

            // Fix protocol double-slash from elfinder URL builder
            fileUrl = fileUrl.replace(/([^:])\/\/+/g, '$1/');

            block.content = fileUrl;
            refreshImageBlock(state, block);
        })
        .catch(function(err) {
            console.error('[GxEditor] Image upload failed:', err);
            var be = state.shell.querySelector('[data-block-id="' + block.id + '"] .gxb-content');
            if (be) {
                be.innerHTML = '<div class="gxb-img-drop" style="border-color:#fca5a5;">'
                    + '<i class="bi bi-exclamation-triangle text-danger fs-4 mb-2 d-block"></i>'
                    + '<strong class="text-danger">Upload failed</strong><br>'
                    + '<small class="text-muted">' + err.message + '</small><br>'
                    + '<button type="button" style="margin-top:10px; border:none; background:#6366f1; color:#fff; border-radius:6px; padding:6px 16px; font-size:.8rem; cursor:pointer;"'
                    + ' onclick="(function(btn){ var input=document.createElement(\'input\'); input.type=\'file\'; input.accept=\'image/*\'; input.onchange=function(){ if(window._gxRetryUpload) window._gxRetryUpload(btn, input.files[0]); }; input.click(); })(this)">'
                    + '<i class="bi bi-arrow-clockwise me-1"></i>Try Again</button>'
                    + '</div>';
            }
        });
    }




    function refreshImageBlock(state, block) {
        var blockEl = state.shell.querySelector('[data-block-id="' + block.id + '"]');
        if (!blockEl) return;
        var newEl = createBlockEl(state, block);
        blockEl.parentNode.replaceChild(newEl, blockEl);
    }

    function triggerImageReplace(state, block) {
        var fi = document.createElement('input');
        fi.type = 'file'; fi.accept = 'image/*';
        fi.addEventListener('change', function() { uploadImageFile(fi.files[0], state, block); });
        fi.click();
    }

    function buildImageCaption(state, block) {
        var cap = document.createElement('div');
        cap.className = 'gxb-caption';
        cap.contentEditable = 'true';
        cap.textContent = block.caption || '';
        cap.setAttribute('data-placeholder', 'Add caption...');
        cap.style.cssText = 'font-size:.82rem; color:#94a3b8; text-align:center; outline:none; padding:2px 4px; border-radius:4px;';
        cap.addEventListener('input', function() { block.caption = cap.textContent; });
        cap.addEventListener('focus', function() { cap.style.background = '#fafafa'; });
        cap.addEventListener('blur',  function() { cap.style.background = ''; });
        return cap;
    }

    // ── Block content events ───────────────────────────────────────────
    function attachBlockEvents(state, block, content, wrapper, keyToBind) {
        var dataKey = keyToBind || 'content';
        // Sync content to block state
        content.addEventListener('input', function() {
            if (block.type === 'icon_list' || block.type === 'single_post' || block.type === 'recent_posts' || block.type === 'random_posts') return;
            
            if (block.type === 'code') {
                block[dataKey] = content.textContent;
            } else {
                block[dataKey] = content.innerHTML;
            }
            serializeToTextarea(state);

            // Slash command detection
            var sel = window.getSelection();
            if (!sel.rangeCount) return;
            var range = sel.getRangeAt(0);
            var text  = range.startContainer.textContent || '';
            var pos   = range.startOffset;
            var slashIdx = text.lastIndexOf('/', pos);
            if (slashIdx !== -1 && (slashIdx === 0 || /\s/.test(text[slashIdx-1]))) {
                var query = text.slice(slashIdx + 1, pos);
                var isInsideCol = (keyToBind === 'col1' || keyToBind === 'col2');
                openPickerInline(state, block.id, query, range, isInsideCol);
            } else {
                closePicker();
            }
        });

        // Enter → new paragraph block
        content.addEventListener('keydown', function(e) {
            // Picker navigation
            var picker = document.getElementById('gxb-picker');
            if (picker.style.display === 'block') {
                handlePickerKey(e, state, block.id); return;
            }

            if (e.key === 'Enter' && !e.shiftKey) {
                // Blocks that handle Enter naturally (lists, code, grids, etc)
                if (['code','ul','ol','grid2','grid2x2','card','table'].indexOf(block.type) !== -1) return;
                
                // Handle Icon List item creation
                if (block.type === 'icon_list') {
                    e.preventDefault();
                    var sel = window.getSelection();
                    if (!sel.rangeCount) return;
                    var range = sel.getRangeAt(0);
                    var currentItem = range.startContainer.nodeType === 3 ? range.startContainer.parentNode.closest('.d-flex') : range.startContainer.closest('.d-flex');
                    
                    var icon = block.listIcon || 'bi bi-check2-circle';
                    var color = block.listColor || '#6366f1';
                    var newItem = document.createElement('div');
                    newItem.className = 'd-flex align-items-start gap-2 mb-1';
                    newItem.innerHTML = '<i class="' + icon + '" style="color:' + color + '; margin-top:4px;"></i><div class="flex-1"><br></div>';
                    
                    if (currentItem && content.contains(currentItem)) {
                        currentItem.parentNode.insertBefore(newItem, currentItem.nextSibling);
                    } else {
                        content.appendChild(newItem);
                    }
                    
                    var newFocus = newItem.querySelector('.flex-1');
                    if (newFocus) {
                        newFocus.focus();
                        var r2 = document.createRange();
                        r2.setStart(newFocus, 0);
                        r2.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(r2);
                    }
                    // Trigger custom input sync
                    content.dispatchEvent(new Event('input', { bubbles: true }));
                    return;
                }

                e.preventDefault();
                var newBlock = addBlock(state, 'paragraph', '', block.id);
                renderAllBlocks(state);
                // Focus newly added block
                var newEl = state.shell.querySelector('[data-block-id="' + newBlock.id + '"] .gxb-content');
                if (newEl) { newEl.focus(); placeCaretAtStart(newEl); }
            }

            // Backspace at start of empty block → delete it and focus previous
            if (e.key === 'Backspace') {
                var txt = content.textContent.trim();
                if (!txt) {
                    e.preventDefault();
                    var idx = state.blocks.findIndex(function(b){ return b.id === block.id; });
                    var prevId = idx > 0 ? state.blocks[idx - 1].id : null;
                    deleteBlock(state, block.id);
                    if (prevId) {
                        var prevEl = state.shell.querySelector('[data-block-id="' + prevId + '"] .gxb-content');
                        if (prevEl) { prevEl.focus(); placeCaretAtEnd(prevEl); }
                    }
                }
            }

            // Arrow-up at top → go to previous block
            if (e.key === 'ArrowUp') {
                var cSel = window.getSelection();
                if (cSel.rangeCount) {
                    var r = cSel.getRangeAt(0);
                    if (r.startOffset === 0 && r.startContainer === content || content.textContent === '') {
                        var idx2 = state.blocks.findIndex(function(b){ return b.id === block.id; });
                        if (idx2 > 0) {
                            var pEl = state.shell.querySelector('[data-block-id="' + state.blocks[idx2-1].id + '"] .gxb-content');
                            if (pEl) { e.preventDefault(); pEl.focus(); placeCaretAtEnd(pEl); }
                        }
                    }
                }
            }
        });

        // Show inline toolbar on text selection
        // Show inline toolbar handled by global selectionchange listener

        // Mark block as selected
        content.addEventListener('focus', function() {
            state.shell.querySelectorAll('.gxb-block').forEach(function(el){ el.classList.remove('gxb-selected'); });
            wrapper.classList.add('gxb-selected');
            _activeShell = state;
        });
    }

    // ── Block operations ───────────────────────────────────────────────
    function moveBlock(state, id, dir) {
        var idx = state.blocks.findIndex(function(b){ return b.id === id; });
        var newIdx = idx + dir;
        if (newIdx < 0 || newIdx >= state.blocks.length) return;
        var tmp = state.blocks[idx];
        state.blocks[idx] = state.blocks[newIdx];
        state.blocks[newIdx] = tmp;
        renderAllBlocks(state);
        var el = state.shell.querySelector('[data-block-id="' + id + '"] .gxb-content');
        if (el) el.focus();
    }

    function deleteBlock(state, id) {
        state.blocks = state.blocks.filter(function(b){ return b.id !== id; });
        if (!state.blocks.length) addBlock(state, 'paragraph', '', null);
        renderAllBlocks(state);
    }

    function insertBlock(state, type, afterId, position) {
        var block = addBlock(state, type, '', afterId, position);
        renderAllBlocks(state);
        var el = state.shell.querySelector('[data-block-id="' + block.id + '"] .gxb-content');
        if (el && el.contentEditable !== 'false') { el.focus(); }
        if (type === 'image') {
            var dropEl = state.shell.querySelector('[data-block-id="' + block.id + '"] .gxb-img-drop');
            if (dropEl) dropEl.click();
        }
    }

    // ── Inline Image Upload Helper ─────────────────────────────────────
    function uploadInlineImageElFinder(file, range) {
        if (!file) return;
        var base = GX_ELFINDER_URL;
        var sep  = base.indexOf('?') !== -1 ? '&' : '?';
        fetch(base + sep + 'cmd=open&init=1&target=', { credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(initData) {
            var fd = new FormData();
            fd.append('cmd', 'upload');
            fd.append('auto_sort', '1');
            fd.append('target', initData.cwd.hash);
            fd.append('upload[]', file);
            return fetch(base + sep + 'cmd=upload&auto_sort=1', { method: 'POST', body: fd, credentials: 'same-origin' });
        }).then(function(r) { return r.json(); }).then(function(parsed) {
            if (parsed.added && parsed.added.length > 0) {
                var url = parsed.added[0].url.replace(/([^:])\/\/+/g, '$1/');
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                // Use insertHTML to include Bootstrap responsive classes
                var imgHtml = '<img src="' + url + '" class="img-fluid rounded mb-2" alt="image">';
                document.execCommand('insertHTML', false, imgHtml);
            }
        });
    }

    // ── Global Keyboard Shortcuts ────────────────────────────────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideInlineToolbar();
            closePicker();
            closeAllContextMenus();
        }

        if (e.key === 'Backspace' || e.key === 'Delete') {
            // Never hijack backspace if the user is typing in form fields
            var tag = e.target.tagName.toLowerCase();
            if (tag === 'input' || tag === 'textarea' || tag === 'select') return;

            var selected = document.querySelector('.gxb-block.gxb-selected');
            if (selected) {
                // If it's a paragraph/editable, only delete if EMPTY
                var content = selected.querySelector('.gxb-content');
                var type = selected.dataset.type;
                var isComplex = ['image', 'grid2', 'grid2x2', 'card', 'icon', 'single_post', 'toc', 'recent_posts', 'random_posts', 'divider'].indexOf(type) !== -1;
                
                if (isComplex || (content && content.textContent.trim() === '')) {
                    // Prevent accidental deletion if focus is inside an input/cell that is NOT empty
                    if (e.target.dataset.placeholder === undefined && e.target.textContent.trim() !== '') {
                        return; // still has text inside
                    }
                    
                    e.preventDefault();
                    var blockId = selected.dataset.blockId;
                    var editorId = selected.closest('.gxb-shell').dataset.editorId;
                    var state = editors.find(function(ed){ return (ed.textarea.id || ed.shell.dataset.editorId) === editorId; });
                    if (state) deleteBlock(state, blockId);
                }
            }
        }
    });

    // ── Drag & Drop ────────────────────────────────────────────────────
    var _dragId = null;
    function setupDrag(state, wrapper, block) {
        wrapper.addEventListener('dragstart', function(e) {
            _dragId = block.id;
            wrapper.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
        });
        wrapper.addEventListener('dragend', function() {
            wrapper.style.opacity = '';
            state.shell.querySelectorAll('.gxb-block').forEach(function(el){ el.classList.remove('drag-over'); });
        });
        wrapper.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (_dragId && _dragId !== block.id) wrapper.classList.add('drag-over');
        });
        wrapper.addEventListener('dragleave', function() { wrapper.classList.remove('drag-over'); });
        wrapper.addEventListener('drop', function(e) {
            e.preventDefault();
            wrapper.classList.remove('drag-over');
            if (!_dragId || _dragId === block.id) return;
            var fromIdx = state.blocks.findIndex(function(b){ return b.id === _dragId; });
            var toIdx   = state.blocks.findIndex(function(b){ return b.id === block.id; });
            if (fromIdx === -1 || toIdx === -1) return;
            var moved = state.blocks.splice(fromIdx, 1)[0];
            state.blocks.splice(toIdx, 0, moved);
            renderAllBlocks(state);
            _dragId = null;
        });
    }

    // ── Inline Toolbar ────────────────────────────────────────────────
    var _inlineTbTimer = null;
    function showInlineToolbar() {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0 || sel.isCollapsed) {
            hideInlineToolbar();
            return;
        }

        var tb = document.getElementById('gxb-inline-toolbar');
        if (!tb) return;

        var range = sel.getRangeAt(0);
        var rects = range.getClientRects();
        if (rects.length === 0) {
            hideInlineToolbar();
            return;
        }

        // Use the first rect for positioning
        var rect = rects[0];
        if (rect.width === 0 || rect.height === 0) {
             hideInlineToolbar();
             return;
        }

        // Only show if selection is inside an editor shell
        var node = range.commonAncestorContainer;
        if (node.nodeType === 3) node = node.parentNode;
        var shell = node.closest('.gxb-shell');
        if (!shell) {
            hideInlineToolbar();
            return;
        }

        var top = rect.top - 52;
        var left = rect.left + (rect.width / 2) - 100;

        // Viewport constraints
        if (top < 10) top = rect.bottom + 10;
        if (left < 0) left = 10;
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

    // Global selection tracking
    document.addEventListener('selectionchange', function() {
        clearTimeout(_inlineTbTimer);
        _inlineTbTimer = setTimeout(showInlineToolbar, 60);
    });



    // ── Inline Image Upload Helper ─────────────────────────────────────
    function uploadInlineImageElFinder(file, range) {
        if (!file) return;
        var base = GX_ELFINDER_URL;
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
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                document.execCommand('insertImage', false, parsed.added[0].url);
            }
        });
    }

    // ── Image Property Context Menu ────────────────────────────────────
    // (Moved to top scope)

    // (Moved to top scope)


    // (Moved to top scope)


    // (Moved to top scope)


    // (Moved to top scope)


    // (Moved to top scope)


    // ── Block Picker ───────────────────────────────────────────────────
    var _pickerMode     = 'end';   // 'end' | 'after' | 'before' | 'inline'
    var _pickerAfter    = null;    // block id
    var _pickerBefore   = null;
    var _pickerQuery    = '';
    var _pickerRange    = null;

    var _pickerIsCol    = false;

    function openPicker(state, afterId, pos) {
        _pickerTargetShell = state;
        _pickerMode = 'after';
        _pickerAfter = afterId;
        _pickerIsCol = false;
        showPickerAt(pos);
    }

    function openPickerBefore(state, beforeId, pos) {
        _pickerTargetShell = state;
        _pickerMode = 'before';
        _pickerBefore = beforeId;
        _pickerIsCol = false;
        showPickerAt(pos);
    }

    function openPickerInline(state, afterId, query, range, isCol) {
        _pickerTargetShell = state;
        _pickerMode = 'inline-replace';
        _pickerAfter = afterId;
        _pickerQuery = query;
        _pickerRange = range;
        _pickerIsCol = !!isCol;
        var rect = range.getBoundingClientRect();
        showPickerAt({ top: rect.bottom + 4, left: rect.left });
        filterPicker(query);
    }

    function showPickerAt(pos) {
        var picker = document.getElementById('gxb-picker');
        if (!picker) return; 
        picker.style.setProperty('display', 'block', 'important');
        picker.style.setProperty('visibility', 'visible', 'important');
        picker.style.opacity = '1';

        filterPicker(''); // Load items FIRST so height calculates correctly!

        var pTop = pos.top || 0;
        var pLeft = pos.left || 0;

        // Ensure menu doesn't overflow bottom of screen
        var rect = picker.getBoundingClientRect();
        if (pTop + rect.height > window.innerHeight) {
            pTop = Math.max(10, window.innerHeight - rect.height - 10);
        }

        picker.style.top  = pTop + 'px';
        picker.style.left = pLeft + 'px';

        var search = document.getElementById('gxb-picker-search');
        if (search) {
            search.value = '';
            setTimeout(function() { search.focus(); }, 50);
        }
    }

    function closePicker() {
        var picker = document.getElementById('gxb-picker');
        if (picker) picker.style.setProperty('display', 'none', 'important');
    }

    function filterPicker(query) {
        var list = document.getElementById('gxb-picker-list');
        if (!list) return;
        var allowed = (_pickerTargetShell && _pickerTargetShell.allowedBlocks && _pickerTargetShell.allowedBlocks.length > 0) ? _pickerTargetShell.allowedBlocks : null;
        
        query = (query || '').toLowerCase();
        list.innerHTML = '';

        Object.keys(GxEditor._blocks).forEach(function(id, i) {
            var b = GxEditor._blocks[id];
            if (allowed && allowed.indexOf(id) === -1) return;
            if (query && !b.label.toLowerCase().includes(query) && !b.desc.toLowerCase().includes(query)) return;

            var item = document.createElement('div');
            item.className = 'gxb-picker-item' + (i === 0 ? ' active' : '');
            item.dataset.type = id;
            item.innerHTML = '<div class="gxb-picker-icon"><i class="' + (b.icon || 'bi bi-box') + '"></i></div>'
                 + '<div><div class="gxb-picker-label">' + b.label + '</div>'
                 + '<div class="gxb-picker-desc">' + b.desc + '</div></div>';
            
            item.addEventListener('mousedown', function(e) {
                e.preventDefault(); e.stopPropagation();
                selectPickerItem(id);
            });
            list.appendChild(item);
        });
    }

    function selectPickerItem(type) {
        closePicker();
        if (!_pickerTargetShell) return;
        var state = _pickerTargetShell;

        if (_pickerMode === 'inline-replace') {
            if (_pickerIsCol) {
                // Delete the slash command text and insert HTML/Action
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(_pickerRange);
                document.execCommand('delete', false, null);
                
                if (type === 'image') {
                    var fi = document.createElement('input');
                    fi.type = 'file'; fi.accept = 'image/*';
                    fi.onchange = function() { uploadInlineImageElFinder(fi.files[0], _pickerRange); };
                    fi.click();
                } else if (type === 'paragraph') {
                    document.execCommand('insertHTML', false, '<p><br></p>');
                } else if (type === 'h1' || type === 'h2' || type === 'h3') {
                    document.execCommand('formatBlock', false, type);
                } else if (type === 'divider') {
                    document.execCommand('insertHTML', false, '<hr class="my-4">');
                } else if (type === 'ul') {
                    document.execCommand('insertUnorderedList', false, null);
                } else if (type === 'ol') {
                    document.execCommand('insertOrderedList', false, null);
                } else if (type === 'button') {
                    document.execCommand('insertHTML', false, '<div class="text-center my-3"><a href="#" class="btn btn-primary">Button Text</a></div>');
                } else if (type === 'icon') {
                    document.execCommand('insertHTML', false, '<div class="text-center my-3"><i class="bi bi-star" style="font-size:2rem; color:#6366f1;"></i></div>');
                } else if (type === 'card') {
                    document.execCommand('insertHTML', false, '<div class="card my-3"><div class="card-body">Nested Card Content</div></div>');
                } else if (type === 'quote') {
                    document.execCommand('formatBlock', false, 'blockquote');
                }
            } else {
                // Delete the block that triggered slash and insert new type at same position
                var idx = state.blocks.findIndex(function(b){ return b.id === _pickerAfter; });
                if (idx !== -1) {
                    state.blocks.splice(idx, 1); // remove old one
                    // insert at the exact same index where the old one was
                    // if it was index 0, we add at start of what's left
                    var afterThatPrev = idx > 0 ? state.blocks[idx - 1].id : null;
                    var pos = idx === 0 ? 'start' : 'after';
                    insertBlock(state, type, afterThatPrev, pos);
                }
            }
        } else if (_pickerMode === 'before') {
            insertBlock(state, type, _pickerBefore, 'before');
        } else {
            insertBlock(state, type, _pickerAfter);
        }
    }

    function handlePickerKey(e, state, blockId) {
        var list = document.querySelectorAll('.gxb-picker-item');
        var active = document.querySelector('.gxb-picker-item.active');
        var idx = active ? [].indexOf.call(list, active) : 0;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            list.forEach(function(el){ el.classList.remove('active'); });
            var next = list[Math.min(idx + 1, list.length - 1)];
            if (next) { next.classList.add('active'); next.scrollIntoView({block:'nearest'}); }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            list.forEach(function(el){ el.classList.remove('active'); });
            var prev = list[Math.max(idx - 1, 0)];
            if (prev) { prev.classList.add('active'); prev.scrollIntoView({block:'nearest'}); }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (active) selectPickerItem(active.dataset.type);
        } else if (e.key === 'Escape') {
            closePicker();
        }
    }

    function getPickerAnchor(el) {
        var r = el.getBoundingClientRect();
        // Since picker is position: fixed, we DO NOT add window.scrollY
        return { top: r.bottom + 4, left: r.left };
    }

    // ── Picker search input & global UI setup ─────────────────────────
    // NOTE: called from boot() directly — not via DOMContentLoaded,
    // because the script is injected in the footer (DOM already ready).
    function initGlobalUI() {
        var search = document.getElementById('gxb-picker-search');
        if (search) {
            search.addEventListener('input', function() { filterPicker(this.value); });
            search.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    var active = document.querySelector('.gxb-picker-item.active');
                    if (active) { e.preventDefault(); selectPickerItem(active.dataset.type); }
                }
                if (e.key === 'Escape') closePicker();
            });
        }

        // Inline toolbar buttons
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
                        var sel = window.getSelection();
                        if (!sel.rangeCount) return;
                        var range = sel.getRangeAt(0).cloneRange();
                        range.collapse(false);
                        var fi = document.createElement('input');
                        fi.type = 'file'; fi.accept = 'image/*';
                        fi.addEventListener('change', function() {
                            uploadInlineImageElFinder(fi.files[0], range);
                        });
                        fi.click();
                    } else if (cmd === 'icon_list' || cmd === 'table' || cmd === 'grid2' || cmd === 'toc') {
                        // Custom block commands from toolbar
                        var state = findCurrentState(btn);
                        if (state) insertBlock(state, cmd, null);
                    } else {
                        document.execCommand(cmd, false, null);
                    }
                    setTimeout(hideInlineToolbar, 100);
                });
            });
        }

        // Image Context select handlers
        ['width', 'align', 'style'].forEach(function(prop) {
            var sel = document.getElementById('gxb-prop-' + prop);
            if (sel) {
                sel.addEventListener('change', function() {
                    if (_activeImgBlock) {
                        if (prop === 'width') _activeImgBlock.imgWidth = this.value;
                        if (prop === 'align') _activeImgBlock.imgAlign = this.value;
                        if (prop === 'style') _activeImgBlock.imgStyle = this.value;
                        refreshImageBlock(_activeImgState, _activeImgBlock);
                        serializeToTextarea(_activeImgState);
                    } else if (_activeImgEl) {
                        var el = _activeImgEl;
                        if (prop === 'width') {
                            ['w-25', 'w-50', 'w-75', 'w-100'].forEach(function(c){ el.classList.remove(c); });
                            if (this.value) el.classList.add(this.value);
                        } else if (prop === 'align') {
                            ['float-start', 'float-end', 'mx-auto', 'd-block'].forEach(function(c){ el.classList.remove(c); });
                            if (this.value) this.value.split(' ').forEach(function(c){ el.classList.add(c); });
                        } else if (prop === 'style') {
                            ['rounded', 'img-thumbnail', 'rounded-circle'].forEach(function(c){ el.classList.remove(c); });
                            if (this.value) el.classList.add(this.value);
                        }
                        updateParentBlockState(_activeImgEl);
                    }
                });
            }
        });

        // Post Context Handler
        var postSave = document.getElementById('gxb-post-save');
        if (postSave) {
            postSave.addEventListener('click', function() {
                if (_activePostBlock) {
                    _activePostBlock.postId = document.getElementById('gxb-prop-post-id').value;
                    renderAllBlocks(_activePostState);
                }
                closePostContext();
            });
        }

        // Delete Image button listener
        var delImgBtn = document.getElementById('gxb-btn-img-del');
        if (delImgBtn) {
            delImgBtn.addEventListener('click', function() {
                if (_activeImgBlock) {
                    deleteBlock(_activeImgState, _activeImgBlock.id);
                } else if (_activeImgEl) {
                    _activeImgEl.remove();
                }
                closeImageContext();
            });
        }

        // Grid Context select handlers
        var ratioSel = document.getElementById('gxb-prop-ratio');
        if (ratioSel) {
            ratioSel.addEventListener('change', function() {
                if (_activeGridBlock) {
                    _activeGridBlock.colRatio = this.value;
                    renderAllBlocks(_activeGridState);
                }
                closeGridContext();
            });
        }
        var countSel = document.getElementById('gxb-prop-grid-count');
        if (countSel) {
            countSel.addEventListener('change', function() {
                if (_activeGridBlock) {
                    _activeGridBlock.colCount = parseInt(this.value);
                    renderAllBlocks(_activeGridState);
                    openGridContext({ clientX: parseInt(document.getElementById('gxb-grid-context').style.left), clientY: parseInt(document.getElementById('gxb-grid-context').style.top) }, _activeGridState, _activeGridBlock);
                }
            });
        }
        var rowSel = document.getElementById('gxb-prop-grid-rows');
        if (rowSel) {
            rowSel.addEventListener('change', function() {
                if (_activeGridBlock) {
                    _activeGridBlock.rowCount = parseInt(this.value);
                    renderAllBlocks(_activeGridState);
                    openGridContext({ clientX: parseInt(document.getElementById('gxb-grid-context').style.left), clientY: parseInt(document.getElementById('gxb-grid-context').style.top) }, _activeGridState, _activeGridBlock);
                }
            });
        }
        var gridSave = document.getElementById('gxb-grid-save');
        if (gridSave) {
            gridSave.addEventListener('click', function() {
                if (_activeGridBlock) {
                    _activeGridBlock.colCount = parseInt(document.getElementById('gxb-prop-grid-count').value);
                    _activeGridBlock.rowCount = parseInt(document.getElementById('gxb-prop-grid-rows').value);
                    _activeGridBlock.colRatio = document.getElementById('gxb-prop-ratio').value;
                    renderAllBlocks(_activeGridState);
                    serializeToTextarea(_activeGridState);
                }
                closeGridContext();
            });
        }

        // TOC Context Handler
        var tocSave = document.getElementById('gxb-toc-save');
        if (tocSave) {
            tocSave.addEventListener('click', function() {
                if (_activeTocBlock) {
                    _activeTocBlock.tocTitle = document.getElementById('gxb-prop-toc-title').value;
                    _activeTocBlock.tocFloat = document.getElementById('gxb-prop-toc-float').value;
                    _activeTocBlock.tocWidth = document.getElementById('gxb-prop-toc-width').value;
                    _activeTocBlock.tocCollapse = document.getElementById('gxb-prop-toc-collapse').value;
                    renderAllBlocks(_activeTocState);
                    serializeToTextarea(_activeTocState);
                }
                closeTocContext();
            });
        }

        // Icon Context Handler
        var iconSave = document.getElementById('gxb-icon-save');
        if (iconSave) {
            iconSave.addEventListener('click', function() {
                if (_activeIconBlock) {
                    _activeIconBlock.iconClass = document.getElementById('gxb-prop-icon-class').value;
                    _activeIconBlock.iconSize  = document.getElementById('gxb-prop-icon-size').value;
                    _activeIconBlock.iconColor = document.getElementById('gxb-prop-icon-color').value;
                    renderAllBlocks(_activeIconState);
                    serializeToTextarea(_activeIconState);
                }
                closeIconContext();
            });
        }

        // Text Context Handler
        var textSave = document.getElementById('gxb-text-save');
        if (textSave) {
            textSave.addEventListener('click', function() {
                if (_activeTextBlock && _activeTextEl) {
                    _activeTextBlock.textAlign = document.getElementById('gxb-prop-text-align').value;
                    _activeTextBlock.lineHeight = document.getElementById('gxb-prop-text-lineheight').value;
                    var contentEl = _activeTextEl.closest('.gxb-block').querySelector('.gxb-content');
                    if (contentEl) {
                        contentEl.style.textAlign = _activeTextBlock.textAlign;
                        contentEl.style.lineHeight = _activeTextBlock.lineHeight;
                    }
                    serializeToTextarea(_activeTextState);
                }
                closeTextContext();
            });
        }

        // Table Context Handlers
        var tableBtnMap = {
            'gxb-table-add-row': function(){
                var row = _activeTableEl.closest('tr');
                var newRow = row.cloneNode(true);
                newRow.querySelectorAll('td, th').forEach(function(c){ c.innerHTML = ''; });
                row.parentNode.insertBefore(newRow, row.nextSibling);
            },
            'gxb-table-add-col': function(){
                var tbl = _activeTableEl.closest('table');
                var colIdx = _activeTableEl.cellIndex;
                [].slice.call(tbl.rows).forEach(function(r){
                    var newCell = r.cells[colIdx].cloneNode(true);
                    newCell.innerHTML = '';
                    r.insertBefore(newCell, r.cells[colIdx].nextSibling);
                });
            },
            'gxb-table-del-row': function(){
                var row = _activeTableEl.closest('tr');
                if (row.parentNode.rows.length > 1) row.remove();
            },
            'gxb-table-del-col': function(){
                var tbl = _activeTableEl.closest('table');
                var colIdx = _activeTableEl.cellIndex;
                if (tbl.rows[0].cells.length > 1) {
                    [].slice.call(tbl.rows).forEach(function(r){ r.deleteCell(colIdx); });
                }
            }
        };
        Object.keys(tableBtnMap).forEach(function(id){
            var btn = document.getElementById(id);
            if (btn) btn.addEventListener('click', function(){
                if (_activeTableEl) {
                    tableBtnMap[id]();
                    _activeTableBlock.content = _activeTableEl.closest('table').innerHTML;
                    serializeToTextarea(_activeTableState);
                }
                closeTableContext();
            });
        });
        ['border', 'striped'].forEach(function(p){
            var sel = document.getElementById('gxb-prop-table-' + p);
            if (sel) sel.addEventListener('change', function(){
                if (_activeTableBlock) {
                    var key = 'table' + p.charAt(0).toUpperCase() + p.slice(1);
                    _activeTableBlock[key] = this.value;
                    renderAllBlocks(_activeTableState);
                    serializeToTextarea(_activeTableState);
                }
            });
        });

        // Icon List Context Handler
        var iconListSave = document.getElementById('gxb-iconlist-save');
        if (iconListSave) {
            iconListSave.addEventListener('click', function() {
                if (_activeIconListBlock) {
                    _activeIconListBlock.listIcon = document.getElementById('gxb-prop-iconlist-class').value;
                    _activeIconListBlock.listColor = document.getElementById('gxb-prop-iconlist-color').value;
                    renderAllBlocks(_activeIconListState);
                    serializeToTextarea(_activeIconListState);
                }
                closeIconListContext();
            });
        }
    }

    function updateParentBlockState(el) {
        var state = findCurrentState(el);
        var blockEl = el.closest('.gxb-block');
        if (!state || !blockEl) return;
        var block = findBlockByEl(state, blockEl);
        if (!block) return;

        if (block.type === 'grid2' || block.type === 'grid2x2') {
             blockEl.querySelectorAll('.gxb-col').forEach(function(col, i) {
                  block['col' + (i+1)] = col.innerHTML;
             });
        } else if (block.type === 'card') {
             var h = blockEl.querySelector('.card-header');
             var b = blockEl.querySelector('.card-body');
             var f = blockEl.querySelector('.card-footer');
             if (h) block.header = h.innerHTML;
             if (b) block.content = b.innerHTML;
             if (f) block.footer = f.innerHTML;
        } else {
             var activeContent = blockEl.querySelector('.gxb-content');
             if (activeContent) {
                 block.content = (block.type === 'code') ? activeContent.textContent : activeContent.innerHTML;
             }
        }
        serializeToTextarea(state);
    }

    // Central Context Menu Manager is moved here for consistency

    // Unified Context Menu System
    document.addEventListener('contextmenu', function(e) {
        var targetEl = e.target;
        if (targetEl.nodeType === 3) targetEl = targetEl.parentElement; // text nodes
        
        var shell = targetEl.closest('.gxb-shell');
        if (!shell) return;
        
        closeAllContextMenus();
        var state = findCurrentState(shell);
        if (!state) return;

        // Find the block first
        var blockEl = targetEl.closest('.gxb-block');
        if (!blockEl) return;
        var block = findBlockByEl(state, blockEl);
        if (!block) return;
        
        // Select the block on right-click for delete action
        state.shell.querySelectorAll('.gxb-block').forEach(function(el){ el.classList.remove('gxb-selected'); });
        blockEl.classList.add('gxb-selected');

        // 1. Check for Images & Icons (Highest priority)
        var isIconBlock = (block.type === 'icon');
        if (targetEl.tagName === 'IMG' || (targetEl.tagName === 'I' && isIconBlock) || isIconBlock) {
            e.preventDefault(); e.stopPropagation();
            if (targetEl.tagName === 'IMG') {
                openImageContext(e, state, block, targetEl);
                return;
            }
            if (isIconBlock) {
                openIconContext(e, state, block);
                return;
            }
        }

        // 2. Check for Buttons
        var btn = targetEl.closest('.btn');
        if (btn && blockEl.contains(btn)) {
            e.preventDefault(); e.stopPropagation();
            openButtonContext(e, state, block, btn);
            return;
        }

        // 3. Block Specific and Standard Text Blocks
        var type = block.type;
        e.preventDefault(); e.stopPropagation(); // Standard prevention for block items

        if (type === 'grid2' || type === 'grid2x2') { openGridContext(e, state, block); return; }
        if (type === 'card') { openCardContext(e, state, block); return; }
        if (type === 'single_post') { openPostContext(e, state, block); return; }
        if (type === 'toc') { openTocContext(e, state, block); return; }
        if (type === 'icon_list') { openIconListContext(e, state, block); return; }
        if (type === 'table') { 
            var td = targetEl.closest('td, th');
            if (td) { openTableContext(e, state, block, td); return; }
        }
        
        if (['paragraph','h1','h2','h3','quote','code','ul','ol'].indexOf(type) !== -1) {
            openTextContext(e, state, block, targetEl);
            return;
        }
    });

    // Close picker + toolbar + context on outside click
    document.addEventListener('mousedown', function(e) {
        // Hide picker if clicking outside
        if (!e.target.closest('#gxb-picker') && !e.target.closest('.gxb-add-inline') && !e.target.closest('.gxb-addbtn')) {
            closePicker();
        }
        
        // Hide inline toolbar if clicking outside it
        if (!e.target.closest('#gxb-inline-toolbar')) {
            hideInlineToolbar();
        }

        // Close context menus on left click outside them
        if (e.button !== 2 && !e.target.closest('.gxb-context-menu')) {
             // We need to identify context menus by a class or similar
             // For now, closeAllContextMenus is safe if we don't click inside them
             if (!e.target.closest('[id$="-context"]')) {
                 closeAllContextMenus();
             }
        }
    });

    function findCurrentState(el) {
        var shell = el.closest('.gxb-shell');
        if (!shell) return null;
        for (var i = 0; i < editors.length; i++) {
            if (editors[i].shell === shell) return editors[i];
        }
        return null;
    }

    function findBlockByEl(state, el) {
        var wrap = el.closest('.gxb-block');
        if (!wrap) return null;
        var id = wrap.dataset.blockId;
        for (var i = 0; i < state.blocks.length; i++) {
            if (state.blocks[i].id == id) return state.blocks[i];
        }
        return null;
    }


    // ── Serialize blocks → HTML → textarea ────────────────────────────
    // ── Serialization & Parsing Helpers ───────────────────────────────
    function htmlToShortcode(html) {
        if (!html) return '';
        var div = document.createElement('div');
        div.innerHTML = html;
        div.querySelectorAll('img').forEach(function(img) {
            var cl = img.classList;
            var sc = '[image src="' + img.getAttribute('src') + '"';
            
            // Width
            if (cl.contains('w-25')) sc += ' width="w-25"';
            else if (cl.contains('w-50')) sc += ' width="w-50"';
            else if (cl.contains('w-75')) sc += ' width="w-75"';
            else if (cl.contains('w-100')) sc += ' width="w-100"';

            // Alignment
            if (cl.contains('float-start')) sc += ' align="float-start"';
            else if (cl.contains('float-end')) sc += ' align="float-end"';
            else if (cl.contains('mx-auto')) sc += ' align="mx-auto d-block"';

            // Style
            if (cl.contains('img-thumbnail')) sc += ' style="img-thumbnail"';
            else if (cl.contains('rounded-circle')) sc += ' style="rounded-circle"';
            else if (cl.contains('rounded')) sc += ' style="rounded"';
            else sc += ' style=""'; // Explicitly none

            // Alt & Caption
            var altText = img.getAttribute('alt') || '';
            var capText = '';
            var wrap = img.closest('.gx-image-wrap, .mb-3, .gx-image-rendered, .gxb-inline-img-wrap');
            var capSpan = wrap ? wrap.querySelector('.gxb-caption-text') : null;
            if (capSpan) capText = capSpan.textContent;

            sc += ' alt="' + altText + '"';
            sc += ' caption="' + capText + '"';
            sc += ']';
            
            if (wrap && wrap.querySelector('img') === img) {
                wrap.outerHTML = sc;
            } else {
                img.outerHTML = sc;
            }
        });
        return div.innerHTML;
    }

    function shortcodeToHtml(html) {
        if (!html) return '';
        // Convert [image src="..." width="..." align="..." style="..." alt="..." caption="..."]
        return html.replace(/\[image\b([^\]]*)\]/ig, function(match, attrStr) {
            var src = (attrStr.match(/src="([^"]*)"/) || [0, ''])[1];
            var w   = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '';
            var a   = (attrStr.match(/align="([^"]*)"/) || [0, ''])[1] || '';
            var s   = (attrStr.match(/style="([^"]*)"/) || [0, ''])[1] || 'rounded';
            var alt = (attrStr.match(/alt="([^"]*)"/) || [0, ''])[1] || '';
            var c   = (attrStr.match(/caption="([^"]*)"/) || [0, ''])[1] || '';

            var cls = 'img-fluid ' + s + ' ' + w + ' ' + a;
            var wrapCls = 'mb-1'; // use lighter spacing for inline wraps
            if (a === 'mx-auto d-block') wrapCls += ' text-center';
            if (a === 'float-start' || a === 'float-end') wrapCls += ' clearfix';

            if (c) {
                // Use SPAN instead of DIV to maintain valid HTML inside <p> or <li>
                var out = '<span class="gxb-inline-img-wrap d-block ' + wrapCls + '"><img src="' + src + '" class="' + cls + '" alt="' + alt + '">';
                out += '<span class="gxb-caption-text d-block text-muted small mt-1">' + c + '</span></span>';
                return out;
            } else {
                // If no caption, don't use a wrapper at all — less fragile
                return '<img src="' + src + '" class="' + cls + '" alt="' + alt + '">';
            }
        });
    }

    function serializeToTextarea(state) {
        if (!state || !state.textarea) return;
        var html = '';
        state.blocks.forEach(function(b) {
            var typeDef = GxEditor._blocks[b.type];
            if (typeDef && typeDef.serialize && typeof typeDef.serialize === 'function') {
                html += typeDef.serialize(b) + '\n';
                return;
            }
            switch (b.type) {
                case 'paragraph': 
                    var style = '';
                    if (b.textAlign) style += 'text-align:' + b.textAlign + ';';
                    if (b.lineHeight) style += 'line-height:' + b.lineHeight + ';';
                    var styleAttr = style ? ' style="' + style + '"' : '';
                    html += '<p' + styleAttr + '>' + htmlToShortcode(b.content || '<br>') + '</p>\n'; 
                    break;
                case 'h1': 
                case 'h2': 
                case 'h3': 
                    var style = '';
                    if (b.textAlign) style += 'text-align:' + b.textAlign + ';';
                    if (b.lineHeight) style += 'line-height:' + b.lineHeight + ';';
                    var styleAttr = style ? ' style="' + style + '"' : '';
                    html += '<' + b.type + styleAttr + '>' + b.content + '</' + b.type + '>\n'; 
                    break;
                case 'quote': html += '<blockquote><p>' + htmlToShortcode(b.content) + '</p></blockquote>\n'; break;
                case 'code':  html += '<pre><code>' + escHtml(b.content) + '</code></pre>\n'; break;
                case 'ul':
                    var items_u = b.content.indexOf('<li>') !== -1 ? b.content : b.content.split('\n').map(function(l){ return '<li>' + htmlToShortcode(l) + '</li>'; }).join('');
                    html += '<ul>' + items_u + '</ul>\n'; break;
                case 'ol':
                    var items_o = b.content.indexOf('<li>') !== -1 ? b.content : b.content.split('\n').map(function(l){ return '<li>' + htmlToShortcode(l) + '</li>'; }).join('');
                    html += '<ol>' + items_o + '</ol>\n'; break;
                case 'image':
                    if (b.content) {
                        var sc = '[image';
                        sc += ' src="' + b.content + '"';
                        sc += ' width="' + (b.imgWidth || '') + '"';
                        sc += ' align="' + (b.imgAlign || '') + '"';
                        sc += ' style="' + (b.imgStyle || 'rounded') + '"';
                        sc += ' caption="' + escHtml(b.caption || '') + '"';
                        sc += ']';
                        html += sc + '\n';
                    }
                    break;
                case 'icon_list':
                    var sc = '[icon_list';
                    sc += ' icon="' + (b.listIcon || 'bi bi-check2-circle') + '"';
                    sc += ' color="' + (b.listColor || '#6366f1') + '"';
                    sc += ']';
                    var items = b.content.split('\n').map(function(it){ return '<li>' + it + '</li>'; }).join('\n');
                    html += sc + '\n' + items + '\n[/icon_list]\n';
                    break;
                case 'table':
                    var sc = '[table';
                    sc += ' border="' + (b.tableBorder || 'yes') + '"';
                    sc += ' striped="' + (b.tableStriped || 'no') + '"';
                    sc += ']';
                    html += sc + '\n' + b.content + '\n[/table]\n';
                    break;
                case 'grid2':
                case 'grid2x2':
                    var rows  = b.rowCount || (b.type === 'grid2x2' ? 2 : 1);
                    var cols  = b.colCount || 2;
                    var r     = (b.colRatio || '6:6').split(':');
                    var bsCol = Math.floor(12 / cols);
                    
                    for (var rowIdx = 0; rowIdx < rows; rowIdx++) {
                        html += '<div class="row gx-3 gy-3 mb-1">\n';
                        for (var colIdx = 0; colIdx < cols; colIdx++) {
                            var cellIdx = (rowIdx * cols) + (colIdx + 1);
                            var cVal = (cols == 2) ? r[colIdx] : bsCol;
                            var dataKey = 'col' + cellIdx;
                            
                            var cellContent = b[dataKey] || '';
                            if (state.shell) {
                                var innerTa = state.shell.querySelector('#gxb-inner-' + b.id + '-' + dataKey);
                                if (innerTa) {
                                    var innerState = editors.find(function(e){ return e.textarea === innerTa; });
                                    if (innerState) serializeToTextarea(innerState);
                                    cellContent = innerTa.value;
                                    b[dataKey] = cellContent; // Sync for next time
                                }
                            }
                            
                            html += '  <div class="col-12 col-md-' + cVal + '">' + htmlToShortcode(cellContent) + '</div>\n';
                        }
                        html += '</div>\n';
                    }
                    html += '<div class="mb-3"></div>\n';
                    break;
                case 'card': 
                    html += '<div class="card mb-3">\n';
                    if (b.hasHeader) html += '  <div class="card-header">' + htmlToShortcode(b.header || '') + '</div>\n';
                    
                    var bodyContent = b.content || '';
                    if (state.shell) {
                        var innerTa = state.shell.querySelector('#gxb-inner-' + b.id + '-content');
                        if (innerTa) {
                            var innerState = editors.find(function(e){ return e.textarea === innerTa; });
                            if (innerState) serializeToTextarea(innerState);
                            bodyContent = innerTa.value;
                            b.content = bodyContent; // Sync for next time
                        }
                    }
                    
                    html += '  <div class="card-body">\n    ' + htmlToShortcode(bodyContent) + '\n  </div>\n';
                    if (b.hasFooter) html += '  <div class="card-footer">' + htmlToShortcode(b.footer || '') + '</div>\n';
                    html += '</div>\n';
                    break;
                case 'button':
                    var target = (b.btnUrl && b.btnUrl.startsWith('http')) ? ' target="_blank"' : '';
                    html += '<div class="mb-3 text-center"><a href="' + (b.btnUrl||'#') + '" class="btn ' + (b.btnClass||'btn-primary') + '"' + target + '>' + escHtml(b.content||'Click Here') + '</a></div>\n';
                    break;
                case 'icon':
                    var sc = '[icon';
                    sc += ' class="' + (b.iconClass || 'bi bi-star') + '"';
                    sc += ' size="' + (b.iconSize || '2.5rem') + '"';
                    sc += ' color="' + (b.iconColor || '#6366f1') + '"';
                    sc += ']';
                    html += '<div class="gx-icon-block mb-3 text-center">' + sc + '</div>\n';
                    break;
                case 'single_post':
                    html += '<div class="gx-single-post mb-4" data-postid="' + (b.content || '0') + '">[post id="' + (b.content || '0') + '"]</div>\n';
                    break;
                case 'toc':
                    var sc = '[toc';
                    sc += ' title="' + (b.tocTitle || 'Daftar Isi') + '"';
                    sc += ' float="' + (b.tocFloat || 'none') + '"';
                    sc += ' width="' + (b.tocWidth || '450px') + '"';
                    sc += ' collapse="' + (b.tocCollapse || 'no') + '"';
                    sc += ']';
                    html += '<div class="gx-toc mb-4">' + sc + '</div>\n';
                    break;
                case 'recent_posts':
                    html += '<div class="gx-recent-posts mb-3">[recent_posts count="5"]</div>\n'; break;
                case 'random_posts':
                    html += '<div class="gx-random-posts mb-3">[random_posts count="5"]</div>\n'; break;
                case 'divider': html += '<hr class="my-4">\n'; break;
            }
        });
        state.textarea.value = html;
    }

    function escHtml(s) {
        return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    function splitLines(s) {
        return (s||'').split(/\n+/).filter(Boolean);
    }

    // Setup context menu delete buttons
    var deleteHanders = [
        { btnId: 'gxb-img-delete', getState: function(){return _activeImgState;}, getBlock: function(){return _activeImgBlock;} },
        { btnId: 'gxb-btn-delete', getState: function(){return _activeBtnState;}, getBlock: function(){return _activeBtnBlock;} },
        { btnId: 'gxb-card-delete', getState: function(){return _activeCardState;}, getBlock: function(){return _activeCardBlock;} },
        { btnId: 'gxb-grid-delete', getState: function(){return _activeGridState;}, getBlock: function(){return _activeGridBlock;} },
        { btnId: 'gxb-icon-delete', getState: function(){return _activeIconState;}, getBlock: function(){return _activeIconBlock;} },
        { btnId: 'gxb-post-delete', getState: function(){return _activePostState;}, getBlock: function(){return _activePostBlock;} },
        { btnId: 'gxb-toc-delete', getState: function(){return _activeTocState;}, getBlock: function(){return _activeTocBlock;} },
        { btnId: 'gxb-text-delete', getState: function(){return _activeTextState;}, getBlock: function(){return _activeTextBlock;} },
        { btnId: 'gxb-table-delete', getState: function(){return _activeTableState;}, getBlock: function(){return _activeTableBlock;} },
        { btnId: 'gxb-iconlist-delete', getState: function(){return _activeIconListState;}, getBlock: function(){return _activeIconListBlock;} }
    ];

    deleteHanders.forEach(function(item) {
        var btn = document.getElementById(item.btnId);
        if (btn) {
            btn.addEventListener('click', function() {
                if (confirm('Delete this block?')) {
                    var state = item.getState();
                    var block = item.getBlock();
                    if (state && block) {
                        deleteBlock(state, block.id);
                    }
                    closeAllContextMenus();
                }
            });
        }
    });

    // ── Caret helpers ──────────────────────────────────────────────────
    function placeCaretAtEnd(el) {
        var range = document.createRange();
        var sel   = window.getSelection();
        range.selectNodeContents(el);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
    }
    function placeCaretAtStart(el) {
        var range = document.createRange();
        var sel   = window.getSelection();
        range.setStart(el, 0);
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
    }

    // ── Boot ───────────────────────────────────────────────────────────

    // Initialize boot on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

})(window);
