<?php
session_start();
include_once __DIR__ . '/../config/config.php';

// Check if user is authorized
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$allowed_roles = ['executive', 'admin'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $message_id = (int)$_POST['message_id'];

    // Perform soft delete (recommended) or permanent delete
    $stmt = mysqli_prepare($conn, "DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete message']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
