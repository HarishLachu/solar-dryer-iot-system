<?php
session_start();
require_once __DIR__ . '/mail_config.php';

if (!isset($_SESSION['otp_email'])) {
    header("location: forgotPassword.php");
    exit();
}

$email = $_SESSION['otp_email'];
$maskedEmail = '';
$parts = explode('@', $email);
if (count($parts) == 2) {
    $local  = $parts[0];
    $domain = $parts[1];
    $visible = min(3, strlen($local));
    $maskedEmail = substr($local, 0, $visible) . str_repeat('*', max(0, strlen($local) - $visible)) . '@' . $domain;
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Verify OTP — AgroCulture</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../login.css"/>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/skel.min.js"></script>
    <script src="../js/skel-layers.min.js"></script>
    <script src="../js/init.js"></script>
    <link rel="stylesheet" href="../css/skel.css"/>
    <link rel="stylesheet" href="../css/style.css"/>
    <link rel="stylesheet" href="../css/style-xlarge.css"/>
    <style>
        .otp-card { max-width: 420px; margin: 80px auto; background: #fff; border-radius: 10px; padding: 40px 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); text-align: center; }
        .otp-card h2 { color: #333; font-size: 24px; margin-bottom: 8px; }
        .otp-card .sub { color: #888; font-size: 13px; margin-bottom: 24px; line-height: 1.6; }
        .otp-inputs { display: flex; gap: 10px; justify-content: center; margin: 20px 0; }
        .otp-inputs input {
            width: 48px; height: 56px; text-align: center; font-size: 22px; font-weight: 700;
            border: 2px solid #ddd; border-radius: 8px; outline: none;
        }
        .otp-inputs input:focus { border-color: #4CAF50; box-shadow: 0 0 0 2px rgba(76,175,80,0.2); }
        .btn-verify { background: #4CAF50; color: #fff; border: none; border-radius: 6px; padding: 11px 0; font-size: 15px; font-weight: 600; width: 100%; cursor: pointer; }
        .btn-verify:hover { background: #388E3C; }
        .back-link { display: block; margin-top: 16px; color: #888; font-size: 13px; text-decoration: none; }
        .back-link:hover { color: #333; text-decoration: underline; }
        .alert-msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 18px; font-size: 13px; text-align: left; }
        .alert-error { background: #FFEBEE; color: #c62828; border-left: 4px solid #e53935; }
        #countdown { font-size: 13px; color: #888; margin-top: 10px; }
        #countdown span { font-weight: bold; color: #333; }
        .resend-link { color: #4CAF50; cursor: pointer; font-size: 13px; display: none; margin-top: 8px; }
        .resend-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php require 'menu.php'; ?>

    <section id="one" class="wrapper style1">
        <div class="inner">
            <div class="otp-card">
                <div style="font-size:40px; margin-bottom:10px;">🔢</div>
                <h2>Enter OTP</h2>
                <p class="sub">
                    We sent a 6-digit code to<br>
                    <strong><?php echo htmlspecialchars($maskedEmail); ?></strong><br>
                    It expires in <strong><?php echo OTP_EXPIRY_MINUTES ?? 10; ?> minutes</strong>.
                </p>

                <?php if (isset($_SESSION['otp_error']) && !empty($_SESSION['otp_error'])): ?>
                    <div class="alert-msg alert-error"><?php echo $_SESSION['otp_error']; $_SESSION['otp_error'] = ''; ?></div>
                <?php endif; ?>

                <form action="verifyOTPProcess.php" method="POST" id="otpForm">
                    <div class="otp-inputs">
                        <input type="tel" maxlength="1" class="otp-digit" data-index="0" />
                        <input type="tel" maxlength="1" class="otp-digit" data-index="1" />
                        <input type="tel" maxlength="1" class="otp-digit" data-index="2" />
                        <input type="tel" maxlength="1" class="otp-digit" data-index="3" />
                        <input type="tel" maxlength="1" class="otp-digit" data-index="4" />
                        <input type="tel" maxlength="1" class="otp-digit" data-index="5" />
                    </div>
                    <input type="hidden" name="otp" id="otpHidden" />
                    <button type="submit" class="btn-verify" id="verifyBtn" disabled>Verify OTP</button>
                </form>

                <div id="countdown">OTP expires in <span id="timer"><?php echo (OTP_EXPIRY_MINUTES ?? 10) * 60; ?></span>s</div>
                <a class="resend-link" id="resendLink" href="forgotPassword.php">Resend OTP</a>
                <a href="forgotPassword.php" class="back-link">← Change email</a>
            </div>
        </div>
    </section>

    <script>
        // OTP digit navigation
        var digits = document.querySelectorAll('.otp-digit');
        var otpHidden = document.getElementById('otpHidden');
        var verifyBtn = document.getElementById('verifyBtn');

        digits.forEach(function(input, index) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value && index < 5) digits[index + 1].focus();
                updateHidden();
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) digits[index - 1].focus();
            });
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                var paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                paste.split('').forEach(function(ch, i) { if (digits[i]) digits[i].value = ch; });
                updateHidden();
                if (digits[paste.length - 1]) digits[Math.min(paste.length, 5)].focus();
            });
        });

        function updateHidden() {
            var val = Array.from(digits).map(function(d) { return d.value; }).join('');
            otpHidden.value = val;
            verifyBtn.disabled = (val.length < 6);
        }

        // Countdown timer
        var seconds = parseInt(document.getElementById('timer').textContent);
        var interval = setInterval(function() {
            seconds--;
            document.getElementById('timer').textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                document.getElementById('countdown').innerHTML = '<span style="color:#e53935">OTP has expired.</span>';
                document.getElementById('resendLink').style.display = 'inline';
                verifyBtn.disabled = true;
            }
        }, 1000);
    </script>
<style>
/* Last-loaded override — beats .wrapper.style1 { color:#fff } */
.otp-inputs input[type="tel"] {
    color: #222 !important;
    -webkit-text-fill-color: #222 !important;
    background-color: #ffffff !important;
    font-size: 22px !important;
    font-weight: 700 !important;
    text-align: center !important;
    width: 48px !important;
    height: 56px !important;
    padding: 0 !important;
}
</style>
</body>
</html>
