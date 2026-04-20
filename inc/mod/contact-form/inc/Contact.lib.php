<?php

class Contact
{
    public function __construct()
    {
        Hooks::attach('mod_control', array('Contact', 'contactPage'));
        Hooks::attach('contact_page_notification', array('System', 'alert'));
        Asset::enqueue(['gx-toast-css', 'gx-toast-js']);

        // Register this module's public hook ID and title into Mod::$listMenu
        // so that Mod::getTitle('contactPage') and Mod.control.php routing work correctly.
        Mod::addMenuList(['contactPage' => _('Contact Us')]);

        Hooks::attach('init', function () {
            AdminMenu::add([
                'id' => 'contactPage',
                'label' => _('Contact Us'),
                'icon' => 'bi bi-envelope',
                'url' => 'index.php?page=mods&mod=contact-form',
                'access' => 1,
                'position' => 'external',
                'order' => 10,
            ]);
        });
    }


    /**
     * @param $data
     */
    public static function contactPage($data)
    {
        if ($data[0]['mod'] == 'contactPage') {
            // Load module options (with fallbacks)
            $opt          = json_decode(Options::get('contact_form_options') ?? '{}', true) ?? [];
            $notify_email = !empty($opt['notify_email']) ? $opt['notify_email'] : Site::$email;
            $success_msg  = !empty($opt['success_msg'])  ? $opt['success_msg']  : _("Your message was sent, Thank You for contacting Us.");
            $show_phone   = ($opt['show_phone'] ?? 'off') === 'on';
            $show_address = ($opt['show_address'] ?? 'off') === 'on';

            $page_data = $data[0];
            $page_data['contact_opt'] = [
                'show_phone'   => $show_phone,
                'show_address' => $show_address,
                'phone'        => $opt['phone'] ?? '',
                'address'      => $opt['address'] ?? '',
            ];

            $sendMessage = isset($_POST['sendMessage']) ? $_POST['sendMessage'] : '';
            switch ($sendMessage) {
                case true:
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) && !Token::validate($token)) {
                        $alertDanger[] = _("Token not exist, or your time has expired. Please refresh your browser to get a new token.");
                    }
                    if (Xaptcha::isEnable()) {
                        if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
                            $alertDanger[] = 'Please insert the Captcha';
                        }
                        if (!Xaptcha::verify($_POST['g-recaptcha-response'])) {
                            $alertDanger[] = 'Your Captcha is not correct.';
                        }
                    }
                    if (empty($_POST['name'])) {
                        $alertDanger[] = 'Name can\'t be empty';
                    }
                    if (empty($_POST['email'])) {
                        $alertDanger[] = 'E-Mail can\'t be empty';
                    }
                    if (empty($_POST['subject'])) {
                        $alertDanger[] = 'Subject can\'t be empty';
                    }
                    if (empty($_POST['message'])) {
                        $alertDanger[] = 'Message can\'t be empty';
                    }
                    if (!isset($alertDanger)) {
                        $date    = date("d F Y H:i:s");
                        $message = "New Message from {$_POST['name']} on {$date},\n\n{$_POST['message']}";
                        $vars = [
                            'to'      => $notify_email,
                            'to_name' => Site::$name,
                            'subject' => 'New Contact from : ' . $_POST['subject'],
                            'message' => $message,
                        ];
                        if (Mail::send($vars)) {
                            $page_data['alertSuccess'][] = $success_msg;
                        } else {
                            $page_data['alertDanger'][] = _("Mail Not Sent");
                        }
                    } else {
                        $page_data['alertDanger'] = $alertDanger;
                    }
                    return Mod::inc('contactpage', $page_data, realpath(__DIR__ . '/../layout/'));
                default:
                    return Mod::inc('contactpage', $page_data, realpath(__DIR__ . '/../layout/'));
            }
        }
    }
}
