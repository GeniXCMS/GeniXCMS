<?php defined('GX_LIB') or die('Direct Access Not Allowed!'); ?>

<h3 class="fw-bold fs-4 mb-4 text-center">Step 3: Verification</h3>
<p class="text-muted text-center mb-5">Review your configuration before we finalize the deployment of your hub.</p>

<form action="?step=4" method="post">
    <div class="card border-0 bg-light rounded-4 mb-5">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-6 col-md-4 text-muted small fw-bold text-uppercase">Site Name</div>
                <div class="col-6 col-md-8"><?= Session::val('sitename'); ?></div>
                
                <div class="col-6 col-md-4 text-muted small fw-bold text-uppercase">Site Slogan</div>
                <div class="col-6 col-md-8"><?= Session::val('siteslogan'); ?></div>
                
                <div class="col-6 col-md-4 text-muted small fw-bold text-uppercase">Site Domain</div>
                <div class="col-6 col-md-8"><?= Session::val('sitedomain'); ?></div>
                
                <div class="col-6 col-md-4 text-muted small fw-bold text-uppercase">Site URL</div>
                <div class="col-6 col-md-8"><?= Session::val('siteurl'); ?></div>
                
                <div class="hr col-12 my-2 border-top border-2 border-white opacity-50"></div>
                
                <div class="col-6 col-md-4 text-muted small fw-bold text-uppercase">Admin Name</div>
                <div class="col-6 col-md-8"><?= Session::val('adminname'); ?></div>
                
                <div class="col-6 col-md-4 text-muted small fw-bold text-uppercase">Admin Username</div>
                <div class="col-6 col-md-8"><?= Session::val('adminuser'); ?></div>
                
                <div class="col-6 col-md-4 text-muted small fw-bold text-uppercase">Admin Password</div>
                <div class="col-6 col-md-8"><span class="badge bg-secondary opacity-50 px-2 py-1">ENCRYPTED</span></div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <a href="?step=2" class="text-decoration-none text-muted small"><i class="fa fa-chevron-left me-1"></i> Back to Credentials</a>
        <button type="submit" name="step3" class="btn btn-primary d-inline-flex align-items-center">
            Finalize Installation <i class="fa fa-rocket ms-2 scale-hover transition-base"></i>
        </button>
    </div>
</form>
