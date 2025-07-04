<?php
include_once(__DIR__ . "/../config/auth.php");
include_once(__DIR__ . "/../config/config.php");

$allowed_roles = ['executive', 'admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
}

include_once 'messsage_functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>System Guide | Inventory Management</title>

  <!-- Styles -->
  <link href="css/font-face.css" rel="stylesheet">
  <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet">
  <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet">
  <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet">
  <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/animsition/animsition.min.css" rel="stylesheet">
  <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
  <link href="vendor/wow/animate.css" rel="stylesheet">
  <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet">
  <link href="vendor/slick/slick.css" rel="stylesheet">
  <link href="vendor/select2/select2.min.css" rel="stylesheet">
  <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet">
  <link href="css/theme.css" rel="stylesheet">
</head>

<body class="animsition">
  <div class="page-wrapper">

    <!-- Include sidebar and topbar -->
    <?php include_once 'templates/header_mobile_menu.php' ?>
    <?php include_once 'templates/side_menu.php' ?>

    <div class="page-container">
      <?php include_once 'templates/header_pc_menu.php' ?>

      <div class="main-content">
        <div class="section__content section__content--p30">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">

                <div class="card">
                  <div class="card-header">
                    <strong class="card-title">📘 Inventory Management System Documentation</strong>
                  </div>
                  <div class="card-body">

                    <h3>📝 System Overview</h3>
                    <p>
                      The <strong>Inventory Management System</strong> helps admins and executives manage stock, track sales, and generate documents like sales orders, quotations, invoices, and delivery notes.
                    </p>

                    <h4>👥 User Roles</h4>
                    <ul>
                      <li><strong>Executive</strong>: Full control of the system, users, and database operations.</li>
                      <li><strong>Admin</strong>: Manages users, inventory, and operations but cannot access database internals.</li>
                      <li><strong>Daily Worker</strong>: Records daily sales orders which must be confirmed by an admin.</li>
                    </ul>

                    <h4>🔑 Key Features</h4>
                    <ul>
                      <li>OTP-based login for enhanced security</li>
                      <li>Role-based dashboard and permission system</li>
                      <li>Internal messaging system (Inbox and Sent)</li>
                      <li>Product and stock management with movement logs</li>
                      <li>Sales order management with approval workflow</li>
                      <li>Quotation, invoice, and delivery document generation</li>
                      <li>User activity and session logging</li>
                    </ul>

                    <h4>📂 Database Tables</h4>
                    <div class="row">
                      <div class="col-md-6">
                        <ul>
                          <li>users</li>
                          <li>otp_codes</li>
                          <li>user_sessions</li>
                          <li>user_activity_logs</li>
                          <li>messages</li>
                          <li>products</li>
                          <li>stock_logs</li>
                          <li>stock_movements</li>
                        </ul>
                      </div>
                      <div class="col-md-6">
                        <ul>
                          <li>sales_orders</li>
                          <li>sales_order_items</li>
                          <li>purchase_orders</li>
                          <li>purchase_order_items</li>
                          <li>invoices</li>
                          <li>invoice_items</li>
                          <li>proforma_invoices</li>
                          <li>proforma_items</li>
                          <li>goods_received_notes</li>
                          <li>delivery_notes</li>
                        </ul>
                      </div>
                    </div>

                    <h4>🧭 System Flow</h4>
                    <ol>
                      <li>User logs in with email and password → OTP is sent and verified</li>
                      <li>User is redirected to their respective dashboard based on role</li>
                      <li>Daily Worker creates a sales order</li>
                      <li>Admin reviews and confirms payment or rejects</li>
                      <li>Executives can manage users, download backups, or analyze reports</li>
                      <li>Messaging system allows easy internal communication</li>
                    </ol>

                    <h4>📌 Important Notes</h4>
                    <ul>
                      <li>All activity is logged in <code>user_activity_logs</code></li>
                      <li>OTP codes expire after 10 minutes (table: <code>otp_codes</code>)</li>
                      <li>Session logs are stored for analytics and audit (<code>user_sessions</code>)</li>
                      <li>Only executives can add/remove admins and workers</li>
                    </ul>

                    <hr>
                    <p class="text-muted small">Last updated: <?= date("Y-m-d") ?> | Version 1.0</p>

                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- JS scripts -->
  <script src="vendor/jquery-3.2.1.min.js"></script>
  <script src="vendor/bootstrap-4.1/popper.min.js"></script>
  <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
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
  <script src="js/main.js"></script>

</body>
</html>
