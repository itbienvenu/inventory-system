<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/auth.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = (int)$_GET['id'];

// Prevent deleting self (optional)
if ($_SESSION['user_id'] == $user_id) {
    echo "<script>alert('You cannot delete your own account.');</script>";
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);

if ($stmt->execute()) {
    header("Location: users.php?message=User+deleted+successfully");
    exit;
} else {
    echo "Error deleting user.";
}
?>
