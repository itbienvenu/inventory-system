<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // PO Header Info
    $supplier_name = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $supplier_contact_person = mysqli_real_escape_string($conn, $_POST['supplier_contact_person']);
    $supplier_email = mysqli_real_escape_string($conn, $_POST['supplier_email']);
    $supplier_phone = mysqli_real_escape_string($conn, $_POST['supplier_phone']);
    $supplier_address = mysqli_real_escape_string($conn, $_POST['supplier_address']);
    $expected_delivery_date = !empty($_POST['expected_delivery_date']) ? mysqli_real_escape_string($conn, $_POST['expected_delivery_date']) : NULL;
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $created_by = $_SESSION['user_id'];
    $po_number = "PO-" . time(); // Generate a unique PO number

    $total_amount = 0; // Initialize total amount for the purchase order

    // Process items data before inserting PO header
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
                 error_log("Invalid data for purchase order item: Product ID $product_id, Quantity $qty, Unit Cost $unit_cost.");
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
        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the form
        exit();
    }

    // Insert purchase order header into 'purchase_orders' table
    $insert_po_sql = "INSERT INTO purchase_orders (
        po_number, supplier_name, supplier_contact_person, supplier_email,
        supplier_phone, supplier_address, total_amount, expected_delivery_date,
        notes, created_by
    ) VALUES (
        '$po_number', '$supplier_name', '$supplier_contact_person', '$supplier_email',
        '$supplier_phone', '$supplier_address', $total_amount, " . ($expected_delivery_date ? "'$expected_delivery_date'" : "NULL") . ",
        '$notes', $created_by
    )";

    if (!mysqli_query($conn, $insert_po_sql)) {
        die("Error inserting purchase order header: " . mysqli_error($conn));
    }

    $po_id = mysqli_insert_id($conn);

    // Insert purchase order items into 'purchase_order_items' table
    foreach ($items_data as $item) {
        $product_id_item = $item['product_id'];
        $qty_item = $item['quantity'];
        $unit_cost_item = $item['unit_cost'];
        $total_cost_for_item = $item['total_cost'];

        $insert_item_sql = "INSERT INTO purchase_order_items (po_id, product_id, quantity, unit_cost, total_cost) VALUES (
            $po_id, $product_id_item, $qty_item, $unit_cost_item, $total_cost_for_item
        )";
        if (!mysqli_query($conn, $insert_item_sql)) {
            die("Error inserting purchase order item: " . mysqli_error($conn));
        }
    }

    $_SESSION['message'] = "Purchase Order #{$po_number} created successfully!";
    $_SESSION['message_type'] = "success";

    // Redirect to the new purchase order view page
    header("Location: ../edits/view_purchase_order.php?po=$po_number");
    exit();
} else {
    die("Invalid request method.");
}
?>
