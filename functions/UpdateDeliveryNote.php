<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_note_id = mysqli_real_escape_string($conn, $_POST['delivery_note_id']);
    $delivery_note_number_redirect = mysqli_real_escape_string($conn, $_POST['delivery_note_number']); // For redirection

    // Delivery Note Header Info
    $customer_company = mysqli_real_escape_string($conn, $_POST['customer_company']);
    $customer_street = mysqli_real_escape_string($conn, $_POST['customer_street']);
    $customer_city = mysqli_real_escape_string($conn, $_POST['customer_city']);
    $customer_postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $customer_country = mysqli_real_escape_string($conn, $_POST['country']);
    $sales_order_id = !empty($_POST['sales_order_id']) ? (int)$_POST['sales_order_id'] : NULL;
    $shipping_date = mysqli_real_escape_string($conn, $_POST['shipping_date']);
    $delivered_by = mysqli_real_escape_string($conn, $_POST['delivered_by']);
    $recipient_name = mysqli_real_escape_string($conn, $_POST['recipient_name']);
    $received_at_input = !empty($_POST['received_at']) ? mysqli_real_escape_string($conn, $_POST['received_at']) : NULL;
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // If received_at is provided and not empty, convert it to MySQL datetime format
    $received_at = $received_at_input ? date('Y-m-d H:i:s', strtotime($received_at_input)) : NULL;


    // Process items data before updating delivery note
    $items_data = [];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];
    $item_notes = $_POST['item_notes'];

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty = (int)$quantities[$i];
            $product_id = (int)$product_id;
            $current_item_note = mysqli_real_escape_string($conn, $item_notes[$i]);

            if ($qty <= 0 || $product_id <= 0) {
                 error_log("Invalid quantity or product ID for update in delivery note #{$delivery_note_id}: Product ID $product_id, Quantity $qty.");
                 continue;
            }

            $items_data[] = [
                'product_id' => $product_id,
                'quantity' => $qty,
                'notes' => $current_item_note
            ];
        }
    }

    if (empty($items_data)) {
        $_SESSION['message'] = "A delivery note must contain at least one valid item.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../edits/edit_delivery_note.php?dn=$delivery_note_number_redirect");
        exit();
    }

    // Start transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // Update delivery_notes header
        $update_dn_sql = "UPDATE delivery_notes SET
            customer_company = '$customer_company',
            customer_street = '$customer_street',
            customer_city = '$customer_city',
            customer_postal_code = '$customer_postal_code',
            customer_country = '$customer_country',
            sales_order_id = " . ($sales_order_id ? $sales_order_id : "NULL") . ",
            shipping_date = '$shipping_date',
            delivered_by = '$delivered_by',
            recipient_name = " . ($recipient_name ? "'$recipient_name'" : "NULL") . ",
            received_at = " . ($received_at ? "'$received_at'" : "NULL") . ",
            notes = '$notes'
        WHERE id = $delivery_note_id";

        if (!mysqli_query($conn, $update_dn_sql)) {
            throw new Exception("Error updating delivery note header: " . mysqli_error($conn));
        }

        // Handle delivery_note_items: Delete all existing items and re-insert new ones
        $delete_items_sql = "DELETE FROM delivery_note_items WHERE delivery_note_id = $delivery_note_id";
        if (!mysqli_query($conn, $delete_items_sql)) {
            throw new Exception("Error deleting existing delivery note items: " . mysqli_error($conn));
        }

        foreach ($items_data as $item) {
            $product_id_item = $item['product_id'];
            $qty_item = $item['quantity'];
            $notes_item = $item['notes'];

            $insert_item_sql = "INSERT INTO delivery_note_items (delivery_note_id, product_id, quantity, notes) VALUES (
                $delivery_note_id, $product_id_item, $qty_item, '$notes_item'
            )";
            if (!mysqli_query($conn, $insert_item_sql)) {
                throw new Exception("Error inserting new delivery note item: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        $_SESSION['message'] = "Delivery Note #{$delivery_note_number_redirect} updated successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback changes if an error occurs
        $_SESSION['message'] = "Error updating Delivery Note #{$delivery_note_number_redirect}: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    // Redirect back to the updated delivery note view page
    header("Location: ../edits/view_delivery_note.php?dn=$delivery_note_number_redirect");
    exit();
} else {
    die("Invalid request method.");
}
?>
