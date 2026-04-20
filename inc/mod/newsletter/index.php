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
    public static $statusMsg = '';

    public static function init()
    {
        if (isset($_POST['sm_subscribe_newsletter'])) {
            self::handleSubscription();
        }
        if (isset($_GET['newsletter_unsubscribe']) && isset($_GET['token'])) {
            self::handleUnsubscription();
        }
        Hooks::attach('newsletter_form', array('NewsletterModule', 'renderForm'));
        Hooks::attach('post_content_filter', array('NewsletterModule', 'parseShortcode'));
        
        // Register as a dedicated widget type for easier theme usage
        Hooks::attach('widget_types_list', function($defaults) {
            $defaults['newsletter'] = _('Newsletter: Subscription Form');
            return $defaults;
        });
        Hooks::attach('widget_render_newsletter', array('NewsletterModule', 'renderForm'));
    }

    public static function parseShortcode($content)
    {
        if (is_array($content)) {
            $content = isset($content[0]) ? $content[0] : '';
        }

        if (!class_exists('Shortcode') || empty($content)) {
            return $content;
        }

        return Shortcode::parse('newsletter', $content, function($attrs) {
            return self::renderForm();
        });
    }

    private static function handleSubscription()
    {
        if (!isset($_POST['ns_token']) || !Token::validate($_POST['ns_token'])) {
            self::$statusMsg = '<div class="gx-alert gx-alert-danger">Invalid Token. Please refresh and try again.</div>';
            return;
        }

        $email = Typo::cleanX($_POST['ns_email'] ?? '');
        $name  = Typo::cleanX($_POST['ns_name'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::$statusMsg = '<div class="gx-alert gx-alert-danger">Invalid email format.</div>';
            return;
        }

        if (Newsletter::subscriberAdd($email, $name)) {
            self::$statusMsg = '<div class="gx-alert gx-alert-success">Thank you! You have successfully subscribed to our newsletter.</div>';
        } else {
            self::$statusMsg = '<div class="gx-alert gx-alert-warning">This email is already registered.</div>';
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
            self::$statusMsg = '<div class="gx-alert gx-alert-success mt-3 container">You have successfully unsubscribed from our newsletter.</div>';
            // Show alert directly if it's a GET request without standard widget rendered
            echo self::$statusMsg;
        } else {
            self::$statusMsg = '<div class="gx-alert gx-alert-danger mt-3 container">Invalid or expired unsubscribe link.</div>';
            echo self::$statusMsg;
        }
    }

    public static function renderForm()
    {
        $token = Token::create();
        $html = self::$statusMsg . '
        <div class="newsletter-widget gx-p-4 gx-rounded gx-shadow gx-bg-white gx-border">
            <h5 class="gx-fw-bold gx-mb-3"><i class="fa fa-envelope-o gx-text-primary"></i> Subscribe to Newsletter</h5>
            <p class="gx-text-muted gx-text-sm">Get the latest updates delivered straight to your inbox.</p>
            <form method="post" action="">
                <input type="hidden" name="ns_token" value="' . $token . '">
                <div class="gx-mb-2">
                    <input type="text" name="ns_name" class="gx-input" placeholder="Your Name (optional)">
                </div>
                <div class="gx-mb-2">
                    <input type="email" name="ns_email" class="gx-input" placeholder="Your Email Address" required>
                </div>
                <button type="submit" name="sm_subscribe_newsletter" class="gx-btn gx-btn-primary gx-btn-block">
                    Subscribe
                </button>
            </form>
        </div>';

        return $html;
    }
}

// Inisialisasi Modul
NewsletterModule::init();
Asset::enqueue('genixcms-css');

// -----------------------------------------------------------------------
// LIFECYCLE HOOKS
// -----------------------------------------------------------------------
Hooks::attach('newsletter_activate', function() {
    Newsletter::install();
});

Hooks::attach('newsletter_delete', function() {
    // Optional: Drop tables on permanent deletion
    try {
        if (Db::connect()) {
            Db::$pdo->exec("DROP TABLE IF EXISTS `newsletter_subscribers` ");
            Db::$pdo->exec("DROP TABLE IF EXISTS `newsletter_campaigns` ");
            Db::$pdo->exec("DROP TABLE IF EXISTS `newsletter_logs` ");
        }
    } catch (Exception $e) {}
});

// -----------------------------------------------------------------------
// BACKEND UI REGISTRATION
// -----------------------------------------------------------------------
AdminMenu::add([
    'id'       => 'newsletter',
    'label'    => _('Newsletter'),
    'icon'     => 'bi bi-mailbox2',
    'url'      => 'index.php?page=mods&mod=newsletter',
    'access'   => 0,          // Administrators only (or change to 2 for editors)
    'position' => 'main',     // Inject directly into the primary Main Navigation
    'order'    => 65,         // Below system components
]);
