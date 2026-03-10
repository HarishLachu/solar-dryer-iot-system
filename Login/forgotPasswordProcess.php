<?php
session_start();
require '../db.php';
require 'smtp_mailer.php';

function dataFilter($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Auto-create OTP table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS password_reset_otp (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(255) NOT NULL,
    category    TINYINT NOT NULL COMMENT '1=farmer 0=buyer',
    otp         VARCHAR(6) NOT NULL,
    expires_at  DATETIME NOT NULL,
    used        TINYINT DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
mysqli_query($conn, $createTable);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("location: forgotPassword.php");
    exit();
}

$email    = strtolower(dataFilter($_POST['email']));
$category = intval($_POST['category']); // 1=farmer, 0=buyer

// Look up the user
if ($category == 1) {
    $sql = "SELECT fname, femail FROM farmer WHERE femail = '$email'";
} else {
    $sql = "SELECT bname, bemail FROM buyer WHERE bemail = '$email'";
}

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['fp_error'] = "No account found with that email address for the selected account type.";
    header("location: forgotPassword.php");
    exit();
}

$user = $result->fetch_assoc();
$name = ($category == 1) ? $user['fname'] : $user['bname'];

// Delete any old unused OTPs for this email
mysqli_query($conn, "DELETE FROM password_reset_otp WHERE email='$email' AND category=$category");

// Generate 6-digit OTP
$otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
$expires = date('Y-m-d H:i:s', time() + (OTP_EXPIRY_MINUTES * 60));

// Store OTP in DB
$sql = "INSERT INTO password_reset_otp (email, category, otp, expires_at) VALUES ('$email', $category, '$otp', '$expires')";
if (!mysqli_query($conn, $sql)) {
    $_SESSION['fp_error'] = "Database error. Please try again.";
    header("location: forgotPassword.php");
    exit();
}

// Send OTP email
$sent = sendOTPEmail($email, $name, $otp);

if (!$sent) {
    // Remove the stored OTP if email failed
    mysqli_query($conn, "DELETE FROM password_reset_otp WHERE email='$email' AND category=$category AND otp='$otp'");
    $_SESSION['fp_error'] = "Failed to send email. Please check mail configuration in Login/mail_config.php.";
    header("location: forgotPassword.php");
    exit();
}

// Save to session for OTP verification page
$_SESSION['otp_email']    = $email;
$_SESSION['otp_category'] = $category;
$_SESSION['otp_name']     = $name;

header("location: verifyOTP.php");
exit();
