<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");

if(isset($_SESSION['role']) && isset($_SESSION['user_id'])){
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
    // Products vs. Services is usually a category, which we don't have.
    // So, let's represent Sales vs. Cost of Goods or just a general "Profitability" metric
    // If total_sales_all is 0, avoid division by zero.
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
    <link href="css/font-face.css" rel="stylesheet" media="all">
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
    <link href="vendor/chartjs/Chart.bundle.min.js" rel="stylesheet" media="all"> <!-- This is likely JS, not CSS -->


    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

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
                                    <a href="create_sales_order.php" class="au-btn au-btn-icon au-btn--blue">
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
                            <div class="col-lg-6">
                                <div class="au-card au-card--no-shadow au-card--no-pad m-b-40">
                                    <div class="au-card-title" style="background-image:url('images/bg-title-02.jpg');">
                                        <div class="bg-overlay bg-overlay--blue"></div>
                                        <h3>
                                            <i class="zmdi zmdi-comment-text"></i>New Messages</h3>
                                        <button class="au-btn-plus">
                                            <i class="zmdi zmdi-plus"></i>
                                        </button>
                                    </div>
                                    <div class="au-inbox-wrap js-inbox-wrap">
                                        <div class="au-message js-list-load">
                                            <div class="au-message__noti">
                                                <p>You Have
                                                    <span>2</span>
                                                    new messages
                                                </p>
                                            </div>
                                            <div class="au-message-list">
                                                <div class="au-message__item unread">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-02.jpg" alt="John Smith">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">John Smith</h5>
                                                                <p>Have sent a photo</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>12 Min ago</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item unread">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap online">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-03.jpg" alt="Nicholas Martinez">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Nicholas Martinez</h5>
                                                                <p>You are now connected on message</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>11:00 PM</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap online">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-04.jpg" alt="Michelle Sims">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Michelle Sims</h5>
                                                                <p>Lorem ipsum dolor sit amet</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>Yesterday</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="au-message__item">
                                                    <div class="au-message__item-inner">
                                                        <div class="au-message__item-text">
                                                            <div class="avatar-wrap">
                                                                <div class="avatar">
                                                                    <img src="images/icon/avatar-05.jpg" alt="Michelle Sims">
                                                                </div>
                                                            </div>
                                                            <div class="text">
                                                                <h5 class="name">Michelle Sims</h5>
                                                                <p>Donec eget augue dapibus</p>
                                                            </div>
                                                        </div>
                                                        <div class="au-message__item-time">
                                                            <span>Yesterday</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="au-message__footer">
                                                <button class="au-btn au-btn-load js-load-btn">load more</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © 2018 Colorlib. All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MAIN CONTENT-->
        </div>
        <!-- END PAGE CONTAINER-->

        <button class="toggle-theme-btn" id="toggleThemeButton" onclick="toggleTheme()">Switch to Dark Mode</button>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

    <script>
        // Data from PHP
        const monthlySalesData = <?php echo json_encode($monthly_sales_data); ?>;
        const monthlySalesLabels = <?php echo json_encode($months); ?>;
        const chartPercentData = <?php echo json_encode($chart_percent_data); ?>;

        // Widget Chart 1-4 (simple bar/line for overview, can be simplified or made more meaningful)
        // For demonstration, I'll use simple static data for these unless real trends are explicitly requested.
        // Or, we can just remove the canvas elements from these overview-items if they are not dynamically populated.
        // Given the existing Chart.bundle.min.js, I will ensure these are initialized properly.
        // For now, let's keep them as dummy charts or remove the canvas if no meaningful data can be shown.
        // I will make them dummy charts for now, as fetching more real-time data for such small charts might be overkill.

        (function ($) {
            //widgetChart1
            try {
                var ctx = document.getElementById("widgetChart1");
                if (ctx) {
                    ctx.height = 120;
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            datasets: [
                                {
                                    label: 'Sales Orders',
                                    tension: 0.3,
                                    fill: true,
                                    backgroundColor: 'rgba(255,255,255,.1)',
                                    borderColor: 'rgba(255,255,255,.5)',
                                    borderWidth: 3,
                                    pointBorderColor: 'transparent',
                                    pointBackgroundColor: 'transparent',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#fff',
                                    pointHitRadius: 10,
                                    pointRadius: 0,
                                    data: [5, 10, 8, 12, 11, 15, 13] // Dummy data for trend
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 0,
                                    right: 0,
                                    top: 0,
                                    bottom: 0
                                }
                            },
                            scales: {
                                xAxis: {
                                    display: false,
                                    type: 'category', // For older Chart.js
                                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] // For older Chart.js
                                },
                                yAxis: {
                                    display: false,
                                    min: 0,
                                    max: 20 // Adjusted max
                                }
                            },
                            elements: {
                                point: {
                                    radius: 0
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: { // Tooltip for older Chart.js
                                    enabled: false
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.log(error);
            }

            //widgetChart2
            try {
                var ctx = document.getElementById("widgetChart2");
                if (ctx) {
                    ctx.height = 120;
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                            datasets: [
                                {
                                    label: 'Items Sold',
                                    data: [1000, 1500, 1200, 1800, 1400, 2000, 1600], // Dummy data
                                    backgroundColor: 'rgba(255,255,255,.1)'
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 0,
                                    right: 0,
                                    top: 0,
                                    bottom: 0
                                }
                            },
                            scales: {
                                xAxis: {
                                    display: false,
                                    type: 'category', // For older Chart.js
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'] // For older Chart.js
                                },
                                yAxis: {
                                    display: false,
                                    min: 0,
                                    max: 2500
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: false
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.log(error);
            }

            //widgetChart3
            try {
                var ctx = document.getElementById("widgetChart3");
                if (ctx) {
                    ctx.height = 120;
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                            datasets: [
                                {
                                    label: 'Orders',
                                    tension: 0.3,
                                    fill: true,
                                    backgroundColor: 'rgba(255,255,255,.1)',
                                    borderColor: 'rgba(255,255,255,.5)',
                                    borderWidth: 3,
                                    pointBorderColor: 'transparent',
                                    pointBackgroundColor: 'transparent',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#fff',
                                    pointHitRadius: 10,
                                    pointRadius: 0,
                                    data: [2, 5, 3, 7, 6, 8, 5] // Dummy data for weekly orders
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 0,
                                    right: 0,
                                    top: 0,
                                    bottom: 0
                                }
                            },
                            scales: {
                                xAxis: {
                                    display: false,
                                    type: 'category', // For older Chart.js
                                    labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'] // For older Chart.js
                                },
                                yAxis: {
                                    display: false,
                                    min: 0,
                                    max: 10
                                }
                            },
                            elements: {
                                point: {
                                    radius: 0
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: false
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.log(error);
            }

            //widgetChart4
            try {
                var ctx = document.getElementById("widgetChart4");
                if (ctx) {
                    ctx.height = 120;
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                            datasets: [
                                {
                                    label: 'Earnings',
                                    data: [50000, 75000, 60000, 80000], // Dummy data
                                    backgroundColor: 'rgba(255,255,255,.1)'
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 0,
                                    right: 0,
                                    top: 0,
                                    bottom: 0
                                }
                            },
                            scales: {
                                xAxis: {
                                    display: false,
                                    type: 'category', // For older Chart.js
                                    labels: ['Q1', 'Q2', 'Q3', 'Q4'] // For older Chart.js
                                },
                                yAxis: {
                                    display: false,
                                    min: 0,
                                    max: 100000
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: false
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.log(error);
            }

            // Recent Report Chart (Line Chart for Monthly Sales)
            try {
                var ctx = document.getElementById("recent-rep-chart");
                if (ctx) {
                    ctx.height = 250; // Adjust height as needed
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: monthlySalesLabels,
                            datasets: [{
                                label: 'Total Sales',
                                fill: true,
                                backgroundColor: 'rgba(13, 110, 253, 0.2)', // blue color with transparency
                                borderColor: 'rgba(13, 110, 253, 1)',
                                borderWidth: 2,
                                pointBorderColor: '#fff',
                                pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                data: monthlySalesData,
                                spanGaps: true // to handle null values if any
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{ // For older Chart.js
                                    gridLines: {
                                        drawOnChartArea: false,
                                        color: "#ccc"
                                    },
                                    ticks: {
                                        fontFamily: "Poppins",
                                        fontColor: "#6c757d"
                                    }
                                }],
                                yAxes: [{ // For older Chart.js
                                    ticks: {
                                        beginAtZero: true,
                                        maxTicksLimit: 5,
                                        stepSize: 2000, // Adjust step size based on expected data range
                                        fontFamily: "Poppins",
                                        fontColor: "#6c757d",
                                        callback: function(value, index, values) {
                                            return '$' + value.toLocaleString(); // Format as currency
                                        }
                                    },
                                    gridLines: {
                                        color: "rgba(0, 0, 0, 0.05)"
                                    }
                                }]
                            },
                            tooltips: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += '$' + tooltipItem.yLabel.toLocaleString();
                                        return label;
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.log(error);
            }


            // Percent Chart (Doughnut Chart for Revenue vs. Cost)
            try {
                var ctx = document.getElementById("percent-chart");
                if (ctx) {
                    ctx.height = 280; // Adjust height as needed
                    var myChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: chartPercentData.labels,
                            datasets: [{
                                data: chartPercentData.data,
                                backgroundColor: chartPercentData.colors,
                                hoverBackgroundColor: chartPercentData.colors, // Same for hover for simplicity
                                borderWidth: [0, 0]
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            rotation: -0.2 * Math.PI, // Start angle for a cleaner look
                            legend: {
                                display: false
                            },
                            cutoutPercentage: 70, // Make it a doughnut chart
                            tooltips: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(tooltipItem, data) {
                                        var label = data.labels[tooltipItem.index] || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                        return label + '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.log(error);
            }

        })(jQuery);
    </script>

</body>
</html>

<?php } else {
    // Redirect to login page if not authenticated
    header("Location: login.php");
    exit();
}
?>
