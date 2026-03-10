<?php
    session_start();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
        $_SESSION['message'] = "You have to Login to view this page!";
        header("Location: Login/error.php");
        exit();
    }
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Edit Profile — <?php echo htmlspecialchars($_SESSION['Username']); ?></title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="login.css"/>
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <link rel="stylesheet" href="css/skel.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/style-xlarge.css" />
    <style>
        .edit-card {
            max-width: 680px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            padding: 35px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid #eee;
        }
        .profile-header img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #4CAF50;
            margin-bottom: 12px;
        }
        .profile-header h3 { margin: 6px 0 2px; color: #333; font-size: 20px; }
        .profile-header span { color: #888; font-size: 13px; }
        .section-title { font-size: 14px; font-weight: 700; color: #555; margin: 18px 0 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control {
            border-radius: 6px;
            border: 1px solid #ccc;
            padding: 10px 14px;
            font-size: 14px;
            height: auto;
            color: #222 !important;
            background-color: #fff !important;
            -webkit-text-fill-color: #222 !important;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76,175,80,0.2);
            color: #222 !important;
            background-color: #fff !important;
        }
        .form-control::placeholder { color: #aaa !important; opacity: 1; }
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #222 !important;
            -webkit-box-shadow: 0 0 0px 1000px #fff inset !important;
        }
        textarea.form-control { resize: vertical; color: #222 !important; background-color: #fff !important; }
        .form-group label { font-weight: 600; color: #444; font-size: 13px; }
        .btn-update { background: #4CAF50; color: #fff; border: none; border-radius: 6px; padding: 11px 28px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .btn-update:hover { background: #388E3C; }
        .btn-cancel { background: #f5f5f5; color: #555; border: 1px solid #ddd; border-radius: 6px; padding: 11px 22px; font-size: 15px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-cancel:hover { background: #eee; color: #333; }
        .alert-success { background: #E8F5E9; color: #2E7D32; border-left: 4px solid #4CAF50; padding: 10px 14px; border-radius: 6px; margin-bottom: 18px; font-size: 13px; }
        .alert-error   { background: #FFEBEE; color: #c62828; border-left: 4px solid #e53935; padding: 10px 14px; border-radius: 6px; margin-bottom: 18px; font-size: 13px; }
        .char-count { font-size: 11px; color: #aaa; text-align: right; margin-top: 2px; }
    </style>
</head>
<body>
    <?php require 'menu.php'; ?>

    <section id="one" class="wrapper style1">
        <div class="inner">
            <div class="edit-card">

                <?php if (isset($_SESSION['message']) && !empty($_SESSION['message'])): ?>
                    <div class="alert-success"><?php echo $_SESSION['message']; $_SESSION['message'] = ''; ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['edit_error']) && !empty($_SESSION['edit_error'])): ?>
                    <div class="alert-error"><?php echo $_SESSION['edit_error']; $_SESSION['edit_error'] = ''; ?></div>
                <?php endif; ?>

                <!-- Profile Header -->
                <div class="profile-header">
                    <img src="<?php echo 'images/profileImages/'.$_SESSION['picName'].'?'.mt_rand(); ?>" alt="Profile Picture" />
                    <h3><?php echo htmlspecialchars($_SESSION['Name']); ?></h3>
                    <span>@<?php echo htmlspecialchars($_SESSION['Username']); ?> &bull;
                        <?php echo ($_SESSION['Category'] == 1) ? 'Farmer' : 'Buyer'; ?>
                    </span>
                </div>

                <!-- Profile Info Update -->
                <form method="post" action="Profile/updateProfile.php" id="profileForm">
                    <div class="section-title">Personal Information</div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="<?php echo htmlspecialchars($_SESSION['Name']); ?>"
                                    placeholder="Full Name" maxlength="100" required />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="uname" id="uname" class="form-control"
                                    value="<?php echo htmlspecialchars($_SESSION['Username']); ?>"
                                    placeholder="Username" maxlength="50" required />
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:14px;">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="<?php echo htmlspecialchars($_SESSION['Email']); ?>"
                                    placeholder="Email Address" required />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Mobile Number</label>
                                <input type="text" name="mobile" id="mobile" class="form-control"
                                    value="<?php echo htmlspecialchars($_SESSION['Mobile']); ?>"
                                    placeholder="10-digit mobile number" maxlength="10" required />
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:14px;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="addr" id="addr" class="form-control" rows="3"
                                    placeholder="Your address" maxlength="255" oninput="updateCharCount(this,'addrCount',255)"
                                    ><?php echo htmlspecialchars($_SESSION['Addr']); ?></textarea>
                                <div class="char-count" id="addrCount"></div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:24px; display:flex; gap:12px; justify-content:center;">
                        <button type="submit" class="btn-update">💾 Update Profile</button>
                        <a href="profileView.php" class="btn-cancel">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </section>

    <script>
        function updateCharCount(el, countId, max) {
            var remaining = max - el.value.length;
            var countEl = document.getElementById(countId);
            countEl.textContent = remaining + ' characters remaining';
            countEl.style.color = remaining < 30 ? '#e53935' : '#aaa';
        }

        // Init char count
        updateCharCount(document.getElementById('addr'), 'addrCount', 255);

        // Mobile number: digits only
        document.getElementById('mobile').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            var mobile = document.getElementById('mobile').value;
            if (mobile.length !== 10) {
                e.preventDefault();
                alert('Mobile number must be exactly 10 digits.');
            }
        });
    </script>
</body>
</html>
