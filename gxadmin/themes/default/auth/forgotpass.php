<?php if (!User::isLoggedin()): ?>

<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body.login-page {
        background: #0f172a !important;
        overflow: hidden;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    .gx-fp-wrapper {
        display: flex;
        min-height: 100vh;
        align-items: center;
        justify-content: center;
        padding: 24px;
        position: relative;
        overflow: hidden;
    }

    /* ---- Background layers ---- */
    .gx-fp-bg {
        position: fixed;
        inset: 0;
        background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
        z-index: 0;
    }
    .gx-fp-blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.25;
        animation: blob-drift ease-in-out infinite alternate;
        z-index: 0;
    }
    @keyframes blob-drift {
        0%   { transform: translate(0, 0) scale(1); }
        100% { transform: translate(30px, -20px) scale(1.08); }
    }

    /* Grid lines overlay */
    .gx-fp-grid {
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(139,92,246,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(139,92,246,0.04) 1px, transparent 1px);
        background-size: 40px 40px;
        z-index: 0;
    }

    /* ---- Card ---- */
    .gx-fp-card {
        position: relative;
        z-index: 2;
        background: rgba(255,255,255,0.97);
        border-radius: 24px;
        padding: 52px 48px;
        width: 100%;
        max-width: 440px;
        box-shadow:
            0 0 0 1px rgba(139,92,246,0.1),
            0 25px 60px rgba(0,0,0,0.4),
            0 0 100px rgba(139,92,246,0.1);
        animation: card-appear 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    @keyframes card-appear {
        from { opacity: 0; transform: translateY(32px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0)    scale(1); }
    }

    /* ---- Icon badge ---- */
    .gx-fp-icon-wrap {
        display: flex;
        justify-content: center;
        margin-bottom: 28px;
    }
    .gx-fp-icon {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #fff;
        box-shadow: 0 8px 24px rgba(139,92,246,0.35);
        position: relative;
    }
    .gx-fp-icon::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 24px;
        border: 2px dashed rgba(139,92,246,0.3);
        animation: spin-slow 12s linear infinite;
    }
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    /* ---- Header ---- */
    .gx-fp-header { text-align: center; margin-bottom: 32px; }
    .gx-fp-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin-bottom: 10px;
    }
    .gx-fp-desc {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.65;
    }

    /* ---- Steps indicator ---- */
    .gx-fp-steps {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 30px;
        background: #f8fafc;
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
    }
    .gx-fp-step {
        flex: 1;
        text-align: center;
        position: relative;
    }
    .gx-fp-step:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 100%;
        height: 2px;
        background: #e2e8f0;
        z-index: 0;
    }
    .gx-fp-step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        background: #fff;
        color: #94a3b8;
        font-size: 0.7rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 4px;
        position: relative;
        z-index: 1;
    }
    .gx-fp-step.active .gx-fp-step-num {
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        border-color: #8b5cf6;
        color: #fff;
        box-shadow: 0 2px 8px rgba(139,92,246,0.3);
    }
    .gx-fp-step-label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .gx-fp-step.active .gx-fp-step-label { color: #8b5cf6; }

    /* ---- Form ---- */
    .gx-form-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
    }
    .gx-input-wrapper { position: relative; margin-bottom: 20px; }
    .gx-input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #cbd5e1;
        font-size: 16px;
        z-index: 2;
        transition: color 0.2s;
    }
    .gx-input-wrapper:focus-within .gx-input-icon { color: #8b5cf6; }
    .gx-fp-input {
        width: 100%;
        padding: 13px 16px 13px 44px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.9rem;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: all 0.25s ease;
        font-family: inherit;
    }
    .gx-fp-input:focus {
        border-color: #8b5cf6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(139,92,246,0.1);
    }
    .gx-fp-input::placeholder { color: #cbd5e1; }

    .gx-btn-reset {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(139,92,246,0.35);
        margin-bottom: 20px;
    }
    .gx-btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139,92,246,0.45);
    }
    .gx-btn-reset:active { transform: translateY(0); }

    /* Alerts */
    .gx-alert {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 18px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .gx-alert i { flex-shrink: 0; margin-top: 1px; }
    .gx-alert-danger  { background: #fef2f2; color: #dc2626; border-left: 3px solid #dc2626; }
    .gx-alert-success { background: #f0fdf4; color: #16a34a; border-left: 3px solid #16a34a; }
    .gx-alert-info    { background: #eff6ff; color: #2563eb; border-left: 3px solid #2563eb; }

    /* Info note */
    .gx-fp-note {
        background: #faf5ff;
        border: 1px solid #e9d5ff;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .gx-fp-note i { color: #8b5cf6; flex-shrink: 0; margin-top: 2px; font-size: 15px; }
    .gx-fp-note-text { font-size: 0.78rem; color: #6d28d9; line-height: 1.5; }

    /* Back / links */
    .gx-fp-footer {
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
    }
    .gx-fp-footer a {
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s;
    }
    .gx-fp-footer a:hover { color: #8b5cf6; }
    .gx-fp-footer a.primary { color: #8b5cf6; font-weight: 700; }
    .gx-fp-footer span { width: 4px; height: 4px; background: #e2e8f0; border-radius: 50%; }

    /* Branding bottom */
    .gx-fp-brand {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 0.4;
        transition: opacity 0.2s;
    }
    .gx-fp-brand:hover { opacity: 0.7; }
    .gx-fp-brand-text { font-size: 0.72rem; color: #fff; font-weight: 600; letter-spacing: 0.3px; }

    @media (max-width: 480px) {
        .gx-fp-card { padding: 36px 24px; }
        body.login-page { overflow-y: auto; }
        .gx-fp-wrapper { align-items: flex-start; padding-top: 40px; }
    }
</style>




<!-- Background layers -->
<div class="gx-fp-bg"></div>
<div class="gx-fp-grid"></div>
<div class="gx-fp-blob" style="width:500px;height:500px;background:#8b5cf6;top:-150px;right:-150px;animation-duration:9s;"></div>
<div class="gx-fp-blob" style="width:350px;height:350px;background:#6366f1;bottom:-100px;left:-80px;animation-duration:12s;animation-direction:alternate-reverse;"></div>

<!-- Bottom brand -->
<a href="<?= Site::$url ?>" class="gx-fp-brand text-decoration-none">
    <?= Site::logo(height:'18px') ?>
    <span class="gx-fp-brand-text"><?= htmlspecialchars(Site::$name) ?></span>
</a>

<div class="gx-fp-wrapper">
    <div class="gx-fp-card">

        <!-- Icon -->
        <div class="gx-fp-icon-wrap">
            <div class="gx-fp-icon">
                <i class="bi bi-key-fill"></i>
            </div>
        </div>

        <!-- Header -->
        <div class="gx-fp-header">
            <h1 class="gx-fp-title"><?= _('Reset Password') ?></h1>
            <p class="gx-fp-desc">
                <?= _("Enter your username below and we'll send you an email with a link to reset your password.") ?>
            </p>
        </div>

        <!-- Step Indicator -->
        <div class="gx-fp-steps">
            <div class="gx-fp-step active">
                <div class="gx-fp-step-num">1</div>
                <span class="gx-fp-step-label">Verify</span>
            </div>
            <div class="gx-fp-step">
                <div class="gx-fp-step-num">2</div>
                <span class="gx-fp-step-label">Email</span>
            </div>
            <div class="gx-fp-step">
                <div class="gx-fp-step-num">3</div>
                <span class="gx-fp-step-label">Reset</span>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($data['alertDanger'])): ?>
            <?php foreach($data['alertDanger'] as $msg): ?>
                <div class="gx-alert gx-alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><?= htmlspecialchars($msg) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (isset($data['alertSuccess'])): ?>
            <?php foreach($data['alertSuccess'] as $msg): ?>
                <div class="gx-alert gx-alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($msg) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Info note -->
        <div class="gx-fp-note">
            <i class="bi bi-info-circle-fill"></i>
            <div class="gx-fp-note-text">
                <?= _("A password reset link will be sent to the email address associated with your account. Please check your inbox and spam folder.") ?>
            </div>
        </div>

        <!-- Form -->
        <form action="" method="post">
            <label class="gx-form-label" for="fpUsername"><?= _('Username') ?></label>
            <div class="gx-input-wrapper">
                <i class="bi bi-person gx-input-icon"></i>
                <input id="fpUsername" type="text" name="username" class="gx-fp-input"
                       placeholder="<?= _('Enter your username') ?>" required
                       autofocus autocomplete="username">
            </div>

            <?= Xaptcha::html(); ?>

            <input type="hidden" name="token" value="<?= TOKEN ?>">

            <button type="submit" name="forgotpass" class="gx-btn-reset">
                <i class="bi bi-send-fill"></i>
                <?= _('Send Reset Link') ?>
            </button>
        </form>

        <!-- Footer links -->
        <div class="gx-fp-footer">
            <a href="<?= Site::$url ?>login/">
                <i class="bi bi-arrow-left"></i> <?= _('Back to Sign In') ?>
            </a>
            <span></span>
            <a href="<?= Site::$url ?>register/" class="primary">
                <i class="bi bi-person-plus"></i> <?= _('Create Account') ?>
            </a>
        </div>

    </div>
</div>

<?php else: ?>

<style>
    body.login-page { background: #0f172a !important; display: flex; align-items: center; justify-content: center; }
</style>
<?php if (!defined("OFFLINE_MODE") || !OFFLINE_MODE): ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<?php endif; ?>

<div style="text-align:center;padding:40px;color:#fff;font-family:Inter,sans-serif;">
    <div style="font-size:48px;margin-bottom:16px;">🔐</div>
    <h2 style="font-weight:800;margin-bottom:10px;"><?= _("Already Logged In") ?></h2>
    <p style="color:rgba(255,255,255,0.5);margin-bottom:24px;"><?= _("You're already signed in to your account.") ?></p>
    <a href="<?= Site::$url ?>logout/" style="background:#8b5cf6;color:#fff;padding:12px 28px;border-radius:50px;text-decoration:none;font-weight:700;font-size:0.875rem;">
        <i class="bi bi-box-arrow-right"></i> <?= _("Logout") ?>
    </a>
</div>

<?php endif; ?>
