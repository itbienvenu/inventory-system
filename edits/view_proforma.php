<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary for your config file

// Check if the user is authorized (e.g., an executive)
// You might want to also allow 'admin' or other roles to view proformas.
if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

// Get the invoice number from the URL parameter
if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    die("Proforma invoice number not provided.");
}

$invoice_number = mysqli_real_escape_string($conn, $_GET['invoice']);

// Fetch proforma invoice header details
$invoice_query = mysqli_query($conn, "
    SELECT pi.*, u.names AS created_by_username
    FROM proforma_invoices pi
    JOIN users u ON pi.created_by = u.id
    WHERE pi.invoice_number = '$invoice_number'
");

if (!$invoice_query) {
    die("Error fetching invoice: " . mysqli_error($conn));
}

$invoice = mysqli_fetch_assoc($invoice_query);

if (!$invoice) {
    die("Proforma invoice with number '$invoice_number' not found.");
}

// Fetch proforma items
$items_query = mysqli_query($conn, "
    SELECT
        pti.*,
        p.name AS product_name,
        p.description AS product_description,
        p.sku AS product_sku
    FROM proforma_items pti
    JOIN products p ON pti.product_id = p.id
    WHERE pti.invoice_id = {$invoice['id']}
");

if (!$items_query) {
    die("Error fetching invoice items: " . mysqli_error($conn));
}

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
    <!-- Assuming you are using Bootstrap or a similar CSS framework as indicated by your form HTML -->
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
            background-color: #007bff;
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
        .invoice-details strong {
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
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Proforma Invoice Details</h2>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Proforma Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?>
            </div>
            <div class="card-body">
                <div class="row invoice-details">
                    <div class="col-md-6">
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($invoice['company']); ?></p>
                        <p><strong>VAT Number:</strong> <?php echo htmlspecialchars($invoice['vat']); ?></p>
                        <p><strong>Street:</strong> <?php echo htmlspecialchars($invoice['street']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>City:</strong> <?php echo htmlspecialchars($invoice['city']); ?></p>
                        <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($invoice['postal_code']); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($invoice['country']); ?></p>
                    </div>
                </div>
                <hr>
                <div class="row invoice-details">
                    <div class="col-md-6">
                        <p><strong>Created By:</strong> <?php echo htmlspecialchars($invoice['created_by_username']); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($invoice['created_at']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 rounded-3">
            <div class="card-header">
                Items
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
                                $grand_total += $item['total_price'];
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
                                <th colspan="4" class="text-right">Grand Total:</th>
                                <th class="text-right">$<?php echo number_format($grand_total, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="javascript:history.back()" class="btn btn-secondary rounded-pill px-4">Go Back</a>
            <!-- Added Edit Button -->
            <a href="edit_proforma.php?invoice=<?php echo htmlspecialchars($invoice['invoice_number']); ?>" class="btn btn-warning rounded-pill px-4 ms-2">Edit Proforma</a>
            <!-- Added Download PDF Button -->
            <a href="generate_proforma_pdf.php?invoice=<?php echo htmlspecialchars($invoice['invoice_number']); ?>" class="btn btn-info rounded-pill px-4 ms-2">Download PDF</a>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
