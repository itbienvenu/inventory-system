<?php
// session_start(); // Assuming session_start() is handled by auth.php
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary
include_once "../helpers/auth_helper.php"; // Include the auth helper

// Only 'executive' and 'admin' roles can access this page
$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}
$id = $_SESSION['user_id'];
include_once 'messsage_functions.php';
// 1. Monthly Sales Trend (for current year)
$current_year = date('Y');
$monthly_sales_query = mysqli_query($conn, "
    SELECT
        DATE_FORMAT(order_date, '%Y-%m') AS sale_month,
        SUM(total_amount) AS monthly_sales
    FROM sales_orders
    WHERE YEAR(order_date) = $current_year AND status IN ('shipped', 'completed', 'invoiced', 'paid') -- Adjust statuses as per your sales order flow
    GROUP BY sale_month
    ORDER BY sale_month ASC
");

$sales_labels = [];
$sales_data = [];
$sales_by_month = [];
if ($monthly_sales_query) {
    while ($row = mysqli_fetch_assoc($monthly_sales_query)) {
        $sales_by_month[$row['sale_month']] = $row['monthly_sales'];
    }
}

// Populate all 12 months, even if no sales
for ($m = 1; $m <= 12; $m++) {
    $month_key = $current_year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
    $sales_labels[] = date('M', mktime(0, 0, 0, $m, 1)); // e.g., Jan, Feb
    $sales_data[] = $sales_by_month[$month_key] ?? 0;
}


// 2. Stock by Category (Doughnut Chart)
$stock_by_category_query = mysqli_query($conn, "
    SELECT
        category,
        SUM(quantity) AS total_quantity
    FROM products
    WHERE quantity > 0 AND category IS NOT NULL AND category != ''
    GROUP BY category
    ORDER BY total_quantity DESC
");

$category_labels = [];
$category_data = [];
if ($stock_by_category_query) {
    while ($row = mysqli_fetch_assoc($stock_by_category_query)) {
        $category_labels[] = $row['category'];
        $category_data[] = $row['total_quantity'];
    }
}


// 3. Top 5 Selling Products (Bar Chart - last 90 days)
$top_selling_products_query = mysqli_query($conn, "
    SELECT
        p.name AS product_name,
        SUM(soi.quantity) AS total_sold_quantity
    FROM sales_order_items soi
    JOIN sales_orders so ON soi.id = so.id -- Corrected JOIN condition
    JOIN products p ON soi.product_id = p.id
    WHERE so.order_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
    AND so.status IN ('shipped', 'completed', 'invoiced', 'paid') -- Only confirmed sales
    GROUP BY p.name
    ORDER BY total_sold_quantity DESC
    LIMIT 5
");

$top_products_labels = [];
$top_products_data = [];
if ($top_selling_products_query) {
    while ($row = mysqli_fetch_assoc($top_selling_products_query)) {
        $top_products_labels[] = $row['product_name'];
        $top_products_data[] = $row['total_sold_quantity'];
    }
}


// 4. Monthly Stock Movement (Line Chart - Inbound vs. Outbound for current year)
$monthly_movement_query = mysqli_query($conn, "
    SELECT
        DATE_FORMAT(movement_timestamp, '%Y-%m') AS movement_month,
        SUM(CASE WHEN movement_type LIKE 'inbound%' OR movement_type LIKE 'adjustment_add' THEN quantity_change ELSE 0 END) AS inbound_qty,
        SUM(CASE WHEN movement_type LIKE 'outbound%' OR movement_type LIKE 'adjustment_deduct' THEN ABS(quantity_change) ELSE 0 END) AS outbound_qty
    FROM stock_movements
    WHERE YEAR(movement_timestamp) = $current_year
    GROUP BY movement_month
    ORDER BY movement_month ASC
");

$movement_labels = [];
$inbound_data = [];
$outbound_data = [];
$movement_by_month = [];

if ($monthly_movement_query) {
    while ($row = mysqli_fetch_assoc($monthly_movement_query)) {
        $movement_by_month[$row['movement_month']] = [
            'inbound' => $row['inbound_qty'],
            'outbound' => $row['outbound_qty']
        ];
    }
}

// Populate all 12 months for movements
for ($m = 1; $m <= 12; $m++) {
    $month_key = $current_year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
    $movement_labels[] = date('M', mktime(0, 0, 0, $m, 1));
    $inbound_data[] = $movement_by_month[$month_key]['inbound'] ?? 0;
    $outbound_data[] = $movement_by_month[$month_key]['outbound'] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Inventory Management System Charts">
    <meta name="author" content="Your Name/Company Name">
    <meta name="keywords" content="charts, inventory, sales, stock, reports">

    <!-- Title Page-->
    <title>Charts - Executive Overview</title>

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

    <!-- Chart.js (Updated to latest version for Chart.getChart compatibility) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

    <style>
        /* Dark mode adjustments */
        .dark-mode body { background-color: #2c2c2c; color: #f0f0f0; }
        .dark-mode .page-wrapper { background-color: #2c2c2c; }
        .dark-mode .header-desktop, .dark-mode .aside-wrap .aside-menu { background-color: #3a3a3a; }
        .dark-mode .au-card { background-color: #4a4a4a; color: #f0f0f0; }
        .dark-mode .au-card-title { background-image: none !important; background-color: #343a40; }
        .dark-mode .table-responsive table th,
        .dark-mode .table-responsive table td {
            background-color: #4a4a4a;
            color: #f0f0f0;
            border-color: #666;
        }
        .dark-mode .table-responsive table.table-earning thead th {
             background-color: #343a40;
        }
        .dark-mode .au-btn-icon { background-color: #0d6efd; color: white; }
        .dark-mode .au-btn-icon i { color: white; }

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

        /* Added styles for chart canvases to control maximum height */
        #monthlySalesChart,
        #stockByCategoryChart,
        #topSellingProductsChart,
        #monthlyStockMovementChart {
            max-height: 350px; /* Adjust this value as needed for desired chart height */
            width: 100% !important; /* Ensure it takes full width of its container */
            height: auto !important; /* Allow height to adjust proportionally */
        }
    </style>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const theme = localStorage.getItem('theme') || 'light';
        document.body.classList.add(`${theme}-mode`);
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
                                    <h2 class="title-1">Executive Charts Overview</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="au-card m-b-30">
                                    <div class="au-card-inner">
                                        <h3 class="title-2 m-b-40">Monthly Sales Trend (<?php echo $current_year; ?>)</h3>
                                        <canvas id="monthlySalesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="au-card m-b-30">
                                    <div class="au-card-inner">
                                        <h3 class="title-2 m-b-40">Stock Quantity by Category</h3>
                                        <canvas id="stockByCategoryChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="au-card m-b-30">
                                    <div class="au-card-inner">
                                        <h3 class="title-2 m-b-40">Top 5 Selling Products (Last 90 Days)</h3>
                                        <canvas id="topSellingProductsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="au-card m-b-30">
                                    <div class="au-card-inner">
                                        <h3 class="title-2 m-b-40">Monthly Stock Movements (<?php echo $current_year; ?>)</h3>
                                        <canvas id="monthlyStockMovementChart"></canvas>
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
    <script src="vendor/slick/slick.min.js"></script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.js"></script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/select2/select2.min.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

    <script>
        $(document).ready(function() {
            // Function to destroy existing chart instance if it exists
            function destroyChart(chartId) {
                const chartCanvas = document.getElementById(chartId);
                // Chart.getChart is available in Chart.js v3+
                const existingChart = Chart.getChart(chartCanvas); 
                if (existingChart) {
                    existingChart.destroy();
                    console.log(`Destroyed existing Chart.js instance on #${chartId}.`);
                }
            }

            // --- Monthly Sales Trend Chart ---
            destroyChart("monthlySalesChart"); // Destroy before creating
            var salesLabels = <?php echo json_encode($sales_labels); ?>;
            var salesData = <?php echo json_encode($sales_data); ?>;
            var ctxSales = document.getElementById("monthlySalesChart");
            if (ctxSales) {
                window.monthlySalesChartInstance = new Chart(ctxSales, {
                    type: 'line',
                    data: {
                        labels: salesLabels,
                        datasets: [{
                            label: "Sales Amount ($)",
                            backgroundColor: "rgba(0, 123, 255, 0.5)", // Blue
                            borderColor: "rgba(0, 123, 255, 1)",
                            pointBackgroundColor: "rgba(0, 123, 255, 1)",
                            pointBorderColor: "#fff",
                            pointHoverBackgroundColor: "#fff",
                            pointHoverBorderColor: "rgba(0, 123, 255, 1)",
                            data: salesData,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { // Chart.js v3+ uses 'plugins' object for title, legend, tooltips
                            title: {
                                display: false,
                                text: 'Monthly Sales Trend'
                            },
                            tooltip: { // 'tooltips' is now 'tooltip'
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(tooltipItem) { // tooltipItem structure changed slightly
                                        return tooltipItem.dataset.label + ': $' + tooltipItem.parsed.y.toFixed(2);
                                    }
                                }
                            },
                            legend: {
                                display: true,
                                labels: {
                                    color: 'rgba(0,0,0,0.8)' // 'fontColor' is now 'color'
                                }
                            }
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: { // 'xAxes' and 'yAxes' are now 'x' and 'y'
                            x: {
                                display: true,
                                title: { // 'scaleLabel' is now 'title'
                                    display: true,
                                    text: 'Month'
                                },
                                ticks: {
                                    color: 'rgba(0,0,0,0.8)' // 'fontColor' is now 'color'
                                }
                            },
                            y: {
                                display: true,
                                title: { // 'scaleLabel' is now 'title'
                                    display: true,
                                    text: 'Amount ($)'
                                },
                                ticks: {
                                    beginAtZero: true,
                                    callback: function(value) {
                                        return '$' + value;
                                    },
                                    color: 'rgba(0,0,0,0.8)' // 'fontColor' is now 'color'
                                }
                            }
                        }
                    }
                });
            }

            // --- Stock Quantity by Category Chart ---
            destroyChart("stockByCategoryChart"); // Destroy before creating
            var categoryLabels = <?php echo json_encode($category_labels); ?>;
            var categoryData = <?php echo json_encode($category_data); ?>;
            var ctxCategory = document.getElementById("stockByCategoryChart");
            if (ctxCategory) {
                window.stockByCategoryChartInstance = new Chart(ctxCategory, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: categoryData,
                            backgroundColor: [
                                "rgba(255, 99, 132, 0.7)", // Red
                                "rgba(54, 162, 235, 0.7)", // Blue
                                "rgba(255, 206, 86, 0.7)", // Yellow
                                "rgba(75, 192, 192, 0.7)", // Green
                                "rgba(153, 102, 255, 0.7)", // Purple
                                "rgba(255, 159, 64, 0.7)", // Orange
                                "rgba(199, 199, 199, 0.7)" // Grey
                            ],
                            hoverBackgroundColor: [
                                "rgba(255, 99, 132, 1)",
                                "rgba(54, 162, 235, 1)",
                                "rgba(255, 206, 86, 1)",
                                "rgba(75, 192, 192, 1)",
                                "rgba(153, 102, 255, 1)",
                                "rgba(255, 159, 64, 1)",
                                "rgba(199, 199, 199, 1)"
                            ],
                            borderWidth: 0
                        }],
                        labels: categoryLabels
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%', // 'cutoutPercentage' is now 'cutout' and takes string percentage
                        plugins: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    color: 'rgba(0,0,0,0.8)' // 'fontColor' is now 'color'
                                }
                            },
                            tooltip: { // 'tooltips' is now 'tooltip'
                                callbacks: {
                                    label: function(tooltipItem) { // tooltipItem structure changed slightly
                                        var label = tooltipItem.label;
                                        var value = tooltipItem.parsed;
                                        return label + ': ' + value + ' units';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // --- Top 5 Selling Products Chart ---
            destroyChart("topSellingProductsChart"); // Destroy before creating
            var topProductsLabels = <?php echo json_encode($top_products_labels); ?>;
            var topProductsData = <?php echo json_encode($top_products_data); ?>;
            var ctxTopProducts = document.getElementById("topSellingProductsChart");
            if (ctxTopProducts) {
                window.topSellingProductsChartInstance = new Chart(ctxTopProducts, {
                    type: 'bar',
                    data: {
                        labels: topProductsLabels,
                        datasets: [{
                            label: "Quantity Sold",
                            backgroundColor: "rgba(40, 167, 69, 0.7)", // Green
                            borderColor: "rgba(40, 167, 69, 1)",
                            borderWidth: 1,
                            data: topProductsData
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: { // 'tooltips' is now 'tooltip'
                                callbacks: {
                                    label: function(tooltipItem) { // tooltipItem structure changed slightly
                                        return tooltipItem.dataset.label + ': ' + tooltipItem.parsed.y + ' units';
                                    }
                                }
                            }
                        },
                        scales: { // 'xAxes' and 'yAxes' are now 'x' and 'y'
                            x: {
                                ticks: {
                                    beginAtZero: true,
                                    color: 'rgba(0,0,0,0.8)' // 'fontColor' is now 'color'
                                }
                            },
                            y: {
                                ticks: {
                                    beginAtZero: true,
                                    color: 'rgba(0,0,0,0.8)' // 'fontColor' is now 'color'
                                }
                            }
                        }
                    }
                });
            }

            // --- Monthly Stock Movement Chart ---
            destroyChart("monthlyStockMovementChart"); // Destroy before creating
            var movementLabels = <?php echo json_encode($movement_labels); ?>;
            var inboundData = <?php echo json_encode($inbound_data); ?>;
            var outboundData = <?php echo json_encode($outbound_data); ?>;
            var ctxMovement = document.getElementById("monthlyStockMovementChart");
            if (ctxMovement) {
                window.monthlyStockMovementChartInstance = new Chart(ctxMovement, {
                    type: 'line',
                    data: {
                        labels: movementLabels,
                        datasets: [{
                            label: "Inbound Quantity",
                            backgroundColor: "rgba(40, 167, 69, 0.2)", // Green transparent
                            borderColor: "rgba(40, 167, 69, 1)",
                            pointBackgroundColor: "rgba(40, 167, 69, 1)",
                            pointBorderColor: "#fff",
                            data: inboundData,
                            fill: true,
                        }, {
                            label: "Outbound Quantity",
                            backgroundColor: "rgba(220, 53, 69, 0.2)", // Red transparent
                            borderColor: "rgba(220, 53, 69, 1)",
                            pointBackgroundColor: "rgba(220, 53, 69, 1)",
                            pointBorderColor: "#fff",
                            data: outboundData,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: false,
                                text: 'Monthly Stock Movements'
                            },
                            tooltip: { // 'tooltips' is now 'tooltip'
                                mode: 'index',
                                intersect: false,
                            },
                            legend: {
                                display: true,
                                labels: {
                                    color: 'rgba(0,0,0,0.8)' // Default for light mode
                                }
                            }
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: { // 'xAxes' and 'yAxes' are now 'x' and 'y'
                            x: {
                                display: true,
                                title: { // 'scaleLabel' is now 'title'
                                    display: true,
                                    text: 'Month'
                                },
                                ticks: {
                                    color: 'rgba(0,0,0,0.8)' // Default for light mode
                                }
                            },
                            y: {
                                display: true,
                                title: { // 'scaleLabel' is now 'title'
                                    display: true,
                                    text: 'Quantity'
                                },
                                ticks: {
                                    beginAtZero: true,
                                    color: 'rgba(0,0,0,0.8)' // Default for light mode
                                }
                            }
                        }
                    }
                });
            }

            // Adjust font colors for dark mode dynamically
            function updateChartFontColors() {
                const isDark = document.body.classList.contains('dark-mode');
                const fontColor = isDark ? 'rgba(255,255,255,0.8)' : 'rgba(0,0,0,0.8)';

                // Update Monthly Sales Chart
                if (window.monthlySalesChartInstance) {
                    window.monthlySalesChartInstance.options.scales.x.ticks.color = fontColor;
                    window.monthlySalesChartInstance.options.scales.y.ticks.color = fontColor;
                    window.monthlySalesChartInstance.update();
                }
                // Update Stock by Category Chart
                if (window.stockByCategoryChartInstance) {
                    window.stockByCategoryChartInstance.options.plugins.legend.labels.color = fontColor;
                    window.stockByCategoryChartInstance.update();
                }
                // Update Top Selling Products Chart
                if (window.topSellingProductsChartInstance) {
                    window.topSellingProductsChartInstance.options.scales.x.ticks.color = fontColor;
                    window.topSellingProductsChartInstance.options.scales.y.ticks.color = fontColor;
                    window.topSellingProductsChartInstance.update();
                }
                // Update Monthly Stock Movement Chart
                if (window.monthlyStockMovementChartInstance) {
                    window.monthlyStockMovementChartInstance.options.scales.x.ticks.color = fontColor;
                    window.monthlyStockMovementChartInstance.options.scales.y.ticks.color = fontColor;
                    window.monthlyStockMovementChartInstance.update();
                }
            }

            // Call updateChartFontColors on theme toggle
            const toggleThemeButton = document.getElementById('toggleThemeButton');
            if (toggleThemeButton) {
                toggleThemeButton.addEventListener('click', function() {
                    toggleTheme(); // Call your existing toggleTheme function
                    updateChartFontColors(); // Update chart colors after theme changes
                });
            }

            // Initial font color update on load
            updateChartFontColors();
        });
    </script>

</body>

</html>
