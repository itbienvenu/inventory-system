<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

if ($_SESSION['role'] !== 'executive') { // Assuming executive can view POs
    die("Unauthorized access.");
}

if (!isset($_GET['po']) || empty($_GET['po'])) {
    die("Purchase Order number not provided.");
}

$po_number = mysqli_real_escape_string($conn, $_GET['po']);

// Fetch purchase order header details
$po_query = mysqli_query($conn, "
    SELECT po.*, u.names AS created_by_username
    FROM purchase_orders po
    JOIN users u ON po.created_by = u.id
    WHERE po.po_number = '$po_number'
");

if (!$po_query) {
    die("Error fetching Purchase Order: " . mysqli_error($conn));
}

$purchase_order = mysqli_fetch_assoc($po_query);

if (!$purchase_order) {
    die("Purchase Order with number '$po_number' not found.");
}

// Fetch purchase order items
$items_query = mysqli_query($conn, "
    SELECT
        poi.*,
        p.name AS product_name,
        p.sku AS product_sku
    FROM purchase_order_items poi
    LEFT JOIN products p ON poi.product_id = p.id
    WHERE poi.po_id = {$purchase_order['id']}
");

if (!$items_query) {
    die("Error fetching Purchase Order items: " . mysqli_error($conn));
}

// Recalculate total from items for verification (optional, as total is stored)
$grand_total_from_items = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #<?php echo htmlspecialchars($purchase_order['po_number']); ?></title>
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
            background-color: #343a40; /* Dark color for Purchase Orders */
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
        .po-details strong {
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
        /* Specific status badge colors for Purchase Orders */
        .status-pending { background-color: #ffc107; color: #343a40; } /* Warning */
        .status-sent { background-color: #0d6efd; color: white; } /* Primary */
        .status-received { background-color: #28a745; color: white; } /* Success */
        .status-cancelled { background-color: #6c757d; color: white; } /* Secondary */
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Purchase Order Details</h2>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Purchase Order #<?php echo htmlspecialchars($purchase_order['po_number']); ?>
            </div>
            <div class="card-body">
                <div class="row po-details">
                    <div class="col-md-6">
                        <p><strong>Supplier Name:</strong> <?php echo htmlspecialchars($purchase_order['supplier_name']); ?></p>
                        <p><strong>Contact Person:</strong> <?php echo htmlspecialchars($purchase_order['supplier_contact_person']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($purchase_order['supplier_email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($purchase_order['supplier_phone']); ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($purchase_order['supplier_address']); ?></p>
                        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($purchase_order['order_date']); ?></p>
                        <p><strong>Expected Delivery:</strong>
                            <?php echo $purchase_order['expected_delivery_date'] ? htmlspecialchars($purchase_order['expected_delivery_date']) : 'N/A'; ?>
                        </p>
                        <p><strong>Status:</strong>
                            <span class="status-badge status-<?php echo str_replace(' ', '_', strtolower(htmlspecialchars($purchase_order['status']))); ?>">
                                <?php echo htmlspecialchars(ucfirst($purchase_order['status'])); ?>
                            </span>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row po-details">
                    <div class="col-md-12">
                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($purchase_order['notes']); ?></p>
                        <p><strong>Created By:</strong> <?php echo htmlspecialchars($purchase_order['created_by_username']); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($purchase_order['created_at']); ?></p>
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
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Total Cost</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_query)) :
                                $grand_total_from_items += $item['total_cost'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['product_sku']); ?></td>
                                    <td class="text-right">$<?php echo number_format($item['unit_cost'], 2); ?></td>
                                    <td class="text-right"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td class="text-right">$<?php echo number_format($item['total_cost'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['notes'] ? $item['notes'] : 'N/A'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Purchase Order Total:</th>
                                <th class="text-right">$<?php echo number_format($purchase_order['total_amount'], 2); ?></th>
                                <th></th> <!-- Empty th for notes column -->
                            </tr>
                            <?php if (abs($purchase_order['total_amount'] - $grand_total_from_items) > 0.01) : ?>
                            <tr>
                                <td colspan="6" class="text-danger text-center">
                                    <em>Warning: Calculated item total ($<?php echo number_format($grand_total_from_items, 2); ?>) does not match stored PO total.</em>
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
            <!-- Add more actions here, e.g., edit, generate PDF, mark as received -->
            <!-- <a href="edit_purchase_order.php?po=<?php echo htmlspecialchars($purchase_order['po_number']); ?>" class="btn btn-warning rounded-pill px-4 ms-2">Edit Order</a> -->
            <!-- <a href="generate_purchase_order_pdf.php?po=<?php echo htmlspecialchars($purchase_order['po_number']); ?>" class="btn btn-info rounded-pill px-4 ms-2">Download PDF</a> -->
            <!-- Button to mark as received -->
            <button type="button" class="btn btn-success rounded-pill px-4 ms-2" data-bs-toggle="modal" data-bs-target="#markReceivedModal" data-po-number="<?php echo htmlspecialchars($purchase_order['po_number']); ?>">Mark as Received</button>
        </div>
    </div>

    <!-- Mark as Received Modal -->
    <div class="modal fade" id="markReceivedModal" tabindex="-1" aria-labelledby="markReceivedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="../functions/MarkPurchaseOrderReceived.php" method="POST">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="markReceivedModalLabel">Confirm Goods Receipt</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Confirm receipt of goods for Purchase Order #<strong id="poNumberToReceive"></strong>.</p>
                        <input type="hidden" name="po_number" id="hiddenPoNumber">
                        <div class="form-group mb-3">
                            <label for="receivedDate" class="form-label">Date Received</label>
                            <input type="date" class="form-control rounded-pill px-3" id="receivedDate" name="received_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="receivedNotes" class="form-label">Receipt Notes (Optional)</label>
                            <textarea class="form-control rounded-pill px-3" id="receivedNotes" name="received_notes" rows="3"></textarea>
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
                const poNumber = button.data('po-number'); // Extract info from data-* attributes
                const modal = $(this);
                modal.find('#poNumberToReceive').text(poNumber);
                modal.find('#hiddenPoNumber').val(poNumber);
            });
        });
    </script>
</body>
</html>
