<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../edits/manage_delivery_note.php");
    exit();
    // die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['delivery_note_number']) || empty($_POST['delivery_note_number']) || !isset($_POST['recipient_name']) || empty($_POST['recipient_name'])) {
        $_SESSION['message'] = "Missing delivery note number or recipient name.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../edits/manage_delivery_note.php");
        exit();
    }

    $delivery_note_number = mysqli_real_escape_string($conn, $_POST['delivery_note_number']);
    $recipient_name = mysqli_real_escape_string($conn, $_POST['recipient_name']);
    $received_at = date('Y-m-d H:i:s'); // Current timestamp

    // Update the delivery note
    $update_sql = "UPDATE delivery_notes SET
        recipient_name = '$recipient_name',
        received_at = '$received_at'
    WHERE delivery_note_number = '$delivery_note_number'";

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['message'] = "Delivery Note #{$delivery_note_number} marked as received by {$recipient_name}.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error marking Delivery Note #{$delivery_note_number} as received: " . mysqli_error($conn);
        $_SESSION['message_type'] = "danger";
    }

    // Redirect back to the view page or manage page
    header("Location: ../edits/view_delivery_note.php?dn=$delivery_note_number");
    exit();
} else {
    die("Invalid request method.");
}
?>
