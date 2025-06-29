<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_delivery_note.php");
    exit();
}

if (!isset($_GET['dn']) || empty($_GET['dn'])) {
    $_SESSION['message'] = "No Delivery Note specified for deletion.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_delivery_note.php");
    exit();
}

$delivery_note_number = mysqli_real_escape_string($conn, $_GET['dn']);

// Get the delivery note ID first
$get_dn_id_query = mysqli_query($conn, "SELECT id FROM delivery_notes WHERE delivery_note_number = '$delivery_note_number'");
$dn_data = mysqli_fetch_assoc($get_dn_id_query);

if (!$dn_data) {
    $_SESSION['message'] = "Delivery Note #{$delivery_note_number} not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_delivery_note.php");
    exit();
}

$delivery_note_id = $dn_data['id'];

// Start transaction for atomicity
mysqli_begin_transaction($conn);

try {
    // Delete the delivery note header. Due to ON DELETE CASCADE on delivery_note_items,
    // its associated items will automatically be deleted.
    $delete_dn_sql = "DELETE FROM delivery_notes WHERE id = $delivery_note_id";
    if (!mysqli_query($conn, $delete_dn_sql)) {
        throw new Exception("Error deleting Delivery Note: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    $_SESSION['message'] = "Delivery Note #{$delivery_note_number} deleted successfully.";
    $_SESSION['message_type'] = "success";

} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback changes if an error occurs
    $_SESSION['message'] = "Error deleting Delivery Note #{$delivery_note_number}: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../edits/manage_delivery_note.php");
exit();
?>
