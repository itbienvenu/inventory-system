<?php
session_start();
include_once "../config/config.php";

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_sales_order.php");
    // die("Unauthorized access.");
}

if (!isset($_GET['order']) || empty($_GET['order'])) {
    $_SESSION['message'] = "No Sales Order specified for deletion.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_sales_order.php");
    exit();
}

$order_number = mysqli_real_escape_string($conn, $_GET['order']);

// Get the sales order ID first
$get_order_id_query = mysqli_query($conn, "SELECT id FROM sales_orders WHERE order_number = '$order_number'");
$order_data = mysqli_fetch_assoc($get_order_id_query);

if (!$order_data) {
    $_SESSION['message'] = "Sales Order #{$order_number} not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_sales_order.php");
    exit();
}

$order_id = $order_data['id'];

// Start transaction for atomicity
mysqli_begin_transaction($conn);

try {
    // Delete the sales order header. Due to ON DELETE CASCADE on sales_order_items,
    // its associated items will automatically be deleted.
    $delete_order_sql = "DELETE FROM sales_orders WHERE id = $order_id";
    if (!mysqli_query($conn, $delete_order_sql)) {
        throw new Exception("Error deleting sales order: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    $_SESSION['message'] = "Sales Order #{$order_number} deleted successfully.";
    $_SESSION['message_type'] = "success";

} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback changes if an error occurs
    $_SESSION['message'] = "Error deleting Sales Order #{$order_number}: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../edits/manage_sales_order.php");
exit();
?>
