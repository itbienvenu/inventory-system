<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/auth.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$user_id = (int)$_GET['id'];

// If the form was submitted, update the user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $names = trim($_POST['names']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);

    $update_stmt = mysqli_prepare($conn, "UPDATE users SET names = ?, email = ?, phone = ?, role = ? WHERE id = ?");
    mysqli_stmt_bind_param($update_stmt, "ssssi", $names, $email, $phone, $role, $user_id);

    if ($update_stmt->execute()) {
        $success = "User updated successfully!";
    } else {
        $error = "Failed to update user.";
    }

    $update_stmt->close();
}

// Always fetch the latest data after update or initial load
$stmt = mysqli_prepare($conn, "SELECT names, email, phone, role FROM users WHERE id = ?");
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
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Edit User Information</h4>
        </div>
        <div class="card-body">
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" id="editUserForm">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="names">Full Name</label>
                        <input type="text" class="form-control" name="names" id="names" value="<?= htmlspecialchars($user['names']) ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" name="role" id="role" required>
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="executive" <?= $user['role'] === 'executive' ? 'selected' : '' ?>>Executive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Update User</button>
                <a href="../executive/table.php#users" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $("#editUserForm").on("submit", function () {
        const name = $("#names").val().trim();
        const email = $("#email").val().trim();
        const phone = $("#phone").val().trim();
        if (!name || !email || !phone) {
            alert("Please fill in all required fields.");
            return false;
        }
        return true;
    });
</script>
</body>
</html>
