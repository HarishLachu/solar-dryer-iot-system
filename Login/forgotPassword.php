<?php session_start(); ?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Forgot Password — AgroCulture</title>
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
        .fp-card { max-width: 440px; margin: 80px auto; background: #fff; border-radius: 10px; padding: 40px 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
        .fp-card h2 { color: #333; font-size: 24px; margin-bottom: 6px; }
        .fp-card p.sub { color: #888; font-size: 13px; margin-bottom: 24px; line-height: 1.6; }
        .form-control { border-radius: 6px; border: 1px solid #ddd; padding: 10px 14px; font-size: 14px; height: auto; width: 100%; }
        .form-control:focus { border-color: #4CAF50; box-shadow: 0 0 0 2px rgba(76,175,80,0.2); outline: none; }
        .form-group label { font-weight: 600; color: #444; font-size: 13px; margin-bottom: 5px; display: block; }
        .btn-submit { background: #4CAF50; color: #fff; border: none; border-radius: 6px; padding: 11px 0; font-size: 15px; font-weight: 600; width: 100%; cursor: pointer; margin-top: 6px; }
        .btn-submit:hover { background: #388E3C; }
        .back-link { display: block; text-align: center; margin-top: 16px; color: #888; font-size: 13px; text-decoration: none; }
        .back-link:hover { color: #333; text-decoration: underline; }
        .alert-msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 18px; font-size: 13px; }
        .alert-error { background: #FFEBEE; color: #c62828; border-left: 4px solid #e53935; }
        .icon-mail { font-size: 40px; text-align: center; display: block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <?php require 'menu.php'; ?>

    <section id="one" class="wrapper style1">
        <div class="inner">
            <div class="fp-card">
                <span class="icon-mail">📧</span>
                <h2 style="text-align:center">Forgot Password?</h2>
                <p class="sub" style="text-align:center">Enter your registered email address. We'll send you a 6-digit OTP to reset your password.</p>

                <?php if (isset($_SESSION['fp_error']) && !empty($_SESSION['fp_error'])): ?>
                    <div class="alert-msg alert-error"><?php echo $_SESSION['fp_error']; $_SESSION['fp_error'] = ''; ?></div>
                <?php endif; ?>

                <form action="forgotPasswordProcess.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" required />
                    </div>
                    <div class="form-group" style="margin-top:12px">
                        <label>Account Type</label>
                        <div style="display:flex; gap:24px; margin-top:5px;">
                            <label style="font-weight:400; cursor:pointer;">
                                <input type="radio" name="category" value="1" checked> Farmer
                            </label>
                            <label style="font-weight:400; cursor:pointer;">
                                <input type="radio" name="category" value="0"> Buyer
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Send OTP</button>
                </form>

                <a href="../index.php" class="back-link">← Back to Login</a>
            </div>
        </div>
    </section>
</body>
</html>
