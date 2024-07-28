<?php
function sendEmail($to, $subject, $message, $attachmentContent)
{
    // Implementation of your email sending logic
    // You can use an email library like PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'user@example.com'; // SMTP username
        $mail->Password = 'secret'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('from@example.com', 'Mailer');
        $mail->addAddress($to);

        // Attachments
        $mail->addStringAttachment($attachmentContent, 'report.html');

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}
?>