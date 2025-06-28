<?php
require_once(__DIR__ . "/../config/config.php");

function sanitise_string($string) {
    $string = htmlentities($string, ENT_QUOTES, "UTF-8");
    return trim($string);
}


function register_product($name, $category, $sku, $description, $cost_price, $price, $quantity, $low_stock, $supplier, $image_file) {
    global $conn;

    // Sanitize string inputs
    $name        = sanitise_string($name);
    $category    = sanitise_string($category);
    $sku         = sanitise_string($sku);
    $description = sanitise_string($description);
    $supplier    = sanitise_string($supplier);
    $created_by  = $_SESSION['user_id'] ?? null;

    // Validate
    if (!$created_by) {
        $_SESSION['product-message'] = "Invalid session. Please log in.";
        return;
    }

    if (!$name || !$sku || $price <= 0 || $quantity < 0) {
        $_SESSION['produc-message'] = "Invalid product data. Please fill all required fields.";
        return;
    }

    // Check if SKU already exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['product-message'] = "SKU already exists.";
        $stmt->close();
        return;
    }
    $stmt->close();

    // Handle image upload
    $image_path = null;
    if (!empty($image_file['name'])) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $image_name = time() . "_" . basename($image_file["name"]);
        $image_path = $target_dir . $image_name;

        if (!move_uploaded_file($image_file["tmp_name"], $image_path)) {
            $_SESSION['produc-message'] = "Failed to upload image.";
            return;
        }
    }

    // Insert product
    $stmt = $conn->prepare("INSERT INTO products (name, category, sku, description, cost_price, price, quantity, low_stock, supplier, image, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssddiissi",
        $name, $category, $sku, $description,
        $cost_price, $price, $quantity, $low_stock,
        $supplier, $image_path, $created_by
    );

    if ($stmt->execute()) {
        $_SESSION['product-message'] = "Product registered successfully.";
    } else {
        $_SESSION['product-message'] = "Database error: " . $stmt->error;
    }

    $stmt->close();
}

?>