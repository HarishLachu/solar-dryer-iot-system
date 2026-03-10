<?php
session_start();
require '../db.php';

if (!isset($_SESSION['otp_email']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("location: forgotPassword.php");
    exit();
}

$email    = $_SESSION['otp_email'];
$category = intval($_SESSION['otp_category']);
$otp      = trim($_POST['otp']);

if (strlen($otp) !== 6 || !ctype_digit($otp)) {
    $_SESSION['otp_error'] = "Invalid OTP format. Please enter the 6-digit code.";
    header("location: verifyOTP.php");
    exit();
}

// Check OTP in database
$now = date('Y-m-d H:i:s');
$sql = "SELECT * FROM password_reset_otp
        WHERE email = '$email'
          AND category = $category
          AND otp = '$otp'
          AND used = 0
          AND expires_at > '$now'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    // Check if OTP exists but expired
    $checkExpired = mysqli_query($conn,
        "SELECT * FROM password_reset_otp WHERE email='$email' AND otp='$otp' AND used=0 LIMIT 1");
    if ($checkExpired && mysqli_num_rows($checkExpired) > 0) {
        $_SESSION['otp_error'] = "OTP has expired. Please request a new one.";
    } else {
        $_SESSION['otp_error'] = "Invalid OTP. Please check and try again.";
    }
    header("location: verifyOTP.php");
    exit();
}

// Mark OTP as used
mysqli_query($conn, "UPDATE password_reset_otp SET used=1 WHERE email='$email' AND category=$category AND otp='$otp'");

// Allow password reset
$_SESSION['reset_allowed']  = true;
$_SESSION['reset_email']    = $email;
$_SESSION['reset_category'] = $category;

// Clear OTP session details
unset($_SESSION['otp_email'], $_SESSION['otp_category']);

header("location: resetPassword.php");
exit();
