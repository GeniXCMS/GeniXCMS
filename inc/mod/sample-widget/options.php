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
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-cpu text-primary me-2"></i> Sample Widget Module</h3>
            <p class="text-muted mb-0">A demonstration module showcasing dynamic widget integration in GeniXCMS.</p>
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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold mb-4 text-dark">How This Module Works</h5>
                    
                    <p class="text-muted mb-4">
                        This module serves as an educational sample for developers. It binds a custom HTML block to the GeniXCMS widget system without requiring complex configurations.
                    </p>

                    <div class="d-flex align-items-start gap-4 mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-plug fw-bold fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-2">1. Hook Registration</h6>
                            <p class="text-muted small mb-0">The module attaches itself to the hook <code>sample_widget_render</code> using the core system's Event/Hook dispatcher.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-4 mb-4">
                        <div class="bg-success bg-opacity-10 rounded-circle text-success d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-rocket-takeoff fw-bold fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-2">2. Visual Rendering</h6>
                            <p class="text-muted small mb-0">You can trigger this widget anywhere in your theme by running the hook or integrating it via the native Widgets visual manager under the Management menu.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle text-warning d-flex flex-shrink-0 align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-code-slash fw-bold fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-2">3. Developer Ready</h6>
                            <p class="text-muted small mb-0">No configuration required! Open <code>inc/mod/sample-widget/index.php</code> to easily inspect and modify its structure as a foundation for your own complex modules.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Code preview -->
            <div class="card border-0 shadow-sm bg-dark text-white h-100 overflow-hidden position-relative pt-2">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-terminal text-info fs-5"></i> Implementation Template
                    </h6>
                    <p class="extra-small text-white-50 mb-3">Copy this snippet to render the widget programmatically inside your theme manually:</p>
                    
                    <div class="bg-black bg-opacity-50 p-3 rounded-3" style="font-family: monospace; font-size: 0.75rem;">
                        <span class="text-muted">&lt;?php</span><br>
                        <span class="text-info">echo</span> Hooks::run(<span class="text-success">'sample_widget_render'</span>);<br>
                        <span class="text-muted">?&gt;</span>
                    </div>

                    <hr class="border-secondary opacity-25 my-4">

                    <a href="index.php?page=widgets" class="btn btn-outline-light rounded-pill w-100 fw-bold border-2">
                        Manage Widgets <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
