<?php
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary
$allowed_roles = ['admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}
include_once '../executive/messsage_functions.php';

if (isset($_SESSION['role']) && isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    // You can fetch some summary data for the report cards here if needed,
// similar to what we did for the main dashboard or inventory management hub.
// For now, we'll just set up the structure.

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Required meta tags-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Inventory Management System Reports">
        <meta name="author" content="Your Name/Company Name">
        <meta name="keywords" content="reports, inventory, sales, purchase, stock">

        <!-- Title Page-->
        <title>Reports</title>

        <!-- Fontfaces CSS-->
        <link href="../css/font-face.css" rel="stylesheet" media="all">
        <link href="../vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
        <link href="../vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
        <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

        <!-- Bootstrap CSS-->
        <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

        <!-- Vendor CSS-->
        <link href="../vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
        <link href="../vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
        <link href="../vendor/wow/animate.css" rel="stylesheet" media="all">
        <link href="../vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
        <link href="../vendor/slick/slick.css" rel="stylesheet" media="all">
        <link href="../vendor/select2/select2.min.css" rel="stylesheet" media="all">
        <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

        <!-- Main CSS-->
        <link href="../css/theme.css" rel="stylesheet" media="all">

        <style>
            /* Custom styles for report cards, consistent with overview items */
            .report-card-item {
                border-radius: 8px;
                overflow: hidden;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
                margin-bottom: 25px;
                position: relative;
                
                /* Taller for more info/actions */
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 20px;
                text-align: center;
            }

            .report-card-item .icon {
                font-size: 48px;
                color: #007bff;
                /* Primary blue for icons */
                margin-bottom: 10px;
            }

            .report-card-item h3 {
                font-size: 1.5rem;
                margin-bottom: 15px;
                color: #333;
            }

            .report-card-item .description {
                font-size: 0.9rem;
                color: #666;
                margin-bottom: 15px;
            }

            .report-card-item .actions a.btn {
                background-color: #007bff;
                border: none;
                color: white;
                padding: 10px 20px;
                border-radius: 50rem;
                text-decoration: none;
                transition: background-color 0.3s ease;
            }

            .report-card-item .actions a.btn:hover {
                background-color: #0056b3;
            }

            /* Specific colors for different report types if desired */
            .report-card-item.inventory .icon {
                color: #28a745;
            }

            /* Green */
            .report-card-item.sales .icon {
                color: #0d6efd;
            }

            /* Blue */
            .report-card-item.purchase .icon {
                color: #ffc107;
            }

            /* Yellow */
            .report-card-item.logs .icon {
                color: #6c757d;
            }

            /* Grey */

            /* Dark mode adjustments */
            .dark-mode body {
                background-color: #2c2c2c;
                color: #f0f0f0;
            }

            .dark-mode .page-wrapper {
                background-color: #2c2c2c;
            }

            .dark-mode .header-desktop,
            .dark-mode .aside-wrap .aside-menu {
                background-color: #3a3a3a;
            }

            .dark-mode .au-card {
                background-color: #4a4a4a;
                color: #f0f0f0;
            }

            .dark-mode .report-card-item {
                background-color: #4a4a4a;
            }

            .dark-mode .report-card-item h3 {
                color: #f0f0f0;
            }

            .dark-mode .report-card-item .description {
                color: #ccc;
            }

            .dark-mode .au-btn-icon {
                background-color: #0d6efd;
                color: white;
            }

            .dark-mode .au-btn-icon i {
                color: white;
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
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
            <?php include_once '../executive/templates/header_mobile_menu.php'; ?>
            <!-- END HEADER MOBILE-->

            <!-- MENU SIDEBAR-->
            <?php include_once '../executive/templates/side_menu.php'; ?>
            <!-- END MENU SIDEBAR-->

            <!-- PAGE CONTAINER-->
            <div class="page-container">
                <!-- HEADER DESKTOP-->
                <?php include_once '../executive/templates/header_pc_menu.php'; ?>
                <!-- HEADER DESKTOP-->

                <!-- MAIN CONTENT-->
                <div class="main-content">
                    <div class="section__content section__content--p30">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="overview-wrap">
                                        <h2 class="title-1">Reports Hub</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-25">
                                <!-- Use management and roles -->
                                 
                                <div class="col-sm-6 col-lg-4">
                                    <div class="report-card-item inventory">
                                        <div class="icon"><i class="fas fa-user"></i></div>
                                        <h3>Users Management</h3>
                                        <div class="description">
                                            Manage user roles and permissions effectively.
                                        </div>
                                        <div class="actions">
                                            <a href="../reports/user_managment_and_working.php" class="btn btn-primary">View Report</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Inventory Valuation Report Card -->
                                <div class="col-sm-6 col-lg-4">
                                    <div class="report-card-item inventory">
                                        <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                                        <h3>Inventory Valuation Report</h3>
                                        <div class="description">
                                            View the total value of your current stock based on cost and selling prices.
                                        </div>
                                        <div class="actions">
                                            <a href="../reports/inventory_valuation_report.php" class="btn btn-primary">View Report</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stock Movement Summary Report Card -->
                                <div class="col-sm-6 col-lg-4">
                                    <div class="report-card-item logs">
                                        <div class="icon"><i class="fas fa-exchange-alt"></i></div>
                                        <h3>Stock Movement Summary</h3>
                                        <div class="description">
                                            See detailed logs of stock in and out by date, product, and user.
                                        </div>
                                        <div class="actions">
                                            <a href="../reports/stock_movement_summary_report.php" class="btn btn-primary">View
                                                Report</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Selling Products Report Card -->
                                <div class="col-sm-6 col-lg-4">
                                    <div class="report-card-item sales">
                                        <div class="icon"><i class="fas fa-fire"></i></div>
                                        <h3>Top Selling Products</h3>
                                        <div class="description">
                                            Discover best-sellers based on quantity or revenue.
                                        </div>
                                        <div class="actions">
                                            <a href="../reports/top_selling_products_report.php" class="btn btn-primary">View
                                                Report</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- (Optional) Add More Reports Below -->
                                <div class="col-sm-6 col-lg-4">
                                    <div class="report-card-item sales">
                                        <div class="icon"><i class="fas fa-chart-bar"></i></div>
                                        <h3>Sales Summary Report</h3>
                                        <div class="description">
                                            Overview of sales performance, total revenue, and items sold.
                                        </div>
                                        <div class="actions">
                                            <a href="../reports/sales_summary_report.php" class="btn btn-primary">View Report</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-4">
                                    <div class="report-card-item purchase">
                                        <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                        <h3>Purchase Order Report</h3>
                                        <div class="description">
                                            Track purchase orders, their status, and associated costs.
                                        </div>
                                        <div class="actions">
                                            <a href="../reports/purchase_order_report.php" class="btn btn-primary">View Report</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-4">
                                    <div class="report-card-item inventory">
                                        <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                                        <h3>Low Stock Report</h3>
                                        <div class="description">
                                            See products nearing stock-out level.
                                        </div>
                                        <div class="actions">
                                            <a href="../reports/low_stock_report.php" class="btn btn-primary">View
                                                Report</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="copyright">
                                        <p>Copyright © <?php echo date("Y"); ?> ITBienvenu.</p>
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
        <script src="../vendor/jquery-3.2.1.min.js"></script>
        <!-- Bootstrap JS--> 
        <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
        <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
        <!-- Vendor JS-->
        <script src="../vendor/slick/slick.min.js"></script>
        <script src="../vendor/wow/wow.min.js"></script>
        <script src="../vendor/animsition/animsition.min.js"></script>
        <script src="../vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
        <script src="../vendor/counter-up/jquery.waypoints.min.js"></script>
        <script src="../vendor/counter-up/jquery.counterup.min.js"></script>
        <script src="../vendor/circle-progress/circle-progress.min.js"></script>
        <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="../vendor/chartjs/Chart.bundle.min.js"></script>
        <script src="../vendor/select2/select2.min.js"></script>

        <!-- Main JS-->
        <script src="../js/main.js"></script>

    </body>

    </html>

<?php } else {
    // Redirect to login page if not authenticated
    header("Location: login.php");
    exit();
}
?>