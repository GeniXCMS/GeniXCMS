<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>
<form action="index.php?page=settings-media" enctype="multipart/form-data" method="post">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data);?>
        <?=Hooks::run('admin_page_top_action', $data);?>
    </div>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 text-start">
                <h3 class="fw-bold text-dark mb-0"><?=_("Media Assets");?></h3>
                <p class="text-muted small mb-0"><?=_("Configure image processing, watermarking, and automated optimizations.");?></p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="btn-group gap-2">
                    <button type="submit" name="change" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> <?=_("Save Config");?>
                    </button>
                    <button type="reset" class="btn btn-light border rounded-pill px-4">
                        <?=_("Discard");?>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Protection & Branding -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <h6 class="fw-bold text-primary text-uppercase mb-4"><?=_("Branding & Protection");?></h6>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="form-check form-switch bg-light rounded-4 p-3 ps-5 border-start border-4 border-info shadow-none">
                                    <input class="form-check-input" type="checkbox" name="media_use_watermark" id="useWatermark" <?= (isset($data['media_use_watermark']) && $data['media_use_watermark'] == 'on') ? 'checked' : ''; ?>>
                                    <label class="form-check-label ps-2" for="useWatermark">
                                        <div class="fw-bold text-dark"><?=_("Active Watermark Protection");?></div>
                                        <div class="extra-small text-muted"><?=_("Apply a transparent overlay to thumbnails during generation.");?></div>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Overlay Graphic");?></label>
                                <div class="bg-white border rounded-4 p-3 text-center position-relative overflow-hidden shadow-none" style="min-height: 180px;">
                                    <?php 
                                    $media_watermark_image = $data['media_watermark_image'] ?? '';
                                    $media_watermark_image_url = $media_watermark_image ? Site::$url.$media_watermark_image : '';
                                    ?>
                                    <input type="file" id="ImageBrowse" name="file" hidden>
                                    <div id="fileBrowse" class="cursor-pointer h-100 d-flex flex-column align-items-center justify-content-center" onclick="uploadWatermark()">
                                        <?php if($media_watermark_image_url): ?>
                                            <img src="<?=$media_watermark_image_url;?>" class="img-fluid rounded mb-2" id="watermark_preview" style="max-height: 100px;">
                                        <?php else: ?>
                                            <div class="d-flex flex-column align-items-center py-4">
                                                <i class="bi bi-plus-circle-dotted fs-1 text-muted opacity-50 mb-2"></i>
                                                <div class="extra-small text-muted"><?=_("Click to upload PNG");?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="media_watermark_image" id="media_watermark_image" value="<?=$media_watermark_image;?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Position Alignment");?></label>
                                    <select name="media_watermark_position" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                                        <?php 
                                        $pos = ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'];
                                        $media_watermark_position = $data['media_watermark_position'] ?? 'bottom-right';
                                        foreach($pos as $v) {
                                            $sel = ($v == $media_watermark_position) ? "selected" : "";
                                            echo "<option value=\"{$v}\" {$sel}>"._($v)."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted text-uppercase"><?=_("Opacity Intensity");?> (1-100)</label>
                                    <div class="input-group">
                                        <input type="number" name="media_watermark_opacity" class="form-control border-0 bg-light rounded-start-3 py-2 shadow-none" value="<?= $data['media_watermark_opacity'] ?? 30; ?>">
                                        <span class="input-group-text border-0 bg-light rounded-end-3 py-1 opacity-50 px-3">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Optimization -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-success text-uppercase mb-4"><?=_("Optimization & Speed");?></h6>
                        <div class="mb-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="media_autogenerate_webp" id="autoWebp" <?= (isset($data['media_autogenerate_webp']) && $data['media_autogenerate_webp'] == 'on') ? 'checked' : ''; ?>>
                                <label class="form-check-label ps-2 fs-7 fw-bold text-dark" for="autoWebp"><?=_("Generate Next-Gen WebP");?></label>
                                <div class="extra-small text-muted ps-2 ms-4"><?=_("Reduces file size by up to 30% without quality loss.");?></div>
                            </div>
                        </div>
                        <hr class="opacity-10 my-4">
                        <div class="mb-0">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="media_autoresize_image" id="autoResize" <?= (isset($data['media_autoresize_image']) && $data['media_autoresize_image'] == 'on') ? 'checked' : ''; ?>>
                                <label class="form-check-label ps-2 fs-7 fw-bold text-dark" for="autoResize"><?=_("Automated Downsizing");?></label>
                            </div>
                            <div class="ps-4 ms-2">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase opacity-75"><?=_("Maximum Width Constraint");?></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="media_autoresize_width" class="form-control border-0 bg-light rounded-start-pill px-3 shadow-none" value="<?= $data['media_autoresize_width'] ?? 1000; ?>">
                                    <span class="input-group-text border-0 bg-light rounded-end-pill pe-3 opacity-50 ps-1">px</span>
                                </div>
                                <div class="extra-small text-info mt-2"><i class="bi bi-info-circle me-1"></i><?=_("Larger uploads will be resized down.");?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="token" value="<?=TOKEN;?>">
</form>

<script>
$(document).ready(function (e) {
    $("#ImageBrowse").on("change", function() {
        var reader,
        input = document.getElementById('ImageBrowse'),
        preview = document.getElementById('watermark_preview');

        if (input.files && input.files[0]) {
            reader = new FileReader();
            reader.onload = function(e) {
                if(preview) {
                    preview.setAttribute('src', e.target.result);
                } else {
                    $('#fileBrowse').html('<img src="'+e.target.result+'" class="img-fluid rounded mb-2 text-center" id="watermark_preview" style="max-height: 100px; display: block; margin: 0 auto;">');
                }
                $.ajax({
                    type:'POST',
                    url: '<?=Url::ajax("saveimage");?>',
                    data: {file: e.target.result, file_name: input.files[0]['name']},
                    success:function(data){
                        data = JSON.parse(data);
                        $('#media_watermark_image').val(data.path);
                    }
                });
            }
            reader.readAsDataURL(input.files[0]);
        } 
    });
});

function uploadWatermark() {
    var input = document.getElementById('ImageBrowse');
    input.click();    
}
</script>