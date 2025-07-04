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
  <title>Manage Database Tables</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <?php include_once 'templates/header_mobile_menu.php'; ?>
    <?php include_once 'templates/side_menu.php'; ?>
    <div class="page-container">
      <?php include_once 'templates/header_pc_menu.php'; ?>

      <div class="main-content">
        <div class="section__content section__content--p30">
          <div class="container-fluid">
            <h3 class="mb-4">Database Tables</h3>

            <?php
            $res = mysqli_query($conn, "SHOW TABLES");
            $tables = [];
            while ($row = mysqli_fetch_array($res)) {
                $tables[] = $row[0];
            }
            ?>

            <!-- Tab Nav -->
            <ul class="nav nav-tabs mb-3" id="tableTab" role="tablist">
              <?php foreach ($tables as $index => $table): ?>
                <li class="nav-item">
                  <a class="nav-link <?= $index === 0 ? 'active' : '' ?>" id="tab-<?= $index ?>" data-toggle="tab" href="#content-<?= $index ?>" role="tab">
                    <?= htmlspecialchars($table) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="tableTabContent">
              <?php foreach ($tables as $index => $table): ?>
                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="content-<?= $index ?>" role="tabpanel">
                  <div class="card mb-4">
                    <div class="card-header">
                      <strong><?= htmlspecialchars($table) ?> Contents</strong>
                    </div>
                    <div class="card-body table-responsive">
                      <?php
                      $result = mysqli_query($conn, "SELECT * FROM `$table`");
                      if (mysqli_num_rows($result) > 0) {
                          echo "<table class='table table-bordered table-sm'>";
                          echo "<thead><tr>";
                          while ($field = mysqli_fetch_field($result)) {
                              echo "<th>" . htmlspecialchars($field->name) . "</th>";
                          }
                          echo "</tr></thead><tbody>";
                          while ($row = mysqli_fetch_assoc($result)) {
                              echo "<tr>";
                              foreach ($row as $value) {
                                  echo "<td>" . htmlspecialchars($value) . "</td>";
                              }
                              echo "</tr>";
                          }
                          echo "</tbody></table>";
                      } else {
                          echo "<div class='alert alert-warning'>No data in this table.</div>";
                      }
                      ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
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
