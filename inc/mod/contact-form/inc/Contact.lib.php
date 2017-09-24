<?php

class Contact
{
    public function __construct()
    {

        Hooks::attach('mod_control', array('Contact', 'contactPage'));
        Hooks::attach('contact_page_notification', array('System', 'alert'));
        $menulist = array(
            'contactPage' => 'Contact Us'
        );
        Mod::addMenuList($menulist);
    }


    /**
     * @param $data
     */
    public static function contactPage($data)
    {

        if ($data[0]['mod'] == 'contactPage') {/* this is to filter only appear when the mod is the same*/
            $sendMessage = isset($_POST['sendMessage']) ?  $_POST['sendMessage']: '';
            switch ($sendMessage){
                case true:
                    $token = Typo::cleanX($_POST['token']);
                    if (!isset($_POST['token']) || !Token::validate($token)){
                        $alertDanger[] = TOKEN_NOT_EXIST;
                    }
                    if (Xaptcha::isEnable()) {
                        if (!isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
                            $alertDanger[] = 'Please insert the Captcha';
                        }
                        if (!Xaptcha::verify($_POST['g-recaptcha-response'])) {
                            $alertDanger[] = 'Your Captcha is not correct.';
                        }
                    }
                    if(empty($_POST['name'])) {
                        $alertDanger[] = 'Name can\'t be empty';
                    }
                    if(empty($_POST['email'])) {
                        $alertDanger[] = 'E-Mail can\'t be empty';
                    }
                    if(empty($_POST['subject'])) {
                        $alertDanger[] = 'Subject can\'t be empty';
                    }
                    if(empty($_POST['message'])) {
                        $alertDanger[] = 'Message can\'t be empty';
                    }
                    if (!isset($alertDanger)) {
                        $date = date("d F Y H:i:s");
                        $message = "New Message from {$_POST['name']} on {$date},
                    
                        {$_POST['message']}";
                        $vars = array(
                            'to' => Site::$email,
                            'to_name' => Site::$name,
                            'subject' => 'New Contact from : ' . $_POST['subject'],
                            'message' => $message,
                        );
                        if (Mail::send($vars)) {
                            $data['alertSuccess'][] = "Your message was sent, Thank You for contacting Us.";
                        } else {
                            $data['alertDanger'][] = "Mail Not Sent";
                        }
                    } else {
                        $data['alertDanger'] = $alertDanger;
                    }
                    Mod::inc('contactpage', $data, realpath(__DIR__.'/../layout/'));
                    break;
                default:
                    Mod::inc('contactpage', $data, realpath(__DIR__.'/../layout/'));
                    break;
            }


        }
    }
}
