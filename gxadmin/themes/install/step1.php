<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

// Auto-detect current URL
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = "https";
}
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$uri = strtok($uri, '?'); // Remove query string
$siteUrl = preg_replace('/index\.php.*$/i', '', $protocol . "://" . $host . $uri);
$siteUrl = rtrim($siteUrl, '/') . '/';
?>

<h3 class="fw-bold fs-4 mb-4 text-center">Step 1: Identity Hub</h3>
<p class="text-muted text-center mb-5">Define the brand and digital presence of your GeniXCMS instance.</p>

<form action="?step=2" method="post">
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <label class="form-label">Site Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-signature text-muted"></i></span>
                <input type="text" name="sitename" class="form-control border-start-0 ps-0" placeholder="e.g. My Awesome Site" required>
            </div>
            <div class="form-text small opacity-75">The name of your platform.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Site Slogan</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-quote-left text-muted"></i></span>
                <input type="text" name="siteslogan" class="form-control border-start-0 ps-0" placeholder="e.g. Just another GeniXCMS blog">
            </div>
            <div class="form-text small opacity-75">A secondary tagline for your brand.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Site Domain</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-link text-muted"></i></span>
                <input type="text" name="sitedomain" class="form-control border-start-0 ps-0" value="<?= $host; ?>" required>
            </div>
            <div class="form-text small opacity-75">Your site's host domain.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Site URL</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-globe text-muted"></i></span>
                <input type="text" name="siteurl" class="form-control border-start-0 ps-0" value="<?= $siteUrl; ?>" required>
            </div>
            <div class="form-text small opacity-75">Must end with a trailing slash <kbd class="py-0 px-1 bg-light text-muted border">/</kbd>.</div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <a href="?" class="text-decoration-none text-muted small"><i class="fa fa-chevron-left me-1"></i> Back to Database</a>
        <button type="submit" name="step2" class="btn btn-primary d-inline-flex align-items-center">
            Continue Setup <i class="fa fa-arrow-right ms-2 scale-hover transition-base"></i>
        </button>
    </div>
</form>
