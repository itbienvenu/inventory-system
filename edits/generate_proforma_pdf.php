<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

// --- DOMPDF Setup ---
// Adjust this path to where you placed the dompdf library
require_once __DIR__.'/../packages/dompdf/autoload.inc.php'; // Example: if dompdf is in 'your_project/libraries/dompdf/'

use Dompdf\Dompdf;
use Dompdf\Options;
// --- End DOMPDF Setup ---

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    die("Proforma invoice number not provided for PDF generation.");
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
    die("Error fetching invoice for PDF: " . mysqli_error($conn));
}

$invoice = mysqli_fetch_assoc($invoice_query);

if (!$invoice) {
    die("Proforma invoice with number '$invoice_number' not found for PDF generation.");
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
    die("Error fetching invoice items for PDF: " . mysqli_error($conn));
}

$grand_total = 0;

// Start buffering HTML output
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
    <!-- Use a simple CSS for PDF to ensure good rendering without relying on external Bootstrap resources -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 14px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            padding: 8px;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            padding: 8px;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .company-info {
            float: left;
            width: 48%;
        }
        .invoice-info {
            float: right;
            width: 48%;
            text-align: right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <!-- Your Company Logo/Name Here -->
                                Proforma Invoice
                            </td>
                            <td>
                                Invoice #: <?php echo htmlspecialchars($invoice['invoice_number']); ?><br>
                                Created: <?php echo htmlspecialchars(date('M d, Y', strtotime($invoice['created_at']))); ?><br>
                                Created By: <?php echo htmlspecialchars($invoice['created_by_username']); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <!-- Customer Info -->
                                <strong>To:</strong><br>
                                <?php echo htmlspecialchars($invoice['company']); ?><br>
                                <?php echo htmlspecialchars($invoice['street']); ?><br>
                                <?php echo htmlspecialchars($invoice['city']); ?>, <?php echo htmlspecialchars($invoice['postal_code']); ?><br>
                                <?php echo htmlspecialchars($invoice['country']); ?><br>
                                VAT: <?php echo htmlspecialchars($invoice['vat']); ?>
                            </td>
                            <td>
                                <!-- Your Company Info (optional, if you want it on the invoice) -->
                                Your Company Name<br>
                                123 Your Street<br>
                                Your City, Your Postal Code<br>
                                Your Country
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Product Name</td>
                <td class="text-right">Unit Price</td>
                <td class="text-right">Quantity</td>
                <td class="text-right">Total Price</td>
            </tr>

            <?php
            // Reset items_query pointer for displaying in PDF
            mysqli_data_seek($items_query, 0); // Reset result pointer to the beginning
            while ($item = mysqli_fetch_assoc($items_query)) :
                $grand_total += $item['total_price'];
            ?>
                <tr class="item">
                    <td><?php echo htmlspecialchars($item['product_name']); ?> (SKU: <?php echo htmlspecialchars($item['product_sku']); ?>)</td>
                    <td class="text-right">$<?php echo number_format($item['unit_price'], 2); ?></td>
                    <td class="text-right"><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td class="text-right">$<?php echo number_format($item['total_price'], 2); ?></td>
                </tr>
            <?php endwhile; ?>

            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">Grand Total: $<?php echo number_format($grand_total, 2); ?></td>
            </tr>
        </table>
        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #888;">
            This is a Proforma Invoice and is not a Tax Invoice.
        </div>
    </div>
</body>
</html>

<?php
$html = ob_get_clean(); // Get the buffered HTML output

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Enable if you use external assets like images, but avoid for simplicity
$options->set('defaultFont', 'Arial'); // A common font for PDF

// Instantiate Dompdf with options
$dompdf = new Dompdf($options);

// Load HTML to Dompdf
$dompdf->loadHtml($html);

// (Optional) Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF (inline or download)
$filename = "Proforma_Invoice_" . str_replace('/', '_', $invoice['invoice_number']) . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]); // 'true' for download, 'false' for inline view
exit();
?>
