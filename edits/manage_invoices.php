<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

// Check if the user is authorized (e.g., an executive or admin)
// You might want to allow 'admin' or other roles to view/manage this list.
$allowed_roles = ['admin', 'executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

// Fetch all invoices
$invoices_query = mysqli_query($conn, "
    SELECT i.*, u.names AS created_by_username
    FROM invoices i
    JOIN users u ON i.created_by = u.id
    ORDER BY i.created_at DESC
");

if (!$invoices_query) {
    die("Error fetching invoices: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Invoices</title>
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
            background-color: #0d6efd; /* Primary blue for invoice management */
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
        }
        .status-badge {
            font-size: 0.8em;
            padding: 0.4em 0.7em;
            border-radius: 0.5rem;
            text-transform: capitalize;
        }
        .status-unpaid { background-color: #dc3545; color: white; } /* Danger */
        .status-paid { background-color: #28a745; color: white; } /* Success */
        .status-partially_paid { background-color: #ffc107; color: #343a40; } /* Warning */
        .status-cancelled { background-color: #6c757d; color: white; } /* Secondary */
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Manage Sales Invoices</h2>

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
                List of Invoices
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Company</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($invoices_query) > 0) : ?>
                                <?php while ($invoice = mysqli_fetch_assoc($invoices_query)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                                        <td><?php echo htmlspecialchars($invoice['company']); ?></td>
                                        <td>$<?php echo number_format($invoice['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo str_replace(' ', '_', strtolower(htmlspecialchars($invoice['status']))); ?>">
                                                <?php echo htmlspecialchars(ucfirst($invoice['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($invoice['created_by_username']); ?></td>
                                        <td><?php echo htmlspecialchars($invoice['created_at']); ?></td>
                                        <td class="action-buttons text-center">
                                            <a href="view_invoice.php?invoice=<?php echo urlencode($invoice['invoice_number']); ?>" class="btn btn-primary btn-sm rounded-pill">View</a>
                                            <!-- Link to edit invoice (create edit_invoice.php later) -->
                                            <!-- <a href="edit_invoice.php?invoice=<?php echo urlencode($invoice['invoice_number']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a> -->
                                            <!-- Link to download invoice PDF (create generate_invoice_pdf.php later) -->
                                            <!-- <a href="generate_invoice_pdf.php?invoice=<?php echo urlencode($invoice['invoice_number']); ?>" class="btn btn-info btn-sm rounded-pill">PDF</a> -->
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-invoice-number="<?php echo htmlspecialchars($invoice['invoice_number']); ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">No sales invoices found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <a href="create_invoice.php" class="btn btn-success rounded-pill px-4 ms-2">Create New Invoice</a>
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
                    Are you sure you want to delete invoice #<strong id="invoiceNumberToDelete"></strong>? This action cannot be undone.
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
            let invoiceToDelete = '';

            // When the delete button is clicked, set the invoice number in the modal
            $('.delete-btn').on('click', function() {
                invoiceToDelete = $(this).data('invoice-number');
                $('#invoiceNumberToDelete').text(invoiceToDelete);
            });

            // When the confirm delete button in the modal is clicked
            $('#confirmDeleteButton').on('click', function() {
                // Redirect to the delete script with the invoice number
                window.location.href = 'delete_invoice.php?invoice=' + encodeURIComponent(invoiceToDelete);
            });
        });
    </script>
</body>
</html>
