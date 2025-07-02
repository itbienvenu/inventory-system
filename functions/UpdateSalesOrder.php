<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $order_number_redirect = mysqli_real_escape_string($conn, $_POST['order_number']); // For redirection

    // Customer Info
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $vat = mysqli_real_escape_string($conn, $_POST['vat']);
    $street = mysqli_real_escape_string($conn, $_POST['street']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $delivery_date = !empty($_POST['delivery_date']) ? mysqli_real_escape_string($conn, $_POST['delivery_date']) : NULL;
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $total_amount = 0; // Recalculate total amount for the sales order

    // Process product items first to calculate total_amount before updating order header
    $products_data = [];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];

    if (!empty($products) && is_array($products)) {
        foreach ($products as $i => $product_id) {
            $qty = (int)$quantities[$i];
            $product_id = (int)$product_id;

            if ($qty <= 0 || $product_id <= 0) {
                 error_log("Invalid quantity or product ID for update in sales order #{$order_id}: Product ID $product_id, Quantity $qty.");
                 continue;
            }

            $product_query = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
            if (!$product_query) {
                die("Error fetching product price for sales order update: " . mysqli_error($conn));
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
                error_log("Attempted to update sales order #{$order_id} with non-existent product ID: $product_id.");
            }
        }
    }

    if (empty($products_data)) {
        $_SESSION['message'] = "A sales order must contain at least one valid product.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../edits/edit_sales_order.php?order=$order_number_redirect");
        exit();
    }

    // Start transaction for atomicity
    mysqli_begin_transaction($conn);

    try {
        // Update sales_orders header
        $update_order_sql = "UPDATE sales_orders SET
            company = '$company',
            vat = '$vat',
            street = '$street',
            city = '$city',
            postal_code = '$postal_code',
            country = '$country',
            total_amount = $total_amount,
            status = '$status',
            delivery_date = " . ($delivery_date ? "'$delivery_date'" : "NULL") . "
        WHERE id = $order_id";

        if (!mysqli_query($conn, $update_order_sql)) {
            throw new Exception("Error updating sales order header: " . mysqli_error($conn));
        }

        // Handle sales_order_items: Delete all existing items and re-insert new ones
        $delete_items_sql = "DELETE FROM sales_order_items WHERE order_id = $order_id";
        if (!mysqli_query($conn, $delete_items_sql)) {
            throw new Exception("Error deleting existing sales order items: " . mysqli_error($conn));
        }

        foreach ($products_data as $item) {
            $product_id_item = $item['product_id'];
            $qty_item = $item['quantity'];
            $price_item = $item['unit_price'];
            $total_price_for_item = $item['total_price'];

            $insert_item_sql = "INSERT INTO sales_order_items (order_id, product_id, quantity, unit_price, total_price) VALUES (
                $order_id, $product_id_item, $qty_item, $price_item, $total_price_for_item
            )";
            if (!mysqli_query($conn, $insert_item_sql)) {
                throw new Exception("Error inserting new sales order item: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        $_SESSION['message'] = "Sales Order #{$order_number_redirect} updated successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback changes if an error occurs
        $_SESSION['message'] = "Error updating sales order #{$order_number_redirect}: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    // Redirect back to the updated sales order view page
    header("Location: ../edits/view_sales_order.php?order=$order_number_redirect");
    exit();
} else {
    die("Invalid request method.");
}
?>
