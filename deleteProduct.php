<?php
session_start();

$conn = new mysqli("localhost", "root", "", "agroculture");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_GET['pid']) || empty($_GET['pid'])) {
    header("Location: manageProducts.php?msg=error");
    exit();
}

$pid = (int)$_GET['pid'];

$query = $conn->query("SELECT * FROM fproduct WHERE pid = $pid");
if (!$query || $query->num_rows == 0) {
    header("Location: manageProducts.php?msg=error");
    exit();
}

$product = $query->fetch_assoc();

/* delete image file if exists and not blank.png */
if (!empty($product['pimage']) && $product['pimage'] != 'blank.png') {
    $imgPath = "images/productImages/" . $product['pimage'];
    if (file_exists($imgPath)) {
        unlink($imgPath);
    }
}

/* delete DB row */
$delete = $conn->query("DELETE FROM fproduct WHERE pid = $pid");

if ($delete) {
    header("Location: manageProducts.php?msg=deleted");
    exit();
} else {
    header("Location: manageProducts.php?msg=error");
    exit();
}

$conn->close();
?>