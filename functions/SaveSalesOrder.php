<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Customer Info
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $vat = mysqli_real_escape_string($conn, $_POST['vat']);
    $street = mysqli_real_escape_string($conn, $_POST['street']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $delivery_date = !empty($_POST['delivery_date']) ? mysqli_real_escape_string($conn, $_POST['delivery_date']) : NULL;
    $executive_id = $_SESSION['user_id'];
    $order_number = "SO-" . time(); // Generate a unique sales order number

    $total_amount = 0; // Initialize total amount for the sales order

    // Process product items first to calculate total_amount before inserting order header
    $products_data = [];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty = (int)$quantities[$i];
            $product_id = (int)$product_id;

            if ($qty <= 0 || $product_id <= 0) {
                 // Skip invalid items, log error for debugging
                 error_log("Invalid quantity or product ID provided for sales order creation: Product ID $product_id, Quantity $qty.");
                 continue;
            }

            $product_query = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
            if (!$product_query) {
                die("Error fetching product price for sales order: " . mysqli_error($conn));
            }
            $product = mysqli_fetch_assoc($product_query);

            if ($product) {
                $price = $product['price'];
                $total_price_item = $price * $qty;
                $total_amount += $total_price_item; // Add to overall total

                $products_data[] = [
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $total_price_item
                ];
            } else {
                error_log("Attempted to add non-existent product ID: $product_id to new sales order.");
            }
        }
    }

    if (empty($products_data)) {
        // If no valid products were added, stop the process
        $_SESSION['message'] = "A sales order must contain at least one valid product.";
        $_SESSION['message_type'] = "danger";
        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the form
        exit();
    }

    // Insert sales order header into 'sales_orders' table
    $insert_order_sql = "INSERT INTO sales_orders (order_number, company, vat, street, city, postal_code, country, total_amount, delivery_date, created_by) VALUES (
        '$order_number', '$company', '$vat', '$street', '$city', '$postal_code', '$country', $total_amount, " . ($delivery_date ? "'$delivery_date'" : "NULL") . ", $executive_id
    )";

    if (!mysqli_query($conn, $insert_order_sql)) {
        die("Error inserting sales order header: " . mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);

    // Insert sales order items into 'sales_order_items' table
    foreach ($products_data as $item) {
        $product_id_item = $item['product_id'];
        $qty_item = $item['quantity'];
        $price_item = $item['unit_price'];
        $total_price_for_item = $item['total_price'];

        $insert_item_sql = "INSERT INTO sales_order_items (order_id, product_id, quantity, unit_price, total_price) VALUES (
            $order_id, $product_id_item, $qty_item, $price_item, $total_price_for_item
        )";
        if (!mysqli_query($conn, $insert_item_sql)) {
            die("Error inserting sales order item: " . mysqli_error($conn));
        }
    }

    $_SESSION['message'] = "Sales Order #{$order_number} created successfully!";
    $_SESSION['message_type'] = "success";

    // Redirect to the new sales order view page (you'll create this next)
    header("Location: ../edits/view_sales_order.php?order=$order_number");
    exit();
} else {
    die("Invalid request method.");
}
?>
