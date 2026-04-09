<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

// Save Settings
if (isset($_POST['save_nixslider'])) {
    if (!Token::validate(Typo::cleanX($_POST['token']))) {
        $alertDanger[] = _('Invalid security token.');
    } else {
        $sliders = [];
        if (isset($_POST['slider_id']) && is_array($_POST['slider_id'])) {
            foreach ($_POST['slider_id'] as $k => $id) {
                $cleanId = Typo::cleanX($id);
                if (empty($cleanId)) continue;
                
                $sliders[$cleanId] = [
                    'height' => Typo::cleanX($_POST['slider_height'][$k]),
                    'images' => []
                ];
                
                if (isset($_POST['img_url'][$cleanId]) && is_array($_POST['img_url'][$cleanId])) {
                    foreach ($_POST['img_url'][$cleanId] as $imgKey => $url) {
                        if (empty($url)) continue;
                        $sliders[$cleanId]['images'][] = [
                            'url' => Typo::cleanX($url),
                            'title' => Typo::cleanX($_POST['img_title'][$cleanId][$imgKey] ?? ''),
                            'caption' => Typo::cleanX($_POST['img_caption'][$cleanId][$imgKey] ?? ''),
                        ];
                    }
                }
            }
        }
        
        Options::update('nixslider_data', json_encode($sliders));
        $alertSuccess[] = _('Sliders saved successfully.');
    }
}

System::alert(['alertSuccess' => $alertSuccess ?? [], 'alertDanger' => $alertDanger ?? []]);

$data = Options::get('nixslider_data');
$sliders = $data ? json_decode($data, true) : [];
$token = TOKEN;

require_once GX_LIB . '/UiBuilder.class.php';

// Prepare unique ID generator for inline uses
function getUid() {
    return substr(md5(uniqid(rand(), true)), 0, 8);
}

ob_start();
?>
<style>
    .media-drop-zone {
        transition: all 0.3s ease;
        border: 2px dashed #e2e8f0;
    }
    .media-drop-zone:hover {
        border-color: var(--gx-primary, #3b82f6);
        background-color: rgba(59, 130, 246, 0.05) !important;
    }
</style>

<div id="sliders-container">
<?php if (empty($sliders)): ?>
    <div class="alert alert-info" id="no-sliders-msg">No sliders created yet. Click "Add New Slider" to start.</div>
<?php else: 
    foreach ($sliders as $id => $slider): ?>
    <div class="card mb-4 border border-primary-subtle slider-item shadow-sm" id="slider-<?=$id;?>">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <div>
                <strong>Slider ID:</strong> <input type="text" name="slider_id[]" value="<?=$id;?>" class="form-control form-control-sm d-inline-block w-auto shadow-none" required readonly>
                <span class="ms-3 text-muted small">Shortcode: <code>[nixslider id="<?=$id;?>"]</code></span>
            </div>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" onclick="removeSlider('slider-<?=$id;?>')"><i class="bi bi-trash"></i> Remove</button>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label fw-bold extra-small text-uppercase tracking-wider">Slider Height</label>
                <input type="text" name="slider_height[]" class="form-control" value="<?=isset($slider['height']) ? $slider['height'] : '400px';?>" placeholder="e.g. 400px, 100vh">
            </div>
            
            <h6 class="fw-bold tracking-wider text-primary mb-3">Slide Items</h6>
            <div class="images-container" id="images-<?=$id;?>">
                <?php if (is_array($slider['images'])): foreach ($slider['images'] as $imgKey => $img): $uid = getUid(); ?>
                <div class="row align-items-center mb-4 pb-4 border-bottom img-item">
                    <div class="col-md-3 text-center">
                        <input type="hidden" name="img_url[<?=$id;?>][]" id="url_<?=$uid;?>" value="<?=$img['url'];?>" required>
                        <div class="media-drop-zone rounded-3 border-2 border-dashed bg-light p-2 position-relative" style="cursor: pointer; min-height: 120px; display:flex; align-items:center; justify-content:center; flex-direction: column;" onclick="nixsliderElfinder('url_<?=$uid;?>', 'prev_<?=$uid;?>', 'ph_<?=$uid;?>')">
                            <?php if (!empty($img['url'])): ?>
                                <img id="prev_<?=$uid;?>" class="img-fluid rounded shadow-sm" src="<?=$img['url'];?>" style="max-height: 100px; width: 100%; object-fit: cover;">
                                <div id="ph_<?=$uid;?>" class="d-none">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                                    <p class="text-muted extra-small mb-0 mt-1">Select Image</p>
                                </div>
                            <?php else: ?>
                                <img id="prev_<?=$uid;?>" class="img-fluid rounded shadow-sm d-none" style="max-height: 100px; width: 100%; object-fit: cover;">
                                <div id="ph_<?=$uid;?>">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                                    <p class="text-muted extra-small mb-0 mt-1">Select Image</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-2">
                            <input type="text" name="img_title[<?=$id;?>][]" class="form-control bg-light shadow-none" value="<?=$img['title'];?>" placeholder="Title (optional)">
                        </div>
                        <div>
                            <input type="text" name="img_caption[<?=$id;?>][]" class="form-control bg-light shadow-none" value="<?=$img['caption'];?>" placeholder="Caption (optional)">
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-outline-danger rounded-circle shadow-sm" title="Remove image" onclick="this.closest('.img-item').remove()"><i class="bi bi-x py-1"></i></button>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
            
            <button type="button" class="btn btn-secondary rounded-pill mt-2 shadow-sm" onclick="addImage('<?=$id;?>')">
                <i class="bi bi-plus-circle me-1"></i> Add Another Slide
            </button>
        </div>
    </div>
<?php endforeach; endif; ?>
</div>

<script>
function generateUid() {
    return Math.random().toString(36).substring(2, 10);
}

function nixsliderElfinder(inputId, previewId, placeholderId) {
    let url = "<?=Url::ajax('elfinder');?>";
    let fm = $("<div/>").dialogelfinder({
        url : url,
        lang : "en", width : 840, height: 450,
        destroyOnClose : true,
        getFileCallback : function(file, fm) {
            document.getElementById(inputId).value = file.url;
            let prev = document.getElementById(previewId);
            let ph = document.getElementById(placeholderId);
            if(prev) {
                prev.src = file.url;
                prev.classList.remove('d-none');
            }
            if(ph) {
                ph.classList.add('d-none');
            }
        },
        commandsOptions : { getfile : { oncomplete : "close", folders : false } }
    }).dialogelfinder("instance");
}

function addSlider() {
    let id = prompt("Enter a unique ID for the new slider (e.g. home, gallery_1):");
    if (!id) return;
    id = id.replace(/[^a-zA-Z0-9_-]/g, '');
    if (!id) return; // invalid
    if (document.getElementById('slider-' + id)) {
        alert("Slider ID already exists!");
        return;
    }
    
    let noMsg = document.getElementById('no-sliders-msg');
    if (noMsg) noMsg.style.display = 'none';
    
    let container = document.getElementById('sliders-container');
    let html = `
    <div class="card mb-4 border border-primary-subtle slider-item shadow-sm" id="slider-${id}">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <div>
                <strong>Slider ID:</strong> <input type="text" name="slider_id[]" value="${id}" class="form-control form-control-sm d-inline-block w-auto shadow-none" required readonly>
                <span class="ms-3 text-muted small">Shortcode: <code>[nixslider id="${id}"]</code></span>
            </div>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" onclick="removeSlider('slider-${id}')"><i class="bi bi-trash"></i> Remove</button>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <label class="form-label fw-bold extra-small text-uppercase tracking-wider">Slider Height</label>
                <input type="text" name="slider_height[]" class="form-control" value="400px" placeholder="e.g. 400px, 100vh">
            </div>
            <h6 class="fw-bold tracking-wider text-primary mb-3">Slide Items</h6>
            <div class="images-container" id="images-${id}"></div>
            <button type="button" class="btn btn-secondary rounded-pill mt-2 shadow-sm" onclick="addImage('${id}')">
                <i class="bi bi-plus-circle me-1"></i> Add Another Slide
            </button>
        </div>
    </div>`;
    
    container.insertAdjacentHTML('beforeend', html);
    addImage(id);
}

function addImage(id) {
    let container = document.getElementById('images-' + id);
    let uid = generateUid();
    let html = `
    <div class="row align-items-center mb-4 pb-4 border-bottom img-item">
        <div class="col-md-3 text-center">
            <input type="hidden" name="img_url[${id}][]" id="url_${uid}" value="" required>
            <div class="media-drop-zone rounded-3 border-2 border-dashed bg-light p-2 position-relative" style="cursor: pointer; min-height: 120px; display:flex; align-items:center; justify-content:center; flex-direction: column;" onclick="nixsliderElfinder('url_${uid}', 'prev_${uid}', 'ph_${uid}')">
                <img id="prev_${uid}" class="img-fluid rounded shadow-sm d-none" style="max-height: 100px; width: 100%; object-fit: cover;">
                <div id="ph_${uid}">
                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                    <p class="text-muted extra-small mb-0 mt-1">Select Image</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="mb-2">
                <input type="text" name="img_title[${id}][]" class="form-control bg-light shadow-none" value="" placeholder="Title (optional)">
            </div>
            <div>
                <input type="text" name="img_caption[${id}][]" class="form-control bg-light shadow-none" value="" placeholder="Caption (optional)">
            </div>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-outline-danger rounded-circle shadow-sm" title="Remove image" onclick="this.closest('.img-item').remove()"><i class="bi bi-x py-1"></i></button>
        </div>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
}

function removeSlider(elementId) {
    if (confirm('Are you sure you want to remove this slider completely?')) {
        document.getElementById(elementId).remove();
    }
}
</script>
<?php
$slidersHtml = ob_get_clean();

$schema = [
    'header' => [
        'title'    => 'Nixslider Manager',
        'subtitle' => 'Create professional image sliders and use shortcodes in your pages.',
        'icon'     => 'bi bi-images',
        'button'   => [
            'type'  => 'button',
            'btn_type'=> 'button',
            'label' => 'Add New Slider',
            'icon'  => 'bi bi-plus-lg',
            'class' => 'btn btn-outline-primary rounded-pill px-4 shadow-sm',
            'attr'  => 'onclick="addSlider()"',
        ]
    ],
    'default_tab' => 'manage',
    'tabs' => [
        'manage' => ['label' => 'Manage Sliders', 'icon' => 'bi bi-sliders', 'content' => []],
        'howto'  => ['label' => 'How To Use', 'icon' => 'bi bi-book', 'content' => []],
    ],
];

// --- MANAGE TAB ---
$schema['tabs']['manage']['content'][] = [
    'type'   => 'form',
    'action' => 'index.php?page=mods&mod=nixslider',
    'hidden' => ['token' => $token],
    'fields' => [
        [
            'type'  => 'raw',
            'html'  => $slidersHtml
        ],
        [
            'type'  => 'raw',
            'html'  => '<hr class="my-4">'
        ],
        [
            'type'  => 'button',
            'name'  => 'save_nixslider',
            'label' => 'Save All Changes',
            'icon'  => 'bi bi-save',
            'class' => 'btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm',
        ],
    ],
];

// --- HOW TO USE TAB ---
$schema['tabs']['howto']['content'][] = [
    'type'  => 'row',
    'items' => [
        [
            'width'   => 12,
            'content' => [
                'type'          => 'card',
                'title'         => 'Using Nixslider',
                'icon'          => 'bi bi-info-circle',
                'body_elements' => [
                    ['type' => 'raw', 'html' => '
                    <p class="lh-lg">Nixslider is a custom-built, professional image slider designed specifically for GeniXCMS.</p>
                    <ol class="ps-3 lh-lg">
                        <li>Click the <b>Add New Slider</b> button on the Manage tab to create your first slider.</li>
                        <li>Give it a unique ID (for example: <kbd>home</kbd> or <kbd>hero_slider</kbd>).</li>
                        <li>Click <b>Select Image</b> to open the File Manager and choose your pictures.</li>
                        <li>Add titles, captions, and customize the slider height (like <kbd>400px</kbd> or <kbd>70vh</kbd>).</li>
                        <li>Save your changes.</li>
                        <li>Copy the shortcode generated for your slider. It will look something like this: <code>[nixslider id="home"]</code></li>
                        <li>Paste that shortcode anywhere on your Pages, Posts, or Widgets!</li>
                    </ol>
                    <h6 class="fw-bold mt-4 pt-4 border-top text-primary"><i class="bi bi-palette2 me-2"></i> Customizing Appearance</h6>
                    <p class="mt-3">Nixslider is designed to be highly customizable. You can override its styles directly in your theme\'s CSS file (e.g., <code>style.css</code> or <code>blog.css</code>) using the classes below:</p>
                    <div class="bg-dark text-white p-3 rounded-3 shadow-sm mt-3 overflow-auto" style="max-height: 250px;">
                        <pre class="mb-0 fs-7" style="color: #cbd5e1;"><code>/* Container Styling */
.nixslider-container {
    border-radius: 15px;
    overflow: hidden;
}

/* Caption Customization */
.nixslider-caption {
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
}

/* Title and Description */
.nixslider-caption h3 {
    font-size: 28px;
    font-weight: 800;
}

/* Control Buttons */
.nixslider-prev, .nixslider-next {
    background-color: rgba(255,255,255,0.2);
    border-radius: 50%;
}

/* Fix Theme Style Conflict (border/padding) */
.nixslider-slide img {
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
}</code></pre>
                    </div>']
                ],
            ],
        ],
    ],
];

$builder = new UiBuilder($schema);
$builder->render();
