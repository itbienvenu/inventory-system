<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') { // Assuming executive can delete GRNs
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_grn.php");
    exit();
}

if (!isset($_GET['grn']) || empty($_GET['grn'])) {
    $_SESSION['message'] = "No Goods Received Note specified for deletion.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_grn.php");
    exit();
}

$grn_number = mysqli_real_escape_string($conn, $_GET['grn']);

// Get the GRN ID first
$get_grn_id_query = mysqli_query($conn, "SELECT id FROM goods_received_notes WHERE grn_number = '$grn_number'");
$grn_data_id = mysqli_fetch_assoc($get_grn_id_query);

if (!$grn_data_id) {
    $_SESSION['message'] = "Goods Received Note #{$grn_number} not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_grn.php");
    exit();
}

$grn_id = $grn_data_id['id'];

// Start transaction for atomicity
mysqli_begin_transaction($conn);

try {
    // Delete the GRN header. Due to ON DELETE CASCADE on goods_received_note_items,
    // its associated items will automatically be deleted.
    $delete_grn_sql = "DELETE FROM goods_received_notes WHERE id = $grn_id";
    if (!mysqli_query($conn, $delete_grn_sql)) {
        throw new Exception("Error deleting Goods Received Note: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    $_SESSION['message'] = "Goods Received Note #{$grn_number} deleted successfully.";
    $_SESSION['message_type'] = "success";

} catch (Exception $e) {
    mysqli_rollback($conn); // Rollback changes if an error occurs
    $_SESSION['message'] = "Error deleting Goods Received Note #{$grn_number}: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: ../edits/manage_grn.php");
exit();
?>
