<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');
?>
<div class="row">
    <div class="col-md-12">
        <?=Hooks::run('admin_page_notif_action', $data ?? []);?>
    </div>
</div>

<div class="container-fluid py-4 mb-5">
    <!-- Header Page -->
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-layers-half text-primary me-2"></i> Dynamic Builder Pro</h3>
            <p class="text-muted mb-0">Drag and drop visual editor for stunning GeniXCMS pages and posts.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">
                <i class="bi bi-check-circle-fill me-1"></i> Module Active
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <!-- How to use -->
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold mb-4 text-dark">How to utilize the builder</h5>
                    
                    <div class="d-flex align-items-start gap-4 mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <h4 class="m-0 fw-bold">1</h4>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-2">Create New Post or Page</h6>
                            <p class="text-muted small mb-0">Go to Content > Pages or Posts and click "Create New". You will see the standard editor interface.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-4 mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <h4 class="m-0 fw-bold">2</h4>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-2">Launch the Visual Editor</h6>
                            <p class="text-muted small mb-0">Scroll down past the text editor. You will see a large blue banner that says <strong>"Design with Dynamic Builder"</strong>. Click the launch button.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <h4 class="m-0 fw-bold">3</h4>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-2">Drag, Drop & Export</h6>
                            <p class="text-muted small mb-0">A fullscreen editor will appear. Drag widgets from the right panel. When finished, click <strong>"Save & Export"</strong> at the top right to instantly generate the HTML back into the CMS editor.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Widgets Included -->
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white h-100 overflow-hidden position-relative">
                <div class="position-absolute end-0 bottom-0 opacity-10" style="transform: translate(20%, 20%);">
                    <i class="bi bi-grid-3x3-gap-fill" style="font-size: 15rem;"></i>
                </div>
                <div class="card-body p-4 p-md-5 position-relative z-1">
                    <h5 class="fw-bold mb-4">Premium Library <span class="badge bg-primary fs-8 align-middle ms-2">Included</span></h5>
                    
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3 fs-7 opacity-75">
                        <li><i class="bi bi-window-fullscreen text-primary me-2"></i> Hero Premium Sections</li>
                        <li><i class="bi bi-grid-3x3-gap text-success me-2"></i> Feature & Service Grids</li>
                        <li><i class="bi bi-tags text-info me-2"></i> Interactive Pricing Tables</li>
                        <li><i class="bi bi-chat-quote text-warning me-2"></i> Testimonial Cards</li>
                        <li><i class="bi bi-people text-danger me-2"></i> Team Showcases</li>
                        <li><i class="bi bi-question-circle text-light me-2"></i> FAQ Accordions <span class="badge bg-light text-dark fs-8">NEW</span></li>
                        <li><i class="bi bi-graph-up-arrow text-primary me-2"></i> Statistics Counters <span class="badge bg-light text-dark fs-8">NEW</span></li>
                        <li><i class="bi bi-youtube text-danger me-2"></i> Video Embeds <span class="badge bg-light text-dark fs-8">NEW</span></li>
                        <li><i class="bi bi-envelope-paper text-success me-2"></i> Newsletter Forms <span class="badge bg-light text-dark fs-8">NEW</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
