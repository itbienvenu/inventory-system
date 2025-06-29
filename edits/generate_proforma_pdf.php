<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

// --- DOMPDF Setup ---
// Adjust this path to where you placed the dompdf library
require_once __DIR__.'/../packages/dompdf/autoload.inc.php'; // Example: if dompdf is in 'your_project/libraries/dompdf/'

use Dompdf\Dompdf;
use Dompdf\Options;
// --- End DOMPDF Setup ---

$allowed_roles = ['admin', 'executive'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
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

$sub_total = 0; // Calculate subtotal from items
// Reset items_query pointer to ensure all items are processed for calculation
mysqli_data_seek($items_query, 0);
while ($item = mysqli_fetch_assoc($items_query)) {
    $sub_total += $item['total_price'];
}
// Reset items_query pointer again for displaying in PDF
mysqli_data_seek($items_query, 0);

// Placeholder values for missing data in DB schema
$discount = 0.00; // Assuming no discount for now
$tax_rate = 0.00; // Assuming no tax for now
$tax_amount = $sub_total * ($tax_rate / 100);
$total_quote = $sub_total - $discount + $tax_amount;

// Start buffering HTML output
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
    <style>
        body {
            font-family: 'Inter', sans-serif; /* Fallback to Arial, sans-serif */
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            color: #333;
            font-size: 10px; /* Base font size for PDF */
        }
        .invoice-container {
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
            overflow: hidden; /* Clear floats for older PDF renderers */
        }
        .header-left, .header-right {
            width: 48%; /* Adjust width for PDF layout */
            float: left; /* For older PDF renderers */
        }
        .header-right {
            float: right; /* For older PDF renderers */
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .quotation-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
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
            background-color: #3498db; /* Blue background for headers */
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
            overflow: hidden; /* Clear floats */
        }
        .address-col {
            width: 48%;
            float: left; /* For older PDF renderers */
        }
        .address-col:last-child {
            float: right; /* For older PDF renderers */
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
            background-color: #3498db; /* Blue header for table */
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
            width: 250px; /* Fixed width for totals section */
            float: right; /* Align right */
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
            clear: both; /* Clear floats */
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .notes-section ol {
            padding-left: 20px;
            margin: 0;
        }
        .notes-section li {
            margin-bottom: 5px;
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
            height: 18px; /* Adjust as needed */
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
                <div class="company-name">ITBIENVENU</div>
                <div class="contact-info">
                    <p>Address, Town, City</p>
                    <p>Phone: 250736701735</p>
                    <p>Email: itbienvenu123@gmail.com</p>
                    <p>Website: www.itbienvenu.onrender.com</p>
                </div>
            </div>
            <div class="header-right">
                <div class="quotation-title">Quotation</div>
                <div class="info-grid">
                    <p>Date: <?php echo htmlspecialchars(date('DD/MM/YYYY')); ?></p>
                    <p>Quote No: #<?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
                    <p>Valid for: 14 days</p> <!-- Placeholder, add to DB if dynamic -->
                </div>
            </div>
            <div style="clear: both;"></div> <!-- Clear float -->
        </div>

        <!-- Billed To / Ship To Section -->
        <div class="section-header">Billed to</div>
        <div class="address-box">
            <div class="address-col">
                <p>Client Name: <?php echo htmlspecialchars($invoice['company']); ?></p>
                <p>Address line 1: <?php echo htmlspecialchars($invoice['street']); ?></p>
                <p>Address line 2: <?php echo htmlspecialchars($invoice['postal_code']); ?></p>
                <p>Town, City: <?php echo htmlspecialchars($invoice['city']); ?></p>
                <p>Phone: Your Client Phone</p> <!-- Placeholder -->
            </div>
            <div class="address-col">
                <div class="section-header" style="background-color: #3498db; margin-bottom: 5px; padding: 5px 10px; border-radius: 4px;">Ship to (if different)</div>
                <p>Client Name: Your Client Name</p> <!-- Placeholder -->
                <p>Address line 1: Your Ship Address 1</p> <!-- Placeholder -->
                <p>Address line 2: Your Ship Address 2</p> <!-- Placeholder -->
                <p>Town, City: Your Ship City</p> <!-- Placeholder -->
                <p>Phone: Your Ship Phone</p> <!-- Placeholder -->
            </div>
            <div style="clear: both;"></div> <!-- Clear float -->
        </div>

        <!-- Items Table Section -->
        <div class="section-header">Description</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Your item name</th>
                    <th class="text-right" style="width: 15%;">Unit cost</th>
                    <th class="text-center" style="width: 15%;">Qty/Hr rate</th>
                    <th class="text-right" style="width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Re-fetch items to ensure correct order/data after calculation
                mysqli_data_seek($items_query, 0);
                while ($item = mysqli_fetch_assoc($items_query)) :
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?> (SKU: <?php echo htmlspecialchars($item['product_sku']); ?>)</td>
                        <td class="text-right">$<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td class="text-right">$<?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Special Notes and Totals Section -->
        <div class="notes-section">
            <div class="section-header">Special notes and instructions</div>
            <ol>
                <li>Deposit amount and payment method requirements here</li>
                <li>Make all cheques payable to my company name</li>
                <li>Warranty terms here</li>
            </ol>
        </div>

        <div class="totals-section">
            <div class="totals-row">
                <span>SUBTOTAL</span>
                <span class="text-right">$<?php echo number_format($sub_total, 2); ?></span>
            </div>
            <div class="totals-row">
                <span>DISCOUNT</span>
                <span class="text-right">-$<?php echo number_format($discount, 2); ?></span>
            </div>
            <div class="totals-row">
                <span>(TAX RATE)</span>
                <span class="text-right"><?php echo number_format($tax_rate, 0); ?>%</span>
            </div>
            <div class="totals-row">
                <span>TAX</span>
                <span class="text-right">$<?php echo number_format($tax_amount, 2); ?></span>
            </div>
            <div class="totals-row">
                <span>TOTAL QUOTE</span>
                <span class="text-right">$<?php echo number_format($total_quote, 2); ?></span>
            </div>
        </div>
        <div style="clear: both;"></div> <!-- Clear float for totals section -->

        <!-- Thank You Section -->
        <div class="thank-you-section">
            <strong>Thank you for your invoice!</strong>
            <p>Should you have any enquiries concerning this invoice, please contact us.</p>
        </div>

        <!-- Powered By Section -->
        <div class="powered-by">
            Send money abroad with Wise.
            <!-- Replace with an actual image URL or embed Base64 if necessary and allowed by Dompdf setup -->
            <img src="https://placehold.co/80x18/F0F2F5/333?text=WISE" alt="ITBienvenu" />
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
