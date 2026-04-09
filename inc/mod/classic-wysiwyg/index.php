<?php
/**
 * Name: Classic WYSIWYG Editor
 * Desc: Modul untuk mengaktifkan editor TinyMCE yang elegan dan mudah digunakan.
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id
 * License: MIT License
 * Icon: bi bi-pencil-square
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class ClassicWYSIWYG
{
    public static function init()
    {
        // Masukkan script editor ke footer admin hanya saat mode editor aktif
        Hooks::attach('admin_footer_action', array('ClassicWYSIWYG', 'loadEditor'));
    }

    public static function loadEditor()
    {
        // Hanya muat jika editor dibutuhkan di halaman tersebut
        if (isset($GLOBALS['editor']) && $GLOBALS['editor'] == true) {
            ?>
            <!-- Classic WYSIWYG Editor Script -->
            <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
            <div class="editor-toggle-btn position-fixed bottom-0 end-0 m-4 shadow-lg" style="z-index: 1060;">
                <button type="button" class="btn btn-warning rounded-pill px-4" id="btn-toggle-editor">
                    <i class="bi bi-code-slash me-2"></i> Toggle WYSIWYG
                </button>
            </div>

            <script>
                $(function () {
                    let editorActive = false;

                    $('#btn-toggle-editor').on('click', function () {
                        if (!editorActive) {
                            // Sembunyikan Summernote (editor default)
                            $('.note-editor').hide();
                            $('.editor').show(); // Pastikan textarea asli tampil untuk tinymce

                            // Inisialisasi TinyMCE pada element dengan class .editor
                            tinymce.init({
                                selector: '.editor',
                                height: 500,
                                menubar: true,
                                plugins: [
                                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                                ],
                                toolbar: 'undo redo | blocks | ' +
                                    'bold italic backcolor | alignleft aligncenter ' +
                                    'alignright alignjustify | bullist numlist outdent indent | ' +
                                    'removeformat | help',
                                content_style: 'body { font-family:Plus Jakarta Sans,Helvetica,Arial,sans-serif; font-size:16px }',
                                setup: function (editor) {
                                    editor.on('change', function () {
                                        editor.save(); // Sinkronkan konten ke textarea asli
                                    });
                                }
                            });

                            $(this).removeClass('btn-warning').addClass('btn-primary')
                                .html('<i class="bi bi-layout-text-window me-2"></i> Switch to Standard');
                            editorActive = true;
                            toastr.info("Classic WYSIWYG Activated");
                        } else {
                            // Matikan TinyMCE
                            tinymce.remove('.editor');
                            $('.note-editor').show();

                            $(this).removeClass('btn-primary').addClass('btn-warning')
                                .html('<i class="bi bi-code-slash me-2"></i> Toggle WYSIWYG');
                            editorActive = false;
                            toastr.info("Standard Editor Restored");
                        }
                    });
                });
            </script>
            <style>
                #btn-toggle-editor {
                    border: 2px solid #fff;
                    transition: all 0.3s ease;
                    font-weight: 700;
                }

                #btn-toggle-editor:hover {
                    transform: scale(1.05);
                }
            </style>
            <?php
        }
    }
}

ClassicWYSIWYG::init();
