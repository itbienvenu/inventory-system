<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");

if(isset($_SESSION['role']) && isset($_SESSION['user_id'])){
    $id = $_SESSION['user_id'];
include_once '../executive/messsage_functions.php';

    // --- Document Data Fetching ---

    // 1. Proforma Invoices
    $total_proformas_query = mysqli_query($conn, "SELECT COUNT(id) AS total, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending, SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved FROM proforma_invoices");
    $proformas_data = $total_proformas_query ? mysqli_fetch_assoc($total_proformas_query) : ['total' => 0, 'pending' => 0, 'approved' => 0];

    // 2. Sales Orders
    $total_sales_orders_query = mysqli_query($conn, "SELECT COUNT(id) AS total, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending, SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) AS shipped, SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed FROM sales_orders");
    $sales_orders_data = $total_sales_orders_query ? mysqli_fetch_assoc($total_sales_orders_query) : ['total' => 0, 'pending' => 0, 'shipped' => 0, 'confirmed' => 0];

    // 3. Delivery Notes
    $total_delivery_notes_query = mysqli_query($conn, "SELECT COUNT(id) AS total, SUM(CASE WHEN received_at IS NULL THEN 1 ELSE 0 END) AS pending_receipt, SUM(CASE WHEN received_at IS NOT NULL THEN 1 ELSE 0 END) AS received FROM delivery_notes");
    $delivery_notes_data = $total_delivery_notes_query ? mysqli_fetch_assoc($total_delivery_notes_query) : ['total' => 0, 'pending_receipt' => 0, 'received' => 0];

    // 4. Purchase Orders
    $total_purchase_orders_query = mysqli_query($conn, "SELECT COUNT(id) AS total, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending, SUM(CASE WHEN status = 'received' THEN 1 ELSE 0 END) AS received FROM purchase_orders");
    $purchase_orders_data = $total_purchase_orders_query ? mysqli_fetch_assoc($total_purchase_orders_query) : ['total' => 0, 'pending' => 0, 'received' => 0];

    // 5. Goods Received Notes (GRN) - Placeholder, assuming a future table 'goods_received_notes'
    // For now, these will show 0 or dummy data until implemented.
    $total_grns_query = mysqli_query($conn, "SELECT COUNT(id) AS total FROM goods_received_notes"); // This table might not exist yet
    $grns_data = $total_grns_query ? mysqli_fetch_assoc($total_grns_query) : ['total' => 0];

    
    // 7. Document Downloads (from document_downloads table)
    $total_downloads_query = mysqli_query($conn, "SELECT COUNT(id) AS total FROM document_downloads");
    $downloads_data = $total_downloads_query ? mysqli_fetch_assoc($total_downloads_query) : ['total' => 0];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Inventory Management System Documents Hub">
    <meta name="author" content="Your Name/Company Name">
    <meta name="keywords" content="documents, proforma, sales order, delivery note, purchase order, GRN, credit note">

    <!-- Title Page-->
    <title>Documents Hub</title>

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
        /* Custom styles if needed, or override theme defaults */
        .overview-item--proforma { background-color: #3498db; /* Blue */ color: white; }
        .overview-item--sales-order { background-color: #0d6efd; /* Primary Blue */ color: white; }
        .overview-item--delivery-note { background-color: #17a2b8; /* Info Blue-Green */ color: white; }
        .overview-item--purchase-order { background-color: #343a40; /* Dark Grey */ color: white; }
        .overview-item--grn { background-color: #28a745; /* Success Green */ color: white; }
        .overview-item--credit-note { background-color: #dc3545; /* Danger Red */ color: white; }
        .overview-item--downloads { background-color: #6c757d; /* Secondary Grey */ color: white; }

        .overview-item .icon i {
            font-size: 48px;
            color: rgba(255,255,255,0.7); /* Lighter icon color for contrast */
        }
        .overview-item .text h2 {
            font-size: 2.2rem;
        }
        .overview-item .text span {
            font-size: 1rem;
            opacity: 0.9;
        }

        .document-analysis {
            font-size: 0.9em;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .document-analysis p {
            margin: 0;
            line-height: 1.5;
        }
        .document-analysis span {
            font-weight: bold;
        }

        .document-actions {
            margin-top: 15px;
        }
        .document-actions a.btn {
            background-color: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .document-actions a.btn:hover {
            background-color: rgba(255,255,255,0.4);
        }

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
        .dark-mode .dot--blue { background-color: #0d6efd; }
        .dark-mode .dot--green { background-color: #28a745; }
        .dark-mode .dot--red { background-color: #dc3545; }

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
                                    <h2 class="title-1">Business Documents Hub</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-25">

                            <!-- Proforma Invoices Box -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--proforma">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($proformas_data['total']); ?></h2>
                                                <span>Proforma Invoices</span>
                                            </div>
                                        </div>
                                        <div class="document-analysis">
                                            <p><span><?php echo number_format($proformas_data['pending']); ?></span> Pending</p>
                                            <p><span><?php echo number_format($proformas_data['approved']); ?></span> Approved</p>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/manage_proformas.php" class="btn btn-sm">Manage Proformas</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales Orders Box -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--sales-order">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-shopping-cart"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($sales_orders_data['total']); ?></h2>
                                                <span>Sales Orders</span>
                                            </div>
                                        </div>
                                        <div class="document-analysis">
                                            <p><span><?php echo number_format($sales_orders_data['pending']); ?></span> Pending</p>
                                            <p><span><?php echo number_format($sales_orders_data['confirmed']); ?></span> Confirmed</p>
                                            <p><span><?php echo number_format($sales_orders_data['shipped']); ?></span> Shipped</p>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/manage_sales_order.php" class="btn btn-sm">Manage Sales Orders</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Notes Box -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--delivery-note">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-truck-loading"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($delivery_notes_data['total']); ?></h2>
                                                <span>Delivery Notes</span>
                                            </div>
                                        </div>
                                        <div class="document-analysis">
                                            <p><span><?php echo number_format($delivery_notes_data['pending_receipt']); ?></span> Pending Receipt</p>
                                            <p><span><?php echo number_format($delivery_notes_data['received']); ?></span> Received</p>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/manage_delivery_note.php" class="btn btn-sm">Manage Delivery Notes</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchase Orders Box -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--purchase-order">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-handshake"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($purchase_orders_data['total']); ?></h2>
                                                <span>Purchase Orders</span>
                                            </div>
                                        </div>
                                        <div class="document-analysis">
                                            <p><span><?php echo number_format($purchase_orders_data['pending']); ?></span> Pending</p>
                                            <p><span><?php echo number_format($purchase_orders_data['received']); ?></span> Received</p>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/manage_purchase_order.php" class="btn btn-sm">Manage Purchase Orders</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Goods Received Notes (GRN) Box - Placeholder -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--grn">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($grns_data['total']); ?></h2>
                                                <span>Goods Received Notes</span>
                                            </div>
                                        </div>
                                        <div class="document-analysis">
                                            <p><span>N/A</span> Analysis (Future)</p>
                                        </div>
                                        <div class="document-actions">
                                            <a href="#" class="btn btn-sm disabled">Manage GRNs (Soon)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Document Downloads Box -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--downloads">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon">
                                                <i class="fas fa-download"></i>
                                            </div>
                                            <div class="text">
                                                <h2><?php echo number_format($downloads_data['total']); ?></h2>
                                                <span>Total Downloads</span>
                                            </div>
                                        </div>
                                        <div class="document-analysis">
                                            <p>View all document download activities.</p>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/view_document_downloads.php" class="btn btn-sm">View Download Log</a>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © <?php echo date("Y"); ?> ITBienvenu. All rights reserved. </p>
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
