    </main>
    <footer class="admin-footer d-flex justify-content-between align-items-center">
        <div>
            <strong>Copyright &copy; 2014-<?=date('Y');?> <a href="https://genixcms.web.id" target="_blank" class="text-decoration-none">GeniXCMS</a>.</strong>
            <?=_("All rights reserved.");?>
        </div>
        <div class="d-none d-sm-block">
            <span class="badge bg-light text-dark border">Version <?=System::$version;?></span>
            <?php
            $time_taken = round(microtime(true) - $GLOBALS['start_time'], 4);
            echo '<small class="ms-2 text-muted">'._("Generated in").' '.$time_taken.'s</small>';
            ?>
        </div>
    </footer>
</div>

<button id="scrollTop" class="btn btn-primary shadow-lg">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
$(function() {
    // Sidebar Toggles
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
        $('#main-wrapper').toggleClass('expanded');
    });

    $('#sidebarClose').on('click', function() {
        $('#sidebar').removeClass('active');
    });

    // Mobile Sidebar Toggle (different behavior)
    if ($(window).width() < 992) {
        $('#sidebarToggle').on('click', function(e) {
            e.stopPropagation();
            $('#sidebar').toggleClass('active');
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#sidebar').length) {
                $('#sidebar').removeClass('active');
            }
        });
    }

    // Treeview Toggle
    $('.has-tree').on('click', function(e) {
        e.preventDefault();
        $(this).parent().toggleClass('open');
        $(this).find('.bi-chevron-down').toggleClass('rotate-180');
    });

    // Scroll Top
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) $('#scrollTop').addClass('visible');
        else $('#scrollTop').removeClass('visible');
    });
    $('#scrollTop').on('click', function() {
        $('html, body').animate({scrollTop: 0}, 400);
    });

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
    .rotate-180 { transform: rotate(180deg); }
    .has-tree .bi-chevron-down { transition: transform 0.3s; }
    #sidebar.active { margin-left: 0 !important; }
    @media (max-width: 992px) {
        #sidebar { margin-left: -260px; height: 100vh; }
    }
</style>

<?php
$__editorType = $GLOBALS['editor_type'] ?? Options::v('editor_type') ?: 'summernote';
if (isset($GLOBALS['editor']) && $GLOBALS['editor'] == true) {
    Hooks::attach('admin_footer_action', array('Files', 'elfinderLib'));
    $url = Url::ajax('saveimage');
    $elfinderUrl = Url::ajax('elfinder');

    if ($__editorType === 'editorjs') {
        // ── EditorJS Initialization ──────────────────────────────────────────
        $encodedUrl = json_encode($url);
        $encodedElfinderUrl = json_encode($elfinderUrl);
        echo <<<EDITORJS
        <style>
            .editorjs-wrapper {
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                min-height: 400px;
                padding: 16px;
                background: #fff;
            }
            .ce-block__content, .ce-toolbar__content { max-width: 100%; }
        </style>
        <script>
        window.addEventListener('load', function() {
            var editors = {};
            window.__gxEditors = editors;
            var uploadUrl = {$encodedUrl};
            var elfinderUrlRaw = {$encodedElfinderUrl};

            // Map tools defensively — only include if plugin loaded
            var editorTools = {};
            if (typeof Header       !== 'undefined') editorTools.header      = { class: Header,      inlineToolbar: true };
            if (typeof EditorjsList !== 'undefined') editorTools.list        = { class: EditorjsList, inlineToolbar: true };
            if (typeof ImageTool    !== 'undefined') editorTools.image       = { 
                class: ImageTool,    
                config: { 
                    uploader: {
                        uploadByFile: function(file) {
                            return new Promise(function(resolve, reject) {
                                var elfinderUrl = elfinderUrlRaw;
                                var sep = elfinderUrl.indexOf('?') === -1 ? '?' : '&';
                                fetch(elfinderUrl + sep + 'cmd=open&init=1&target=')
                                .then(function(res) { return res.json(); })
                                .then(function(initData) {
                                    if (!initData || !initData.cwd) return reject('Failed to init elfinder API');
                                    var target = initData.cwd.hash;
                                    var fd = new FormData();
                                    fd.append('cmd', 'upload');
                                    fd.append('target', target);
                                    fd.append('upload[]', file);
                                    var sep2 = elfinderUrl.indexOf('?') === -1 ? '?' : '&';
                                    fetch(elfinderUrl + sep2 + 'auto_sort=1', { method: 'POST', body: fd })
                                    .then(function(r) { return r.json(); })
                                    .then(function(upRes) {
                                        if (upRes.added && upRes.added.length > 0) {
                                            var fileObj = upRes.added.find(function(i) { return i.mime !== 'directory' && i.url; });
                                            if (!fileObj) fileObj = upRes.added[upRes.added.length - 1]; // Fallback
                                            resolve({ success: 1, file: { url: fileObj.url } });
                                        }
                                        else reject(upRes.error || 'Upload failed');
                                    }).catch(reject);
                                }).catch(reject);
                            });
                        }
                    }
                } 
            };
            if (typeof Quote        !== 'undefined') editorTools.quote       = { class: Quote,        inlineToolbar: true };
            if (typeof CodeTool     !== 'undefined') editorTools.code        = { class: CodeTool };
            if (typeof Embed        !== 'undefined') editorTools.embed       = { class: Embed };
            if (typeof Table        !== 'undefined') editorTools.table       = { class: Table,        inlineToolbar: true };
            if (typeof Delimiter    !== 'undefined') editorTools.delimiter   = { class: Delimiter };
            if (typeof InlineCode   !== 'undefined') editorTools.inlineCode  = { class: InlineCode };

            document.querySelectorAll('.editor').forEach(function(textarea, idx) {
                var editorId = 'editorjs-holder-' + idx;
                var wrapper = document.createElement('div');
                wrapper.id = editorId;
                wrapper.className = 'editorjs-wrapper';
                textarea.parentNode.insertBefore(wrapper, textarea);
                textarea.style.display = 'none';

                var initData = { blocks: [] };
                var existing = textarea.value.trim();
                if (existing) {
                    try {
                        var parserDiv = document.createElement('div');
                        parserDiv.innerHTML = existing;
                        var children = Array.from(parserDiv.childNodes);
                        
                        children.forEach(function(node) {
                            if (node.nodeType === 1) { // Element
                                var tag = node.tagName;
                                if (tag === 'P') {
                                    initData.blocks.push({ type: 'paragraph', data: { text: node.innerHTML } });
                                } else if (tag.match(/^H[1-6]$/)) {
                                    initData.blocks.push({ type: 'header', data: { text: node.innerText, level: parseInt(tag[1]) } });
                                } else if (tag === 'BLOCKQUOTE') {
                                    var p = node.querySelector('p');
                                    var f = node.querySelector('footer') || node.querySelector('cite');
                                    initData.blocks.push({ type: 'quote', data: { text: p ? p.innerHTML : node.innerHTML, caption: f ? f.innerText : '' } });
                                } else if (tag === 'DIV' && (node.className.includes('text-center') || node.querySelector('img'))) {
                                    var img = node.querySelector('img');
                                    var capt = node.querySelector('.text-muted') || node.querySelector('span');
                                    if (img) {
                                        initData.blocks.push({ type: 'image', data: { file: { url: img.src }, caption: capt ? capt.innerText : (img.alt || '') } });
                                    }
                                } else if (tag === 'UL' || tag === 'OL') {
                                    var items = Array.from(node.querySelectorAll('li')).map(function(li) { return li.innerHTML; });
                                    initData.blocks.push({ type: 'list', data: { style: tag === 'OL' ? 'ordered' : 'unordered', items: items } });
                                } else if (tag === 'HR') {
                                    initData.blocks.push({ type: 'delimiter', data: {} });
                                } else if (tag === 'TABLE') {
                                    var rows = Array.from(node.querySelectorAll('tr')).map(function(tr) {
                                        return Array.from(tr.querySelectorAll('td, th')).map(function(cell) { return cell.innerHTML; });
                                    });
                                    initData.blocks.push({ type: 'table', data: { content: rows } });
                                } else {
                                    // Default to paragraph for unknown elements
                                    initData.blocks.push({ type: 'paragraph', data: { text: node.outerHTML } });
                                }
                            } else if (node.nodeType === 3 && node.textContent.trim().length > 0) {
                                initData.blocks.push({ type: 'paragraph', data: { text: node.textContent.trim() } });
                            }
                        });
                    } catch (e) {
                         console.error('HTML Parsing error', e);
                         initData.blocks.push({ type: 'paragraph', data: { text: existing } });
                    }
                }

                editors[idx] = new EditorJS({
                    holder: editorId,
                    data: initData,
                    tools: editorTools,
                    onChange: function() {}
                });
                editors[idx]._textarea = textarea;
            });

            // On form submit: serialize EditorJS blocks to HTML, fill textarea, then submit
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    var promises = Object.keys(editors).map(function(idx) {
                        return editors[idx].save().then(function(data) {
                            var html = '';
                            data.blocks.forEach(function(block) {
                                switch (block.type) {
                                    case 'header':
                                        html += '<h' + block.data.level + '>' + block.data.text + '</h' + block.data.level + '>';
                                        break;
                                    case 'paragraph':
                                        html += '<p>' + block.data.text + '</p>';
                                        break;
                                    case 'list':
                                        var tag = block.data.style === 'ordered' ? 'ol' : 'ul';
                                        html += '<' + tag + '>';
                                        block.data.items.forEach(function(item) {
                                            html += '<li>' + (item.content || item) + '</li>';
                                        });
                                        html += '</' + tag + '>';
                                        break;
                                    case 'image':
                                        var capt = block.data.caption ? '<div class="text-muted text-center small">' + block.data.caption + '</div>' : '';
                                        html += '<div class="text-center mb-3"><img src="' + block.data.file.url + '" alt="' + (block.data.caption || '') + '" class="img-fluid rounded">' + capt + '</div>';
                                        break;
                                    case 'quote':
                                        html += '<blockquote class="blockquote"><p>' + block.data.text + '</p><footer class="blockquote-footer">' + (block.data.caption || '') + '</footer></blockquote>';
                                        break;
                                    case 'code':
                                        html += '<pre><code>' + block.data.code + '</code></pre>';
                                        break;
                                    case 'delimiter':
                                        html += '<hr>';
                                        break;
                                    case 'table':
                                        html += '<table class="table table-bordered"><tbody>';
                                        (block.data.content || []).forEach(function(row) {
                                            html += '<tr>';
                                            row.forEach(function(cell) { html += '<td>' + cell + '</td>'; });
                                            html += '</tr>';
                                        });
                                        html += '</tbody></table>';
                                        break;
                                    case 'embed':
                                        html += '<div class="embed-responsive"><iframe src="' + block.data.embed + '" frameborder="0" allowfullscreen></iframe></div>';
                                        break;
                                    default:
                                        if (block.data && block.data.text) html += '<p>' + block.data.text + '</p>';
                                }
                                html += "\\n";
                            });
                            editors[idx]._textarea.value = html;
                        });
                    });
                    e.preventDefault();
                    Promise.all(promises).then(function() { 
                        var sub = document.createElement('input');
                        sub.type = 'hidden';
                        sub.name = 'submit';
                        sub.value = '1';
                        form.appendChild(sub);

                        if (typeof HTMLFormElement.prototype.submit === 'function') {
                            HTMLFormElement.prototype.submit.call(form);
                        } else {
                            form.submit();
                        }
                    });
                });
            });
        });
        </script>
EDITORJS;
    } else {
        // ── Summernote Initialization (default) ─────────────────────────────
        $foot = '
        <script>
          $(document).ready(function() {
            function sendFile(file,editor,welEditable) {
                var elfinderUrl = \''.$elfinderUrl.'\';
                var sep = elfinderUrl.indexOf(\'?\') === -1 ? \'?\' : \'&\';
                $.ajax({ url: elfinderUrl + sep + \'cmd=open&init=1&target=\', type: \'GET\', dataType: \'json\' })
                 .done(function(initData) {
                     if (!initData || !initData.cwd) return console.log(\'Failed to init elfinder API\');
                     var target = initData.cwd.hash;
                     var fd = new FormData();
                     fd.append(\'cmd\', \'upload\');
                     fd.append(\'target\', target);
                     fd.append(\'upload[]\', file);
                     var sep2 = elfinderUrl.indexOf(\'?\') === -1 ? \'?\' : \'&\';
                     $.ajax({
                         url: elfinderUrl + sep2 + \'auto_sort=1\', data: fd, cache: false, contentType: false, processData: false, type: \'POST\',
                         success: function(data) { 
                             var parsed = typeof data === \'string\' ? JSON.parse(data) : data;
                             if (parsed.added && parsed.added.length > 0) {
                                 $(\'.editor\').summernote(\'editor.insertImage\', parsed.added[0].url);
                             } else if(parsed.error) {
                                 toastr.error(parsed.error);
                             }
                         },
                         error: function(jqXHR, textStatus, errorThrown) { console.log(textStatus+\' \'+errorThrown); }
                     });
                 });
            }

            $(\'.editor\').each(function(i, obj) { $(obj).summernote({
                minHeight: 300,
                maxHeight: ($(window).height() - 150),
                toolbar: ['.System::$toolbar.'],
                callbacks: {
                    onImageUpload: function(files, editor, welEditable) { sendFile(files[0],editor,welEditable); },
                    onPaste: function (e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData(\'Text\');
                        e.preventDefault();
                        document.execCommand(\'insertText\', false, bufferText);
                    }
                }
            }); });
          });
        </script>';
        echo Site::minifyJS($foot);
    }
}

// Version Check (Modernized toastr call)
$versionUrl = Url::ajax('version');
?>
<script>
    setTimeout(function() {
        $.getJSON('<?=$versionUrl;?>', function(obj) {
            if (obj.status == 'false') {
                toastr.warning("<?=_("CMS Update Available");?>", "<?=_("New version");?> (" + obj.version + ") <?=_("is ready to download.");?>");
            }
        });
    }, 3000);
</script>

<?php echo Hooks::run('admin_footer_action', $data); ?>

</body>
</html>