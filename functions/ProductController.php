<?php
require_once(__DIR__ . "/../config/config.php");

// Ensure session is started if not already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

    if (empty($name) || empty($sku) || $price <= 0 || $quantity < 0) {
        $_SESSION['product-message'] = "Invalid product data. Please fill all required fields correctly.";
        return;
    }

    // Check if SKU already exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['product-message'] = "SKU already exists. Please use a unique SKU.";
        $stmt->close();
        return;
    }
    $stmt->close();

    // Handle image upload
    $image_filename = null; // Will store just the filename, not the full path
    $target_dir = "../uploads/"; // Changed to 'products' subdirectory for organization
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Use 0777 for testing, adjust to 0755 in production
    }

    if (isset($image_file) && $image_file['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $image_file['tmp_name'];
        $file_ext = strtolower(pathinfo($image_file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpeg", "jpg", "png", "gif");

        if (in_array($file_ext, $allowed_extensions)) {
            $image_filename = uniqid('product_') . '.' . $file_ext; // Generate unique name
            $target_file = $target_dir . $image_filename;

            if (!move_uploaded_file($file_tmp, $target_file)) {
                $_SESSION['product-message'] = "Failed to upload image. Product not registered.";
                return;
            }
        } else {
            $_SESSION['product-message'] = "Invalid image file type. Only JPG, JPEG, PNG, GIF allowed. Product not registered.";
            return;
        }
    }

    // Start transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // Insert product
        // Note: Using 'price' and 'low_stock' to match your database schema
        $stmt = $conn->prepare("INSERT INTO products (name, category, sku, description, cost_price, price, quantity, low_stock, supplier, image, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssddiissi",
            $name, $category, $sku, $description,
            $cost_price, $price, $quantity, $low_stock,
            $supplier, $image_filename, $created_by // Store just the filename
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting product: " . $stmt->error);
        }

        $product_id = $conn->insert_id; // Get the ID of the newly inserted product

        // Log initial stock as an 'inbound_creation' movement
        if ($quantity > 0) { // Only log if initial quantity is positive
            $log_movement_sql = "INSERT INTO stock_movements (
                product_id, movement_type, quantity_change, current_stock_after,
                reference_document_type, reference_document_id, reference_document_number,
                notes, moved_by
            ) VALUES (
                ?, 'inbound_creation', ?, ?,
                'Product Creation', ?, ?,
                'Initial stock upon product creation', ?
            )";
            $stmt_log = $conn->prepare($log_movement_sql);
            $stmt_log->bind_param(
                "iiisis",
                $product_id, $quantity, $quantity,
                $product_id, $sku, // Use product_id and sku as reference
                $created_by
            );

            if (!$stmt_log->execute()) {
                throw new Exception("Error logging initial stock movement: " . $stmt_log->error);
            }
            $stmt_log->close();
        }

        mysqli_commit($conn); // Commit the transaction
        $_SESSION['product-message'] = "Product registered successfully and stock movement logged.";

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback changes if an error occurs
        $_SESSION['product-message'] = "Failed to register product: " . $e->getMessage();
        // If image was uploaded, try to delete it on rollback
        if ($image_filename && file_exists($target_dir . $image_filename)) {
            unlink($target_dir . $image_filename);
        }
    }

    $stmt->close(); // Close the product insert statement
}

?>
