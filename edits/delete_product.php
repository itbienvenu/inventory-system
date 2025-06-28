<?php
session_start();
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/auth.php');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['product-message'] = "Access denied. Please login.";
    header("Location: ../login.php");
    exit;
}

// Check if product ID is passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['product-message'] = "Invalid product ID.";
    header("Location: products.php");
    exit;
}

$product_id = (int) $_GET['id'];

// Optional: fetch product and delete its image
$query = mysqli_prepare($conn, "SELECT image FROM products WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $product_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
    if (!empty($product['image']) && file_exists($product['image'])) {
        unlink($product['image']); // delete the image file
    }
}

// Delete product
$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);

if ($stmt->execute()) {
    $_SESSION['product-message'] = "Product deleted successfully.";
} else {
    $_SESSION['product-message'] = "Failed to delete product.";
}

header("Location: ../executive/products.php#products-table");
exit;
