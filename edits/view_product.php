<?php
session_start();
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') { // Assuming executive can view products
    die("Unauthorized access.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Product ID not provided or invalid.");
}

$product_id = (int)$_GET['id'];

// Fetch product details
$product_query = mysqli_query($conn, "
    SELECT p.*, c.name AS category_name, s.name AS supplier_name, u.names AS created_by_username
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    LEFT JOIN users u ON p.created_by = u.id
    WHERE p.id = $product_id
");

if (!$product_query) {
    die("Error fetching product details: " . mysqli_error($conn));
}

$product_data = mysqli_fetch_assoc($product_query);

if (!$product_data) {
    die("Product with ID #{$product_id} not found.");
}

// Fetch stock movements for this product
$stock_movements_query = mysqli_query($conn, "
    SELECT sm.*, u.names AS moved_by_username
    FROM stock_movements sm
    LEFT JOIN users u ON sm.moved_by = u.id
    WHERE sm.product_id = $product_id
    ORDER BY sm.movement_timestamp DESC
");

if (!$stock_movements_query) {
    die("Error fetching stock movementss: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details: <?php echo htmlspecialchars($product_data['name']); ?></title>
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
            background-color: #007bff; /* Primary blue for Products */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .product-details strong {
            color: #343a40;
        }
        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody tr:hover {
            background-color: #f2f2f2;
        }
        .stock-movement-in {
            color: #28a745; /* Green for inbound */
            font-weight: bold;
        }
        .stock-movement-out {
            color: #dc3545; /* Red for outbound */
            font-weight: bold;
        }
        .stock-movement-adj {
            color: #ffc107; /* Yellow for adjustments */
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Product Details</h2>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Product: <?php echo htmlspecialchars($product_data['name']); ?> (SKU: <?php echo htmlspecialchars($product_data['sku']); ?>)
            </div>
            <div class="card-body">
                <div class="row product-details mb-3">
                    <div class="col-md-6">
                        <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product_data['name']); ?></p>
                        <p><strong>SKU:</strong> <?php echo htmlspecialchars($product_data['sku']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($product_data['category_name'] ? $product_data['category_name'] : 'N/A'); ?></p>
                        <p><strong>Supplier:</strong> <?php echo htmlspecialchars($product_data['supplier_name'] ? $product_data['supplier_name'] : 'N/A'); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($product_data['description']); ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>Current Quantity:</strong> <?php echo number_format($product_data['quantity']); ?></p>
                        <p><strong>Reorder Level:</strong> <?php echo number_format($product_data['reorder_level']); ?></p>
                        <p><strong>Cost Price:</strong> $<?php echo number_format($product_data['cost_price'], 2); ?></p>
                        <p><strong>Selling Price:</strong> $<?php echo number_format($product_data['selling_price'], 2); ?></p>
                        <p><strong>Total Inventory Value (Cost):</strong> $<?php echo number_format($product_data['quantity'] * $product_data['cost_price'], 2); ?></p>
                        <p><strong>Created By:</strong> <?php echo htmlspecialchars($product_data['created_by_username']); ?> on <?php echo htmlspecialchars($product_data['created_at']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Stock Movement History
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Movement Type</th>
                                <th class="text-right">Quantity Change</th>
                                <th class="text-right">Stock After</th>
                                <th>Reference Document</th>
                                <th>Notes</th>
                                <th>Moved By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($stock_movements_query) > 0) : ?>
                                <?php while ($movement = mysqli_fetch_assoc($stock_movements_query)) :
                                    $qty_class = '';
                                    if (str_contains($movement['movement_type'], 'inbound') || str_contains($movement['movement_type'], 'add')) {
                                        $qty_class = 'stock-movement-in';
                                    } elseif (str_contains($movement['movement_type'], 'outbound') || str_contains($movement['movement_type'], 'deduct')) {
                                        $qty_class = 'stock-movement-out';
                                    } else {
                                        $qty_class = 'stock-movement-adj'; // For other adjustments or neutral types
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($movement['movement_timestamp']); ?></td>
                                        <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $movement['movement_type']))); ?></td>
                                        <td class="text-right <?php echo $qty_class; ?>">
                                            <?php echo ($movement['quantity_change'] > 0 ? '+' : '') . number_format($movement['quantity_change']); ?>
                                        </td>
                                        <td class="text-right"><?php echo number_format($movement['current_stock_after']); ?></td>
                                        <td>
                                            <?php
                                            echo htmlspecialchars($movement['reference_document_type']);
                                            if (!empty($movement['reference_document_number'])) {
                                                echo " #";
                                                // Link to relevant document if applicable (example for SO, PO, GRN)
                                                if ($movement['reference_document_type'] == 'Sales Order') {
                                                    echo '<a href="view_sales_order.php?order=' . urlencode($movement['reference_document_number']) . '">' . htmlspecialchars($movement['reference_document_number']) . '</a>';
                                                } elseif ($movement['reference_document_type'] == 'Purchase Order') {
                                                    echo '<a href="view_purchase_order.php?po=' . urlencode($movement['reference_document_number']) . '">' . htmlspecialchars($movement['reference_document_number']) . '</a>';
                                                } elseif ($movement['reference_document_type'] == 'GRN') {
                                                     echo '<a href="view_grn.php?grn=' . urlencode($movement['reference_document_number']) . '">' . htmlspecialchars($movement['reference_document_number']) . '</a>';
                                                } else {
                                                    echo htmlspecialchars($movement['reference_document_number']);
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($movement['notes']); ?></td>
                                        <td><?php echo htmlspecialchars($movement['moved_by_username'] ? $movement['moved_by_username'] : 'N/A (User Deleted)'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">No stock movement history for this product.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="manage_products.php" class="btn btn-secondary rounded-pill px-4">Back to Products List</a>
            <a href="edit_product.php?id=<?php echo urlspecialchars($product_data['id']); ?>" class="btn btn-warning rounded-pill px-4 ms-2">Edit Product</a>
            <a href="adjust_inventory.php?id=<?php echo urlspecialchars($product_data['id']); ?>" class="btn btn-info rounded-pill px-4 ms-2">Adjust Stock</a>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
