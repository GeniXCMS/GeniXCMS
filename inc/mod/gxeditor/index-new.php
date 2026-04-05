    public static function injectAssets($data = null)
    {
        static $injected = false;
        if ($injected) {
            return '';
        }
        $injected = true;

        $elfinderAjaxUrl = json_encode(Url::ajax('elfinder'));

        $out = '
<!-- Inline Toolbar -->
<div id="gxb-inline-toolbar" class="d-flex align-items-center gap-1">
    <button type="button" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
    <button type="button" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
    <div class="gxb-tb-sep"></div>
    <button type="button" data-cmd="h1" title="Heading 1">H1</button>
    <button type="button" data-cmd="h2" title="Heading 2">H2</button>
    <div class="gxb-tb-sep"></div>
    <button type="button" data-cmd="createLink" title="Link"><i class="bi bi-link-45deg"></i></button>
</div>

<!-- Block Picker -->
<div id="gxb-picker">
    <div class="gxb-picker-search-wrap">
        <i class="bi bi-search"></i>
        <input type="text" id="gxb-picker-search" placeholder="Search blocks... (or type /)">
    </div>
    <div id="gxb-picker-list" class="gxb-picker-grid"></div>
</div>

<!-- Image Properties Context Menu -->
<div id="gxb-img-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:200px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Image Properties</div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Width</label>
        <select id="gxb-prop-width" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="">Default</option>
            <option value="w-25">25%</option>
            <option value="w-50">50%</option>
            <option value="w-75">75%</option>
            <option value="w-100">100%</option>
        </select>
    </div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Alignment</label>
        <select id="gxb-prop-align" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="">Default</option>
            <option value="float-start">Left</option>
            <option value="mx-auto d-block">Center</option>
            <option value="float-end">Right</option>
        </select>
    </div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Style</label>
        <select id="gxb-prop-style" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="rounded">Rounded</option>
            <option value="img-thumbnail">Thumbnail</option>
            <option value="rounded-circle">Circle</option>
        </select>
    </div>
    <div style="margin-top:8px; padding-top:8px; border-top:1px solid #f1f5f9;">
        <button id="gxb-img-replace" type="button" class="btn btn-sm btn-light w-100 text-primary mb-1"><i class="bi bi-arrow-repeat me-1"></i> Replace Image</button>
        <button type="button" class="btn btn-sm btn-light w-100 text-danger" onclick="if(confirm(\'Delete this block?\')) { document.querySelector(\'.gxb-block.gxb-selected .gxb-del\')?.click(); closeAllContextMenus(); }"><i class="bi bi-trash me-1"></i> Delete Block</button>
    </div>
</div>
';

        $out .= '
<!-- Button Properties Context Menu -->
<div id="gxb-btn-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:200px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Button Settings</div>
    <div style="margin-bottom:6px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Style</label>
        <select id="gxb-prop-btn-style" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="btn-primary">Solid Primary</option>
            <option value="btn-outline-primary">Outline Primary</option>
            <option value="btn-light">Light</option>
            <option value="btn-link">Link Only</option>
        </select>
    </div>
    <div style="margin-bottom:6px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Size</label>
        <select id="gxb-prop-btn-size" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="">Normal</option>
            <option value="btn-sm">Small</option>
            <option value="btn-lg">Large</option>
        </select>
    </div>
    <div class="mt-3 pt-2 border-top">
        <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill py-1 fw-bold extra-small" onclick="if(confirm(\'Delete this block?\')) { document.querySelector(\'.gxb-block.gxb-selected .gxb-del\')?.click(); closeAllContextMenus(); }">
            <i class="bi bi-trash3 me-1"></i> Delete This Block
        </button>
    </div>
</div>

<!-- Card Properties Context Menu -->
<div id="gxb-card-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:180px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Card Settings</div>
    <div style="margin-bottom:6px;">
        <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" id="gxb-prop-card-header"> Show Header
        </label>
    </div>
    <div style="margin-bottom:6px;">
        <label style="cursor:pointer; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" id="gxb-prop-card-footer"> Show Footer
        </label>
    </div>
</div>
';

        $out .= '
<!-- Classic Mode Toolbar -->
<div id="gxb-classic-toolbar-template" style="display:none;">
    <div class="gxb-classic-toolbar border-bottom shadow-sm">
        <div class="d-flex align-items-center gap-1 flex-wrap">
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-sm btn-light p-1 border-0 shadow-none d-flex align-items-center" data-bs-toggle="dropdown" title="Headings" style="font-size:0.8rem; min-width:40px; font-weight:700;">H <i class="bi bi-chevron-down ms-1" style="font-size:0.6rem;"></i></button>
                <ul class="dropdown-menu shadow-sm" style="font-size:0.85rem; min-width:120px;">
                    <li><a class="dropdown-item py-1" href="#" data-cmd="h1"><i class="bi bi-type-h1 me-2"></i> Heading 1</a></li>
                    <li><a class="dropdown-item py-1" href="#" data-cmd="h2"><i class="bi bi-type-h2 me-2"></i> Heading 2</a></li>
                    <li><a class="dropdown-item py-1" href="#" data-cmd="h3"><i class="bi bi-type-h3 me-2"></i> Heading 3</a></li>
                    <li><a class="dropdown-item py-1" href="#" data-cmd="paragraph"><i class="bi bi-paragraph me-2"></i> Paragraph</a></li>
                </ul>
            </div>
            <div class="gxb-tb-sep"></div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" data-cmd="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
                <button type="button" data-cmd="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
                <button type="button" data-cmd="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
                <button type="button" data-cmd="createLink" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
            </div>
            <div class="gxb-tb-sep"></div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" data-cmd="justifyLeft" title="Align Left"><i class="bi bi-text-left"></i></button>
                <button type="button" data-cmd="justifyCenter" title="Align Center"><i class="bi bi-text-center"></i></button>
                <button type="button" data-cmd="justifyRight" title="Align Right"><i class="bi bi-text-right"></i></button>
                <div class="dropdown d-inline-block ms-1">
                    <button type="button" class="btn btn-sm btn-light p-1 border-0 shadow-none" data-bs-toggle="dropdown" title="Line Height" style="font-size:0.7rem; min-width:28px;">LH</button>
                    <ul class="dropdown-menu shadow-sm" style="font-size:0.75rem; min-width:70px;">
                        <li><a class="dropdown-item py-1" href="#" onclick="document.execCommand(\'insertHTML\', false, \'<span style=\\\'line-height:1.2\\\'>\' + window.getSelection().toString() + \'</span>\'); return false;">1.2</a></li>
                        <li><a class="dropdown-item py-1" href="#" onclick="document.execCommand(\'insertHTML\', false, \'<span style=\\\'line-height:1.5\\\'>\' + window.getSelection().toString() + \'</span>\'); return false;">1.5</a></li>
                        <li><a class="dropdown-item py-1" href="#" onclick="document.execCommand(\'insertHTML\', false, \'<span style=\\\'line-height:1.8\\\'>\' + window.getSelection().toString() + \'</span>\'); return false;">1.8</a></li>
                        <li><a class="dropdown-item py-1" href="#" onclick="document.execCommand(\'insertHTML\', false, \'<span style=\\\'line-height:2.0\\\'>\' + window.getSelection().toString() + \'</span>\'); return false;">2.0</a></li>
                    </ul>
                </div>
            </div>
            <div class="gxb-tb-sep"></div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" data-cmd="ul" title="Unordered List"><i class="bi bi-list-ul"></i></button>
                <button type="button" data-cmd="ol" title="Ordered List"><i class="bi bi-list-ol"></i></button>
            </div>
            <div class="gxb-tb-sep"></div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" data-cmd="insertImageGX" title="Insert Image"><i class="bi bi-image"></i></button>
                <button type="button" id="gxb-classic-add-btn" title="Add Complex Block (/)"><i class="bi bi-plus-circle-fill" style="color:#6366f1;"></i></button>
            </div>
            <div class="d-flex gap-1 border-start ps-2">
                <button type="button" data-cmd="icon_list" title="Icon List"><i class="bi bi-check2-square"></i></button>
                <button type="button" data-cmd="table" title="Table"><i class="bi bi-table"></i></button>
                <button type="button" data-cmd="grid2" title="2 Columns"><i class="bi bi-layout-split"></i></button>
                <button type="button" data-cmd="toc" title="TOC"><i class="bi bi-list-nested"></i></button>
            </div>
        </div>
    </div>
</div>
';

        $out .= '
<!-- Grid / Column Properties Context Menu -->
<div id="gxb-grid-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:200px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Grid Settings</div>
    <div style="display:flex; gap:10px; margin-bottom:8px;">
        <div style="flex:1;">
            <label style="display:block; color:#64748b; margin-bottom:2px;">Columns</label>
            <select id="gxb-prop-grid-count" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
        </div>
        <div style="flex:1;">
            <label style="display:block; color:#64748b; margin-bottom:2px;">Rows</label>
            <select id="gxb-prop-grid-rows" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
                <option value="1">1</option>
                <option value="2">2</option>
            </select>
        </div>
    </div>
    <div id="gxb-grid-ratio-wrap" style="margin-bottom:6px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Column Ratio (for 2 cols)</label>
        <select id="gxb-prop-ratio" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="6:6">50% - 50%</option>
            <option value="4:8">33% - 66%</option>
            <option value="8:4">66% - 33%</option>
            <option value="3:9">25% - 75%</option>
            <option value="9:3">75% - 25%</option>
            <option value="12:12">100% - 100% (Stacked)</option>
        </select>
    </div>
</div>

<!-- Post Selection Context Menu -->
<div id="gxb-post-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:180px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Single Post Setting</div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Post ID</label>
        <input type="text" id="gxb-prop-post-id" placeholder="Enter Post ID..." style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
    </div>
    <button id="gxb-post-save" type="button" style="width:100%; background:#6366f1; color:#fff; border:none; padding:6px; border-radius:4px; cursor:pointer;">Update Preview</button>
</div>
';

        $out .= '
<!-- TOC Context Menu -->
<div id="gxb-toc-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:180px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">TOC Settings</div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Floating</label>
        <select id="gxb-prop-toc-float" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="none">None</option>
            <option value="start">Left</option>
            <option value="end">Right</option>
        </select>
    </div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Initial Collapse</label>
        <select id="gxb-prop-toc-collapse" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="no">No</option>
            <option value="yes">Yes</option>
        </select>
    </div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Width (ex: 350px)</label>
        <input type="text" id="gxb-prop-toc-width" placeholder="450px" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
    </div>
    <button id="gxb-toc-save" type="button" style="width:100%; background:#6366f1; color:#fff; border:none; padding:6px; border-radius:4px; cursor:pointer;">Update TOC</button>
</div>

<!-- Text Properties Context Menu -->
<div id="gxb-text-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:180px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Text Properties</div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Alignment</label>
        <select id="gxb-prop-text-align" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="">Default</option>
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
            <option value="justify">Justify</option>
        </select>
    </div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Line Height</label>
        <select id="gxb-prop-text-lineheight" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
            <option value="">Default</option>
            <option value="1">1.0</option>
            <option value="1.2">1.2</option>
            <option value="1.4">1.4</option>
            <option value="1.6">1.6</option>
            <option value="1.8">1.8</option>
            <option value="2">2.0</option>
        </select>
    </div>
    <button id="gxb-text-save" type="button" style="width:100%; background:#6366f1; color:#fff; border:none; padding:6px; border-radius:4px; cursor:pointer;">Apply Changes</button>
</div>
';

        $out .= '
<!-- Table Context Menu -->
<div id="gxb-table-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:200px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Table Actions</div>
    <div class="d-flex flex-column gap-1">
        <button id="gxb-table-add-row" type="button" class="btn btn-sm btn-light text-start"><i class="bi bi-plus-lg me-2"></i>Add Row</button>
        <button id="gxb-table-add-col" type="button" class="btn btn-sm btn-light text-start"><i class="bi bi-plus-lg me-2"></i>Add Column</button>
        <div class="border-top my-1"></div>
        <button id="gxb-table-del-row" type="button" class="btn btn-sm btn-light text-start text-danger"><i class="bi bi-dash-lg me-2"></i>Delete Row</button>
        <button id="gxb-table-del-col" type="button" class="btn btn-sm btn-light text-start text-danger"><i class="bi bi-dash-lg me-2"></i>Delete Column</button>
    </div>
</div>

<!-- Icon List Context Menu -->
<div id="gxb-iconlist-context" style="display:none; position:fixed; z-index:99999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); padding:10px; font-size:0.85rem; width:190px;">
    <div style="font-weight:600; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">Icon List Settings</div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Global Icon</label>
        <input type="text" id="gxb-prop-iconlist-class" placeholder="bi bi-check2-circle" style="width:100%; padding:4px; border:1px solid #cbd5e1; border-radius:4px;">
    </div>
    <div style="margin-bottom:8px;">
        <label style="display:block; color:#64748b; margin-bottom:2px;">Color</label>
        <input type="color" id="gxb-prop-iconlist-color" style="width:100%; height:32px; padding:2px;">
    </div>
    <button id="gxb-iconlist-save" type="button" style="width:100%; background:#6366f1; color:#fff; border:none; padding:6px; border-radius:4px; cursor:pointer;">Update List</button>
</div>
';
