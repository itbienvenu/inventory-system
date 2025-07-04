<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary
include_once "../functions/SecurityLayer.php";
$allowed_roles = ['executive','admin','daily'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if (!isset($_GET['order']) || empty($_GET['order'])) {
    die("Sales Order number not provided for editing.");
}

$od_number = decryptToken($_GET["order"]);

if (!$od_number || strlen($od_number) < 4) {
    die("Invalid or tampered token.");
}
// $order_number = mysqli_real_escape_string($conn, $od_number);
// $od_number= decryptToken($_GET["order"]);
$order_number = mysqli_real_escape_string($conn, $od_number);

// Fetch sales order header details
$order_query = mysqli_query($conn, "
    SELECT *
    FROM sales_orders
    WHERE order_number = '$order_number'
");

if (!$order_query) {
    die("Error fetching sales order for editing: " . mysqli_error($conn));
}

$order_data = mysqli_fetch_assoc($order_query);

if (!$order_data) {
    die("Sales Order with number '$order_number' not found for editing.");
}

$order_id = $order_data['id'];

// Fetch sales order items
$items_query = mysqli_query($conn, "
    SELECT soi.*, p.name AS product_name, p.price AS product_price
    FROM sales_order_items soi
    JOIN products p ON soi.product_id = p.id
    WHERE soi.order_id = $order_id
");

if (!$items_query) {
    die("Error fetching sales order items for editing: " . mysqli_error($conn));
}

$sales_order_items = [];
while ($item = mysqli_fetch_assoc($items_query)) {
    $sales_order_items[] = $item;
}

// Fetch all products for the dropdown
$all_products_query_edit = mysqli_query($conn, "SELECT id, name, price FROM products");
$all_products_edit = [];
while ($p = mysqli_fetch_assoc($all_products_query_edit)) {
    $all_products_edit[] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sales Order #<?php echo htmlspecialchars($order_data['order_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #ffc107; /* Warning color for edit */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .product-row .form-control {
            border-radius: 5px;
        }
        .btn-danger, .btn-success {
            border-radius: 5px;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .rounded-pill.px-3 {
            border-radius: 50rem !important; /* Ensure consistent rounded pills */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Edit Sales Order #<?php echo htmlspecialchars($order_data['order_number']); ?></h2>

        <form action="../functions/UpdateSalesOrder.php" method="POST">
            <!-- Hidden field to send order ID -->
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_data['id']); ?>">
            <input type="hidden" name="order_number" value="<?php echo htmlspecialchars($order_data['order_number']); ?>">

            <div class="row">
                <!-- Customer Info Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Customer Info</strong></div>
                        <div class="card-body card-block">
                            <div class="form-group mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($order_data['company']); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">VAT Number</label>
                                <input type="text" name="vat" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($order_data['vat']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Street</label>
                                <input type="text" name="street" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($order_data['street']); ?>">
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($order_data['city']); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($order_data['postal_code']); ?>">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($order_data['country']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Expected Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($order_data['delivery_date']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control rounded-pill px-3">
                                    <option value="pending" <?php echo ($order_data['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo ($order_data['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="shipped" <?php echo ($order_data['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="cancelled" <?php echo ($order_data['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Product Selection Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Products</strong></div>
                        <div class="card-body card-block">
                            <div id="productRowsSO">
                                <?php if (empty($sales_order_items)) : ?>
                                    <!-- Default empty row if no items -->
                                    <div class="form-row mb-2 product-row-so d-flex align-items-center">
                                        <div class="col-7 pe-1">
                                            <select name="products[]" class="form-control rounded-pill px-3" required>
                                                <option value="">-- Select Product --</option>
                                                <?php foreach ($all_products_edit as $p) : ?>
                                                    <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                                        <?php echo htmlspecialchars($p['name']); ?> - $<?php echo htmlspecialchars($p['price']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-3 pe-1">
                                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                                        </div>
                                        <div class="col-2 ps-0">
                                            <button type="button" class="btn btn-danger btn-sm removeRowSO rounded-pill">X</button>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($sales_order_items as $item) : ?>
                                        <div class="form-row mb-2 product-row-so d-flex align-items-center">
                                            <div class="col-7 pe-1">
                                                <select name="products[]" class="form-control rounded-pill px-3" required>
                                                    <option value="">-- Select Product --</option>
                                                    <?php foreach ($all_products_edit as $p) : ?>
                                                        <option value="<?php echo htmlspecialchars($p['id']); ?>"
                                                            <?php echo ($p['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($p['name']); ?> - $<?php echo htmlspecialchars($p['price']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-3 pe-1">
                                                <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" value="<?php echo htmlspecialchars($item['quantity']); ?>" required min="1">
                                            </div>
                                            <div class="col-2 ps-0">
                                                <button type="button" class="btn btn-danger btn-sm removeRowSO rounded-pill">X</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Add New Product Button -->
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-sm rounded-pill px-4" id="addProductSO">+ Add Product</button>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-right mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                <a href="manage_sales_order.php?order=<?php echo htmlspecialchars(encryptToken($order_data['order_number'])); ?>" class="btn btn-secondary rounded-pill px-4 ms-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const allProductsEdit = <?php echo json_encode($all_products_edit); ?>;
            let productRowCount = $('#productRowsSO .product-row-so').length; // Initialize with existing rows

            $('#addProductSO').on('click', function() {
                productRowCount++;
                let productOptions = '<option value="">-- Select Product --</option>';
                allProductsEdit.forEach(function(p) {
                    productOptions += `<option value="${p.id}">${p.name} - $${p.price}</option>`;
                });

                const productRow = `
                    <div class="form-row mb-2 product-row-so d-flex align-items-center">
                        <div class="col-7 pe-1">
                            <select name="products[]" class="form-control rounded-pill px-3" required>
                                ${productOptions}
                            </select>
                        </div>
                        <div class="col-3 pe-1">
                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                        </div>
                        <div class="col-2 ps-0">
                            <button type="button" class="btn btn-danger btn-sm removeRowSO rounded-pill">X</button>
                        </div>
                    </div>
                `;
                $('#productRowsSO').append(productRow);
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
</body>
</html>
