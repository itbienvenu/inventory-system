<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/auth.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = (int)$_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT names, email, phone, role, time FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4>User Details</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Full Name</th>
                    <td><?= htmlspecialchars($user['names']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                </tr>
                <tr>
                    <th>Registered At</th>
                    <td><?= date("Y-m-d H:i:s", strtotime($user['time'])) ?></td>
                </tr>
            </table>

            <a href="../executive/table.php#users" class="btn btn-secondary">Back to Users</a>
        </div>
    </div>
</div>
</body>
</html>
