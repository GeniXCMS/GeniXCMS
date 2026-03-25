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
            <h3 class="fw-bold text-dark mb-0"><?=_("Infographical Overlays");?></h3>
            <p class="text-muted small mb-0"><?=_("Transform standard articles into feature-rich location and review cards.");?></p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">
                <i class="bi bi-check2-circle me-1"></i> <?=_("V2 Engine Active");?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Dashboard & Guide -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-md-5">
                    <h6 class="fw-bold text-dark text-uppercase mb-4"><?=_("How to utilize");?></h6>
                    <p class="small text-muted mb-4">
                        <?=_("This module injects a specialized metadata panel at the bottom of the Post Editor. When active, it displays a structured information card before the main article content.");?>
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border-start border-4 border-info h-100 shadow-none">
                                <h6 class="fw-bold fs-7 text-dark mb-2"><i class="bi bi-geo-alt me-2 text-info"></i><?=_("Geo Integration");?></h6>
                                <p class="extra-small text-muted mb-0"><?=_("Embeds interactive Google Maps based on latitude and longitude coordinates.");?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border-start border-4 border-warning h-100 shadow-none">
                                <h6 class="fw-bold fs-7 text-dark mb-2"><i class="bi bi-star me-2 text-warning"></i><?=_("Review System");?></h6>
                                <p class="extra-small text-muted mb-0"><?=_("Aggregates ratings for comfort, accessibility, and quality into an overall score.");?></p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-primary border-0 rounded-4 p-4 shadow-none">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                             <i class="bi bi-search"></i> <?=_("SEO Optimization");?>
                        </h6>
                        <p class="extra-small mb-0 opacity-75">
                            <?=_("Infograph automatically generates <strong>Schema.org</strong> structured data (JSON-LD) for your locations and reviews, making your content more discoverable by search engine rich results.");?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Mockup -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 h-100">
                <div class="card-header bg-dark py-3 px-4">
                    <h6 class="fw-bold text-white extra-small text-uppercase mb-0 opacity-75"><?=_("Frontend Preview Mockup");?></h6>
                </div>
                <div class="card-body p-4 bg-light">
                    <!-- Fake Infograph UI -->
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-4 text-center bg-success bg-opacity-10 rounded-2 py-3 border border-success border-opacity-10">
                                    <div class="extra-small fw-bold text-success text-uppercase opacity-50"><?=_("Rating");?></div>
                                    <div class="h4 fw-bold text-success mb-0">4.5</div>
                                </div>
                                <div class="col-8 d-flex flex-column justify-content-center ps-3">
                                    <div class="fw-bold text-dark fs-7 mb-1"><?=_("Sample Destination Name");?></div>
                                    <div class="d-flex text-warning fs-8">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3 opacity-10">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="extra-small fw-bold text-muted text-uppercase mb-1"><?=_("Utilities");?></div>
                                    <div class="d-flex gap-2 text-success opacity-75">
                                        <i class="bi bi-wifi"></i><i class="bi bi-p-circle"></i><i class="bi bi-lightning-charge"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-gray-200 rounded-3 w-100 h-100 d-flex align-items-center justify-content-center bg-light border py-3">
                                        <i class="bi bi-map text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted italic opacity-50 fs-8"><?=_("Visualization of actual frontend component");?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
