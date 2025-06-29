<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

// Check if the user is authorized (e.g., an executive or admin)
// You might want to allow 'admin' or other roles to view/manage this list.
if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

// Fetch all sales orders
$sales_orders_query = mysqli_query($conn, "
    SELECT so.*, u.names AS created_by_username
    FROM sales_orders so
    JOIN users u ON so.created_by = u.id
    ORDER BY so.created_at DESC
");

if (!$sales_orders_query) {
    die("Error fetching sales orders: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sales Orders</title>
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
            background-color: #0d6efd; /* Primary blue for sales order management */
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
            margin-bottom: 5px; /* Add some spacing for smaller screens */
        }
        .status-badge {
            font-size: 0.8em;
            padding: 0.4em 0.7em;
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
        <h2 class="mb-4 text-center">Manage Sales Orders</h2>

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

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                List of Sales Orders
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Company</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($sales_orders_query) > 0) : ?>
                                <?php while ($order = mysqli_fetch_assoc($sales_orders_query)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                        <td><?php echo htmlspecialchars($order['company']); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo str_replace(' ', '_', strtolower(htmlspecialchars($order['status']))); ?>">
                                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                        <td><?php echo htmlspecialchars($order['created_by_username']); ?></td>
                                        <td class="action-buttons text-center">
                                            <a href="view_sales_order.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-primary btn-sm rounded-pill">View</a>
                                            <a href="edit_sales_order.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a>
                                            <a href="generate_sales_order_pdf.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-info btn-sm rounded-pill">PDF</a>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-order-number="<?php echo htmlspecialchars($order['order_number']); ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">No sales orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <a href="../executive/form.php" class="btn btn-success rounded-pill px-4 ms-2">Create New Sales Order</a>
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
                    Are you sure you want to delete Sales Order #<strong id="orderNumberToDelete"></strong>? This action cannot be undone.
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
            let orderToDelete = '';

            // When the delete button is clicked, set the order number in the modal
            $('.delete-btn').on('click', function() {
                orderToDelete = $(this).data('order-number');
                $('#orderNumberToDelete').text(orderToDelete);
            });

            // When the confirm delete button in the modal is clicked
            $('#confirmDeleteButton').on('click', function() {
                // Redirect to the delete script with the order number
                window.location.href = '../functions/delete_sales_order.php?order=' + encodeURIComponent(orderToDelete);
            });
        });
    </script>
</body>
</html>
