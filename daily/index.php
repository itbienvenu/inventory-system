<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php";
log_user_action("Visited Dashboard", "User entered seller dashboard");

// Check role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'daily') {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];
$get_seller = mysqli_query($conn, "SELECT * FROM users where id = $user_id");
$get_seller = mysqli_fetch_array($get_seller);
$user_name = $get_seller['names'];

// Stats
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM sales_orders WHERE created_by = $user_id"))['total'] ?? 0;
$total_items = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(soi.quantity) AS qty FROM sales_order_items soi
    JOIN sales_orders so ON soi.order_id = so.id
    WHERE so.created_by = $user_id AND so.status IN ('confirmed','shipped')
"))['qty'] ?? 0;
$start_week = date('Y-m-d', strtotime("monday this week"));
$end_week = date('Y-m-d', strtotime("sunday this week"));
$weekly_sales = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM sales_orders
    WHERE created_by = $user_id AND order_date BETWEEN '$start_week' AND '$end_week'
"))['total'] ?? 0;
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(total_amount) AS amount FROM sales_orders
    WHERE created_by = $user_id AND status IN ('confirmed','shipped')
"))['amount'] ?? 0;

// Chart
$labels = [];
$data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('D', strtotime($date));
    $query = mysqli_query($conn, "
        SELECT SUM(total_amount) AS daily_total
        FROM sales_orders
        WHERE created_by = $user_id AND order_date = '$date' AND status IN ('confirmed','shipped')
    ");
    $row = mysqli_fetch_assoc($query);
    $data[] = $row['daily_total'] ?? 0;
}

// Recent Sales
$recent = mysqli_query($conn, "
    SELECT so.order_date, so.order_number, p.name, soi.quantity, soi.unit_price, soi.total_price
    FROM sales_order_items soi
    JOIN sales_orders so ON soi.order_id = so.id
    JOIN products p ON soi.product_id = p.id
    WHERE so.created_by = $user_id AND so.status IN ('confirmed','shipped')
    ORDER BY so.order_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-light p-4">
    <div class="container">

    <?php require_once 'top_bar.php'; ?>

        <h2 class="mb-4">Welcome, <?= $user_name ?> 👋</h2>

        <div class="row text-white mb-4">
            <div class="col-sm-6 col-md-3 mb-3 mb-md-0"><div class="bg-primary p-3 rounded">Orders<br><strong><?= $total_sales ?></strong></div></div>
            <div class="col-sm-6 col-md-3 mb-3 mb-md-0"><div class="bg-success p-3 rounded">Items Sold<br><strong><?= $total_items ?></strong></div></div>
            <div class="col-sm-6 col-md-3 mb-3 mb-md-0"><div class="bg-warning p-3 rounded">This Week<br><strong><?= $weekly_sales ?></strong></div></div>
            <div class="col-sm-6 col-md-3 mb-3 mb-md-0"><div class="bg-danger p-3 rounded">Revenue<br><strong>$<?= number_format($revenue, 2) ?></strong></div></div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Sales Last 7 Days</div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Recent Sales</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead><tr><th>Date</th><th>Order</th><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                            <tr>
                                <td><?= $r['order_date'] ?></td>
                                <td><?= $r['order_number'] ?></td>
                                <td><?= $r['name'] ?></td>
                                <td><?= $r['quantity'] ?></td>
                                <td>$<?= number_format($r['unit_price'], 2) ?></td>
                                <td>$<?= number_format($r['total_price'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Daily Revenue ($)',
                data: <?= json_encode($data) ?>,
                borderColor: 'blue',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allows the chart to fill the container better
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '$' + value; }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }
    });
    </script>
</body>
</html>