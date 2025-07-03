<?php
session_start();
require_once(__DIR__ . "/config/config.php");
if (!isset($_SESSION['pending_user'])) {
    die("No OTP process started.");
}

$pending_user = $_SESSION['pending_user'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);

    $stmt = mysqli_prepare($conn, "
        SELECT id FROM otp_codes
        WHERE user_id = ? AND otp_code = ? AND is_used = 0 AND expires_at > NOW()
        ORDER BY created_at DESC
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "is", $pending_user['id'], $otp);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $otp_data = mysqli_fetch_assoc($result);

    if ($otp_data) {
        // Mark OTP as used
        $otp_id = $otp_data['id'];
        mysqli_query($conn, "UPDATE otp_codes SET is_used = 1 WHERE id = $otp_id");

        // ✅ Finish login
        $_SESSION['user_id'] = $pending_user['id'];
        $_SESSION['role'] = $pending_user['role'];

        // Optional: log action
        require_once(__DIR__ . "/includes/logger.php");
        log_user_action($pending_user['id'], "OTP verified", "Login success");

        unset($_SESSION['pending_user']); // Clear pending state

        // Redirect based on role
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: admin/index.php");
                break;
            case 'executive':
                header("Location: executive/index.php");
                break;
            case 'daily':
                header("Location: daily/index.php");
                break;
            default:
                echo "Invalid role";
        }
        exit;
    } else {
        $message = "Invalid or expired OTP.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="col-md-6 mx-auto bg-white p-4 shadow rounded">
        <h3 class="mb-3">Enter OTP sent to <?= htmlspecialchars($pending_user['email']) ?></h3>
        <?php if ($message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>OTP Code</label>
                <input type="text" name="otp" maxlength="10" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary">Verify OTP</button>
        </form>
    </div>
</div>
</body>
</html>
