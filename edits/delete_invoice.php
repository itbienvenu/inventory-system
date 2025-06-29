<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_invoices.php");
    exit();
}

if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    $_SESSION['message'] = "No invoice specified for deletion.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_invoices.php");
    exit();
}

$invoice_number = mysqli_real_escape_string($conn, $_GET['invoice']);

// Get the invoice ID first
$get_invoice_id_query = mysqli_query($conn, "SELECT id FROM invoices WHERE invoice_number = '$invoice_number'");
$invoice_data = mysqli_fetch_assoc($get_invoice_id_query);

if (!$invoice_data) {
    $_SESSION['message'] = "Invoice #{$invoice_number} not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_invoices.php");
    exit();
}

$invoice_id = $invoice_data['id'];

// Start transaction for atomicity
mysqli_begin_transaction($conn);

try {
    // Delete items first (though ON DELETE CASCADE on invoices table handles this)
    // It's good practice to understand that they are linked.
    // Explicitly deleting invoice_items might be done if ON DELETE CASCADE wasn't set,
    // but with CASCADE, simply deleting from 'invoices' is enough.
    // $delete_items_sql = "DELETE FROM invoice_items WHERE invoice_id = $invoice_id";
    // if (!mysqli_query($conn, $delete_items_sql)) {
    //     throw new Exception("Error deleting invoice items: " . mysqli_error($conn));
    // }

    // Delete the invoice header
    $delete_invoice_sql = "DELETE FROM invoices WHERE id = $invoice_id";
    if (!mysqli_query($conn, $delete_invoice_sql)) {
        throw new Exception("Error deleting invoice: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    $_SESSION['message'] = "Invoice #{$invoice_number} deleted successfully.";
    $_SESSION['message_type'] = "success";

} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback changes if an error occurs
    $_SESSION['message'] = "Error deleting invoice #{$invoice_number}: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../edits/manage_invoice.php");
exit();
?>
