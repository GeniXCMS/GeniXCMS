<?php defined('GX_LIB') or die('Direct Access Not Allowed!'); ?>

<div class="text-center py-5">
    <div class="mb-4 d-inline-block bg-danger bg-opacity-10 p-4 rounded-circle">
        <i class="fa-solid fa-triangle-exclamation fa-4x text-danger"></i>
    </div>
    
    <h3 class="fw-bold fs-2 mb-3">Deployment Halt</h3>
    <p class="text-muted mb-5 px-md-5">An unexpected exception occurred during the hub fertilization process. Please review the diagnostic details below.</p>

    <div class="alert alert-danger rounded-4 border-0 p-4 mb-5 text-start">
        <h6 class="fw-bold small text-uppercase mb-2">Diagnostic Log:</h6>
        <div class="font-monospace small opacity-75">
            <?php 
                if (isset($data['alertDanger'])) {
                    foreach ($data['alertDanger'] as $err) {
                        echo "• " . htmlspecialchars($err) . "<br>";
                    }
                } else {
                    echo "• Unknown critical error detected.";
                }
            ?>
        </div>
    </div>

    <div class="d-grid gap-3 col-md-8 mx-auto">
        <a href="?" class="btn btn-primary btn-lg rounded-4 shadow-sm py-3 fw-bold">
            <i class="fa fa-rotate-right me-2"></i> Initialise Diagnostics Again
        </a>
    </div>
</div>
