<?php
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 */
?>

<div class="col-md-12">
    <?=Hooks::run('admin_page_notif_action', $data);?>
</div>

<div class="container-fluid py-5 min-vh-100 d-flex flex-column">
    <!-- Header Section -->
    <div class="row align-items-center mb-5 flex-shrink-0">
        <div class="col-md-7">
            <h3 class="fw-bold text-dark mb-1"><?=_("Digital Asset Library");?></h3>
            <p class="text-muted mb-0"><?=_("Manage, organize, and optimize your site media and file uploads.");?></p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <div class="bg-white px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center border">
                <i class="bi bi-hdd-network text-primary me-2"></i>
                <span class="text-muted extra-small fw-bold text-uppercase"><?=_("Storage Connectivity: Active");?></span>
            </div>
        </div>
    </div>

    <!-- Media Explorer Container -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden flex-grow-1 d-flex flex-column mb-5" style="min-height: 550px;">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-muted extra-small text-uppercase tracking-wider">
                <i class="bi bi-folder2-open me-2 text-warning"></i><?=_("File System Explorer");?>
            </h6>
            <div class="extra-small text-muted opacity-50">
                <i class="bi bi-info-circle me-1"></i><?=_("Drag & drop enabled");?>
            </div>
        </div>
        <div class="card-body p-0 flex-grow-1 position-relative">
            <div id="elfinder" class="border-0" style="height: 450px;"></div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .tracking-wider { letter-spacing: 0.1em; }
    /* Overriding elfinder styles to blend better if possible */
    #elfinder { border: none !important; }
    .elfinder-workzone { background: #fff !important; }
</style>

