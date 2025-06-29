<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') { // Assuming executive can create GRNs
    die("Unauthorized access.");
}

// Fetch all purchase orders to optionally link to a GRN
$all_purchase_orders_query = mysqli_query($conn, "SELECT id, po_number, supplier_name FROM purchase_orders ORDER BY po_number DESC");
$all_purchase_orders = [];
if ($all_purchase_orders_query) {
    while ($po = mysqli_fetch_assoc($all_purchase_orders_query)) {
        $all_purchase_orders[] = $po;
    }
} else {
    error_log("Error fetching purchase orders for GRN form: " . mysqli_error($conn));
}

// Fetch all products for the dropdown
$all_products_query_grn = mysqli_query($conn, "SELECT id, name, sku FROM products ORDER BY name ASC");
$all_products_grn = [];
if ($all_products_query_grn) {
    while ($p = mysqli_fetch_assoc($all_products_query_grn)) {
        $all_products_grn[] = $p;
    }
} else {
    error_log("Error fetching products for GRN creation form: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Goods Received Note</title>
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
            background-color: #28a745; /* Success color for GRN creation */
            color: white;
            font-weight: bold;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
        .product-row .form-control {
            border-radius: 5px;
        }
        .btn-danger, .btn-success {
            border-radius: 5px;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .rounded-pill.px-3 {
            border-radius: 50rem !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Create New Goods Received Note</h2>

        <form action="../functions/SaveGrn.php" method="post" class="form-horizontal">
            <!-- GRN Header Info Section -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>GRN Details</strong></div>
                        <div class="card-body card-block">
                            <div class="form-group mb-3">
                                <label for="po_id_grn" class="form-label">Link to Purchase Order (Optional)</label>
                                <select name="po_id" id="po_id_grn" class="form-control rounded-pill px-3">
                                    <option value="">-- Select Purchase Order --</option>
                                    <?php foreach ($all_purchase_orders as $po) : ?>
                                        <option value="<?php echo htmlspecialchars($po['id']); ?>" data-supplier-name="<?php echo htmlspecialchars($po['supplier_name']); ?>">
                                            <?php echo htmlspecialchars($po['po_number']); ?> (<?php echo htmlspecialchars($po['supplier_name']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="supplier_name_grn" class="form-label">Supplier Name (Auto-filled if PO selected)</label>
                                <input type="text" id="supplier_name_grn" name="supplier_name" placeholder="Enter Supplier Name" class="form-control rounded-pill px-3" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="receipt_date_grn" class="form-label">Receipt Date</label>
                                <input type="date" id="receipt_date_grn" name="receipt_date" class="form-control rounded-pill px-3" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="notes_grn" class="form-label">Notes</label>
                                <textarea name="notes" id="notes_grn" rows="4" placeholder="Any special notes about this receipt, e.g., condition of goods, partial shipment, etc." class="form-control rounded-pill px-3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Received Items Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Received Items</strong></div>
                        <div class="card-body card-block">
                            <div id="productRowsGRN">
                                <!-- Initial Product row template -->
                                <div class="row form-group product-row-grn d-flex align-items-center mb-2">
                                    <div class="col-6 pe-1">
                                        <label for="product_grn_0" class="form-control-label">Product</label>
                                        <select name="products[]" id="product_grn_0" class="form-control rounded-pill px-3" required>
                                            <option value="">-- Select Product --</option>
                                            <?php foreach ($all_products_grn as $p) : ?>
                                                <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                                    <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['sku']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-3 pe-1">
                                        <label for="quantity_received_grn_0" class="form-control-label">Qty Received</label>
                                        <input type="number" id="quantity_received_grn_0" name="quantities_received[]" placeholder="Qty" class="form-control rounded-pill px-3" required min="1">
                                    </div>
                                    <div class="col-2 pe-1">
                                        <label for="condition_notes_grn_0" class="form-control-label">Condition</label>
                                        <input type="text" id="condition_notes_grn_0" name="condition_notes[]" placeholder="e.g., Damaged" class="form-control rounded-pill px-3">
                                    </div>
                                    <div class="col-1 ps-0 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm removeRowGRN mb-1">X</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add New Product Button -->
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-sm rounded-pill px-4" id="addProductGRN">+ Add Received Item</button>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-end mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">Generate GRN</button>
                                <button type="reset" class="btn btn-danger rounded-pill px-4 ms-2">Reset</button>
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
            const allProductsGRN = <?php echo json_encode($all_products_grn); ?>;
            let productRowCountGRN = 0; // To ensure unique IDs for new rows

            // Auto-fill supplier name if PO is selected
            $('#po_id_grn').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const supplierName = selectedOption.data('supplier-name');
                if (supplierName) {
                    $('#supplier_name_grn').val(supplierName);
                } else {
                    $('#supplier_name_grn').val('');
                }
            });

            $('#addProductGRN').on('click', function() {
                productRowCountGRN++;
                let productOptionsGRN = '<option value="">-- Select Product --</option>';
                allProductsGRN.forEach(function(p) {
                    productOptionsGRN += `<option value="${p.id}">${p.name} (${p.sku})</option>`;
                });

                const productRowHtml = `
                    <div class="row form-group product-row-grn d-flex align-items-center mb-2">
                        <div class="col-6 pe-1">
                            <label for="product_grn_${productRowCountGRN}" class="form-control-label">Product</label>
                            <select name="products[]" id="product_grn_${productRowCountGRN}" class="form-control rounded-pill px-3" required>
                                ${productOptionsGRN}
                            </select>
                        </div>
                        <div class="col-3 pe-1">
                            <label for="quantity_received_grn_${productRowCountGRN}" class="form-control-label">Qty Received</label>
                            <input type="number" id="quantity_received_grn_${productRowCountGRN}" name="quantities_received[]" placeholder="Qty" class="form-control rounded-pill px-3" required min="1">
                        </div>
                        <div class="col-2 pe-1">
                            <label for="condition_notes_grn_${productRowCountGRN}" class="form-control-label">Condition</label>
                            <input type="text" id="condition_notes_grn_${productRowCountGRN}" name="condition_notes[]" placeholder="e.g., Damaged" class="form-control rounded-pill px-3">
                        </div>
                        <div class="col-1 ps-0 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm removeRowGRN mb-1">X</button>
                        </div>
                    </div>
                `;
                $('#productRowsGRN').append(productRowHtml);
            });

            // Event listener for removing product rows
            $(document).on('click', '.removeRowGRN', function() {
                // Ensure at least one product row remains
                if ($('#productRowsGRN .product-row-grn').length > 1) {
                    $(this).closest('.product-row-grn').remove();
                } else {
                    alert("A Goods Received Note must have at least one item.");
                }
            });
        });
    </script>
</body>
</html>
