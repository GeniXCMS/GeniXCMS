<?php
/**
 * Name: NewsLetter
 * Desc: Newsletter Campaign Manager — send emails to members and subscribers.
 * Version: 2.0.0
 * Build: 2.0.0
 * Developer: GeniXCMS
 * URI: https://github.com/GeniXCMS/GeniXCMS
 * License: MIT License
 * Icon: fa fa-envelope
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');
require_once __DIR__ . '/Newsletter.class.php';

class NewsletterModule
{
    private static $statusMsg = '';

    public static function init()
    {
        if (isset($_POST['sm_subscribe_newsletter'])) {
            self::handleSubscription();
        }
        if (isset($_GET['newsletter_unsubscribe']) && isset($_GET['token'])) {
            self::handleUnsubscription();
        }
        Hooks::attach('newsletter_form', array('NewsletterModule', 'renderForm'));
    }

    private static function handleSubscription()
    {
        if (!isset($_POST['ns_token']) || !Token::validate($_POST['ns_token'])) {
            self::$statusMsg = '<div class="alert alert-danger">Invalid Token. Please refresh and try again.</div>';
            return;
        }

        $email = Typo::cleanX($_POST['ns_email'] ?? '');
        $name  = Typo::cleanX($_POST['ns_name'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::$statusMsg = '<div class="alert alert-danger">Format email tidak valid.</div>';
            return;
        }

        if (Newsletter::subscriberAdd($email, $name)) {
            self::$statusMsg = '<div class="alert alert-success">Terima kasih! Anda berhasil berlangganan newsletter kami.</div>';
        } else {
            self::$statusMsg = '<div class="alert alert-warning">Email ini sudah terdaftar.</div>';
        }
    }

    private static function handleUnsubscription()
    {
        $email = Typo::cleanX($_GET['newsletter_unsubscribe']);
        $token = Typo::cleanX($_GET['token']);

        $sub = Query::table('newsletter_subscribers')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if ($sub) {
            Query::table('newsletter_subscribers')->where('id', $sub->id)->update(['status' => 0]);
            self::$statusMsg = '<div class="alert alert-success mt-3 container">Anda telah berhasil berhenti berlangganan newsletter kami.</div>';
            // Show alert directly if it's a GET request without standard widget rendered
            echo self::$statusMsg;
        } else {
            self::$statusMsg = '<div class="alert alert-danger mt-3 container">Link unsubscribe tidak valid atau kadaluarsa.</div>';
            echo self::$statusMsg;
        }
    }

    public static function renderForm()
    {
        $token = Token::create();
        $html = self::$statusMsg . '
        <div class="newsletter-widget p-4 rounded shadow-sm bg-light">
            <h5 class="fw-bold mb-3"><i class="fa fa-envelope-o text-primary"></i> Berlangganan Newsletter</h5>
            <p class="text-muted small">Dapatkan update terbaru langsung ke inbox Anda.</p>
            <form method="post" action="">
                <input type="hidden" name="ns_token" value="' . $token . '">
                <div class="mb-2">
                    <input type="text" name="ns_name" class="form-control" placeholder="Nama Anda (opsional)">
                </div>
                <div class="mb-2">
                    <input type="email" name="ns_email" class="form-control" placeholder="Alamat Email Anda" required>
                </div>
                <button type="submit" name="sm_subscribe_newsletter" class="btn btn-primary w-100">
                    Subscribe
                </button>
            </form>
        </div>';

        return $html;
    }
}

// Inisialisasi Modul
NewsletterModule::init();
