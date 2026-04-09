(function (window) {
    'use strict';

    // ── GxEditor Global API ───────────────────────────────────────────
    var GxEditor = {
        _blocks: {},
        _editors: [], // Track all initialized instances
        registerBlock: function (id, config) {
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
        getBlocksExclude: function (excluded) {
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
        { id: 'paragraph', icon: 'bi bi-paragraph', label: 'Paragraph', desc: 'Plain text', placeholder: 'Start typing...' },
        { id: 'h1', icon: 'bi bi-type-h1', label: 'Heading 1', desc: 'Large title', placeholder: 'Heading 1' },
        { id: 'h2', icon: 'bi bi-type-h2', label: 'Heading 2', desc: 'Medium heading', placeholder: 'Heading 2' },
        { id: 'h3', icon: 'bi bi-type-h3', label: 'Heading 3', desc: 'Small heading', placeholder: 'Heading 3' },
        { id: 'quote', icon: 'bi bi-quote', label: 'Quote', desc: 'Blockquote', placeholder: 'Enter quote text...' },
        { id: 'code', icon: 'bi bi-code-slash', label: 'Code', desc: 'Code block', placeholder: '// Enter code here...' },
        { id: 'ul', icon: 'bi bi-list-ul', label: 'Bullet List', desc: 'Unordered list', placeholder: 'List item' },
        { id: 'ol', icon: 'bi bi-list-ol', label: 'Numbered List', desc: 'Ordered list', placeholder: 'List item' },
        { id: 'image', icon: 'bi bi-image', label: 'Image', desc: 'Upload or embed', placeholder: '' },
        { id: 'button', icon: 'bi bi-hand-index', label: 'Button', desc: 'Interactive button', placeholder: 'Button Text' },
        { id: 'grid2', icon: 'bi bi-layout-split', label: '2 Columns', desc: 'Side-by-side grid', placeholder: '' },
        { id: 'grid2x2', icon: 'bi bi-grid-fill', label: '2x2 Grid', desc: '4 cells layout', placeholder: '' },
        { id: 'card', icon: 'bi bi-card-text', label: 'Card', desc: 'Boxed content container', placeholder: 'Card body...' },
        { id: 'icon', icon: 'bi bi-star', label: 'Icon', desc: 'Bootstrap/FA icon', placeholder: '' },
        { id: 'divider', icon: 'bi bi-dash-lg', label: 'Divider', desc: 'Horizontal separator', placeholder: '' },
        { id: 'single_post', icon: 'bi bi-card-checklist', label: 'Single Post', desc: 'Display a specific post', placeholder: '' },
        { id: 'toc', icon: 'bi bi-list-nested', label: 'Table of Contents', desc: 'Auto-generate from H1-H4', placeholder: '' },
        { id: 'icon_list', icon: 'bi bi-check2-square', label: 'Icon List', desc: 'List with custom icons', placeholder: 'List item' },
        { id: 'table', icon: 'bi bi-table', label: 'Table', desc: 'Data grid table', placeholder: '' },
        { id: 'recent_posts', icon: 'bi bi-clock-history', label: 'Recent Posts', desc: 'Dynamic recent list', placeholder: '' },
        { id: 'random_posts', icon: 'bi bi-shuffle', label: 'Random Posts', desc: 'Dynamic random list', placeholder: '' },
        { id: 'pricing', icon: 'bi bi-tags', label: 'Pricing Table', desc: 'Comparison with switch', placeholder: '' },
    ];
    CORE_BLOCKS.forEach(function (b) { GxEditor.registerBlock(b.id, b); });

    // ── Boot sequence ──────────────────────────────────────────────────
    var _uiInitialized = false;
    function boot() {
        if (!_uiInitialized && typeof window.initGlobalUI === 'function') {
            window.initGlobalUI();
            _uiInitialized = true;
        }

        document.querySelectorAll('textarea.editor').forEach(function (ta) {
            if (ta.dataset.gxbInit) return;
            ta.dataset.gxbInit = '1';
            window.initShell(ta);
        });

        document.querySelectorAll('form').forEach(function (form) {
            if (form.dataset.gxbInit) return;
            form.dataset.gxbInit = '1';
            form.addEventListener('submit', function () {
                GxEditor._editors.forEach(function (e) { if (typeof serializeToTextarea === 'function') serializeToTextarea(e); });
            });
        });

        setInterval(function () {
            GxEditor._editors.forEach(function (e) { if (typeof serializeToTextarea === 'function') serializeToTextarea(e); });
        }, 2000);

        if (!document.getElementById('gx-katex-css')) {
            var kCss = document.createElement('link');
            kCss.id = 'gx-katex-css';
            kCss.rel = 'stylesheet';
            kCss.href = 'https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css';
            document.head.appendChild(kCss);
        }
        if (!document.getElementById('gx-katex-js')) {
            var kJs = document.createElement('script');
            kJs.id = 'gx-katex-js';
            kJs.src = 'https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js';
            document.head.appendChild(kJs);
        }
    }
    window.GxEditor.boot = boot;

    function getMathModal(initType, initCode, callback) {
        var modalEl = document.getElementById('gxeditorMathModal');
        if (!modalEl) {
            var mHtml = '<div class="modal fade" id="gxeditorMathModal" tabindex="-1" style="z-index: 999999 !important;">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h5 class="modal-title">Math Equation</h5>' +
                '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="mb-3">' +
                '<label class="form-label">Format</label>' +
                '<select class="form-select" id="gxeditorMathType">' +
                '<option value="latex">LaTeX</option>' +
                '<option value="mathml">MathML</option>' +
                '</select>' +
                '</div>' +
                '<div class="mb-3">' +
                '<label class="form-label">Formula / Code</label>' +
                '<textarea class="form-control" id="gxeditorMathCode" rows="6" placeholder="Enter LaTeX (e.g. x^2 = y) or MathML"></textarea>' +
                '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>' +
                '<button type="button" class="btn btn-primary" id="gxeditorMathSave">Save Equation</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            document.body.insertAdjacentHTML('beforeend', mHtml);
            modalEl = document.getElementById('gxeditorMathModal');

            document.getElementById('gxeditorMathSave').addEventListener('click', function () {
                var type = document.getElementById('gxeditorMathType').value;
                var code = document.getElementById('gxeditorMathCode').value;
                if (window._gxMathCallback) window._gxMathCallback(type, code);
                var bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
            });
        }
        document.getElementById('gxeditorMathType').value = initType || 'latex';
        document.getElementById('gxeditorMathCode').value = initCode || '';
        window._gxMathCallback = callback;
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    }

    function getTableModal(callback) {
        var modalEl = document.getElementById('gxeditorTableModal');
        if (!modalEl) {
            var mHtml = '<div class="modal fade" id="gxeditorTableModal" tabindex="-1" style="z-index: 999999 !important;">' +
                '<div class="modal-dialog modal-sm">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h5 class="modal-title">Insert Table</h5>' +
                '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="row g-2 mb-3">' +
                '<div class="col-6">' +
                '<label class="form-label">Rows</label>' +
                '<input type="number" class="form-control" id="gxeditorTableRows" value="3" min="1">' +
                '</div>' +
                '<div class="col-6">' +
                '<label class="form-label">Cols</label>' +
                '<input type="number" class="form-control" id="gxeditorTableCols" value="3" min="1">' +
                '</div>' +
                '</div>' +
                '<div class="form-check mb-2">' +
                '<input class="form-check-input" type="checkbox" id="gxeditorTableHeader" checked>' +
                '<label class="form-check-label" for="gxeditorTableHeader">Include Header</label>' +
                '</div>' +
                '<div class="form-check mb-2">' +
                '<input class="form-check-input" type="checkbox" id="gxeditorTableStriped">' +
                '<label class="form-check-label" for="gxeditorTableStriped">Striped Style</label>' +
                '</div>' +
                '<div class="form-check">' +
                '<input class="form-check-input" type="checkbox" id="gxeditorTableBordered" checked>' +
                '<label class="form-check-label" for="gxeditorTableBordered">Bordered Style</label>' +
                '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                '<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>' +
                '<button type="button" class="btn btn-primary btn-sm" id="gxeditorTableSave">Insert Table</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            document.body.insertAdjacentHTML('beforeend', mHtml);
            modalEl = document.getElementById('gxeditorTableModal');

            document.getElementById('gxeditorTableSave').addEventListener('click', function () {
                var rows = parseInt(document.getElementById('gxeditorTableRows').value) || 1;
                var cols = parseInt(document.getElementById('gxeditorTableCols').value) || 1;
                var header = document.getElementById('gxeditorTableHeader').checked;
                var striped = document.getElementById('gxeditorTableStriped').checked;
                var bordered = document.getElementById('gxeditorTableBordered').checked;
                
                if (window._gxTableCallback) window._gxTableCallback(rows, cols, header, striped, bordered);
                var bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
            });
        }
        window._gxTableCallback = callback;
        var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.show();
    }

    /**
     * Initializes a block editor shell for a textarea
     */
    window.initShell = function (textarea) {
        var isClassic = typeof GX_EDITOR_STYLE !== 'undefined' && GX_EDITOR_STYLE === 'classic';

        var shell = document.createElement('div');
        shell.className = 'gxb-shell' + (isClassic ? ' classic-mode' : '');
        shell.dataset.editorId = textarea.id || ('gxb-' + Date.now());
        textarea.parentNode.insertBefore(shell, textarea);
        textarea.style.display = 'none';

        var state = {
            textarea: textarea,
            shell: shell,
            blocks: [],
            isClassic: isClassic,
            isNested: textarea.classList.contains('gxb-nested-editor')
        };

        GxEditor._editors.push(state);

        if (isClassic) {
            var toolbarHtml = `
            <div class="gxb-classic-toolbar">
                <button type="button" data-cmd="undo" title="Undo"><i class="bi bi-arrow-counterclockwise"></i></button>
                <button type="button" data-cmd="redo" title="Redo"><i class="bi bi-arrow-clockwise"></i></button>
                <div class="gxb-tb-sep"></div>
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Format" style="width: auto; padding: 0 10px; font-size: 0.9rem;">
                        Format
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-cmd="formatBlock" data-val="P">Paragraph</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="formatBlock" data-val="H1"><strong>Heading 1</strong></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="formatBlock" data-val="H2"><strong>Heading 2</strong></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="formatBlock" data-val="H3"><strong>Heading 3</strong></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="formatBlock" data-val="H4"><strong>Heading 4</strong></a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-cmd="formatBlock" data-val="BLOCKQUOTE">Quote</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="formatBlock" data-val="PRE">Preformatted (&lt;pre&gt;)</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Font Family" style="width: auto; padding: 0 10px; font-size: 0.9rem;">
                        Font
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="Arial" style="font-family: Arial, sans-serif;">Arial</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="Helvetica" style="font-family: Helvetica, sans-serif;">Helvetica</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="'Times New Roman'" style="font-family: 'Times New Roman', serif;">Times New Roman</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="'Courier New'" style="font-family: 'Courier New', monospace;">Courier New</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="Verdana" style="font-family: Verdana, sans-serif;">Verdana</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="Georgia" style="font-family: Georgia, serif;">Georgia</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="Tahoma" style="font-family: Tahoma, sans-serif;">Tahoma</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontName" data-val="'Trebuchet MS'" style="font-family: 'Trebuchet MS', sans-serif;">Trebuchet MS</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Font Size" style="width: auto; padding: 0 10px; font-size: 0.9rem;">
                        Size
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-cmd="fontSize" data-val="1"><font size="1">Small (1)</font></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontSize" data-val="2"><font size="2">Normal (2)</font></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontSize" data-val="3"><font size="3">Medium (3)</font></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontSize" data-val="4"><font size="4">Large (4)</font></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontSize" data-val="5"><font size="5">X-Large (5)</font></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontSize" data-val="6"><font size="6">XX-Large (6)</font></a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="fontSize" data-val="7"><font size="7">Huge (7)</font></a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Line Height" style="width: auto; padding: 0 10px; font-size: 0.9rem;">
                        LH
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-cmd="lineHeightX" data-val="1">1.0 (Tight)</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="lineHeightX" data-val="1.2">1.2 (Compact)</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="lineHeightX" data-val="1.5">1.5 (Normal)</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="lineHeightX" data-val="1.8">1.8 (Relaxed)</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="lineHeightX" data-val="2.0">2.0 (Double)</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="lineHeightX" data-val="3.0">3.0 (Spaced)</a></li>
                    </ul>
                </div>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                <button type="button" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                <button type="button" data-cmd="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
                <button type="button" data-cmd="strikethrough" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>
                <button type="button" data-cmd="superscript" title="Superscript"><span style="font-size: 0.9em; font-weight: bold; font-family: serif;">x<sup>2</sup></span></button>
                <button type="button" data-cmd="subscript" title="Subscript"><span style="font-size: 0.9em; font-weight: bold; font-family: serif;">x<sub>2</sub></span></button>
                <button type="button" data-cmd="removeFormat" title="Clear Formatting"><i class="bi bi-eraser"></i></button>
                <div class="gxb-tb-sep"></div>
                <div class="gxb-color-picker" style="display: inline-flex; align-items: center; gap: 8px; padding: 0 8px;">
                    <label style="cursor: pointer; display: flex; align-items: center; margin: 0; gap: 2px;" title="Text Color">
                        <i class="bi bi-type" style="font-size: 0.9rem; color: #475569;"></i>
                        <input type="color" data-cmd-color="foreColor" style="width: 18px; height: 18px; border: 1px solid #ddd; padding: 0; background: none; cursor: pointer; border-radius: 2px; outline: none;">
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; margin: 0; gap: 2px;" title="Background Color">
                        <i class="bi bi-paint-bucket" style="font-size: 0.8rem; color: #475569;"></i>
                        <input type="color" data-cmd-color="backColor" value="#ffff00" style="width: 18px; height: 18px; border: 1px solid #ddd; padding: 0; background: none; cursor: pointer; border-radius: 2px; outline: none;">
                    </label>
                </div>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="justifyLeft" title="Align Left"><i class="bi bi-justify-left"></i></button>
                <button type="button" data-cmd="justifyCenter" title="Align Center"><i class="bi bi-text-center"></i></button>
                <button type="button" data-cmd="justifyRight" title="Align Right"><i class="bi bi-justify-right"></i></button>
                <button type="button" data-cmd="justifyFull" title="Justify Full"><i class="bi bi-justify"></i></button>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="insertUnorderedList" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                <button type="button" data-cmd="insertOrderedList" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                <button type="button" data-cmd="outdent" title="Decrease Indent"><i class="bi bi-text-indent-right"></i></button>
                <button type="button" data-cmd="indent" title="Increase Indent"><i class="bi bi-text-indent-left"></i></button>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="createLink" title="Insert Link"><i class="bi bi-link"></i></button>
                <button type="button" data-cmd="unlink" title="Remove Link"><i class="bi bi-link-45deg"></i><i class="bi bi-x"></i></button>
                <button type="button" data-cmd="insertImageGX" title="Insert Image"><i class="bi bi-image"></i></button>
                <button type="button" data-cmd="insertVideoGX" title="Insert Video"><i class="bi bi-camera-video"></i></button>
                <button type="button" data-cmd="codeBlockGX" title="Insert Code Block"><i class="bi bi-code-slash"></i></button>
                <button type="button" data-cmd="mathGX" title="Insert Math Equation"><span style="font-family: serif; font-weight: bold; font-size: 1rem; line-height: 1;">&sum;&radic;</span></button>
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Table" style="width: auto; padding: 0 10px;">
                        <i class="bi bi-table"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-cmd="table_insert"><i class="bi bi-plus-square me-2"></i>Insert Table...</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-cmd="table_row_add_above">Add Row Above</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="table_row_add_below">Add Row Below</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="table_col_add_left">Add Column Left</a></li>
                        <li><a class="dropdown-item" href="#" data-cmd="table_col_add_right">Add Column Right</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" data-cmd="table_row_del">Delete Row</a></li>
                        <li><a class="dropdown-item text-danger" href="#" data-cmd="table_col_del">Delete Column</a></li>
                        <li><a class="dropdown-item text-danger" href="#" data-cmd="table_del">Delete Table</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Special Characters" style="width: auto; padding: 0 10px;">
                        <span style="font-family: serif; font-weight: bold; font-size: 1.1rem; line-height: 1;">&Omega;</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2 pb-1" style="width: 240px;">
                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&copy;" style="width: 32px; height: 32px; padding: 0;">&copy;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&reg;" style="width: 32px; height: 32px; padding: 0;">&reg;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&trade;" style="width: 32px; height: 32px; padding: 0;">&trade;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&euro;" style="width: 32px; height: 32px; padding: 0;">&euro;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&pound;" style="width: 32px; height: 32px; padding: 0;">&pound;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&yen;" style="width: 32px; height: 32px; padding: 0;">&yen;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&sect;" style="width: 32px; height: 32px; padding: 0;">&sect;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&para;" style="width: 32px; height: 32px; padding: 0;">&para;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&deg;" style="width: 32px; height: 32px; padding: 0;">&deg;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&plusmn;" style="width: 32px; height: 32px; padding: 0;">&plusmn;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&ne;" style="width: 32px; height: 32px; padding: 0;">&ne;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&le;" style="width: 32px; height: 32px; padding: 0;">&le;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&ge;" style="width: 32px; height: 32px; padding: 0;">&ge;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&divide;" style="width: 32px; height: 32px; padding: 0;">&divide;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&times;" style="width: 32px; height: 32px; padding: 0;">&times;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&infin;" style="width: 32px; height: 32px; padding: 0;">&infin;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&micro;" style="width: 32px; height: 32px; padding: 0;">&micro;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&pi;" style="width: 32px; height: 32px; padding: 0;">&pi;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&alpha;" style="width: 32px; height: 32px; padding: 0;">&alpha;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&beta;" style="width: 32px; height: 32px; padding: 0;">&beta;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&Omega;" style="width: 32px; height: 32px; padding: 0;">&Omega;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&sum;" style="width: 32px; height: 32px; padding: 0;">&sum;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&radic;" style="width: 32px; height: 32px; padding: 0;">&radic;</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-cmd="insertHTML" data-val="&hearts;" style="width: 32px; height: 32px; padding: 0;">&hearts;</button>
                        </div>
                    </div>
                </div>
                <button type="button" data-cmd="insertHTML" data-val="<hr>" title="Insert Line"><i class="bi bi-dash-lg"></i></button>
                <div class="gxb-tb-sep"></div>
                <button type="button" data-cmd="sourceCodeGX" title="View/Edit Source Code" id="gxb-source-btn-${state.shell.dataset.editorId}" style="color: #6366f1; font-weight: bold;"><i class="bi bi-code-slash"></i></button>
            </div>
            `;

            shell.innerHTML = toolbarHtml;

            var contentWrap = document.createElement('div');
            contentWrap.className = 'gxb-classic-content-wrap gxb-content';
            contentWrap.contentEditable = 'true';

            var html = textarea.value || '<p><br></p>';
            if (typeof shortcodeToHtml === 'function') html = shortcodeToHtml(html);
            contentWrap.innerHTML = html;
            shell.appendChild(contentWrap);

            // Command logic
            shell.querySelectorAll('.gxb-classic-toolbar [data-cmd]').forEach(function (btn) {
                btn.addEventListener('mousedown', function (e) {
                    e.preventDefault(); // Prevent loss of focus
                    var cmd = btn.dataset.cmd;
                    var val = btn.dataset.val || null;

                    if (cmd === 'createLink') {
                        var url = prompt('Enter URL:');
                        if (url) document.execCommand(cmd, false, url);
                    } else if (cmd === 'insertImageGX') {
                        if (typeof window.elfinderDialog === 'function') {
                            var fakeContext = {
                                invoke: function (dummy, url) {
                                    contentWrap.focus(); document.execCommand('insertImage', false, url);
                                }
                            };
                            window.elfinderDialog(fakeContext);
                            return;
                        }
                        var url2 = prompt('Enter Image URL:');
                        if (url2) { contentWrap.focus(); document.execCommand('insertImage', false, url2); }
                    } else if (cmd === 'codeBlockGX') {
                        var sel = window.getSelection();
                        var text = (sel.rangeCount > 0 && !sel.isCollapsed) ? sel.toString() : '';
                        if (text) {
                            text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            document.execCommand('insertHTML', false, '<pre><code>' + text + '</code></pre><p><br></p>');
                        } else {
                            document.execCommand('insertHTML', false, '<pre><code><br></code></pre><p><br></p>');
                        }
                    } else if (cmd === 'mathGX') {
                        var sel = window.getSelection();
                        var savedRange = sel.rangeCount > 0 ? sel.getRangeAt(0) : null;
                        getMathModal('latex', '', function (type, code) {
                            if (!code.trim()) return;
                            contentWrap.focus();
                            if (savedRange) {
                                var s = window.getSelection();
                                s.removeAllRanges();
                                s.addRange(savedRange);
                            }
                            var h = '';
                            if (type === 'latex') {
                                var rendered = '$$' + code.replace(/</g, '&lt;') + '$$';
                                if (typeof katex !== 'undefined') {
                                    try { rendered = katex.renderToString(code, { throwOnError: false }); } catch (e) { }
                                }
                                h = '<span class="gx-math d-inline-block" data-type="latex" data-formula="' + encodeURIComponent(code) + '" style="cursor:pointer;" title="Double-click to edit">' + rendered + '</span>&nbsp;';
                            } else {
                                h = '<span class="gx-math-ml" style="cursor:pointer;" title="Double-click to edit">' + code + '</span>&nbsp;';
                            }
                            document.execCommand('insertHTML', false, h);
                        });
                    } else if (cmd === 'insertVideoGX') {
                        var vUrl = prompt('Enter Video URL (YouTube, Vimeo, or direct video file):');
                        if (vUrl) {
                            var vHtml = '';
                            if (vUrl.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/)) {
                                var yId = vUrl.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/)[1];
                                vHtml = '<div class="ratio ratio-16x9 my-3"><iframe src="https://www.youtube.com/embed/' + yId + '" allowfullscreen style="border:none;"></iframe></div><p><br></p>';
                            } else if (vUrl.match(/vimeo\.com\/(\d+)/)) {
                                var vId = vUrl.match(/vimeo\.com\/(\d+)/)[1];
                                vHtml = '<div class="ratio ratio-16x9 my-3"><iframe src="https://player.vimeo.com/video/' + vId + '" allowfullscreen style="border:none;"></iframe></div><p><br></p>';
                            } else {
                                vHtml = '<div class="my-3 text-center"><video src="' + vUrl + '" controls style="max-width:100%; border-radius:8px;"></video></div><p><br></p>';
                            }
                            contentWrap.focus();
                            document.execCommand('insertHTML', false, vHtml);
                        }
                    } else if (cmd === 'lineHeightX') {
                        var sel = window.getSelection();
                        if (sel.rangeCount) {
                            var range = sel.getRangeAt(0);
                            var container = range.commonAncestorContainer;
                            if (container.nodeType === 3) container = container.parentNode;
                            var block = container.closest('p, h1, h2, h3, h4, h5, h6, blockquote, li, td, th');
                            if (!block && container.classList && container.classList.contains('gxb-content')) {
                                block = container;
                            } else if (!block) {
                                block = container.closest('.gxb-content');
                            }
                            if (block) {
                                block.style.lineHeight = val;
                            }
                        }
                    } else if (cmd.indexOf('table_') === 0) {
                        var sel = window.getSelection();
                        var range = (sel.rangeCount > 0) ? sel.getRangeAt(0) : null;
                        var container = range ? range.commonAncestorContainer : contentWrap;
                        if (container.nodeType === 3) container = container.parentNode;
                        var table = container.closest('table');
                        var tr = container.closest('tr');
                        var td = container.closest('td, th');

                        if (cmd === 'table_insert') {
                            var savedRange = (sel.rangeCount > 0) ? sel.getRangeAt(0) : null;
                            getTableModal(function(rows, cols, hasHeader, isStriped, isBordered) {
                                contentWrap.focus();
                                if (savedRange) {
                                    var s = window.getSelection();
                                    s.removeAllRanges();
                                    s.addRange(savedRange);
                                }
                                
                                var classes = ['table'];
                                if (isStriped) classes.push('table-striped');
                                if (isBordered) classes.push('table-bordered');
                                
                                var html = '<table class="' + classes.join(' ') + '">';
                                if (hasHeader) {
                                    html += '<thead><tr>';
                                    for(var c=0; c<cols; c++) html += '<th>Header ' + (c+1) + '</th>';
                                    html += '</tr></thead>';
                                }
                                html += '<tbody>';
                                for(var r=0; r<rows; r++) {
                                    html += '<tr>';
                                    for(var c=0; c<cols; c++) html += '<td><br></td>';
                                    html += '</tr>';
                                }
                                html += '</tbody></table><p><br></p>';
                                document.execCommand('insertHTML', false, html);
                            });
                        } else if (table) {
                            if (cmd === 'table_del') {
                                table.parentNode.removeChild(table);
                            } else if (tr && cmd === 'table_row_del') {
                                tr.parentNode.removeChild(tr);
                            } else if (tr && (cmd === 'table_row_add_above' || cmd === 'table_row_add_below')) {
                                var newTr = table.insertRow(cmd === 'table_row_add_above' ? tr.rowIndex : tr.rowIndex + 1);
                                var cellCount = tr.cells.length;
                                for (var i = 0; i < cellCount; i++) newTr.insertCell(i).innerHTML = '<br>';
                            } else if (td) {
                                var cellIndex = td.cellIndex;
                                var allRows = table.rows;
                                if (cmd === 'table_col_del') {
                                    for (var i = 0; i < allRows.length; i++) {
                                        if (allRows[i].cells[cellIndex]) allRows[i].deleteCell(cellIndex);
                                    }
                                } else if (cmd === 'table_col_add_left' || cmd === 'table_col_add_right') {
                                    var insertIdx = cmd === 'table_col_add_left' ? cellIndex : cellIndex + 1;
                                    for (var i = 0; i < allRows.length; i++) {
                                        allRows[i].insertCell(insertIdx).innerHTML = '<br>';
                                    }
                                }
                            }
                        } else {
                            alert('Please place the cursor inside a table first.');
                        }
                    } else if (cmd === 'sourceCodeGX') {
                        var sourceBox = shell.querySelector('.gxb-source-editor');
                        if (!sourceBox) {
                            // Create source editor panel
                            sourceBox = document.createElement('textarea');
                            sourceBox.className = 'gxb-source-editor form-control font-monospace';
                            sourceBox.style.cssText = 'width:100%;min-height:350px;font-size:12px;line-height:1.6;border-radius:0 0 8px 8px;border:2px solid #6366f1;display:none;resize:vertical;background:#0f172a;color:#e2e8f0;padding:16px;';
                            shell.appendChild(sourceBox);
                            // Sync from source back to editor
                            sourceBox.addEventListener('input', function() {
                                contentWrap.innerHTML = sourceBox.value;
                                textarea.value = (typeof htmlToShortcode === 'function') ? htmlToShortcode(contentWrap.innerHTML) : contentWrap.innerHTML;
                            });
                        }
                        var isSourceVisible = sourceBox.style.display !== 'none';
                        if (isSourceVisible) {
                            // Switch back to visual mode
                            contentWrap.innerHTML = sourceBox.value;
                            sourceBox.style.display = 'none';
                            contentWrap.style.display = '';
                            btn.style.color = '#6366f1';
                            btn.title = 'View/Edit Source Code';
                        } else {
                            // Switch to source mode: sync visual → source
                            sourceBox.value = contentWrap.innerHTML;
                            contentWrap.style.display = 'none';
                            sourceBox.style.display = 'block';
                            btn.style.color = '#f59e0b';
                            btn.title = 'Back to Visual Editor';
                            sourceBox.focus();
                        }
                    } else {
                        document.execCommand(cmd, false, val);
                    }
                    contentWrap.focus();
                });
            });

            // Color picker logic
            var savedRange = null;
            var pickerContainer = shell.querySelector('.gxb-color-picker');
            if (pickerContainer) {
                pickerContainer.addEventListener('mousedown', function () {
                    var sel = window.getSelection();
                    if (sel.getRangeAt && sel.rangeCount > 0) {
                        savedRange = sel.getRangeAt(0);
                    }
                });
            }

            shell.querySelectorAll('.gxb-classic-toolbar input[data-cmd-color]').forEach(function (input) {
                input.addEventListener('change', function () {
                    contentWrap.focus();
                    if (savedRange) {
                        var sel = window.getSelection();
                        sel.removeAllRanges();
                        sel.addRange(savedRange);
                    }
                    var cmd = input.dataset.cmdColor;

                    try { document.execCommand('styleWithCSS', false, true); } catch (e) { }

                    if (cmd === 'backColor') {
                        // Some browsers (Chrome) prefer hiliteColor for text background spans
                        if (!document.execCommand('hiliteColor', false, input.value)) {
                            document.execCommand('backColor', false, input.value);
                        }
                    } else {
                        document.execCommand(cmd, false, input.value);
                    }
                });
            });

            // Sync logic
            var sync = function () { 
                textarea.value = (typeof htmlToShortcode === 'function') ? htmlToShortcode(contentWrap.innerHTML) : contentWrap.innerHTML; 
            };
            contentWrap.addEventListener('input', sync);
            contentWrap.addEventListener('blur', sync);

            // Double click Math editor
            contentWrap.addEventListener('dblclick', function (e) {
                var mathEl = e.target.closest('.gx-math');
                var mathMlEl = e.target.closest('math') || e.target.closest('.gx-math-ml');
                if (mathEl) {
                    var code = decodeURIComponent(mathEl.getAttribute('data-formula') || '');
                    getMathModal('latex', code, function (type, nCode) {
                        if (type === 'latex') {
                            mathEl.setAttribute('data-formula', encodeURIComponent(nCode));
                            var rendered = '$$' + nCode.replace(/</g, '&lt;') + '$$';
                            if (typeof katex !== 'undefined') {
                                try { rendered = katex.renderToString(nCode, { throwOnError: false }); } catch (e) { }
                            }
                            mathEl.innerHTML = rendered;
                            if (!mathEl.classList.contains('d-inline-block')) mathEl.classList.add('d-inline-block');
                            mathEl.style.cssText = "cursor:pointer;";
                        } else {
                            var span = document.createElement('span');
                            span.className = 'gx-math-ml';
                            span.title = 'Double-click to edit';
                            span.style.cursor = 'pointer';
                            span.innerHTML = nCode;
                            mathEl.replaceWith(span);
                        }
                        sync();
                    });
                } else if (mathMlEl) {
                    var container = mathMlEl.closest('.gx-math-ml');
                    var targetSave = container || mathMlEl;
                    var code = container ? container.innerHTML : mathMlEl.outerHTML;
                    getMathModal('mathml', code, function (type, nCode) {
                        if (type === 'mathml') {
                            if (container) container.innerHTML = nCode;
                            else mathMlEl.outerHTML = nCode;
                        } else {
                            var span = document.createElement('span');
                            span.className = 'gx-math d-inline-block';
                            span.setAttribute('data-type', 'latex');
                            span.setAttribute('data-formula', encodeURIComponent(nCode));
                            span.style.cssText = "cursor:pointer;";
                            span.title = 'Double-click to edit';
                            var rendered = '$$' + nCode.replace(/</g, '&lt;') + '$$';
                            if (typeof katex !== 'undefined') {
                                try { rendered = katex.renderToString(nCode, { throwOnError: false }); } catch (e) { }
                            }
                            span.innerHTML = rendered;
                            targetSave.replaceWith(span);
                        }
                        sync();
                    });
                }
            });

            // External change listener (for Dynamic Builder sync)
            textarea.addEventListener('change', function () {
                var html = textarea.value || '<p><br></p>';
                if (typeof shortcodeToHtml === 'function') html = shortcodeToHtml(html);
                if (contentWrap.innerHTML !== html) {
                    contentWrap.innerHTML = html;
                }
            });

            return;
        }

        // Parse existing HTML into blocks
        var htmlToParse = textarea.value || textarea.dataset.gxcontent || '';
        if (typeof parseHTML === 'function') parseHTML(htmlToParse, state);

        // Add initial paragraph if empty
        if (!state.blocks.length) {
            if (typeof addBlock === 'function') addBlock(state, 'paragraph', '', null);
        }

        if (typeof renderAllBlocks === 'function') renderAllBlocks(state);

        // External change listener (for Dynamic Builder sync)
        textarea.addEventListener('change', function () {
            if (typeof parseHTML === 'function') parseHTML(textarea.value, state);
            if (typeof renderAllBlocks === 'function') renderAllBlocks(state);
        });

        // Add-block button at bottom
        var addWrap = document.createElement('div');
        addWrap.className = 'gxb-addbtn-wrap';
        addWrap.innerHTML = '<button class="gxb-addbtn" type="button"><i class="bi bi-plus-circle me-2"></i>Add Block</button>';
        addWrap.querySelector('.gxb-addbtn').addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (typeof openPicker === 'function') {
                var btn = this;
                var r = btn.getBoundingClientRect();
                openPicker(state, null, { top: r.top, left: r.left });
            }
        });
        shell.appendChild(addWrap);

        // ── Keyboard Shortcuts ───────────────────────────────────────────
        shell.addEventListener('keydown', function (e) {
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
