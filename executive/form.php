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
        <title>Forms</title>

        <!-- Fontfaces CSS-->
        <link href="css/font-face.css" rel="stylesheet" media="all">
        <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
        <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Main CSS-->
        <link href="css/theme.css" rel="stylesheet" media="all">

    </head>

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
                                <div class="col-lg-12">
                                    <?php
                                    // Load products from the database
                                    $all_products_query_for_invoice = mysqli_query($conn, "SELECT id, name, price FROM products");
                                    $all_products_for_invoice = [];
                                    if ($all_products_query_for_invoice) {
                                        while ($p = mysqli_fetch_assoc($all_products_query_for_invoice)) {
                                            $all_products_for_invoice[] = $p;
                                        }
                                    }
                                    ?>

                                    <div class="col-lg-12" id="invoices-part">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-success text-white">
                                                <strong>Create New Invoice</strong>
                                            </div>
                                            <div class="card-body">
                                                <h4 class="text-center mb-4">Generate Invoice</h4>
                                                <form action="../functions/SaveInvoice.php" method="POST">
                                                    <div class="row">
                                                        <!-- Company Information -->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Company Name</label>
                                                                <input type="text" name="company" class="form-control"
                                                                    placeholder="Company" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">VAT Number</label>
                                                                <input type="text" name="vat" class="form-control"
                                                                    placeholder="VAT">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Street</label>
                                                                <input type="text" name="street" class="form-control"
                                                                    placeholder="Street">
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-6">
                                                                    <label class="form-label">City</label>
                                                                    <input type="text" name="city" class="form-control"
                                                                        placeholder="City">
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label">Postal Code</label>
                                                                    <input type="text" name="postal_code"
                                                                        class="form-control" placeholder="Postal Code">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Country</label>
                                                                <input type="text" name="country" class="form-control"
                                                                    placeholder="Country">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Due Date (Optional)</label>
                                                                <input type="date" name="due_date" class="form-control">
                                                            </div>
                                                        </div>

                                                        <!-- Product List Section -->
                                                        <div class="col-lg-6">
                                                            <div id="productRows">
                                                                <div class="product-row row g-2 align-items-end mb-3">
                                                                    <div class="col-md-7">
                                                                        <label class="form-label">Product</label>
                                                                        <select name="products[]" class="form-select"
                                                                            required>
                                                                            <option value="">-- Select Product --</option>
                                                                            <?php foreach ($all_products_for_invoice as $p): ?>
                                                                                <option value="<?= $p['id'] ?>">
                                                                                    <?= htmlspecialchars($p['name']) ?> —
                                                                                    $<?= number_format($p['price'], 2) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label class="form-label">Quantity</label>
                                                                        <input type="number" name="quantities[]"
                                                                            class="form-control" min="1" required>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm removeRow w-100">Remove</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="text-start">
                                                                <button type="button" class="btn btn-outline-success btn-sm"
                                                                    id="addProduct">+ Add Another Product</button>
                                                            </div>

                                                            <div class="mt-4 d-flex justify-content-between">
                                                                <a href="javascript:history.back()"
                                                                    class="btn btn-secondary">Cancel</a>
                                                                <button type="submit" class="btn btn-primary px-4">Generate
                                                                    Invoice</button>
                                                                </div>
                                                                <br>
                                                                <a href="../edits/manage_invoices.php">View All Generated Invoices</a>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Row Script -->
                                    <script>
                                        $(document).ready(function () {
                                            const products = <?php echo json_encode($all_products_for_invoice); ?>;

                                            $('#addProduct').click(function () {
                                                let productOptions = '<option value="">-- Select Product --</option>';
                                                products.forEach(p => {
                                                    productOptions += `<option value="${p.id}">${p.name} — $${parseFloat(p.price).toFixed(2)}</option>`;
                                                });

                                                const newRow = `
                <div class="product-row row g-2 align-items-end mb-3">
                    <div class="col-md-7">
                        <label class="form-label">Product</label>
                        <select name="products[]" class="form-select" required>
                            ${productOptions}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantities[]" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm removeRow w-100">Remove</button>
                    </div>
                </div>
            `;
                                                $('#productRows').append(newRow);
                                            });

                                            // Remove product row
                                            $(document).on('click', '.removeRow', function () {
                                                if ($('.product-row').length > 1) {
                                                    $(this).closest('.product-row').remove();
                                                } else {
                                                    alert("You must keep at least one product.");
                                                }
                                            });
                                        });
                                    </script>

                                </div>
                                <form action="../functions/SaveProforma.php" method="POST">
                                    <h3>QUOTATION CREATION PART</h3>
                                    <div class="row" id="proforma-part">
                                        <!-- Customer Info Section -->
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header"><strong>Customer Info</strong></div>
                                                <div class="card-body card-block">
                                                    <div class="form-group">
                                                        <label>Company Name</label>
                                                        <input type="text" name="company" class="form-control" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>VAT Number</label>
                                                        <input type="text" name="vat" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Street</label>
                                                        <input type="text" name="street" class="form-control">
                                                    </div>
                                                    <div class="row form-group">
                                                        <div class="col-6">
                                                            <label>City</label>
                                                            <input type="text" name="city" class="form-control">
                                                        </div>
                                                        <div class="col-6">
                                                            <label>Postal Code</label>
                                                            <input type="text" name="postal_code" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Country</label>
                                                        <input type="text" name="country" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dynamic Product Selection Section -->
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header"><strong>Products</strong></div>
                                                <div class="card-body card-block">
                                                    <div id="productRows">
                                                        <!-- Product row template -->
                                                        <div class="form-row mb-2 product-row">
                                                            <div class="col-7">
                                                                <select name="products[]" class="form-control" required>
                                                                    <option value="">-- Select Product --</option>
                                                                    <?php
                                                                    $products = mysqli_query($conn, "SELECT id, name, price FROM products");
                                                                    while ($p = mysqli_fetch_assoc($products)) {
                                                                        echo "<option value='{$p['id']}'>{$p['name']} - \${$p['price']}</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="number" name="quantities[]"
                                                                    class="form-control" placeholder="Qty" required>
                                                            </div>
                                                            <div class="col-2">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm removeRow">X</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Add New Product Button -->
                                                    <div class="form-group mt-3">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            id="addProduct">+ Add Product</button>
                                                    </div>

                                                    <!-- Submit Button -->
                                                    <div class="form-group text-right">
                                                        <button type="submit" class="btn btn-primary">Generate
                                                            Proforma</button>
                                                        <a href="../edits/manage_proformas.php"
                                                            class="btn btn-secondary">Manage Generated Proforma</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="col-lg-6">
                                    <?php
// This PHP block assumes 'config.php' is already included and session is started
// at the top of your main page where this section will be embedded.

// Fetch all products for the dropdown
$all_products_query_for_so = mysqli_query($conn, "SELECT id, name, price FROM products");
$all_products_for_so = [];
if ($all_products_query_for_so) {
    while ($p = mysqli_fetch_assoc($all_products_query_for_so)) {
        $all_products_for_so[] = $p;
    }
} else {
    error_log("Error fetching products for sales order creation form: " . mysqli_error($conn));
}
?>

<div class="col-lg-12"> <!-- Assuming this card is within a col-lg-6 on your main page,
                            this col-lg-12 will make its internal content span full width. -->
    <div class="card">
        <div class="card-header bg-primary text-white"> <!-- Changed header for Sales Order creation -->
            <strong>Create New Sales Order</strong>
        </div>
        <div class="card-body card-block">
            <form action="../functions/SaveSalesOrder.php" method="post" class="form-horizontal">
                <!-- Customer Info Section -->
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="company_so" class="form-control-label">Company Name</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="company_so" name="company" placeholder="Enter Company Name" class="form-control" required>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="vat_so" class="form-control-label">VAT Number</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="vat_so" name="vat" placeholder="Enter VAT Number" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="street_so" class="form-control-label">Street</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="street_so" name="street" placeholder="Enter Street Address" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="city_so" class="form-control-label">City</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="city_so" name="city" placeholder="Enter City" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="postal_code_so" class="form-control-label">Postal Code</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="postal_code_so" name="postal_code" placeholder="Enter Postal Code" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="country_so" class="form-control-label">Country</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="country_so" name="country" placeholder="Enter Country" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="delivery_date_so" class="form-control-label">Expected Delivery Date</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="date" id="delivery_date_so" name="delivery_date" class="form-control">
                    </div>
                </div>

                <hr class="mt-4 mb-4">
                <h4 class="text-center mb-3">Order Items</h4>

                <!-- Dynamic Product Selection Section -->
                <div id="productRowsSO">
                    <!-- Initial Product row template -->
                    <div class="row form-group product-row-so">
                        <div class="col col-md-5">
                            <label for="product_so_0" class="form-control-label">Product</label>
                            <select name="products[]" id="product_so_0" class="form-control" required>
                                <option value="">-- Select Product --</option>
                                <?php foreach ($all_products_for_so as $p) : ?>
                                    <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                        <?php echo htmlspecialchars($p['name']); ?> - $<?php echo htmlspecialchars($p['price']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col col-md-3">
                            <label for="quantity_so_0" class="form-control-label">Quantity</label>
                            <input type="number" id="quantity_so_0" name="quantities[]" placeholder="Qty" class="form-control" required min="1">
                        </div>
                        <div class="col col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm removeRowSO mb-1">X</button>
                        </div>
                    </div>
                </div>

                <!-- Add New Product Button -->
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-success btn-sm" id="addProductSO">+ Add Product</button>
                </div>

                <!-- Submit Button -->
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> Create Sales Order
                    </button>
                    <button type="reset" class="btn btn-danger btn-sm">
                        <i class="fa fa-ban"></i> Reset
                    </button>
                </div>
                <a href="../edits/manage_sales_order.php">View All Sales Ordes</a>
            </form>
        </div>
    </div>
</div>

<script>
    // This script assumes jQuery is loaded on the main page.
    $(document).ready(function() {
        const allProductsSO = <?php echo json_encode($all_products_for_so); ?>;
        let productRowCountSO = 0; 

        $('#addProductSO').on('click', function() {
            productRowCountSO++;
            let productOptionsSO = '<option value="">-- Select Product --</option>';
            allProductsSO.forEach(function(p) {
                productOptionsSO += `<option value="${p.id}">${p.name} - $${p.price}</option>`;
            });

            const productRowHtml = `
                <div class="row form-group product-row-so">
                    <div class="col col-md-5">
                        <label for="product_so_${productRowCountSO}" class="form-control-label">Product</label>
                        <select name="products[]" id="product_so_${productRowCountSO}" class="form-control" required>
                            ${productOptionsSO}
                        </select>
                    </div>
                    <div class="col col-md-3">
                        <label for="quantity_so_${productRowCountSO}" class="form-control-label">Quantity</label>
                        <input type="number" id="quantity_so_${productRowCountSO}" name="quantities[]" placeholder="Qty" class="form-control" required min="1">
                    </div>
                    <div class="col col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm removeRowSO mb-1">X</button>
                    </div>
                </div>
            `;
            $('#productRowsSO').append(productRowHtml);
        });

        // Event listener for removing product rows
        $(document).on('click', '.removeRowSO', function() {
            // Ensure at least one product row remains
            if ($('#productRowsSO .product-row-so').length > 1) {
                $(this).closest('.product-row-so').remove();
            } else {
                alert("A sales order must have at least one product.");
            }
        });
    });
</script>

                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Inline</strong> Form
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="" method="post" class="form-inline">
                                                <div class="form-group">
                                                    <label for="exampleInputName2"
                                                        class="pr-1  form-control-label">Name</label>
                                                    <input type="text" id="exampleInputName2" placeholder="Jane Doe"
                                                        required="" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail2"
                                                        class="px-1  form-control-label">Email</label>
                                                    <input type="email" id="exampleInputEmail2"
                                                        placeholder="jane.doe@example.com" required="" class="form-control">
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-dot-circle-o"></i> Submit
                                            </button>
                                            <button type="reset" class="btn btn-danger btn-sm">
                                                <i class="fa fa-ban"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Horizontal</strong> Form
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="" method="post" class="form-horizontal">
                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label for="hf-email" class=" form-control-label">Email</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <input type="email" id="hf-email" name="hf-email"
                                                            placeholder="Enter Email..." class="form-control">
                                                        <span class="help-block">Please enter your email</span>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-md-3">
                                                        <label for="hf-password"
                                                            class=" form-control-label">Password</label>
                                                    </div>
                                                    <div class="col-12 col-md-9">
                                                        <input type="password" id="hf-password" name="hf-password"
                                                            placeholder="Enter Password..." class="form-control">
                                                        <span class="help-block">Please enter your password</span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-dot-circle-o"></i> Submit
                                            </button>
                                            <button type="reset" class="btn btn-danger btn-sm">
                                                <i class="fa fa-ban"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Normal</strong> Form
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="" method="post" class="">
                                                <div class="form-group">
                                                    <label for="nf-email" class=" form-control-label">Email</label>
                                                    <input type="email" id="nf-email" name="nf-email"
                                                        placeholder="Enter Email.." class="form-control">
                                                    <span class="help-block">Please enter your email</span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nf-password" class=" form-control-label">Password</label>
                                                    <input type="password" id="nf-password" name="nf-password"
                                                        placeholder="Enter Password.." class="form-control">
                                                    <span class="help-block">Please enter your password</span>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-dot-circle-o"></i> Submit
                                            </button>
                                            <button type="reset" class="btn btn-danger btn-sm">
                                                <i class="fa fa-ban"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            Input
                                            <strong>Grid</strong>
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="" method="post" class="form-horizontal">
                                                <div class="row form-group">
                                                    <div class="col col-sm-3">
                                                        <input type="text" placeholder=".col-sm-3" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-4">
                                                        <input type="text" placeholder=".col-sm-4" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-5">
                                                        <input type="text" placeholder=".col-sm-5" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-6">
                                                        <input type="text" placeholder=".col-sm-6" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-7">
                                                        <input type="text" placeholder=".col-sm-7" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-8">
                                                        <input type="text" placeholder=".col-sm-8" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-9">
                                                        <input type="text" placeholder=".col-sm-9" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-10">
                                                        <input type="text" placeholder=".col-sm-10" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-11">
                                                        <input type="text" placeholder=".col-sm-11" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-12">
                                                        <input type="text" placeholder=".col-sm-12" class="form-control">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-user"></i> Login
                                            </button>
                                            <button type="reset" class="btn btn-danger btn-sm">
                                                <i class="fa fa-ban"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            Input
                                            <strong>Sizes</strong>
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="" method="post" class="form-horizontal">
                                                <div class="row form-group">
                                                    <div class="col col-sm-5">
                                                        <label for="input-small" class=" form-control-label">Small
                                                            Input</label>
                                                    </div>
                                                    <div class="col col-sm-6">
                                                        <input type="text" id="input-small" name="input-small"
                                                            placeholder=".form-control-sm"
                                                            class="input-sm form-control-sm form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-5">
                                                        <label for="input-normal" class=" form-control-label">Normal
                                                            Input</label>
                                                    </div>
                                                    <div class="col col-sm-6">
                                                        <input type="text" id="input-normal" name="input-normal"
                                                            placeholder="Normal" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-sm-5">
                                                        <label for="input-large" class=" form-control-label">Large
                                                            Input</label>
                                                    </div>
                                                    <div class="col col-sm-6">
                                                        <input type="text" id="input-large" name="input-large"
                                                            placeholder=".form-control-lg"
                                                            class="input-lg form-control-lg form-control">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-dot-circle-o"></i> Submit
                                            </button>
                                            <button type="reset" class="btn btn-danger btn-sm">
                                                <i class="fa fa-ban"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Validation states</strong> Form
                                        </div>
                                        <div class="card-body card-block">
                                            <div class="has-success form-group">
                                                <label for="inputIsValid" class=" form-control-label">Input is valid</label>
                                                <input type="text" id="inputIsValid"
                                                    class="is-valid form-control-success form-control">
                                            </div>
                                            <div class="has-warning form-group">
                                                <label for="inputIsInvalid" class=" form-control-label">Input is
                                                    invalid</label>
                                                <input type="text" id="inputIsInvalid" class="is-invalid form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Validation states</strong> with optional icons
                                            <em>(deprecated)</em>
                                        </div>
                                        <div class="card-body card-block">
                                            <div class="has-success form-group">
                                                <label for="inputSuccess2i" class=" form-control-label">Input with
                                                    success</label>
                                                <input type="text" id="inputSuccess2i"
                                                    class="form-control-success form-control">
                                            </div>
                                            <div class="has-warning form-group">
                                                <label for="inputWarning2i" class=" form-control-label">Input with
                                                    warning</label>
                                                <input type="text" id="inputWarning2i"
                                                    class="form-control-warning form-control">
                                            </div>
                                            <div class="has-danger has-feedback form-group">
                                                <label for="inputError2i" class=" form-control-label">Input with
                                                    error</label>
                                                <input type="text" id="inputError2i"
                                                    class="form-control-danger form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <strong>Icon/Text</strong> Groups
                                        </div>
                                        <div class="card-body card-block">
                                            <form action="" method="post" class="form-horizontal">
                                                <div class="row form-group">
                                                    <div class="col col-md-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-user"></i>
                                                            </div>
                                                            <input type="text" id="input1-group1" name="input1-group1"
                                                                placeholder="Username" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-md-12">
                                                        <div class="input-group">
                                                            <input type="email" id="input2-group1" name="input2-group1"
                                                                placeholder="Email" class="form-control">
                                                            <div class="input-group-addon">
                                                                <i class="far fa-envelope"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row form-group">
                                                    <div class="col col-md-12">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-euro"></i>
                                                            </div>
                                                            <input type="text" id="input3-group1" name="input3-group1"
                                                                placeholder=".." class="form-control">
                                                            <div class="input-group-addon">.00</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fa fa-dot-circle-o"></i> Submit
                                            </button>
                                            <button type="reset" class="btn btn-danger btn-sm">
                                                <i class="fa fa-ban"></i> Reset
                                            </button>
                                        </div>
                                    </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#addProduct').click(function () {
                let row = `<div class="form-row mb-2 product-row">
                            <div class="col-7">
                                <select name="products[]" class="form-control" required>
                                    <option value="">-- Select Product --</option>
                                    <?php
                                    $products = mysqli_query($conn, "SELECT id, name, price FROM products");
                                    while ($p = mysqli_fetch_assoc($products)) {
                                        echo "<option value='{$p['id']}'>{$p['name']} - \${$p['price']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" name="quantities[]" class="form-control" placeholder="Qty" required>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                            </div>
                        </div>`;
                $('#productRows').append(row);
            });

            $(document).on('click', '.removeRow', function () {
                $(this).closest('.product-row').remove();
            });
        });
    </script>
<?php } ?>