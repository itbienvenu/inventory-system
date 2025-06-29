<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') { // Assuming executive can create GRNs
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // GRN Header Info
    $po_id = !empty($_POST['po_id']) ? (int)$_POST['po_id'] : NULL;
    $supplier_name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $receipt_date = mysqli_real_escape_string($conn, $_POST['receipt_date']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $received_by = $_SESSION['user_id'];
    $grn_number = "GRN-" . time(); // Generate a unique GRN number

    // Process items data before inserting GRN header
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
                 error_log("Invalid data for GRN item: Product ID $product_id, Quantity Received $qty_received.");
                 continue;
            }

            // Important: This is where you would update your main products inventory quantity
            // For now, we are just recording the GRN, but actual inventory update logic
            // should be considered here (e.g., UPDATE products SET quantity = quantity + $qty_received WHERE id = $product_id)
            // You might also want to update the PO status if all items are received.

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
        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the form
        exit();
    }

    // Insert GRN header into 'goods_received_notes' table
    $insert_grn_sql = "INSERT INTO goods_received_notes (
        grn_number, po_id, supplier_name, receipt_date, received_by, notes
    ) VALUES (
        '$grn_number', " . ($po_id ? $po_id : "NULL") . ", '$supplier_name', '$receipt_date', $received_by, '$notes'
    )";

    if (!mysqli_query($conn, $insert_grn_sql)) {
        die("Error inserting Goods Received Note header: " . mysqli_error($conn));
    }

    $grn_id = mysqli_insert_id($conn);

    // Insert GRN items into 'goods_received_note_items' table
    foreach ($items_data as $item) {
        $product_id_item = $item['product_id'];
        $qty_received_item = $item['quantity_received'];
        $condition_notes_item = $item['condition_notes'];

        $insert_item_sql = "INSERT INTO goods_received_note_items (grn_id, product_id, quantity_received, condition_notes) VALUES (
            $grn_id, $product_id_item, $qty_received_item, '$condition_notes_item'
        )";
        if (!mysqli_query($conn, $insert_item_sql)) {
            die("Error inserting Goods Received Note item: " . mysqli_error($conn));
        }
    }

    $_SESSION['message'] = "Goods Received Note #{$grn_number} created successfully!";
    $_SESSION['message_type'] = "success";

    // Redirect to the new GRN view page
    header("Location: ../edits/view_grn.php?grn=$grn_number");
    exit();
} else {
    die("Invalid request method.");
}
?>
