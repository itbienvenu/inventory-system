<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    die("Proforma invoice number not provided for editing.");
}

$invoice_number = mysqli_real_escape_string($conn, $_GET['invoice']);

// Fetch proforma invoice header details
$invoice_query = mysqli_query($conn, "
    SELECT *
    FROM proforma_invoices
    WHERE invoice_number = '$invoice_number'
");

if (!$invoice_query) {
    die("Error fetching invoice for editing: " . mysqli_error($conn));
}

$invoice_data = mysqli_fetch_assoc($invoice_query);

if (!$invoice_data) {
    die("Proforma invoice with number '$invoice_number' not found for editing.");
}

$invoice_id = $invoice_data['id'];

// Fetch proforma items
$items_query = mysqli_query($conn, "
    SELECT pti.*, p.name AS product_name, p.price AS product_price
    FROM proforma_items pti
    JOIN products p ON pti.product_id = p.id
    WHERE pti.invoice_id = $invoice_id
");

if (!$items_query) {
    die("Error fetching invoice items for editing: " . mysqli_error($conn));
}

$proforma_items = [];
while ($item = mysqli_fetch_assoc($items_query)) {
    $proforma_items[] = $item;
}

// Fetch all products for the dropdown
$all_products_query = mysqli_query($conn, "SELECT id, name, price FROM products");
$all_products = [];
while ($p = mysqli_fetch_assoc($all_products_query)) {
    $all_products[] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Proforma Invoice #<?php echo htmlspecialchars($invoice_data['invoice_number']); ?></title>
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
            background-color: #ffc107; /* Warning color for edit */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .product-row .form-control {
            border-radius: 5px;
        }
        .btn-danger, .btn-success {
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Edit Proforma Invoice #<?php echo htmlspecialchars($invoice_data['invoice_number']); ?></h2>

        <form action="UpdateProforma.php" method="POST">
            <!-- Hidden field to send invoice ID -->
            <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice_data['id']); ?>">
            <input type="hidden" name="invoice_number" value="<?php echo htmlspecialchars($invoice_data['invoice_number']); ?>">

            <div class="row">
                <!-- Customer Info Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Customer Info</strong></div>
                        <div class="card-body card-block">
                            <div class="form-group mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($invoice_data['company']); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">VAT Number</label>
                                <input type="text" name="vat" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($invoice_data['vat']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Street</label>
                                <input type="text" name="street" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($invoice_data['street']); ?>">
                            </div>
                            <div class="row form-group mb-3">
                                <div class="col-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($invoice_data['city']); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($invoice_data['postal_code']); ?>">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($invoice_data['country']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Product Selection Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Products</strong></div>
                        <div class="card-body card-block">
                            <div id="productRows">
                                <?php if (empty($proforma_items)) : ?>
                                    <!-- Default empty row if no items -->
                                    <div class="form-row mb-2 product-row d-flex align-items-center">
                                        <div class="col-7 pe-1">
                                            <select name="products[]" class="form-control rounded-pill px-3" required>
                                                <option value="">-- Select Product --</option>
                                                <?php foreach ($all_products as $p) : ?>
                                                    <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                                        <?php echo htmlspecialchars($p['name']); ?> - $<?php echo htmlspecialchars($p['price']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-3 pe-1">
                                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                                        </div>
                                        <div class="col-2 ps-0">
                                            <button type="button" class="btn btn-danger btn-sm removeRow rounded-pill">X</button>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($proforma_items as $item) : ?>
                                        <div class="form-row mb-2 product-row d-flex align-items-center">
                                            <div class="col-7 pe-1">
                                                <select name="products[]" class="form-control rounded-pill px-3" required>
                                                    <option value="">-- Select Product --</option>
                                                    <?php foreach ($all_products as $p) : ?>
                                                        <option value="<?php echo htmlspecialchars($p['id']); ?>"
                                                            <?php echo ($p['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($p['name']); ?> - $<?php echo htmlspecialchars($p['price']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-3 pe-1">
                                                <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" value="<?php echo htmlspecialchars($item['quantity']); ?>" required min="1">
                                            </div>
                                            <div class="col-2 ps-0">
                                                <button type="button" class="btn btn-danger btn-sm removeRow rounded-pill">X</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Add New Product Button -->
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-sm rounded-pill px-4" id="addProduct">+ Add Product</button>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-right mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                <a href="view_proforma.php?invoice=<?php echo htmlspecialchars($invoice_data['invoice_number']); ?>" class="btn btn-secondary rounded-pill px-4 ms-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to get product price from the dropdown (for display purposes, actual calculation is server-side)
            function getProductPrice(productId) {
                const products = <?php echo json_encode($all_products); ?>;
                const product = products.find(p => p.id == productId);
                return product ? parseFloat(product.price) : 0;
            }

            $('#addProduct').on('click', function() {
                const productRow = `
                    <div class="form-row mb-2 product-row d-flex align-items-center">
                        <div class="col-7 pe-1">
                            <select name="products[]" class="form-control rounded-pill px-3" required>
                                <option value="">-- Select Product --</option>
                                <?php foreach ($all_products as $p) : ?>
                                    <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                        <?php echo htmlspecialchars($p['name']); ?> - $<?php echo htmlspecialchars($p['price']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-3 pe-1">
                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                        </div>
                        <div class="col-2 ps-0">
                            <button type="button" class="btn btn-danger btn-sm removeRow rounded-pill">X</button>
                        </div>
                    </div>
                `;
                $('#productRows').append(productRow);
            });

            // Event listener for removing product rows
            $(document).on('click', '.removeRow', function() {
                // Ensure at least one product row remains
                if ($('#productRows .product-row').length > 1) {
                    $(this).closest('.product-row').remove();
                } else {
                    alert("You must have at least one product in the proforma.");
                }
            });
        });
    </script>
</body>
</html>
