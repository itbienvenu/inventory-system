<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php";
log_user_action("Visited My Orders Page", "User viewed their sales orders");

// Check role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'daily') {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];
$get_seller = mysqli_query($conn, "SELECT * FROM users where id = $user_id");
$get_seller = mysqli_fetch_array($get_seller);
$user_name = $get_seller['names'];

// Example: Fetch user's orders (you'll replace this with your actual query)
$user_orders = mysqli_query($conn, "
    SELECT so.order_date, so.order_number, so.total_amount, so.status
    FROM sales_orders so
    WHERE so.created_by = $user_id
    ORDER BY so.order_date DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
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
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="my_orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_profile.php">My Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text">Hello, <strong><?= $user_name ?></strong></span>
            </div>
        </nav>

        <h2 class="mb-4">My Sales Orders</h2>

        <div class="card">
            <div class="card-header">Your Recent Orders</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Order Date</th>
                            <th>Order Number</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($user_orders) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($user_orders)): ?>
                                <tr>
                                    <td><?= $order['order_date'] ?></td>
                                    <td><?= $order['order_number'] ?></td>
                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                    <td><?= ucfirst($order['status']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
