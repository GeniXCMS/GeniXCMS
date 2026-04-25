<?php
$site_name = Site::$name ?? 'GeniXCMS';
$site_logo = Site::logo(height:'40px', class:'img-fluid');
$site_url  = Site::$url;
?>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body.login-page {
        background: #0f172a !important;
        overflow: hidden;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    .gx-login-wrapper {
        display: flex;
        min-height: 100vh;
        width: 100%;
        overflow: hidden;
    }

    /* ---- LEFT PANEL ---- */
    .gx-login-left {
        flex: 1;
        background: linear-gradient(145deg, #1e3a5f 0%, #0f172a 60%, #1a1040 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 52px;
        position: relative;
        overflow: hidden;
    }
    .gx-login-left::before {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
        top: -100px;
        left: -100px;
        border-radius: 50%;
        animation: pulse-glow 6s ease-in-out infinite alternate;
    }
    .gx-login-left::after {
        content: '';
        position: absolute;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 70%);
        bottom: -80px;
        right: -50px;
        border-radius: 50%;
        animation: pulse-glow 8s ease-in-out infinite alternate-reverse;
    }
    @keyframes pulse-glow {
        0%   { transform: scale(1); opacity: 0.6; }
        100% { transform: scale(1.15); opacity: 1; }
    }

    .gx-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 2;
    }
    .gx-brand-text {
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: -0.3px;
    }
    .gx-brand-badge {
        background: rgba(59,130,246,0.2);
        border: 1px solid rgba(59,130,246,0.4);
        color: #60a5fa;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 50px;
    }

    .gx-hero-text {
        position: relative;
        z-index: 2;
    }
    .gx-hero-title {
        font-size: clamp(2rem, 3.5vw, 3rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
        margin-bottom: 20px;
        letter-spacing: -1px;
    }
    .gx-hero-title span {
        background: linear-gradient(90deg, #60a5fa, #a78bfa);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .gx-hero-desc {
        color: rgba(255,255,255,0.5);
        font-size: 0.95rem;
        line-height: 1.7;
        max-width: 380px;
    }

    .gx-stats {
        display: flex;
        gap: 30px;
        position: relative;
        z-index: 2;
    }
    .gx-stat-item { text-align: left; }
    .gx-stat-num {
        font-size: 1.6rem;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }
    .gx-stat-label {
        color: rgba(255,255,255,0.4);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
    }

    /* ---- RIGHT PANEL ---- */
    .gx-login-right {
        width: 460px;
        flex-shrink: 0;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 52px 48px;
        position: relative;
        box-shadow: -20px 0 60px rgba(0,0,0,0.3);
    }

    .gx-login-header {
        margin-bottom: 36px;
    }
    .gx-welcome-eyebrow {
        font-size: 11px;
        font-weight: 800;
        color: #3b82f6;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 10px;
    }
    .gx-login-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin-bottom: 8px;
    }
    .gx-login-subtitle {
        color: #94a3b8;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    /* Form */
    .gx-form-group {
        margin-bottom: 20px;
    }
    .gx-form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
    }
    .gx-input-wrapper {
        position: relative;
    }
    .gx-input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #cbd5e1;
        font-size: 16px;
        transition: color 0.2s;
        z-index: 2;
    }
    .gx-input {
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
    .gx-input:focus {
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
    }
    .gx-input:focus + .gx-input-icon, /* wrong selector */
    .gx-input-wrapper:focus-within .gx-input-icon {
        color: #3b82f6;
    }
    .gx-input::placeholder { color: #cbd5e1; }

    .gx-toggle-pass {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #cbd5e1;
        cursor: pointer;
        font-size: 15px;
        padding: 0;
        line-height: 1;
        transition: color 0.2s;
    }
    .gx-toggle-pass:hover { color: #3b82f6; }

    .gx-options-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }
    .gx-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
    }
    .gx-remember input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #3b82f6;
        cursor: pointer;
    }
    .gx-forgot {
        font-size: 0.82rem;
        font-weight: 600;
        color: #3b82f6;
        text-decoration: none;
        transition: color 0.2s;
    }
    .gx-forgot:hover { color: #2563eb; }

    .gx-btn-login {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
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
        box-shadow: 0 4px 15px rgba(59,130,246,0.35);
    }
    .gx-btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59,130,246,0.45);
        opacity: 0.95;
    }
    .gx-btn-login:active {
        transform: translateY(0);
    }

    .gx-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 24px 0;
        color: #cbd5e1;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .gx-divider::before, .gx-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .gx-register-link {
        text-align: center;
        font-size: 0.82rem;
        color: #94a3b8;
        font-weight: 500;
    }
    .gx-register-link a {
        color: #3b82f6;
        font-weight: 700;
        text-decoration: none;
    }
    .gx-register-link a:hover { text-decoration: underline; }

    /* Alerts */
    .gx-alert {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .gx-alert-danger { background: #fef2f2; color: #dc2626; border-left: 3px solid #dc2626; }
    .gx-alert-success { background: #f0fdf4; color: #16a34a; border-left: 3px solid #16a34a; }

    /* Decorative grid dots on left panel */
    .gx-dots {
        position: absolute;
        right: 40px;
        top: 50%;
        transform: translateY(-50%);
        display: grid;
        grid-template-columns: repeat(6, 12px);
        gap: 10px;
        z-index: 1;
        opacity: 0.2;
    }
    .gx-dots span {
        width: 4px;
        height: 4px;
        background: #fff;
        border-radius: 50%;
        display: block;
    }

    /* Fade-in animation */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .gx-login-right > * {
        animation: fadeInUp 0.5s ease both;
    }
    .gx-login-right > *:nth-child(1) { animation-delay: 0.05s; }
    .gx-login-right > *:nth-child(2) { animation-delay: 0.1s; }
    .gx-login-right > *:nth-child(3) { animation-delay: 0.15s; }

    /* Responsive */
    @media (max-width: 768px) {
        .gx-login-left { display: none; }
        .gx-login-right { width: 100%; padding: 40px 28px; }
    }
</style>
<?php if (!defined("OFFLINE_MODE") || !OFFLINE_MODE): ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<?php endif; ?>





<div class="gx-login-wrapper">

    <!-- ===== LEFT BRAND PANEL ===== -->
    <div class="gx-login-left">
        <div class="gx-brand">
            <?= Site::logo(height:'36px') ?>
            <div>
                <div class="gx-brand-text"><?= htmlspecialchars($site_name) ?></div>
                <div class="gx-brand-badge">Admin Panel</div>
            </div>
        </div>

        <!-- Dot grid decor -->
        <div class="gx-dots">
            <?php for($i=0; $i<48; $i++): ?><span></span><?php endfor; ?>
        </div>

        <div class="gx-hero-text">
            <h1 class="gx-hero-title">
                Manage your<br>content with<br><span>confidence.</span>
            </h1>
            <p class="gx-hero-desc">
                A powerful, flexible content management system built for creators and developers who value elegance and speed.
            </p>
        </div>

        <div class="gx-stats">
            <div class="gx-stat-item">
                <div class="gx-stat-num">99%</div>
                <div class="gx-stat-label">Uptime</div>
            </div>
            <div class="gx-stat-item">
                <div class="gx-stat-num">∞</div>
                <div class="gx-stat-label">Flexibility</div>
            </div>
            <div class="gx-stat-item">
                <div class="gx-stat-num">24/7</div>
                <div class="gx-stat-label">Live</div>
            </div>
        </div>
    </div>

    <!-- ===== RIGHT FORM PANEL ===== -->
    <div class="gx-login-right">

        <div class="gx-login-header">
            <div class="gx-welcome-eyebrow">Welcome back</div>
            <h2 class="gx-login-title">Sign in to your account</h2>
            <p class="gx-login-subtitle">Enter your credentials to access the admin dashboard.</p>
        </div>

        <!-- Alerts -->
        <?php echo Hooks::run('login_form_header'); ?>
        <?php if (isset($data['alertDanger'])): ?>
            <?php foreach($data['alertDanger'] as $msg): ?>
                <div class="gx-alert gx-alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (isset($data['alertSuccess'])): ?>
            <?php foreach($data['alertSuccess'] as $msg): ?>
                <div class="gx-alert gx-alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form action="" method="post" autocomplete="on">

            <!-- Username -->
            <div class="gx-form-group">
                <label class="gx-form-label" for="loginUsername"><?= _('Username') ?></label>
                <div class="gx-input-wrapper">
                    <i class="bi bi-person gx-input-icon"></i>
                    <input id="loginUsername" type="text" name="username" class="gx-input"
                           placeholder="your_username" autocomplete="username" required>
                </div>
            </div>

            <!-- Password -->
            <div class="gx-form-group">
                <label class="gx-form-label" for="loginPassword"><?= _('Password') ?></label>
                <div class="gx-input-wrapper">
                    <i class="bi bi-lock gx-input-icon"></i>
                    <input id="loginPassword" name="password" type="password" class="gx-input"
                           placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" class="gx-toggle-pass" onclick="togglePassVis()" id="togglePassBtn" title="Toggle password">
                        <i class="bi bi-eye" id="togglePassIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Captcha if enabled -->
            <?= Xaptcha::html(); ?>

            <!-- Remember Me + Forgot -->
            <div class="gx-options-row">
                <label class="gx-remember">
                    <input type="checkbox" name="rememberme" value="1" checked>
                    <?= _('Remember Me') ?>
                </label>
                <a href="<?= $site_url ?>forgotpass/" class="gx-forgot"><?= _("Forgot password?") ?></a>
            </div>

            <input type="hidden" name="token" value="<?= TOKEN ?>">

            <button type="submit" name="login" class="gx-btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                <?= _("Sign In") ?>
            </button>
        </form>

        <?php echo Hooks::run('login_form_footer'); ?>

        <div class="gx-divider">OR</div>

        <div class="gx-register-link">
            <?= _("Don't have an account?") ?>
            <a href="<?= $site_url ?>register/"><?= _("Create one") ?></a>
        </div>

    </div>
</div>

<script>
function togglePassVis() {
    var inp = document.getElementById('loginPassword');
    var icon = document.getElementById('togglePassIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
