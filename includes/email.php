<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function sendEmail($to, $subject, $message, $attachmentContent)
{
    echo $to;
    echo $subject;
    echo $message;
    echo $attachmentContent;
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'abdullahsufyan2007@gmail.com'; // SMTP username
        $mail->Password = 'ndcevprxowqgwbok'; // SMTP password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS for port 587
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('gmt15939@gmail.com', 'Mailer');
        $mail->addAddress($to);

        // Attachments
        $mail->addStringAttachment($attachmentContent, 'report.html');

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>