<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delivery Note Header Info
    $customer_company = mysqli_real_escape_string($conn, $_POST['customer_company']);
    $customer_street = mysqli_real_escape_string($conn, $_POST['customer_street']);
    $customer_city = mysqli_real_escape_string($conn, $_POST['customer_city']);
    $customer_postal_code = mysqli_real_escape_string($conn, $_POST['customer_postal_code']);
    $customer_country = mysqli_real_escape_string($conn, $_POST['customer_country']);
    $sales_order_id = !empty($_POST['sales_order_id']) ? (int)$_POST['sales_order_id'] : NULL;
    $shipping_date = mysqli_real_escape_string($conn, $_POST['shipping_date']);
    $delivered_by = mysqli_real_escape_string($conn, $_POST['delivered_by']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $created_by = $_SESSION['user_id'];
    $delivery_note_number = "DN-" . time(); // Generate a unique delivery note number

    // Process items data before inserting delivery note header
    $items_data = [];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];
    $item_notes = $_POST['item_notes']; // Optional notes per item

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty = (int)$quantities[$i];
            $product_id = (int)$product_id;
            $current_item_note = mysqli_real_escape_string($conn, $item_notes[$i]);

            if ($qty <= 0 || $product_id <= 0) {
                 error_log("Invalid quantity or product ID for delivery note creation: Product ID $product_id, Quantity $qty.");
                 continue;
            }

            // You might want to add logic here to deduct from inventory 'products.quantity'
            // For now, just focus on saving the delivery note
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
        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the form
        exit();
    }

    // Insert delivery note header into 'delivery_notes' table
    $insert_dn_sql = "INSERT INTO delivery_notes (
        delivery_note_number, customer_company, customer_street, customer_city,
        customer_postal_code, customer_country, sales_order_id, shipping_date,
        delivered_by, notes, created_by
    ) VALUES (
        '$delivery_note_number', '$customer_company', '$customer_street', '$customer_city',
        '$customer_postal_code', '$customer_country', " . ($sales_order_id ? $sales_order_id : "NULL") . ", '$shipping_date',
        '$delivered_by', '$notes', $created_by
    )";

    if (!mysqli_query($conn, $insert_dn_sql)) {
        die("Error inserting delivery note header: " . mysqli_error($conn));
    }

    $delivery_note_id = mysqli_insert_id($conn);

    // Insert delivery note items into 'delivery_note_items' table
    foreach ($items_data as $item) {
        $product_id_item = $item['product_id'];
        $qty_item = $item['quantity'];
        $notes_item = $item['notes'];

        $insert_item_sql = "INSERT INTO delivery_note_items (delivery_note_id, product_id, quantity, notes) VALUES (
            $delivery_note_id, $product_id_item, $qty_item, '$notes_item'
        )";
        if (!mysqli_query($conn, $insert_item_sql)) {
            die("Error inserting delivery note item: " . mysqli_error($conn));
        }
    }

    $_SESSION['message'] = "Delivery Note #{$delivery_note_number} created successfully!";
    $_SESSION['message_type'] = "success";

    // Redirect to the new delivery note view page
    header("Location: ../edits/view_delivery_note.php?dn=$delivery_note_number");
    exit();
} else {
    die("Invalid request method.");
}
?>
