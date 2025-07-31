<?php
// require_once(__DIR__ . '/vendor/phpmailer/PHPMailer.php');
// require_once(__DIR__ . '/phpmailer/SMTP.php');
// require_once(__DIR__ . '/phpmailer/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require "vendor/autoload.php";
function send_otp_email($to_email, $otp_code) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bienvenugashema@gmail.com';       // ✅ your Gmail
        $mail->Password   = 'bbbbbbbbbbbbbbbbbbbbbbbbbbbb';          // ✅ Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('bienvenugashema@gmail.com', 'ITBIENVENU');
        $mail->addAddress($to_email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<h3>Your OTP is:</h3><h2 style='color:blue;'>$otp_code</h2><p>Expires in 10 minutes.</p>";
        $mail->AltBody = "Your OTP is: $otp_code (valid for 10 minutes)";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
