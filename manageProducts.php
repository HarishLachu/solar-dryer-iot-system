<?php
session_start();

$conn = new mysqli("localhost", "root", "", "agroculture");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM fproduct ORDER BY pid DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f4f6f9;
            margin:0;
            padding:30px;
        }
        .container{
            max-width:1200px;
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
        .top-bar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }
        .btn{
            text-decoration:none;
            padding:10px 16px;
            border-radius:6px;
            color:#fff;
            font-size:14px;
            display:inline-block;
        }
        .btn-add{ background:#27ae60; }
        .btn-edit{ background:#2980b9; }
        .btn-delete{ background:#c0392b; }
        .btn-back{ background:#7f8c8d; }

        table{
            width:100%;
            border-collapse:collapse;
        }
        table th, table td{
            border:1px solid #ddd;
            padding:12px;
            text-align:left;
            vertical-align:middle;
        }
        table th{
            background:#2c3e50;
            color:#fff;
        }
        table tr:nth-child(even){
            background:#f9f9f9;
        }
        img{
            width:80px;
            height:80px;
            object-fit:cover;
            border-radius:6px;
            border:1px solid #ccc;
        }
        .status-active{
            color:green;
            font-weight:bold;
        }
        .status-inactive{
            color:red;
            font-weight:bold;
        }
        .actions a{
            margin-right:8px;
        }
        .msg{
            padding:12px;
            border-radius:6px;
            margin-bottom:15px;
        }
        .success{ background:#d4edda; color:#155724; }
        .error{ background:#f8d7da; color:#721c24; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <h2>Manage Products</h2>
        <div>
            <a href="uploadProduct.php" class="btn btn-add">+ Add New Product</a>
            <a href="productMenu.php" class="btn btn-back">View Product Menu</a>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] == 'updated'): ?>
            <div class="msg success">Product updated successfully.</div>
        <?php elseif ($_GET['msg'] == 'deleted'): ?>
            <div class="msg success">Product deleted successfully.</div>
        <?php elseif ($_GET['msg'] == 'error'): ?>
            <div class="msg error">Something went wrong.</div>
        <?php endif; ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>PID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
                <th>Status</th>
                <th width="180">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['pid']; ?></td>
                    <td><?php echo htmlspecialchars($row['product']); ?></td>
                    <td><?php echo htmlspecialchars($row['pcat']); ?></td>
                    <td><?php echo $row['pinfo']; ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td>
                        <?php
                        $imgPath = "images/productImages/" . $row['pimage'];
                        if (!empty($row['pimage']) && file_exists($imgPath)) {
                            echo '<img src="'.$imgPath.'" alt="Product Image">';
                        } else {
                            echo '<img src="images/productImages/blank.png" alt="No Image">';
                            
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row['picStatus'] == 1): ?>
                            <span class="status-active">Active</span>
                        <?php else: ?>
                            <span class="status-inactive">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a class="btn btn-edit" href="editProduct.php?pid=<?php echo $row['pid']; ?>">Edit</a>
                        <a class="btn btn-delete" href="deleteProduct.php?pid=<?php echo $row['pid']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No products found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php $conn->close(); ?>