<?php
// seller/send_reply_ajax.php
// This script handles AJAX requests to send replies to messages.

// Ensure session is started (auth.php should handle this conditionally)
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php"; // For logging actions
include_once __DIR__. "/../functions/message_functions.php"; // Contains the sendMessage() function

header('Content-Type: application/json'); // Set header for JSON response

// Check if user is authenticated and is an executive
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'executive') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Get data from POST request
$original_message_id = $_POST['original_message_id'] ?? null;
$receiver_id = $_POST['receiver_id'] ?? null;
$subject = trim($_POST['subject'] ?? '');
$message_content = trim($_POST['message_content'] ?? '');

// Basic validation
if (empty($receiver_id) || empty($subject) || empty($message_content) || empty($original_message_id)) {
    echo json_encode(['status' => 'error', 'message' => 'All reply fields are required.']);
    exit;
}

// Ensure receiver_id and original_message_id are integers
$receiver_id = (int)$receiver_id;
$original_message_id = (int)$original_message_id;

// Use the sendMessage function to save the reply
// The parent_message_id for the reply will be the ID of the original message
if (sendMessage($conn, $current_user_id, $receiver_id, $subject, $message_content, $original_message_id)) {
    echo json_encode(['status' => 'success', 'message' => 'Reply sent successfully!']);
    log_user_action("Message Reply Sent", "Executive $current_user_id replied to message $original_message_id, sending to $receiver_id.");
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send reply.']);
    error_log("Executive $current_user_id failed to send reply to message $original_message_id.");
}

// Close database connection
mysqli_close($conn);
?>
