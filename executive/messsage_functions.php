<?php

$executive_user_id = $_SESSION['user_id'];

// Fetch unread messages count for the executive
$unread_messages_query = mysqli_query($conn, "SELECT COUNT(*) AS total_unread FROM messages WHERE receiver_id = $executive_user_id AND is_read = FALSE");
$new_messages_count = 0;
if ($unread_messages_query) {
    $unread_row = mysqli_fetch_assoc($unread_messages_query);
    $new_messages_count = $unread_row['total_unread'] ?? 0;
} else {
    error_log("Error fetching unread messages count for executive: " . mysqli_error($conn));
}

$recent_executive_messages_query = mysqli_query($conn, "
    SELECT m.id, m.subject, m.message_content, m.timestamp, m.is_read, m.sender_id,
           s.names AS sender_name, s.role AS sender_role
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    WHERE m.receiver_id = $executive_user_id
    ORDER BY m.timestamp DESC
    LIMIT 5
");
$recent_executive_messages = [];
if ($recent_executive_messages_query) {
    while ($msg = mysqli_fetch_assoc($recent_executive_messages_query)) {
        $recent_executive_messages[] = $msg;
    }
} else {
    error_log("Error fetching recent messages for executive: " . mysqli_error($conn));
}

if (!function_exists('formatMessageTime')) {
    function formatMessageTime($timestamp) {
        $message_time = strtotime($timestamp);
        $current_time = time();
        $diff = $current_time - $message_time;

        if ($diff < 60) { // Less than 1 minute
            return $diff . " Sec ago";
        } elseif ($diff < 3600) { // Less than 1 hour
            return round($diff / 60) . " Min ago";
        } elseif ($diff < 86400) { // Less than 24 hours (today)
            return date('h:i A', $message_time);
        } elseif ($diff < 172800) { // Less than 48 hours (yesterday)
            return "Yesterday";
        } else { // Older than yesterday
            return date('M j, Y', $message_time);
        }
    }
}