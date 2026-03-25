<?php
/**
 * Name: Page Dynamic Builder
 * Desc: Module builder drag & drop mirip Elementor untuk membuat halaman yang menakjubkan.
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: Antigravity AI
 * URI: https://genixcms.web.id
 * License: MIT License
 * Icon: bi bi-layers-half
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

class DynamicBuilder {
    public static function init() {
        Hooks::attach('admin_footer_action', array('DynamicBuilder', 'injectStatic'));
        Hooks::attach('page_param_form_bottom', array('DynamicBuilder', 'injectToggle'));
        Hooks::attach('footer_load_lib', array('DynamicBuilder', 'injectFrontendJS'));
        
        // Handle Ajax Save
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'dynamic_builder_save') {
            self::savePage();
        }
    }

    public static function injectFrontendJS() {
        $siteUrl = rtrim(Site::$url, '/').'/';
        $isSmartUrl = SMART_URL ? '1' : '0';
        ob_start();
        ?>
        <script>
        function gx_load_dynamic_content() {
            var containers = document.querySelectorAll('.recent-posts-container');
            if (containers.length > 0) {
                console.log('Dynamic Builder: Initializing recent posts for ' + containers.length + ' elements');
            }
            
            containers.forEach(function(container) {
                if (container.getAttribute('data-loaded') === 'true') return;
                
                var siteUrl = '<?=rtrim(Site::$url, "/");?>/';
                var isSmartUrl = '<?=SMART_URL ? "1" : "0";?>' === '1';
                var apiUrl = isSmartUrl 
                    ? siteUrl + 'ajax/api/public?action=recent_posts&num=3' 
                    : siteUrl + 'index.php?ajax=api&token=public&action=recent_posts&num=3';

                fetch(apiUrl)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(res) {
                        if (res.status === 'success' && res.data && res.data.length > 0) {
                            var html = `
                                <div class="bg-white p-4 rounded-4 shadow-sm border border-light w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="fw-bold m-0 border-start border-primary border-4 ps-3">Latest CMS Stories</h5>
                                        <a href="${siteUrl}" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold text-primary">View Library →</a>
                                    </div>
                                    <div class="row g-4 text-dark text-start">`;
                            
                            res.data.forEach(function(post) {
                                html += `
                                    <div class="col-md-4">
                                        <div class="card border-0 bg-transparent h-100">
                                            <div class="ratio ratio-16x9 mb-3">
                                                <a href="${post.url}"><img src="${post.image}" class="rounded-3 shadow-sm object-fit-cover w-100 h-100 border border-light" alt="${post.title}"></a>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="font-size: 10px;">${post.category.toUpperCase()}</span>
                                                    <small class="text-muted" style="font-size: 10px;">${post.date}</small>
                                                </div>
                                                <h6 class="fw-bold mb-2 lh-base"><a href="${post.url}" class="text-decoration-none text-dark">${post.title}</a></h6>
                                                <p class="text-muted small mb-0 opacity-75">${post.excerpt}</p>
                                            </div>
                                        </div>
                                    </div>`;
                            });

                            html += `</div></div>`;
                            container.innerHTML = html;
                            container.setAttribute('data-loaded', 'true');
                            container.style.display = 'block';
                        } else {
                            container.innerHTML = '<div class="alert alert-info">No posts found in CMS.</div>';
                        }
                    })
                    .catch(function(err) {
                        console.error('Dynamic Builder Error:', err);
                        container.innerHTML = '<div class="alert alert-danger">Failed to load posts.</div>';
                    });
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', gx_load_dynamic_content);
        } else {
            gx_load_dynamic_content();
        }
        window.addEventListener('load', gx_load_dynamic_content);
        </script>
        <?php
        echo ob_get_clean();
    }

    public static function injectToggle() {
        echo '
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 mt-4 bg-primary text-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold m-0"><i class="bi bi-magic me-2"></i> Design with Dynamic Builder</h5>
                    <p class="small m-0 text-white-50">Create stunning layouts with visual drag & drop experience.</p>
                </div>
                <button type="button" class="btn btn-white rounded-pill px-4 fw-bold" id="launch-builder">
                    Launch Visual Editor
                </button>
            </div>
        </div>
        <style>.btn-white { background: #fff; color: var(--gx-primary); border: none; transition: 0.3s; } .btn-white:hover { background: #f8fafc; transform: scale(1.05); }</style>
        ';
    }

    public static function injectStatic() {
        if (isset($_GET['page']) && $_GET['page'] == 'pages' && (isset($_GET['act']) && ($_GET['act'] == 'add' || $_GET['act'] == 'edit'))) {
            ?>
            <!-- Dynamic Builder Core -->
            <link href="https://cdn.jsdelivr.net/npm/grapesjs@0.21.10/dist/css/grapes.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/grapesjs@0.21.10/dist/grapes.min.js"></script>
            <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>
            <script src="https://unpkg.com/grapesjs-blocks-basic@1.0.1"></script>

            <!-- Builder Modal -->
            <div class="modal fade" id="builderModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content border-0">
                        <div class="modal-header bg-dark text-white border-0 py-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-box-fill me-2 text-primary fs-4"></i>
                                <h6 class="modal-title fw-bold">DYNAMIC BUILDER <span class="badge bg-primary ms-2 small" style="font-size: 10px;">PRO</span></h6>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" id="save-builder-page">
                                    <i class="bi bi-check-lg me-1"></i> Save & Export
                                </button>
                                <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">
                                    <i class="bi bi-x-lg me-1"></i> Close
                                </button>
                            </div>
                        </div>
                        <div class="modal-body p-0">
                            <div id="gjs" style="height: 100vh;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            $(function() {
                var editor = null;
                
                $('#launch-builder').on('click', function() {
                    $('#builderModal').modal('show');
                    
                    if (!editor) {
                        editor = grapesjs.init({
                            container: '#gjs',
                            fromElement: false,
                            height: '100%',
                            width: 'auto',
                            storageManager: false,
                            plugins: ['gjs-preset-webpage', 'gjs-blocks-basic'],
                            pluginsOpts: {
                                'gjs-preset-webpage': {},
                                'gjs-blocks-basic': {}
                            },
                            canvas: {
                                styles: [
                                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
                                    'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap'
                                ],
                                scripts: [
                                    'https://code.jquery.com/jquery-3.7.1.min.js',
                                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
                                ]
                            }
                        });

                        const bm = editor.BlockManager;

                        // --- BASIC WIDGETS ---
                        bm.add('heading-custom', {
                            label: '<div class="gjs-block-label"><i class="bi bi-type-h1 mb-2 fs-4 d-block"></i>Heading</div>',
                            category: 'Basic Widgets',
                            content: '<h2 class="fw-bold mb-3">Your Awesome Heading Here</h2>',
                        });

                        bm.add('separator-custom', {
                            label: '<div class="gjs-block-label"><i class="bi bi-hr mb-2 fs-4 d-block"></i>Separator</div>',
                            category: 'Basic Widgets',
                            content: '<hr class="my-5 border-2 opacity-25 w-100 mx-auto">',
                        });

                        bm.add('icon-info', {
                            label: '<div class="gjs-block-label"><i class="bi bi-info-square mb-2 fs-4 d-block"></i>Icon Info</div>',
                            category: 'Basic Widgets',
                            content: `
                                <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-3 shadow-sm border border-light">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                                        <i class="bi bi-info-circle-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Informative Title</h6>
                                        <p class="small text-muted mb-0">Describe important information or features briefly here.</p>
                                    </div>
                                </div>
                            `
                        });

                        bm.add('image-list', {
                            label: '<div class="gjs-block-label"><i class="bi bi-images mb-2 fs-4 d-block"></i>Image List</div>',
                            category: 'Basic Widgets',
                            content: `
                                <div class="row g-3">
                                    <div class="col-4"><img src="https://picsum.photos/400/300?1" class="img-fluid rounded-3 shadow-sm"></div>
                                    <div class="col-4"><img src="https://picsum.photos/400/300?2" class="img-fluid rounded-3 shadow-sm"></div>
                                    <div class="col-4"><img src="https://picsum.photos/400/300?3" class="img-fluid rounded-3 shadow-sm"></div>
                                </div>
                            `
                        });

                        bm.add('image-carousel', {
                            label: '<div class="gjs-block-label"><i class="bi bi-images mb-2 fs-4 d-block"></i>Carousel</div>',
                            category: 'Basic Widgets',
                            content: `
                                <div id="carousel-${Math.floor(Math.random() * 1000)}" class="carousel slide shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img src="https://picsum.photos/1200/600?nature" class="d-block w-100" alt="Slide 1">
                                            <div class="carousel-caption d-none d-md-block p-4 rounded-4" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(10px);">
                                                <h5 class="fw-bold">Experience Nature</h5>
                                                <p>Explore the beauty of the world through our lens.</p>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <img src="https://picsum.photos/1200/600?tech" class="d-block w-100" alt="Slide 2">
                                            <div class="carousel-caption d-none d-md-block p-4 rounded-4" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(10px);">
                                                <h5 class="fw-bold">Modern Technology</h5>
                                                <p>Building the future with cutting-edge solutions.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target=".carousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target=".carousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                </div>
                            `
                        });

                        bm.add('image-slideshow', {
                            label: '<div class="gjs-block-label"><i class="bi bi-play-circle mb-2 fs-4 d-block"></i>Slideshow</div>',
                            category: 'Basic Widgets',
                            content: `
                                <div class="slideshow-wrapper position-relative rounded-4 overflow-hidden shadow-lg mb-4">
                                    <div class="ratio ratio-21x9">
                                        <img src="https://picsum.photos/1600/900?modern" class="object-fit-cover w-100 h-100">
                                    </div>
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 pb-5">
                                        <div class="text-center text-white p-5">
                                            <h1 class="display-4 fw-bold mb-3">Premium Slideshow Content</h1>
                                            <p class="lead mb-4 opacity-75">Create beautiful full-width visual experiences effortlessly.</p>
                                            <button class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg">Discover More</button>
                                        </div>
                                    </div>
                                </div>
                            `
                        });

                        bm.add('spacer-widget', {
                            label: '<div class="gjs-block-label"><i class="bi bi-arrows-expand mb-2 fs-4 d-block"></i>Spacer</div>',
                            category: 'Basic Widgets',
                            content: '<div class="py-5 w-100"></div>',
                        });

                        bm.add('recent-posts-widget', {
                            label: '<div class="gjs-block-label"><i class="bi bi-card-text mb-2 fs-4 d-block"></i>Recent Posts</div>',
                            category: 'Basic Widgets',
                            content: { type: 'recent-posts-dynamic' }
                        });

                        bm.add('video-embed', {
                            label: '<div class="gjs-block-label"><i class="bi bi-youtube mb-2 fs-4 d-block"></i>Video</div>',
                            category: 'Basic Widgets',
                            content: `
                                <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm mb-4 border border-light">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            `
                        });

                        bm.add('newsletter-form', {
                            label: '<div class="gjs-block-label"><i class="bi bi-envelope-paper mb-2 fs-4 d-block"></i>Newsletter</div>',
                            category: 'Basic Widgets',
                            content: `
                                <div class="bg-primary bg-opacity-10 p-4 p-md-5 rounded-4 border border-primary border-opacity-25 text-center mt-4 mb-4">
                                    <h4 class="fw-bold mb-3 text-dark">Subscribe to Our Newsletter</h4>
                                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Get the latest articles, updates, and exclusive resources delivered directly to your inbox weekly.</p>
                                    <form class="d-flex max-w-sm mx-auto gap-2 justify-content-center" style="max-width: 400px;">
                                        <input type="email" class="form-control rounded-pill px-4 shadow-sm" placeholder="Your email address" required>
                                        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" type="submit">Subscribe</button>
                                    </form>
                                    <div class="small mt-3 text-muted"><i class="bi bi-shield-check text-success me-1"></i> No spam. Unsubscribe anytime.</div>
                                </div>
                            `
                        });

                        // Define Dynamic Recent Posts Component
                        editor.DomComponents.addType('recent-posts-dynamic', {
                            bool: {
                                draggable: true,
                                droppable: false,
                            },
                            model: {
                                defaults: {
                                    tagName: 'div',
                                    attributes: { class: 'recent-posts-container' },
                                    content: '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Synchronizing with CMS Library...</p></div>',
                                     script: function() {
                                        // Try to use global function first
                                        if (typeof gx_load_dynamic_content === 'function') {
                                            gx_load_dynamic_content();
                                        } else {
                                            // Fallback for direct execution in editor if global script not loaded yet
                                            const container = this;
                                            const siteUrl = '<?=rtrim(Site::$url, "/");?>/';
                                            const isSmartUrl = '<?=SMART_URL ? "1" : "0";?>' === '1';
                                            const apiUrl = isSmartUrl 
                                                ? siteUrl + 'ajax/api/public?action=recent_posts&num=3' 
                                                : siteUrl + 'index.php?ajax=api&token=public&action=recent_posts&num=3';
                                            
                                            fetch(apiUrl)
                                                .then(r => r.json())
                                                .then(res => {
                                                    if (res.status === 'success' && res.data.length > 0) {
                                                        let h = '<div class="bg-white p-4 rounded-4 shadow-sm border border-light w-100"><div class="d-flex justify-content-between align-items-center mb-4"><h5 class="fw-bold m-0 border-start border-primary border-4 ps-3">Latest CMS Stories</h5><a href="#" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold text-primary">View Library →</a></div><div class="row g-4 text-dark text-start">';
                                                        res.data.forEach(p => {
                                                            h += `<div class="col-md-4"><div class="card border-0 bg-transparent h-100"><div class="ratio ratio-16x9 mb-3"><img src="${p.image}" class="rounded-3 shadow-sm object-fit-cover w-100 h-100 border border-light"></div><div class="card-body p-0"><div class="d-flex justify-content-between align-items-center mb-2"><span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="font-size: 10px;">${p.category.toUpperCase()}</span><small class="text-muted" style="font-size: 10px;">${p.date}</small></div><h6 class="fw-bold mb-2 lh-base">${p.title}</h6><p class="text-muted small mb-0 opacity-75">${p.excerpt}</p></div></div></div>`;
                                                        });
                                                        h += '</div></div>';
                                                        container.innerHTML = h;
                                                        container.setAttribute('data-loaded', 'true');
                                                    }
                                                }).catch(e => console.error("Editor Fetch Error:", e));
                                        }
                                    }
                                }
                            }
                        });


                        // --- PREMIUM SECTIONS ---


                        bm.add('hero-premium', {
                            label: '<div class="gjs-block-label"><i class="bi bi-window-fullscreen mb-2 fs-4 d-block"></i>Hero Premium</div>',
                            category: 'Premium Sections',
                            content: `
                                <section class="py-5 bg-dark text-white position-relative overflow-hidden" style="min-height: 80vh; display: flex; align-items: center;">
                                    <div class="position-absolute w-100 h-100 top-0 start-0" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%); z-index: 1;"></div>
                                    <div class="container position-relative" style="z-index: 2;">
                                        <div class="row align-items-center">
                                            <div class="col-lg-7">
                                                <span class="badge bg-primary px-3 py-2 mb-3 rounded-pill fw-bold">NEW GENERATION</span>
                                                <h1 class="display-3 fw-bold mb-4">Build Your Future <br>With Modern Design</h1>
                                                <p class="lead mb-5 text-white-50">Empower your business with a stunning visual presence that captures attention and drives results effortlessly.</p>
                                                <div class="d-flex gap-3">
                                                    <a href="#" class="btn btn-primary btn-lg px-5 rounded-pill shadow">Get Started</a>
                                                    <a href="#" class="btn btn-outline-light btn-lg px-5 rounded-pill">Learn More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            `
                        });

                        // Custom Blocks: Feature Cards
                        bm.add('feature-cards', {
                            label: '<div class="gjs-block-label"><i class="bi bi-grid-3x3-gap mb-2 fs-4 d-block"></i>Feature Grid</div>',
                            category: 'Premium Sections',
                            content: `
                                <section class="py-5 bg-light">
                                    <div class="container py-5 text-center">
                                        <h2 class="fw-bold mb-3">Our Amazing Features</h2>
                                        <p class="text-muted mb-5">Discover why thousands of users trust our platform for their success.</p>
                                        <div class="row g-4">
                                            <div class="col-md-4">
                                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 70px; height: 70px;">
                                                        <i class="bi bi-lightning-charge fs-2 text-primary"></i>
                                                    </div>
                                                    <h5 class="fw-bold">Fast Performance</h5>
                                                    <p class="text-muted small mb-0">Experience blazing fast speeds with our optimized engine and modern infrastructure.</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 70px; height: 70px;">
                                                        <i class="bi bi-shield-check fs-2 text-success"></i>
                                                    </div>
                                                    <h5 class="fw-bold">Secure by Default</h5>
                                                    <p class="text-muted small mb-0">Your data is safe with us. We implement the latest security standards across the board.</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 70px; height: 70px;">
                                                        <i class="bi bi-cpu fs-2 text-warning"></i>
                                                    </div>
                                                    <h5 class="fw-bold">Smart Interface</h5>
                                                    <p class="text-muted small mb-0">Intuitive design makes it easy for anyone to create and manage professional content.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            `
                        });

                        // Custom Blocks: Pricing
                        bm.add('pricing-tables', {
                            label: '<div class="gjs-block-label"><i class="bi bi-tags mb-2 fs-4 d-block"></i>Pricing Table</div>',
                            category: 'Premium Sections',
                            content: `
                                <section class="py-5 p-4">
                                    <div class="container text-center">
                                        <h2 class="fw-bold mb-5">Simple, Transparent Pricing</h2>
                                        <div class="row g-4 justify-content-center">
                                            <div class="col-md-4">
                                                <div class="card border-0 shadow-sm rounded-4 p-4">
                                                    <h6 class="text-uppercase fw-bold text-muted small mb-3">Starter</h6>
                                                    <div class="display-5 fw-bold mb-4">$0 <span class="fs-6 fw-normal text-muted">/mo</span></div>
                                                    <ul class="list-unstyled mb-5 text-start">
                                                        <li class="mb-3"><i class="bi bi-check2 text-success me-2"></i> 1 Project</li>
                                                        <li class="mb-3"><i class="bi bi-check2 text-success me-2"></i> Community Support</li>
                                                        <li class="mb-3 text-muted opacity-50"><i class="bi bi-x text-danger me-2"></i> Professional Tools</li>
                                                    </ul>
                                                    <button class="btn btn-outline-primary rounded-pill w-100 fw-bold">Choose Starter</button>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-0 shadow-lg rounded-4 p-4 bg-primary text-white scale-110">
                                                    <span class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle rounded-pill fw-bold">MOST POPULAR</span>
                                                    <h6 class="text-uppercase fw-bold text-white-50 small mb-3">Pro Plan</h6>
                                                    <div class="display-5 fw-bold mb-4">$29 <span class="fs-6 fw-normal text-white-50">/mo</span></div>
                                                    <ul class="list-unstyled mb-5 text-start">
                                                        <li class="mb-3"><i class="bi bi-check2 text-white me-2"></i> Unlimited Projects</li>
                                                        <li class="mb-3"><i class="bi bi-check2 text-white me-2"></i> Priority Email Support</li>
                                                        <li class="mb-3"><i class="bi bi-check2 text-white me-2"></i> Advanced Analytics</li>
                                                    </ul>
                                                    <button class="btn btn-light rounded-pill w-100 fw-bold py-2">Go Professional</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            `
                        });

                         // Custom Blocks: Testimonial
                        bm.add('testimonial-card', {
                            label: '<div class="gjs-block-label"><i class="bi bi-chat-quote mb-2 fs-4 d-block"></i>Testimonial</div>',
                            category: 'Premium Sections',
                            content: `
                                <div class="p-4 bg-light rounded-4 shadow-sm border border-white">
                                    <div class="d-flex mb-3 text-warning">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                    <p class="fst-italic lead mb-4">"This builder has completely transformed how we create landing pages. It's so intuitive and the results look premium every time."</p>
                                    <div class="d-flex align-items-center">
                                        <img src="https://i.pravatar.cc/100?u=jane" class="rounded-circle me-3 border border-white shadow-sm" width="50">
                                        <div>
                                            <h6 class="fw-bold mb-0">Jane Cooper</h6>
                                            <small class="text-muted">Marketing Director @ Creative Agency</small>
                                        </div>
                                    </div>
                                </div>
                            `
                        });

                        // Custom Blocks: Team Section
                        bm.add('team-section', {
                            label: '<div class="gjs-block-label"><i class="bi bi-people mb-2 fs-4 d-block"></i>Team Section</div>',
                            category: 'Premium Sections',
                            content: `
                                <section class="py-5">
                                    <div class="container text-center py-5">
                                        <h2 class="fw-bold mb-5">Meet Our Creative Minds</h2>
                                        <div class="row g-4">
                                            <div class="col-md-3">
                                                <div class="team-card p-4 rounded-4 shadow-sm border h-100">
                                                    <img src="https://i.pravatar.cc/150?u=1" class="rounded-circle mb-4 border border-5 border-light shadow-sm" width="120">
                                                    <h6 class="fw-bold mb-1">Alex Morgan</h6>
                                                    <small class="text-primary d-block mb-3">CEO & Founder</small>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="#" class="text-muted"><i class="bi bi-twitter"></i></a>
                                                        <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="team-card p-4 rounded-4 shadow-sm border h-100">
                                                    <img src="https://i.pravatar.cc/150?u=2" class="rounded-circle mb-4 border border-5 border-light shadow-sm" width="120">
                                                    <h6 class="fw-bold mb-1">Sarah Johnson</h6>
                                                    <small class="text-primary d-block mb-3">Lead Designer</small>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="#" class="text-muted"><i class="bi bi-twitter"></i></a>
                                                        <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="team-card p-4 rounded-4 shadow-sm border h-100">
                                                    <img src="https://i.pravatar.cc/150?u=3" class="rounded-circle mb-4 border border-5 border-light shadow-sm" width="120">
                                                    <h6 class="fw-bold mb-1">Michael Chen</h6>
                                                    <small class="text-primary d-block mb-3">Senior Developer</small>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="#" class="text-muted"><i class="bi bi-twitter"></i></a>
                                                        <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="team-card p-4 rounded-4 shadow-sm border h-100">
                                                    <img src="https://i.pravatar.cc/150?u=4" class="rounded-circle mb-4 border border-5 border-light shadow-sm" width="120">
                                                    <h6 class="fw-bold mb-1">Emily Davis</h6>
                                                    <small class="text-primary d-block mb-3">Marketing Manager</small>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="#" class="text-muted"><i class="bi bi-twitter"></i></a>
                                                        <a href="#" class="text-muted"><i class="bi bi-linkedin"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            `
                        });

                        // Custom Blocks: Call To Action
                        bm.add('cta-premium', {
                            label: '<div class="gjs-block-label"><i class="bi bi-megaphone mb-2 fs-4 d-block"></i>Call to Action</div>',
                            category: 'Premium Sections',
                            content: `
                                <section class="py-5 bg-primary text-white my-5 rounded-4 shadow-lg overflow-hidden position-relative">
                                    <div class="position-absolute top-0 end-0 opacity-10" style="width: 300px; height: 300px; background: white; border-radius: 50%; transform: translate(50%, -50%);"></div>
                                    <div class="container py-4 text-center">
                                        <h2 class="display-5 fw-bold mb-4 px-lg-5">Ready to take your project to the next level?</h2>
                                        <p class="lead mb-5 px-lg-5 opacity-75">Join over 10,000+ creators who are already using our platform to build stunning modern websites.</p>
                                        <div class="d-flex justify-content-center gap-3">
                                            <a href="#" class="btn btn-light btn-lg px-5 rounded-pill fw-bold shadow">Start Now Free</a>
                                            <a href="#" class="btn btn-outline-light btn-lg px-5 rounded-pill">View Demo</a>
                                        </div>
                                    </div>
                                </section>
                            `
                        });

                        bm.add('faq-accordion', {
                            label: '<div class="gjs-block-label"><i class="bi bi-question-circle mb-2 fs-4 d-block"></i>FAQ Section</div>',
                            category: 'Premium Sections',
                            content: `
                                <section class="py-5">
                                    <div class="container">
                                        <div class="text-center mb-5">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-bold">HELP CENTER</span>
                                            <h2 class="fw-bold">Frequently Asked Questions</h2>
                                            <p class="text-muted">Find answers to common questions about our platform and services.</p>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8">
                                                <div class="accordion accordion-flush bg-white rounded-4 shadow-sm border p-3" id="faqAccordion">
                                                    <div class="accordion-item mb-3 border-0 bg-light rounded-4 overflow-hidden">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed bg-transparent fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                                                How do I get started with this service?
                                                            </button>
                                                        </h2>
                                                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                            <div class="accordion-body text-muted pt-0">Getting started is completely effortless. Simply sign up for a free account, choose a template or build from scratch, and follow our interactive tutorial. You will have your first project live in minutes.</div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item mb-3 border-0 bg-light rounded-4 overflow-hidden">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed bg-transparent fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                                                What kind of support is included in standard plans?
                                                            </button>
                                                        </h2>
                                                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                            <div class="accordion-body text-muted pt-0">Our standard plans include comprehensive email support with a guaranteed 24-hour response time. You also gain access to our extensive community forums and detailed documentation library.</div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item border-0 bg-light rounded-4 overflow-hidden">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed bg-transparent fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                                                Can I upgrade or cancel my subscription at any time?
                                                            </button>
                                                        </h2>
                                                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                                            <div class="accordion-body text-muted pt-0">Yes! We believe in complete flexibility. You can upgrade to access more features or cancel your subscription at any moment without any hidden cancellation fees.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            `
                        });

                        bm.add('stats-counter', {
                            label: '<div class="gjs-block-label"><i class="bi bi-graph-up-arrow mb-2 fs-4 d-block"></i>Statistics</div>',
                            category: 'Premium Sections',
                            content: `
                                <section class="py-5 bg-dark text-white rounded-4 mt-5 mb-5 position-relative overflow-hidden shadow-lg border-bottom border-primary border-5">
                                    <div class="container position-relative z-index-1">
                                        <div class="row g-4 text-center">
                                            <div class="col-6 col-md-3">
                                                <i class="bi bi-cloud-arrow-down fs-1 text-primary mb-3"></i>
                                                <h2 class="display-4 fw-bold mb-1">2M+</h2>
                                                <p class="text-white-50 text-uppercase small fw-bold tracking-wide m-0">Downloads</p>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <i class="bi bi-emoji-smile fs-1 text-warning mb-3"></i>
                                                <h2 class="display-4 fw-bold mb-1">98%</h2>
                                                <p class="text-white-50 text-uppercase small fw-bold tracking-wide m-0">Happy Clients</p>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <i class="bi bi-globe fs-1 text-info mb-3"></i>
                                                <h2 class="display-4 fw-bold mb-1">150+</h2>
                                                <p class="text-white-50 text-uppercase small fw-bold tracking-wide m-0">Countries</p>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <i class="bi bi-trophy fs-1 text-success mb-3"></i>
                                                <h2 class="display-4 fw-bold mb-1">45</h2>
                                                <p class="text-white-50 text-uppercase small fw-bold tracking-wide m-0">Awards Won</p>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            `
                        });


                        // Ambil konten lama dari textarea jika ada
                        let currentContent = $('#primary_editor').summernote('code');
                        if (!currentContent) currentContent = $('.editor').first().val();
                        
                        editor.setComponents(currentContent);
                    }
                });

                $('#save-builder-page').on('click', function() {
                    if (editor) {
                        const html = editor.getHtml();
                        const css = editor.getCss();
                        const fullContent = `<style>${css}</style>${html}`;
                        
                        // Masukkan ke editor utama (Summernote atau textarea)
                        if ($('#primary_editor').length) {
                             $('#primary_editor').summernote('code', fullContent);
                        } else if ($('.editor').length) {
                             $('.editor').first().summernote('code', fullContent);
                        }
                        
                        toastr.success("Layout exported successfully to editor.");
                        $('#builderModal').modal('hide');
                    }
                });
            });
            </script>
            <style>
                .gjs-one-bg { background-color: #1e1e1e !important; }
                .gjs-two-bg { background-color: #2d2d2d !important; }
                .gjs-three-bg { background-color: #3d3d3d !important; }
                .gjs-four-color, .gjs-four-color-h:hover { color: #3b82f6 !important; }
                .gjs-category-title, .gjs-layer-title, .gjs-block-category .gjs-title { background-color: #252525 !important; border-bottom: 1px solid #1e1e1e !important; font-weight: 700 !important; color: #fff !important; }
                .gjs-block-label { text-align: center; padding: 10px 5px; font-weight: 500; font-size: 0.75rem; }
                .gjs-block { background-color: #333 !important; border-radius: 8px !important; margin: 5px !important; transition: all 0.2s; border: 1px solid transparent !important;}
                .gjs-block:hover { border-color: #3b82f6 !important; transform: translateY(-2px); }
                .scale-110 { transform: scale(1.05); }
            </style>

<?php
        }
    }
}

DynamicBuilder::init();

