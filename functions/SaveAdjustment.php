<?php
session_start();
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') { // Assuming executive can save adjustments
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $product_sku = mysqli_real_escape_string($conn, $_POST['product_sku']);
    $current_quantity = (int)$_POST['current_quantity'];
    $adjustment_type = mysqli_real_escape_string($conn, $_POST['adjustment_type']);
    $quantity_change_input = (int)$_POST['quantity_change']; // This is always a positive number from the form
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $moved_by = $_SESSION['user_id'];

    // Validate inputs
    if (empty($adjustment_type) || $quantity_change_input <= 0 || empty($notes)) {
        $_SESSION['message'] = "Please fill in all adjustment fields correctly.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../edits/adjust_inventory.php?id={$product_id}");
        exit();
    }

    $final_quantity_change = $quantity_change_input; // Default to positive
    $movement_type_for_log = ''; // Will be set based on adjustment_type

    // Determine the actual quantity change and movement type for logging
    switch ($adjustment_type) {
        case 'add':
            $movement_type_for_log = 'adjustment_add';
            // $final_quantity_change is already positive
            break;
        case 'deduct':
            $final_quantity_change = -$quantity_change_input; // Make it negative for deduction
            $movement_type_for_log = 'adjustment_deduct';
            // Prevent negative stock unless explicitly allowed by business logic
            if (($current_quantity + $final_quantity_change) < 0) {
                 $_SESSION['message'] = "Cannot deduct that much. Stock would go below zero.";
                 $_SESSION['message_type'] = "danger";
                 header("Location: ../edits/adjust_inventory.php?id={$product_id}");
                 exit();
            }
            break;
        // The 'correction' type is implicitly handled by 'add' or 'deduct' based on user input
        // If you want a distinct 'correction' type, the form would need a 'target_quantity' field
        // and logic to calculate the difference. For now, we stick to add/deduct.
        default:
            $_SESSION['message'] = "Invalid adjustment type selected.";
            $_SESSION['message_type'] = "danger";
            header("Location: ../edits/adjust_inventory.php?id={$product_id}");
            exit();
    }


    // Calculate new stock
    $new_stock_after_movement = $current_quantity + $final_quantity_change;

    // Start transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // 1. Update product quantity in the 'products' table
        $update_product_sql = "UPDATE products SET quantity = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_product_sql);
        $stmt_update->bind_param("ii", $new_stock_after_movement, $product_id);
        if (!$stmt_update->execute()) {
            throw new Exception("Error updating product quantity: " . $stmt_update->error);
        }
        $stmt_update->close();

        // 2. Log stock movement in the 'stock_movements' table
        $log_movement_sql = "INSERT INTO stock_movements (
            product_id, movement_type, quantity_change, current_stock_after,
            reference_document_type, reference_document_id, reference_document_number,
            notes, moved_by
        ) VALUES (
            ?, ?, ?, ?,
            'Manual Adjustment', NULL, ?,
            ?, ?
        )";
        $stmt_log = $conn->prepare($log_movement_sql);
        $stmt_log->bind_param(
            "isiisss",
            $product_id, $movement_type_for_log, $final_quantity_change, $new_stock_after_movement,
            $product_sku, // Use product SKU as reference number for manual adjustments
            $notes, $moved_by
        );
        if (!$stmt_log->execute()) {
            throw new Exception("Error logging stock movement: " . $stmt_log->error);
        }
        $stmt_log->close();

        mysqli_commit($conn); // Commit the transaction
        $_SESSION['message'] = "Inventory for SKU '{$product_sku}' adjusted successfully. New stock: {$new_stock_after_movement}.";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback changes if an error occurs
        $_SESSION['message'] = "Error adjusting inventory for SKU '{$product_sku}': " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    header("Location: ../edits/view_product.php?id={$product_id}");
    exit();
} else {
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_products.php");
    exit();
}
?>
