<?php
namespace app\components;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Emailer
{
    public static function sendEmail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.bps.go.id';
            $mail->SMTPAuth = true;
            $mail->Username = 'portalpintar';
            $mail->Password = 'Portalpintar1700';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Email headers and body
            $mail->setFrom('portalpintar@bps.go.id', 'Portal Pintar');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            if (!$mail->send()) {
                return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
    
            return true;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}