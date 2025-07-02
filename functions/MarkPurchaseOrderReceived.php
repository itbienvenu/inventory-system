<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_purchase_order.php");
    exit();
    // die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['po_number']) || empty($_POST['po_number']) || !isset($_POST['received_date']) || empty($_POST['received_date'])) {
        $_SESSION['message'] = "Missing Purchase Order number or received date.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../edits/manage_purchase_order.php");
        exit();
    }

    $po_number = mysqli_real_escape_string($conn, $_POST['po_number']);
    $received_date = mysqli_real_escape_string($conn, $_POST['received_date']);
    $received_notes = mysqli_real_escape_string($conn, $_POST['received_notes']);

    // Update the purchase order status and receipt details
    $update_sql = "UPDATE purchase_orders SET
        status = 'received',
        received_date = '$received_date',
        received_notes = '$received_notes'
    WHERE po_number = '$po_number'";

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['message'] = "Purchase Order #{$po_number} marked as received.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error marking Purchase Order #{$po_number} as received: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }

    // Redirect back to the view page or manage page
    header("Location: ../edits/view_purchase_order.php?po=$po_number");
    exit();
} else {
    die("Invalid request method.");
}
?>
