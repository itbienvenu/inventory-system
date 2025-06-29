<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

if ($_SESSION['role'] !== 'executive') {
    die("Unauthorized access.");
}

if (!isset($_GET['grn']) || empty($_GET['grn'])) {
    die("Goods Received Note number not provided for editing.");
}

$grn_number = mysqli_real_escape_string($conn, $_GET['grn']);

// Fetch GRN header details
$grn_query = mysqli_query($conn, "
    SELECT *
    FROM goods_received_notes
    WHERE grn_number = '$grn_number'
");

if (!$grn_query) {
    die("Error fetching Goods Received Note for editing: " . mysqli_error($conn));
}

$grn_data = mysqli_fetch_assoc($grn_query);

if (!$grn_data) {
    die("Goods Received Note with number '$grn_number' not found for editing.");
}

$grn_id = $grn_data['id'];

// Fetch GRN items
$items_query = mysqli_query($conn, "
    SELECT grni.*, p.name AS product_name, p.sku AS product_sku
    FROM goods_received_note_items grni
    LEFT JOIN products p ON grni.product_id = p.id
    WHERE grni.grn_id = $grn_id
");

if (!$items_query) {
    die("Error fetching Goods Received Note items for editing: " . mysqli_error($conn));
}

$grn_items = [];
while ($item = mysqli_fetch_assoc($items_query)) {
    $grn_items[] = $item;
}

// Fetch all purchase orders for the dropdown (optional linking)
$all_purchase_orders_query = mysqli_query($conn, "SELECT id, po_number, supplier_name FROM purchase_orders ORDER BY po_number DESC");
$all_purchase_orders = [];
while ($po = mysqli_fetch_assoc($all_purchase_orders_query)) {
    $all_purchase_orders[] = $po;
}

// Fetch all products for the dropdown (for adding/editing items)
$all_products_edit_grn = mysqli_query($conn, "SELECT id, name, sku FROM products ORDER BY name ASC");
$all_products_edit_grn_array = [];
while ($p = mysqli_fetch_assoc($all_products_edit_grn)) {
    $all_products_edit_grn_array[] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Goods Received Note #<?php echo htmlspecialchars($grn_data['grn_number']); ?></title>
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
        <h2 class="mb-4 text-center">Edit Goods Received Note #<?php echo htmlspecialchars($grn_data['grn_number']); ?></h2>

        <form action="../functions/UpdateGrn.php" method="POST">
            <!-- Hidden fields to send GRN ID and number for redirection -->
            <input type="hidden" name="grn_id" value="<?php echo htmlspecialchars($grn_data['id']); ?>">
            <input type="hidden" name="grn_number" value="<?php echo htmlspecialchars($grn_data['grn_number']); ?>">

            <div class="row">
                <!-- GRN Details Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>GRN Details</strong></div>
                        <div class="card-body card-block">
                            <div class="form-group mb-3">
                                <label for="po_id_grn" class="form-label">Link to Purchase Order (Optional)</label>
                                <select name="po_id" id="po_id_grn" class="form-control rounded-pill px-3">
                                    <option value="">-- Select Purchase Order --</option>
                                    <?php foreach ($all_purchase_orders as $po) : ?>
                                        <option value="<?php echo htmlspecialchars($po['id']); ?>" data-supplier-name="<?php echo htmlspecialchars($po['supplier_name']); ?>"
                                            <?php echo ($po['id'] == $grn_data['po_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($po['po_number']); ?> (<?php echo htmlspecialchars($po['supplier_name']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="supplier_name_grn" class="form-label">Supplier Name</label>
                                <input type="text" id="supplier_name_grn" name="supplier_name" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($grn_data['supplier_name']); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="receipt_date_grn" class="form-label">Receipt Date</label>
                                <input type="date" id="receipt_date_grn" name="receipt_date" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($grn_data['receipt_date']); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="notes_grn" class="form-label">Notes</label>
                                <textarea name="notes" id="notes_grn" rows="4" class="form-control rounded-pill px-3" placeholder="Any special notes about this receipt"><?php echo htmlspecialchars($grn_data['notes']); ?></textarea>
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
                                <?php if (empty($grn_items)) : ?>
                                    <!-- Default empty row if no items -->
                                    <div class="row form-group product-row-grn d-flex align-items-center mb-2">
                                        <div class="col-6 pe-1">
                                            <label for="product_grn_0" class="form-control-label">Product</label>
                                            <select name="products[]" id="product_grn_0" class="form-control rounded-pill px-3" required>
                                                <option value="">-- Select Product --</option>
                                                <?php foreach ($all_products_edit_grn_array as $p) : ?>
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
                                <?php else : ?>
                                    <?php foreach ($grn_items as $i => $item) : ?>
                                        <div class="row form-group product-row-grn d-flex align-items-center mb-2">
                                            <div class="col-6 pe-1">
                                                <label for="product_grn_<?php echo $i; ?>" class="form-control-label">Product</label>
                                                <select name="products[]" id="product_grn_<?php echo $i; ?>" class="form-control rounded-pill px-3" required>
                                                    <option value="">-- Select Product --</option>
                                                    <?php foreach ($all_products_edit_grn_array as $p) : ?>
                                                        <option value="<?php echo htmlspecialchars($p['id']); ?>"
                                                            <?php echo ($p['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['sku']); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-3 pe-1">
                                                <label for="quantity_received_grn_<?php echo $i; ?>" class="form-control-label">Qty Received</label>
                                                <input type="number" id="quantity_received_grn_<?php echo $i; ?>" name="quantities_received[]" class="form-control rounded-pill px-3" placeholder="Qty" value="<?php echo htmlspecialchars($item['quantity_received']); ?>" required min="1">
                                            </div>
                                            <div class="col-2 pe-1">
                                                <label for="condition_notes_grn_<?php echo $i; ?>" class="form-control-label">Condition</label>
                                                <input type="text" id="condition_notes_grn_<?php echo $i; ?>" name="condition_notes[]" class="form-control rounded-pill px-3" placeholder="e.g., Damaged" value="<?php echo htmlspecialchars($item['condition_notes']); ?>">
                                            </div>
                                            <div class="col-1 ps-0 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger btn-sm removeRowGRN mb-1">X</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Add New Product Button -->
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-sm rounded-pill px-4" id="addProductGRN">+ Add Received Item</button>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-end mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                <a href="view_grn.php?grn=<?php echo htmlspecialchars($grn_data['grn_number']); ?>" class="btn btn-secondary rounded-pill px-4 ms-2">Cancel</a>
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
            const allProductsEditGRN = <?php echo json_encode($all_products_edit_grn_array); ?>;
            let productRowCountGRN = $('#productRowsGRN .product-row-grn').length; // Initialize with existing rows

            // Auto-fill supplier name if PO is selected (on initial load if PO is already linked)
            const initialPoId = $('#po_id_grn').val();
            if (initialPoId) {
                const selectedOption = $('#po_id_grn').find('option:selected');
                const supplierName = selectedOption.data('supplier-name');
                if (supplierName) {
                    $('#supplier_name_grn').val(supplierName);
                }
            }

            // Auto-fill supplier name if PO is selected (on change)
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
                let productOptions = '<option value="">-- Select Product --</option>';
                allProductsEditGRN.forEach(function(p) {
                    productOptions += `<option value="${p.id}">${p.name} (${p.sku})</option>`;
                });

                const productRow = `
                    <div class="row form-group product-row-grn d-flex align-items-center mb-2">
                        <div class="col-6 pe-1">
                            <label for="product_grn_${productRowCountGRN}" class="form-control-label">Product</label>
                            <select name="products[]" id="product_grn_${productRowCountGRN}" class="form-control rounded-pill px-3" required>
                                ${productOptions}
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
                $('#productRowsGRN').append(productRow);
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
