<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 0.0.1 build date 20150126
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

use PHPMailer\PHPMailer;
/**
 *
 */
class Mail
{
    public static $smtphost = '';
    public static $smtpuser = '';
    public static $smtppass = '';
    public static $smtpport = '';
    public static $siteemail = '';
    public static $sitename = '';

    public function __construct()
    {
        self::$smtphost = Options::v('smtphost');
        self::$smtpuser = Options::v('smtpuser');
        self::$smtppass = Options::v('smtppass');
        self::$smtpport = Options::v('smtpport');
        self::$siteemail = Options::v('siteemail');
        self::$sitename = Options::v('sitename');
    }

    ## SEND MAIL
    // $vars = array (
    //             'to'         => $to,
    //             'to_name'    => $to_name,
    //             'subject'    => $subject,
    //             'message'    => $message,
    //             'msgtype'    => $msgtype,
    //         );

    public static function send($vars)
    {
        self::$smtphost = Options::v('smtphost');
        self::$smtpuser = Options::v('smtpuser');
        self::$smtppass = Options::v('smtppass');
        self::$smtpport = Options::v('smtpport');
        self::$siteemail = Options::v('siteemail');
        self::$sitename = Options::v('sitename');

        //print_r($vars);
        $to = $vars['to'];
        $to_name = $vars['to_name'];
        $subject = $vars['subject'];
        $message = $vars['message'];
        (isset($vars['msgtype'])) ? $msgtype = $vars['msgtype'] : $msgtype = 'html';
        //require_once GX_LIB.'/Vendor/PHPMailer/PHPMailerAutoload.php';
        // check if using plain mail or smtp
        $type = Options::v('mailtype');

        if ($type == 0) { // use php mail command
            //Create a new PHPMailer instance
            $mail = new PHPMailer\PHPMailer(true);

            try {
                $mail->isMail();
                //Set who the message is to be sent from
                $mail->setFrom(self::$siteemail, self::$sitename);
                //Set an alternative reply-to address
                $mail->addReplyTo(self::$siteemail, self::$sitename);
                //Set who the message is to be sent to
                $mail->addAddress($to, $to_name);
                //Set the subject line
                $mail->Subject = $subject;
                //Replace the plain text body with one created manually
                if ($msgtype == 'text') {
                    $mail->ContentType = 'text/plain';
                    $mail->IsHTML(false);
                    $mail->Body = $message;
                } else {
                    $mail->msgHTML($message);
                }

                $mail->send();
            } catch (phpmailerException $e) {
                return $e->errorMessage(); //Pretty error messages from PHPMailer
            } catch (Exception $e) {
                return $e->getMessage(); //Boring error messages from anything else!
            }
            //send the message, check for errors
            // if (!$mail->send()) {
            //     $mailer = "Mailer Error: " . $mail->ErrorInfo;
            // } else {
            //     $mailer = "Message sent!";
            // }
        } elseif ($type == 1) {
            //Create a new PHPMailer instance
            $mail = new PHPMailer\PHPMailer(true);
            try {
                //Tell PHPMailer to use SMTP
                $mail->isSMTP();
                //Enable SMTP debugging
                // 0 = off (for production use)
                // 1 = client messages
                // 2 = client and server messages
                $mail->SMTPDebug = 0;
                //Ask for HTML-friendly debug output
                $mail->Debugoutput = 'html';
                //Set the hostname of the mail server
                $mail->Host = self::$smtphost;
                //Set the SMTP port number - likely to be 25, 465 or 587
                $mail->Port = self::$smtpport;

                //Whether to use SMTP authentication
                $mail->SMTPAuth = true;
                //Username to use for SMTP authentication
                $mail->Username = self::$smtpuser;
                //Password to use for SMTP authentication
                $mail->Password = self::$smtppass;
                //Set who the message is to be sent from
                $mail->setFrom(self::$siteemail, self::$sitename);
                //Set an alternative reply-to address
                $mail->addReplyTo(self::$siteemail, self::$sitename);
                //Set who the message is to be sent to
                $mail->addAddress($to, $to_name);
                //Set the subject line
                $mail->Subject = $subject;
                //Replace the plain text body with one created manually
                if ($msgtype == 'text') {
                    $mail->ContentType = 'text/plain';
                    $mail->IsHTML(false);
                    $mail->Body = $message;
                } else {
                    $mail->msgHTML($message);
                }
                $mail->send();
            } catch (phpmailerException $e) {
                return $e->errorMessage(); //Pretty error messages from PHPMailer
            } catch (Exception $e) {
                return $e->getMessage(); //Boring error messages from anything else!
            }
            // //send the message, check for errors
            // if (!$mail->send()) {
            //     $mailer = "Mailer Error: " . $mail->ErrorInfo;
            // } else {
            //     $mailer = "Message sent!";
            // }
        }

        //return $mailer;
    }
}

/* End of file Mail.class.php */
/* Location: ./inc/lib/Mail.class.php */
