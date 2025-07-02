<?php
// message_functions.php

/**
 * Sends a message and logs the action.
 *
 * @param mysqli $conn The database connection object.
 * @param int $sender_id The ID of the user sending the message.
 * @param int $receiver_id The ID of the user receiving the message.
 * @param string $subject The subject of the message.
 * @param string $message_content The content of the message.
 * @param int|null $parent_message_id The ID of the parent message if this is a reply, null otherwise.
 * @return bool True on success, false on failure.
 */
function sendMessage(mysqli $conn, int $sender_id, int $receiver_id, string $subject, string $message_content, ?int $parent_message_id = null): bool {
    // Include logger if it's not already globally available or included in config
    // Ensure logger.php is accessible from this file's context
    if (function_exists('log_user_action')) {
        // Log the attempt to send message
        log_user_action("Attempting to Send Message", "User $sender_id attempting to send message to $receiver_id with subject: '$subject'");
    }

    // Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($conn, "INSERT INTO messages (sender_id, receiver_id, subject, message_content, parent_message_id) VALUES (?, ?, ?, ?, ?)");

    if ($stmt) {
        // Bind parameters: i = integer, s = string, b = boolean, d = double
        // For parent_message_id, it can be null, so we use 'i' and pass null directly.
        mysqli_stmt_bind_param($stmt, "iissi", $sender_id, $receiver_id, $subject, $message_content, $parent_message_id);

        if (mysqli_stmt_execute($stmt)) {
            if (function_exists('log_user_action')) {
                log_user_action("Message Sent Successfully", "User $sender_id sent message to $receiver_id with subject: '$subject'");
            }
            mysqli_stmt_close($stmt);
            return true;
        } else {
            if (function_exists('log_user_action')) {
                log_user_action("Failed to Send Message", "User $sender_id failed to send message to $receiver_id: " . mysqli_error($conn));
            }
            error_log("Failed to send message by user $sender_id to $receiver_id: " . mysqli_error($conn));
            mysqli_stmt_close($stmt);
            return false;
        }
    } else {
        if (function_exists('log_user_action')) {
            log_user_action("Database Error", "Database error preparing message insert statement for user $sender_id: " . mysqli_error($conn));
        }
        error_log("Database error preparing message insert statement: " . mysqli_error($conn));
        return false;
    }
}
?>
