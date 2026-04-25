<?php defined('GX_LIB') or die('Direct Access Not Allowed!'); ?>

<div class="text-center py-5">
    <div class="mb-4 d-inline-block bg-success bg-opacity-10 p-4 rounded-circle">
        <i class="fa-solid fa-cloud-check fa-4x text-success"></i>
    </div>
    
    <h3 class="fw-bold fs-2 mb-3">Deployment Finalized!</h3>
    <p class="text-muted mb-5 px-md-5">Congratulations. Your GeniXCMS instance has been successfully deployed and is now ready for production.</p>

    <div class="d-grid gap-3 col-md-8 mx-auto mt-5">
        <a href="gxadmin" class="btn btn-primary btn-lg rounded-4 shadow-sm py-3 fw-bold">
            <i class="fa fa-gauge-high me-2"></i> Launch Admin Dashboard
        </a>
        <a href="index.php" class="btn btn-outline-secondary btn-lg rounded-4 py-3">
            <i class="fa fa-globe me-2"></i> View Your New Website
        </a>
    </div>

    <div class="mt-5 pt-4">
        <div class="bg-warning bg-opacity-10 p-3 rounded-4 border border-warning border-opacity-20 d-flex align-items-center justify-content-center text-warning small">
            <i class="fa fa-shield-halved me-2"></i> Warning: For security, we recommend removing the installation file or directory.
        </div>
    </div>
</div>
