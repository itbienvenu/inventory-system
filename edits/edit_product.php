<?php
include_once(__DIR__ . "/../config/auth.php");
include_once(__DIR__ . "/../config/config.php");

// Ensure user is logged in
if (!isset($_SESSION['role']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid product ID.";
    exit;
}

$product_id = (int) $_GET['id'];
$message = "";

// Fetch product from database
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Product not found.";
    exit;
}

$product = mysqli_fetch_assoc($result);

// Update product if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $category = htmlspecialchars(trim($_POST['category']));
    $sku = htmlspecialchars(trim($_POST['sku']));
    $description = htmlspecialchars(trim($_POST['description']));
    $cost_price = floatval($_POST['cost_price']);
    $price = floatval($_POST['price']);
    $quantity = (int) $_POST['quantity'];
    $low_stock = (int) $_POST['low_stock'];
    $supplier = htmlspecialchars(trim($_POST['supplier']));

    // Image update (optional)
    $image = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = '../uploads/';
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $upload_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image = $upload_path;
        }
    }

    // Update query
    $update_stmt = mysqli_prepare($conn, "UPDATE products SET name=?, category=?, sku=?, description=?, cost_price=?, price=?, quantity=?, low_stock=?, supplier=?, image=? WHERE id=?");
    mysqli_stmt_bind_param($update_stmt, "ssssddiissi", $name, $category, $sku, $description, $cost_price, $price, $quantity, $low_stock, $supplier, $image, $product_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $message = "<div class='alert alert-success'>Product updated successfully.</div>";
        // Refresh the product data
        $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
    } else {
        $message = "<div class='alert alert-danger'>Failed to update product. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product</title>
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Product</h2>
        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($product['category']); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Supplier</label>
                    <input type="text" name="supplier" class="form-control" value="<?php echo htmlspecialchars($product['supplier']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Cost Price</label>
                    <input type="number" name="cost_price" step="0.01" class="form-control" value="<?php echo $product['cost_price']; ?>">
                </div>
                <div class="form-group col-md-4">
                    <label>Selling Price</label>
                    <input type="number" name="price" step="0.01" class="form-control" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="<?php echo $product['quantity']; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Low Stock Alert</label>
                    <input type="number" name="low_stock" class="form-control" value="<?php echo $product['low_stock']; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label>Product Image</label><br>
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" width="100" alt="Current Image"><br>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control-file">
                </div>
            </div>

            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>

            <a href="<?php 
                if($_SESSION['role'] == "executive"){
                    echo "../executive/table.php#products-table";
                } 
            ?>" class="btn btn-secondary">Back to List</a>
        </form>
    </div>
</body>
</html>
