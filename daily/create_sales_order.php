<?php
include_once (__DIR__."/../config/auth.php");
include_once (__DIR__."/../config/config.php");
include_once __DIR__. "/../includes/logger.php";
include_once  __DIR__.'/../functions/SecurityLayer.php';
log_user_action("Visited Create Sales Order Page", "User navigated to create sales order form");

// Check role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'daily') {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];
$get_seller = mysqli_query($conn, "SELECT * FROM users where id = $user_id");
$get_seller = mysqli_fetch_array($get_seller);
$user_name = $get_seller['names'];

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
<!DOCTYPE html>
<html>
<head>
    <title>Create Sales Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-light p-4">
    <div class="container">

        <!-- Navbar (consistent with dashboard) -->
       <?php include_once 'top_bar.php'; ?>
        <h2 class="mb-4">Create New Sales Order</h2>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <strong>Create New Sales Order</strong>
                    </div>
                    <div class="card-body card-block">
                        <form action="../functions/SaveSalesOrder.php" method="post" class="form-horizontal">
                            <!-- Customer Info Section -->
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="company_so" class="form-control-label">Company Name</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="company_so" name="company"
                                        placeholder="Enter Company Name" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="vat_so" class="form-control-label">VAT Number</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="vat_so" name="vat"
                                        placeholder="Enter VAT Number" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="street_so" class="form-control-label">Street</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="street_so" name="street"
                                        placeholder="Enter Street Address" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="city_so" class="form-control-label">City</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="city_so" name="city"
                                        placeholder="Enter City" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="postal_code_so" class="form-control-label">Postal Code</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="postal_code_so" name="postal_code"
                                        placeholder="Enter Postal Code" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="country_so" class="form-control-label">Country</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="text" id="country_so" name="country"
                                        placeholder="Enter Country" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col col-md-3">
                                    <label for="delivery_date_so" class="form-control-label">Expected Delivery Date</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <input type="date" id="delivery_date_so" name="delivery_date"
                                        class="form-control">
                                </div>
                            </div>

                            <hr class="mt-4 mb-4">
                            <h4 class="text-center mb-3">Order Items</h4>

                            <!-- Dynamic Product Selection Section -->
                            <div id="productRowsSO">
                                <!-- Initial Product row template -->
                                <div class="row form-group product-row-so mb-3">
                                    <div class="col-md-5 col-12 mb-2 mb-md-0">
                                        <label for="product_so_0" class="form-control-label">Product</label>
                                        <select name="products[]" id="product_so_0" class="form-control" required>
                                            <option value="">-- Select Product --</option>
                                            <?php foreach ($all_products_for_so as $p): ?>
                                                <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                                    <?php echo htmlspecialchars($p['name']); ?> -
                                                    $<?php echo htmlspecialchars($p['price']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-12 mb-2 mb-md-0">
                                        <label for="quantity_so_0" class="form-control-label">Quantity</label>
                                        <input type="number" id="quantity_so_0" name="quantities[]"
                                            placeholder="Qty" class="form-control" required min="1">
                                    </div>
                                    <div class="col-md-2 col-12 d-flex align-items-end">
                                        <button type="button"
                                            class="btn btn-danger btn-sm removeRowSO w-100">X</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add New Product Button -->
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-sm"
                                    id="addProductSO">+ Add Product</button>
                            </div>

                            <!-- Submit Button -->
                            <div class="card-footer text-end mt-4">
                                <button type="submit" class="btn btn-primary btn-sm me-2">
                                    <i class="fa fa-dot-circle-o"></i> Create Sales Order
                                </button>
                                <button type="reset" class="btn btn-danger btn-sm">
                                    <i class="fa fa-ban"></i> Reset
                                </button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="../edits/manage_sales_order.php">View All Sales Orders</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            const allProductsSO = <?php echo json_encode($all_products_for_so); ?>;
            let productRowCountSO = 0; // Initialize with 0 as the first row is already there

            // Function to generate a new product row HTML
            function generateProductRowHtml(index) {
                let productOptionsSO = '<option value="">-- Select Product --</option>';
                allProductsSO.forEach(function (p) {
                    productOptionsSO += `<option value="${p.id}">${p.name} - $${p.price}</option>`;
                });

                return `
                    <div class="row form-group product-row-so mb-3">
                        <div class="col-md-5 col-12 mb-2 mb-md-0">
                            <label for="product_so_${index}" class="form-control-label">Product</label>
                            <select name="products[]" id="product_so_${index}" class="form-control" required>
                                ${productOptionsSO}
                            </select>
                        </div>
                        <div class="col-md-3 col-12 mb-2 mb-md-0">
                            <label for="quantity_so_${index}" class="form-control-label">Quantity</label>
                            <input type="number" id="quantity_so_${index}" name="quantities[]" placeholder="Qty" class="form-control" required min="1">
                        </div>
                        <div class="col-md-2 col-12 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm removeRowSO w-100">X</button>
                        </div>
                    </div>
                `;
            }

            // Add New Product Button Click Handler
            $('#addProductSO').on('click', function () {
                productRowCountSO++;
                const newRowHtml = generateProductRowHtml(productRowCountSO);
                $('#productRowsSO').append(newRowHtml);
            });

            // Event listener for removing product rows (delegated event for dynamically added elements)
            $(document).on('click', '.removeRowSO', function () {
                // Ensure at least one product row remains
                if ($('#productRowsSO .product-row-so').length > 1) {
                    $(this).closest('.product-row-so').remove();
                } else {
                    // Using a Bootstrap modal or a custom message box would be better than alert()
                    // For now, keeping alert as per original code's style, but noting the improvement.
                    alert("A sales order must have at least one product.");
                }
            });
        });
    </script>
</body>
</html>
