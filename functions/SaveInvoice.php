<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['admin', 'executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
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
    $due_date = !empty($_POST['due_date']) ? mysqli_real_escape_string($conn, $_POST['due_date']) : NULL;
    $executive_id = $_SESSION['user_id'];
    $invoice_number = "INV-" . time(); // Generate a unique invoice number

    $total_amount = 0; // Initialize total amount for the invoice

    // Process product items first to calculate total_amount before inserting invoice header
    $products_data = [];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty = (int)$quantities[$i];
            $product_id = (int)$product_id;

            if ($qty <= 0 || $product_id <= 0) {
                 // Skip invalid items
                 error_log("Invalid quantity or product ID provided for invoice creation.");
                 continue;
            }

            $product_query = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
            if (!$product_query) {
                die("Error fetching product price: " . mysqli_error($conn));
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
                error_log("Attempted to add non-existent product ID: $product_id to new invoice.");
            }
        }
    }

    if (empty($products_data)) {
        // If no valid products were added, stop the process
        die("An invoice must contain at least one valid product.");
    }

    // Insert invoice header into 'invoices' table
    $insert_invoice_sql = "INSERT INTO invoices (invoice_number, company, vat, street, city, postal_code, country, total_amount, due_date, created_by) VALUES (
        '$invoice_number', '$company', '$vat', '$street', '$city', '$postal_code', '$country', $total_amount, " . ($due_date ? "'$due_date'" : "NULL") . ", $executive_id
    )";

    if (!mysqli_query($conn, $insert_invoice_sql)) {
        die("Error inserting invoice header: " . mysqli_error($conn));
    }

    $invoice_id = mysqli_insert_id($conn);

    // Insert invoice items into 'invoice_items' table
    foreach ($products_data as $item) {
        $product_id_item = $item['product_id'];
        $qty_item = $item['quantity'];
        $price_item = $item['unit_price'];
        $total_price_for_item = $item['total_price'];

        $insert_item_sql = "INSERT INTO invoice_items (invoice_id, product_id, quantity, unit_price, total_price) VALUES (
            $invoice_id, $product_id_item, $qty_item, $price_item, $total_price_for_item
        )";
        if (!mysqli_query($conn, $insert_item_sql)) {
            die("Error inserting invoice item: " . mysqli_error($conn));
        }
    }

    // Redirect to the new invoice view page (you'll create this next)
    header("Location: ../edits/view_invoice.php?invoice=$invoice_number");
    exit();
} else {
    die("Invalid request method.");
}
?>
