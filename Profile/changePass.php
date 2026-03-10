<?php
    session_start();

    require '../db.php';

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
        $_SESSION['message'] = "You must be logged in to change your password!";
        header("location: ../Login/error.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $currPass   = $_POST['currPass'];
        $newPass    = $_POST['newPass'];
        $conNewPass = $_POST['conNewPass'];
        $newHash    = md5(rand(0, 1000));
        $userId     = $_SESSION['id'];
        $category   = $_SESSION['Category'];

        // Determine table and column names based on user type
        if ($category == 1) {
            // Farmer
            $table      = 'farmer';
            $colId      = 'fid';
            $colPass    = 'fpassword';
            $colHash    = 'fhash';
        } else {
            // Buyer
            $table      = 'buyer';
            $colId      = 'bid';
            $colPass    = 'bpassword';
            $colHash    = 'bhash';
        }

        $sql    = "SELECT * FROM $table WHERE $colId = '$userId'";
        $result = mysqli_query($conn, $sql);

        if (!$result || mysqli_num_rows($result) == 0) {
            $_SESSION['message'] = "User not found!";
            header("location: ../Login/error.php");
            exit();
        }

        $User = $result->fetch_assoc();

        if (!password_verify($currPass, $User[$colPass])) {
            $_SESSION['message'] = "Current password is incorrect!";
            header("location: ../Login/error.php");
            exit();
        }

        if (strlen($newPass) < 6) {
            $_SESSION['message'] = "New password must be at least 6 characters!";
            header("location: ../Login/error.php");
            exit();
        }

        if ($newPass !== $conNewPass) {
            $_SESSION['message'] = "New passwords do not match!";
            header("location: ../Login/error.php");
            exit();
        }

        $hashedNew = password_hash($newPass, PASSWORD_BCRYPT);
        $sql       = "UPDATE $table SET $colPass='$hashedNew', $colHash='$newHash' WHERE $colId='$userId'";
        $result    = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['Hash']    = $newHash;
            $_SESSION['message'] = "Password changed successfully!";
            header("location: ../Login/success.php");
        } else {
            $_SESSION['message'] = "Error updating password. Please try again!";
            header("location: ../Login/error.php");
        }
        exit();
    } else {
        header("location: ../changePassPage.php");
        exit();
    }

    function dataFilter($data)
    {
    	$data = trim($data);
     	$data = stripslashes($data);
    	$data = htmlspecialchars($data);
      	return $data;
    }

?>
