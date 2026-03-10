<?php
/**
 * Simple SMTP mailer using fsockopen (no external libraries needed)
 * Supports Gmail SSL on port 465
 */

require_once __DIR__ . '/mail_config.php';

function sendOTPEmail($to_email, $to_name, $otp) {
    $subject = "AgroCulture - Your Password Reset OTP";
    $body = "
    <html><body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:20px'>
    <div style='max-width:480px;margin:auto;background:#fff;border-radius:8px;padding:30px;box-shadow:0 2px 8px rgba(0,0,0,0.1)'>
        <h2 style='color:#4CAF50;text-align:center'>AgroCulture</h2>
        <hr style='border:1px solid #eee'>
        <p>Hello <strong>" . htmlspecialchars($to_name) . "</strong>,</p>
        <p>You requested a password reset. Use the OTP below. It expires in <strong>" . OTP_EXPIRY_MINUTES . " minutes</strong>.</p>
        <div style='text-align:center;margin:30px 0'>
            <span style='font-size:36px;font-weight:bold;letter-spacing:10px;color:#333;background:#f0f0f0;padding:15px 30px;border-radius:8px'>$otp</span>
        </div>
        <p style='color:#888;font-size:13px'>If you did not request this, please ignore this email.</p>
        <hr style='border:1px solid #eee'>
        <p style='color:#aaa;font-size:11px;text-align:center'>&copy; AgroCulture Farm Management System</p>
    </div>
    </body></html>";

    return smtpSendMail($to_email, $to_name, $subject, $body);
}

function smtpSendMail($to, $to_name, $subject, $body) {
    $from    = MAIL_FROM_ADDRESS;
    $pass    = MAIL_FROM_PASSWORD;
    $name    = MAIL_FROM_NAME;
    $host    = MAIL_SMTP_HOST;
    $port    = MAIL_SMTP_PORT;

    $socket = @fsockopen($host, $port, $errno, $errstr, 30);
    if (!$socket) {
        error_log("SMTP connect failed: $errstr ($errno)");
        return false;
    }

    $read = function() use ($socket) {
        return fgets($socket, 1024);
    };
    $write = function($cmd) use ($socket) {
        fputs($socket, $cmd . "\r\n");
    };

    $read(); // greeting

    $write("EHLO localhost");
    while ($line = $read()) { if (substr($line, 3, 1) == ' ') break; }

    $write("AUTH LOGIN");
    $read();
    $write(base64_encode($from));
    $read();
    $write(base64_encode($pass));
    $response = $read();
    if (strpos($response, '235') === false) {
        fclose($socket);
        error_log("SMTP AUTH failed: $response");
        return false;
    }

    $write("MAIL FROM: <$from>");
    $read();
    $write("RCPT TO: <$to>");
    $read();
    $write("DATA");
    $read();

    $write("From: $name <$from>");
    $write("To: $to_name <$to>");
    $write("Subject: $subject");
    $write("MIME-Version: 1.0");
    $write("Content-Type: text/html; charset=UTF-8");
    $write("");
    $write($body);
    $write(".");
    $read();

    $write("QUIT");
    fclose($socket);

    return true;
}
