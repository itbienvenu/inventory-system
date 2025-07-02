<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

// --- DOMPDF Setup ---
require_once __DIR__.'/../packages/dompdf/autoload.inc.php'; // Adjust this path to your dompdf library

use Dompdf\Dompdf;
use Dompdf\Options;
// --- End DOMPDF Setup ---

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if (!isset($_GET['grn']) || empty($_GET['grn'])) {
    die("Goods Received Note number not provided for PDF generation.");
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
    die("Error fetching Goods Received Note for PDF: " . mysqli_error($conn));
}

$grn_data_pdf = mysqli_fetch_assoc($grn_query);

if (!$grn_data_pdf) {
    die("Goods Received Note with number '$grn_number' not found for PDF generation.");
}

// Fetch GRN items
$items_query = mysqli_query($conn, "
    SELECT
        grni.*,
        p.name AS product_name,
        p.sku AS product_sku
    FROM goods_received_note_items grni
    LEFT JOIN products p ON grni.product_id = p.id
    WHERE grni.grn_id = {$grn_data_pdf['id']}
");

if (!$items_query) {
    die("Error fetching Goods Received Note items for PDF: " . mysqli_error($conn));
}

// --- Record the download before rendering PDF ---
$document_type = 'goods_received_note';
$document_id = $grn_data_pdf['id'];
$downloaded_by = $_SESSION['user_id']; // Assuming user_id is stored in session

$insert_download_sql = "INSERT INTO document_downloads (document_type, document_id, document_number, downloaded_by) VALUES (
    '$document_type', $document_id, '{$grn_data_pdf['grn_number']}', $downloaded_by
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
    <title>Goods Received Note #<?php echo htmlspecialchars($grn_data_pdf['grn_number']); ?></title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            font-size: 10px;
        }
        .document-container { /* Renamed from invoice-container for broader use */
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
            color: #28a745; /* Success color for GRN */
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
            background-color: #28a745; /* Success color for GRN headers */
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
            background-color: #28a745; /* Success color for GRN table header */
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
    <div class="document-container">
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
                <div class="document-title">Goods Received Note</div>
                <div class="info-grid">
                    <p>GRN Date: <?php echo htmlspecialchars(date('DD/MM/YYYY', strtotime($grn_data_pdf['receipt_date']))); ?></p>
                    <p>GRN No: #<?php echo htmlspecialchars($grn_data_pdf['grn_number']); ?></p>
                    <p>Linked PO: <?php echo $grn_data_pdf['purchase_order_number'] ? htmlspecialchars($grn_data_pdf['purchase_order_number']) : 'N/A'; ?></p>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Supplier Info Section -->
        <div class="section-header">Supplier Information</div>
        <div class="address-box">
            <div class="address-col">
                <p>Supplier Name: <?php echo htmlspecialchars($grn_data_pdf['supplier_name']); ?></p>
            </div>
            <div class="address-col">
                <p>Received By: <?php echo htmlspecialchars($grn_data_pdf['received_by_username']); ?></p>
                <p>Date Generated: <?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($grn_data_pdf['created_at']))); ?></p>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Items Table Section -->
        <div class="section-header">Items Received</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Product Name (SKU)</th>
                    <th class="text-center" style="width: 20%;">Quantity Received</th>
                    <th style="width: 20%;">Condition Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($items_query, 0); // Reset result pointer for display
                while ($item = mysqli_fetch_assoc($items_query)) :
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name'] ? $item['product_name'] : 'N/A (Product Deleted)'); ?> (<?php echo htmlspecialchars($item['product_sku'] ? $item['product_sku'] : 'N/A'); ?>)</td>
                        <td class="text-center"><?php echo htmlspecialchars($item['quantity_received']); ?></td>
                        <td><?php echo htmlspecialchars($item['condition_notes'] ? $item['condition_notes'] : 'Good'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- General Notes Section -->
        <div class="general-notes-section">
            <div class="section-header">General Notes</div>
            <p><?php echo htmlspecialchars($grn_data_pdf['notes'] ? $grn_data_pdf['notes'] : 'No general notes for this Goods Received Note.'); ?></p>
        </div>

        <div style="clear: both;"></div>

        <!-- Thank You Section -->
        <div class="thank-you-section">
            <strong>Goods received and inspected.</strong>
            <p>This document confirms the receipt of goods listed above.</p>
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

$filename = "Goods_Received_Note_" . str_replace('/', '_', $grn_data_pdf['grn_number']) . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit();
?>
