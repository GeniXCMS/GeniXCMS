<?php
$site_name = Site::$name ?? 'GeniXCMS';
$site_url  = Site::$url;
?>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body.login-page {
        background: #0f172a !important;
        overflow: hidden;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    .gx-reg-wrapper {
        display: flex;
        min-height: 100vh;
        width: 100%;
        overflow: hidden;
    }

    /* ============ RIGHT FORM PANEL ============ */
    .gx-reg-right {
        width: 520px;
        flex-shrink: 0;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 44px 52px;
        position: relative;
        box-shadow: 20px 0 60px rgba(0,0,0,0.3);
        z-index: 2;
        overflow-y: auto;
    }

    .gx-reg-header { margin-bottom: 28px; }
    .gx-welcome-eyebrow {
        font-size: 11px;
        font-weight: 800;
        color: #8b5cf6;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 8px;
    }
    .gx-reg-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }
    .gx-reg-subtitle {
        color: #94a3b8;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    /* Form Elements */
    .gx-form-group { margin-bottom: 18px; }
    .gx-form-row   { display: flex; gap: 14px; }
    .gx-form-row .gx-form-group { flex: 1; }

    .gx-form-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 7px;
    }
    .gx-input-wrapper { position: relative; }
    .gx-input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #cbd5e1;
        font-size: 15px;
        z-index: 2;
        transition: color 0.2s;
    }
    .gx-input-wrapper:focus-within .gx-input-icon { color: #8b5cf6; }
    .gx-input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.875rem;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: all 0.25s ease;
        font-family: inherit;
    }
    .gx-input:focus {
        border-color: #8b5cf6;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(139,92,246,0.1);
    }
    .gx-input::placeholder { color: #cbd5e1; }
    .gx-input.is-error  { border-color: #ef4444; background: #fef2f2; }
    .gx-input.is-ok     { border-color: #22c55e; }

    .gx-toggle-pass {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #cbd5e1;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        transition: color 0.2s;
    }
    .gx-toggle-pass:hover { color: #8b5cf6; }

    /* Password strength */
    .gx-pass-strength {
        margin-top: 6px;
        display: flex;
        gap: 4px;
    }
    .gx-strength-bar {
        flex: 1;
        height: 3px;
        border-radius: 10px;
        background: #e2e8f0;
        transition: background 0.3s;
    }
    .gx-strength-text {
        font-size: 0.7rem;
        font-weight: 600;
        color: #94a3b8;
        margin-top: 4px;
    }

    /* Aggreement */
    .gx-terms {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 22px;
        padding: 14px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .gx-terms input[type="checkbox"] {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        margin-top: 2px;
        accent-color: #8b5cf6;
        cursor: pointer;
    }
    .gx-terms-text {
        font-size: 0.8rem;
        color: #64748b;
        line-height: 1.5;
    }
    .gx-terms-text a { color: #8b5cf6; font-weight: 700; text-decoration: none; }
    .gx-terms-text a:hover { text-decoration: underline; }

    .gx-btn-register {
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(139,92,246,0.35);
    }
    .gx-btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139,92,246,0.45);
    }
    .gx-btn-register:active { transform: translateY(0); }

    .gx-login-link {
        text-align: center;
        font-size: 0.82rem;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 20px;
    }
    .gx-login-link a {
        color: #8b5cf6;
        font-weight: 700;
        text-decoration: none;
    }
    .gx-login-link a:hover { text-decoration: underline; }

    /* Alerts */
    .gx-alert {
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .gx-alert-danger  { background: #fef2f2; color: #dc2626; border-left: 3px solid #dc2626; }
    .gx-alert-success { background: #f0fdf4; color: #16a34a; border-left: 3px solid #16a34a; }

    /* ============ LEFT BRAND PANEL ============ */
    .gx-reg-left {
        flex: 1;
        background: linear-gradient(145deg, #2d1b69 0%, #0f172a 55%, #1a1040 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 52px;
        position: relative;
        overflow: hidden;
    }
    .gx-reg-left::before {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(139,92,246,0.25) 0%, transparent 70%);
        bottom: -120px;
        right: -120px;
        border-radius: 50%;
        animation: pulse-glow 7s ease-in-out infinite alternate;
    }
    .gx-reg-left::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(99,102,241,0.2) 0%, transparent 70%);
        top: 60px;
        left: -80px;
        border-radius: 50%;
        animation: pulse-glow 9s ease-in-out infinite alternate-reverse;
    }
    @keyframes pulse-glow {
        0%   { transform: scale(1);    opacity: 0.6; }
        100% { transform: scale(1.2);  opacity: 1; }
    }

    .gx-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 2;
    }
    .gx-brand-text  { font-size: 1.05rem; font-weight: 700; color: #fff; }
    .gx-brand-badge {
        background: rgba(139,92,246,0.25);
        border: 1px solid rgba(139,92,246,0.5);
        color: #c4b5fd;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 50px;
    }

    /* Steps / benefits */
    .gx-hero-desc-title {
        font-size: clamp(1.5rem, 2.5vw, 2.2rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
        margin-bottom: 16px;
        letter-spacing: -0.5px;
        position: relative;
        z-index: 2;
    }
    .gx-hero-desc-title span {
        background: linear-gradient(90deg, #a78bfa, #818cf8);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .gx-benefits {
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
        z-index: 2;
    }
    .gx-benefit-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }
    .gx-benefit-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(139,92,246,0.2);
        border: 1px solid rgba(139,92,246,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 16px;
        color: #a78bfa;
    }
    .gx-benefit-text strong {
        display: block;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .gx-benefit-text span {
        color: rgba(255,255,255,0.45);
        font-size: 0.78rem;
        line-height: 1.4;
    }

    .gx-bottom-note {
        color: rgba(255,255,255,0.3);
        font-size: 0.72rem;
        position: relative;
        z-index: 2;
    }

    /* Animated circles in background */
    .gx-floating-circle {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(139,92,246,0.15);
        animation: float-circle linear infinite;
        z-index: 1;
    }
    @keyframes float-circle {
        from { transform: rotate(0deg) translate(20px) rotate(0deg); }
        to   { transform: rotate(360deg) translate(20px) rotate(-360deg); }
    }

    /* Fade in right panel */
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(30px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .gx-reg-right {
        animation: fadeInRight 0.55s ease both;
    }

    @media (max-width: 768px) {
        .gx-reg-left  { display: none; }
        .gx-reg-right { width: 100%; padding: 36px 24px; box-shadow: none; }
        body.login-page { overflow-y: auto; }
    }
</style>
<?php if (!defined("OFFLINE_MODE") || !OFFLINE_MODE): ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<?php endif; ?>





<div class="gx-reg-wrapper">

    <!-- ===== LEFT BRAND PANEL ===== -->
    <div class="gx-reg-left">

        <!-- Floating decorative circles -->
        <div class="gx-floating-circle" style="width:220px;height:220px;top:15%;left:10%;animation-duration:22s;"></div>
        <div class="gx-floating-circle" style="width:120px;height:120px;bottom:25%;right:15%;animation-duration:16s;animation-direction:reverse;"></div>

        <div class="gx-brand">
            <?= Site::logo(height:'34px') ?>
            <div>
                <div class="gx-brand-text"><?= htmlspecialchars($site_name) ?></div>
                <div class="gx-brand-badge">Admin Panel</div>
            </div>
        </div>

        <div style="position:relative;z-index:2;">
            <h2 class="gx-hero-desc-title">
                Join and start<br>building something<br><span>extraordinary.</span>
            </h2>

            <div class="gx-benefits">
                <div class="gx-benefit-item">
                    <div class="gx-benefit-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                    <div class="gx-benefit-text">
                        <strong>Instant Dashboard Access</strong>
                        <span>Manage all your content, media, and pages from one powerful place.</span>
                    </div>
                </div>
                <div class="gx-benefit-item">
                    <div class="gx-benefit-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <div class="gx-benefit-text">
                        <strong>Secure by Default</strong>
                        <span>Your data is protected by modern encryption and role-based access control.</span>
                    </div>
                </div>
                <div class="gx-benefit-item">
                    <div class="gx-benefit-icon"><i class="bi bi-puzzle-fill"></i></div>
                    <div class="gx-benefit-text">
                        <strong>Extensible &amp; Modular</strong>
                        <span>Expand functionality with modules and themes tailored for your project.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="gx-bottom-note">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($site_name) ?> &mdash; Powered by GeniXCMS
        </div>
    </div>

    <!-- ===== RIGHT FORM PANEL ===== -->
    <div class="gx-reg-right">

        <div class="gx-reg-header">
            <div class="gx-welcome-eyebrow">Create Account</div>
            <h2 class="gx-reg-title">Start your journey</h2>
            <p class="gx-reg-subtitle">Fill in the details below to create your free account.</p>
        </div>

        <!-- Alerts -->
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

        <form action="" method="post" id="regForm" autocomplete="off">

            <!-- Username -->
            <div class="gx-form-group">
                <label class="gx-form-label" for="regUsername"><?= _('Username') ?></label>
                <div class="gx-input-wrapper">
                    <i class="bi bi-person gx-input-icon"></i>
                    <input id="regUsername" type="text" name="userid" class="gx-input"
                           placeholder="e.g. john_doe" minlength="6" maxlength="30"
                           pattern="[a-zA-Z0-9_]+" required>
                </div>
                <div class="gx-strength-text" id="username-hint" style="display:none;"></div>
            </div>

            <!-- Email -->
            <div class="gx-form-group">
                <label class="gx-form-label" for="regEmail"><?= _('Email Address') ?></label>
                <div class="gx-input-wrapper">
                    <i class="bi bi-envelope gx-input-icon"></i>
                    <input id="regEmail" type="email" name="email" class="gx-input"
                           placeholder="you@example.com" required>
                </div>
            </div>

            <!-- Password -->
            <div class="gx-form-group">
                <label class="gx-form-label" for="regPassword"><?= _('Password') ?></label>
                <div class="gx-input-wrapper">
                    <i class="bi bi-lock gx-input-icon"></i>
                    <input id="regPassword" name="pass1" type="password" class="gx-input"
                           placeholder="At least 8 characters" minlength="8" required
                           oninput="checkStrength(this.value)">
                    <button type="button" class="gx-toggle-pass" onclick="togglePass('regPassword','togglePass1Icon')" title="Toggle">
                        <i class="bi bi-eye" id="togglePass1Icon"></i>
                    </button>
                </div>
                <div class="gx-pass-strength" id="strengthBars">
                    <div class="gx-strength-bar" id="bar1"></div>
                    <div class="gx-strength-bar" id="bar2"></div>
                    <div class="gx-strength-bar" id="bar3"></div>
                    <div class="gx-strength-bar" id="bar4"></div>
                </div>
                <div class="gx-strength-text" id="strengthText"></div>
            </div>

            <!-- Confirm Password -->
            <div class="gx-form-group">
                <label class="gx-form-label" for="regPassword2"><?= _('Confirm Password') ?></label>
                <div class="gx-input-wrapper">
                    <i class="bi bi-lock-fill gx-input-icon"></i>
                    <input id="regPassword2" name="pass2" type="password" class="gx-input"
                           placeholder="Repeat your password" required
                           oninput="checkMatch()">
                    <button type="button" class="gx-toggle-pass" onclick="togglePass('regPassword2','togglePass2Icon')" title="Toggle">
                        <i class="bi bi-eye" id="togglePass2Icon"></i>
                    </button>
                </div>
                <div class="gx-strength-text" id="matchText"></div>
            </div>

            <!-- Captcha if enabled -->
            <?= Xaptcha::html(); ?>

            <!-- Terms -->
            <div class="gx-terms">
                <input type="checkbox" id="agreeTerms" required>
                <label class="gx-terms-text" for="agreeTerms">
                    By registering, I agree to the
                    <a href="#">Terms of Service</a> and
                    <a href="#">Privacy Policy</a>. I understand my account requires activation via email.
                </label>
            </div>

            <input type="hidden" name="token" value="<?= TOKEN ?>">

            <button type="submit" name="register" class="gx-btn-register">
                <i class="bi bi-person-plus-fill"></i>
                <?= _("Create My Account") ?>
            </button>
        </form>

        <div class="gx-login-link">
            Already have an account?
            <a href="<?= $site_url ?>login/"><?= _("Sign in here") ?></a>
        </div>

    </div>
</div>

<script>
/* ---------- Password toggle ---------- */
function togglePass(inputId, iconId) {
    var inp  = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type  = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        inp.type  = 'password';
        icon.className = 'bi bi-eye';
    }
}

/* ---------- Password strength ---------- */
function checkStrength(val) {
    var score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^a-zA-Z0-9]/.test(val)) score++;

    var colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    var labels = ['Weak','Fair','Good','Strong'];
    var bars   = document.querySelectorAll('.gx-strength-bar');
    var txt    = document.getElementById('strengthText');

    bars.forEach(function(b, i) {
        b.style.background = i < score ? colors[score - 1] : '#e2e8f0';
    });
    txt.textContent = val.length ? labels[score - 1] || '' : '';
    txt.style.color = score ? colors[score - 1] : '#94a3b8';
}

/* ---------- Password match ---------- */
function checkMatch() {
    var p1  = document.getElementById('regPassword').value;
    var p2  = document.getElementById('regPassword2').value;
    var txt = document.getElementById('matchText');
    var inp = document.getElementById('regPassword2');
    if (p2.length === 0) { txt.textContent = ''; inp.className = 'gx-input'; return; }
    if (p1 === p2) {
        txt.textContent = '✓ Passwords match';
        txt.style.color = '#22c55e';
        inp.className   = 'gx-input is-ok';
    } else {
        txt.textContent = '✗ Passwords do not match';
        txt.style.color = '#ef4444';
        inp.className   = 'gx-input is-error';
    }
}

/* ---------- Username hint ---------- */
document.getElementById('regUsername').addEventListener('input', function() {
    var hint = document.getElementById('username-hint');
    var ok   = /^[a-zA-Z0-9_]{6,30}$/.test(this.value);
    hint.style.display = this.value.length ? 'block' : 'none';
    if (ok) {
        hint.textContent = '✓ Username looks good!';
        hint.style.color = '#22c55e';
        this.className   = 'gx-input is-ok';
    } else {
        hint.textContent = '6–30 characters, letters, numbers and _ only.';
        hint.style.color = '#f97316';
        this.className   = 'gx-input';
    }
});
</script>
