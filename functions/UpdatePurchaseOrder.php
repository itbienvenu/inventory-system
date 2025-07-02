<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $po_id = mysqli_real_escape_string($conn, $_POST['po_id']);
    $po_number_redirect = mysqli_real_escape_string($conn, $_POST['po_number']); // For redirection

    // PO Header Info
    $supplier_name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $supplier_contact_person = mysqli_real_escape_string($conn, $_POST['supplier_contact_person']);
    $supplier_email = mysqli_real_escape_string($conn, $_POST['supplier_email']);
    $supplier_phone = mysqli_real_escape_string($conn, $_POST['supplier_phone']);
    $supplier_address = mysqli_real_escape_string($conn, $_POST['supplier_address']);
    $expected_delivery_date = !empty($_POST['expected_delivery_date']) ? mysqli_real_escape_string($conn, $_POST['expected_delivery_date']) : NULL;
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $total_amount = 0; // Recalculate total amount for the purchase order

    // Process items data before updating PO header
    $items_data = [];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];
    $unit_costs = $_POST['unit_costs'];

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty = (int)$quantities[$i];
            $product_id = (int)$product_id;
            $unit_cost = (float)$unit_costs[$i];

            if ($qty <= 0 || $product_id <= 0 || $unit_cost <= 0) {
                 error_log("Invalid data for update in purchase order #{$po_id} item: Product ID $product_id, Quantity $qty, Unit Cost $unit_cost.");
                 continue;
            }

            $total_cost_item = $unit_cost * $qty;
            $total_amount += $total_cost_item; // Add to overall total

            $items_data[] = [
                'product_id' => $product_id,
                'quantity' => $qty,
                'unit_cost' => $unit_cost,
                'total_cost' => $total_cost_item
            ];
        }
    }

    if (empty($items_data)) {
        $_SESSION['message'] = "A purchase order must contain at least one valid item.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../edits/edit_purchase_order.php?po=$po_number_redirect");
        exit();
    }

    // Start transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // Update purchase_orders header
        $update_po_sql = "UPDATE purchase_orders SET
            supplier_name = '$supplier_name',
            supplier_contact_person = '$supplier_contact_person',
            supplier_email = '$supplier_email',
            supplier_phone = '$supplier_phone',
            supplier_address = '$supplier_address',
            total_amount = $total_amount,
            expected_delivery_date = " . ($expected_delivery_date ? "'$expected_delivery_date'" : "NULL") . ",
            status = '$status',
            notes = '$notes'
        WHERE id = $po_id";

        if (!mysqli_query($conn, $update_po_sql)) {
            throw new Exception("Error updating purchase order header: " . mysqli_error($conn));
        }

        // Handle purchase_order_items: Delete all existing items and re-insert new ones
        $delete_items_sql = "DELETE FROM purchase_order_items WHERE po_id = $po_id";
        if (!mysqli_query($conn, $delete_items_sql)) {
            throw new Exception("Error deleting existing purchase order items: " . mysqli_error($conn));
        }

        foreach ($items_data as $item) {
            $product_id_item = $item['product_id'];
            $qty_item = $item['quantity'];
            $unit_cost_item = $item['unit_cost'];
            $total_cost_for_item = $item['total_cost'];

            $insert_item_sql = "INSERT INTO purchase_order_items (po_id, product_id, quantity, unit_cost, total_cost) VALUES (
                $po_id, $product_id_item, $qty_item, $unit_cost_item, $total_cost_for_item
            )";
            if (!mysqli_query($conn, $insert_item_sql)) {
                throw new Exception("Error inserting new purchase order item: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        $_SESSION['message'] = "Purchase Order #{$po_number_redirect} updated successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback changes if an error occurs
        $_SESSION['message'] = "Error updating Purchase Order #{$po_number_redirect}: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    // Redirect back to the updated purchase order view page
    header("Location: ../edits/view_purchase_order.php?po=$po_number_redirect");
    exit();
} else {
    die("Invalid request method.");
}
?>
