<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>
<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data ?? []);?>
</div>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-start">
            <h3 class="fw-bold text-dark mb-0"><?=_("Code Highlighting");?></h3>
            <p class="text-muted small mb-0"><?=_("Automated syntax highlighting for technical documentation and snippets.");?></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary border-opacity-25">
                <i class="bi bi-cpu-fill me-1"></i> Engine: highlight.js v9.6.0
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Status & Info -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-md-5">
                    <h6 class="fw-bold text-primary text-uppercase mb-4"><?=_("Operational Profile");?></h6>
                    <div class="bg-light rounded-4 p-4 mb-4 border-start border-4 border-primary">
                        <div class="d-flex gap-3">
                            <div class="fs-1 text-primary opacity-50"><i class="bi bi-terminal-fill"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><?=_("Zero-Config Integration");?></h6>
                                <p class="text-muted small mb-0">
                                    <?=_("This module automatically detects and transforms standard HTML <code>&lt;pre&gt;</code> tags into rich, readable code blocks with Monokai aesthetics.");?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold fs-7 text-dark mb-3"><?=_("Quick Usage Guide");?></h6>
                    <p class="small text-muted mb-3"><?=_("Simply wrap your code in standard tags in the editor:");?></p>
                    <div class="bg-dark rounded-3 p-3 position-relative overflow-hidden mb-4 shadow-none">
                        <div class="extra-small text-white opacity-25 position-absolute top-0 end-0 m-2">HTML EXAMPLE</div>
                        <pre class="m-0"><code class="text-info">&lt;pre&gt;</code>
    <code class="text-light">function helloWorld() {
        console.log("Hello GeniXCMS!");
    }</code>
<code class="text-info">&lt;/pre&gt;</code></pre>
                    </div>

                    <div class="alert alert-info border-0 rounded-4 py-3 px-4 mb-0 d-flex align-items-center gap-3 shadow-none">
                        <i class="bi bi-stars fs-4 text-primary"></i>
                        <div class="extra-small lh-sm text-dark opacity-75">
                            <?=_("The module automatically matches the syntax against over 180 supported languages and provides consistent styling across all browsers.");?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Meta -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4 text-center bg-transparent border-0 mt-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-code-slash fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1"><?=_("Code Highlight");?></h5>
                    <div class="extra-small text-muted mb-3"><?=_("By Puguh Wijayanto");?></div>
                    <hr class="opacity-10">
                    <div class="d-grid gap-2">
                        <a href="https://highlightjs.org" target="_blank" class="btn btn-light rounded-pill btn-sm fw-bold border py-2">
                            <i class="bi bi-link-45deg me-1"></i> <?=_("Engine Library");?>
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between extra-small fw-bold text-muted text-uppercase tracking-wider">
                        <span><?=_("Next Update");?></span>
                        <span class="text-primary"><?=_("Custom Themes");?></span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 bg-dark rounded-4 shadow-sm p-4">
                <h6 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle text-info"></i> <?=_("Dev Tip");?>
                </h6>
                <p class="extra-small text-white-50 mb-0">
                    <?=_("For best results, specify the language class on your container, e.g.");?> <code>&lt;pre class='php'&gt;</code>.
                </p>
            </div>
        </div>
    </div>
</div>
