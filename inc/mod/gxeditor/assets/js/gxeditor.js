(function(window) {
    'use strict';

    // ── GxEditor Global API ───────────────────────────────────────────
    var GxEditor = {
        _blocks: {},
        _editors: [], // Track all initialized instances
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
        },
        getBlocksExclude: function(excluded) {
            var filtered = [];
            for (var id in this._blocks) {
                if (excluded.indexOf(id) === -1) {
                    var b = this._blocks[id];
                    filtered.push({ id: id, icon: b.icon, label: b.label, desc: b.desc });
                }
            }
            return filtered;
        }
    };
    window.GxEditor = GxEditor;

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

    // ── Boot sequence ──────────────────────────────────────────────────
    var _uiInitialized = false;
    function boot() {
        if (!_uiInitialized && typeof window.initGlobalUI === 'function') {
            window.initGlobalUI();
            _uiInitialized = true;
        }

        document.querySelectorAll('textarea.editor').forEach(function(ta) {
            if (ta.dataset.gxbInit) return;
            ta.dataset.gxbInit = '1';
            window.initShell(ta);
        });

        document.querySelectorAll('form').forEach(function(form) {
            if (form.dataset.gxbInit) return;
            form.dataset.gxbInit = '1';
            form.addEventListener('submit', function() {
                GxEditor._editors.forEach(function(e) { if(typeof serializeToTextarea === 'function') serializeToTextarea(e); });
            });
        });

        setInterval(function() {
            GxEditor._editors.forEach(function(e) { if(typeof serializeToTextarea === 'function') serializeToTextarea(e); });
        }, 2000);
    }
    window.GxEditor.boot = boot;

    /**
     * Initializes a block editor shell for a textarea
     */
    window.initShell = function(textarea) {
        var shell = document.createElement('div');
        shell.className = 'gxb-shell';
        shell.dataset.editorId = textarea.id || ('gxb-' + Date.now());
        textarea.parentNode.insertBefore(shell, textarea);
        textarea.style.display = 'none';

        var state = { 
            textarea: textarea, 
            shell: shell, 
            blocks: [],
            isNested: textarea.classList.contains('gxb-nested-editor')
        };

        GxEditor._editors.push(state);

        // Parse existing HTML into blocks
        var htmlToParse = textarea.value || textarea.dataset.gxcontent || '';
        if (typeof parseHTML === 'function') parseHTML(htmlToParse, state);

        // Add initial paragraph if empty
        if (!state.blocks.length) {
            if (typeof addBlock === 'function') addBlock(state, 'paragraph', '', null);
        }

        if (typeof renderAllBlocks === 'function') renderAllBlocks(state);
        
        // Add-block button at bottom
        var addWrap = document.createElement('div');
        addWrap.className = 'gxb-addbtn-wrap';
        addWrap.innerHTML = '<button class="gxb-addbtn" type="button"><i class="bi bi-plus-circle me-2"></i>Add Block</button>';
        addWrap.querySelector('.gxb-addbtn').addEventListener('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            if (typeof openPicker === 'function') {
                var btn = this;
                var r = btn.getBoundingClientRect();
                openPicker(state, null, { top: r.top, left: r.left });
            }
        });
        shell.appendChild(addWrap);
        
        // ── Keyboard Shortcuts ───────────────────────────────────────────
        shell.addEventListener('keydown', function(e) {
            if (e.key === '/' && !e.ctrlKey && !e.metaKey) {
                var sel = window.getSelection();
                if (sel.rangeCount) {
                    var range = sel.getRangeAt(0);
                    var node = range.commonAncestorContainer;
                    if (node.nodeType === 3) node = node.parentNode;
                    var contentEl = node.closest('.gxb-content');
                    if (contentEl && contentEl.textContent.trim() === '') {
                        e.preventDefault();
                        var bEl = contentEl.closest('.gxb-block');
                        var r = contentEl.getBoundingClientRect();
                        if (typeof openPicker === 'function') {
                            openPicker(state, bEl ? bEl.dataset.blockId : null, { top: r.top, left: r.left });
                        }
                    }
                }
            }
        });
    };

    // ── Global Initializer ───────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

})(window);
