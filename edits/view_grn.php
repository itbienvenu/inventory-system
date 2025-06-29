<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

if ($_SESSION['role'] !== 'executive') { // Assuming executive can view GRNs
    die("Unauthorized access.");
}

if (!isset($_GET['grn']) || empty($_GET['grn'])) {
    die("Goods Received Note number not provided.");
}

$grn_number = mysqli_real_escape_string($conn, $_GET['grn']);

// Fetch GRN header details
$grn_query = mysqli_query($conn, "
    SELECT grn.*, u.names AS received_by_username, po.po_number AS purchase_order_number
    FROM goods_received_notes grn
    JOIN users u ON grn.received_by = u.id
    LEFT JOIN purchase_orders po ON grn.po_id = po.id
    WHERE grn.grn_number = '$grn_number'
");

if (!$grn_query) {
    die("Error fetching Goods Received Note: " . mysqli_error($conn));
}

$grn_data = mysqli_fetch_assoc($grn_query);

if (!$grn_data) {
    die("Goods Received Note with number '$grn_number' not found.");
}

// Fetch GRN items
$items_query = mysqli_query($conn, "
    SELECT
        grni.*,
        p.name AS product_name,
        p.sku AS product_sku
    FROM goods_received_note_items grni
    LEFT JOIN products p ON grni.product_id = p.id
    WHERE grni.grn_id = {$grn_data['id']}
");

if (!$items_query) {
    die("Error fetching Goods Received Note items: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goods Received Note #<?php echo htmlspecialchars($grn_data['grn_number']); ?></title>
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
            background-color: #28a745; /* Success color for GRNs */
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
        .grn-details strong {
            color: #343a40;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Goods Received Note Details</h2>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Goods Received Note #<?php echo htmlspecialchars($grn_data['grn_number']); ?>
            </div>
            <div class="card-body">
                <div class="row grn-details">
                    <div class="col-md-6">
                        <p><strong>Supplier Name:</strong> <?php echo htmlspecialchars($grn_data['supplier_name']); ?></p>
                        <p><strong>Receipt Date:</strong> <?php echo htmlspecialchars($grn_data['receipt_date']); ?></p>
                        <p><strong>Received By:</strong> <?php echo htmlspecialchars($grn_data['received_by_username']); ?></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>Linked Purchase Order:</strong>
                            <?php if ($grn_data['purchase_order_number']) : ?>
                                <a href="view_purchase_order.php?po=<?php echo urlencode($grn_data['purchase_order_number']); ?>">
                                    <?php echo htmlspecialchars($grn_data['purchase_order_number']); ?>
                                </a>
                            <?php else : ?>
                                N/A
                            <?php endif; ?>
                        </p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($grn_data['created_at']); ?></p>
                    </div>
                </div>
                <hr>
                <div class="row grn-details">
                    <div class="col-md-12">
                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($grn_data['notes']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Items Received
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th class="text-right">Quantity Received</th>
                                <th>Condition Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_query)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name'] ? $item['product_name'] : 'N/A (Product Deleted)'); ?></td>
                                    <td><?php echo htmlspecialchars($item['product_sku'] ? $item['product_sku'] : 'N/A'); ?></td>
                                    <td class="text-right"><?php echo htmlspecialchars($item['quantity_received']); ?></td>
                                    <td><?php echo htmlspecialchars($item['condition_notes'] ? $item['condition_notes'] : 'Good'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <a href="edit_grn.php?grn=<?php echo urlencode($grn_data['grn_number']); ?>" class="btn btn-warning rounded-pill px-4 ms-2">Edit GRN</a>
            <a href="generate_grn_pdf.php?grn=<?php echo urlencode($grn_data['grn_number']); ?>" class="btn btn-info rounded-pill px-4 ms-2">Download PDF</a>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
