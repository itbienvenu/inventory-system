<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_id = mysqli_real_escape_string($conn, $_POST['invoice_id']);
    $invoice_number_redirect = mysqli_real_escape_string($conn, $_POST['invoice_number']); // For redirection

    // Customer Info
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $vat = mysqli_real_escape_string($conn, $_POST['vat']);
    $street = mysqli_real_escape_string($conn, $_POST['street']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);

    // Update proforma_invoices header
    $update_invoice_sql = "UPDATE proforma_invoices SET
        company = '$company',
        vat = '$vat',
        street = '$street',
        city = '$city',
        postal_code = '$postal_code',
        country = '$country'
    WHERE id = $invoice_id";

    if (!mysqli_query($conn, $update_invoice_sql)) {
        die("Error updating invoice header: " . mysqli_error($conn));
    }

    // Handle proforma_items: Delete all existing items and re-insert new ones
    // This is a common and simpler strategy for updates where items can change significantly.
    $delete_items_sql = "DELETE FROM proforma_items WHERE invoice_id = $invoice_id";
    if (!mysqli_query($conn, $delete_items_sql)) {
        die("Error deleting existing invoice items: " . mysqli_error($conn));
    }

    $products = $_POST['products'];
    $quantities = $_POST['quantities'];

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty = (int)$quantities[$i];
            $product_id = (int)$product_id;

            if ($qty <= 0 || $product_id <= 0) {
                 // Optionally log this error or provide feedback to the user
                 // For now, we'll just skip invalid items silently
                 continue;
            }

            // Get product price from the database (important for security, don't trust client-side prices)
            $product_query = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
            if (!$product_query) {
                die("Error fetching product price: " . mysqli_error($conn));
            }
            $product = mysqli_fetch_assoc($product_query);

            if ($product) {
                $price = $product['price'];
                $total = $price * $qty;

                $insert_item_sql = "INSERT INTO proforma_items (invoice_id, product_id, quantity, unit_price, total_price) VALUES (
                    $invoice_id, $product_id, $qty, $price, $total
                )";
                if (!mysqli_query($conn, $insert_item_sql)) {
                    die("Error inserting new proforma item: " . mysqli_error($conn));
                }
            } else {
                // Handle case where product ID might be invalid (e.g., deleted product)
                // You might want to log this or provide a warning to the user
                error_log("Attempted to add non-existent product ID: $product_id to invoice $invoice_id");
            }
        }
    }

    // Redirect back to the updated proforma view page
    header("Location: ../edits/view_proforma.php?invoice=$invoice_number_redirect");
    exit(); // Always call exit after a header redirect
} else {
    die("Invalid request method.");
}
?>
