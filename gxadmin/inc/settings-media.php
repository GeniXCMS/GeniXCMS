<?php
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * 
 * @since 1.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

// ── PREPARE SCHEMA ────────────────────────────────────────────────
$schema = [
    'header' => [
        'title' => _('Media Settings'),
        'subtitle' => _('Manage where and how your website images are stored and optimized.'),
        'icon' => 'bi bi-images',
        'button' => [
            'type' => 'button',
            'name' => 'change',
            'label' => _('Sync Media Parameters'),
            'icon' => 'bi bi-hdd-fill',
            'class' => 'btn btn-primary rounded-pill px-4 shadow-sm fw-bold',
            'attr' => 'value="Change"'
        ],
    ],
    'card_wrapper' => true,
    'tab_mode' => 'js',
    'tab_style' => 'modern',
    'tabs' => [
        'storage' => [
            'label' => _('Storage Location'),
            'icon' => 'bi bi-folder-check',
            'content' => [
                ['type' => 'heading', 'text' => _('Main File Storage'), 'icon' => 'bi bi-hdd', 'subtitle' => _('Choose where to store your uploaded files.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'raw',
                                    'html' => '
                            <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("Select Storage Method") . '</label>
                            <select name="media_storage_backend" id="storageBackend" class="form-select border bg-light rounded-4 py-1 px-3 shadow-none fs-8 fw-bold mb-4" onchange="toggleStorageFields()">
                                <option value="local" ' . (Options::v('media_storage_backend') == 'local' || Options::v('media_storage_backend') == '' ? 'selected' : '') . '>' . _("Local Folder (Recommended)") . '</option>
                                <option value="ftp" ' . (Options::v('media_storage_backend') == 'ftp' ? 'selected' : '') . '>' . _("External Server (FTP)") . '</option>
                                <option value="s3" ' . (Options::v('media_storage_backend') == 's3' ? 'selected' : '') . '>' . _("Cloud Storage (Amazon S3 / DO Spaces)") . '</option>
                            </select>'
                                ],
                                [
                                    'type' => 'raw',
                                    'html' => '
                            <div class="local-field border-start border-4 border-primary ps-4 py-2 mb-4">
                                <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.65rem;">' . _("Base Folder Path") . '</label>
                                <input type="text" name="media_local_path" value="' . (Options::v('media_local_path') ?: 'assets/media/') . '" class="form-control border bg-light shadow-none rounded-4 py-1 fs-8 fw-bold">
                                <div class="extra-small text-muted mt-2 fw-medium">' . _("Default is 'assets/media/'. Keep it as is if you are unsure.") . '</div>
                            </div>'
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'alert',
                                    'style' => 'warning',
                                    'content' => '
                            <h6 class="fw-black text-warning text-uppercase extra-small mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Important Note</h6>
                            <p class="extra-small mb-0 lh-base fw-bold">' . _("Switching storage methods will not migrate existing files automatically. Ensure the target is reachable to prevent broken images.") . '</p>'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'raw',
                    'html' => '
                    <div class="ftp-field col-md-12 mb-4" style="display:none;">
                        <div class="card border-0 bg-light rounded-5 p-4 border-start border-4 border-info shadow-sm">
                            <h6 class="fw-black fs-8 mb-4 text-info text-uppercase tracking-widest"><i class="bi bi-link-45deg me-2"></i> ' . _("FTP Server Settings") . '</h6>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Server Hostname") . '</label>
                                    <input type="text" name="media_ftp_host" value="' . Options::v('media_ftp_host') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold" placeholder="ftp.mysite.com">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Port") . '</label>
                                    <input type="text" name="media_ftp_port" value="' . (Options::v('media_ftp_port') ?: '21') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Username") . '</label>
                                    <input type="text" name="media_ftp_user" value="' . Options::v('media_ftp_user') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Password") . '</label>
                                    <input type="password" name="media_ftp_pass" value="' . Options::v('media_ftp_pass') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Remote Assets Directory") . '</label>
                                    <input type="text" name="media_ftp_path" value="' . (Options::v('media_ftp_path') ?: '/') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Public Media URL") . '</label>
                                    <input type="text" name="media_ftp_url" value="' . Options::v('media_ftp_url') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold" placeholder="https://cdn.mysite.com/">
                                </div>
                            </div>
                        </div>
                    </div>
                '
                ],
                [
                    'type' => 'raw',
                    'html' => '
                    <div class="s3-field col-md-12 mb-4" style="display:none;">
                        <div class="card border-0 bg-light rounded-5 p-4 border-start border-4 border-warning shadow-sm">
                            <h6 class="fw-black fs-8 mb-4 text-warning text-uppercase tracking-widest"><i class="bi bi-cloud me-2"></i> ' . _("S3 Cloud Storage Settings") . '</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Access Key ID") . '</label>
                                    <input type="text" name="media_s3_key" value="' . Options::v('media_s3_key') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Secret Access Key") . '</label>
                                    <input type="password" name="media_s3_secret" value="' . Options::v('media_s3_secret') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Bucket / Space Name") . '</label>
                                    <input type="text" name="media_s3_bucket" value="' . Options::v('media_s3_bucket') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Region") . '</label>
                                    <input type="text" name="media_s3_region" value="' . (Options::v('media_s3_region') ?: 'us-east-1') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-black text-muted text-uppercase" style="font-size:0.6rem;">' . _("Endpoint URL (Optional)") . '</label>
                                    <input type="text" name="media_s3_endpoint" value="' . Options::v('media_s3_endpoint') . '" class="form-control border bg-white shadow-none rounded-4 py-1 fs-8 fw-bold" placeholder="https://sgp1.digitaloceanspaces.com">
                                </div>
                            </div>
                        </div>
                    </div>
                '
                ]
            ]
        ],
        'processing' => [
            'label' => _('Optimization'),
            'icon' => 'bi bi-lightning',
            'content' => [
                ['type' => 'heading', 'text' => _('Image Sizes & Formats'), 'icon' => 'bi bi-aspect-ratio', 'subtitle' => _('Automatically reduce image sizes to keep your pages loading fast.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="media_autoresize_image" id="autoResize" ' . (Options::v('media_autoresize_image') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold" for="autoResize">' . _("Auto-Resize Uploaded Images") . '</label></div>'],
                                        ['type' => 'input', 'label' => _('Max Width (pixels)'), 'name' => 'media_autoresize_width', 'value' => (Options::v('media_autoresize_width') ?: '1200'), 'input_type' => 'number']
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="media_autogenerate_webp" id="autoWebp" ' . (Options::v('media_autogenerate_webp') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold" for="autoWebp">' . _("Enable WebP Generation (Faster)") . '</label></div>'],
                                        ['type' => 'alert', 'style' => 'light', 'content' => '<p class="extra-small text-muted mb-0 fw-bold"><i class="bi bi-info-circle me-1"></i> ' . _("WebP significantly reduces image file size without losing quality, making your site load faster.") . '</p>']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'watermark' => [
            'label' => _('Watermark'),
            'icon' => 'bi bi-patch-check',
            'content' => [
                ['type' => 'heading', 'text' => _('Digital Watermark'), 'icon' => 'bi bi-shield-shaded', 'subtitle' => _('Apply a transparent brand logo over your images for copyright protection.')],
                [
                    'type' => 'row',
                    'items' => [
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'body_elements' => [
                                        ['type' => 'raw', 'html' => '<div class="form-check form-switch mb-4"><input class="form-check-input" type="checkbox" name="media_watermark_enable" id="enableWM" ' . (Options::v('media_watermark_enable') == 'on' ? 'checked' : '') . '><label class="form-check-label fw-bold" for="enableWM">' . _("Enable Watermark Protection") . '</label></div>'],
                                        [
                                            'type' => 'raw',
                                            'html' => '
                                <div class="d-flex align-items-center gap-4 mb-4">
                                    <div class="wm_preview bg-white rounded-4 d-flex align-items-center justify-content-center border shadow-none" style="width:200px; height:120px; overflow:hidden;" id="wmPreviewBox">
                                        ' . (Options::v('media_watermark_image') ? '<img src="' . Site::$url . Options::v('media_watermark_image') . '" id="wm_preview" style="max-width:90%; max-height:90%;">' : '<i class="bi bi-image fs-1 text-muted opacity-25"></i>') . '
                                    </div>
                                    <div class="flex-fill">
                                        <label class="form-label fw-black text-muted extra-small text-uppercase tracking-wider" style="font-size:0.6rem;">' . _("Signature Image Source") . '</label>
                                        <div class="input-group input-group-sm mb-2 shadow-none rounded-3 overflow-hidden border">
                                            <input type="text" name="media_watermark_image" id="wm_image" value="' . Options::v('media_watermark_image') . '" class="form-control border-0 bg-white shadow-none fs-8 fw-bold">
                                            <button class="btn btn-white border-0 shadow-none px-3" type="button" onclick="uploadWM()">
                                                <i class="bi bi-upload text-primary mb-0"></i>
                                            </button>
                                        </div>
                                        <div class="extra-small text-muted fw-medium" style="font-size:0.65rem;">' . _("Transparent PNG recommended.") . '</div>
                                    </div>
                                </div>
                                <input type="file" id="WMFile" hidden>'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'content' => [
                                [
                                    'type' => 'card',
                                    'title' => _('Watermark Position'),
                                    'body_elements' => [
                                        [
                                            'type' => 'select',
                                            'name' => 'media_watermark_pos',
                                            'selected' => (Options::v('media_watermark_pos') ?: 'center'),
                                            'options' => [
                                                'center' => _('Absolute Center'),
                                                'top-left' => _('Top Left'),
                                                'top-right' => _('Top Right'),
                                                'bottom-left' => _('Bottom Left'),
                                                'bottom-right' => _('Bottom Right')
                                            ]
                                        ]
                                    ],
                                    'footer' => '<p class="extra-small text-muted mb-0 fw-bold">' . _("Select where the brand signature should appear on your images.") . '</p>'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

// ── RENDER ────────────────────────────────────────────────────────
echo '<div class="col-md-12">';
echo Hooks::run('admin_page_notif_action', $data);
echo '</div>';

echo '<form action="" method="POST" enctype="multipart/form-data">';
$builder = new UiBuilder($schema);
$builder->render();
echo '<input type="hidden" name="token" value="' . TOKEN . '">';
echo '</form>';
?>

<script>
    function toggleStorageFields() {
        const backend = document.getElementById('storageBackend').value;
        $('.ftp-field, .s3-field, .local-field').hide();
        if (backend === 'local') {
            $('.local-field').show();
        } else if (backend === 'ftp') {
            $('.ftp-field').show();
        } else if (backend === 's3') {
            $('.s3-field').show();
        }
    }

    function uploadWM() { document.getElementById('WMFile').click(); }

    $(document).ready(function () {
        toggleStorageFields();
        $("#WMFile").on("change", function () {
            var input = this, preview = document.getElementById('wm_preview');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    if (preview) preview.setAttribute('src', e.target.result);
                    else $('#wmPreviewBox').html('<img src="' + e.target.result + '" id="wm_preview" style="max-width:80%; max-height:80%;">');

                    $.post('<?= Url::ajax("saveimage"); ?>', { file: e.target.result, file_name: input.files[0]['name'] }, function (data) {
                        $('#wm_image').val(JSON.parse(data).path);
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    });
</script>