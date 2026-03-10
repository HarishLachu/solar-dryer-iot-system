<?php
session_start();

if (!isset($_SESSION['reset_allowed']) || $_SESSION['reset_allowed'] !== true) {
    $_SESSION['fp_error'] = "Please complete OTP verification first.";
    header("location: forgotPassword.php");
    exit();
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Reset Password — AgroCulture</title>
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
        .rp-card { max-width: 440px; margin: 80px auto; background: #fff; border-radius: 10px; padding: 40px 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
        .rp-card h2 { color: #333; font-size: 24px; margin-bottom: 6px; text-align:center; }
        .rp-card .sub { color: #888; font-size: 13px; text-align: center; margin-bottom: 24px; }
        .form-group label { font-weight: 600; color: #444; font-size: 13px; margin-bottom: 5px; display: block; }
        .form-control { border-radius: 6px; border: 1px solid #ddd; padding: 10px 14px; font-size: 14px; height: auto; width: 100%; }
        .form-control:focus { border-color: #4CAF50; box-shadow: 0 0 0 2px rgba(76,175,80,0.2); outline: none; }
        .btn-reset { background: #4CAF50; color: #fff; border: none; border-radius: 6px; padding: 11px 0; font-size: 15px; font-weight: 600; width: 100%; cursor: pointer; margin-top: 8px; }
        .btn-reset:hover { background: #388E3C; }
        .alert-msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 18px; font-size: 13px; }
        .alert-error { background: #FFEBEE; color: #c62828; border-left: 4px solid #e53935; }
        .password-strength { height: 5px; border-radius: 3px; margin-top: 5px; background: #eee; transition: all 0.3s; }
        .strength-weak   { background: #e53935; width: 33%; }
        .strength-medium { background: #FB8C00; width: 66%; }
        .strength-strong { background: #43A047; width: 100%; }
        .eye-toggle { cursor: pointer; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #888; font-size: 15px; }
        .input-wrapper { position: relative; }
    </style>
</head>
<body>
    <?php require 'menu.php'; ?>

    <section id="one" class="wrapper style1">
        <div class="inner">
            <div class="rp-card">
                <div style="font-size:40px; text-align:center; margin-bottom:10px;">🔑</div>
                <h2>Set New Password</h2>
                <p class="sub">OTP verified! Create a strong new password for your account.</p>

                <?php if (isset($_SESSION['rp_error']) && !empty($_SESSION['rp_error'])): ?>
                    <div class="alert-msg alert-error"><?php echo $_SESSION['rp_error']; $_SESSION['rp_error'] = ''; ?></div>
                <?php endif; ?>

                <form action="resetPasswordProcess.php" method="POST" id="resetForm">
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="newPass" id="newPass" class="form-control" placeholder="Min. 6 characters" required oninput="checkStrength(this.value)" />
                            <span class="eye-toggle" onclick="togglePass('newPass', this)">👁</span>
                        </div>
                        <div class="password-strength" id="strengthBar"></div>
                        <div id="strengthLabel" style="font-size:11px; margin-top:2px;"></div>
                    </div>

                    <div class="form-group" style="margin-top:14px">
                        <label>Confirm New Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="conNewPass" id="conNewPass" class="form-control" placeholder="Retype new password" required oninput="checkMatch()" />
                            <span class="eye-toggle" onclick="togglePass('conNewPass', this)">👁</span>
                        </div>
                        <div id="matchMsg" style="font-size:12px; margin-top:3px;"></div>
                    </div>

                    <button type="submit" class="btn-reset" id="submitBtn">Reset Password</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        function togglePass(id, icon) {
            var f = document.getElementById(id);
            f.type = (f.type === 'password') ? 'text' : 'password';
            icon.textContent = (f.type === 'password') ? '👁' : '🙈';
        }
        function checkStrength(val) {
            var bar = document.getElementById('strengthBar');
            var lbl = document.getElementById('strengthLabel');
            bar.className = 'password-strength';
            if (!val) { lbl.textContent = ''; return; }
            var score = 0;
            if (val.length >= 6) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            if (score <= 1) { bar.classList.add('strength-weak');   lbl.textContent = 'Weak';   lbl.style.color = '#e53935'; }
            else if (score == 2) { bar.classList.add('strength-medium'); lbl.textContent = 'Medium'; lbl.style.color = '#FB8C00'; }
            else { bar.classList.add('strength-strong'); lbl.textContent = 'Strong'; lbl.style.color = '#43A047'; }
        }
        function checkMatch() {
            var a = document.getElementById('newPass').value;
            var b = document.getElementById('conNewPass').value;
            var msg = document.getElementById('matchMsg');
            var btn = document.getElementById('submitBtn');
            if (!b) { msg.textContent = ''; return; }
            if (a === b) { msg.textContent = '✓ Passwords match'; msg.style.color = '#43A047'; btn.disabled = false; }
            else         { msg.textContent = '✗ Passwords do not match'; msg.style.color = '#e53935'; btn.disabled = true; }
        }
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            var np = document.getElementById('newPass').value;
            var cp = document.getElementById('conNewPass').value;
            if (np.length < 6) { e.preventDefault(); alert('Password must be at least 6 characters.'); }
            else if (np !== cp) { e.preventDefault(); alert('Passwords do not match.'); }
        });
    </script>
</body>
</html>
