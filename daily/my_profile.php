<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php";
log_user_action("Visited My Profile Page", "User viewed their profile");

// Check role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'daily') {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];
$get_seller = mysqli_query($conn, "SELECT * FROM users where id = $user_id");
$get_seller = mysqli_fetch_array($get_seller);
$user_name = $get_seller['names'];
$user_email = $get_seller['email']; // Assuming 'email' column exists
$user_role = $get_seller['role']; // Assuming 'role' column exists
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-light p-4">
    <div class="container">

        <!-- Navbar (consistent with dashboard) -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 border rounded shadow-sm px-4">
            <a class="navbar-brand fw-bold text-primary" href="#">Seller Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_sales_order.php">Create Sale</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="my_profile.php">My Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text">Hello, <strong><?= $user_name ?></strong></span>
            </div>
        </nav>

        <h2 class="mb-4">My Profile</h2>

        <div class="card">
            <div class="card-header">Profile Information</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Name:</div>
                    <div class="col-md-9"><?= $user_name ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Email:</div>
                    <div class="col-md-9"><?= $user_email ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Role:</div>
                    <div class="col-md-9"><?= ucfirst($user_role) ?></div>
                </div>
                <p>You can add more profile details or an "Edit Profile" form here.</p>
                <button class="btn btn-primary">Edit Profile</button>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
