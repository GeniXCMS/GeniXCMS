<?php
// Run the notification hook - System::alert is attached and now generates toast scripts
// The return value contains the <script> tags for window.showGxToast
echo Hooks::run('contact_page_notification', $data);

$opt          = $contact_opt ?? [];
$show_phone   = !empty($opt['show_phone']);
$show_address = !empty($opt['show_address']);
$has_sidebar  = $show_phone || $show_address;

$formWidth = $has_sidebar ? 'col-lg-8' : 'col-12';
?>

<div class="row g-5">
    <!-- Contact Form -->
    <div class="<?= $formWidth ?>">
        <form action="" method="post" class="contact-form">
            <div class="mb-4">
                <label class="form-label fw-bold">Name</label>
                <input type="text" class="form-control form-control-lg bg-light border-0 rounded-3" name="name" required placeholder="Your full name">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">E-Mail</label>
                <input type="email" class="form-control form-control-lg bg-light border-0 rounded-3" name="email" required placeholder="name@example.com">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Subject</label>
                <input type="text" class="form-control form-control-lg bg-light border-0 rounded-3" name="subject" required placeholder="What is this about?">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Message</label>
                <textarea class="form-control form-control-lg bg-light border-0 rounded-3" name="message" rows="8" required placeholder="Write your message here..."></textarea>
            </div>

            <?php $captcha = Xaptcha::html(); if ($captcha): ?>
            <div class="mb-4">
                <?= $captcha ?>
            </div>
            <?php endif; ?>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm" name="sendMessage" value="true">
                    <i class="bi bi-send me-2"></i> Send Message
                </button>
                <input type="hidden" name="token" value="<?= TOKEN; ?>">
            </div>
        </form>
    </div>

    <?php if ($has_sidebar): ?>
    <!-- Contact Info Sidebar -->
    <div class="col-lg-4">
        <div class="contact-info-card bg-primary text-white rounded-4 p-4 shadow-sm h-100">
            <h5 class="fw-bold mb-4 border-bottom border-white border-opacity-25 pb-3">
                <i class="bi bi-geo-alt me-2"></i>Get in Touch
            </h5>

            <?php if ($show_phone && !empty($opt['phone'])): ?>
            <div class="d-flex align-items-start mb-4">
                <div class="contact-info-icon me-3 bg-white bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-telephone fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold small text-white text-opacity-75 mb-1">Phone</div>
                    <a href="tel:<?= htmlspecialchars($opt['phone']) ?>" class="text-white text-decoration-none fw-semibold">
                        <?= htmlspecialchars($opt['phone']) ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($show_address && !empty($opt['address'])): ?>
            <div class="d-flex align-items-start mb-4">
                <div class="contact-info-icon me-3 bg-white bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-pin-map fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold small text-white text-opacity-75 mb-1">Address</div>
                    <span class="fw-semibold"><?= nl2br(htmlspecialchars($opt['address'])) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-flex align-items-start">
                <div class="contact-info-icon me-3 bg-white bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-envelope fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold small text-white text-opacity-75 mb-1">Email</div>
                    <a href="mailto:<?= htmlspecialchars(Options::v('siteemail')) ?>" class="text-white text-decoration-none fw-semibold">
                        <?= htmlspecialchars(Options::v('siteemail')) ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
