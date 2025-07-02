<?php
require_once(__DIR__ . '/../config/config.php');

function log_user_action($action, $details = null) {
    if (!isset($_SESSION['user_id'])) return;

    $user_id = $_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $page = $_SERVER['REQUEST_URI'] ?? 'UNKNOWN';
    $session_id = session_id();

    global $conn;
    $stmt = mysqli_prepare($conn, "
        INSERT INTO user_activity_logs (user_id, action, details, ip_address, user_agent, session_id, page)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssss", $user_id, $action, $details, $ip, $agent, $session_id, $page);
    $stmt->execute();
    $stmt->close();
}
