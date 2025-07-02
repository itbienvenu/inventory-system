<?php
// fetch_messages_ajax.php
// session_start(); // Ensure session is started for auth check

include_once (__DIR__."/../config/auth.php"); // Adjust path if config is in a different location
include_once (__DIR__."/../config/config.php"); // Adjust path if config is in a different location
// No logger include here, as this is an AJAX endpoint and we don't want to log every fetch.

header('Content-Type: application/json'); // Set header for JSON response

// Check if user is authenticated
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'daily' || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'inbox'; // 'inbox' or 'sent'

$messages = [];
$stmt = null;

if ($type === 'inbox') {
    // Fetch received messages for the current user
    $query = "SELECT m.id, m.subject, m.message_content, m.timestamp, m.is_read, m.parent_message_id,
                     s.names AS sender_name, s.role AS sender_role
              FROM messages m
              JOIN users s ON m.sender_id = s.id
              WHERE m.receiver_id = ?
              ORDER BY m.timestamp DESC";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    }
} elseif ($type === 'sent') {
    // Fetch sent messages by the current user
    // Also check if there's any reply to this message (where this message's ID is the parent_message_id)
    $query = "SELECT m.id, m.subject, m.message_content, m.timestamp, m.is_read, m.parent_message_id,
                     r.names AS receiver_name, r.role AS receiver_role,
                     (SELECT COUNT(*) FROM messages WHERE parent_message_id = m.id) AS reply_count
              FROM messages m
              JOIN users r ON m.receiver_id = r.id
              WHERE m.sender_id = ?
              ORDER BY m.timestamp DESC";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid message type.']);
    exit;
}

if ($stmt) {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        echo json_encode(['status' => 'success', 'messages' => $messages]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch messages: ' . mysqli_error($conn)]);
        error_log("Failed to fetch messages for user $user_id (type: $type): " . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error preparing statement: ' . mysqli_error($conn)]);
    error_log("Database error preparing message fetch statement for user $user_id (type: $type): " . mysqli_error($conn));
}

// Close database connection
mysqli_close($conn);
?>
