<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

// Fetch all delivery notes
$delivery_notes_query = mysqli_query($conn, "
    SELECT dn.*, u.names AS created_by_username, so.order_number AS sales_order_number
    FROM delivery_notes dn
    JOIN users u ON dn.created_by = u.id
    LEFT JOIN sales_orders so ON dn.sales_order_id = so.id
    ORDER BY dn.created_at DESC
");

if (!$delivery_notes_query) {
    die("Error fetching delivery notes: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Delivery Notes</title>
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
            background-color: #17a2b8; /* Info color for Delivery Notes */
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
        .status-received {
            font-size: 0.8em;
            padding: 0.4em 0.7em;
            border-radius: 0.5rem;
            background-color: #28a745; /* Success */
            color: white;
        }
        .status-pending-receipt {
            font-size: 0.8em;
            padding: 0.4em 0.7em;
            border-radius: 0.5rem;
            background-color: #ffc107; /* Warning */
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Manage Delivery Notes</h2>

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
                List of Delivery Notes
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>DN Number</th>
                                <th>Customer Company</th>
                                <th>Shipping Date</th>
                                <th>Linked SO</th>
                                <th>Delivered By</th>
                                <th>Recipient</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($delivery_notes_query) > 0) : ?>
                                <?php while ($dn = mysqli_fetch_assoc($delivery_notes_query)) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dn['delivery_note_number']); ?></td>
                                        <td><?php echo htmlspecialchars($dn['customer_company']); ?></td>
                                        <td><?php echo htmlspecialchars($dn['shipping_date']); ?></td>
                                        <td>
                                            <?php if ($dn['sales_order_number']) : ?>
                                                <a href="view_sales_order.php?order=<?php echo urlencode($dn['sales_order_number']); ?>">
                                                    <?php echo htmlspecialchars($dn['sales_order_number']); ?>
                                                </a>
                                            <?php else : ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($dn['delivered_by']); ?></td>
                                        <td><?php echo htmlspecialchars($dn['recipient_name'] ? $dn['recipient_name'] : 'Pending'); ?></td>
                                        <td>
                                            <?php if ($dn['received_at']) : ?>
                                                <span class="status-received">Received</span>
                                            <?php else : ?>
                                                <span class="status-pending-receipt">Pending Receipt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($dn['created_by_username']); ?></td>
                                        <td class="action-buttons text-center">
                                            <a href="view_delivery_note.php?dn=<?php echo urlencode($dn['delivery_note_number']); ?>" class="btn btn-primary btn-sm rounded-pill">View</a>
                                            <!-- <a href="edit_delivery_note.php?dn=<?php echo urlencode($dn['delivery_note_number']); ?>" class="btn btn-warning btn-sm rounded-pill">Edit</a> -->
                                            <a href="edit_delivery_note.php?dn=<?php echo urlencode($dn['delivery_note_number']); ?>" class="btn btn-info btn-sm rounded-pill">Edit</a>
                                            <a href="generate_delivery_note_pdf.php?dn=<?php echo urlencode($dn['delivery_note_number']); ?>" class="btn btn-info btn-sm rounded-pill">PDF</a>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-dn-number="<?php echo htmlspecialchars($dn['delivery_note_number']); ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9" class="text-center">No delivery notes found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <a href="../executive/form.php#delivery-note-part" class="btn btn-success rounded-pill px-4 ms-2">Create New Delivery Note</a>
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
                    Are you sure you want to delete Delivery Note #<strong id="dnNumberToDelete"></strong>? This action cannot be undone.
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
            let dnToDelete = '';

            // When the delete button is clicked, set the DN number in the modal
            $('.delete-btn').on('click', function() {
                dnToDelete = $(this).data('dn-number');
                $('#dnNumberToDelete').text(dnToDelete);
            });

            // When the confirm delete button in the modal is clicked
            $('#confirmDeleteButton').on('click', function() {
                // Redirect to the delete script with the DN number
                window.location.href = '../functions/delete_delivery_note.php?dn=' + encodeURIComponent(dnToDelete);
            });
        });
    </script>
</body>
</html>
