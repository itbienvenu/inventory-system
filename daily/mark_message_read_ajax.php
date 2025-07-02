<?php
// mark_message_read_ajax.php
session_start();

include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$message_id = $_POST['message_id'] ?? null;

if (empty($message_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Message ID is required.']);
    exit;
}

// Mark the message as read ONLY if the current user is the receiver
$stmt = mysqli_prepare($conn, "UPDATE messages SET is_read = TRUE WHERE id = ? AND receiver_id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $message_id, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Message marked as read.']);
        } else {
            echo json_encode(['status' => 'info', 'message' => 'Message already read or not found for this user.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
        error_log("Failed to mark message $message_id as read for user $user_id: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error preparing statement: ' . mysqli_error($conn)]);
    error_log("Database error preparing mark as read statement: " . mysqli_error($conn));
}

mysqli_close($conn);
?>
