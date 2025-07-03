<?php
// session_start(); // Ensure session is started at the very top

include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php"; // Assuming logger.php exists and defines log_user_action
include_once __DIR__. "/../functions/message_functions.php"; // Include the message functions for formatMessageTime

$allowed_roles = ['executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

$id = $_SESSION['user_id'];

// Fetch user's name
$user_name_query = mysqli_query($conn, "SELECT names FROM users WHERE id=$id");
$user_name = '';
if ($user_name_query && mysqli_num_rows($user_name_query) > 0) {
    $user_row = mysqli_fetch_assoc($user_name_query);
    $user_name = htmlspecialchars($user_row['names']);
}

// --- Dashboard Data Fetching ---

// 1. Total Sales Orders Count
$total_sales_orders_query = mysqli_query($conn, "SELECT COUNT(id) AS total_orders FROM sales_orders");
$total_sales_orders = $total_sales_orders_query ? mysqli_fetch_assoc($total_sales_orders_query)['total_orders'] : 0;

// 2. Total Items Sold (from sales_order_items, consider 'shipped' or 'confirmed' sales orders)
$total_items_sold_query = mysqli_query($conn, "
    SELECT SUM(soi.quantity) AS total_sold_qty
    FROM sales_order_items soi
    JOIN sales_orders so ON soi.order_id = so.id
    WHERE so.status IN ('confirmed', 'shipped')
");
$total_items_sold = $total_items_sold_query ? mysqli_fetch_assoc($total_items_sold_query)['total_sold_qty'] : 0;
$total_items_sold = $total_items_sold === null ? 0 : $total_items_sold; // Handle NULL sum for no records

// 3. Sales Orders This Week
$start_of_week = date('Y-m-d', strtotime('monday this week'));
$end_of_week = date('Y-m-d', strtotime('sunday this week'));
$sales_this_week_query = mysqli_query($conn, "
    SELECT COUNT(id) AS orders_this_week
    FROM sales_orders
    WHERE order_date BETWEEN '$start_of_week' AND '$end_of_week'
");
$sales_this_week = $sales_this_week_query ? mysqli_fetch_assoc($sales_this_week_query)['orders_this_week'] : 0;

// 4. Total Earnings (from confirmed/shipped sales orders)
$total_earnings_query = mysqli_query($conn, "
    SELECT SUM(total_amount) AS total_revenue
    FROM sales_orders
    WHERE status IN ('confirmed', 'shipped')
");
$total_earnings = $total_earnings_query ? mysqli_fetch_assoc($total_earnings_query)['total_revenue'] : 0.00;
$total_earnings = $total_earnings === null ? 0.00 : $total_earnings; // Handle NULL sum for no records

// 5. Recent Earnings By Items (e.g., last 10 sales order items)
$recent_earnings_query = mysqli_query($conn, "
    SELECT soi.quantity, soi.unit_price, soi.total_price,
            p.name AS product_name, so.order_number, so.order_date
    FROM sales_order_items soi
    JOIN sales_orders so ON soi.order_id = so.id
    JOIN products p ON soi.product_id = p.id
    WHERE so.status IN ('confirmed', 'shipped')
    ORDER BY so.order_date DESC, soi.id DESC
    LIMIT 10
");
$recent_earnings_items = [];
if ($recent_earnings_query) {
    while ($row = mysqli_fetch_assoc($recent_earnings_query)) {
        $recent_earnings_items[] = $row;
    }
}

// 6. Top Countries (Sales)
$top_countries_query = mysqli_query($conn, "
    SELECT country, SUM(total_amount) AS country_total_sales
    FROM sales_orders
    WHERE status IN ('confirmed', 'shipped')
    GROUP BY country
    ORDER BY country_total_sales DESC
    LIMIT 8
");
$top_countries = [];
if ($top_countries_query) {
    while ($row = mysqli_fetch_assoc($top_countries_query)) {
        $top_countries[] = $row;
    }
}

// 7. Data for Recent Report Chart (e.g., monthly sales over last 6 months)
$monthly_sales_data = [];
$months = [];
for ($i = 5; $i >= 0; $i--) { // Last 6 months including current
    $month = date('Y-m', strtotime("-$i months"));
    $month_label = date('M Y', strtotime("-$i months"));
    $months[] = $month_label;

    $monthly_sales_query = mysqli_query($conn, "
        SELECT SUM(total_amount) AS monthly_total
        FROM sales_orders
        WHERE DATE_FORMAT(order_date, '%Y-%m') = '$month'
        AND status IN ('confirmed', 'shipped')
    ");
    $monthly_sales_result = mysqli_fetch_assoc($monthly_sales_query);
    $monthly_sales_data[] = $monthly_sales_result['monthly_total'] ? (float)$monthly_sales_result['monthly_total'] : 0;
}

// 8. Data for Percent Chart (e.g., Ratio of Sales vs Purchase Costs)
$total_sales_all = $total_earnings; // Already fetched
$total_purchase_cost_query = mysqli_query($conn, "
    SELECT SUM(total_amount) AS total_po_cost
    FROM purchase_orders
    WHERE status = 'received'
");
$total_purchase_cost = $total_purchase_cost_query ? mysqli_fetch_assoc($total_purchase_cost_query)['total_po_cost'] : 0.00;
$total_purchase_cost = $total_purchase_cost === null ? 0.00 : $total_purchase_cost;

// For simplicity, let's use a simple ratio for the percent chart
$profit_margin_percentage = 0;
if ($total_sales_all > 0) {
    $profit_margin_percentage = (($total_sales_all - $total_purchase_cost) / $total_sales_all) * 100;
}
$cost_percentage = 100 - $profit_margin_percentage;

$chart_percent_data = [
    'labels' => ['Revenue', 'Costs'],
    'data' => [round($total_sales_all, 2), round($total_purchase_cost, 2)],
    'colors' => ['#55b883', '#ffc107'] // Green for revenue, yellow for costs
];

// --- Message Data Fetching for Executive Dashboard ---
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

// Fetch recent messages for the executive (e.g., last 5)
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

// Helper function to format time (defined here if not already in message_functions.php)
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

log_user_action("Visited Admin Dashboard", "Executive user $user_name viewed dashboard");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Inventory Management System Dashboard">
    <meta name="author" content="Your Name/Company Name">
    <meta name="keywords" content="dashboard, inventory, sales, purchase, reports">

    <!-- Title Page-->
    <title>Dashboard</title>

    <!-- Fontfaces CSS-->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <!-- Chart.js is a JS file, should not be in rel="stylesheet" -->
    <!-- <link href="vendor/chartjs/Chart.bundle.min.js" rel="stylesheet" media="all"> -->


    <!-- Main CSS-->
    <link href="../css/theme.css" rel="stylesheet" media="all">

    <style>
        /* Custom styles if needed, or override theme defaults */
        .overview-item--c1 { background-color: #55b883; /* Green for Sales */ }
        .overview-item--c2 { background-color: #0d6efd; /* Blue for Items Sold */ }
        .overview-item--c3 { background-color: #ffc107; /* Yellow for Weekly Activity */ }
        .overview-item--c4 { background-color: #dc3545; /* Red for Total Purchases/Costs - or primary for Earnings */ }

        /* Dark mode adjustments (if not handled by theme.css already) */
        .dark-mode body {
            background-color: #2c2c2c;
            color: #f0f0f0;
        }
        .dark-mode .page-wrapper {
            background-color: #2c2c2c;
        }
        .dark-mode .header-desktop, .dark-mode .aside-wrap .aside-menu {
            background-color: #3a3a3a;
        }
        .dark-mode .au-card {
            background-color: #4a4a4a;
            color: #f0f0f0;
        }
        .dark-mode .table-responsive table th,
        .dark-mode .table-responsive table td {
            background-color: #4a4a4a; /* Card background */
            color: #f0f0f0;
            border-color: #666;
        }
        .dark-mode .table-responsive table.table-earning thead th {
             background-color: #343a40; /* Darker header for tables */
        }
        .dark-mode .au-btn-icon {
            background-color: #0d6efd; /* Same as light mode primary */
            color: white;
        }
        .dark-mode .au-btn-icon i {
            color: white;
        }
        .dark-mode .dot--blue { background-color: #0d6efd; }
        .dark-mode .dot--green { background-color: #28a745; }
        .dark-mode .dot--red { background-color: #dc3545; }
        .dark-mode .overview-item--c1,
        .dark-mode .overview-item--c2,
        .dark-mode .overview-item--c3,
        .dark-mode .overview-item--c4 {
            color: #fff; /* Ensure text is white on colored backgrounds */
        }

        .toggle-theme-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Styles for message items */
        .au-message__item.unread, .mess__item.unread {
            font-weight: bold;
            background-color: #e6f7ff; /* Light blue for unread messages */
        }
        .au-message__item:hover, .mess__item:hover {
            cursor: pointer;
            background-color: #f0f0f0; /* Lighter background on hover */
        }
        .au-message__item-text .text p, .mess__item .content p {
            margin-bottom: 0.2rem; /* Reduce space between subject and content preview */
            line-height: 1.2;
        }
        .au-message__item-text .text small, .mess__item .content small {
            font-size: 0.85em;
            color: #666;
        }
        /* Style for the modal content */
        .message-detail-modal .modal-body {
            white-space: pre-wrap; /* Preserve whitespace and line breaks */
        }
        .reply-form-section {
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const theme = localStorage.getItem('theme') || 'light';
        document.body.classList.add(`${theme}-mode`);
        // Update the button text initially
        const themeButton = document.getElementById('toggleThemeButton');
        if (themeButton) {
            themeButton.textContent = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    });

    function toggleTheme() {
        const isDark = document.body.classList.contains('dark-mode');
        document.body.classList.toggle('dark-mode', !isDark);
        document.body.classList.toggle('light-mode', isDark);
        localStorage.setItem('theme', isDark ? 'light' : 'dark');

        // Update the button text
        const themeButton = document.getElementById('toggleThemeButton');
        if (themeButton) {
            themeButton.textContent = isDark ? 'Switch to Dark Mode' : 'Switch to Light Mode';
        }
    }
</script>

<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <?php include_once 'templates/header_mobile_menu.php'; ?>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <?php include_once 'templates/side_menu.php'; ?>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <?php include_once 'templates/header_pc_menu.php'; ?>
            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">Dashboard Overview</h2>
                                    <a href="seller/create_sales_order.php" class="au-btn au-btn-icon au-btn--blue">
                                        <i class="zmdi zmdi-plus"></i>Add Sales Order</a>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-25">
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c1">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-chart-line"></i> <!-- Changed icon -->
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($total_sales_orders); ?></h2>
                                                <span>Total Sales Orders</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart1"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c2">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-cubes"></i> <!-- Changed icon -->
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($total_items_sold); ?></h2>
                                                <span>Items Sold (Confirmed)</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart2"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c3">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-calendar-alt"></i> <!-- Changed icon -->
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($sales_this_week); ?></h2>
                                                <span>Orders This Week</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart3"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="overview-item overview-item--c4">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-dollar-sign"></i> <!-- Changed icon -->
                                            </div>
                                            <div class="text">
                                                <h2>$<?php echo number_format($total_earnings, 2); ?></h2>
                                                <span>Total Revenue (Confirmed)</span>
                                            </div>
                                        </div>
                                        <div class="overview-chart">
                                            <canvas id="widgetChart4"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="au-card recent-report">
                                    <div class="au-card-inner">
                                        <h3 class="title-2">Monthly Sales Trend</h3>
                                        <div class="chart-info">
                                            <div class="chart-info__left">
                                                <div class="chart-note">
                                                    <span class="dot dot--blue"></span>
                                                    <span>Sales Amount</span>
                                                </div>
                                            </div>
                                            <div class="chart-info__right">
                                                <!-- You can add dynamic percentages here if you have a baseline -->
                                            </div>
                                        </div>
                                        <div class="recent-report__chart">
                                            <canvas id="recent-rep-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="au-card chart-percent-card">
                                    <div class="au-card-inner">
                                        <h3 class="title-2 tm-b-5">Revenue vs. Cost Overview</h3>
                                        <div class="row no-gutters">
                                            <div class="col-xl-6">
                                                <div class="chart-note-wrap">
                                                    <div class="chart-note mr-0 d-block">
                                                        <span class="dot" style="background-color: <?php echo $chart_percent_data['colors'][0]; ?>;"></span>
                                                        <span><?php echo $chart_percent_data['labels'][0]; ?></span>
                                                    </div>
                                                    <div class="chart-note mr-0 d-block">
                                                        <span class="dot" style="background-color: <?php echo $chart_percent_data['colors'][1]; ?>;"></span>
                                                        <span><?php echo $chart_percent_data['labels'][1]; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-6">
                                                <div class="percent-chart">
                                                    <canvas id="percent-chart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-9">
                                <h2 class="title-1 m-b-25">Recent Sales Items</h2>
                                <div class="table-responsive table--no-card m-b-40">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                            <tr>
                                                <th>Order Date</th>
                                                <th>Order ID</th>
                                                <th>Product Name</th>
                                                <th class="text-right">Unit Price</th>
                                                <th class="text-right">Quantity</th>
                                                <th class="text-right">Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_earnings_items)): ?>
                                                <?php foreach ($recent_earnings_items as $item): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($item['order_date']))); ?></td>
                                                        <td><a href="view_sales_order.php?order=<?php echo urlencode($item['order_number']); ?>"><?php echo htmlspecialchars($item['order_number']); ?></a></td>
                                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                        <td class="text-right">$<?php echo number_format($item['unit_price'], 2); ?></td>
                                                        <td class="text-right"><?php echo number_format($item['quantity']); ?></td>
                                                        <td class="text-right">$<?php echo number_format($item['total_price'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No recent sales items found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <h2 class="title-1 m-b-25">Sales By Country</h2>
                                <div class="au-card au-card--bg-blue au-card-top-countries m-b-40">
                                    <div class="au-card-inner">
                                        <div class="table-responsive">
                                            <table class="table table-top-countries">
                                                <tbody>
                                                    <?php if (!empty($top_countries)): ?>
                                                        <?php foreach ($top_countries as $country_data): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($country_data['country']); ?></td>
                                                                <td class="text-right">$<?php echo number_format($country_data['country_total_sales'], 2); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="2" class="text-center text-white">No country data.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Existing Executive Message Display Block -->
                            <div class="col-lg-6">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url('images/bg-title-02.jpg');">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-comment-text"></i>New Messages
                                        </h3>
                                        <a href="seller/send_message.php" class="au-btn-plus"> <!-- Link to send_message.php -->
                                            <i class="zmdi zmdi-plus"></i>
                                        </a>
                                    </div>
                                    <div class="au-inbox-wrap js-inbox-wrap">
                                        <div class="au-message js-list-load">
                                            <div class="au-message__noti">
                                                <p>You Have
                                                    <span><?= $new_messages_count ?></span>
                                                    new messages
                                                </p>
                                            </div>
                                            <div class="au-message-list" id="executiveMessageList">
                                                <?php if (!empty($recent_executive_messages)): ?>
                                                    <?php foreach ($recent_executive_messages as $message): ?>
                                                        <div class="au-message__item <?= $message['is_read'] == 0 ? 'unread' : '' ?>"
                                                             data-message-id="<?= htmlspecialchars($message['id']) ?>"
                                                             data-sender-id="<?= htmlspecialchars($message['sender_id']) ?>"
                                                             data-subject="<?= htmlspecialchars($message['subject']) ?>"
                                                             data-content="<?= htmlspecialchars($message['message_content']) ?>"
                                                             data-timestamp="<?= htmlspecialchars($message['timestamp']) ?>"
                                                             data-sender-name="<?= htmlspecialchars($message['sender_name']) ?>"
                                                             data-sender-role="<?= htmlspecialchars($message['sender_role']) ?>">
                                                            <div class="au-message__item-inner">
                                                                <div class="au-message__item-text">
                                                                    <div class="avatar-wrap">
                                                                        <div class="avatar">
                                                                            <!-- Using a generic placeholder image -->
                                                                            <img src="https://placehold.co/40x40/cccccc/333333?text=User" alt="<?= htmlspecialchars($message['sender_name']) ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="text">
                                                                        <h5 class="name"><?= htmlspecialchars($message['sender_name']) ?> (<?= htmlspecialchars(ucfirst($message['sender_role'])) ?>)</h5>
                                                                        <p><?= htmlspecialchars(substr($message['subject'], 0, 50)) ?><?= (strlen($message['subject']) > 50 ? '...' : '') ?></p>
                                                                        <small><?= htmlspecialchars(substr($message['message_content'], 0, 70)) ?><?= (strlen($message['message_content']) > 70 ? '...' : '') ?></small>
                                                                    </div>
                                                                </div>
                                                                <div class="au-message__item-time">
                                                                    <span><?= formatMessageTime($message['timestamp']) ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="text-center p-3 text-muted">No new messages.</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="au-message__footer">
                                                <!-- Link to a full inbox page for executives, or the send_message.php if that's the primary message center -->
                                                <a href="seller/send_message.php?tab=inbox" class="au-btn au-btn-load js-load-btn">load more</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Original Tasks for Executive Block (remains unchanged) -->
                            <div class="col-lg-6">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url('images/bg-title-01.jpg');">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-account-calendar"></i>Tasks for <?php echo $user_name; ?></h3>
                                        <button class="au-btn-plus">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="au-task js-list-load">
                                        <div class="au-task__title">
                                            <p>Your Recent Tasks</p>
                                        </div>
                                        <div class="au-task-list js-scrollbar3">
                                            <div class="au-task__item au-task__item--danger">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Review pending Sales Orders</a>
                                                    </h5>
                                                    <span class="time">Yesterday</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--warning">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Follow up on overdue Invoices</a>
                                                    </h5>
                                                    <span class="time">2 days ago</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--primary">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Check stock levels for low items</a>
                                                    </h5>
                                                    <span class="time">This Week</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--success">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Process new Purchase Orders</a>
                                                    </h5>
                                                    <span class="time">Today</span>
                                                </div>
                                            </div>
                                            <div class="au-task__item au-task__item--danger js-load-item">
                                                <div class="au-task__item-inner">
                                                    <h5 class="task">
                                                        <a href="#">Update product descriptions</a>
                                                    </h5>
                                                    <span class="time">Last Month</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="au-task__footer">
                                            <button class="au-btn au-btn-load js-load-btn">load more</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->
            <!-- END PAGE CONTAINER-->
        </div>

    </div>

    <!-- Message Detail Modal (for dashboard context) -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1" aria-labelledby="messageDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="messageDetailModalLabel">Message Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>From:</strong> <span id="modalSender"></span></p>
                    <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
                    <p><strong>Date:</strong> <span id="modalDate"></span></p>
                    <hr>
                    <p id="modalContent"></p>

                    <!-- Reply Form Section (initially hidden) -->
                    <div id="replyFormSection" class="reply-form-section" style="display:none;">
                        <h5>Reply to this message</h5>
                        <form id="messageReplyForm">
                            <input type="hidden" id="replyOriginalMessageId" name="original_message_id">
                            <input type="hidden" id="replyReceiverId" name="receiver_id">
                            <div class="mb-3">
                                <label for="replySubject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="replySubject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="replyContent" class="form-label">Your Reply</label>
                                <textarea class="form-control" id="replyContent" name="message_content" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Send Reply</button>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-info" id="toggleReplyFormBtn">Reply</button> <!-- Changed ID -->
                </div>
            </div>
        </div>
    </div>


    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script> -->
    <!-- Vendor JS-->
    <script src="vendor/slick/slick.min.js"></script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>

    <!-- Main JS-->
    <script src="../js/main.js"></script>

    <script>
        $(document).ready(function() {
            // Function to format time for display in JS (client-side)
            function formatTimeForDisplay(timestamp) {
                const messageTime = new Date(timestamp);
                const now = new Date();
                const diffSeconds = Math.floor((now.getTime() - messageTime.getTime()) / 1000);

                if (diffSeconds < 60) { // Less than 1 minute
                    return diffSeconds + " Sec ago";
                } else if (diffSeconds < 3600) { // Less than 1 hour
                    return Math.round(diffSeconds / 60) + " Min ago";
                } else if (diffSeconds < 86400) { // Less than 24 hours (today)
                    return messageTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                } else if (diffSeconds < 172800) { // Less than 48 hours (yesterday)
                    return "Yesterday";
                } else { // Older than yesterday
                    return messageTime.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
                }
            }

            // Function to display a Bootstrap alert message (reused from send_message.php)
            function displayMessage(type, text) {
                const container = $('#systemMessageContainer');
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${text}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                container.append(alertHtml);
                // Optionally, auto-dismiss after a few seconds
                setTimeout(function() {
                    container.find('.alert').alert('close');
                }, 5000); // Close after 5 seconds
            }

            // Event listener for clicking on a message item (both main block and header dropdown)
            // Use a common class for both types of message items if they share behavior
            $(document).on('click', '.au-message__item, .mess__item', function() {
                const messageElement = $(this);
                const messageId = messageElement.data('message-id');
                const senderId = messageElement.data('sender-id');
                const subject = messageElement.data('subject');
                const content = messageElement.data('content');
                const timestamp = messageElement.data('timestamp');
                const senderName = messageElement.data('sender-name');
                const senderRole = messageElement.data('sender-role');

                // Populate modal with message details
                $('#modalSender').text(`${senderName} (${senderRole.charAt(0).toUpperCase() + senderRole.slice(1)})`);
                $('#modalSubject').text(subject);
                $('#modalDate').text(formatTimeForDisplay(timestamp));
                $('#modalContent').text(content);

                // Set data for reply form
                $('#replyOriginalMessageId').val(messageId);
                $('#replyReceiverId').val(senderId);
                $('#replySubject').val(`Re: ${subject}`);
                $('#replyContent').val(''); // Clear previous reply content

                // Hide reply form initially when modal opens
                $('#replyFormSection').hide();
                $('#toggleReplyFormBtn').text('Reply'); // Reset button text

                // Mark message as read via AJAX if it's currently unread
                if (messageElement.hasClass('unread')) {
                    $.ajax({
                        url: '../seller/mark_message_read_ajax.php', // Correct path
                        type: 'POST',
                        data: { message_id: messageId },
                        success: function(response) {
                            if (response.status === 'success') {
                                messageElement.removeClass('unread'); // Remove unread styling
                                // Update unread count in both places
                                $('.au-message__noti span').each(function() {
                                    let currentCount = parseInt($(this).text());
                                    if (!isNaN(currentCount) && currentCount > 0) {
                                        $(this).text(currentCount - 1);
                                    }
                                });
                                $('.noti__item .quantity').each(function() {
                                    let currentCount = parseInt($(this).text());
                                    if (!isNaN(currentCount) && currentCount > 0) {
                                        $(this).text(currentCount - 1);
                                    }
                                });
                                $('.mess__title p').each(function() {
                                    let currentText = $(this).text();
                                    let currentCount = parseInt(currentText.match(/\d+/));
                                    if (!isNaN(currentCount) && currentCount > 0) {
                                        $(this).text(currentText.replace(currentCount, currentCount - 1));
                                    }
                                });

                            } else {
                                console.error("Failed to mark message as read:", response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error marking message as read: ", status, error);
                        }
                    });
                }

                // Show the modal
                const messageDetailModal = new bootstrap.Modal(document.getElementById('messageDetailModal'));
                messageDetailModal.show();
            });

            // Toggle Reply Form visibility
            $('#toggleReplyFormBtn').on('click', function() {
                $('#replyFormSection').slideToggle(function() {
                    if ($(this).is(':visible')) {
                        $('#toggleReplyFormBtn').text('Hide Reply Form');
                        $('#replyContent').focus(); // Focus on textarea when shown
                    } else {
                        $('#toggleReplyFormBtn').text('Reply');
                    }
                });
            });

            // Handle Reply Form Submission via AJAX
            $('#messageReplyForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const formData = {
                    original_message_id: $('#replyOriginalMessageId').val(),
                    receiver_id: $('#replyReceiverId').val(),
                    subject: $('#replySubject').val(),
                    message_content: $('#replyContent').val()
                };

                $.ajax({
                    url: '../seller/send_reply_ajax.php', // New backend file for sending replies
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            displayMessage('success', response.message);
                            // Optionally, refresh the messages in the main dashboard block
                            // (This would require a new AJAX call to fetchMessages function if you want to update it live)
                            // For now, we'll just close the modal and show a success message.
                            const messageDetailModal = bootstrap.Modal.getInstance(document.getElementById('messageDetailModal'));
                            messageDetailModal.hide();
                            // Clear form after successful send
                            $('#messageReplyForm')[0].reset();
                            $('#replyOriginalMessageId').val('');
                            $('#replyReceiverId').val('');

                        } else {
                            displayMessage('danger', response.message || 'Failed to send reply.');
                        }
                    },
                    error: function(xhr, status, error) {
                        displayMessage('danger', 'An error occurred while sending the reply.');
                        console.error("AJAX Error sending reply: ", status, error, xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>
