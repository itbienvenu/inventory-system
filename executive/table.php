<?php
include_once(__DIR__ . "/../config/auth.php");
include_once(__DIR__ . "/../config/config.php");
if (isset($_SESSION['role']) && isset($_SESSION['user_id'])) {
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
        <title>Tables</title>

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

        <!-- Main CSS-->
        <link href="css/theme.css" rel="stylesheet" media="all">

    </head>

    <body class="animsition">
        <div class="page-wrapper">
            <!-- HEADER MOBILE-->
            <?php include_once 'templates/header_mobile_menu.php' ?>
            <!-- END HEADER MOBILE-->

            <!-- MENU SIDEBAR-->
            <?php include_once 'templates/side_menu.php' ?>
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
                                <div class="col-lg-9">
                                    <div class="table-responsive table--no-card m-b-30">
                                        <table class="table table-borderless table-striped table-earning">
                                            <thead>
                                                <tr>
                                                    <th>date</th>
                                                    <th>order ID</th>
                                                    <th>name</th>
                                                    <th class="text-right">price</th>
                                                    <th class="text-right">quantity</th>
                                                    <th class="text-right">total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2018-09-29 05:57</td>
                                                    <td>100398</td>
                                                    <td>iPhone X 64Gb Grey</td>
                                                    <td class="text-right">$999.00</td>
                                                    <td class="text-right">1</td>
                                                    <td class="text-right">$999.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-28 01:22</td>
                                                    <td>100397</td>
                                                    <td>Samsung S8 Black</td>
                                                    <td class="text-right">$756.00</td>
                                                    <td class="text-right">1</td>
                                                    <td class="text-right">$756.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-27 02:12</td>
                                                    <td>100396</td>
                                                    <td>Game Console Controller</td>
                                                    <td class="text-right">$22.00</td>
                                                    <td class="text-right">2</td>
                                                    <td class="text-right">$44.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-26 23:06</td>
                                                    <td>100395</td>
                                                    <td>iPhone X 256Gb Black</td>
                                                    <td class="text-right">$1199.00</td>
                                                    <td class="text-right">1</td>
                                                    <td class="text-right">$1199.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-25 19:03</td>
                                                    <td>100393</td>
                                                    <td>USB 3.0 Cable</td>
                                                    <td class="text-right">$10.00</td>
                                                    <td class="text-right">3</td>
                                                    <td class="text-right">$30.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29 05:57</td>
                                                    <td>100392</td>
                                                    <td>Smartwatch 4.0 LTE Wifi</td>
                                                    <td class="text-right">$199.00</td>
                                                    <td class="text-right">6</td>
                                                    <td class="text-right">$1494.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-24 19:10</td>
                                                    <td>100391</td>
                                                    <td>Camera C430W 4k</td>
                                                    <td class="text-right">$699.00</td>
                                                    <td class="text-right">1</td>
                                                    <td class="text-right">$699.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-22 00:43</td>
                                                    <td>100393</td>
                                                    <td>USB 3.0 Cable</td>
                                                    <td class="text-right">$10.00</td>
                                                    <td class="text-right">3</td>
                                                    <td class="text-right">$30.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="au-card au-card--bg-blue au-card-top-countries m-b-30">
                                        <div class="au-card-inner">
                                            <div class="table-responsive">
                                                <table class="table table-top-countries">
                                                    <tbody>
                                                        <tr>
                                                            <td>United States</td>
                                                            <td class="text-right">$119,366.96</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Australia</td>
                                                            <td class="text-right">$70,261.65</td>
                                                        </tr>
                                                        <tr>
                                                            <td>United Kingdom</td>
                                                            <td class="text-right">$46,399.22</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Turkey</td>
                                                            <td class="text-right">$35,364.90</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Germany</td>
                                                            <td class="text-right">$20,366.96</td>
                                                        </tr>
                                                        <tr>
                                                            <td>France</td>
                                                            <td class="text-right">$10,366.96</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Australia</td>
                                                            <td class="text-right">$5,366.96</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Italy</td>
                                                            <td class="text-right">$1639.32</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <!-- USER DATA-->
                                    <div class="user-data m-b-30">
                                        <h3 class="title-3 m-b-30">
                                            <i class="zmdi zmdi-account-calendar"></i>user data
                                        </h3>
                                        <div class="filters m-b-45">
                                            <div class="rs-select2--dark rs-select2--md m-r-10 rs-select2--border">
                                                <select class="js-select2" name="property">
                                                    <option selected="selected">All Properties</option>
                                                    <option value="">Products</option>
                                                    <option value="">Services</option>
                                                </select>
                                                <div class="dropDownSelect2"></div>
                                            </div>
                                            <div class="rs-select2--dark rs-select2--sm rs-select2--border">
                                                <select class="js-select2 au-select-dark" name="time">
                                                    <option selected="selected">All Time</option>
                                                    <option value="">By Month</option>
                                                    <option value="">By Day</option>
                                                </select>
                                                <div class="dropDownSelect2"></div>
                                            </div>
                                        </div>
                                        <div class="table-responsive table-data">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td>
                                                            <label class="au-checkbox">
                                                                <input type="checkbox">
                                                                <span class="au-checkmark"></span>
                                                            </label>
                                                        </td>
                                                        <td>name</td>
                                                        <td>role</td>
                                                        <td>Actions</td>
                                                        <td></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Get all users and their roles
                                                    $get_users = mysqli_query($conn, "SELECT * FROM users");
                                                    while ($row = mysqli_fetch_array($get_users)) {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <label class="au-checkbox">
                                                                    <input type="checkbox">
                                                                    <span class="au-checkmark"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <div class="table-data__info">
                                                                    <h6>
                                                                        <?php echo $row['names']; ?>
                                                                    </h6>
                                                                    <span>
                                                                        <a href="#"><?php echo $row['email']; ?></a>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="role <?php
                                                                if ($row['role'] == 'admin') {
                                                                    echo 'admin';
                                                                } elseif ($row['role'] == 'executive') {
                                                                    echo 'executive';
                                                                } elseif ($row['role'] == 'user') {
                                                                    echo "user";
                                                                } else {
                                                                    echo 'member';
                                                                }
                                                                ?>">
                                                                    <?php echo $row['role']; ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="edit">
                                                                    <a href="edit_user.php?id=<?php echo $row['id'] ?>">
                                                                        <i class="zmdi zmdi-edit">

                                                                        </i> </a>
                                                                </span>
                                                                <span class="delete">
                                                                    <a style="color: red;"
                                                                        href="delete_user.php?id=<?php echo $row['id'] ?>">
                                                                        <i class="zmdi zmdi-delete"></i> </a></span>
                                                                </span>
                                                                <span>
                                                                    <a style="color: orangered; "
                                                                        href="view_user.php?id=<?php echo $row['id']; ?>">
                                                                        <i class="zmdi zmdi-eye"></i></a>

                                                                </span>

                                                                </span>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="user-data__footer">
                                            <button class="au-btn au-btn-load">load more</button>
                                        </div>
                                    </div>
                                    <!-- END USER DATA-->
                                </div>
                                <div class="col-lg-6">
                                    <!-- TOP CAMPAIGN-->
                                    <div class="top-campaign">
                                        <h3 class="title-3 m-b-30">top campaigns</h3>
                                        <div class="table-responsive">
                                            <table class="table table-top-campaign">
                                                <tbody>
                                                    <tr>
                                                        <td>1. Australia</td>
                                                        <td>$70,261.65</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2. United Kingdom</td>
                                                        <td>$46,399.22</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3. Turkey</td>
                                                        <td>$35,364.90</td>
                                                    </tr>
                                                    <tr>
                                                        <td>4. Germany</td>
                                                        <td>$20,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>5. France</td>
                                                        <td>$10,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3. Turkey</td>
                                                        <td>$35,364.90</td>
                                                    </tr>
                                                    <tr>
                                                        <td>4. Germany</td>
                                                        <td>$20,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>5. France</td>
                                                        <td>$10,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3. Turkey</td>
                                                        <td>$35,364.90</td>
                                                    </tr>
                                                    <tr>
                                                        <td>4. Germany</td>
                                                        <td>$20,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>5. France</td>
                                                        <td>$10,366.96</td>
                                                    </tr>
                                                    <tr>
                                                        <td>4. Germany</td>
                                                        <td>$20,366.96</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!--  END TOP CAMPAIGN-->
                                </div>
                            </div>
                            <div class="table-responsive m-b-40" id="products-table">
                                <?php require_once(__DIR__ . '/../config/config.php'); ?>

                                <!-- 🔍 FILTER FORM -->
                                <form method="GET" class="form-inline mb-3">
                                    <input type="text" name="search" class="form-control mr-2 mb-2"
                                        placeholder="Search name or SKU"
                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

                                    <input type="number" name="min_price" class="form-control mr-2 mb-2"
                                        placeholder="Min Price" step="0.01"
                                        value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">

                                    <input type="number" name="max_price" class="form-control mr-2 mb-2"
                                        placeholder="Max Price" step="0.01"
                                        value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">

                                    <select name="category" class="form-control mr-2 mb-2">
                                        <option value="">All Categories</option>
                                        <?php
                                        $cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM products");
                                        while ($cat = mysqli_fetch_assoc($cat_query)):
                                            $selected = (isset($_GET['category']) && $_GET['category'] == $cat['category']) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($cat['category']) . "' $selected>" . htmlspecialchars($cat['category']) . "</option>";
                                        endwhile;
                                        ?>
                                    </select>

                                    <button type="submit" class="btn btn-sm btn-info mb-2">Filter</button>
                                    <a href="products.php" class="btn btn-sm btn-secondary mb-2 ml-2">Reset</a>
                                </form>

                                <!-- 📦 PRODUCT TABLE -->
                                <table class="table table-borderless table-data3">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>SKU</th>
                                            <th>Description</th>
                                            <th>Cost Price</th>
                                            <th>Selling Price</th>
                                            <th>Quantity</th>
                                            <th>Low Stock</th>
                                            <th>Supplier</th>
                                            <th>Date Added</th>
                                            <th>Image</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // FILTER LOGIC
                                        $conditions = [];

                                        if (!empty($_GET['search'])) {
                                            $search = mysqli_real_escape_string($conn, $_GET['search']);
                                            $conditions[] = "(name LIKE '%$search%' OR sku LIKE '%$search%')";
                                        }

                                        if (!empty($_GET['category'])) {
                                            $category = mysqli_real_escape_string($conn, $_GET['category']);
                                            $conditions[] = "category = '$category'";
                                        }

                                        if (!empty($_GET['min_price'])) {
                                            $min_price = floatval($_GET['min_price']);
                                            $conditions[] = "price >= $min_price";
                                        }

                                        if (!empty($_GET['max_price'])) {
                                            $max_price = floatval($_GET['max_price']);
                                            $conditions[] = "price <= $max_price";
                                        }

                                        $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

                                        $query = "SELECT * FROM products $where ORDER BY created_at DESC";
                                        $result = mysqli_query($conn, $query);

                                        if ($result && mysqli_num_rows($result) > 0):
                                            while ($product = mysqli_fetch_assoc($result)):
                                                ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                                    <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?>
                                                    </td>
                                                    <td>$<?php echo number_format($product['cost_price'], 2); ?></td>
                                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                                    <td><?php echo (int) $product['quantity']; ?></td>
                                                    <td>
                                                        <?php if ($product['quantity'] <= $product['low_stock']): ?>
                                                            <span class="badge badge-danger">Low</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-success">OK</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($product['supplier']); ?></td>
                                                    <td><?php echo date("Y-m-d", strtotime($product['created_at'])); ?></td>
                                                    <td>
                                                        <?php if (!empty($product['image'])): ?>
                                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" width="50"
                                                                alt="Product Image">
                                                        <?php else: ?>
                                                            <span class="text-muted">No image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="../edits/edit_product.php?id=<?php echo $product['id']; ?>"
                                                            class="btn btn-sm btn-warning"><i class="zmdi zmdi-edit"></i></a>
                                                        <a href="../edits/delete_product.php?id=<?php echo $product['id']; ?>"
                                                            onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">
                                                            <i class="zmdi zmdi-delete"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; else: ?>
                                            <tr>
                                                <td colspan="12" class="text-center">No products found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                            </div>


                            <div class="row m-t-30">
                                <div class="col-md-12">
                                    <!-- DATA TABLE-->
                                    <div class="table-responsive m-b-40">
                                        <table class="table table-borderless table-data3">
                                            <thead>
                                                <tr>
                                                    <th>date</th>
                                                    <th>type</th>
                                                    <th>description</th>
                                                    <th>status</th>
                                                    <th>price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>2018-09-29 05:57</td>
                                                    <td>Mobile</td>
                                                    <td>iPhone X 64Gb Grey</td>
                                                    <td class="process">Processed</td>
                                                    <td>$999.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-28 01:22</td>
                                                    <td>Mobile</td>
                                                    <td>Samsung S8 Black</td>
                                                    <td class="process">Processed</td>
                                                    <td>$756.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-27 02:12</td>
                                                    <td>Game</td>
                                                    <td>Game Console Controller</td>
                                                    <td class="denied">Denied</td>
                                                    <td>$22.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-26 23:06</td>
                                                    <td>Mobile</td>
                                                    <td>iPhone X 256Gb Black</td>
                                                    <td class="denied">Denied</td>
                                                    <td>$1199.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-25 19:03</td>
                                                    <td>Accessories</td>
                                                    <td>USB 3.0 Cable</td>
                                                    <td class="process">Processed</td>
                                                    <td>$10.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29 05:57</td>
                                                    <td>Accesories</td>
                                                    <td>Smartwatch 4.0 LTE Wifi</td>
                                                    <td class="denied">Denied</td>
                                                    <td>$199.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-24 19:10</td>
                                                    <td>Camera</td>
                                                    <td>Camera C430W 4k</td>
                                                    <td class="process">Processed</td>
                                                    <td>$699.00</td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-22 00:43</td>
                                                    <td>Computer</td>
                                                    <td>Macbook Pro Retina 2017</td>
                                                    <td class="process">Processed</td>
                                                    <td>$10.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- END DATA TABLE-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="copyright">
                                        <p>Copyright © 2018 Colorlib. All rights reserved. Template by <a
                                                href="https://colorlib.com">Colorlib</a>.</p>
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
        <script src="vendor/select2/select2.min.js">
        </script>

        <!-- Main JS-->
        <script src="js/main.js"></script>

    </body>

    </html>
    <!-- end document-->
<?php } ?>