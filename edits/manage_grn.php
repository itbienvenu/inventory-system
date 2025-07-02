<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

// Fetch all Goods Received Notes
$grn_query = mysqli_query($conn, "
    SELECT grn.*, u.names AS received_by_username, po.po_number AS purchase_order_number
    FROM goods_received_notes grn
    JOIN users u ON grn.received_by = u.id
    LEFT JOIN purchase_orders po ON grn.po_id = po.id
    ORDER BY grn.created_at DESC
");

if (!$grn_query) {
    die("Error fetching Goods Received Notes: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Goods Received Notes</title>
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
            background-color: #28a745; /* Success color for GRNs */
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
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Manage Goods Received Notes</h2>

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
                List of Goods Received Notes
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>GRN Number</th>
                                <th>Supplier</th>
                                <th>Receipt Date</th>
                                <th>Linked PO</th>
                                <th>Received By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($grn_query) > 0) : ?>
                                <?php while ($grn = mysqli_fetch_assoc($grn_query)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($grn['grn_number']); ?></td>
                                        <td><?php echo htmlspecialchars($grn['supplier_name']); ?></td>
                                        <td><?php echo htmlspecialchars($grn['receipt_date']); ?></td>
                                        <td>
                                            <?php if ($grn['purchase_order_number']) : ?>
                                                <a href="view_purchase_order.php?po=<?php echo urlencode($grn['purchase_order_number']); ?>">
                                                    <?php echo htmlspecialchars($grn['purchase_order_number']); ?>
                                                </a>
                                            <?php else : ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($grn['received_by_username']); ?></td>
                                        <td class="action-buttons text-center">
                                            <a href="view_grn.php?grn=<?php echo urlencode($grn['grn_number']); ?>" class="btn btn-primary btn-sm rounded-pill">View</a>
                                            <a href="edit_grn.php?grn=<?php echo urlencode($grn['grn_number']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a>
                                            <a href="generate_grn_pdf.php?grn=<?php echo urlencode($grn['grn_number']); ?>" class="btn btn-info btn-sm rounded-pill">PDF</a>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-grn-number="<?php echo htmlspecialchars($grn['grn_number']); ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center">No Goods Received Notes found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <a href="create_grn.php" class="btn btn-success rounded-pill px-4 ms-2">Create New GRN</a>
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
                    Are you sure you want to delete Goods Received Note #<strong id="grnNumberToDelete"></strong>? This action cannot be undone.
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
            let grnToDelete = '';

            // When the delete button is clicked, set the GRN number in the modal
            $('.delete-btn').on('click', function() {
                grnToDelete = $(this).data('grn-number');
                $('#grnNumberToDelete').text(grnToDelete);
            });

            // When the confirm delete button in the modal is clicked
            $('#confirmDeleteButton').on('click', function() {
                // Redirect to the delete script with the GRN number
                window.location.href = '../functions/delete_grn.php?grn=' + encodeURIComponent(grnToDelete);
            });
        });
    </script>
</body>
</html>
