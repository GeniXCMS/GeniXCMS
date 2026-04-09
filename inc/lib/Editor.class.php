<?php

/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @since 2.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Editor
{
    private static $editors = [];

    private static $defaultsLoaded = false;

    /**
     * Internal method to load default editors (Summernote and EditorJS) into the registry.
     * Also processes the 'editor_type_options' hook for third-party extensions.
     */
    private static function loadDefaults()
    {
        if (self::$defaultsLoaded)
            return;
        self::$defaultsLoaded = true;

        // Default editors are registered via its own methods
        self::register('summernote', 'Summernote Classic', [self::class, 'summernote']);
        self::register('editorjs', 'Editor.js (Blocks)', [self::class, 'editorjs']);

        $options = [];
        foreach (self::$editors as $id => $data) {
            $options[$id] = is_array($data) ? $data['name'] : $data;
        }

        // Allow modules to register/override editors via hook
        // We pass the simplified [id => name] list to the filter
        $filtered_options = Hooks::filter('editor_type_options', $options);

        // Merge back the filtered options
        foreach ($filtered_options as $id => $name) {
            if (isset(self::$editors[$id])) {
                // If it's already an array, just update the name
                if (is_array(self::$editors[$id])) {
                    self::$editors[$id]['name'] = $name;
                } else {
                    // It was a string, now it's a string, do nothing
                    self::$editors[$id] = $name;
                }
            } else {
                // New editor from legacy hook format
                self::$editors[$id] = $name;
            }
        }
    }

    /**
     * Initializes the editor system by loading defaults and executing the active editor's callback.
     * Enqueues necessary assets based on the 'editor_type' option.
     */
    public static function init()
    {
        self::loadDefaults();

        // Load active editor assets
        $active = Options::v('editor_type') ?: 'summernote';
        if (isset(self::$editors[$active])) {
            $callback = self::$editors[$active];
            if (is_array($callback) && isset($callback['callback'])) {
                $callback = $callback['callback'];
            }

            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
    }

    /**
     * Registers a new editor type into the system.
     *
     * @param string   $id       Unique identifier for the editor (e.g., 'summernote').
     * @param string   $name     Human-readable name for the editor.
     * @param callable $callback The function that enqueues assets and initializes the editor.
     */
    public static function register($id, $name, $callback)
    {
        self::$editors[$id] = [
            'name' => $name,
            'callback' => $callback
        ];
    }

    /**
     * Retrieves a list of all registered editors.
     *
     * @return array Associative array [id => name].
     */
    public static function getEditors()
    {
        self::loadDefaults();
        $options = [];
        foreach (self::$editors as $id => $data) {
            $options[$id] = is_array($data) ? $data['name'] : $data;
        }
        return $options;
    }

    /**
     * Callback for the Summernote (Classic WYSIWYG) editor.
     * Registers and enqueues Summernote assets and initializes it with elFinder support.
     */
    public static function summernote()
    {
        $siteUrl = rtrim(Site::$url, '/');
        $elfinderUrl = Url::ajax('elfinder');

        // Register Summernote Assets
        Asset::register('summernote-css', 'css', 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css', 'header');
        Asset::register('summernote-js', 'js', 'https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js', 'footer');

        // Enqueue jQuery UI CSS for elfinder dialog
        Asset::enqueue('jquery-ui-css');

        // summernote-elfinder-button
        Asset::register('summernote-elfinder-button', 'raw', '
        <script>
            window.elfinderDialog = function(context) {
                var ui = $.summernote.ui;
                var button = ui.button({
                    contents: \'<i class="bi bi-folder2-open"></i>\',
                    tooltip: "File Manager",
                    click: function() {
                        var elfinderUrl = "' . $elfinderUrl . '";
                        var sep = elfinderUrl.indexOf("?") === -1 ? "?" : "&";
                        var context_btn = context;
                        
                        if (typeof $.fn.dialogelfinder === "undefined") {
                            console.error("elFinder dialogelfinder is not loaded!");
                            alert("File Manager Error: elFinder components not loaded correctly.");
                            return;
                        }

                        setTimeout(function() {
                            $("<div />").appendTo("body").dialogelfinder({
                                url: elfinderUrl + sep + "token=' . TOKEN . '",
                                lang: "en",
                                width: 840,
                                destroyOnClose: true,
                                getFileCallback: function(file, fm) {
                                    context_btn.invoke("editor.insertImage", file.url);
                                },
                            }).dialogelfinder("instance");
                        }, 10);
                    }
                });
                return button.render();
            };

            window.gxcodeBtn = function(context) {
                var ui = $.summernote.ui;
                var button = ui.button({
                    contents: \'<i class="bi bi-code-slash"></i>\',
                    tooltip: "Add Code Snippet",
                    click: function() {
                        context.invoke("editor.insertText", " [code] [/code] ");
                    }
                });
                return button.render();
            };
        </script>', 'footer', ['elfinder-js', 'elfinder-theme', 'elfinder-css-custom', 'jquery-ui-css']);

        // Summernote Init Script
        Asset::register('summernote-init', 'raw', '
        <script>
            $(document).ready(function() {
                function sendFile(file, editor, welEditable) {
                    var elfinderUrl = "' . $elfinderUrl . '";
                    var sep = elfinderUrl.indexOf("?") === -1 ? "?" : "&";
                    $.ajax({ 
                        url: elfinderUrl + sep + "cmd=open&init=1&target=", 
                        type: "GET", 
                        dataType: "json" 
                    })
                    .done(function(initData) {
                        if (!initData || !initData.cwd) return console.log("Failed to init elfinder API");
                        var target = initData.cwd.hash;
                        var fd = new FormData();
                        fd.append("cmd", "upload");
                        fd.append("target", target);
                        fd.append("upload[]", file);
                        var sep2 = elfinderUrl.indexOf("?") === -1 ? "?" : "&";
                        $.ajax({
                            url: elfinderUrl + sep2 + "auto_sort=1", data: fd, cache: false, contentType: false, processData: false, type: "POST",
                            success: function(data) { 
                                var parsed = typeof data === "string" ? JSON.parse(data) : data;
                                if (parsed.added && parsed.added.length > 0) {
                                    $(".editor").summernote("editor.insertImage", parsed.added[0].url);
                                } else if(parsed.error) {
                                    if (typeof window.showGxToast === "function") window.showGxToast(parsed.error, "error");
                                    else alert(parsed.error);
                                }
                            }
                        });
                    });
                }

                $(".editor").each(function(i, obj) { 
                    $(obj).summernote({
                        minHeight: 300,
                        maxHeight: ($(window).height() - 150),
                        toolbar: [' . System::$toolbar . '],
                        buttons: {
                            elfinder: window.elfinderDialog,
                            gxcode: window.gxcodeBtn
                        },
                        callbacks: {
                            onImageUpload: function(files) { sendFile(files[0]); },
                            onPaste: function (e) {
                                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData("Text");
                                e.preventDefault();
                                document.execCommand("insertText", false, bufferText);
                            }
                        }
                    }); 
                });
            });
        </script>', 'footer', ['summernote-js', 'summernote-elfinder-button']);

        // Enqueue everything
        Asset::enqueue('summernote-css');
        Asset::enqueue('elfinder-helper');
        Asset::enqueue('summernote-init');
    }

    /**
     * Callback for the Editor.js (Block-based) editor.
     * Registers and enqueues Editor.js core, tools, and custom initialization logic.
     */
    public static function editorjs()
    {
        // Register EditorJS Core & Tools
        Asset::register('editorjs-core', 'js', 'https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.30.6/dist/editorjs.umd.min.js', 'footer');
        Asset::register('editorjs-header', 'js', 'https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.7/dist/header.umd.min.js', 'footer', ['editorjs-core']);
        Asset::register('editorjs-list', 'js', 'https://cdn.jsdelivr.net/npm/@editorjs/list@2.0.9/dist/editorjs-list.umd.min.js', 'footer', ['editorjs-core']);
        Asset::register('editorjs-image', 'js', 'https://cdn.jsdelivr.net/npm/@editorjs/image@2.10.1/dist/image.umd.min.js', 'footer', ['editorjs-core']);
        Asset::register('editorjs-quote', 'js', 'https://cdn.jsdelivr.net/npm/@editorjs/quote@2.7.6/dist/quote.umd.min.js', 'footer', ['editorjs-core']);
        Asset::register('editorjs-table', 'js', 'https://cdn.jsdelivr.net/npm/@editorjs/table@2.4.3/dist/table.umd.min.js', 'footer', ['editorjs-core']);
        Asset::register('editorjs-delimiter', 'js', 'https://cdn.jsdelivr.net/npm/@editorjs/delimiter@1.3.0/dist/bundle.min.js', 'footer', ['editorjs-core']);

        // EditorJS Init logic
        $url = Url::ajax('saveimage');
        $elfinderUrl = Url::ajax('elfinder');
        $encodedUrl = json_encode($url);
        $encodedElfinderUrl = json_encode($elfinderUrl);

        Asset::register('editorjs-init', 'raw', '
        <style>
            .editorjs-wrapper { border: 1px solid #dee2e6; border-radius: 0.5rem; min-height: 400px; padding: 16px; background: #fff; }
            .ce-block__content, .ce-toolbar__content { max-width: 100%; }
        </style>
        <script>
            window.addEventListener("load", function() {
                var editors = {};
                window.__gxEditors = editors;
                var elfinderUrlRaw = ' . $encodedElfinderUrl . ';

                document.querySelectorAll(".editor").forEach(function(textarea, idx) {
                    var editorId = "editorjs-holder-" + idx;
                    var wrapper = document.createElement("div");
                    wrapper.id = editorId; wrapper.className = "editorjs-wrapper";
                    textarea.parentNode.insertBefore(wrapper, textarea);
                    textarea.style.display = "none";

                    var tools = {
                        header: { class: Header, inlineToolbar: true },
                        list: { class: EditorjsList, inlineToolbar: true },
                        quote: { class: Quote, inlineToolbar: true },
                        table: { class: Table, inlineToolbar: true },
                        delimiter: Delimiter,
                        image: {
                            class: ImageTool,
                            config: {
                                uploader: {
                                    uploadByFile: function(file) {
                                        return new Promise(function(resolve, reject) {
                                            fetch(elfinderUrlRaw + (elfinderUrlRaw.indexOf("?") === -1 ? "?" : "&") + "cmd=open&init=1&target=")
                                            .then(function(res) { return res.json(); })
                                            .then(function(initData) {
                                                if (!initData || !initData.cwd) return reject("Failed to init elfinder API");
                                                var target = initData.cwd.hash;
                                                var fd = new FormData();
                                                fd.append("cmd", "upload");
                                                fd.append("target", target);
                                                fd.append("upload[]", file);
                                                fetch(elfinderUrlRaw + (elfinderUrlRaw.indexOf("?") === -1 ? "?" : "&") + "auto_sort=1", { method: "POST", body: fd })
                                                .then(function(r) { return r.json(); })
                                                .then(function(upRes) {
                                                    if (upRes.added && upRes.added.length > 0) resolve({ success: 1, file: { url: upRes.added[0].url } });
                                                    else reject(upRes.error || "Upload failed");
                                                }).catch(reject);
                                            }).catch(reject);
                                        });
                                    }
                                }
                            }
                        }
                    };

                    var initData = { blocks: [] };
                    var existing = textarea.value.trim();
                    if (existing) {
                        try {
                            var parserDiv = document.createElement("div");
                            parserDiv.innerHTML = existing;
                            Array.from(parserDiv.childNodes).forEach(function(node) {
                                if (node.nodeType === 1) {
                                    var tag = node.tagName;
                                    if (tag === "P") initData.blocks.push({ type: "paragraph", data: { text: node.innerHTML } });
                                    else if (tag.match(/^H[1-6]$/)) initData.blocks.push({ type: "header", data: { text: node.innerText, level: parseInt(tag[1]) } });
                                    else if (tag === "UL" || tag === "OL") initData.blocks.push({ type: "list", data: { style: tag === "OL" ? "ordered" : "unordered", items: Array.from(node.querySelectorAll("li")).map(li => li.innerHTML) } });
                                    else if (node.querySelector("img")) initData.blocks.push({ type: "image", data: { file: { url: node.querySelector("img").src }, caption: (node.innerText || "").trim() } });
                                }
                            });
                        } catch(e) { console.error(e); }
                    }

                    editors[idx] = new EditorJS({
                        holder: editorId, tools: tools, data: initData
                    });
                    editors[idx]._textarea = textarea;
                });

                document.querySelectorAll("form").forEach(function(form) {
                    var formEditors = [];
                    Object.keys(editors).forEach(function(idx) {
                        if (form.contains(editors[idx]._textarea)) {
                            formEditors.push(idx);
                        }
                    });

                    if (formEditors.length === 0) return;

                    form.addEventListener("submit", function(e) {
                        if (form.dataset.gxEditorSaved === "true") {
                            return; // Allow submission to proceed naturally
                        }

                        e.preventDefault();
                        var promises = formEditors.map(function(idx) {
                            return editors[idx].save().then(function(data) {
                                var html = "";
                                data.blocks.forEach(function(block) {
                                    switch (block.type) {
                                        case "header": html += "<h" + block.data.level + ">" + block.data.text + "</h" + block.data.level + ">"; break;
                                        case "paragraph": html += "<p>" + block.data.text + "</p>"; break;
                                        case "list":
                                            var tag = block.data.style === "ordered" ? "ol" : "ul";
                                            html += "<" + tag + ">" + block.data.items.map(i => "<li>"+i+"</li>").join("") + "</" + tag + ">";
                                            break;
                                        case "image": html += "<div class=\'text-center mb-3\'><img src=\'"+block.data.file.url+"\' class=\'img-fluid rounded\'></div>"; break;
                                    }
                                });
                                editors[idx]._textarea.value = html;
                            });
                        });
                        
                        Promise.all(promises).then(function() { 
                            form.dataset.gxEditorSaved = "true";
                            var s = document.createElement("input"); s.type="hidden"; s.name="submit"; s.value="1"; form.appendChild(s);
                            
                            // Let jQuery handle the AJAX submit natively if it exists
                            if (window.jQuery) {
                                window.jQuery(form).trigger("submit");
                            } else {
                                HTMLFormElement.prototype.submit.call(form);
                            }
                        });
                    });
                });
            });
        </script>', 'footer', ['editorjs-delimiter', 'elfinder-helper']);

        // Enqueue everything
        Asset::enqueue('editorjs-init');
    }
}
