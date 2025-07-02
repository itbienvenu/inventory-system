<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file
$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if (!isset($_GET['dn']) || empty($_GET['dn'])) {
    die("Delivery Note number not provided.");
}

$delivery_note_number = mysqli_real_escape_string($conn, $_GET['dn']);

// Fetch delivery note header details
$dn_query = mysqli_query($conn, "
    SELECT dn.*, u.names AS created_by_username, so.order_number AS sales_order_number
    FROM delivery_notes dn
    JOIN users u ON dn.created_by = u.id
    LEFT JOIN sales_orders so ON dn.sales_order_id = so.id
    WHERE dn.delivery_note_number = '$delivery_note_number'
");

if (!$dn_query) {
    die("Error fetching delivery note: " . mysqli_error($conn));
}

$delivery_note = mysqli_fetch_assoc($dn_query);

if (!$delivery_note) {
    die("Delivery Note with number '$delivery_note_number' not found.");
}

// Fetch delivery note items
$items_query = mysqli_query($conn, "
    SELECT
        dni.*,
        p.name AS product_name,
        p.sku AS product_sku
    FROM delivery_note_items dni
    LEFT JOIN products p ON dni.product_id = p.id
    WHERE dni.delivery_note_id = {$delivery_note['id']}
");

if (!$items_query) {
    die("Error fetching delivery note items: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note #<?php echo htmlspecialchars($delivery_note['delivery_note_number']); ?></title>
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
            background-color: #17a2b8; /* Info color for Delivery Notes */
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
        .dn-details strong {
            color: #343a40;
        }
        .text-right {
            text-align: right;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
            border-radius: 0.5rem;
            text-transform: capitalize;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Delivery Note Details</h2>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Delivery Note #<?php echo htmlspecialchars($delivery_note['delivery_note_number']); ?>
            </div>
            <div class="card-body">
                <div class="row dn-details">
                    <div class="col-md-6">
                        <p><strong>Customer Company:</strong> <?php echo htmlspecialchars($delivery_note['customer_company']); ?></p>
                        <p><strong>Street:</strong> <?php echo htmlspecialchars($delivery_note['customer_street']); ?></p>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($delivery_note['customer_city']); ?></p>
                        <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($delivery_note['customer_postal_code']); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($delivery_note['customer_country']); ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>Linked Sales Order:</strong>
                            <?php if ($delivery_note['sales_order_number']) : ?>
                                <a href="view_sales_order.php?order=<?php echo urlencode($delivery_note['sales_order_number']); ?>">
                                    <?php echo htmlspecialchars($delivery_note['sales_order_number']); ?>
                                </a>
                            <?php else : ?>
                                N/A
                            <?php endif; ?>
                        </p>
                        <p><strong>Shipping Date:</strong> <?php echo htmlspecialchars($delivery_note['shipping_date']); ?></p>
                        <p><strong>Delivered By:</strong> <?php echo htmlspecialchars($delivery_note['delivered_by']); ?></p>
                        <p><strong>Recipient Name:</strong> <?php echo htmlspecialchars($delivery_note['recipient_name'] ? $delivery_note['recipient_name'] : 'Pending'); ?></p>
                        <p><strong>Received At:</strong> <?php echo htmlspecialchars($delivery_note['received_at'] ? $delivery_note['received_at'] : 'Not yet signed'); ?></p>
                    </div>
                </div>
                <hr>
                <div class="row dn-details">
                    <div class="col-md-12">
                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($delivery_note['notes']); ?></p>
                        <p><strong>Created By:</strong> <?php echo htmlspecialchars($delivery_note['created_by_username']); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($delivery_note['created_at']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Items Shipped
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th class="text-right">Quantity Shipped</th>
                                <th>Item Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_query)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['product_sku']); ?></td>
                                    <td class="text-right"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($item['notes'] ? $item['notes'] : 'N/A'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <!-- You can add more actions here, e.g., edit, generate PDF, mark as received -->
            <!-- <a href="edit_delivery_note.php?dn=<?php echo htmlspecialchars($delivery_note['delivery_note_number']); ?>" class="btn btn-warning rounded-pill px-4 ms-2">Edit Note</a> -->
            <a href="generate_delivery_note_pdf.php?dn=<?php echo htmlspecialchars($delivery_note['delivery_note_number']); ?>" class="btn btn-info rounded-pill px-4 ms-2">Download PDF</a>
            <a href="manage_delivery_note.php" class="btn btn-primary rounded-pill px-4 ms-2">View others</a>
            <!-- Button to mark as received -->
            <button type="button" class="btn btn-success rounded-pill px-4 ms-2" data-bs-toggle="modal" data-bs-target="#markReceivedModal" data-dn-number="<?php echo htmlspecialchars($delivery_note['delivery_note_number']); ?>">Mark as Received</button>
        </div>
    </div>

    <!-- Mark as Received Modal -->
    <div class="modal fade" id="markReceivedModal" tabindex="-1" aria-labelledby="markReceivedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="../functions/MarkDeliveryReceived.php" method="POST">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="markReceivedModalLabel">Confirm Receipt</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Confirm receipt for Delivery Note #<strong id="dnNumberToReceive"></strong>.</p>
                        <input type="hidden" name="delivery_note_number" id="hiddenDnNumber" value="<?php echo $delivery_note_number; ?>">
                        <div class="form-group mb-3">
                            <label for="recipientName" class="form-label">Recipient's Name</label>
                            <input type="text" class="form-control rounded-pill px-3" id="recipientName" name="recipient_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill">Mark as Received</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Populate modal when Mark as Received button is clicked
            $('#markReceivedModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget); // Button that triggered the modal
                const dnNumber = button.data('dn-number'); // Extract info from data-* attributes
                const modal = $(this);
                modal.find('#dnNumberToReceive').text(dnNumber);
                modal.find('#hiddenDnNumber').val(dnNumber);
            });
        });
    </script>
</body>
</html>
