<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grn_id = mysqli_real_escape_string($conn, $_POST['grn_id']);
    $grn_number_redirect = mysqli_real_escape_string($conn, $_POST['grn_number']); // For redirection

    // GRN Header Info
    $po_id = !empty($_POST['po_id']) ? (int)$_POST['po_id'] : NULL;
    $supplier_name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $receipt_date = mysqli_real_escape_string($conn, $_POST['receipt_date']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // Process items data before updating GRN header
    $items_data = [];
    $products = $_POST['products'];
    $quantities_received = $_POST['quantities_received'];
    $condition_notes = $_POST['condition_notes'];

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty_received = (int)$quantities_received[$i];
            $product_id = (int)$product_id;
            $current_condition_note = mysqli_real_escape_string($conn, $condition_notes[$i]);

            if ($qty_received <= 0 || $product_id <= 0) {
                 error_log("Invalid data for update in GRN #{$grn_id} item: Product ID $product_id, Quantity Received $qty_received.");
                 continue;
            }

            $items_data[] = [
                'product_id' => $product_id,
                'quantity_received' => $qty_received,
                'condition_notes' => $current_condition_note
            ];
        }
    }

    if (empty($items_data)) {
        $_SESSION['message'] = "A Goods Received Note must contain at least one valid item.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../edits/edit_grn.php?grn=$grn_number_redirect");
        exit();
    }

    // Start transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // Update goods_received_notes header
        $update_grn_sql = "UPDATE goods_received_notes SET
            po_id = " . ($po_id ? $po_id : "NULL") . ",
            supplier_name = '$supplier_name',
            receipt_date = '$receipt_date',
            notes = '$notes'
        WHERE id = $grn_id";

        if (!mysqli_query($conn, $update_grn_sql)) {
            throw new Exception("Error updating Goods Received Note header: " . mysqli_error($conn));
        }

        // Handle goods_received_note_items: Delete all existing items and re-insert new ones
        $delete_items_sql = "DELETE FROM goods_received_note_items WHERE grn_id = $grn_id";
        if (!mysqli_query($conn, $delete_items_sql)) {
            throw new Exception("Error deleting existing GRN items: " . mysqli_error($conn));
        }

        foreach ($items_data as $item) {
            $product_id_item = $item['product_id'];
            $qty_received_item = $item['quantity_received'];
            $condition_notes_item = $item['condition_notes'];

            $insert_item_sql = "INSERT INTO goods_received_note_items (grn_id, product_id, quantity_received, condition_notes) VALUES (
                $grn_id, $product_id_item, $qty_received_item, '$condition_notes_item'
            )";
            if (!mysqli_query($conn, $insert_item_sql)) {
                throw new Exception("Error inserting new GRN item: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        $_SESSION['message'] = "Goods Received Note #{$grn_number_redirect} updated successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback changes if an error occurs
        $_SESSION['message'] = "Error updating Goods Received Note #{$grn_number_redirect}: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    // Redirect back to the updated GRN view page
    header("Location: ../edits/view_grn.php?grn=$grn_number_redirect");
    exit();
} else {
    die("Invalid request method.");
}
?>
