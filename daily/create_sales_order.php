<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php";
log_user_action("Visited Create Sales Order Page", "Saller navigated to create sales order form");

// Check role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'daily') {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];
$get_seller = mysqli_query($conn, "SELECT * FROM users where id = $user_id");
$get_seller = mysqli_fetch_array($get_seller);
$user_name = $get_seller['names'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Sales Order</title>
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
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="create_sales_order.php">Create Sale</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_profile.php">My Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text">Hello, <strong><?= $user_name ?></strong></span>
            </div>
        </nav>

        <h2 class="mb-4">Create New Sales Order</h2>

        <div class="card">
            <div class="card-body">
                <p>This is where you would add your form to create a new sales order.</p>
                <!-- Example Form Placeholder -->
                <form>
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Enter customer name">
                    </div>
                    <div class="mb-3">
                        <label for="product" class="form-label">Product</label>
                        <select class="form-select" id="product">
                            <option selected>Choose...</option>
                            <option value="1">Product A</option>
                            <option value="2">Product B</option>
                            <option value="3">Product C</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" value="1" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Order</button>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
