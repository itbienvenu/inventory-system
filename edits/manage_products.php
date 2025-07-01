<?php
include_once "../config/auth.php"; // Adjust path as necessary
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['admin','executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

// --- Dashboard Data Fetching for Products Overview ---
// Total Unique Products
$total_products_count_query = mysqli_query($conn, "SELECT COUNT(id) AS total_products FROM products");
$total_products_count = $total_products_count_query ? mysqli_fetch_assoc($total_products_count_query)['total_products'] : 0;

// Total Quantity in Stock
$total_quantity_in_stock_query = mysqli_query($conn, "SELECT SUM(quantity) AS total_qty FROM products");
$total_quantity_in_stock = $total_quantity_in_stock_query ? (int)mysqli_fetch_assoc($total_quantity_in_stock_query)['total_qty'] : 0;

// Products Below Reorder Level (using 'low_stock' column)
$products_low_stock_query = mysqli_query($conn, "SELECT COUNT(id) AS low_stock_count FROM products WHERE quantity <= low_stock");
$products_low_stock_count = $products_low_stock_query ? mysqli_fetch_assoc($products_low_stock_query)['low_stock_count'] : 0;

// Total Inventory Value (Cost Price)
$total_inventory_value_cost_query = mysqli_query($conn, "SELECT SUM(quantity * cost_price) AS total_cost_value FROM products");
$total_inventory_value_cost = $total_inventory_value_cost_query ? (float)mysqli_fetch_assoc($total_inventory_value_cost_query)['total_cost_value'] : 0.00;


// --- Product Table Data Fetching with Optional Filters ---
$conditions = [];
$page_title = "Manage Products";

// Check for low stock filter
if (isset($_GET['filter']) && $_GET['filter'] === 'low_stock') {
    $conditions[] = "quantity <= low_stock"; // Using 'low_stock' column
    $page_title = "Low Stock Products";
}

// Existing search filter
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $conditions[] = "(name LIKE '%$search%' OR sku LIKE '%$search%')";
}

// Existing category filter (using 'category' VARCHAR column)
if (!empty($_GET['category'])) {
    $category_filter = mysqli_real_escape_string($conn, $_GET['category']);
    $conditions[] = "category = '$category_filter'";
}

// Existing supplier filter (using 'supplier' VARCHAR column)
if (!empty($_GET['supplier'])) {
    $supplier_filter = mysqli_real_escape_string($conn, $_GET['supplier']);
    $conditions[] = "supplier = '$supplier_filter'";
}


$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Fetch all products for the table - NO JOINS needed as category/supplier are VARCHAR
$products_query = mysqli_query($conn, "
    SELECT *
    FROM products
    $where_clause
    ORDER BY name ASC
");

if (!$products_query) {
    die("Error fetching products: " . mysqli_error($conn));
}

// Fetch distinct categories for filter dropdown (from 'products' table directly)
$categories_filter_query = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$filter_categories = [];
if ($categories_filter_query) {
    while ($cat = mysqli_fetch_assoc($categories_filter_query)) {
        $filter_categories[] = $cat['category'];
    }
}

// Fetch distinct suppliers for filter dropdown (from 'products' table directly)
$suppliers_filter_query = mysqli_query($conn, "SELECT DISTINCT supplier FROM products WHERE supplier IS NOT NULL AND supplier != '' ORDER BY supplier ASC");
$filter_suppliers = [];
if ($suppliers_filter_query) {
    while ($sup = mysqli_fetch_assoc($suppliers_filter_query)) {
        $filter_suppliers[] = $sup['supplier'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody tr:hover {
            background-color: #f2f2f2;
        }
        .action-buttons a, .action-buttons button {
            margin-right: 5px;
            margin-bottom: 5px; /* Spacing for smaller screens */
        }
        .stock-low {
            color: #dc3545; /* Red for low stock */
            font-weight: bold;
        }
        .stock-ok {
            color: #28a745; /* Green for healthy stock */
        }
        /* Overview card styles (copied from previous dashboard for consistency) */
        .overview-item {
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            position: relative;
            height: 140px; /* Fixed height for consistency */
            display: flex;
            align-items: center;
            padding: 20px;
        }
        .overview-item .icon {
            font-size: 40px;
            color: rgba(255, 255, 255, 0.8);
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        .overview-item .text h2 {
            font-size: 2.2rem;
            margin-bottom: 5px;
            font-weight: 700;
            color: white; /* Text color for value */
        }
        .overview-item .text span {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
        }
        .overview-item.bg-primary { background-color: #007bff !important; }
        .overview-item.bg-success { background-color: #28a745 !important; }
        .overview-item.bg-warning { background-color: #ffc107 !important; }
        .overview-item.bg-info { background-color: #17a2b8 !important; }

        .filter-form .form-control, .filter-form .btn {
            border-radius: 0.5rem; /* Slightly rounded for filters */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center"><?php echo $page_title; ?></h2>

        <!-- Success/Error Message Display -->
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <!-- Product Overview Cards -->
        <div class="row m-b-25">
            <div class="col-sm-6 col-lg-3">
                <div class="overview-item bg-primary">
                    <div class="text">
                        <h2><?php echo number_format($total_products_count); ?></h2>
                        <span>Total Products</span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="overview-item bg-success">
                    <div class="text">
                        <h2><?php echo number_format($total_quantity_in_stock); ?></h2>
                        <span>Total Quantity in Stock</span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="overview-item bg-warning">
                    <div class="text">
                        <h2><?php echo number_format($products_low_stock_count); ?></h2>
                        <span>Products Low Stock</span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="overview-item bg-info">
                    <div class="text">
                        <h2>$<?php echo number_format($total_inventory_value_cost, 2); ?></h2>
                        <span>Inventory Value (Cost)</span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Product List
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" class="row g-3 mb-4 align-items-end filter-form">
                    <div class="col-md-4">
                        <label for="search_input" class="form-label">Search Product</label>
                        <input type="text" class="form-control" id="search_input" name="search" placeholder="Name or SKU" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="category_filter" class="form-label">Category</label>
                        <select class="form-select" id="category_filter" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($filter_categories as $cat) : ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"
                                    <?php echo (isset($_GET['category']) && $_GET['category'] == $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="supplier_filter" class="form-label">Supplier</label>
                        <select class="form-select" id="supplier_filter" name="supplier">
                            <option value="">All Suppliers</option>
                            <?php foreach ($filter_suppliers as $sup) : ?>
                                <option value="<?php echo htmlspecialchars($sup); ?>"
                                    <?php echo (isset($_GET['supplier']) && $_GET['supplier'] == $sup) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sup); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </div>
                    <?php if (isset($_GET['filter']) || isset($_GET['search']) || isset($_GET['category']) || isset($_GET['supplier'])) : ?>
                        <div class="col-12 text-end">
                            <a href="manage_products.php" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Low Stock</th>
                                <th class="text-right">Cost Price</th>
                                <th class="text-right">Selling Price</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($products_query) > 0) : ?>
                                <?php while ($product = mysqli_fetch_assoc($products_query)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                        <td class="text-right <?php echo ($product['quantity'] <= $product['low_stock']) ? 'stock-low' : 'stock-ok'; ?>">
                                            <?php echo htmlspecialchars($product['quantity']); ?>
                                        </td>
                                        <td class="text-right"><?php echo htmlspecialchars($product['low_stock']); ?></td>
                                        <td class="text-right">$<?php echo number_format($product['cost_price'], 2); ?></td>
                                        <td class="text-right">$<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($product['category'] ? $product['category'] : 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($product['supplier'] ? $product['supplier'] : 'N/A'); ?></td>
                                        <td class="action-buttons text-center">
                                            <a href="view_product.php?id=<?php echo urlencode($product['id']); ?>" class="btn btn-primary btn-sm rounded-pill">View</a>
                                            <a href="edit_product.php?id=<?php echo urlencode($product['id']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-product-id="<?php echo htmlspecialchars($product['id']); ?>" data-product-name="<?php echo htmlspecialchars($product['name']); ?>">Delete</button>
                                            <a href="adjust_inventory.php?id=<?php echo urlencode($product['id']); ?>" class="btn btn-info btn-sm rounded-pill mt-1">Adjust Stock</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9" class="text-center">No products found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <a href="../executive/products.php" class="btn btn-success rounded-pill px-4 ms-2">Add New Product</a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete product: <strong id="productNameToDelete"></strong> (ID: <strong id="productIdToDelete"></strong>)? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let productIdToDelete = '';
            let productNameToDelete = '';

            // When the delete button is clicked, set the product ID and name in the modal
            $('.delete-btn').on('click', function() {
                productIdToDelete = $(this).data('product-id');
                productNameToDelete = $(this).data('product-name');
                $('#productIdToDelete').text(productIdToDelete);
                $('#productNameToDelete').text(productNameToDelete);
            });

            // When the confirm delete button in the modal is clicked
            $('#confirmDeleteButton').on('click', function() {
                // Redirect to the delete script with the product ID
                window.location.href = '../edits/delete_product.php?id=' + encodeURIComponent(productIdToDelete);
            });
        });
    </script>
</body>
</html>
