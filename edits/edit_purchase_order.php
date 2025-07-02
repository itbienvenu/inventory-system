<?php
session_start();
include_once "../config/config.php"; // Adjust path as necessary

$allowed_roles = ['executive','admin'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    die("Unauthorized access.");
}

if (!isset($_GET['po']) || empty($_GET['po'])) {
    die("Purchase Order number not provided for editing.");
}

$po_number = mysqli_real_escape_string($conn, $_GET['po']);

// Fetch purchase order header details
$po_query = mysqli_query($conn, "
    SELECT *
    FROM purchase_orders
    WHERE po_number = '$po_number'
");

if (!$po_query) {
    die("Error fetching Purchase Order for editing: " . mysqli_error($conn));
}

$po_data = mysqli_fetch_assoc($po_query);

if (!$po_data) {
    die("Purchase Order with number '$po_number' not found for editing.");
}

$po_id = $po_data['id'];

// Fetch purchase order items
$items_query = mysqli_query($conn, "
    SELECT poi.*, p.name AS product_name, p.sku AS product_sku, p.cost_price AS product_default_cost_price
    FROM purchase_order_items poi
    LEFT JOIN products p ON poi.product_id = p.id
    WHERE poi.po_id = $po_id
");

if (!$items_query) {
    die("Error fetching Purchase Order items for editing: " . mysqli_error($conn));
}

$purchase_order_items = [];
while ($item = mysqli_fetch_assoc($items_query)) {
    $purchase_order_items[] = $item;
}

// Fetch all products for the dropdown
$all_products_edit_po = mysqli_query($conn, "SELECT id, name, sku, cost_price FROM products ORDER BY name ASC");
$all_products_edit_po_array = [];
while ($p = mysqli_fetch_assoc($all_products_edit_po)) {
    $all_products_edit_po_array[] = $p;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Purchase Order #<?php echo htmlspecialchars($po_data['po_number']); ?></title>
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
            border-radius: 50rem !important; /* Ensure consistent rounded pills */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4 text-center">Edit Purchase Order #<?php echo htmlspecialchars($po_data['po_number']); ?></h2>

        <form action="../functions/UpdatePurchaseOrder.php" method="POST">
            <!-- Hidden fields to send PO ID and number for redirection -->
            <input type="hidden" name="po_id" value="<?php echo htmlspecialchars($po_data['id']); ?>">
            <input type="hidden" name="po_number" value="<?php echo htmlspecialchars($po_data['po_number']); ?>">

            <div class="row">
                <!-- Supplier Info Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Supplier Details</strong></div>
                        <div class="card-body card-block">
                            <div class="form-group mb-3">
                                <label class="form-label">Supplier Name</label>
                                <input type="text" name="supplier_name" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($po_data['supplier_name']); ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="supplier_contact_person" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($po_data['supplier_contact_person']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="supplier_email" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($po_data['supplier_email']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="supplier_phone" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($po_data['supplier_phone']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="supplier_address" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($po_data['supplier_address']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Expected Delivery Date</label>
                                <input type="date" name="expected_delivery_date" class="form-control rounded-pill px-3" value="<?php echo htmlspecialchars($po_data['expected_delivery_date']); ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control rounded-pill px-3">
                                    <option value="pending" <?php echo ($po_data['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="sent" <?php echo ($po_data['status'] == 'sent') ? 'selected' : ''; ?>>Sent</option>
                                    <option value="received" <?php echo ($po_data['status'] == 'received') ? 'selected' : ''; ?>>Received</option>
                                    <option value="cancelled" <?php echo ($po_data['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control rounded-pill px-3" placeholder="Any special instructions or comments for the supplier"><?php echo htmlspecialchars($po_data['notes']); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Product Selection Section -->
                <div class="col-lg-6">
                    <div class="card rounded-3 mb-4">
                        <div class="card-header"><strong>Order Items</strong></div>
                        <div class="card-body card-block">
                            <div id="productRowsPO">
                                <?php if (empty($purchase_order_items)) : ?>
                                    <!-- Default empty row if no items -->
                                    <div class="form-row mb-2 product-row-po d-flex align-items-center">
                                        <div class="col-4 pe-1">
                                            <select name="products[]" class="form-control rounded-pill px-3" required onchange="updateUnitCost(this)">
                                                <option value="">-- Select Product --</option>
                                                <?php foreach ($all_products_edit_po_array as $p) : ?>
                                                    <option value="<?php echo htmlspecialchars($p['id']); ?>" data-cost-price="<?php echo htmlspecialchars($p['cost_price']); ?>">
                                                        <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['sku']); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-3 pe-1">
                                            <input type="number" step="0.01" name="unit_costs[]" class="form-control rounded-pill px-3" placeholder="Unit Cost" required min="0.01">
                                        </div>
                                        <div class="col-3 pe-1">
                                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                                        </div>
                                        <div class="col-2 ps-0">
                                            <button type="button" class="btn btn-danger btn-sm removeRowPO rounded-pill">X</button>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <?php foreach ($purchase_order_items as $item) : ?>
                                        <div class="form-row mb-2 product-row-po d-flex align-items-center">
                                            <div class="col-4 pe-1">
                                                <select name="products[]" class="form-control rounded-pill px-3" required onchange="updateUnitCost(this)">
                                                    <option value="">-- Select Product --</option>
                                                    <?php foreach ($all_products_edit_po_array as $p) : ?>
                                                        <option value="<?php echo htmlspecialchars($p['id']); ?>" data-cost-price="<?php echo htmlspecialchars($p['cost_price']); ?>"
                                                            <?php echo ($p['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['sku']); ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-3 pe-1">
                                                <input type="number" step="0.01" name="unit_costs[]" class="form-control rounded-pill px-3" placeholder="Unit Cost" value="<?php echo htmlspecialchars($item['unit_cost']); ?>" required min="0.01">
                                            </div>
                                            <div class="col-3 pe-1">
                                                <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" value="<?php echo htmlspecialchars($item['quantity']); ?>" required min="1">
                                            </div>
                                            <div class="col-2 ps-0">
                                                <button type="button" class="btn btn-danger btn-sm removeRowPO rounded-pill">X</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Add New Product Button -->
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-sm rounded-pill px-4" id="addProductPO">+ Add Item</button>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group text-right mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                <a href="view_purchase_order.php?po=<?php echo htmlspecialchars($po_data['po_number']); ?>" class="btn btn-secondary rounded-pill px-4 ms-2">Cancel</a>
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
            const allProductsEditPO = <?php echo json_encode($all_products_edit_po_array); ?>;
            let productRowCountPO = $('#productRowsPO .product-row-po').length; // Initialize with existing rows

            window.updateUnitCost = function(selectElement) {
                const selectedOption = $(selectElement).find('option:selected');
                const costPrice = selectedOption.data('cost-price');
                const row = $(selectElement).closest('.product-row-po');
                row.find('input[name="unit_costs[]"]').val(costPrice);
            };

            $('#addProductPO').on('click', function() {
                productRowCountPO++;
                let productOptions = '<option value="">-- Select Product --</option>';
                allProductsEditPO.forEach(function(p) {
                    productOptions += `<option value="${p.id}" data-cost-price="${p.cost_price}">
                                            ${p.name} (${p.sku})
                                        </option>`;
                });

                const productRow = `
                    <div class="form-row mb-2 product-row-po d-flex align-items-center">
                        <div class="col-4 pe-1">
                            <select name="products[]" class="form-control rounded-pill px-3" required onchange="updateUnitCost(this)">
                                ${productOptions}
                            </select>
                        </div>
                        <div class="col-3 pe-1">
                            <input type="number" step="0.01" name="unit_costs[]" class="form-control rounded-pill px-3" placeholder="Unit Cost" required min="0.01">
                        </div>
                        <div class="col-3 pe-1">
                            <input type="number" name="quantities[]" class="form-control rounded-pill px-3" placeholder="Qty" required min="1">
                        </div>
                        <div class="col-2 ps-0">
                            <button type="button" class="btn btn-danger btn-sm removeRowPO rounded-pill">X</button>
                        </div>
                    </div>
                `;
                $('#productRowsPO').append(productRow);
            });

            // Event listener for removing product rows
            $(document).on('click', '.removeRowPO', function() {
                // Ensure at least one product row remains
                if ($('#productRowsPO .product-row-po').length > 1) {
                    $(this).closest('.product-row-po').remove();
                } else {
                    alert("A purchase order must have at least one item.");
                }
            });
        });
    </script>
</body>
</html>
