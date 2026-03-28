<?php defined('GX_LIB') or die('Direct Access Not Allowed!'); ?>

<h3 class="fw-bold fs-4 mb-4 text-center">Step 2: Security & Access</h3>
<p class="text-muted text-center mb-5">Create the master credentials for your administrative dashboard.</p>

<form action="?step=3" method="post">
    <div class="row g-4 mb-5">
        <div class="col-md-12">
            <label class="form-label">Administrator Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                <input type="text" name="adminname" class="form-control border-start-0 ps-0" placeholder="e.g. John Doe" required>
            </div>
            <div class="form-text small opacity-75">Your real name or display name.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Admin Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-at text-muted"></i></span>
                <input type="text" name="adminuser" class="form-control border-start-0 ps-0" placeholder="e.g. admin" required>
            </div>
            <div class="form-text small opacity-75">Used for authentication and identity.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Admin Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                <input type="password" name="adminpass" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
            </div>
            <div class="form-text small opacity-75">Choose a strong password to protect your hub.</div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <a href="?step=1" class="text-decoration-none text-muted small"><i class="fa fa-chevron-left me-1"></i> Back to Site Info</a>
        <button type="submit" name="step3" class="btn btn-primary d-inline-flex align-items-center">
            Finalize Hub Profile <i class="fa fa-arrow-right ms-2 scale-hover transition-base"></i>
        </button>
    </div>
</form>
