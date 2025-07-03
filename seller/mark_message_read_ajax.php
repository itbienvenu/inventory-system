<?php
// seller/mark_message_read_ajax.php
// This script handles AJAX requests to mark a specific message as read.

// Start session and include necessary configurations
session_start();
include_once (__DIR__."/../config/auth.php"); // Adjust path as necessary
include_once (__DIR__."/../config/config.php"); // Adjust path as necessary
include_once __DIR__. "/../includes/logger.php"; // For logging actions

header('Content-Type: application/json'); // Set header for JSON response

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Get the message ID from the POST data
$message_id = $_POST['message_id'] ?? null;

// Validate message ID
if (empty($message_id) || !is_numeric($message_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid message ID provided.']);
    exit;
}

$message_id = (int)$message_id;
$logged_in_user_id = $_SESSION['user_id'];

// Prepare and execute the SQL query to mark the message as read
// IMPORTANT: Ensure the message belongs to the logged-in user to prevent unauthorized marking
$stmt = mysqli_prepare($conn, "UPDATE messages SET is_read = TRUE WHERE id = ? AND receiver_id = ?");

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $message_id, $logged_in_user_id);
    if (mysqli_stmt_execute($stmt)) {
        // Check if any rows were affected (i.e., message was found and updated)
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Message marked as read.']);
            log_user_action("Message Read", "User $logged_in_user_id marked message $message_id as read.");
        } else {
            // Message not found for this user, or already read
            echo json_encode(['status' => 'error', 'message' => 'Message not found or already read.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
        error_log("Database error marking message $message_id as read for user $logged_in_user_id: " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement.']);
    error_log("Failed to prepare statement for marking message as read: " . mysqli_error($conn));
}

// Close database connection
mysqli_close($conn);
?>
