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

$productQuery = $conn->query("SELECT * FROM fproduct WHERE pid = $pid");
if (!$productQuery || $productQuery->num_rows == 0) {
    header("Location: manageProducts.php?msg=error");
    exit();
}

$product = $productQuery->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $conn->real_escape_string($_POST['product']);
    $cat   = $conn->real_escape_string($_POST['pcat']);
    $info  = $conn->real_escape_string($_POST['pinfo']);
    $price = $conn->real_escape_string($_POST['price']);

    $imageName = $product['pimage'];
    $picStatus = $product['picStatus'];

    if (isset($_FILES['pimage']) && $_FILES['pimage']['error'] == 0) {
        $targetDir = "images/productImages/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $newFileName = time() . "_" . basename($_FILES['pimage']['name']);
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($_FILES['pimage']['tmp_name'], $targetFile)) {
            if (!empty($product['pimage']) && $product['pimage'] != 'blank.png' && file_exists("images/" . $product['pimage'])) {
                unlink("images/productImages/" . $product['pimage']);
            }
            $imageName = $newFileName;
            $picStatus = 1;
        }
    }

    $sql = "UPDATE fproduct 
            SET product='$name', pcat='$cat', pinfo='$info', price='$price', pimage='$imageName', picStatus='$picStatus'
            WHERE pid=$pid";

    if ($conn->query($sql)) {
        header("Location: manageProducts.php?msg=updated");
        exit();
    } else {
        $error = "Update failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f4f6f9;
            margin:0;
            padding:30px;
        }
        .container{
            max-width:800px;
            margin:auto;
            background:#fff;
            padding:25px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
        }
        h2{
            margin-top:0;
            color:#2c3e50;
        }
        label{
            font-weight:bold;
            display:block;
            margin-top:15px;
            margin-bottom:6px;
        }
        input[type="text"], input[type="number"], select, textarea{
            width:100%;
            padding:10px;
            border:1px solid #ccc;
            border-radius:6px;
            font-size:14px;
        }
        textarea{
            min-height:120px;
        }
        .btn{
            margin-top:20px;
            padding:10px 18px;
            border:none;
            border-radius:6px;
            color:#fff;
            cursor:pointer;
            font-size:14px;
        }
        .btn-save{ background:#27ae60; }
        .btn-back{ background:#7f8c8d; text-decoration:none; display:inline-block; margin-left:10px; }
        img{
            margin-top:10px;
            width:120px;
            height:120px;
            object-fit:cover;
            border:1px solid #ccc;
            border-radius:6px;
        }
        .error{
            background:#f8d7da;
            color:#721c24;
            padding:10px;
            border-radius:6px;
            margin-bottom:15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Product</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Name</label>
        <input type="text" name="product" value="<?php echo htmlspecialchars($product['product']); ?>" required>

        <label>Category</label>
        <select name="pcat" required>
            <option value="Fruit" <?php if($product['pcat']=="Fruit") echo "selected"; ?>>Fruit</option>
            <option value="Vegetable" <?php if($product['pcat']=="Vegetable") echo "selected"; ?>>Vegetable</option>
            <option value="Grains" <?php if($product['pcat']=="Grains") echo "selected"; ?>>Grains</option>
            <option value="Other" <?php if($product['pcat']=="Other") echo "selected"; ?>>Other</option>
        </select>

        <label>Description</label>
        <textarea name="pinfo" required><?php echo htmlspecialchars(strip_tags($product['pinfo'])); ?></textarea>

        <label>Price</label>
        <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label>Current Image</label>
        <?php
        $imgPath = "images/productImages/" . $product['pimage'];
        if (!empty($product['pimage']) && file_exists($imgPath)) {
            echo '<img src="'.$imgPath.'" alt="Current Image">';
        } else {
            echo '<img src="images/blank.png" alt="No Image">';
        }
        ?>

        <label>Change Image</label>
        <input type="file" name="pimage" accept="image/*">

        <button type="submit" class="btn btn-save">Update Product</button>
        <a href="manageProducts.php" class="btn btn-back">Back</a>
    </form>
</div>
</body>
</html>
<?php $conn->close(); ?>