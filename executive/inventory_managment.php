<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");

if(isset($_SESSION['role']) && isset($_SESSION['user_id'])){
    $id = $_SESSION['user_id'];

    // --- Inventory Data Fetching ---

    // 1. Stock In (Goods Received Notes)
    $total_grns_query = mysqli_query($conn, "SELECT COUNT(id) AS total_grns FROM goods_received_notes");
    $total_grns = $total_grns_query ? mysqli_fetch_assoc($total_grns_query)['total_grns'] : 0;

    // 2. Stock Out (Sales Orders Shipped)
    $total_shipped_orders_query = mysqli_query($conn, "SELECT COUNT(id) AS total_shipped FROM sales_orders WHERE status = 'shipped'");
    $total_shipped_orders = $total_shipped_orders_query ? mysqli_fetch_assoc($total_shipped_orders_query)['total_shipped'] : 0;

    // 3. Low Stock
    $low_stock_products_query = mysqli_query($conn, "SELECT COUNT(id) AS low_stock_count FROM products WHERE quantity <= low_stock");
    $low_stock_products = $low_stock_products_query ? mysqli_fetch_assoc($low_stock_products_query)['low_stock_count'] : 0;

    // 4. Restock Requests
    $pending_po_query = mysqli_query($conn, "SELECT COUNT(id) AS pending_po FROM purchase_orders WHERE status = 'pending'");
    $pending_po_count = $pending_po_query ? mysqli_fetch_assoc($pending_po_query)['pending_po'] : 0;
    
    

    // 5. NEW - Total Products
    $total_products_query = mysqli_query($conn, "SELECT COUNT(id) AS total_products FROM products");
    $total_products = $total_products_query ? mysqli_fetch_assoc($total_products_query)['total_products'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">

    <style>
        .overview-item--stock-in { background-color: #28a745; color: white; }
        .overview-item--stock-out { background-color: #0d6efd; color: white; }
        .overview-item--low-stock { background-color: #ffc107; color: white; }
        .overview-item--restock { background-color: #6f42c1; color: white; }
        .overview-item--total-products { background-color: #17a2b8; color: white; }

        .overview-item .icon i {
            font-size: 48px;
            color: rgba(255,255,255,0.7);
        }
        .overview-item .text h2 { font-size: 2.2rem; }
        .overview-item .text span { font-size: 1rem; }
        .document-actions a.btn {
            background-color: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .document-actions a.btn:hover { background-color: rgba(255,255,255,0.4); }

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
        }
    </style>
</head>
<body class="animsition">
    <div class="page-wrapper">
        <?php include_once 'templates/header_mobile_menu.php'; ?>
        <?php include_once 'templates/side_menu.php'; ?>
        <div class="page-container">
            <?php include_once 'templates/header_pc_menu.php'; ?>

            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h2 class="title-1">Inventory Management Hub</h2>
                            </div>
                        </div>

                        <div class="row m-t-25">
                            <!-- Stock In -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--stock-in">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon"><i class="fas fa-truck-loading"></i></div>
                                            <div class="text">
                                                <h2><?= number_format($total_grns); ?></h2>
                                                <span>Stock In (GRNs)</span>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/manage_grn.php" class="btn btn-sm">Manage GRNs</a>
                                            <a href="form.php#grn-part" class="btn btn-sm">Add GRN</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Out -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--stock-out">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon"><i class="fas fa-shipping-fast"></i></div>
                                            <div class="text">
                                                <h2><?= number_format($total_shipped_orders); ?></h2>
                                                <span>Stock Out (Shipped Orders)</span>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="manage_sales_order.php" class="btn btn-sm">Manage Orders</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Low Stock -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--low-stock">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                                            <div class="text">
                                                <h2><?= number_format($low_stock_products); ?></h2>
                                                <span>Low Stock Items</span>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/manage_products.php?filter=low_stock" class="btn btn-sm">View Low Stock</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Restock Requests -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--restock">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon"><i class="fas fa-sync-alt"></i></div>
                                            <div class="text">
                                                <h2><?= number_format($pending_po_count); ?></h2>
                                                <span>Pending Restocks</span>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="../edits/manage_purchase_order.php" class="btn btn-sm">Manage POs</a>
                                            <a href="form.php#purchase-order-part" class="btn btn-sm">Create PO</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Products -->
                            <div class="col-sm-6 col-lg-4">
                                <div class="overview-item overview-item--total-products">
                                    <div class="overview__inner">
                                        <div class="overview-box clearfix">
                                            <div class="icon"><i class="fas fa-boxes"></i></div>
                                            <div class="text">
                                                <h2><?= number_format($total_products); ?></h2>
                                                <span>Total Products</span>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="table.php#products-table" class="btn btn-sm">View Products</a>
                                            <a href="products.php" class="btn btn-sm">Add Product</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row mt-5">
                            <div class="col-md-12 text-center">
                                <div class="copyright">
                                    <p>&copy; <?= date('Y') ?> Your Company. All rights reserved.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <button class="toggle-theme-btn" onclick="toggleTheme()">Switch Mode</button>
    </div>

    <script src="vendor/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>
<?php
} else {
    header("Location: login.php");
    exit();
}
?>
