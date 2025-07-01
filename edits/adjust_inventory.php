<?php
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') { // Assuming executive can adjust inventory
    die("Unauthorized access.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Product ID not provided or invalid for adjustment.");
}

$product_id = (int)$_GET['id'];

// Fetch product details for displaying in the adjustment form
// Using 'price' for selling_price and 'low_stock' for reorder_level as per your products table
$product_query = mysqli_query($conn, "
    SELECT id, name, sku, quantity, low_stock AS reorder_level
    FROM products
    WHERE id = $product_id
");

if (!$product_query) {
    die("Error fetching product for inventory adjustment: " . mysqli_error($conn));
}

$product_data = mysqli_fetch_assoc($product_query);

if (!$product_data) {
    die("Product with ID #{$product_id} not found for adjustment.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adjust Inventory for <?php echo htmlspecialchars($product_data['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-color: #17a2b8; /* Info color for Adjustments */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 0 0.25rem rgba(23, 162, 184, 0.25);
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .rounded-pill.px-3 {
            border-radius: 50rem !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Adjust Inventory for: <br> <?php echo htmlspecialchars($product_data['name']); ?> (SKU: <?php echo htmlspecialchars($product_data['sku']); ?>)</h2>

        <div class="card rounded-3 mb-4">
            <div class="card-header">
                <strong>Current Stock: <?php echo number_format($product_data['quantity']); ?></strong>
            </div>
            <div class="card-body card-block">
                <form action="../functions/SaveAdjustment.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_data['id']); ?>">
                    <input type="hidden" name="product_sku" value="<?php echo htmlspecialchars($product_data['sku']); ?>">
                    <input type="hidden" name="current_quantity" value="<?php echo htmlspecialchars($product_data['quantity']); ?>">

                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <div class="form-group mb-3">
                                <label for="adjustment_type" class="form-label">Adjustment Type</label>
                                <select id="adjustment_type" name="adjustment_type" class="form-control rounded-pill px-3" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="add">Add Stock (Inbound)</option>
                                    <option value="deduct">Deduct Stock (Outbound)</option>
                                    <!-- 'correction' type is handled by 'add' or 'deduct' based on the quantity change input -->
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="quantity_change" class="form-label">Quantity to Adjust</label>
                                <input type="number" id="quantity_change" name="quantity_change" class="form-control rounded-pill px-3" placeholder="e.g., 10" required min="1">
                            </div>
                            <div class="form-group mb-3">
                                <label for="adjustment_reason" class="form-label">Reason for Adjustment</label>
                                <textarea id="adjustment_reason" name="notes" rows="4" class="form-control rounded-pill px-3" placeholder="e.g., Damaged items, stock count discrepancy, return from customer, etc." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-end mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Apply Adjustment</button>
                        <a href="view_product.php?id=<?php echo htmlspecialchars($product_data['id']); ?>" class="btn btn-secondary rounded-pill px-4 ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
