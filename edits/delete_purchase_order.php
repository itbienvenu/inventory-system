<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_purchase_order.php");
    // die("Unauthorized access.");
}


if (!isset($_GET['po']) || empty($_GET['po'])) {
    $_SESSION['message'] = "No Purchase Order specified for deletion.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_purchase_order.php");
    exit();
}

$po_number = mysqli_real_escape_string($conn, $_GET['po']);

// Get the purchase order ID first
$get_po_id_query = mysqli_query($conn, "SELECT id FROM purchase_orders WHERE po_number = '$po_number'");
$po_data = mysqli_fetch_assoc($get_po_id_query);

if (!$po_data) {
    $_SESSION['message'] = "Purchase Order #{$po_number} not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_purchase_order.php");
    exit();
}

$po_id = $po_data['id'];

// Start transaction for atomicity
mysqli_begin_transaction($conn);

try {
    // Delete the purchase order header. Due to ON DELETE CASCADE on purchase_order_items,
    // its associated items will automatically be deleted.
    $delete_po_sql = "DELETE FROM purchase_orders WHERE id = $po_id";
    if (!mysqli_query($conn, $delete_po_sql)) {
        throw new Exception("Error deleting Purchase Order: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    $_SESSION['message'] = "Purchase Order #{$po_number} deleted successfully.";
    $_SESSION['message_type'] = "success";

} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback changes if an error occurs
    $_SESSION['message'] = "Error deleting Purchase Order #{$po_number}: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../edits/manage_purchase_order.php");
exit();
?>
