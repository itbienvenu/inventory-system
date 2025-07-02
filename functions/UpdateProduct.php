<?php
session_start();
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_products.php");
    exit();
    // die("Unauthorized access.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];

    // Sanitize and escape input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cost_price = (float)$_POST['cost_price'];
    $selling_price = (float)$_POST['selling_price']; // This maps to 'price' in your DB
    $new_quantity = (int)$_POST['quantity']; // New quantity from form
    $reorder_level = (int)$_POST['reorder_level']; // This maps to 'low_stock' in your DB
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : NULL; // Now an ID
    $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : NULL; // Now an ID
    $current_image = isset($_POST['current_image']) ? mysqli_real_escape_string($conn, $_POST['current_image']) : '';
    $updated_by = $_SESSION['user_id'];

    // Validate essential fields
    if (empty($name) || empty($sku) || $cost_price < 0 || $selling_price < 0 || $new_quantity < 0 || $reorder_level < 0) {
        // $_SESSION['message'] = "Please fill in all required product fields correctly.";
        echo "Please fill required files";
        // $_SESSION['message_type'] = "danger";
        header("Location: ../edits/edit_product.php?id={$product_id}");
        exit();
    }

    // Handle image upload
    $image_filename = $current_image; // Default to current image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array("jpeg", "jpg", "png", "gif");

        if (in_array($file_ext, $allowed_extensions)) {
            // Generate a unique file name
            $image_filename = uniqid('product_') . '.' . $file_ext;
            if (move_uploaded_file($file_tmp, $upload_dir . $image_filename)) {
                // If a new image is uploaded, delete the old one if it exists and is not the default
                if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
            } else {
                // $_SESSION['message'] = "Error uploading new image. Product not updated fully.";
                echo "Failed to add image";
                // $_SESSION['message_type'] = "warning";
                // Continue with product update, but without new image
                $image_filename = $current_image; // Revert to old image if upload fails
            }
        } else {
            // $_SESSION['message'] = "Invalid image file type. Only JPG, JPEG, PNG, GIF allowed. Product not updated fully.";
            // $_SESSION['message_type'] = "warning";
            echo "Invalid image tye";
            // Continue with product update, but without new image
            $image_filename = $current_image; // Revert to old image if file type is invalid
        }
    }


    // Fetch current quantity before update for logging purposes
    $current_qty_query = mysqli_query($conn, "SELECT quantity, sku FROM products WHERE id = $product_id");
    $current_qty_row = mysqli_fetch_assoc($current_qty_query);
    $old_quantity = $current_qty_row['quantity'];
    $product_sku_for_log = $current_qty_row['sku']; // Get SKU for logging

    // Start transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // Update product details
        // Note: 'price' and 'low_stock' are used here to match your database column names
        $update_sql = "UPDATE products SET
            name = '$name',
            sku = '$sku',
            description = '$description',
            cost_price = $cost_price,
            selling_price = $selling_price,
            quantity = $new_quantity,
            reorder_level = $reorder_level,
            category_id = " . ($category_id ? $category_id : "NULL") . ",
            supplier_id = " . ($supplier_id ? $supplier_id : "NULL") . ",
            image = " . (!empty($image_filename) ? "'$image_filename'" : "NULL") . "
        WHERE id = $product_id";

        if (!mysqli_query($conn, $update_sql)) {
            throw new Exception("Error updating product details: " . mysqli_error($conn));
        }

        // Log stock movement if quantity has changed
        if ($new_quantity != $old_quantity) {
            $quantity_change = $new_quantity - $old_quantity;
            $movement_type = ($quantity_change > 0) ? 'adjustment_add' : 'adjustment_deduct';
            $movement_notes = "Manual adjustment from product edit.";

            $log_movement_sql = "INSERT INTO stock_movements (
                product_id, movement_type, quantity_change, current_stock_after,
                reference_document_type, reference_document_id, reference_document_number,
                notes, moved_by
            ) VALUES (
                $product_id, '$movement_type', $quantity_change, $new_quantity,
                'Manual Adjustment', $product_id, '$product_sku_for_log',
                '$movement_notes', $updated_by
            )";

            if (!mysqli_query($conn, $log_movement_sql)) {
                // Log error but don't halt the main transaction if movement logging fails
                error_log("Failed to log stock movement during product update: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        // $_SESSION['message'] = "Product '{$name}' (SKU: {$sku}) updated successfully!";
        echo "Pridcu sku updted";
        // $_SESSION['message_type'] = "success";
        header("Location: ../edits/view_product.php?id={$product_id}");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback changes if an error occurs
        // $_SESSION['message'] = "Error updating product: " . $e->getMessage();
        echo "Error updating product: " . $e->getMessage();
        // $_SESSION['message_type'] = "danger";
        header("Location: ../edits/edit_product.php?id={$product_id}");
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_products.php");
    exit();
}
?>
