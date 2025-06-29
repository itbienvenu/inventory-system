<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

// --- DOMPDF Setup ---
require_once __DIR__.'/../packages/dompdf/autoload.inc.php'; // Adjust this path to your dompdf library

use Dompdf\Dompdf;
use Dompdf\Options;
// --- End DOMPDF Setup ---

if ($_SESSION['role'] !== 'executive') { // Assuming executive can generate PO PDFs
    die("Unauthorized access.");
}

if (!isset($_GET['po']) || empty($_GET['po'])) {
    die("Purchase Order number not provided for PDF generation.");
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
    die("Error fetching Purchase Order for PDF: " . mysqli_error($conn));
}

$purchase_order = mysqli_fetch_assoc($po_query);

if (!$purchase_order) {
    die("Purchase Order with number '$po_number' not found for PDF generation.");
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
    die("Error fetching Purchase Order items for PDF: " . mysqli_error($conn));
}

// Recalculate total from items for verification (optional, as total is stored)
$sub_total_items_pdf = 0;
mysqli_data_seek($items_query, 0); // Reset for calculations
while ($item = mysqli_fetch_assoc($items_query)) {
    $sub_total_items_pdf += $item['total_cost'];
}
mysqli_data_seek($items_query, 0); // Reset again for display

// --- Record the download before rendering PDF ---
$document_type = 'purchase_order';
$document_id = $purchase_order['id'];
$downloaded_by = $_SESSION['user_id']; // Assuming user_id is stored in session

$insert_download_sql = "INSERT INTO document_downloads (document_type, document_id, document_number, downloaded_by) VALUES (
    '$document_type', $document_id, '{$purchase_order['po_number']}', $downloaded_by
)";
mysqli_query($conn, $insert_download_sql); // No need to die() on error, just log it
// --- End Record Download ---

ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #<?php echo htmlspecialchars($purchase_order['po_number']); ?></title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            font-size: 10px;
        }
        .invoice-container { /* Reusing 'invoice-container' for general document container */
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            overflow: hidden;
        }
        .header-left, .header-right {
            width: 48%;
            float: left;
        }
        .header-right {
            float: right;
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #343a40; /* Dark color for Purchase Order */
            margin-bottom: 5px;
        }
        .contact-info p {
            margin: 0;
            line-height: 1.4;
        }
        .info-grid {
            margin-top: 10px;
            line-height: 1.4;
        }
        .info-grid p {
            margin: 0;
        }

        .section-header {
            background-color: #343a40; /* Dark color for Purchase Order headers */
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .address-box {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        .address-col {
            width: 48%;
            float: left;
        }
        .address-col:last-child {
            float: right;
        }
        .address-box p {
            margin: 0;
            line-height: 1.4;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        .items-table th {
            background-color: #343a40; /* Dark color for Purchase Order table header */
            color: white;
            font-weight: bold;
        }
        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        .totals-section {
            margin-top: 20px;
            width: 250px;
            float: right;
            line-height: 1.6;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            border-bottom: 1px dashed #eee;
        }
        .totals-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
            padding-top: 8px;
            margin-top: 8px;
        }
        .totals-row span:first-child {
            font-weight: normal;
            color: #555;
        }

        .notes-section {
            clear: both;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .notes-section p {
            margin: 0;
            line-height: 1.4;
        }

        .thank-you-section {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #555;
        }
        .thank-you-section strong {
            font-size: 16px;
            color: #2c3e50;
        }

        .powered-by {
            text-align: center;
            margin-top: 30px;
            font-size: 9px;
            color: #888;
        }
        .powered-by img {
            height: 18px;
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-left">
                <div class="company-name">Your Company name</div>
                <div class="contact-info">
                    <p>Address, Town, City</p>
                    <p>Phone: Your Phone Number</p>
                    <p>Email: your@email.com</p>
                    <p>Website: www.yourwebsite.com</p>
                </div>
            </div>
            <div class="header-right">
                <div class="document-title">Purchase Order</div>
                <div class="info-grid">
                    <p>PO Date: <?php echo htmlspecialchars(date('DD/MM/YYYY', strtotime($purchase_order['order_date']))); ?></p>
                    <p>PO No: #<?php echo htmlspecialchars($purchase_order['po_number']); ?></p>
                    <p>Expected Delivery: <?php echo $purchase_order['expected_delivery_date'] ? htmlspecialchars(date('DD/MM/YYYY', strtotime($purchase_order['expected_delivery_date']))) : 'N/A'; ?></p>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Supplier Info Section -->
        <div class="section-header">Supplier Information</div>
        <div class="address-box">
            <div class="address-col">
                <p>Supplier Name: <?php echo htmlspecialchars($purchase_order['supplier_name']); ?></p>
                <p>Contact Person: <?php echo htmlspecialchars($purchase_order['supplier_contact_person']); ?></p>
                <p>Email: <?php echo htmlspecialchars($purchase_order['supplier_email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($purchase_order['supplier_phone']); ?></p>
                <p>Address: <?php echo htmlspecialchars($purchase_order['supplier_address']); ?></p>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Items Table Section -->
        <div class="section-header">Items to Purchase</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Product Name (SKU)</th>
                    <th class="text-right" style="width: 15%;">Unit Cost</th>
                    <th class="text-center" style="width: 15%;">Quantity</th>
                    <th class="text-right" style="width: 15%;">Total Cost</th>
                    <th style="width: 10%;">Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($item = mysqli_fetch_assoc($items_query)) :
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?> (<?php echo htmlspecialchars($item['product_sku']); ?>)</td>
                        <td class="text-right">$<?php echo number_format($item['unit_cost'], 2); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td class="text-right">$<?php echo number_format($item['total_cost'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['notes'] ? $item['notes'] : 'N/A'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-row">
                <span>SUBTOTAL</span>
                <span class="text-right">$<?php echo number_format($sub_total_items_pdf, 2); ?></span>
            </div>
            <div class="totals-row">
                <span>TOTAL PO AMOUNT</span>
                <span class="text-right">$<?php echo number_format($purchase_order['total_amount'], 2); ?></span>
            </div>
        </div>
        <div style="clear: both;"></div>

        <!-- General Notes Section -->
        <div class="notes-section">
            <div class="section-header">General Notes</div>
            <p><?php echo htmlspecialchars($purchase_order['notes'] ? $purchase_order['notes'] : 'No general notes for this purchase order.'); ?></p>
        </div>

        <!-- Thank You Section -->
        <div class="thank-you-section">
            <strong>Thank you for your business!</strong>
            <p>Please confirm receipt of this Purchase Order and expected delivery date.</p>
        </div>

        <!-- Powered By Section -->
        <div class="powered-by">
            Generated by Your Inventory System.
            <img src="https://placehold.co/80x18/F0F2F5/333?text=SYSTEM" alt="System Logo" />
        </div>
    </div>
</body>
</html>

<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "Purchase_Order_" . str_replace('/', '_', $purchase_order['po_number']) . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit();
?>
