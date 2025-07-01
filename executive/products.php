<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
$allowed_roles = ['executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}
if(isset($_SESSION['role']) && isset($_SESSION['user_id'])){
    $id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Products</title>

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

    <!-- FullCalendar -->
    <link href='vendor/fullcalendar-3.10.0/fullcalendar.css' rel='stylesheet' media="all" />

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

    <style type="text/css">
    /* force class color to override the bootstrap base rule
       NOTE: adding 'url: #' to calendar makes this unneeded
     */
    .fc-event, .fc-event:hover {
          color: #fff !important;
          text-decoration: none;
    }
    </style>

</head>

<!-- animsition overrides all click events on clickable things like a,
      since calendar doesn't add href's be default,
      it leads to odd behaviors like loading 'undefined'
      moving the class to menus lead to only the menu having the effect -->
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
            <!-- END HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
    <div class="card">
        <div class="card-header justify-content-between d-flex">
            <span>
            Product Registration
            </span>
            <span>
                <a href="../edits/manage_products.php">View All Products</a>
            </span>
        </div>
        <div class="card-body">
            <div class="card-title">
                <h3 class="text-center title-2">Register New Product</h3>
            </div>
            <hr>
            <form action="../routes/insert_product.php" method="POST" enctype="multipart/form-data" novalidate>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="name">Product Name</label>
            <input id="name" name="name" type="text" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
            <label for="category">Category</label>
            <input id="category" name="category" type="text" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="sku">SKU / Product Code</label>
            <input id="sku" name="sku" type="text" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
            <label for="supplier">Supplier</label>
            <input id="supplier" name="supplier" type="text" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="cost_price">Cost Price</label>
            <input id="cost_price" name="cost_price" type="number" step="0.01" class="form-control">
        </div>
        <div class="form-group col-md-6">
            <label for="price">Selling Price</label>
            <input id="price" name="price" type="number" step="0.01" class="form-control" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="quantity">Quantity in Stock</label>
            <input id="quantity" name="quantity" type="number" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
            <label for="low_stock">Low Stock Alert</label>
            <input id="low_stock" name="low_stock" type="number" class="form-control" value="5">
        </div>
    </div>

    <div class="form-group">
        <label for="image">Product Image</label>
        <input id="image" name="image" type="file" class="form-control-file">
    </div>

    <div>
        <button id="register-button" type="submit" name="register_product" class="btn btn-lg btn-info btn-block">
            <i class="fa fa-save fa-lg"></i>&nbsp;
            <span id="register-button-label">Save Product</span>
            <span id="register-button-sending" style="display:none;">Saving…</span>
        </button>
    </div>

    <div>
        <?php
        if (isset($_SESSION['product-message'])) {
            $message = $_SESSION['product-message'];
            echo "<div class='alert alert-info mt-3'>$message</div>";
        }
        ?>
    </div>
</form>

        </div>
    </div>
</div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © <?php echo date("Y"); ?> ITBienvenu. All rights reserved.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
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
    <script src="vendor/select2/select2.min.js"></script>

    <!-- full calendar requires moment along jquery which is included above -->
    <script src="vendor/fullcalendar-3.10.0/lib/moment.min.js"></script>
    <script src="vendor/fullcalendar-3.10.0/fullcalendar.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

    <script type="text/javascript">
$(function() {
  // for now, there is something adding a click handler to 'a'
  var tues = moment().day(2).hour(19);

  // build trival night events for example data
  var events = [
    {
      title: "Special Conference",
      start: moment().format('YYYY-MM-DD'),
      url: '#'
    },
    {
      title: "Doctor Appt",
      start: moment().hour(9).add(2, 'days').toISOString(),
      url: '#'
    }

  ];

  var trivia_nights = []

  for(var i = 1; i <= 4; i++) {
    var n = tues.clone().add(i, 'weeks');
    console.log("isoString: " + n.toISOString());
    trivia_nights.push({
      title: 'Trival Night @ Pub XYZ',
      start: n.toISOString(),
      allDay: false,
      url: '#'
    });
  }

  // setup a few events
  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay,listWeek'
    },
    events: events.concat(trivia_nights)
  });
});
    </script>


</body>

</html>
<!-- end document-->
<?php } ?>