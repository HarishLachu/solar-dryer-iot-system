<?php
session_start();
require '../db.php';

if (!isset($_SESSION['reset_allowed']) || $_SESSION['reset_allowed'] !== true) {
    $_SESSION['fp_error'] = "Unauthorized. Please restart the password reset process.";
    header("location: forgotPassword.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("location: resetPassword.php");
    exit();
}

$email    = $_SESSION['reset_email'];
$category = intval($_SESSION['reset_category']);
$newPass  = $_POST['newPass'];
$conPass  = $_POST['conNewPass'];

if (strlen($newPass) < 6) {
    $_SESSION['rp_error'] = "Password must be at least 6 characters.";
    header("location: resetPassword.php");
    exit();
}

if ($newPass !== $conPass) {
    $_SESSION['rp_error'] = "Passwords do not match.";
    header("location: resetPassword.php");
    exit();
}

$hashed  = password_hash($newPass, PASSWORD_BCRYPT);
$newHash = md5(rand(0, 1000));

if ($category == 1) {
    $sql = "UPDATE farmer SET fpassword='$hashed', fhash='$newHash' WHERE femail='$email'";
} else {
    $sql = "UPDATE buyer SET bpassword='$hashed', bhash='$newHash' WHERE bemail='$email'";
}

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_affected_rows($conn) == 0) {
    $_SESSION['rp_error'] = "Failed to update password. Please try again.";
    header("location: resetPassword.php");
    exit();
}

// Clean up all OTP records for this email
mysqli_query($conn, "DELETE FROM password_reset_otp WHERE email='$email' AND category=$category");

// Clear all reset-related session data
unset($_SESSION['reset_allowed'], $_SESSION['reset_email'], $_SESSION['reset_category'], $_SESSION['otp_name']);

$_SESSION['message'] = "Password reset successfully! You can now log in with your new password.";
header("location: success.php");
exit();
