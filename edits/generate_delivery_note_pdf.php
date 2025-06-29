<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

// --- DOMPDF Setup ---
require_once __DIR__.'/../packages/dompdf/autoload.inc.php'; // Adjust this path to your dompdf library

use Dompdf\Dompdf;
use Dompdf\Options;
// --- End DOMPDF Setup ---

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if (!isset($_GET['dn']) || empty($_GET['dn'])) {
    die("Delivery Note number not provided for PDF generation.");
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
    die("Error fetching delivery note for PDF: " . mysqli_error($conn));
}

$delivery_note = mysqli_fetch_assoc($dn_query);

if (!$delivery_note) {
    die("Delivery Note with number '$delivery_note_number' not found for PDF generation.");
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
    die("Error fetching delivery note items for PDF: " . mysqli_error($conn));
}

// --- Record the download before rendering PDF ---
$document_type = 'delivery_note';
$document_id = $delivery_note['id'];
$downloaded_by = $_SESSION['user_id']; // Assuming user_id is stored in session

$insert_download_sql = "INSERT INTO document_downloads (document_type, document_id, document_number, downloaded_by) VALUES (
    '$document_type', $document_id, '{$delivery_note['delivery_note_number']}', $downloaded_by
)";
mysqli_query($conn, $insert_download_sql);
// --- End Record Download ---

ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note #<?php echo htmlspecialchars($delivery_note['delivery_note_number']); ?></title>
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
        .document-title { /* Changed from .quotation-title */
            font-size: 24px;
            font-weight: bold;
            color: #17a2b8; /* Info color for Delivery Note */
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
            background-color: #17a2b8; /* Info color for Delivery Note headers */
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
            background-color: #17a2b8; /* Info color for Delivery Note table header */
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

        /* Notes section for Delivery Note (no totals usually) */
        .general-notes-section {
            clear: both;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .general-notes-section p {
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
                <div class="document-title">Delivery Note</div>
                <div class="info-grid">
                    <p>DN Date: <?php echo htmlspecialchars(date('DD/MM/YYYY', strtotime($delivery_note['shipping_date']))); ?></p>
                    <p>DN No: #<?php echo htmlspecialchars($delivery_note['delivery_note_number']); ?></p>
                    <p>Linked SO: <?php echo $delivery_note['sales_order_number'] ? htmlspecialchars($delivery_note['sales_order_number']) : 'N/A'; ?></p>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Ship To Section -->
        <div class="section-header">Ship To</div>
        <div class="address-box">
            <div class="address-col">
                <p>Company: <?php echo htmlspecialchars($delivery_note['customer_company']); ?></p>
                <p>Address: <?php echo htmlspecialchars($delivery_note['customer_street']); ?></p>
                <p>Town, City: <?php echo htmlspecialchars($delivery_note['customer_city']); ?></p>
                <p>Postal Code: <?php echo htmlspecialchars($delivery_note['customer_postal_code']); ?></p>
                <p>Country: <?php echo htmlspecialchars($delivery_note['customer_country']); ?></p>
            </div>
            <div class="address-col">
                <p><strong>Delivered By:</strong> <?php echo htmlspecialchars($delivery_note['delivered_by']); ?></p>
                <p><strong>Recipient Name:</strong> <?php echo htmlspecialchars($delivery_note['recipient_name'] ? $delivery_note['recipient_name'] : '____________________'); ?></p>
                <p><strong>Received At:</strong> <?php echo htmlspecialchars($delivery_note['received_at'] ? date('Y-m-d H:i:s', strtotime($delivery_note['received_at'])) : '____________________'); ?></p>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Items Table Section -->
        <div class="section-header">Items Included</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Product Name (SKU)</th>
                    <th class="text-center" style="width: 20%;">Quantity</th>
                    <th style="width: 20%;">Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($items_query, 0); // Reset result pointer for display
                while ($item = mysqli_fetch_assoc($items_query)) :
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?> (<?php echo htmlspecialchars($item['product_sku']); ?>)</td>
                        <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['notes'] ? $item['notes'] : 'N/A'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- General Notes Section -->
        <div class="general-notes-section">
            <div class="section-header">General Notes</div>
            <p><?php echo htmlspecialchars($delivery_note['notes'] ? $delivery_note['notes'] : 'No general notes for this delivery.'); ?></p>
        </div>

        <div style="clear: both;"></div>

        <!-- Thank You Section -->
        <div class="thank-you-section">
            <strong>Thank you for your business!</strong>
            <p>Please inspect the goods upon receipt. Any discrepancies should be reported within 24 hours.</p>
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

$filename = "Delivery_Note_" . str_replace('/', '_', $delivery_note['delivery_note_number']) . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit();
?>
