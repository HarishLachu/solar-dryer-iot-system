<?php
    session_start();
    require '../db.php';

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
        $_SESSION['message'] = "You must be logged in!";
        header("Location: ../Login/error.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: ../profileEdit.php");
        exit();
    }

    $name   = dataFilter($_POST['name']);
    $mobile = dataFilter($_POST['mobile']);
    $user   = dataFilter($_POST['uname']);
    $email  = dataFilter($_POST['email']);
    $addr   = dataFilter($_POST['addr']);
    $id     = $_SESSION['id'];
    $category = $_SESSION['Category'];

    // Validate mobile
    if (strlen($mobile) !== 10 || !ctype_digit($mobile)) {
        $_SESSION['edit_error'] = "Invalid mobile number. Must be exactly 10 digits.";
        header("Location: ../profileEdit.php");
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['edit_error'] = "Invalid email address.";
        header("Location: ../profileEdit.php");
        exit();
    }

    if ($category == 1) {
        // Farmer
        $checkSql = "SELECT fid FROM farmer WHERE fusername='$user' AND fid != '$id'";
        $emailCheckSql = "SELECT fid FROM farmer WHERE femail='$email' AND fid != '$id'";
        $updateSql = "UPDATE farmer SET fname='$name', fusername='$user', fmobile='$mobile', femail='$email', faddress='$addr' WHERE fid='$id'";
    } else {
        // Buyer
        $checkSql = "SELECT bid FROM buyer WHERE busername='$user' AND bid != '$id'";
        $emailCheckSql = "SELECT bid FROM buyer WHERE bemail='$email' AND bid != '$id'";
        $updateSql = "UPDATE buyer SET bname='$name', busername='$user', bmobile='$mobile', bemail='$email', baddress='$addr' WHERE bid='$id'";
    }

    // Check username uniqueness
    $checkResult = mysqli_query($conn, $checkSql);
    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $_SESSION['edit_error'] = "Username '$user' is already taken. Please choose another.";
        header("Location: ../profileEdit.php");
        exit();
    }

    // Check email uniqueness
    $emailResult = mysqli_query($conn, $emailCheckSql);
    if ($emailResult && mysqli_num_rows($emailResult) > 0) {
        $_SESSION['edit_error'] = "Email '$email' is already registered to another account.";
        header("Location: ../profileEdit.php");
        exit();
    }

    $result = mysqli_query($conn, $updateSql);

    if ($result) {
        // Update session
        $_SESSION['Name']     = $name;
        $_SESSION['Username'] = $user;
        $_SESSION['Mobile']   = $mobile;
        $_SESSION['Email']    = $email;
        $_SESSION['Addr']     = $addr;

        $_SESSION['message'] = "Profile updated successfully!";
        header("Location: ../profileView.php");
    } else {
        $_SESSION['edit_error'] = "Error updating profile: " . mysqli_error($conn);
        header("Location: ../profileEdit.php");
    }
    exit();

function dataFilter($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>
