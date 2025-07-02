<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

// Check if the user is authorized (e.g., an executive)
// You might want to also allow 'admin' or other roles to view sales orders.
$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

// Get the sales order number from the URL parameter
if (!isset($_GET['order']) || empty($_GET['order'])) {
    die("Sales Order number not provided.");
}

$order_number = mysqli_real_escape_string($conn, $_GET['order']);

// Fetch sales order header details
$order_query = mysqli_query($conn, "
    SELECT so.*, u.names AS created_by_username
    FROM sales_orders so
    JOIN users u ON so.created_by = u.id
    WHERE so.order_number = '$order_number'
");

if (!$order_query) {
    die("Error fetching sales order: " . mysqli_error($conn));
}

$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    die("Sales Order with number '$order_number' not found.");
}

// Fetch sales order items
$items_query = mysqli_query($conn, "
    SELECT
        soi.*,
        p.name AS product_name,
        p.description AS product_description,
        p.sku AS product_sku
    FROM sales_order_items soi
    JOIN products p ON soi.product_id = p.id
    WHERE soi.order_id = {$order['id']}
");

if (!$items_query) {
    die("Error fetching sales order items: " . mysqli_error($conn));
}

// Recalculate total from items for verification (optional, as total is stored)
$grand_total_from_items = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order #<?php echo htmlspecialchars($order['order_number']); ?></title>
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
            background-color: #0d6efd; /* Primary color for sales orders */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody tr:hover {
            background-color: #f2f2f2;
        }
        .order-details strong {
            color: #343a40;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            font-size: 1.25em;
            font-weight: bold;
            margin-top: 20px;
            border-top: 2px solid #dee2e6;
            padding-top: 15px;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
            border-radius: 0.5rem;
            text-transform: capitalize;
        }
        /* Specific status badge colors for Sales Orders */
        .status-pending { background-color: #ffc107; color: #343a40; } /* Warning */
        .status-confirmed { background-color: #0d6efd; color: white; } /* Primary */
        .status-shipped { background-color: #28a745; color: white; } /* Success */
        .status-cancelled { background-color: #6c757d; color: white; } /* Secondary */
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Sales Order Details</h2>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Sales Order #<?php echo htmlspecialchars($order['order_number']); ?>
            </div>
            <div class="card-body">
                <div class="row order-details">
                    <div class="col-md-6">
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($order['company']); ?></p>
                        <p><strong>VAT Number:</strong> <?php echo htmlspecialchars($order['vat']); ?></p>
                        <p><strong>Street:</strong> <?php echo htmlspecialchars($order['street']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                        <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($order['postal_code']); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($order['country']); ?></p>
                    </div>
                </div>
                <hr>
                <div class="row order-details">
                    <div class="col-md-6">
                        <p><strong>Created By:</strong> <?php echo htmlspecialchars($order['created_by_username']); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>Order Date:</strong>
                            <?php echo htmlspecialchars($order['order_date']); ?>
                        </p>
                        <p><strong>Expected Delivery:</strong>
                            <?php echo $order['delivery_date'] ? htmlspecialchars($order['delivery_date']) : 'N/A'; ?>
                        </p>
                        <p><strong>Status:</strong>
                            <span class="status-badge status-<?php echo str_replace(' ', '_', strtolower(htmlspecialchars($order['status']))); ?>">
                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Items Ordered
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_query)) :
                                $grand_total_from_items += $item['total_price'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['product_sku']); ?></td>
                                    <td class="text-right">$<?php echo number_format($item['unit_price'], 2); ?></td>
                                    <td class="text-right"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td class="text-right">$<?php echo number_format($item['total_price'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Order Total:</th>
                                <th class="text-right">$<?php echo number_format($order['total_amount'], 2); ?></th>
                            </tr>
                            <?php if (abs($order['total_amount'] - $grand_total_from_items) > 0.01) : ?>
                            <tr>
                                <td colspan="5" class="text-danger text-center">
                                    <em>Warning: Calculated item total ($<?php echo number_format($grand_total_from_items, 2); ?>) does not match stored order total.</em>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <a href="../edits/manage_sales_order.php" class="btn btn-primary rounded-pill px-4">View Others</a>
            <!-- Add more actions here if needed, e.g., print, convert to invoice -->
            <!-- <a href="edit_sales_order.php?order=<?php echo htmlspecialchars($order['order_number']); ?>" class="btn btn-warning rounded-pill px-4 ms-2">Edit Order</a> -->
            <!-- <a href="generate_sales_order_pdf.php?order=<?php echo htmlspecialchars($order['order_number']); ?>" class="btn btn-info rounded-pill px-4 ms-2">Download PDF</a> -->
            <!-- <a href="create_invoice_from_so.php?order=<?php echo htmlspecialchars($order['order_number']); ?>" class="btn btn-success rounded-pill px-4 ms-2">Convert to Invoice</a> -->
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
